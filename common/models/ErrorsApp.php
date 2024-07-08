<?php namespace app\common\models;

require_once "common/models/ErrorModel.php";

use app\common\models\ErrorModel;

class ErrorsApp
{
    private static array $errors = [];

    public function setError(string $placeError, string $textError): void
    {
        $error = new ErrorModel($placeError, $textError);

        self::$errors = array_merge(self::$errors, [$error]);
    }

    public function getErrors(): array
    {
        return self::$errors;
    }

    public function hasErrors(): bool
    {
        return !empty(self::$errors);
    }
}