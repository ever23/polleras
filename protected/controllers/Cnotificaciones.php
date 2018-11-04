<?php

namespace Cc\Mvc;

/**
 * controlador index sera ejecutado al llamar a / o a index/
 */
class Cnotificaciones extends CostumController implements AccessUserController
{

    /**
     *
     * @var Json 
     */
    public static function AccessUser()
    {
        return [
// 'NoAth' => ['font'],
        ];
    }

    public function index(Json $res, DBtabla $notificaciones, SelectorControllers $c, $id_notificacion = null)
    {
        if ($id_notificacion)
        {
            $notificaciones->Update(['visto' => true], "id_notificacion='" . $id_notificacion . "'");
            $res['visto'] = true;
        } else
        {
            $notificaciones->Select(null, null, null, null, null, 'fech_notificacion desc', '20');
            $res['data'] = $notificaciones->FetchAll();
        }
    }

    public function now(Json $res, DBtabla $notificaciones, SelectorControllers $c, $fech_notificacion = null)
    {
        $c->aves();
        $c->CreateController('notificaciones');
        $c->alimentos();
        $notificaciones->Select(null, null, null, null, null, 'fech_notificacion desc', '20');
        $res['data'] = $notificaciones->FetchAll();
        if ($fech_notificacion)
        {
            $notificaciones->Select(null, "visto=false and fech_notificacion>'" . $fech_notificacion . "'", null, null, null, 'fech_notificacion desc', '20');
            $res['new'] = $notificaciones->FetchAll();
//$res['new'] = true;
        }
    }

    public function aves(Json $res, DBtabla $galpones, DBtabla $compra_aves, DBtabla $venta_aves, DBtabla $mortalidad, DBtabla $produccion_huevos, SelectorControllers $c, Request $r)
    {

        $c->CreateController('aves');
        $galpones->Select();

        $sumAves = 0;
        $sumMuertes = 0;
        $sumCapacidad = 0;
        $sumStock = 0;
        foreach ($galpones as $row)
        {
            $r->Get['id_galpon'] = $row['id_galpon'];
            $c->CreateController('aves');
            $c->index();

            $porcentaje = $res['aves'] == 0 ? 0 : ($res['aves'] * 100) / $row['capacidad'];
            $sumCapacidad+=$row['capacidad'];
            $sumStock+=$res['aves'];
            $tinger = "capacidadG" . $row['id_galpon'];
            if ($porcentaje == 0)
            {
                $mensaje = "El galpon " . $row['nombre'] . " no se esta usando";
            } else
            {
                $mensaje = "Solo se esta usando el " . number_format($porcentaje, 2) . "% de la capacidad del galpon " . $row['nombre'];
            }

            if ($porcentaje < $this->Settings['usoGalpon'])
            {
                $this->TingerNotificacion($tinger, $mensaje, "/polleras/aves/" . $row['id_galpon'], "fa-info", "info", '3 day');
            } else
            {
                $this->DeleteNotificacion($tinger);
            }
            $tinger2 = "muertes" . $row['id_galpon'];
            $tinger4 = "produccion" . $row['id_galpon'];
            if ($porcentaje == 0)
            {
                $this->DeleteNotificacion($tinger2);
                $this->DeleteNotificacion($tinger4);
                continue;
            }


            //existencia mes anterior
            $where = "id_galpon='" . $row['id_galpon'] . "' and MONTH(fecha)<'" . date('m') . "'";
            $compra_aves->Select(['sum(cantidad) as c'], $where);
            $compras = $compra_aves->num_rows == 1 ? $compra_aves->fetch()->c : 0;
            $venta_aves->Select(['sum(cantidad) as c'], $where);
            $ventas = $venta_aves->num_rows == 1 ? $venta_aves->fetch()->c : 0;
            $mortalidad->Select(['sum(cantidad) as c'], $where);
            $muertes = $mortalidad->num_rows == 1 ? $mortalidad->fetch()->c : 0;
            $aves_ante = $compras - ($ventas + $muertes);
            // existencia este mes 
            $where = "id_galpon='" . $row['id_galpon'] . "' and MONTH(fecha)='" . date('m') . "' and YEAR(fecha)='" . date('Y') . "'";
            $compra_aves->Select(['sum(cantidad) as c'], $where);
            $compras = (int) $compra_aves->num_rows == 1 ? $compra_aves->fetch()->c : 0;
            $venta_aves->Select(['sum(cantidad) as c'], $where);
            $ventas = (int) $venta_aves->num_rows == 1 ? $venta_aves->fetch()->c : 0;
            $aves_este = ($aves_ante + $compras) - $ventas;
            //muertes este mes 
            $mortalidad->Select(['sum(cantidad) as c'], $where);
            $muertes = (int) $mortalidad->num_rows == 1 ? $mortalidad->fetch()->c : 0;
            $muertes = is_null($muertes) ? 0 : $muertes;
            $procentaje_muertes = $muertes == 0 ? 0 : ($muertes * 100) / $aves_este;

            $mensaje2 = "EL " . number_format($procentaje_muertes, 2) . "% de la aves del galpon " . $row['nombre'] . " se han muerto este mes ";
            // var_dump($muertes, $aves_ante, $aves_este, $procentaje_muertes);
            // echo ' -----------------';
            $sumAves+=$aves_este;
            $sumMuertes+=$muertes;
            if ($procentaje_muertes > (int) $this->Settings['muertes'])
            {
                $this->TingerNotificacion($tinger2, $mensaje2, "/polleras/aves/" . $row['id_galpon'], "fa-warning", "danger", '1 day');
            } else
            {
                $this->DeleteNotificacion($tinger2);
            }
            //produccion de huevos 
            $w = "YEAR(fecha)='" . date('Y') . "' and MONTH(fecha)='" . date('m') . "' and id_galpon='" . $row['id_galpon'] . "'";
            $produccion_huevos->Select(['sum(cantidad) as cantidad', 'count(fecha) as c'], $w, null, "fecha", null, 'fecha DESC');
            $sumph = 0;
            foreach ($produccion_huevos as $ph)
                $sumph+=$ph['cantidad'];
            $porcentaje_produccion = $sumph == 0 ? 0 : ($sumph * 100) / ($produccion_huevos->num_rows * $res['aves']);
            //var_dump($p['c'], $p['cantidad'], $produccion_huevos->sql);
            // $res['sql'] = $produccion_huevos->sql;
            $mensaje4 = "La produccion de huevos en el galpon " . $row['nombre'] . " esta baja  al " . number_format($porcentaje_produccion, 2) . "% de su capacidad para este mes ";
            $icon = "fa-info";
            $tipo = "info";
            if ($porcentaje_produccion < 70)
            {
                $icon = "fa-warning";
                $tipo = "warning";
            }
            if ($porcentaje_produccion < 50)
            {
                $icon = "fa-warning";
                $tipo = "danger";
            }
            if ($porcentaje_produccion == 0)
                $mensaje4 = "En el galpon " . $row['nombre'] . " no hay produccion ";
            if ($porcentaje_produccion < (int) $this->Settings['produccion'])
            {
                $this->TingerNotificacion($tinger4, $mensaje4, "/polleras/huevos/" . $row['id_galpon'], $icon, $tipo, '1 day');
            } else
            {
                $this->DeleteNotificacion($tinger4);
            }
            //$produccion_huevos->Select($campos, $where, $joins, $group, $having, $order, $limit);
        }

        $porcentajeMiertesFinal = $sumMuertes == 0 ? 0 : ($sumMuertes * 100) / $sumAves;
        $tinger3 = "muertes";
        $mensaje3 = "EL " . number_format($porcentajeMiertesFinal, 2) . "% de la aves de toda la granja se han muerto este mes ";
        if ($porcentajeMiertesFinal > (int) $this->Settings['muertes'])
        {
            $this->TingerNotificacion($tinger3, $mensaje3, "/polleras/aves", "fa-warning", "danger", '1 day');
        } else
        {
            $this->DeleteNotificacion($tinger3);
        }
        $porcentajeCapacidad = $sumStock == 0 ? 0 : ($sumStock * 100) / $sumCapacidad;
        $tinger = "capacidad";
        if ($porcentaje == 0)
        {
            $mensaje = "no hay aves en la granja";
        } else
        {
            $mensaje = "Solo se esta usando el " . number_format($porcentajeCapacidad, 2) . "% de la capacidad instalada de la granja ";
        }

        if ($porcentaje < $this->Settings['usoGalpon'])
        {
            $this->TingerNotificacion($tinger, $mensaje, "/polleras/aves", "fa-info", "info", '3 day');
        } else
        {
            $this->DeleteNotificacion($tinger);
        }
        unset($r->Get['id_galpon']);
        unset($res['aves']);
    }

    public function alimentos(Json $res, DBtabla $consumo_alimentos, SelectorControllers $c, Request $r)
    {
        $c->CreateController('alimentos');

        $time = new \DateTime();
        //$time->modify('-1 month');

        $c->resumen();
        $alimentos = $res['alimentos'];
        $media_consumo = $res['media_consumo'];
        $consumo = 0;
        $tinger = "BajoAlimento";
        $mensaje = "Alerta le faltan  para cubrir la media de consumo para este mes ";
        $consumo_alimentos->Select(['sum(cantidad) as cantidad', 'fecha'], "YEAR(fecha)='" . $time->format('Y') . "' and MONTH(fecha)='" . $time->format('m') . "' ", null, 'month(fecha)');
        foreach ($consumo_alimentos as $row)
        {
            $consumo+=$row['cantidad'];
        }

        $alim = $consumo > 0 ? ($consumo / $consumo_alimentos->num_rows) : 0;
        // var_dump($alim + $alimentos, $consumo, $alimentos, $alim, $media_consumo, $consumo_alimentos->num_rows);
        if (($alim + $alimentos) < $media_consumo)
        {
            $mensaje = "Alerta le faltan " . number_format($media_consumo - ($alim + $alimentos), 2) . " " . $this->Settings['umalimentos'] . " de alimento para cubrir la media de consumo para este mes ";
            $this->TingerNotificacion($tinger, $mensaje, "/polleras/alimentos", "fa-warning", "warning", '1 day');
        } else
        {
            $this->DeleteNotificacion($tinger);
        }

        $res->CreateJson([], true);
    }

}
