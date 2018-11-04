<?php

namespace Cc\Mvc;

/**
 * Modelo de formulario BasicForm
 *
 */
class MedicinasForm extends Form
{

    /**
     * 
     * @return DBtabla
     */
    protected function Campos()
    {
        $this->text('descripcion')->Validator("required")->MensajeError("Descripcion no valida");
        $this->text('tipo')->Validator("required")->MensajeError("Tipo no valido");
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

class CompraMedicinasForm extends Form
{

    /**
     * 
     * @return DBtabla
     */
    protected function Campos()
    {

        $this->text('id_medicina')->Validator("required|numeric")->MensajeError("Medicamento no valido");
        $this->text('cantidad')->Validator("required|numeric")->MensajeError("Cantidad no valida");
        $this->text('costo')->Validator("required|numeric")->MensajeError("Costo no valido");
        $this->date('fecha')->Validator('required')->MensajeError("Fecha no valida");
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

class ConsumoMedicinasForm extends Form
{

    /**
     * 
     * @return DBtabla
     */
    protected function Campos()
    {
        $this->text('id_galpon')->Validator("required|numeric")->MensajeError("Galpon no valido");
        $this->text('id_medicina')->Validator("required|numeric")->MensajeError("Medicamento no valido");
        $this->text('detalles')->Validator("required")->MensajeError("Detalles no validos");
        $this->text('cantidad')->Validator("required|numeric")->MensajeError("Cantidad no valida");

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
