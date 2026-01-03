<?php

namespace App\Traits;

Trait ApiValidation
{
    public $validationErrorStatus = 422;
    public $uncompletedErrorStatus = 423;
    public $unauthorizedErrorStatus = 403;
    public $notFoundErrorStatus = 404;
    public $invalidErrorStatus = 400;
    public $notAcceptableStatus = 406;
    public $unknownStatus = 419;

    public function validationErrors($error)
    {
        return ['message' => 'The given data was invalid.', 'error' => $error];
    }

    public function withErrors($error)
    {
        return ['status' => 'failed', 'message' => $error];
    }

    public function withSuccess($msg)
    {
        return ['status' => 'success', 'message' => $msg];
    }
}
