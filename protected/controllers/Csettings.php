<?php

namespace Cc\Mvc;

use Cc\Mvc;

/**
 * controlador index sera ejecutado al llamar a / o a index/
 */
class Csettings extends Controllers implements AccessUserController
{

    public static function AccessUser()
    {
        return [
            'NoAth' => ['settings', 'imagen'],
            'admin' => [
                'NoAccess' => ['index']
            ]
        ];
    }

    public function index(Json $res)
    {
        $file = dirname(__FILE__) . '/../settings.json';
        $Settings = new Json();
        $Settings->SetJsonFile($file);
        $form = new SettingsForm();
        if (!$form->IsSubmited())
        {
            $res['settings'] = $Settings;
            return;
        }
        if (!$form->IsValid())
        {
            // var_dump($form);
            $res['error'] = 'Datos no validos';
            return;
        }
        //$f = new PostFiles($v);
        foreach ($form as $i => $row)
        {
            if ($i != 'imagen')
                $Settings[$i] = $row->__toString();
        }
        // $Settings->CreateJson($form->jsonSerialize(), true);
        if (is_object($form['imagen']) && $form['imagen']->is_Uploaded())
        {

            // var_dump($form['imagen']);
            $Settings['imagen'] = ['type' => $form['imagen']['type'], 'ext' => $form['imagen']->getExtension(), 'ModifiTime' => (new \DateTime())->format('Y-m-d H:i:s')];
            $form['imagen']->Copy(dirname(__FILE__) . '/../icono');
        }
        $Settings['nombre'] = $form['nombre'];
        $Settings->SaveToFile($file);
        $res['editado'] = true;
        $res['Settings'] = $Settings;
    }

    public function settings(Json $res)
    {
        $file = dirname(__FILE__) . '/../settings.json';
        $Settings = new Json();
        $Settings->SetJsonFile($file);
        $res['settings'] = $Settings;
    }

    public function imagen()
    {
        $file = dirname(__FILE__) . '/../settings.json';
        $Settings = new Json();
        $Settings->SetJsonFile($file);
        $this->ChangeContenType($Settings['imagen']['type']);
        Mvc::App()->Response->ActiveCache(false, 0, (new \DateTime($Settings['imagen']['ModifiTime']))->getTimestamp());
        echo file_get_contents(dirname(__FILE__) . '/../icono');
    }

}
