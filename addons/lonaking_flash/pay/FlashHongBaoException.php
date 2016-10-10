<?php
class FlashHongBaoException extends Exception
{
    private $error_message;
    private $error_code;
    public function __construct($error_message, $error_code)
    {
        $this->error_message = $error_message;
        $this->error_code    = $error_code;
        $this->message       = $error_message;
        $this->code          = $error_code;
    }
    public function getErrorCode()
    {
        return $this->error_code;
    }
    public function setErrorCode($error_code)
    {
        $this->error_code = $error_code;
    }
    public function getErrorMessage()
    {
        return $this->error_message;
    }
    public function setErrorMessage($error_message)
    {
        $this->error_message = $error_message;
    }
}