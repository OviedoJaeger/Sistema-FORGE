<?php
namespace App\Controllers;
use App\Models\Productos_modelo;

class TablaProductos extends BaseController{

    /*=========================================
    MOSTRAR LA TABLA DE PRODUCTOS
    ==========================================*/
    public function mostrarTablaProductos(){

        $item = null;
        $valor = null;
        $orden = "id";
        /*$productosCtrl = new Productos();
        $productos = $productosCtrl->ctrMostrarProductos($item, $valor, $orden);*/
        $productosModelo = new Productos_modelo();
        $productos = $productosModelo->mdlMostrarProductos($item, $valor, $orden);

        

        $datosJson = '{
            "data": [';

            for ($i = 0; $i < count($productos); $i++) {
                /*=========================================
                TRAEMOS LA IMAGEN
                ==========================================*/
                
                //$imagen = "<img src='".$productos[$i]["imagen"]."' width='40px'>";
                $imagen = "<img src='" . $productos[$i]["imagen"] . "' width='40px'>";
                

                /*=========================================
                TRAEMOS LA CATEGORIA
                ==========================================*/

                $item = "id";
                $valor = $productos[$i]["id_categoria"];
                $categoriasCtrl = new Categorias();
                $categorias = $categoriasCtrl->ctrMostrarCategorias($item, $valor);

                /*=========================================
                STOCK
                ==========================================*/

                if ($productos[$i]["stock"] <= 4) {
                
                    $stock = "<button class='btn btn-danger'>".$productos[$i]["stock"]."</button>";

                } else if ($productos[$i]["stock"] > 4 && $productos[$i]["stock"] <= 9) {
                    
                    $stock = "<button class='btn btn-warning'>".$productos[$i]["stock"]."</button>";

                } else {
                    
                    $stock = "<button class='btn btn-success'>".$productos[$i]["stock"]."</button>";

                }

                /*=========================================
                TRAEMOS LAS ACCIONES
                ==========================================*/



                    $botones =  "<div class='btn-group'><button class='btn btn-warning btnEditarProducto' idProducto='".$productos[$i]["id"]."' data-toggle='modal' data-target='#modalEditarProducto'><i class='fa fa-pencil'></i></button></div>"; 



                    $botones =  "<div class='btn-group'><button class='btn btn-warning btnEditarProducto' idProducto='".$productos[$i]["id"]."' data-toggle='modal' data-target='#modalEditarProducto'><i class='fa fa-pencil'></i></button><button class='btn btn-danger btnEliminarProducto' idProducto='".$productos[$i]["id"]."' codigo='".$productos[$i]["codigo"]."' imagen='".$productos[$i]["imagen"]."'><i class='fa fa-times'></i></button></div>"; 

                
                
                $datosJson .='[
                    "'.($i+1).'",
                    "'.$imagen.'",
                    "'.$productos[$i]["codigo"].'",
                    "'.$productos[$i]["descripcion"].'",
                    "'.$categorias[0]["categoria"].'",
                    "'.$stock.'",
                    "$ '.number_format($productos[$i]["precio_compra"],2).'",
                    "$ '.number_format($productos[$i]["precio_venta"],2).'",
                    "'.$productos[$i]["fecha"].'",
                    "'.$botones.'"
                ],';

            }

            $datosJson = substr($datosJson, 0, -1);
            
            $datosJson .= '] 
            }';


        echo $datosJson;


    }

}

/*=========================================
ACTIVAR LA TABLA DE PRODUCTOS
==========================================*/

/*$activarProductos = new TablaProductos();
$activarProductos -> mostrarTablaProductos();*/