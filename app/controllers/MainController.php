<?php namespace App\controllers;

require_once "common/models/response/Response.php";
require_once "app/models/Buyers.php";
require_once "app/models/Goods.php";
require_once "app/models/Orders.php";
require_once "app/models/Discounts.php";

use app\common\response\Response;
use App\models\Buyers;
use App\models\Discounts;
use App\models\Goods;
use App\models\Orders;
use Exception;

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

class MainController extends Controller
{
    public function index(): false|string
    {
        $response = new Response();

        return $response->getSuccess([
            'routing' => [
                [
                    'url' => 'http://localhost:8081/order',
                    'description' => 'для получения списка заказов(GET-запрос) и для добавления нового заказа(POST-запрос)',
                    'params_for_POST-request' => [
                        "fio_buyer" => "Иванова Илона Ивановна",
                        "date_birth_buyer" => "1936-11-26",
                        "gender_buyer" => "ж",
                        "delivery_date" => "16.07.2024",
                        "name_goods" => "электронная беспроводная мышь",
                        "quantity" => "3"
                    ],
                    'params_for_GET-request' => []
                ],
                [
                    'url' => 'http://localhost:8081/buyers',
                    'description' => 'для получения списка покупателей(GET-запрос)',
                    'params_for_GET-request' => []
                ],
                [
                    'url' => 'http://localhost:8081/goods',
                    'description' => 'для получения списка товаров(GET-запрос)',
                    'params_for_GET-request' => []
                ],
            ]
        ]);
    }

    /**
     * @throws Exception
     */
    public function order(Orders $model): string
    {
        $response = new Response();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            return $response->getSuccess($model->getAll());
        } else {
            $params = $model->prepareParams($_POST);

            //Проверка на существование указанного покупателя в табл. Buyer.
            // Если указанного покупателя не существует, то создаётся новый.
            if (isset($params[Buyers::class])) {
                $modelBuyer = new Buyers();
                $buyer_id = $modelBuyer->isExistedBuyer($params[Buyers::class]);

                if (($buyer_id === 0) && !$modelBuyer->hasErrors()) {
                    $modelBuyer->create();
                }

                if ($modelBuyer->hasErrors()) {

                    return $response->getModelErrors($modelBuyer->getErrors());
                }
            } else {

                return $this->error();
            }

            //Проверка на существование указанного пользователем продукта в с табл Goods.
            // Если такого продукта не существует - выводиться ошибка.
            if (isset($params[Goods::class])) {
                $modelGoods = new Goods();
                $modelGoods->isExistedGoods($params[Goods::class]);

                if ($modelGoods->hasErrors()) {

                    return $response->getModelErrors($modelGoods->getErrors());
                }
            } else {

                return $this->error();
            }


            if ($model->validate($modelGoods)) {
                //Проверка на предоставления скидки и получение скидок, если они есть.
                // А так же заполняется вся модель Orders
                $model->getDiscount($modelBuyer, $modelGoods);

                //создать заказ
                $model->create($modelGoods->amount);
            }

            if ($model->hasErrors()) {

                return $response->getModelErrors($model->getErrors());
            }

            return $response->getSuccess($model->toShowOrder());
        }
    }

    public function buyers(Buyers $model): string
    {
        $response = new Response();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            return $response->getSuccess($model->getAll());
        }

        return $this->error();
    }

    public function goods(Goods $model): string
    {
        $response = new Response();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            return $response->getSuccess($model->getAll());
        }

        return $this->error();
    }
}
