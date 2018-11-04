<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cc\Mvc;

use Cc\Mvc;

/**
 * Description of AuthMinea
 *
 * @author usuario
 */
class Auth extends AuteticateUserDB
{

    /**
     * 
     * @return array
     */
    protected function InfoUserDB()
    {
        return [
            self::TablaUsers => 'usuarios',
            self::CollPassword => 'hash',
            self::CollUser => 'user',
            self::CollUserType=>'permisos'
        ];
    }

    public function Tabla()
    {
        return $this->DBtabla;
    }

    /**
     * 
     */
    public function OnFailed()
    {
        switch ($this->IsFailed())
        {
            case self::FailedAuth:

            case self::FailedDataBase:
            case self::NoAuth:
            default :
                return false;
        }
    }

//put your code here
}
