<?php namespace app\config\db;

require_once 'vendor/autoload.php';

use Dotenv\Dotenv;
use PDO;

class Connect
{
    public object $connection;

    public function __construct() {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        $this->connection = new PDO("pgsql:host=" . $_ENV['DB_HOST'] . ";port=" . $_ENV['DB_PORT'] . ";dbname=" . $_ENV['DB_DATABASE'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'] );
    }
}