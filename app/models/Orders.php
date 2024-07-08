<?php namespace App\models;

require_once "common/models/ModelApp.php";
require_once "config/db/Connect.php";
require_once "app/models/Discounts/Discounts.php";

use app\common\models\ModelApp;
use app\config\db\Connect;
use DateTimeImmutable;
use Exception;
use PDO;
use app\models\Discounts\Discounts;

class Orders extends ModelApp
{
    public int $id;
    public int $buyer_id;
    public int $goods_id;
    public string $delivery_date;
    public float $cost_order;
    public string $created_at;

    protected int $quantity;

    protected array $props = [
        'buyer_id',
        'goods_id',
        'delivery_date',
        'cost_order',
        'created_at',
    ];

    public function prepareParams(array $post_params): array
    {
        $result = [];

        foreach ($post_params as $key => $param) {
            if (str_contains($key, 'buyer')) {
                $result[Buyers::class][$key] = $param;
            } else if (str_contains($key, 'goods')) {
                $result[Goods::class][$key] = $param;
            } else {
                $this->$key = $param;
            }
        }

        return $result;
    }

    /**
     * @throws Exception
     */
    public function validate(Goods $modelGoods): bool
    {
        //проверка на достаточность кол-ва товара
        if ($this->quantity > $modelGoods->amount) {
            $this->setError(get_called_class(), "Кол-во товаров на складе '" . $modelGoods->name . "' недостаточно.");

            return false;
        }

        //проверка на актуальность даты доставки
        $today = new DateTimeImmutable(date('Y-m-d'));
        $deliveryDate = new DateTimeImmutable($this->delivery_date);
        $different = $today->diff($deliveryDate);

        if (str_contains($different->format('%R%a'), '-')) {
            $this->setError(get_called_class(), "Дата доставки не может быть меньше сегодняшней даты.");

            return false;
        }

        return true;
    }

    /**
     * @throws \Exception
     */
    public function getDiscount(Buyers $buyer, Goods $modelGoods): void
    {
        $modelDiscounts = new Discounts([
            'date_birth_buyer' => $buyer->date_birth,
            'gender_buyer' => $buyer->gender,
            'delivery_date' => $this->delivery_date,
            'quantity' => $this->quantity
        ]);

        $unitCost = $modelGoods->base_cost;
        if ($modelDiscounts->forPensioners || $modelDiscounts->forEarlyBird || $modelDiscounts->forQuantityGoods) {
            $unitCost = $modelDiscounts->calculateDiscount($modelGoods->base_cost);
        }

        $this->cost_order = $this->quantity * $unitCost;
        $this->buyer_id = $buyer->id;
        $this->goods_id = $modelGoods->id;
        $this->created_at = date("Y-m-d H:i");
    }

    public function getAll(): array|false
    {
        $connect = new Connect();

        return $connect->connection->query('SELECT * FROM re.orders')->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(int $amountGoods): void
    {
        $connect = new Connect();

        try {
            $str_nameProps = implode(', ', $this->props);

            $stmt = $connect->connection->prepare(
                "INSERT INTO re.orders (" . $str_nameProps . ") VALUES (:buyer_id, :goods_id, :delivery_date, :cost_order, :created_at)"
            );

            foreach ($this->props as $prop) {
                $stmt->bindParam(':'.$prop, $this->$prop);
            }

            if (!$stmt->execute() || !$this->reduceAmount($amountGoods)) {
                $this->setError(get_called_class(), "Запрос не выполнился");
//                return $this->id;
            }
            $this->id = $connect->connection->lastInsertId();



//            return 0;
        } catch (PDOException $exception) {
            $this->setError(get_called_class(), $exception->getMessage());

//            return 0;
        }
    }

    private function reduceAmount(int $amountGoods): bool
    {
        $goods = new Goods();

        $amountGoods = $amountGoods - $this->quantity;

        return $goods->updateAmount($this->goods_id, $amountGoods);

//        if (!$goods->reduceAmount($this->goods_id, $amountGoods)) {
//            $this->setError(Goods::class, $goods->getErrors()[Goods::class]);
//
//            return false;
//        }
//
//        return true;
    }

    public function toShowOrder(): array
    {
        $dataBuyer = (new Buyers())->getOne($this->buyer_id);
        $dataGoods = (new Goods())->getOne($this->goods_id);

        return [
            [
                'ФИО покупателя' => $dataBuyer->fio,
                'Дата рождения' => $dataBuyer->date_birth
            ], [
                'Наименование товара' => $dataGoods->name,
                'Базовая стоимость товара' => $dataGoods->base_cost
            ], [
                'Дата доставки' => $this->delivery_date,
                'Общая стоимость заказа' => (float)$this->cost_order,
                'Кол-во заказвнного товара' => $this->quantity
            ]
        ];
    }
}