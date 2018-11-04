<?php

namespace Cc\Mvc;

/**
 * Modelo de formulario BasicForm
 *
 */
class SettingsForm extends Form
{

    /**
     * 
     * @return DBtabla
     */
    protected function Campos()
    {

        $this->text('nombre')->Validator("required")->MensajeError("Nombre no valido");
        $this->text('moneda')->Validator("required")->MensajeError("Moneda no valida");
        $this->text('umalimentos')->Validator("required")->MensajeError("Unidad de medida para alimentos no valida");
        $this->text('produccion')->Validator("required|numeric")->MensajeError("El porcentaje de produccion de huevos no es valido");
        $this->text('usoGalpon')->Validator("required|numeric")->MensajeError("El porcentaje de uso de galpones no es valido");
        $this->text('muertes')->Validator("required|numeric")->MensajeError("El porcentaje de muertes caeptables no es valido");
        $this->file('imagen')->MensajeError("Imagen no valida");
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
