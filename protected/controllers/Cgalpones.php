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

    public function index(Json $res, DBtabla $galpones, $id_galpon = null)
    {
        if ($id_galpon)
        {
            $res['galpones'] = $galpones->Select("id_galpon='" . $id_galpon . "'")->FetchAll();
        } else
        {
            $res['galpones'] = $galpones->Select();
        }
    }

    public function resumen(Json $res, DBtabla $galpones, SelectorControllers $s)
    {
        $res['galpones'] = $galpones->Select()->FetchAll();
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
