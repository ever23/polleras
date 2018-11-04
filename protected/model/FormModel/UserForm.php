<?php

namespace Cc\Mvc;

/**
 * Modelo de formulario BasicForm
 *
 */
class UserForm extends Form
{

    protected $errorPass = false;

    /**
     * 
     * @return DBtabla
     */
    protected function Campos()
    {
        $this->text('user')->Validator("required")->MensajeError("Nombre de usuario no valido");
        $this->text('permisos')->Validator("required")->MensajeError("Permiso no valido");
        $this->text('nombres')->Validator("required")->MensajeError("Nombres no validos");
        $this->text('apellidos')->Validator("required")->MensajeError("Apellidos no validos");
        $this->text('pass1')->Validator("required");
        $this->text('pass2')->Validator("required");
        // $this->text('id_granjas')->Validator("required|numeric");
    }

    /**
     * Este metodo se ejecutara cuando se reciban datos del formulario
     *
     */
    protected function OnSubmit()
    {
        if ($this->pass1 != $this->pass2)
        {
            $this->errorPass = true;
            $this->valid = false;
        }
    }

    public function ApiFormError()
    {
        return parent::ApiFormError() + ($this->errorPass ? ['pass2' => 'Contraseña no coincide'] : []);
    }

}

class EditarUserForm extends Form
{

    /**
     * 
     * @return DBtabla
     */
    protected function Campos()
    {
        $this->text('user')->Validator("required")->MensajeError("Nombre de usuario no valido");
        $this->text('permisos')->Validator("required")->MensajeError("Permiso no valido");
        $this->text('nombres')->Validator("required")->MensajeError("Nombres no validos");
        $this->text('apellidos')->Validator("required")->MensajeError("Apellidos no validos");
        $this->text('id_usuarios')->Validator("required|numeric");

        // $this->text('id_granjas')->Validator("required|numeric");
    }

    /**
     * Este metodo se ejecutara cuando se reciban datos del formulario
     *
     */
    protected function OnSubmit()
    {
        
    }

}

class FormEditarContrasena extends Form
{

    protected $errorPass = false;

    /**
     * 
     * @return DBtabla
     */
    protected function Campos()
    {
        $this->text('pass')->Validator("required");
        $this->text('pass1')->Validator("required");
        $this->text('pass2')->Validator("required");
        // $this->text('id_granjas')->Validator("required|numeric");
    }

    /**
     * Este metodo se ejecutara cuando se reciban datos del formulario
     *
     */
    protected function OnSubmit()
    {
        if ($this->pass1 != $this->pass2)
        {
            $this->errorPass = true;
            $this->valid = false;
        }
    }

    public function ApiFormError()
    {
        return parent::ApiFormError() + ($this->errorPass ? ['pass2' => 'Contraseña no coincide'] : []);
    }

}
