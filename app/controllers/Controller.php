<?php namespace App\controllers;

use app\common\models\ErrorModel;
use app\common\response\Response;

class Controller
{
    protected function isFilled(array $fields): bool
    {
        $isNotEmpty = array();
        foreach ($fields as $field) {
            $isNotEmpty[] = !empty($field);
        }

        if (count($fields) === count($isNotEmpty) && $this->allTrue($isNotEmpty)) {
            return true;
        }
        return false;
    }

    private function allTrue(array $array): bool {
        foreach ($array as $value) {
            if ($value !== true) {
                return false;
            }
        }
        return true;
    }

    public function error(): string
    {
        $errorModel = new ErrorModel(get_called_class(), 'Неверно составлен запрос');

        return (new Response())->getModelErrors([$errorModel]);
    }
}