<?php namespace app\common\response;

include_once 'common/Constants.php';

use app\common\Constants;
use Exception;

class Response
{
    /**
     * HTTP-code of response
     *
     * @var int
     */
    public int $code;

    /**
     * Resulting data set
     *
     * @var array
     */
    public array $response;

    /**
     * Handler messages about success response
     * @param mixed $data
     *
     * @return string
     */
    public function getSuccess(mixed $data): string
    {
        try {
            header('Content-Type: application/json');

            $this->response = (array)$data;
            $this->code = Constants::SUCCESS_CODE;

            return json_encode($this, JSON_UNESCAPED_UNICODE);
        } catch (Exception $exception) {

            return $this->getExceptionError($exception, Constants::WARNING_CODE);
        }
    }

    /**
     * Handler errors
     * @param array $errors
     *
     * @return string
     */
    public function getModelErrors(array $errors): string
    {
        try {
            header('Content-Type: application/json');

            $this->getError($errors);

            http_response_code($this->code);

            return json_encode($this, JSON_UNESCAPED_UNICODE);
        } catch (Exception $exception) {
            return $this->getExceptionError($exception);
        }
    }

    /**
     * Message about error
     * @param array $errors
     *
     * @return void
     */
    private function getError(array $errors): void
    {
        $data = [];

        if (count($errors) > 0) {
            foreach($errors as $item) {
                $data[] = $item;
            }
        } else if (count($errors) === 0) {
            $data['message'] = "Ошибок не обноружено";
        }

        $this->response = $data;
        $this->code = Constants::ERROR_CODE;
    }

    /**
     * Handler Exception
     * @param Exception $exception
     * @param int $code
     *
     * @return string
     */
    public function getExceptionError(Exception $exception, int $code = 0): string
    {
        header('Content-Type: application/json');

        $data = [];
        $data['message'] = $exception->getMessage();
        $data['trace'] = $exception->getTrace();

        $this->response = $data;
        $this->code = ($code !== 0) ? $code : Constants::EXCEPTION_CODE;

        http_response_code($this->code);

        return json_encode($this, JSON_UNESCAPED_UNICODE);
    }
}