<?php
namespace App\Controllers;
use App\Models\Usuarios_modelo;

class AjaxUsuarios extends BaseController{

    /*=========================================
    EDITAR USUARIO
    ==========================================*/

    public $idUsuario;

    public function ajaxEditarUsuario(){

        $item = "id";
        $valor = $this->idUsuario;
        if ($valor !== null) {
            $usuariosModelo = new Usuarios_modelo();
            $respuesta = $usuariosModelo->mdlMostrarUsuario($item, $valor);
            echo json_encode($respuesta);
        } 
    }

    /*=========================================
    ACTIVAR USUARIO
    ==========================================*/
    public $activarUsuario;
    public $activarId;

    public function ajaxActivarUsuario(){

        $item1 = "estado";
        $valor1 = $this->activarUsuario;

        $item2 = "id";
        $valor2 = $this->activarId;

        $usuariosModelo = new Usuarios_modelo();
        $respuesta = $usuariosModelo->mdlActualizarUsuario($item1, $valor1, $item2, $valor2);

    }

    /*=========================================
    VALIDAR NO REPETIR USUARIO
    ==========================================*/

    public $validarUsuario;
    public function ajaxValidarUsuario(){

        $item = "usuario";
        $valor = $this->validarUsuario;

        $usuariosCtlr = new Usuarios();
        $respuesta = $usuariosCtlr->ctrMostrarUsuarios($item, $valor);

        echo json_encode($respuesta);

    }

}

/*=========================================
EDITAR USUARIO
==========================================*/
$request = \Config\Services::request();
if (null !== $request->getPostGet('idUsuario')) {

    $editar = new AjaxUsuarios();
    $editar -> idUsuario = $request->getPostGet('idUsuario');
    $editar -> ajaxEditarUsuario();
} 

/*=========================================
ACTIVAR USUARIO
==========================================*/
if (null !== $request->getPost('activarUsuario')) { 

        $activarUsuario = new AjaxUsuarios();
        $activarUsuario -> activarUsuario = $request->getPost('activarUsuario');
        $activarUsuario -> activarId = $request->getPost('activarId');
        $activarUsuario -> ajaxActivarUsuario();
    
} 

/*=========================================
VALIDAR NO REPETIR USUARIO
==========================================*/

if (null !== $request->getPost('validarUsuario')) {

    $valUsuario = new AjaxUsuarios();
    $valUsuario -> validarUsuario = $request->getPost('validarUsuario');
    $valUsuario -> ajaxValidarUsuario();

} 