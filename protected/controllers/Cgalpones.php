<?php

namespace Cc\Mvc;

use TablePdfIterator;

/**
 * controlador index sera ejecutado al llamar a / o a index/
 */
class Cgalpones extends CostumController implements AccessUserController
{

    public static function AccessUser()
    {
        return [];
    }

    public function index(Json $res, DBtabla $galpones, DBtabla $consumo_alimentos, Request $r, SelectorControllers $c, $id_galpon = null, $fecha = 'now')
    {
        $where = $id_galpon ? "id_galpon='" . $id_galpon . "'" : NULL;
        $time = new \DateTime($fecha);

        // $where = $where ? $where . ' and ' : '';
        // $having = "consumo_alimentos.fecha='" . date('Y-m-d') . "'";


        $galpones->Select(["galpones.*"], $where);
        //$res['sql'] = $galpones->sql;
        $gal = [];
        /*
         * @var $get DBRow 
         */
        foreach ($galpones->FetchAll() as $i => $row)
        {

            $r->Get['id_galpon'] = $row['id_galpon'];
            $c->CreateController('aves');
            $c->index();

            $consumo_alimentos->Select(['sum(cantidad) as consumo'], "id_galpon='" . $row['id_galpon'] . "' and YEAR(fecha)='" . $time->format('Y') . "' and MONTH(fecha)='" . $time->format('m') . "'  and DAY(fecha)='" . $time->format('d') . "'");
            $consumo = $consumo_alimentos->num_rows == 1 ? $consumo_alimentos->fetch()->consumo | 0 : 0;
            $gal[$i] = ['aves' => $res['aves'], 'consumo_dia' => $consumo] + $row->GetRow();
        }
        unset($res['aves']);
        unset($res['capacidad']);
        $r->Get['id_galpon'] = $id_galpon;
        $res['galpones'] = $gal;
    }

    public function resumen(Json $res, DBtabla $galpones, SelectorControllers $s)
    {
        $s->index();
        $s->CreateController('huevos');
        $s->Resumen();
        $res['ventas_huevos'] = $res['ventas'];
        unset($res['ventas']);
        $s->CreateController('Aves');
        $s->Resumen();
        $res['ventas_aves'] = $res['ventas'];
        $res['compras_aves'] = $res['compras'];
        $res['muertes_aves'] = $res['muertes'];
        unset($res['muertes']);
        unset($res['ventas']);
        unset($res['compras']);
    }

    public function insertar(Json $res, DBtabla $galpones, Request $req)
    {
        $form = new GalponesForm();
        if ($form->IsSubmited())
        {
            if (!$form->IsValid())
            {
                $res['insert'] = false;
                $res['error'] = $form->ApiFormError();
                return;
            }
            if ($galpones->Insert($form))
            {
                $res['insert'] = true;
            } else
            {
                $res['insert'] = false;
                $res['error'] = 'ha ocurrido un error al agregar el galpon ';
            }
        } else
        {
            //$res['inserta'] = var_dump($form);
            $res['insert'] = false;
            $res['error'] = 'los datos no son validos';
        }
    }

    public function editar(Json $res, DBtabla $galpones, $id_galpon)
    {
        $form = new GalponesForm();
        if ($form->IsSubmited())
        {
            if (!$form->IsValid())
            {
                $res['insert'] = false;
                $res['error'] = $form->ApiFormError();
                return;
            }
            if ($galpones->Update($form, ['id_galpon' => $id_galpon]))
            {
                $res['insert'] = true;
            } else
            {
                $res['insert'] = false;
                $res['error'] = 'ha ocurrido un error al editar el galpon';
            }
        } else
        {
            //$res['inserta'] = var_dump($form);
            $res['insert'] = false;
            $res['error'] = 'los datos no son validos';
        }
    }

}
