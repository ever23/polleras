<?php

/*
 * 
 */

namespace Cc\Mvc;

use Cc\Mvc;

/**
 * Description of Cuser
 *
 * @author usuario
 */
class Cuser extends Controllers implements AccessUserController
{

    /**
     * 
     * @return array
     */
    public static function AccessUser()
    {
        return [
            'NoAth' => ['index', 'islogin', 'logout'],
            'admin' => [
                'NoAccess' => [
                    'lista',
                    'sessiones',
                    'closesessione',
                    'editar',
                    'eliminar',
                    'registro'
                ]
            ]
        ];
    }

    /**
     * 
     * @param \Cc\Mvc\Html $h
     * @param \Cc\Mvc\AuthMinea $auth
     */
    public function index(Json $res, Auth $auth, DBtabla $sessiones, Request $e)
    {
        if ($auth->IsUser())
        {
            $res['login'] = true;
            $res['token'] = Mvc::App()->GetInternalSession()->GetId();
            $res['data'] = [
                'id_user' => $auth['id_usuarios'],
                'user' => $auth['user'],
                'nombres' => $auth['nombres'],
                'apellidos' => $auth['apellidos'],
                'permisos' => $auth['permisos'],
                'token' => Mvc::App()->GetInternalSession()->GetId(),
                //'id_granjas' => $auth['id_granjas'],
                'login' => true
            ];
            return;
        }
        $login = new LoginUserForm();

//$res['IsSubmited'] = $login->ButtonSubmit('', [], true);
        if ($login->IsSubmited())
        {
            if (!$login->IsValid())
            {
                $res['login'] = false;
                $res['error'] = $login->ApiFormError();
                return;
            }
            if ($auth->Login($login->user, $login->pass))
            {
                if (!$sessiones->Insert(Mvc::App()->GetInternalSession()->GetId(), (new \DateTime("now", new \DateTimeZone('America/Caracas')))->format('Y-m-d H:i:s'), $auth['id_usuarios']))
                {
                    $auth->CloseSessionUser(true);
                    Mvc::App()->GetInternalSession()->Destroy();
                    $res['login'] = false;
                    $res['error'] = "Sesion exixtente intente de nuevo";
                    return;
                }
                $res['login'] = true;

                $res['data'] = [
                    'id_user' => $auth['id_usuarios'],
                    'user' => $auth['user'],
                    'nombres' => $auth['nombres'],
                    'apellidos' => $auth['apellidos'],
                    'permisos' => $auth['permisos'],
                    'token' => Mvc::App()->GetInternalSession()->GetId(),
                    //'id_granjas' => $auth['id_granjas'],
                    'login' => true
                ];
// $res['data'] = [ 'token' => Mvc::App()->GetInternalSession()->GetId()];
            } else
            {
                $auth->CloseSessionUser(true);
                Mvc::App()->GetInternalSession()->Destroy();
                $res['login'] = false;
                $res['error'] = "La contraseña es invalida";
            }
        } else
        {
            $res['login'] = false;
            $res['error'] = "no hay datos";
        }
    }

    public function islogin(Json $res, Auth $auth)
    {
        if ($auth->IsUser())
        {
            $res['login'] = true;
            $res['data'] = [
                'id_user' => $auth['id_usuarios'],
                'user' => $auth['user'],
                'nombres' => $auth['nombres'],
                'apellidos' => $auth['apellidos'],
                'permisos' => $auth['permisos'],
                'token' => Mvc::App()->GetInternalSession()->GetId(),
                //'id_granjas' => $auth['id_granjas'],
                'login' => true
            ];
        } else
        {
            $res['login'] = false;
            $res['data'] = ['login' => false];
        }
    }

    public function user(Json $res, DBtabla $usuarios, $id_usuarios)
    {
        $usuarios->Select("id_usuarios='" . $id_usuarios . "'");
        $row = $usuarios->fetch();
        $res['user'] = [
            'id_user' => $row['id_usuarios'],
            'user' => $row['user'],
            'nombres' => $row['nombres'],
            'apellidos' => $row['apellidos'],
            'permisos' => $row['permisos'],
        ];
    }

    public function lista(Json $res, DBtabla $usuarios)
    {
        $usuarios->Select("permisos!='root'");
        $res['data'] = $usuarios->FetchAll();
    }

    public function sessiones(Json $res, Config $c, DBtabla $sessiones)
    {
        $sessiones->Select(['usuarios.*', 'sessiones.fecha', 'sessiones.id_session'], null, ['>usuarios' => 'id_usuarios'], null, null, "fecha DESC");
        $sess = [];
        $time = new \DateTime("now", new \DateTimeZone('America/Caracas'));
        foreach ($sessiones as $row)
        {
            $Stime = new \DateTime($row['fecha']);
            $Stime->modify($c->Autenticate['SessionCookie']['time']);
            if ($Stime > $time && file_exists(dirname(__FILE__) . '/../Cache/session/sess_' . $row['id_session']))
            {
                $status = 'activo';
            } else
            {
                $status = 'cerrado';
            }
            $sess[] = [
                'id_session' => $row['id_session'],
                'id_user' => $row['id_usuarios'],
                'user' => $row['user'],
                'nombres' => $row['nombres'],
                'apellidos' => $row['apellidos'],
                'permisos' => $row['permisos'],
                'status' => $status,
                'fecha' => $row['fecha']
            ];
        }
        $res['sessiones'] = $sess;
    }

    public function closesessione(Json $res, $id_session)
    {
        $filename = dirname(__FILE__) . '/../Cache/session/sess_' . $id_session;
        if (file_exists($filename))
        {
            if (unlink($filename))
            {
                $res['cerrado'] = true;
            } else
            {
                $res['cerrado'] = false;
                $res['error'] = "Error al cerrar sesion ";
            }
        } else
        {
            $res['cerrado'] = false;
            $res['error'] = "Error al cerrar sesion ";
        }
    }

    /**
     * 
     * @param \Cc\Mvc\AuthMinea $sess
     */
    public function registro(Json $res, Auth $sess)
    {
        $user = new UserForm();
        if ($user->IsSubmited())
        {
            if ($user->IsValid())
            {
                if ($sess->CreteUser($user->pass1, $user))
                {
                    $res['insert'] = true;
                } else
                {
                    $res['insert'] = false;
                    $res['error'] = "No se ha podido registrar";
                }
            } else
            {

                $res['insert'] = false;
                $res['error'] = $user->ApiFormError();
                return;
            }
        } else
        {
            $res['insert'] = false;
            $res['error'] = "No hay datos";
        }
    }

    public function editar(Json $res, DBtabla $usuarios, Auth $auth, $id_usuarios)
    {
        $form = new EditarUserForm();
        if ($form->IsSubmited())
        {
            if (!$form->IsValid())
            {
                $res['insert'] = false;
                $res['error'] = $form->ApiFormError();
                return;
            }
            if ($usuarios->Update($form, ['id_usuarios' => $id_usuarios]))
            {
                $res['editado'] = true;
                $res['data'] = [
                    'id_user' => $auth['id_usuarios'],
                    'user' => $form['user'],
                    'nombres' => $form['nombres'],
                    'apellidos' => $form['apellidos'],
                    'permisos' => $form['permisos'],
                    'token' => Mvc::App()->GetInternalSession()->GetId(),
                    //'id_granjas' => $auth['id_granjas'],
                    'login' => true
                ];
            } else
            {
                $res['editado'] = false;
                $res['error'] = "no se ha editado";
            }
        } else
        {
            $res['editado'] = false;
            $res['error'] = "no hay datos";
        }
    }

    public function eliminar(Json $res, DBtabla $usuarios, $id_usuarios)
    {
        if ($usuarios->Delete(['id_usuarios' => $id_usuarios]))
        {
            $res['eliminado'] = true;
        } else
        {
            $res['eiminado'] = false;
            $res['error'] = "No se ha podido eliminar";
        }
    }

    public function CambiarContrasena(Json $res, Auth $sess)
    {
        $form = new FormEditarContrasena();
        if ($form->IsSubmited())
        {
            if (!$form->IsValid())
            {
                $res['insert'] = false;
                $res['error'] = $form->ApiFormError();
                return;
            }
            if ($sess->UpdatePassword($form->pass, $form->pass1))
            {
                $res['editado'] = true;
            } else
            {
                $res['editado'] = false;
                $res['error'] = "contraseña incorrecta";
                //  $h->AddError("No se ha cambiado la contraseña ");
            }
        } else
        {
            $res['editado'] = false;
            $res['error'] = "no hay datos";
        }
    }

    /**
     * 
     * @param \Cc\Mvc\AuthMinea $auth
     */
    public function logout(Auth $auth, Json $res)
    {
        $auth->CloseSessionUser(true);
        Mvc::App()->GetInternalSession()->Destroy();
        $res['logout'] = true;
    }

}
