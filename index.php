<?php

require __DIR__ . '/vendor/autoload.php';
require_once "app/models/Buyers.php";
require_once "app/models/Goods.php";
require_once "app/models/Orders.php";

use App\Controllers\MainController;
use App\models\Buyers;
use App\models\Goods;
use App\models\Orders;

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$controller = new MainController();

if ($uri === '/' || $uri === '' || $uri === '/index.php') {
    echo $controller->index();
} else if ($uri === '/buyers') {
    echo $controller->buyers(new Buyers());
} else if ($uri === '/goods') {
    echo $controller->goods(new Goods());
} else if ($uri === '/order') {
    echo $controller->order(new Orders());
} else {
    http_response_code(404);
    echo "Page not found";
}
