<?php namespace app\common\models;

class ErrorModel
{
    public string $place;
    public string $message;

    /**
     * ErrorModel constructor.
     * @param string $place
     * @param string $message
     */
    public function __construct(string $place, string $message)
    {
        $this->place = $place;
        $this->message = $message;
    }
}