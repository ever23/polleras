<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cc\Mvc;

use Cc\Mvc;

/**
 * Description of CostumController
 *
 * @author Equipo
 */
class CostumController extends Controllers
{

    protected $Settings;

    public function __construct()
    {
        $file = dirname(__FILE__) . '/../settings.json';
        $this->Settings = new Json();
        $this->Settings->SetJsonFile($file);
        // parent::__construct();
    }

    protected function TingerNotificacion($tinger, $descripcion, $href, $icon, $type, $recurrencia = '1 day')
    {
        $user = Mvc::App()->Session['id_usuarios'];
        $notificaciones = Mvc::App()->DataBase()->Tab('notificaciones');
        $time = new \DateTime("now", new \DateTimeZone('America/Caracas'));
        $time2 = new \DateTime("now", new \DateTimeZone('America/Caracas'));
        $time2->modify("-" . $recurrencia);
        $notificaciones->Select("cod_tinger='" . $tinger . "' and id_usuarios='" . $user . "'");
        $n = $notificaciones->fetch();
        if ($notificaciones->num_rows == 0)
        {
            $notificaciones->Insert(null, $descripcion, $href, $icon, $time->format('Y-m-d H:i:s'), $type, false, $tinger, $user);
        } elseif ((new \DateTime($n->fech_notificacion)) < $time2 || $descripcion != $n->desc_notificacion || $type != $n->tipo_notificacion)
        {
            //var_dump((new \DateTime($n->fech_notificacion))->format('Y-m-d H:i:s'), $time2->format('Y-m-d H:i:s'));
            $n->tipo_notificacion = $type;
            $n->desc_notificacion = $descripcion;
            $n->visto = false;
            $n->fech_notificacion = $time->format('Y-m-d H:i:s');
            $n->Update();
        }
    }

    protected function DeleteNotificacion($tinger)
    {
        $notificaciones = Mvc::App()->DataBase()->Tab('notificaciones');
        $notificaciones->Delete("cod_tinger='" . $tinger . "'");
    }

    protected function dateWhere($consulta = null, $query = null)
    {
        $where = '';
        $time = new \DateTime($query);
        switch ($consulta)
        {
            case 'year':
                //$time->modify("+1 month");
                $where .= "YEAR(fecha)='" . $query . "'";
                break;
            case 'date':
                $where .= "concat(YEAR(fecha),'-',if(month(fecha)>9,month(fecha),concat('0',month(fecha))),'-',if(day(fecha)>9,day(fecha),concat('0',day(fecha))))='" . $query . "'";
                break;
            case 'month':
                $time->modify("+1 month");
                $where .= "concat(YEAR(fecha),'-',if(month(fecha)>9,month(fecha),concat('0',month(fecha))),'-',if(day(fecha)>9,day(fecha),concat('0',day(fecha))))>='" . $query . "-01' and concat(YEAR(fecha),'-',if(month(fecha)>9,month(fecha),concat('0',month(fecha))),'-',if(day(fecha)>9,day(fecha),concat('0',day(fecha))))<'" . $time->format('Y-m') . "-01'";
                break;
            case 'week':
                $query1 = $time->format("Y-m-d");
                $time->modify("+1 week");
                $where .= "concat(YEAR(fecha),'-',if(month(fecha)>9,month(fecha),concat('0',month(fecha))),'-',if(day(fecha)>9,day(fecha),concat('0',day(fecha))))>='" . $query1 . "' and concat(YEAR(fecha),'-',if(month(fecha)>9,month(fecha),concat('0',month(fecha))),'-',if(day(fecha)>9,day(fecha),concat('0',day(fecha))))<'" . $time->format('Y-m-d') . "'";
                break;

            default:
                return null;
        }
        return $where;
    }

}
