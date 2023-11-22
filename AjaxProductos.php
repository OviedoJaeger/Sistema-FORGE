<?php
namespace App\Controllers;
use App\Models\Productos_modelo;

class AjaxProductos extends BaseController{

  /*=============================================
  GENERAR CÓDIGO A PARTIR DE ID CATEGORIA
  =============================================*/
  public $idCategoria;

  public function ajaxCrearCodigoProducto(){

  	$item = "id_categoria";
  	$valor = $this->idCategoria;
    $orden = "id";

    $productosCtrl = new Productos();
  	$respuesta = $productosCtrl->ctrMostrarProductos($item, $valor, $orden);

  	echo json_encode($respuesta);

  }


  /*=============================================
  EDITAR PRODUCTO
  =============================================*/ 

  public $idProducto;
  public $traerProductos;
  public $nombreProducto;

  public function ajaxEditarProducto(){


    $productosModelo = new Productos_modelo();
    

    if($this->idProducto !== null){
      
            $item = "id";
            $valor = $this->idProducto;
            $orden = "id";
            //$productosCtrl = new Productos();
            //$respuesta = $productosCtrl->ctrMostrarProductos($item, $valor, $orden);
            $respuesta = $productosModelo->mdlMostrarProducto($item, $valor, $orden);

            echo json_encode($respuesta);

          }
          
      

  }

  public function ajaxTraerProductoSelect(){


    $productosModelo = new Productos_modelo();
    

      if($this->nombreProducto !== null){
        if($this->nombreProducto !== ""){

            $item = "descripcion";
            $valor = $this->nombreProducto;
            $orden = "id";
            $respuesta = $productosModelo->mdlMostrarProducto($item, $valor, $orden);

            echo json_encode($respuesta);

        }
      }
          
      

  }

  public function ajaxTraerProducto(){


    $productosModelo = new Productos_modelo();
    

          if($this->traerProductos == "ok"){

            $item = null;
            $valor = null;
            $orden = "id";
            $respuesta = $productosModelo->mdlMostrarProductos($item, $valor, $orden);

            echo json_encode($respuesta);


          }

  }

}



/*=============================================
GENERAR CÓDIGO A PARTIR DE ID CATEGORIA
=============================================*/	
$request = \Config\Services::request();
if(null !== $request->getPost("idCategoria")){

	$codigoProducto = new AjaxProductos();
	$codigoProducto -> idCategoria =  $request -> getPost("idCategoria");
	$codigoProducto -> ajaxCrearCodigoProducto();

}
/*=============================================
EDITAR PRODUCTO
=============================================*/ 

if(null !== $request->getPost("idProducto")){

  $editarProducto = new AjaxProductos();
  $editarProducto -> idProducto = $request -> getPost("idProducto");
  $editarProducto -> ajaxEditarProducto();

}

/*=============================================
TRAER PRODUCTO
=============================================*/ 

if(null !== $request->getPost("traerProductos")){

  $traerProductos = new AjaxProductos();
  $traerProductos -> traerProductos = $request -> getPost("traerProductos");
  $traerProductos -> ajaxTraerProducto();

}

/*=============================================
TRAER PRODUCTO
=============================================*/ 

if(null !== $request->getPost("nombreProducto")){

  $traerProductos = new AjaxProductos();
  $traerProductos -> nombreProducto = $request -> getPost("nombreProducto");
  $traerProductos -> ajaxTraerProductoSelect();

}







