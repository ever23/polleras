<?php

namespace Cc\Mvc;

/**
 * Modelo de formulario BasicForm
 *
 */
class GalponesForm extends Form
{

    /**
     * 
     * @return DBtabla
     */
    protected function Campos()
    {
        $this->text('nombre')->Validator("required")->MensajeError("Descripcion  no valida");
        $this->text('capacidad')->Validator("required|numeric")->MensajeError("Capacidad  no valida");
    }

    /**
     * Este metodo se ejecutara cuando se reciban datos del formulario
     *
     */
    protected function OnSubmit()
    {
        //tu codigo aqui
    }

}
