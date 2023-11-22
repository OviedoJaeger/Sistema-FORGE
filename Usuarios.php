<?php
namespace App\Controllers;
use App\Models\Usuarios_modelo;

class Usuarios extends BaseController
{

    /* Sustituciones
    $request->getPost('');
    $session->get('');
    $request->getPostGet('');

    */
    /*=========================================
    INGRESO DE USUARIO
    ==========================================*/
    static public function ctrIngresoUsuario()
    {

        $request = \Config\Services::request();

        if ($request->getPost('ingUsuario') !== null) {

            if (
                preg_match('/^[a-zA-Z0-9]+$/', $request->getPost('ingUsuario')) &&
                preg_match('/^[a-zA-Z0-9]+$/', $request->getPost('ingPassword'))
            ) {
                $encriptar = crypt($request->getPost('ingPassword'), '$2a$07$KH43his98Y.4xC.5HZoO5.Q6ksrG8lfxeeUe8ONTtUS4S/NVi7zMC$');

                $item = "usuario";
                $valor = $request->getPost('ingUsuario');

                $usuariosModel = new Usuarios_modelo();
                $respuesta = $usuariosModel->MdlMostrarUsuarios($item, $valor);

                if ($respuesta === false || empty($respuesta)) {
                    $data['error'] = 'Error al ingresar, vuelve a intentarlo';
                    return view('plantilla',$data);

                } else {
                    $respuesta = [
                        'usuario' => $respuesta[0]['usuario'],
                        'password' => $respuesta[0]['password'],
                        'estado' => $respuesta[0]['estado'],
                        'id' => $respuesta[0]['id'],
                        'nombre' => $respuesta[0]['nombre'],
                        'foto' => $respuesta[0]['foto'],
                        'perfil' => $respuesta[0]['perfil']
                    ];
                    
                    if (
                        is_array($respuesta) && $respuesta['usuario'] == $valor &&
                        $respuesta['password'] == $encriptar) 
                    {
                        
                        $session = session();

                        if ($respuesta["estado"] == 1) {


                                $session->set('iniciarSesion', 'ok');
                                $session->set('id', $respuesta["id"]);
                                $session->set('nombre', $respuesta["nombre"]);
                                $session->set('usuario', $respuesta["usuario"]);
                                $session->set('foto', $respuesta["foto"]);
                                $session->set('perfil', $respuesta["perfil"]);

                                /*=========================================
                                REGISTRAR FECHA PARA SABER EL ÚLTIMO LOGIN
                                ==========================================*/

                                date_default_timezone_set('America/Mexico_City');

                                $fecha = date('Y-m-d');
                                $hora = date('H:i:s');

                                $fechaActual = $fecha.' '.$hora;

                                $item1 = "ultimo_login";
                                $valor1 = $fechaActual;

                                $item2 = "id";
                                $valor2 = $respuesta["id"];

                                $usuariosModel = new Usuarios_modelo();
                                $ultimoLogin = $usuariosModel->mdlActualizarUsuario($item1, $valor1, $item2, $valor2);

                                if ($ultimoLogin == "ok") {
                                
                                    //REVISAR ESTO, POSIBLEMENTE SU CAMBIO SEA UN RETURN VIEW EN VEZ DE UN ECHO
                                    return view('plantilla');

                                }

                        } else {
                            
                        $data['error'] = 'El usuario no está activado';
                        return view('plantilla',$data);

                        }

                    } else {

                        $data['error'] = 'Error al ingresar, vuelve a intentarlo';
                        return view('plantilla',$data);

                    }

                }
            }
        }
    }

    /*=========================================
    REGISTRO USUARIO
    ==========================================*/
    static public function ctrCrearUsuario()
    {
        $response = \Config\Services::response();
        $request = \Config\Services::request();

        if (null !== $request->getPost('nuevoUsuario')) {

            if (
                preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $request->getPost('nuevoNombre')) &&
                preg_match('/^[a-zA-Z0-9]+$/', $request->getPost('nuevoUsuario')) &&
                preg_match('/^[a-zA-Z0-9]+$/', $request->getPost('nuevoPassword'))
            ) {

                /*=========================================
                VALIDAR IMAGEN
                ==========================================*/

                $ruta = "";
                $file = $request->getFile('nuevaFoto');

                if ($file->isValid() && !$file->hasMoved()) {

                    list($ancho, $alto) = getimagesize($file->getTempName());

                    $nuevoAncho = 500;
                    $nuevoAlto = 500;

                    /*=========================================
                    CREAMOS EL DIRECTORIO DONDE VAMOS A GUARDAR LA FOTO DE USUARIO
                    ==========================================*/

                    $directorio = base_url()."img/usuarios/" . $request->getPost('nuevoUsuario');
                    
                    mkdir($directorio, 0755);

                    /*=========================================
                    DE ACUERDO AL TIPO DE IMAGEN APLICAMOS LAS FUNCIONES POR DEFECTO DE PHP
                    ==========================================*/

                    if ($file->getMimeType() == "image/jpeg") {

                    /*=========================================
                    GUARDAMOS LA IMAGEN EN EL DIRECTORIO
                    ==========================================*/

                    $aleatorio = mt_rand(100,999);

                    $ruta = base_url()."img/usuarios/" . $request->getPost('nuevoUsuario') . "/" . $aleatorio . ".jpg";

                    $origen = imagecreatefromjpeg($file->getTempName());

                    $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

                    imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, 
                    $ancho, $alto);

                    imagejpeg($destino, $ruta);

                    }

                    if ($file->getMimeType() == "image/png") {

                        /*=========================================
                        GUARDAMOS LA IMAGEN EN EL DIRECTORIO
                        ==========================================*/
    
                        $aleatorio = mt_rand(100,999);
                        $ruta = base_url()."img/usuarios/" . $request->getPost('nuevoUsuario') . "/" . $aleatorio . ".png";
    
                        $origen = imagecreatefrompng($file->getTempName());
    
                        $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);
    
                        imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, 
                        $ancho, $alto);
    
                        imagepng($destino, $ruta);
    
                        }

                }

                $encriptar = crypt($_POST["nuevoPassword"], '$2a$07$KH43his98Y.4xC.5HZoO5.Q6ksrG8lfxeeUe8ONTtUS4S/NVi7zMC$');

                $datos = array("nombre" => $request->getPost('nuevoNombre'),
                        "usuario" => $request->getPost('nuevoUsuario'),
                        "password" => $encriptar,
                        "perfil" => $request->getPost('nuevoPerfil'),
                        "foto"=>$ruta);

                $usuariosModel = new Usuarios_modelo();
                $respuesta = $usuariosModel->mdlIngresarUsuario($datos);

                if($respuesta == "ok"){

                    return $response->setJSON(['status' => 'success']);

                } 
            } else {

                    return $response->setJSON(['status' => 'errorU']);
                
            }
        }
    }

    /*=========================================
    MOSTRAR USUARIO
    ==========================================*/

    static public function ctrMostrarUsuarios($item, $valor){

        $modeloUsuarios = new Usuarios_modelo();
        $respuesta = $modeloUsuarios->MdlMostrarUsuarios($item, $valor);

        return $respuesta;

    }

    /*=========================================
    EDITAR USUARIO
    ==========================================*/

    static public function ctrEditarUsuario(){

        $request = \Config\Services::request();
        $response = \Config\Services::response();

        if($request->getPost('editarUsuario') !== null){

            if (preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $request->getPost('editarNombre'))) {


                /*=========================================
                VALIDAR IMAGEN
                ==========================================*/

                $ruta = $request->getPost('fotoActual');

                $file = $request->getFile('editarFoto');

                if ($file->isValid() && !$file->hasMoved()) {
                    
                    list($ancho, $alto) = getimagesize($file->getTempName());

                    $nuevoAncho = 500;
                    $nuevoAlto = 500;

                    /*=========================================
                    CREAMOS EL DIRECTORIO DONDE VAMOS A GUARDAR LA FOTO DE USUARIO
                    ==========================================*/

                    $directorio = base_url()."img/usuarios/" . $request->getPost('editarUsuario');

                    /*=========================================
                    PRIMERO PREGUNTAMOS SI EXISTE OTRA IMAGEN EN LA BD
                    ==========================================*/

                    if (!empty($request->getPost('fotoActual'))) {
                        
                        unlink($request->getPost('fotoActual'));

                    }else {
                        
                        mkdir($directorio, 0755);

                    }
                    

                    /*=========================================
                    DE ACUERDO AL TIPO DE IMAGEN APLICAMOS LAS FUNCIONES POR DEFECTO DE PHP
                    ==========================================*/

                    if ($file->getMimeType() == "image/jpeg") {

                    /*=========================================
                    GUARDAMOS LA IMAGEN EN EL DIRECTORIO
                    ==========================================*/

                    $aleatorio = mt_rand(100,999);

                    $ruta = base_url()."img/usuarios/" . $request->getPost('editarUsuario') . "/" . $aleatorio . ".jpg";

                    $origen = imagecreatefromjpeg($file->getTempName());

                    $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

                    imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, 
                    $ancho, $alto);

                    imagejpeg($destino, $ruta);

                    }

                    if ($file->getMimeType() == "image/png") {

                        /*=========================================
                        GUARDAMOS LA IMAGEN EN EL DIRECTORIO
                        ==========================================*/
    
                        $aleatorio = mt_rand(100,999);
    
                        $ruta = base_url()."img/usuarios/" . $request->getPost('editarUsuario') . "/" . $aleatorio . ".png";
    
                        $origen = imagecreatefrompng($file->getTempName());
    
                        $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);
    
                        imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, 
                        $ancho, $alto);
    
                        imagepng($destino, $ruta);
    
                    }

                }

                if ($request->getPost('editarPassword') != "") {

                    if (preg_match('/^[a-zA-Z0-9]+$/', $request->getPost('editarPassword'))) {
                        
                        $encriptar = crypt($request->getPost('editarPassword'), '$2a$07$KH43his98Y.4xC.5HZoO5.Q6ksrG8lfxeeUe8ONTtUS4S/NVi7zMC$');

                    } else {
                        
                            return $response->setJSON(['status' => 'errorC']);

                    } 

                } else {

                    $encriptar = $request->getPost('passwordActual');

                }

                $datos = array("nombre" => $request->getPost('editarNombre'),
                        "usuario" => $request->getPost('editarUsuario'),
                        "password" => $encriptar,
                        "perfil" => $request->getPost('editarPerfil'),
                        "foto"=>$ruta);

                    $modeloUsuarios = new Usuarios_modelo();
                    $respuesta = $modeloUsuarios->mdlEditarUsuario($datos);
                    
                    if($respuesta == "ok"){
                        
                        return $response->setJSON(['status' => 'success']);
    
                    } 
            
            } else {

                    return $response->setJSON(['status' => 'errorN']);

                }

        }


    }

    /*=========================================
    BORRAR USUARIO
    ==========================================*/

    static function ctrBorrarUsuario(){

        $response = \Config\Services::response();

        $request = \Config\Services::request();

        if (null !== $request->getPostGet('idUsuario')) {

            $datos = $request->getPostGet('idUsuario');

            if ($request->getPostGet('fotoUsuario') != "") {
                
                unlink($request->getPostGet('fotoUsuario'));
                rmdir(base_url().'img/usuarios/'.$request->getPostGet('usuario'));

            }

            $modeloUsuarios = new Usuarios_modelo();
            $respuesta =$modeloUsuarios->mdlBorrarUsuario($datos);

            if($respuesta == "ok"){

                return $response->setJSON(['status' => 'success']);

            } 
            
        }

    }

}