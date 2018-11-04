<?php

namespace Cc\Mvc;

use TablePdfIterator;

/**
 * controlador index sera ejecutado al llamar a / o a index/
 */
class Chuevos extends CostumController implements AccessUserController
{

    public static function AccessUser()
    {
        return [];
    }

    public function index(Json $res, DBtabla $produccion_huevos, DBtabla $venta_huevos)
    {
        //huevos grandes
        $produccion_huevos->Select(['sum(cantidad) as p'], "tipo='grande'");
        $produccion = $produccion_huevos->num_rows == 1 ? $produccion_huevos->fetch()->p : 0;
        $venta_huevos->Select(['sum(cantidad) as p'], "tipo='grande'");
        $ventas = $venta_huevos->num_rows == 1 ? $venta_huevos->fetch()->p : 0;
        $res['huevos_grandes'] = $produccion - $ventas;
        //huevos pequeños
        $produccion_huevos->Select(['sum(cantidad) as p'], "tipo='pequeño'");
        $produccion = $produccion_huevos->num_rows == 1 ? $produccion_huevos->fetch()->p : 0;
        $venta_huevos->Select(['sum(cantidad) as p'], "tipo='pequeño'");
        $ventas = $venta_huevos->num_rows == 1 ? $venta_huevos->fetch()->p : 0;
        $res['huevos_pequenos'] = $produccion - $ventas;
        $res['huevos'] = $res['huevos_grandes'] + $res['huevos_pequenos'];
    }

    public function resumen(Json $res, DBtabla $produccion_huevos, DBtabla $venta_huevos, SelectorControllers $c, $consulta = null, $query = null, $id_galpon = null)
    {
        $where1 = $id_galpon ? "id_galpon='" . $id_galpon . "'" : null;
        $where2 = null;
        $where_med = $id_galpon ? "id_galpon='" . $id_galpon . "'" : null;
        ;
        if ($consulta)
        {
            $where1 = $where1 ? $where1 . ' and ' : '';
            $where_med = $where_med ? $where_med . ' and ' : '';
            $where1.=$this->dateWhere($consulta, $query);
            $where2 = $this->dateWhere($consulta, $query);
            $where_med.= $this->dateWhere($consulta, $query);
        } else
        {
            $where_med = $where_med ? $where_med . ' and ' : '';
            $time = new \DateTime();
            $where_med .= " year(fecha)='" . $time->format('Y') . "' ";
        }
        $produccion_huevos->Select(['produccion_huevos.*', 'galpones.nombre'], $where1, ['>galpones' => 'id_galpon'], null, null, 'fecha DESC');
        $res['produccion'] = $produccion_huevos->FetchAll();
        // $res['p_sql'] = $produccion_huevos->sql;
        $venta_huevos->Select(['venta_huevos.*', 'venta_huevos.costo * venta_huevos.cantidad as costo_total'], $where2);
        $res['ventas'] = $venta_huevos->FetchAll();
        $c->index();

        //grande
        $produccion_huevos->Select(['sum(cantidad) as cantidad', 'fecha'], $where_med . " and tipo='pequeño'", null, 'fecha');
        $sum = 0;
        $count = 0;
        foreach ($produccion_huevos as $row)
        {
            $sum+=$row['cantidad'];
            $count++;
        }
        $res['media_produccion_pequeno'] = $count == 0 ? 0 : $sum / $count;
        //pequeño
        $produccion_huevos->FreeResult();
        $produccion_huevos->Select(['sum(cantidad) as cantidad', 'fecha'], $where_med . " and tipo='grande'", null, 'fecha');
        // $res['sql'] = $produccion_huevos->sql;
        $sum = 0;
        $count = 0;
        foreach ($produccion_huevos as $row)
        {
            $sum+=$row['cantidad'];
            $count++;
        }
        $res['media_produccion_grande'] = $count == 0 ? 0 : $sum / $count;
        //media total 
        $res['media_produccion'] = $res['media_produccion_pequeno'] + $res['media_produccion_grande'];
    }

    public function reporte(Json $res, SelectorControllers $s)
    {
        $s->resumen();
        $pdf = new ReporteFPDF('P', 'mm', 'Letter');
        $pdf->SetFont('arial', 'B', 18);
        $pdf->AddPage();
        $pdf->ln();
        $pdf->MultiCell(200, 5, "PRODUCCION DE HUEVOS", 0, 'C');
        $pdf->ln();
        $pdf->ln();
        $tablapdf = new TablePdfIterator(5, [90, 90, 90], 'arial', 12);

        $tablapdf->AddCollHead('detalles', 'DETALLES', 110, 'j');
        $tablapdf->AddCollHead('tipo', 'TIPO', 30, 'j');
        $tablapdf->AddCollHead('cantidad', 'CANTIDAD', 25, 'C');
        $tablapdf->AddCollHead('fecha', 'FECHA', 25, 'C');

        //$tablapdf->AddCollHead('fecha', 'FECHA', 30, 'J');
        /* $row \Cc\Mvc\DBRow */
        foreach ($res['produccion'] as $row)
        {
            $tablapdf->AddRowArray($row->GetRow(), 5, 'arial', 10);
        }
        $pdf->Table($tablapdf);
        $pdf->SetFont('arial', 'B', 18);
        $pdf->AddPage();
        $pdf->ln();
        $pdf->MultiCell(200, 5, "VENTAS DE HUEVOS", 0, 'C');
        $pdf->ln();
        $pdf->ln();
        $tablapdf2 = new TablePdfIterator(5, [90, 90, 90], 'arial', 12);
        $tablapdf2->AddCollHead('detalles', 'DETALLES', 75, 'J');
        $tablapdf2->AddCollHead('tipo', 'TIPO', 25, 'J');
        $tablapdf2->AddCollHead('costo', 'PRECIO UNITARIO', 25, 'C');
        $tablapdf2->AddCollHead('cantidad', 'CANTIDAD', 23, 'C');
        $tablapdf2->AddCollHead('fecha', 'FECHA', 25, 'C');
        $tablapdf2->AddCollHead('costo_total', 'PRECIO', 25, 'C');
        /* $row \Cc\Mvc\DBRow */
        foreach ($res['ventas'] as $row)
        {
            $tablapdf2->AddRowArray($row->GetRow(), 6, 'arial', 10);
        }
        $pdf->Table($tablapdf2);
        $pdf->Output('Reporte de huevos.pdf', 'I');

        //$res['pdf'] = "data:application/pdf;base64," . base64_encode($pdf->Output(NULL, 'S'));
    }

    public function insertar(Json $res, DBtabla $produccion_huevos, Request $req)
    {
        $form = new ProduccionHuevosForm();
        if ($form->IsSubmited())
        {
            if (!$form->IsValid())
            {
                $res['insert'] = false;
                $res['error'] = $form->ApiFormError();
                return;
            }
            if ($produccion_huevos->Insert($form))
            {
                $res['sql'] = $produccion_huevos->sql;
                $res['insert'] = true;
            } else
            {
                $res['insert'] = false;
                $res['error'] = 'ha ocurrido un error al agregar la produccion';
            }
        } else
        {
            //$res['inserta'] = var_dump($form);
            $res['insert'] = false;
            $res['error'] = 'los datos no son validos';
        }
    }

    public function venta(Json $res, DBtabla $venta_huevos, Request $req, SelectorControllers $c)
    {
        $form = new VentaHuevosForm();
        if ($form->IsSubmited())
        {
            if (!$form->IsValid())
            {
                $res['insert'] = false;
                $res['error'] = $form->ApiFormError();
                return;
            }
            $c->index();
            if (($form['tipo'] == 'grande' && $res['huevos_grandes'] < $form['cantidad']) || ($form['tipo'] == 'pequeño' && $res['huevos_pequenos'] < $form['cantidad']))
            {
                $res['insert'] = false;
                $res['error'] = 'la cantidad supera la existencia';
                return;
            }
            if ($venta_huevos->Insert($form))
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
