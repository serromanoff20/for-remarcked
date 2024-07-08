<?php namespace App\models;

require_once "common/models/ModelApp.php";
require_once "config/db/Connect.php";

use app\common\models\ModelApp;
use app\config\db\Connect;
use PDO;
use PDOException;

class Buyers extends ModelApp
{
    public int|null $id;
    public string $fio = '';
    public string $date_birth = '';
    public string $gender = '';

    protected array $props = [
        'fio',
        'date_birth',
        'gender',
    ];

    public function isExistedBuyer(array $params): int
    {
        $connect = new Connect();

        foreach ($params as $key => $param) {
            $input_prop = str_replace('_buyer', '', $key);
            foreach ($this->props as $prop) {
                if ($prop === $input_prop) {
                    $this->$prop = $param;
                }
            }
        }
        if (empty($this->fio) || empty($this->date_birth) || empty($this->gender)) {
            $this->setError(get_called_class(), 'Не все необходимые параметры были заполнены');

            return 0;
        }

        $isExistedBuyer = $connect->connection->query("SELECT * FROM re.buyers where fio='" . $this->fio ."' AND date_birth='" . $this->date_birth . "' AND gender = '" . $this->gender . "'")->fetch(PDO::FETCH_ASSOC);

        if ($isExistedBuyer) {
            foreach ($isExistedBuyer as $key => $value) {
                $this->$key = $value;
            }
            return $this->id;
        }

        return 0;
    }

    public function getAll(): array|false
    {
        $connect = new Connect();
        return $connect->connection->query('SELECT * FROM re.buyers')->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOne(int $id): ?self
    {
        $connect = new Connect();
        $row = $connect->connection->query('SELECT * FROM re.buyers WHERE id = ' . $id . ' LIMIT 1')->fetch(PDO::FETCH_ASSOC);

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

    public function create(): void
    {
        $connect = new Connect();
        try {
            $str_nameProps = implode(', ', $this->props);

            $stmt = $connect->connection->prepare(
                "INSERT INTO re.buyers (" . $str_nameProps . ") VALUES (:fio, :date_birth, :gender)"
            );
            foreach ($this->props as $prop) {
                $stmt->bindParam(':'.$prop, $this->$prop);
            }

            if ($stmt->execute()) {
                $this->id = $connect->connection->lastInsertId();

//                return $this->id;
            } else {
                $this->setError(get_called_class(), "Запрос не выполнился");

//                return 0;
            }
        } catch (PDOException $exception) {
            $this->setError(get_called_class(), $exception->getMessage());

//            return 0;
        }
    }
}