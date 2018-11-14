<?php

namespace Cc\Mvc;

use TablePdfIterator;

/**
 * controlador index sera ejecutado al llamar a / o a index/
 */
class Caves extends CostumController implements AccessUserController
{

    public static function AccessUser()
    {
        return [];
    }

    public function index(Json $res, DBtabla $compra_aves, DBtabla $venta_aves, DBtabla $mortalidad, DBtabla $galpones, SelectorControllers $c, $id_galpon = null)
    {
        $where = $id_galpon ? "id_galpon='" . $id_galpon . "'" : NULL;
        $compra_aves->Select(['sum(cantidad) as c'], $where);
        $compras = $compra_aves->num_rows == 1 ? $compra_aves->fetch()->c : 0;
        $venta_aves->Select(['sum(cantidad) as c'], $where);
        $ventas = $venta_aves->num_rows == 1 ? $venta_aves->fetch()->c : 0;
        $mortalidad->Select(['sum(cantidad) as c'], $where);
        $muertes = $mortalidad->num_rows == 1 ? $mortalidad->fetch()->c : 0;
        $res['aves'] = $compras - ($ventas + $muertes);
        $galpones->Select(['sum(capacidad) as capacidad'], $where);
        $res['capacidad'] = $galpones->fetch()->capacidad;
    }

    public function compras(Json $res, DBtabla $compra_aves, $consulta = null, $query = null, $id_galpon = null)
    {
        $where = $id_galpon ? "id_galpon='" . $id_galpon . "'" : NULL;
        ;
        if ($consulta)
        {
            $where = $where ? $where . ' and ' : '';
            $where.=$this->dateWhere($consulta, $query);
        }
        $compra_aves->Select(['compra_aves.*', 'galpones.nombre', 'compra_aves.costo * compra_aves.cantidad as costo_total'], $where, ['>galpones' => 'id_galpon']);
        $res['compras'] = $compra_aves->FetchAll();
    }

    public function ventas(Json $res, DBtabla $venta_aves, $consulta = null, $query = null, $id_galpon = null)
    {
        $where = $id_galpon ? "id_galpon='" . $id_galpon . "'" : NULL;
        if ($consulta)
        {
            $where = $where ? $where . ' and ' : '';
            $where.=$this->dateWhere($consulta, $query);
        }
        $venta_aves->Select(['venta_aves.*', 'galpones.nombre', 'venta_aves.costo * venta_aves.cantidad as costo_total'], $where, ['>galpones' => 'id_galpon']);
        $res['ventas'] = $venta_aves->FetchAll();
    }

    public function muertes(Json $res, DBtabla $mortalidad, $consulta = null, $query = null, $id_galpon = null)
    {
        $where = $id_galpon ? "id_galpon='" . $id_galpon . "'" : NULL;
        if ($consulta)
        {
            $where = $where ? $where . ' and ' : '';
            $where = $this->dateWhere($consulta, $query);
        }
        $mortalidad->Select(['mortalidad.*', 'galpones.nombre'], $where, ['>galpones' => 'id_galpon']);
        $res['muertes'] = $mortalidad->FetchAll();
    }

    public function resumen(Json $res, DBtabla $mortalidad, SelectorControllers $c, $id_galpon = null)
    {
        $c->ventas();
        $c->compras();
        $c->muertes();
        $time = new \DateTime();
        $where = $id_galpon ? "id_galpon='" . $id_galpon . "'" : NULL;
        $where = ($where ? $where . ' and ' : '') . "year(fecha)='" . $time->format('Y') . "' ";

        $mortalidad->Select(['sum(cantidad) as cantidad', 'fecha'], $where, null, 'month(fecha)');
        $sum = 0;
        $count = 0;
        foreach ($mortalidad as $row)
        {
            $sum+=$row['cantidad'];
            $count++;
        }
        $res['media_muertes'] = $count == 0 ? 0 : $sum / $count;

        $c->index();
        //$res['granja'] = ['aves' => $res['aves']];
    }

    public function reporte(Json $res, SelectorControllers $s)
    {
        $s->resumen();
        $pdf = new ReporteFPDF('P', 'mm', 'Letter');
        $pdf->SetFont('arial', 'B', 18);
        $pdf->AddPage();
        $pdf->ln();
        $pdf->MultiCell(200, 5, "COMPRAS DE AVES", 0, 'C');
        $pdf->ln();
        $pdf->ln();
        $tablapdf = new TablePdfIterator(5, [90, 90, 90], 'arial', 12);
        $tablapdf->AddCollHead('detalles', 'DETALLES', 75, 'J');
        $tablapdf->AddCollHead('nombre', 'GALPON', 30, 'j');
        $tablapdf->AddCollHead('costo', 'PRECIO POR UNIDAD', 25, 'C');
        $tablapdf->AddCollHead('cantidad', 'CANTIDAD', 23, 'C');
        $tablapdf->AddCollHead('fecha', 'FECHA', 22, 'C');
        $tablapdf->AddCollHead('costo_total', 'PRECIO', 23, 'C');

        //$tablapdf->AddCollHead('fecha', 'FECHA', 30, 'J');
        /* $row \Cc\Mvc\DBRow */
        foreach ($res['compras'] as $row)
        {
            $tablapdf->AddRowArray($row->GetRow(), 5, 'arial', 10);
        }
        $pdf->Table($tablapdf);
        $pdf->SetFont('arial', 'B', 18);
        $pdf->AddPage();
        $pdf->ln();
        $pdf->MultiCell(200, 5, "VENTAS DE AVES", 0, 'C');
        $pdf->ln();
        $pdf->ln();
        $tablapdf2 = new TablePdfIterator(5, [90, 90, 90], 'arial', 12);
        $tablapdf2->AddCollHead('detalles', 'DETALLES', 65, 'J');
        $tablapdf2->AddCollHead('nombre', 'GALPON', 30, 'j');
        $tablapdf2->AddCollHead('costo', 'PRECIO POR UNIDAD', 25, 'C');
        $tablapdf2->AddCollHead('cantidad', 'CANTIDAD', 23, 'C');
        $tablapdf2->AddCollHead('fecha', 'FECHA', 25, 'C');
        $tablapdf2->AddCollHead('costo_total', 'PRECIO', 30, 'C');
        /* $row \Cc\Mvc\DBRow */
        foreach ($res['ventas'] as $row)
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
        $tablapdf3->AddCollHead('rason', 'DETALLES', 80, 'J');
        $tablapdf3->AddCollHead('nombre', 'GALPON', 40, 'j');

        $tablapdf3->AddCollHead('cantidad', 'CANTIDAD', 32, 'C');
        $tablapdf3->AddCollHead('fecha', 'FECHA', 42, 'C');
        /* $row \Cc\Mvc\DBRow */
        foreach ($res['muertes'] as $row)
        {
            $tablapdf3->AddRowArray($row->GetRow(), 6, 'arial', 10);
        }
        $pdf->Table($tablapdf3);
        $pdf->Output('Reporte de Aves.pdf', 'I');

        // $res['pdf'] = "data:application/pdf;base64," . base64_encode($pdf->Output(NULL, 'S'));
    }

    public function insertar(Json $res, DBtabla $compra_aves, DBtabla $galpones, Request $req, SelectorControllers $c)
    {
        $form = new CompraAvesForm();
        if ($form->IsSubmited())
        {
            if (!$form->IsValid())
            {
                $res['insert'] = false;
                $res['error'] = $form->ApiFormError();
                return;
            }
            $c->index();
            $galpones->Select("id_galpon='" . $form->id_galpon . "'");
            if ($galpones->fetch()->capacidad < ($res['aves'] + $form->cantidad))
            {
                $res['insert'] = false;
                $res['error'] = ["cantidad" => "La cantidad supera la capacidad del galpon"];
                return;
            }

            if ($compra_aves->Insert($form))
            {
                $res['insert'] = true;
            } else
            {
                $res['insert'] = false;
                $res['error'] = 'ha ocurrido un error al agregar la compra';
            }
        } else
        {
            //$res['inserta'] = var_dump($form);
            $res['insert'] = false;
            $res['error'] = 'los datos no son validos';
        }
    }

    public function Mortalidad(Json $res, DBtabla $mortalidad, Request $req, SelectorControllers $c)
    {
        $form = new MortalidadAvesForm();
        if ($form->IsSubmited())
        {
            if (!$form->IsValid())
            {
                $res['insert'] = false;
                $res['error'] = $form->ApiFormError();
                return;
            }
            //$c->CreateController('granjas');
            $c->index();
            if ($res['aves'] < $form['cantidad'])
            {
                $res['insert'] = false;
                $res['error'] = ['cantidad' => 'la cantidad supera la existencia'];
                return;
            }
            if ($mortalidad->Insert($form))
            {
                //var_dump($form);
                //$res['a'] = $consumo_aves->sql;
                $res['insert'] = true;
            } else
            {
                $res['insert'] = false;
                $res['error'] = 'ha ocurrido un error al agregar la muerte de las aves';
            }
        } else
        {
            //$res['inserta'] = var_dump($form);
            $res['insert'] = false;
            $res['error'] = 'los datos no son validos';
        }
    }

    public function venta(Json $res, DBtabla $venta_aves, Request $req, SelectorControllers $c)
    {
        $form = new CompraAvesForm();
        if ($form->IsSubmited())
        {
            if (!$form->IsValid())
            {
                $res['insert'] = false;
                $res['error'] = $form->ApiFormError();
                return;
            }
            $c->index();
            if ($res['aves'] < $form['cantidad'])
            {
                $res['insert'] = false;
                $res['error'] = ['cantidad' => 'la cantidad supera la existencia'];
                return;
            }
            if ($venta_aves->Insert($form))
            {
                //var_dump($form);
                //$res['a'] = $consumo_aves->sql;
                $res['insert'] = true;
            } else
            {
                $res['insert'] = false;
                $res['error'] = 'ha ocurrido un error al agregar la venta';
            }
        } else
        {
            //$res['inserta'] = var_dump($form);
            $res['insert'] = false;
            $res['error'] = 'los datos no son validos';
        }
    }

}
