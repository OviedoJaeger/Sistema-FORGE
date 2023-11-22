<?php
namespace App\Controllers;
use App\Models\Categorias_modelo;

class AjaxCategorias extends BaseController{

    /*=========================================
    EDITAR CATEGORIA
    ==========================================*/

    public $idCategoria;

    public function ajaxEditarCategoria(){

        $item = "id";
        $valor = $this->idCategoria;
        if($valor !== null){
            $categoriasModelo = new Categorias_modelo();
            $respuesta = $categoriasModelo->mdlMostrarCategoria($item, $valor);
            echo json_encode($respuesta);
        }

    }

}

/*=========================================
EDITAR CATEGORIA
==========================================*/
$request = \Config\Services::request();
if (null !== $request->getPost('idCategoria')) {
    
    
    $categoria = new AjaxCategorias();
    $categoria -> idCategoria = $request->getPost('idCategoria');
    $categoria -> ajaxEditarCategoria();
}