<?php

namespace Cc\Mvc;

use TablePdfIterator;

/**
 * controlador index sera ejecutado al llamar a / o a index/
 */
class Calimentos extends CostumController implements AccessUserController
{

    public static function AccessUser()
    {
        return [];
    }

    public function index(Json $res, DBtabla $compra_alimentos, DBtabla $consumo_alimentos)
    {
        $compra_alimentos->Select(['sum(cantidad) as p']);
        $compras = $compra_alimentos->num_rows == 1 ? $compra_alimentos->fetch()->p : 0;

        $consumo_alimentos->Select(['sum(cantidad) as p']);


        $ventas = $consumo_alimentos->num_rows == 1 ? $consumo_alimentos->fetch()->p : 0;
        $res['alimentos'] = $compras - $ventas;
    }

    public function insertar(Json $res, DBtabla $compra_alimentos, Request $req)
    {
        $form = new CompraAlimentosForm();
        if (!$form->IsSubmited())
        {
            $res['insert'] = false;
            $res['error'] = 'los datos no son validos';
            return;
        }
        if (!$form->IsValid())
        {
            $res['insert'] = false;
            $res['error'] = $form->ApiFormError();
            return;
        }
        if ($compra_alimentos->Insert($form))
        {
            $res['insert'] = true;
        } else
        {
            $res['insert'] = false;
            $res['error'] = 'ha ocurrido un error al agregar la compra';
        }
    }

    public function consumo(Json $res, DBtabla $consumo_alimentos, Request $req, SelectorControllers $c)
    {
        $form = new ConsumoAlimentosForm();
        if ($form->IsSubmited())
        {
            if (!$form->IsValid())
            {
                $res['insert'] = false;
                $res['error'] = $form->ApiFormError();
                return;
            }

            $c->index();
            var_dump((float) $res['alimentos'], (float) $form['cantidad']);
            if ((float) $res['alimentos'] < (float) $form['cantidad'])
            {
                $res['insert'] = false;
                $res['error'] = 'la cantidad supera la existencia';
                return;
            }

            if ($consumo_alimentos->Insert($form))
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
            $res['error'] = 'los datos no son validos';
        }
    }

    public function consumos(Json $res, DBtabla $consumo_alimentos, $consulta = null, $query = null, $id_galpon = null)
    {
        $where = NULL;
        if ($id_galpon)
        {
            $where = "id_galpon='" . $id_galpon . "'";
        }

        if ($consulta)
        {
            $where = $where ? $where . ' and ' : '';
            $where.=$this->dateWhere($consulta, $query);
        }
        $consumo_alimentos->Select(['consumo_alimentos.*', 'galpones.nombre'], $where, ['>galpones' => 'id_galpon']);
        //$res['sql'] = $consumo_alimentos->sql;
        $res['consumos'] = $consumo_alimentos->FetchAll();
    }

    public function compras(Json $res, DBtabla $compra_alimentos, $consulta = null, $query = null)
    {
        $where = null;
        if ($consulta)
        {

            $where = $this->dateWhere($consulta, $query);
        }
        $compra_alimentos->Select(['compra_alimentos.*', 'compra_alimentos.costo * compra_alimentos.cantidad as costo_total'], $where);
        $res['sql'] = $compra_alimentos->sql;
        $res['compras'] = $compra_alimentos->FetchAll();
    }

    public function resumen(Json $res, SelectorControllers $s, DBtabla $consumo_alimentos, $id_galpon = null)
    {
        $s->index();
        $s->consumos();
        $s->compras();
        $time = new \DateTime();
        $where = NULL;
        if ($id_galpon)
        {
            $where = "id_galpon='" . $id_galpon . "'";
        }
        $where = $where ? $where . ' and ' : '';
        $where.="year(fecha)='" . $time->format('Y') . "' and MONTH(fecha)<'" . $time->format('m') . "' ";

        $consumo_alimentos->Select(['sum(cantidad) as cantidad', 'fecha'], $where, null, 'month(fecha)');
        $sum = 0;
        $count = 0;
        foreach ($consumo_alimentos as $row)
        {
            $sum+=$row['cantidad'];
            $count++;
        }
        $res['media_consumo'] = $count == 0 ? 0 : $sum / $count;
    }

    public function reporte(Json $res, SelectorControllers $s)
    {
        $s->resumen();
        $pdf = new ReporteFPDF('P', 'mm', 'Letter');
        $pdf->SetFont('arial', 'B', 18);
        $pdf->AddPage();
        $pdf->ln();
        $pdf->MultiCell(200, 5, "Compras de alimentos", 0, 'C');
        $pdf->ln();
        $pdf->ln();
        $tablapdf = new TablePdfIterator(5, [90, 90, 90], 'arial', 12);
        $tablapdf->AddCollHead('detalles', 'DETALLES', 85, 'C');
        $tablapdf->AddCollHead('cantidad', 'CANTIDAD', 25, 'J');
        $tablapdf->AddCollHead('costo', 'COSTO UNITARIO', 40, 'J');
        $tablapdf->AddCollHead('fecha', 'FECHA', 25, 'J');
        $tablapdf->AddCollHead('costo_total', 'COSTO', 20, 'J');
        /* $row \Cc\Mvc\DBRow */
        foreach ($res['compras'] as $row)
        {
            $tablapdf->AddRowArray($row->GetRow(), 5, 'arial', 10);
        }
        $pdf->Table($tablapdf);
        $pdf->SetFont('arial', 'B', 18);
        $pdf->AddPage();
        $pdf->ln();
        $pdf->MultiCell(200, 5, "Consumos de alimentos", 0, 'C');
        $pdf->ln();
        $pdf->ln();
        $tablapdf2 = new TablePdfIterator(5, [90, 90, 90], 'arial', 12);
        $tablapdf2->AddCollHead('nombre', 'GALPON', 110, 'C');
        $tablapdf2->AddCollHead('cantidad', 'CANTIDAD', 40, 'J');
        //$tablapdf2->AddCollHead('costo', 'COSTO', 30, 'J');
        $tablapdf2->AddCollHead('fecha', 'FECHA', 40, 'J');
        /* $row \Cc\Mvc\DBRow */
        foreach ($res['consumos'] as $row)
        {
            $tablapdf2->AddRowArray($row->GetRow(), 5, 'arial', 10);
        }
        $pdf->Table($tablapdf2);
        $pdf->Output('Reporte de alimentos.pdf', 'I');
        //  $res['pdf'] = "data:application/pdf;base64," . base64_encode($pdf->Output(NULL, 'S'));
    }

}
