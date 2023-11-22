<?php
namespace App\Controllers;
use App\Models\Categorias_modelo;

class Categorias extends BaseController{

    /*=========================================
    CREAR CATEGORIAS
    ==========================================*/

    static public function ctrCrearCategoria(){

        $request = \Config\Services::request();
        $response = \Config\Services::response();
        if ( null !== $request->getPost('nuevaCategoria')) {
            
            if (preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $request->getPost('nuevaCategoria'))) {
            

                $datos = $request->getPost('nuevaCategoria');
                $modelo_categorias = new Categorias_modelo();
                $respuesta = $modelo_categorias->mdlIngresarCategoria($datos);

                if ($respuesta == "ok") {
                    
                    return $response->setJSON(['status' => 'success']);

                }

            }else {
                
                
                    return $response->setJSON(['status' => 'error']);
            }
        }

    }


    /*=========================================
    MOSTRAR CATEGORIAS
    ==========================================*/

    static public function ctrMostrarCategorias($item, $valor){

        $modelo_categorias = new Categorias_modelo();
        $respuesta = $modelo_categorias->mdlMostrarCategorias($item, $valor);

        return $respuesta;

    }

    /*=========================================
    EDITAR CATEGORIA
    ==========================================*/

    static public function ctrEditarCategoria(){

        $request = \Config\Services::request();
        $response = \Config\Services::response();
        //if (isset($_POST["editarCategoria"])) {
        if ( null !== $request->getPost('editarCategoria')) {
            
            if (preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $request->getPost('editarCategoria'))) {
                

                $datos = array("categoria"=>$request->getPost('editarCategoria'), "id"=>$request->getPost('idCategoria'));
                $modelo_categorias = new Categorias_modelo();
                $respuesta = $modelo_categorias->mdlEditarCategoria($datos);

                if ($respuesta == "ok") {
                    
                    
                    return $response->setJSON(['status' => 'success']);
                }

            }else {
                
                    return $response->setJSON(['status' => 'error']);
                    
            }
        }

    }
    
    /*=========================================
    BORRAR CATEGORIA
    ==========================================*/

    static public function ctrBorrarCategoria(){

        $request = \Config\Services::request();
        $response = \Config\Services::response();
        //if (isset($_GET["idCategoria"])) {
        if ( null !== $request->getPostGet('idCategoria')) {

            $datos = $request->getPostGet("idCategoria");
            $categorias_modelo = new Categorias_modelo();
            //$respuesta = ModeloCategorias::mdlBorrarCategoria($tabla, $datos);
            $respuesta = $categorias_modelo->mdlBorrarCategoria($datos);

            if ($respuesta == "ok") {

                    return $response->setJSON(['status' => 'success']);

            }

        }

    }

}