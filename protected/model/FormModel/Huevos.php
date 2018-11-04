<?php

namespace Cc\Mvc;

/**
 * Modelo de formulario BasicForm
 *
 */
class ProduccionHuevosForm extends Form
{

    /**
     * 
     * @return DBtabla
     */
    protected function Campos()
    {
        $this->text('id_galpon')->Validator("required|numeric")->MensajeError("Galpon no valido");
        $this->text('cantidad')->Validator("required|numeric")->MensajeError("Cantidad no valida");
        $this->text('detalles')->Validator("required")->MensajeError("Detalles no valido");
        $this->text('tipo')->Validator('required')->MensajeError("tipo no valido")->in_options(['grande', 'pequeño']);
        $this->date('fecha')->Validator('required')->MensajeError("Fecha no valida");
        // $this->text('id_granjas')->Validator('required|numeric')->MensajeError("Granja no valida");
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

class VentaHuevosForm extends Form
{

    /**
     * 
     * @return DBtabla
     */
    protected function Campos()
    {

        $this->text('cantidad')->Validator("required|numeric")->MensajeError("Cantidad no valida");
        $this->text('costo')->Validator("required|numeric")->MensajeError("Costo no valido");
        $this->date('fecha')->Validator('required')->MensajeError("Fecha no valida");
        $this->text('detalles')->Validator('required')->MensajeError("detalles no validos");
        $this->text('tipo')->Validator('required')->MensajeError("tipo no valido")->in_options(['grande', 'pequeño']);
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
