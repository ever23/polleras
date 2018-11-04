<?php

namespace Cc\Mvc;

use Cc\Mvc;

/**
 * Modelo de formulario BasicForm
 *
 */
class LoginUserForm extends Form
{

    /**
     * 
     * @return DBtabla
     */
    protected function Campos()
    {
        $user = Mvc::App()->DataBase()->Tab('usuarios');
        $user->Select();
        $options = [];
        foreach ($user as $row)
        {
            $options[] = $row['user'];
        }
        $this->text('user')->Validator("required")->MensajeError("Usuario no valido")->in_options($options);
        $this->text('pass')->Validator("required")->MensajeError("Contraseña no valida");
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
