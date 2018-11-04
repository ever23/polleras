<?php

namespace Cc\Mvc;

use TablePdfIterator;

/**
 * 
 * controlador index sera ejecutado al llamar a / o a index/
 */
class Cempleados extends CostumController implements AccessUserController
{

    public static function AccessUser()
    {
        return [
            'NoAth' => [],
            'admin' => [
                'NoAccess' => ['editar', 'eliminar']
            ]
        ];
    }

    public function index(Json $res, DBtabla $empleados, $ci_empleado = null)
    {
        $dia = (new \DateTime('now'))->format('Y') . '-' . ((int) (new \DateTime("now", new \DateTimeZone('America/Caracas')))->format('m')) . '-' . ((int) (new \DateTime("now", new \DateTimeZone('America/Caracas')))->format('d'));
        $campos = [
            'empleados.*',
            "if(concat(year(max(asistencia.fecha)),'-',month(max(asistencia.fecha)),'-',day(max(asistencia.fecha)))='" . $dia . "','si','no') as asistencia",
            "concat(hour(max(asistencia.fecha)),':',minute(max(asistencia.fecha)),':',second(max(asistencia.fecha))) as hora"
        ];
        $where = null;
        if ($ci_empleado)
        {
            $where = "ci_empleado='" . $ci_empleado . "'";
        }
        $empleados->Select($campos, $where, ['>asistencia' => 'ci_empleado'], 'ci_empleado');
        $res['empleados'] = $empleados->FetchAll();
        //$res['sql'] = $dia;
    }

    public function nomina(Json $res, DBtabla $empleados, $ci_empleado = null)
    {
        $mes = (new \DateTime('now'))->format('Y') . '-' . ((int) (new \DateTime("now", new \DateTimeZone('America/Caracas')))->format('m'));
        $campos = [
            'empleados.*',
            "if(concat(year(max(pago_nomina.fecha)),'-',month(max(pago_nomina.fecha)))='" . $mes . "','si','no') as pago",
        ];
        $where = null;
        if ($ci_empleado)
        {
            $where = "ci_empleado='" . $ci_empleado . "'";
        }
        $empleados->Select($campos, $where, ['>pago_nomina' => 'ci_empleado'], 'ci_empleado');
        $res['empleados'] = $empleados->FetchAll();
    }

    public function insertar(Json $res, DBtabla $empleados)
    {
        $form = new EmpleadosForm();
        if ($form->IsSubmited())
        {
            if (!$form->IsValid())
            {
                $res['insert'] = false;
                $res['error'] = $form->ApiFormError();
                return;
            }
            if ($empleados->Insert($form))
            {
                $res['insert'] = true;
            } else
            {
                $res['insert'] = false;
                $res['error'] = 'ha ocurrido un error al agregar el empleado';
            }
        } else
        {
            //$res['inserta'] = var_dump($form);
            $res['insert'] = false;
            $res['error'] = 'los datos no son validos';
        }
    }

    public function editar(Json $res, DBtabla $empleados)
    {
        $form = new EmpleadosForm();
        if ($form->IsSubmited())
        {
            if (!$form->IsValid())
            {
                $res['insert'] = false;
                $res['error'] = $form->ApiFormError();
                return;
            }
            if ($empleados->Update($form, ['ci_empleado' => $form['ci_empleado']]))
            {
                $res['insert'] = true;
            } else
            {
                $res['insert'] = false;
                $res['error'] = 'ha ocurrido un error al editar el empleado';
            }
        } else
        {
            //$res['inserta'] = var_dump($form);
            $res['insert'] = false;
            $res['error'] = 'los datos no son validos';
        }
    }

    public function eliminar(Json $res, DBtabla $empleados, $ci_empleado)
    {
        if ($empleados->Delete(['ci_empleado' => $ci_empleado]))
        {
            $res['eliminado'] = true;
        } else
        {
            $res['eliminado'] = false;
            $res['error'] = "Ocurrio un error al eliminar el empleado";
        }
    }

    public function pago_nomina(Json $res, Request $req, DBtabla $pago_nomina, SelectorControllers $c)
    {
        //var_dump($req->Post['empleados']);
        if (isset($req['empleados']) && is_array($req['empleados']))
        {
            $fecha = (new \DateTime("now", new \DateTimeZone('America/Caracas')))->format("Y-m-d H-m-s");
            foreach ($req['empleados'] as $ci_empleado => $pago)
            {
                $pago_nomina->Insert(null, $pago, $ci_empleado, $fecha);
            }
            $res['insert'] = true;
        } else
        {
            $res['insert'] = false;
            $res['error'] = 'los datos no son validos';
        }
    }

    public function asistencia(Json $res, DBtabla $asistencia, $consulta = null, $query = null)
    {
        $where = NULL;
        if ($consulta)
        {
            $where = $where ? $where . ' and ' : '';
            $where.=$this->dateWhere($consulta, $query);
        }
        $asistencia->Select(['empleados.*', 'asistencia.fecha', 'asistencia.id_asistencia'], $where, ['>empleados' => 'ci_empleado'], null, null, 'fecha DESC');
        $res['asistencia'] = $asistencia->FetchAll();
        // $res['sql'] = $asistencia->sql;
    }

    public function PagosNomina(Json $res, DBtabla $pago_nomina, $consulta = null, $query = null)
    {
        $where = NULL;
        if ($consulta)
        {
            $where = $where ? $where . ' and ' : '';
            $where.=$this->dateWhere($consulta, $query);
        }
        $pago_nomina->Select(['empleados.*', 'pago_nomina.fecha', 'pago_nomina.pago', 'pago_nomina.id_nomina'], $where, ['>empleados' => 'ci_empleado'], null, null, 'fecha DESC');
        $res['pagos'] = $pago_nomina->FetchAll();
    }

    public function resumen(Json $res, SelectorControllers $s, DBtabla $empleados)
    {
        $s->index();
        $s->asistencia();
        $s->PagosNomina();
        $empleados->Select(['count(cargo) as cantidad', 'cargo'], null, null, 'cargo');
        foreach ($empleados as $row)
        {
            $res[$row['cargo']] = $row['cantidad'];
        }
    }

    public function reporte(Json $res, SelectorControllers $s, DBtabla $empleados)
    {
        $s->nominapdf();
        $s->asistenciapdf();
        $s->pagospdf();
        $empleados->Select(['count(cargo) as cantidad', 'cargo'], null, null, 'cargo');
        foreach ($empleados as $row)
        {
            $res[$row['cargo']] = $row['cantidad'];
        }
    }

    public function pagospdf(Json $res, SelectorControllers $s)
    {
        $s->PagosNomina();
        $pdf = new ReporteFPDF('P', 'mm', 'Letter');
        $pdf->SetFont('arial', 'B', 18);
        $pdf->AddPage();
        $pdf->ln();
        $pdf->MultiCell(200, 5, "PAGOS DE NOMINA", 0, 'C');
        $pdf->ln();
        $pdf->ln();
        $tablapdf = new TablePdfIterator(5, [90, 90, 90], 'arial', 12);
        $tablapdf->AddCollHead('ci_empleado', 'C.I', 30, 'J');
        $tablapdf->AddCollHead('nombres', 'NOMBRES', 45, 'J');
        $tablapdf->AddCollHead('apellidos', 'APELLIDOS', 45, 'J');
        $tablapdf->AddCollHead('cargo', 'CARGO', 25, 'C');
        $tablapdf->AddCollHead('fecha', 'FECHA', 25, 'C');
        $tablapdf->AddCollHead('pago', 'PAGO', 25, 'C');

        //$tablapdf->AddCollHead('fecha', 'FECHA', 30, 'J');
        /* $row \Cc\Mvc\DBRow */
        foreach ($res['pagos'] as $row)
        {
            $tablapdf->AddRowArray($row->GetRow(), 5, 'arial', 10);
        }
        $pdf->Table($tablapdf);
        $pdf->Output('Reporte de pagos.pdf', 'I');
        //$res['pagospdf'] = "data:application/pdf;base64," . base64_encode($pdf->Output(NULL, 'S'));
    }

    public function asistenciapdf(Json $res, SelectorControllers $s)
    {
        $s->asistencia();
        $pdf = new ReporteFPDF('P', 'mm', 'Letter');
        $pdf->SetFont('arial', 'B', 18);
        $pdf->AddPage();
        $pdf->ln();
        $pdf->MultiCell(200, 5, "ASISTENCIA", 0, 'C');
        $pdf->ln();
        $pdf->ln();
        $tablapdf = new TablePdfIterator(5, [90, 90, 90], 'arial', 12);
        $tablapdf->AddCollHead('ci_empleado', 'C.I', 30, 'J');
        $tablapdf->AddCollHead('nombres', 'NOMBRES', 50, 'J');
        $tablapdf->AddCollHead('apellidos', 'APELLIDOS', 50, 'J');
        $tablapdf->AddCollHead('cargo', 'CARGO', 40, 'C');
        $tablapdf->AddCollHead('fecha', 'FECHA', 30, 'C');

        //$tablapdf->AddCollHead('fecha', 'FECHA', 30, 'J');
        /* $row \Cc\Mvc\DBRow */
        foreach ($res['asistencia'] as $row)
        {
            $tablapdf->AddRowArray($row->GetRow(), 5, 'arial', 10);
        }
        $pdf->Table($tablapdf);
        $pdf->Output('Reporte de asistencia.pdf', 'I');
        //$res['asistenciapdf'] = "data:application/pdf;base64," . base64_encode($pdf->Output(NULL, 'S'));
    }

    public function nominapdf(Json $res, SelectorControllers $s)
    {
        $s->index();
        $pdf = new ReporteFPDF('P', 'mm', 'Letter');
        $pdf->SetFont('arial', 'B', 18);
        $pdf->AddPage();
        $pdf->ln();
        $pdf->MultiCell(200, 5, "NOMINA", 0, 'C');
        $pdf->ln();
        $pdf->ln();
        $tablapdf = new TablePdfIterator(5, [90, 90, 90], 'arial', 12);
        $tablapdf->AddCollHead('ci_empleado', 'C.I', 30, 'J');
        $tablapdf->AddCollHead('nombres', 'NOMBRES', 50, 'J');
        $tablapdf->AddCollHead('apellidos', 'APELLIDOS', 50, 'J');
        $tablapdf->AddCollHead('cargo', 'CARGO', 40, 'C');
        $tablapdf->AddCollHead('sueldo', 'SUELDO', 30, 'C');

        //$tablapdf->AddCollHead('fecha', 'FECHA', 30, 'J');
        /* $row \Cc\Mvc\DBRow */
        foreach ($res['empleados'] as $row)
        {
            $tablapdf->AddRowArray($row->GetRow(), 5, 'arial', 10);
        }
        $pdf->Table($tablapdf);
        $pdf->Output('Reporte de nomina.pdf', 'I');
        // $res['nominapdf'] = "data:application/pdf;base64," . base64_encode($pdf->Output(NULL, 'S'));
    }

    public function asistir(Json $res, DBtabla $asistencia, $ci_empleado)
    {
        $time = new \DateTime("now", new \DateTimeZone('America/Caracas'));
        if ($asistencia->Insert(null, $time->format('Y-m-d H:i:s'), $ci_empleado))
        {
            $res['asistio'] = true;
            // $res['sql']=$asistencia->sql;
        } else
        {
            $res['asistio'] = false;
            $res['error'] = "no se ha registrado la asistencia";
        }
    }

}
