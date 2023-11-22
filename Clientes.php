<?php

namespace App\Controllers;

use App\Models\Clientes_modelo;

class Clientes extends BaseController
{
/* Sustituciones
    $request->getPost('');
    $session->get('');
    $request->getPostGet('');

*/


    /*=========================================
                CREAR CLIENTES
    ==========================================*/

    static public function ctrCrearCliente()
    {

        $request = \Config\Services::request();
        $response = \Config\Services::response();

        if (isset($_POST["nuevoCliente"])) {

            if (
                preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $request->getPost('nuevoCliente')) &&
                preg_match('/^[0-9]+$/', $request->getPost('nuevoDocumentoId')) &&
                preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/', $request->getPost('nuevoEmail')) &&
                preg_match('/^[()\-0-9 ]+$/', $request->getPost('nuevoTelefono')) &&
                preg_match('/^[#\.\-a-zA-Z0-9 ]+$/', $request->getPost('nuevaDireccion'))
            ) {

                $datos = array(
                    "nombre" => $request->getPost('nuevoCliente'),
                    "documento" => $request->getPost('nuevoDocumentoId'),
                    "email" => $request->getPost('nuevoEmail'),
                    "telefono" => $request->getPost('nuevoTelefono'),
                    "direccion" => $request->getPost('nuevaDireccion'),
                    "fecha_nacimiento" => $request->getPost('nuevaFechaNacimiento')
                );

                $clientesModelo = new Clientes_modelo();
                $respuesta = $clientesModelo->mdlIngresarCliente($datos);

                if ($respuesta == "ok") {

                    return $response->setJSON(['status' => 'success']);
                }
            } else {

                    return $response->setJSON(['status' => 'error']);
            }
        }
    }

    /*=========================================
    MOSTRAR CLIENTES
    ==========================================*/

    static public function ctrMostrarClientes($item, $valor)
    {

        $clientesModelo = new Clientes_modelo();
        $respuesta = $clientesModelo->mdlMostrarClientes($item, $valor);

        return $respuesta;
    }

    /*=========================================
    EDITAR CLIENTES
    ==========================================*/

    static public function ctrEditarCliente()
    {

        $request = \Config\Services::request();
        $response = \Config\Services::response();

        if (null !== $request->getPost('editarCliente')) {

            if (
                preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $request->getPost('editarCliente')) &&
                preg_match('/^[0-9]+$/', $request->getPost('editarDocumentoId')) &&
                preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/', $request->getPost('editarEmail')) &&
                preg_match('/^[()\-0-9 ]+$/', $request->getPost('editarTelefono')) &&
                preg_match('/^[#\.\-a-zA-Z0-9 ]+$/', $request->getPost('editarDireccion'))
            ) {

                $datos = array(
                    "id" => $request->getPost('idCliente'),
                    "nombre" => $request->getPost('editarCliente'),
                    "documento" => $request->getPost('editarDocumentoId'),
                    "email" => $request->getPost('editarEmail'),
                    "telefono" => $request->getPost('editarTelefono'),
                    "direccion" => $request->getPost('editarDireccion'),
                    "fecha_nacimiento" => $request->getPost('editarFechaNacimiento')
                );

                $clientesModelo = new Clientes_modelo();
                $respuesta = $clientesModelo->mdlEditarCliente($datos);

                if ($respuesta == "ok") {

                    return $response->setJSON(['status' => 'success']);
                } 
                
            }else {

                    return $response->setJSON(['status' => 'error']);

            }
        }
    }

    /*=========================================
    ELIMINAR CLIENTES
==========================================*/

    static public function ctrEliminarCliente()
    {

        $request = \Config\Services::request();
        $response = \Config\Services::response();

        if (null !== $request->getPostGet('idCliente')) {

            $datos = $request->getPostGet('idCliente');

            $clientesModelo = new Clientes_modelo();
            $respuesta = $clientesModelo->mdlEliminarCLiente($datos);

            if ($respuesta == "ok") {

            return $response->setJSON(['status' => 'success']);
            }
        }
    }
}
