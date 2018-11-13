<?php

namespace Cc\Mvc;

use TablePdfIterator;

/**
 * controlador index sera ejecutado al llamar a / o a index/
 */
class Cmedicinas extends CostumController implements AccessUserController
{

    public static function AccessUser()
    {
        return [
            'admin' => [
                'NoAccess' => ['editar', 'eliminar']
            ]
        ];
    }

    public function index(Json $res, DBtabla $medicinas, $id_medicina)
    {
        $medicinas->select("id_medicina='" . $id_medicina . "'");
        $res['medicina'] = $medicinas->fetch();
    }

    public function inventario(Json $res, DBtabla $medicinas, DBtabla $compra_medicinas, DBtabla $consumo_medicinas, $id_medicina = null)
    {

        $data = [];
        if (!is_null($id_medicina))
        {
            $medicinas->Select("id_medicina='" . $id_medicina . "'");
        } else
        {
            $medicinas->Select();
        }

        /* @var $v DBRow */
        foreach ($medicinas->FetchAll() as $i => $v)
        {
            $where = "id_medicina='" . $v['id_medicina'] . "'";
            $compra_medicinas->Select(['sum(cantidad) as compra'], $where);
            $compra = $compra_medicinas->num_rows == 1 ? $compra_medicinas->fetch()->compra : 0;
            $consumo_medicinas->Select(['sum(cantidad) as consumo'], $where);
            $consumo = $consumo_medicinas->num_rows == 1 ? $consumo_medicinas->fetch()->consumo : 0;


            $data[] = $v->GetRow() + ['stock' => $compra - $consumo];
        }
        $res['inventario'] = $data;
        //$res['sql'] = $medicinas->sql;
    }

    public function insertar(Json $res, DBtabla $medicinas, Request $req)
    {
        $form = new MedicinasForm();
        if ($form->IsSubmited())
        {
            if (!$form->IsValidid())
            {
                $res['errror'] = $form->ApiFormError();
                $res['insert'] = false;
                return;
            }
            if ($medicinas->Insert($form))
            {
                $res['insert'] = true;
            } else
            {
                $res['insert'] = false;
                $res['error'] = 'ha ocurrido un error al agregar el medicamento';
            }
        } else
        {
            //$res['inserta'] = var_dump($form);
            $res['insert'] = false;
            $res['error'] = 'no hay datos';
        }
    }

    public function compras(Json $res, DBtabla $compra_medicinas, $consulta = null, $query = null)
    {

        $where = $this->dateWhere($consulta, $query);
        $compra_medicinas->Select(['medicinas.descripcion', 'medicinas.tipo', 'compra_medicinas.*', 'compra_medicinas.costo * compra_medicinas.cantidad as costo_total'], $where, ['>medicinas' => 'id_medicina']);
        $res['compras'] = $compra_medicinas->FetchAll();
    }

    public function consumos(Json $res, DBtabla $consumo_medicinas, $consulta = null, $query = null, $id_galpon = null)
    {
        $where = $id_galpon ? "id_galpon='" . $id_galpon . "'" : NULL;
        if ($consulta)
        {
            $where = $where ? $where . ' and ' : '';
            $where.=$this->dateWhere($consulta, $query);
        }
        $consumo_medicinas->Select(['medicinas.descripcion', 'medicinas.tipo', 'consumo_medicinas.*', 'galpones.nombre'], $where, ['>medicinas' => 'id_medicina', '>galpones' => 'id_galpon']);
        $res['consumos'] = $consumo_medicinas->FetchAll();
        //$res['sql'] = $consumo_medicinas->sql;
    }

    public function resumen(SelectorControllers $c)
    {
        $c->consumos();
        $c->compras();
        $c->inventario();
    }

    public function reporte(Json $res, SelectorControllers $s)
    {
        $s->resumen();
        $pdf = new ReporteFPDF('P', 'mm', 'Letter');
        $pdf->SetFont('arial', 'B', 18);
        $pdf->AddPage();
        $pdf->ln();
        $pdf->MultiCell(200, 5, "INVENTARIO", 0, 'C');
        $pdf->ln();
        $pdf->ln();
        $tablapdf = new TablePdfIterator(5, [90, 90, 90], 'arial', 12);
        $tablapdf->AddCollHead('descripcion', 'DESCRIPCION', 110, 'J');
        $tablapdf->AddCollHead('tipo', 'TIPO', 40, 'C');
        $tablapdf->AddCollHead('stock', 'STOCK', 40, 'C');
        //$tablapdf->AddCollHead('fecha', 'FECHA', 30, 'J');
        /* $row \Cc\Mvc\DBRow */
        foreach ($res['inventario'] as $row)
        {
            $tablapdf->AddRowArray($row, 5, 'arial', 10);
        }
        $pdf->Table($tablapdf);
        $pdf->SetFont('arial', 'B', 18);
        $pdf->AddPage();
        $pdf->ln();
        $pdf->MultiCell(200, 5, "COMPRAS DE MEDICINAS", 0, 'C');
        $pdf->ln();
        $pdf->ln();
        $tablapdf2 = new TablePdfIterator(5, [90, 90, 90], 'arial', 12);
        $tablapdf2->AddCollHead('descripcion', 'DESCRIPCION', 70, 'J');
        $tablapdf2->AddCollHead('tipo', 'TIPO', 30, 'J');
        $tablapdf2->AddCollHead('costo', 'COSTO UNITARIO', 25, 'C');
        $tablapdf2->AddCollHead('cantidad', 'CANTIDAD', 22, 'C');
        $tablapdf2->AddCollHead('fecha', 'FECHA', 22, 'C');
        $tablapdf2->AddCollHead('costo_total', 'COSTO', 25, 'C');
        /* $row \Cc\Mvc\DBRow */
        foreach ($res['compras'] as $row)
        {
            $tablapdf2->AddRowArray($row->GetRow(), 6, 'arial', 10);
        }
        $pdf->Table($tablapdf2);
        $pdf->SetFont('arial', 'B', 18);
        $pdf->AddPage();
        $pdf->ln();
        $pdf->MultiCell(200, 5, "CONSUMOS DE MEDICINAS", 0, 'C');
        $pdf->ln();
        $pdf->ln();
        $tablapdf3 = new TablePdfIterator(5, [90, 90, 90], 'arial', 12);
        $tablapdf3->AddCollHead('descripcion', 'DESCRIPCION', 50, 'J');
        $tablapdf3->AddCollHead('detalles', 'DETALLES', 50, 'J');
        $tablapdf3->AddCollHead('tipo', 'TIPO', 30, 'J');
        $tablapdf3->AddCollHead('costo', 'COSTO', 18, 'C');
        $tablapdf3->AddCollHead('cantidad', 'CANTIDAD', 22, 'C');
        $tablapdf3->AddCollHead('fecha', 'FECHA', 22, 'C');
        /* $row \Cc\Mvc\DBRow */
        foreach ($res['consumos'] as $row)
        {
            $tablapdf3->AddRowArray($row->GetRow(), 6, 'arial', 10);
        }
        $pdf->Table($tablapdf3);
        $pdf->Output('Reporte de medicianas.pdf', 'I');
        //$res['pdf'] = "data:application/pdf;base64," . base64_encode($pdf->Output(NULL, 'S'));
    }

    public function editar(Json $res, DBtabla $medicinas, Request $req)
    {
        $form = new MedicinasForm();
        if ($form->IsSubmited())
        {
            if (!$form->IsValidid())
            {
                $res['errror'] = $form->ApiFormError();
                $res['insert'] = false;
                return;
            }
            if ($medicinas->Update($form, ['id_medicina' => $req->Post['id_medicina']]))
            {
                $res['insert'] = true;
            } else
            {
                $res['insert'] = false;
                $res['error'] = 'ha ocurrido un error al editar el medicamento';
            }
        } else
        {
            //$res['inserta'] = var_dump($form);
            $res['insert'] = false;
            $res['error'] = 'no hay datos';
        }
    }

    public function compra(Json $res, DBtabla $compra_medicinas, Request $req)
    {
        $form = new CompraMedicinasForm();
        if ($form->IsSubmited())
        {
            if (!$form->IsValid())
            {
                $res['insert'] = false;
                $res['error'] = $form->ApiFormError();
                return;
            }
            if ($compra_medicinas->Insert($form))
            {
                $res['insert'] = true;
            } else
            {
                $res['insert'] = false;
                $res['error'] = 'ha ocurrido un error al registrar la compra  del medicamento';
            }
        } else
        {
            //$res['inserta'] = var_dump($form);
            $res['insert'] = false;
            $res['error'] = 'no hay datos';
        }
    }

    public function consumo(Json $res, DBtabla $consumo_medicinas, Request $req, SelectorControllers $c)
    {
        $form = new ConsumoMedicinasForm();
        if ($form->IsSubmited())
        {
            if (!$form->IsValid())
            {
                $res['insert'] = false;
                $res['error'] = $form->ApiFormError();
                return;
            }
            $c->inventario();
            if ($res['inventario'][0]['stock'] < $form['cantidad'])
            {
                $res['insert'] = false;
                $res['error'] = 'La cantidad supera la existencia';
                return;
            }


            if ($consumo_medicinas->Insert($form))
            {
                //var_dump($form);
                //$res['a'] = $consumo_alimentos->sql;
                $res['insert'] = true;
            } else
            {
                $res['insert'] = false;
                $res['error'] = 'ha ocurrido un error al agregar el consumo';
            }
        } else
        {
            //$res['inserta'] = var_dump($form);
            $res['insert'] = false;
            $res['error'] = 'no hay datos';
        }
    }

    public function eliminar(Json $res, DBtabla $medicinas, $id_medicina)
    {
        if ($medicinas->Delete(['id_medicina' => $id_medicina]))
        {
            $res['eliminado'] = true;
        } else
        {
            $res['error'] = "Error al eliminar el medicamento ";
        }
    }

}
