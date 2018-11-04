<?php

namespace Cc\Mvc;

/**
 * Modelo de formulario BasicForm
 *
 */
class CompraAvesForm extends Form
{

    /**
     * 
     * @return DBtabla
     */
    protected function Campos()
    {
        $this->text('id_galpon')->Validator("required|numeric")->MensajeError("Galpon no valido");
        $this->text('cantidad')->Validator("required|numeric")->MensajeError("Cantidad no valida");
        $this->text('costo')->Validator("required|numeric")->MensajeError("COsto no valido");
        $this->date('fecha')->Validator('required')->MensajeError("Fecha no valida");
        $this->text('detalles')->Validator('required')->MensajeError("Detalles no valido");
        //$this->text('id_granjas')->Validator('required|numeric')->MensajeError("Granja no valida");
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

class MortalidadAvesForm extends Form
{

    /**
     * 
     * @return DBtabla
     */
    protected function Campos()
    {
        $this->text('id_galpon')->Validator("required|numeric")->MensajeError("Galpon no valido");
        $this->text('cantidad')->Validator("required|numeric")->MensajeError("Cantidad no valida");
        $this->text('rason')->Validator("required")->MensajeError("Rason no valida");
        $this->date('fecha')->Validator('required')->MensajeError("Fecha no valida");
        //$this->text('id_granjas')->Validator('required|numeric')->MensajeError("Granja no valida");
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
