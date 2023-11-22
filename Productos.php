<?php
namespace App\Controllers;
use App\Models\Productos_modelo;

class Productos extends BaseController
{

    /* Sustituciones
    $request->getPost('');
    $session->get('');
    $request->getPostGet('');

    */

    /*=========================================
    MOSTRAR PRODUCTOS
    ==========================================*/

    static public function ctrMostrarProductos($item, $valor, $orden)
    {

        $productosModelo = new Productos_modelo();
        $respuesta = $productosModelo->mdlMostrarProductos($item, $valor, $orden);

        return $respuesta;
    }

    /*=========================================
    CREAR PRODUCTO
    ==========================================*/

    static public function ctrCrearProducto()
    {

        $request = \Config\Services::request();
        $response = \Config\Services::response();

        if (null !== $request->getPost('nuevaDescripcion')) {

            if (
                preg_match('/^[a-zA-Z0-9.,ñÑáéíóúÁÉÍÓÚüÜ ]+$/', $request->getPost('nuevaDescripcion')) &&
                preg_match('/^[0-9]+$/', $request->getPost('nuevoStock')) &&
                preg_match('/^[0-9.]+$/', $request->getPost('nuevoPrecioCompra')) &&
                preg_match('/^[0-9.]+$/', $request->getPost('nuevoPrecioVenta'))
            ) {

                /*=========================================
                VALIDAR IMAGEN
                ==========================================*/

                $ruta = base_url()."img/productos/default/anonimo.png";

                $file = $request->getFile('nuevaImagen');

                
                if ($file->isValid() && !$file->hasMoved()) {

                    
                    list($ancho, $alto) = getimagesize($file->getTempName());

                    $nuevoAncho = 500;
                    $nuevoAlto = 500;

                    /*=========================================
                    CREAMOS EL DIRECTORIO DONDE VAMOS A GUARDAR LA IMAGEN DEL PRODUCTO
                    ==========================================*/
                    $directorio = base_url()."img/productos/" . $request->getPost('nuevoCodigo');

                    mkdir($directorio, 0755);

                    /*=========================================
                    DE ACUERDO AL TIPO DE IMAGEN APLICAMOS LAS FUNCIONES POR DEFECTO DE PHP
                    ==========================================*/

                    if ($file->getMimeType() == "jpeg") {

                        /*=========================================
                        GUARDAMOS LA IMAGEN EN EL DIRECTORIO
                        ==========================================*/

                        $aleatorio = mt_rand(100, 999);

                        $ruta = base_url()."img/productos/" . $request->getPost('nuevoCodigo') . "/" . $aleatorio . ".jpg";

                        $origen = imagecreatefromjpeg($file->getTempName());

                        $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

                        imagecopyresized(
                            $destino,
                            $origen,
                            0,
                            0,
                            0,
                            0,
                            $nuevoAncho,
                            $nuevoAlto,
                            $ancho,
                            $alto
                        );

                        imagejpeg($destino, $ruta);
                    }

                    if ($file->getMimeType() == "png") {

                        /*=========================================
                        GUARDAMOS LA IMAGEN EN EL DIRECTORIO
                        ==========================================*/

                        $aleatorio = mt_rand(100, 999);

                        $ruta = base_url()."img/productos/" . $request->getPost('nuevoCodigo') . "/" . $aleatorio . ".png";

                        $origen = imagecreatefrompng($file->getTempName());

                        $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

                        imagecopyresized(
                            $destino,
                            $origen,
                            0,
                            0,
                            0,
                            0,
                            $nuevoAncho,
                            $nuevoAlto,
                            $ancho,
                            $alto
                        );

                        imagepng($destino, $ruta);
                    }
                }

                $datos = array(
                    "id_categoria" => $request->getPost('nuevaCategoria'),
                    "codigo" => $request->getPost('nuevoCodigo'),
                    "descripcion" => $request->getPost('nuevaDescripcion'),
                    "stock" => $request->getPost('nuevoStock'),
                    "precio_compra" => $request->getPost('nuevoPrecioCompra'),
                    "precio_venta" => $request->getPost('nuevoPrecioVenta'),
                    "imagen" => $ruta
                );

                $productosModelo = new Productos_modelo();
                $respuesta = $productosModelo->mdlIngresarProducto($datos);

                if ($respuesta == "ok") {

                    return $response->setJSON(['status' => 'success']);

                }
            } else {

                    return $response->setJSON(['status' => 'errorP']);

            }
        }
    }

    /*=========================================
    EDITAR PRODUCTO
    ==========================================*/

    static public function ctrEditarProducto()
    {

        $request = \Config\Services::request();
        $response = \Config\Services::response();

        if (null !== $request->getPost('editarDescripcion')) {

            if (
                preg_match('/^[a-zA-Z0-9.,ñÑáéíóúÁÉÍÓÚüÜ ]+$/', $request->getPost('editarDescripcion')) &&
                preg_match('/^[0-9]+$/', $request->getPost('editarStock')) &&
                preg_match('/^[0-9.]+$/', $request->getPost('editarPrecioCompra')) &&
                preg_match('/^[0-9.]+$/', $request->getPost('editarPrecioVenta'))
            ) {

                /*=========================================
                VALIDAR IMAGEN
                ==========================================*/

                $ruta = $request->getPost('imagenActual');

                $file = $request->getFile('editarImagen');

                
                if ($file->isValid() && !$file->hasMoved()) {

                    
                    list($ancho, $alto) = getimagesize($file->getTempName());

                    $nuevoAncho = 500;
                    $nuevoAlto = 500;

                    /*=========================================
                    CREAMOS EL DIRECTORIO DONDE VAMOS A GUARDAR LA IMAGEN DEL PRODUCTO
                    ==========================================*/

                    
                    $directorio = base_url()."img/productos/" . $request->getPost('editarCodigo');

                    /*=========================================
                    PRIMERO PREGUNTAMOS SI EXISTE OTRA IMAGEN EN LA BD
                    ==========================================*/
                    
                    if (!empty($request->getPost('imagenActual')) && $request->getPost('imagenActual') != base_url()."img/productos/default/anonimo.png") {

                        //unlink($_POST["imagenActual"]);
                        unlink($request->getPost('imagenActual'));
                    } else {

                        mkdir($directorio, 0755);
                    }

                    /*=========================================
                    DE ACUERDO AL TIPO DE IMAGEN APLICAMOS LAS FUNCIONES POR DEFECTO DE PHP
                    ==========================================*/

                    if ($file->getMimeType() == "jpeg") {

                        /*=========================================
                        GUARDAMOS LA IMAGEN EN EL DIRECTORIO
                        ==========================================*/

                        $aleatorio = mt_rand(100, 999);

                        $ruta = base_url()."img/productos/" . $request->getPost('editarCodigo') . "/" . $aleatorio . ".jpg";

                        $origen = imagecreatefromjpeg($file->getTempName());

                        $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

                        imagecopyresized(
                            $destino,
                            $origen,
                            0,
                            0,
                            0,
                            0,
                            $nuevoAncho,
                            $nuevoAlto,
                            $ancho,
                            $alto
                        );

                        imagejpeg($destino, $ruta);
                    }

                    if ($file->getMimeType() == "png") {

                        /*=========================================
                        GUARDAMOS LA IMAGEN EN EL DIRECTORIO
                        ==========================================*/

                        $aleatorio = mt_rand(100, 999);

                        $ruta = base_url()."img/productos/" . $request->getPost('editarCodigo') . "/" . $aleatorio . ".png";

                        $origen = imagecreatefrompng($file->getTempName());

                        $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

                        imagecopyresized(
                            $destino,
                            $origen,
                            0,
                            0,
                            0,
                            0,
                            $nuevoAncho,
                            $nuevoAlto,
                            $ancho,
                            $alto
                        );

                        imagepng($destino, $ruta);
                    }
                }


                $datos = array(
                    "id_categoria" => $request->getPost('editarCategoria'),
                    "codigo" => $request->getPost('editarCodigo'),
                    "descripcion" => $request->getPost('editarDescripcion'),
                    "stock" => $request->getPost('editarStock'),
                    "precio_compra" => $request->getPost('editarPrecioCompra'),
                    "precio_venta" => $request->getPost('editarPrecioVenta'),
                    "imagen" => $ruta
                );

                $productosModelo = new Productos_modelo();
                $respuesta = $productosModelo->mdlEditarProducto($datos);

                if ($respuesta == "ok") {

                    return $response->setJSON(['status' => 'success']);
                }
            } else {

                return $response->setJSON(['status' => 'error']);

            }
        }
    }

    /*=========================================
    BORRAR PRODUCTO
    ==========================================*/

    static public function ctrEliminarProducto()
    {

        $request = \Config\Services::request();
        $response = \Config\Services::response();

        if (null !== $request->getPostGet('idProducto')) {

            $datos = $request->getPostGet('idProducto');

            if ($request->getPostGet('imagen') != "" && $request->getPostGet('imagen') != base_url()."img/productos/default/anonimo.png") {

                unlink($request->getPostGet('imagen'));
                
                rmdir(base_url()."img/productos/" . $request->getPostGet('codigo'));
            }

            $productosModelo = new Productos_modelo();
            $respuesta = $productosModelo->mdlEliminarProducto($datos);

            if ($respuesta == "ok") {

                return $response->setJSON(['status' => 'success']);
                
            }
        }
    }

    /*=============================================
	MOSTRAR SUMA VENTAS
	=============================================*/

	static public function ctrMostrarSumaVentas(){

        $productosModelo = new Productos_modelo();
		$respuesta = $productosModelo->mdlMostrarSumaVentas();

		return $respuesta;

	}


}