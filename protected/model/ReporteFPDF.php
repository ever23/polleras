<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cc\Mvc;

use ExtendsFpdf;
use TablePdfIterator;

/**
 * Description of ReportePDF
 *
 * @author enyerber
 */
class ReporteFPDF extends ExtendsFpdf
{

    /**
     * REDEFINIDO PARA AGREGAR EL LOGO DE LA UNIVERSIDAD EN LA CABECERA DE CADA PAGINA PDF
     */
    public function Header()
    {

        //   parent::Header();
        $file = dirname(__FILE__) . '/../settings.json';
        $Settings = new Json();
        $Settings->SetJsonFile($file);
        $this->Image(dirname(__FILE__) . '/../icono', 15, 10, 20, null, $Settings['imagen']['ext']);
        // $this->Image($file, $x, $y, $w, $h, $type, $link)
        $this->SetFont('arial', 'B', 18);
        $this->SetXY(40, 10);
        $this->MultiCell(140, 10, strtoupper($Settings['nombre']), 0, 'C');
        $this->Ln();
        $this->SetY(25);
        parent::TableFpdfHeader();
        $this->CallFnHeader();
        //  $this->Image(dirname(__FILE__) . "/../../img/logo_unefa.png", 180, 10, 17, 20);
        $this->SetCompression(true);
    }

}
