<?php namespace App\models;

require_once "config/db/Connect.php";

use app\common\models\ModelApp;
use app\config\db\Connect;
use PDO;

class Goods extends ModelApp
{
    public int|null $id;
    public string $name = '';
    public float $base_cost = 0.00;
    public int $amount = 0;

    protected array $props = [
        'name',
        'base_cost',
        'amount',
    ];

    public function getAll(): array|false
    {
        $connect = new Connect();

        return $connect->connection->query('SELECT * FROM re.goods')->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOne(int $id): ?self
    {
        $connect = new Connect();
        $row = $connect->connection->query('SELECT * FROM re.goods WHERE id = ' . $id . ' LIMIT 1')->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            $this->setError(get_called_class(), "По id - " . $id . " запись не найдена");

            return null;
        }

        $this->id = $row['id'];
        foreach ($this->props as $prop) {
            $this->$prop = $row[$prop];
        }

        return $this;
    }

    public function isExistedGoods(array $params): void
    {
        $connect = new Connect();

        foreach ($params as $key => $param) {
            $input_prop = str_replace('_goods', '', $key);
            foreach ($this->props as $prop) {
                if ($prop === $input_prop) {
                    $this->$prop = $param;
                }
            }
        }
        if (empty($this->name)) {
            $this->setError(get_called_class(), 'Не все необходимые параметры были переданы');

//            return null;
        }

        $isExistedGoods = $connect->connection->query("SELECT * FROM re.goods where name='" . $this->name ."'")->fetch(PDO::FETCH_ASSOC);

        if ($isExistedGoods) {
            foreach ($isExistedGoods as $key => $value) {
                $this->$key = $value;
            }
//            return $this;
        } else {
            $this->setError(get_called_class(), "Позиции товара с наименованием '" . $this->name . "' не существует");

//            return null;
        }
    }

    public function updateAmount($goodsId, $amount): bool
    {
        $connect = new Connect();

        $updateGoods = $connect->connection->prepare(
            "UPDATE re.goods SET amount = :amount where id=:goodsId"
        );

        $updateGoods->bindParam(':goodsId', $goodsId);
        $updateGoods->bindParam(':amount', $amount);

        return $updateGoods->execute();
//        if (!$updateGoods->execute()) {
//            $this->setError(get_called_class(), "Невозможно обновить таблицу с товарами");
//
//            return false;
//        }
//
//        return true;
    }
}