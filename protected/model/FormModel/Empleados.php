<?php

namespace Cc\Mvc;

/**
 * Modelo de formulario BasicForm
 *
 */
class EmpleadosForm extends Form
{

    /**
     * 
     * @return DBtabla
     */
    protected function Campos()
    {

        $this->text('ci_empleado')->Validator("required|maxlength:8|numeric")->MensajeError("Cedula no valida");
        $this->text('nombres')->Validator("required")->MensajeError("Nombres no validos");
        $this->text('apellidos')->Validator('required')->MensajeError("Apellidos no validos");
        $this->text('sueldo')->Validator('required|numeric')->MensajeError("Sueldo no valido");
        $this->text('cargo')->Validator('required')->MensajeError("Cargo no valido");
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

class PagoNominaForm extends Form
{

    /**
     * 
     * @return DBtabla
     */
    protected function Campos()
    {

        $this->text('pago')->Validator("required|numeric")->MensajeError("Pago no valido");
        $this->date('ci_empleado')->Validator('required')->MensajeError("Cedula no valida");
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
