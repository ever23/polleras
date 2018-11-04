<?php

namespace Cc\Mvc;

/**
 * Modelo de formulario BasicForm
 *
 */
class Form extends FormModel
{

    public function __construct($action = NULL, $method = 'POST', $protected = false, $inyected = false)
    {
        parent::__construct($action, $method, $protected, true);
        $this->NameSubmited = 'Submited';
        if (!$inyected)
        {
            $this->Request();
        }
    }

    public function ApiFormError()
    {
        return $this->GetError();
    }

}
