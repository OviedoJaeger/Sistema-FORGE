<?php
namespace App\Controllers;
use App\Models\Clientes_modelo;
class AjaxClientes extends BaseController{

	/*=============================================
	EDITAR CLIENTE
	=============================================*/	



	public $idCliente;

	public function ajaxEditarCliente(){

		$item = "id";
		$valor = $this->idCliente;

		if($valor !== null){
		$clientesModel = new Clientes_modelo();
		$respuesta = $clientesModel->mdlMostrarCliente($item, $valor);

		echo json_encode($respuesta);
		}

	}

}

/*=============================================
EDITAR CLIENTE
=============================================*/	
$request = \Config\Services::request();
if($request->getPost('idCliente') !== null){

	
	$cliente = new AjaxClientes();
	$cliente -> idCliente = $request -> getPost("idCliente");
	$cliente -> ajaxEditarCliente();

}