<?php

namespace Cc\Mvc;

/**
 * controlador index sera ejecutado al llamar a / o a index/
 */
class Cindex extends CostumController implements AccessUserController
{

    public static function AccessUser()
    {
        return [
            'NoAth' => ['font'],
        ];
    }

    public function index(Json $res, DBtabla $usuarios, DBtabla $sessiones, SelectorControllers $c)
    {

        $usuarios->Select(['count(*) as c']);
        $res['usuarios'] = $usuarios->num_rows == 1 ? $usuarios->fetch()->c : 0;


        $c->ventas();
        $c->gastos();
        $c->CreateController('alimentos');
        $c->index();
        $c->CreateController('aves');
        $c->index();
        $c->CreateController('huevos');
        $c->index();
        $c->CreateController('user');
        $c->sessiones();
        $month = date('m');
        if ($res['ventas'][$month - 1] - $res['gastos'][$month - 1] < 0)
        {
            $this->TingerNotificacion("perdidas", "Alerta se estan generando perdidas  de " . ($res['ventas'][$month - 1] - $res['gastos'][$month - 1]) . " " . $this->Settings['moneda'] . " en la granja", "/polleras/", "fa-warning", "danger");
        } else
        {
            $this->DeleteNotificacion("perdidas");
        }

        $count = 0;
        foreach ($res['sessiones'] as $row)
        {
            if ($row['status'] == 'activo')
            {
                $count++;
            }
        }
        $res['sessiones'] = $count;
    }

    public function ventas(Json $res, DBtabla $venta_aves, DBtabla $venta_huevos, $year = null)
    {
        $meses = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $year = $year ? $year : date('Y');
        $venta_aves->Select(['sum(costo*cantidad) as precio', 'month(fecha) as mes'], "year(fecha)='" . $year . "'", 'month(fecha)');
        foreach ($venta_aves as $row)
            $meses[$row['mes'] - 1] += $row['precio'];
        $venta_huevos->Select(['sum(costo*cantidad) as precio', 'month(fecha) as mes'], "year(fecha)='" . $year . "'", 'month(fecha)');
        foreach ($venta_huevos as $row)
            $meses[$row['mes'] - 1] += $row['precio'];
        $res['ventas'] = $meses;
    }

    public function gastos(Json $res, DBtabla $compra_alimentos, DBtabla $compra_medicinas, DBtabla $compra_aves, DBtabla $pago_nomina, $year = null)
    {
        $meses = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $year = $year ? $year : date('Y');
        $compra_alimentos->Select(['sum(costo*cantidad) as precio', 'month(fecha) as mes'], "year(fecha)='" . $year . "'", 'month(fecha)');
        foreach ($compra_alimentos as $row)
            $meses[$row['mes'] - 1] += $row['precio'];
        $compra_medicinas->Select(['sum(costo*cantidad) as precio', 'month(fecha) as mes'], "year(fecha)='" . $year . "'", 'month(fecha)');
        foreach ($compra_medicinas as $row)
            $meses[$row['mes'] - 1] += $row['precio'];
        $compra_aves->Select(['sum(costo*cantidad) as precio', 'month(fecha) as mes'], "year(fecha)='" . $year . "'", 'month(fecha)');
        foreach ($compra_aves as $row)
            $meses[$row['mes'] - 1] += $row['precio'];
        $pago_nomina->Select(['sum(pago) as precio', 'month(fecha) as mes'], "year(fecha)='" . $year . "'", 'month(fecha)');
        foreach ($pago_nomina as $row)
            $meses[$row['mes'] - 1] += $row['precio'];
        $res['gastos'] = $meses;
    }

    public function Delete(Json $res, \Cc\iDataBase $db, $tabla, $name, $value)
    {
        $t = $db->Tab($tabla);
        if ($t->Delete([$name => $value]))
        {
            $res['eliminado'] = true;
        } else
        {
            $res['eliminado'] = false;
        }
    }

}
