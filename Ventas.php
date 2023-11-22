<?php
namespace App\Controllers;
use App\Models\Ventas_modelo;
use App\Models\Productos_modelo;
use App\Models\Clientes_modelo;

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class Ventas extends BaseController
{

	/*=============================================
	MOSTRAR VENTAS
	=============================================*/

	static public function ctrMostrarVentas($item, $valor)
	{

		$ventasModelo = new Ventas_modelo();
		$respuesta = $ventasModelo->mdlMostrarVentas($item, $valor);

		return $respuesta;
	}

	/*=============================================
	CREAR VENTA
	=============================================*/

	static public function ctrCrearVenta()
	{
		//debido a que es funcion estatica se debe instanciar el request para evitar usar el $this
		$request = \Config\Services::request();
		$response = \Config\Services::response();

		$nuevoValorEfectivo = $request->getPost('nuevoValorEfectivo');
        $nuevoCambioEfectivo = $request->getPost('nuevoCambioEfectivo');

		$ventasCtrl = new Ventas();

			if (null !== $request->getPost('nuevaVenta')) {

			/*=============================================
			ACTUALIZAR LAS COMPRAS DEL CLIENTE Y REDUCIR EL STOCK Y
			AUMENTAR LAS VENTAS DE LOS PRODUCTOS
			=============================================*/

			$listaProductos = json_decode($request->getPost('listaProductos'), true);

			$totalProductosComprados = array();

			/*var_dump($listaProductos);
				exit();*/

			foreach ($listaProductos as $key => $value) {

				array_push($totalProductosComprados, $value["cantidad"]);

				$item = "id";
				$valor = $value["id"];
				$orden = "id";
				$productosModelo = new Productos_modelo();
				$traerProducto = $productosModelo->mdlMostrarProductosPrueba($item, $valor, $orden);

				$traerProducto = [
					"ventas" => $traerProducto[0]["ventas"]
				];

				/*var_dump($value);
				exit();*/
				
				$item1a = "ventas";
			
				$valor1a = $value["cantidad"] + $traerProducto["ventas"];

				$nuevasVentas = $productosModelo->mdlActualizarProducto($item1a, $valor1a, $valor);

				$item1b = "stock";
				$valor1b = $value["stock"];

				$nuevoStock = $productosModelo->mdlActualizarProducto($item1b, $valor1b, $valor);
			}

			$item = "id";
			$valor = $request->getPost('seleccionarCliente');

			$clientesModelo = new Clientes_modelo();
			$traerCliente = $clientesModelo->mdlMostrarClientes($item, $valor);
			

			$item1a = "compras";
			$valor1a = array_sum($totalProductosComprados) + $traerCliente[0]["compras"];

			$comprasCliente = $clientesModelo->mdlActualizarCliente($item1a, $valor1a, $valor);

			$item1b = "ultima_compra";
			$valor1b = array_sum($totalProductosComprados) + $traerCliente[0]["compras"];

			date_default_timezone_set('America/Mexico_City');

			$fecha = date('Y-m-d');
			$hora = date('H:i:s');
			$valor1b = $fecha . ' ' . $hora;

			$fechaCliente = $clientesModelo->mdlActualizarCliente($item1b, $valor1b, $valor);

			/*=============================================
			GUARDAR LA COMPRA
			=============================================*/

			$datos = [
				"id_vendedor" => $request->getPost('idVendedor'),
				"id_cliente" => $request->getPost('seleccionarCliente'),
				"codigo" => $request->getPost('nuevaVenta'),
				"productos" => $request->getPost('listaProductos'),
				"impuesto" => $request->getPost('nuevoPrecioImpuesto'),
				"neto" => $request->getPost('nuevoPrecioNeto'),
				"total" => $request->getPost('totalVenta'),
				"metodo_pago" => $request->getPost('listaMetodoPago')
			];

			/*var_dump($datos);
			exit();*/


			$ventasModelo = new Ventas_modelo();
			$respuesta = $ventasModelo->mdlIngresarVenta($datos);


			if($respuesta == "ok"){

				return $response->setJSON(['status' => 'success']);

			}
			

		}

	}

	/*=============================================
			IMPRIMIR TICKET EN MPDF
	=============================================*/

	public function imprimirTicket($datos) {

		 // Name of the printer as recognized by the operating system
		 $printerName = 'your_printer_name';

		 // Create a connector for the printer
		 $connector = new WindowsPrintConnector($printerName);
	 
		 // Create a printer
		 $printer = new Printer($connector);
	 
		 // Add some content
		 $printer->text("Factura N.: " . $datos['id_factura'] . "\n");
		 $printer->text("Cliente: " . $datos['nombre_cliente'] . "\n");
		 $printer->text("Vendedor: " . $datos['nombre_vendedor'] . "\n");
		 // Add more content as needed
	 
		 // Cut the paper
		 $printer->cut();
	 
		 // Close the printer
		 $printer->close();
		
	}



	/*=============================================
	EDITAR VENTA
	=============================================*/

	static public function ctrEditarVenta()
	{
		$request = \Config\Services::request();
		$response = \Config\Services::response();

		//if (isset($_POST["editarVenta"])) {
		if (null !== $request->getPost('editarVenta')) {

			/*=============================================
			FORMATEAR TABLA DE PRODUCTOS Y LA DE CLIENTES
			=============================================*/
			$item = "codigo";
			$valor = $request->getPost('editarVenta');

			$ventasModelo = new Ventas_modelo();
			$traerVenta = $ventasModelo->mdlMostrarVentas($item, $valor);

			/*=============================================
			REVISAR SI VIENE PRODUCTOS EDITADOS *RECUPERAR LOS PRODUCTOS EN CASO DE NO MODIFICARLOS
			=============================================*/

			if ($request->getPost('listaProductos') == "") {

				$listaProductos = $traerVenta["productos"];
				$cambioProducto = false;
			} else {

				$listaProductos = $request->getPost('listaProductos');
				$cambioProducto = true;
			}

			if ($cambioProducto) {

				$productos = json_decode($traerVenta["productos"], true);

				$totalProductosComprados = array();

				$productosModelo = new Productos_modelo();

				foreach ($productos as $key => $value) {

					array_push($totalProductosComprados, $value["cantidad"]);

					$item = "id";
					$valor = $value["id"];
					$orden = "id";

					$traerProducto = $productosModelo->mdlMostrarProductos($item, $valor, $orden);

					$item1a = "ventas";
					$valor1a = $traerProducto[0]["ventas"] - $value["cantidad"];

					$nuevasVentas = $productosModelo->mdlActualizarProducto($item1a, $valor1a, $valor);

					$item1b = "stock";
					$valor1b = $value["cantidad"] + $traerProducto[0]["stock"];

					$nuevoStock = $productosModelo->mdlActualizarProducto($item1b, $valor1b, $valor);
				}

				$itemCliente = "id";
				$valorCliente = $request->getPost('seleccionarCliente');

				$clientesModelo = new Clientes_modelo();
				$traerCliente = $clientesModelo->mdlMostrarClientes($itemCliente, $valorCliente);
				$item1a = "compras";
				$valor1a = $traerCliente[0]["compras"] - array_sum($totalProductosComprados);

				$comprasCliente = $clientesModelo->mdlActualizarCliente($item1a, $valor1a, $valorCliente);

				/*=============================================
				ACTUALIZAR LAS COMPRAS DEL CLIENTE Y REDUCIR EL STOCK Y AUMENTAR LAS VENTAS DE LOS PRODUCTOS
				=============================================*/

				$listaProductos_2 = json_decode($listaProductos, true);

				$totalProductosComprados_2 = array();

				foreach ($listaProductos_2 as $key => $value) {

					array_push($totalProductosComprados_2, $value["cantidad"]);

					$item_2 = "id";
					$valor_2 = $value["id"];


					$traerProducto_2 = $productosModelo->mdlMostrarProductos($item_2, $valor_2, $orden);

					$item1a_2 = "ventas";
					$valor1a_2 = $value["cantidad"] + $traerProducto_2[0]["ventas"];

					$nuevasVentas_2 = $productosModelo->mdlActualizarProducto($item1a_2, $valor1a_2, $valor_2);

					$item1b_2 = "stock";
					$valor1b_2 = $traerProducto_2[0]["stock"] - $value["cantidad"];

					$nuevoStock_2 = $productosModelo->mdlActualizarProducto($item1b_2, $valor1b_2, $valor_2);
				}

				$item_2 = "id";
				$valor_2 = $_POST["seleccionarCliente"];

				$traerCliente_2 = $clientesModelo->mdlMostrarClientes($item_2, $valor_2);

				$item1a_2 = "compras";
				$valor1a_2 = array_sum($totalProductosComprados_2) + $traerCliente_2[0]["compras"];

				$comprasCliente_2 = $clientesModelo->mdlActualizarCliente($item1a_2, $valor1a_2, $valor_2);

				$item1b_2 = "ultima_compra";

				date_default_timezone_set('America/Mexico_City');

				$fecha = date('Y-m-d');
				$hora = date('H:i:s');
				$valor1b_2 = $fecha . ' ' . $hora;

				$fechaCliente_2 = $clientesModelo->mdlActualizarCliente($item1b_2, $valor1b_2, $valor_2);
			}
			/*=============================================
				GUARDAR CAMBIOS DE LA COMPRA
			=============================================*/

			$datos = array(
				"id_vendedor" => $request->getPost('idVendedor'),
				"id_cliente" => $request->getPost('seleccionarCliente'),
				"codigo" => $request->getPost('editarVenta'),
				"productos" => $listaProductos,
				"impuesto" => $request->getPost('nuevoPrecioImpuesto'),
				"neto" => $request->getPost('nuevoPrecioNeto'),
				"total" => $request->getPost('totalVenta'),
				"metodo_pago" => $request->getPost('listaMetodoPago')
			);

			$respuesta = $ventasModelo->mdlEditarVenta($datos);

			if ($respuesta == "ok") {

				return $response->setJSON(['status' => 'success']);

			}
		}
	}

	/*=============================================
	ELIMINAR VENTA
	=============================================*/

	static public function ctrEliminarVenta()
	{

		$request = \Config\Services::request();
		$response = \Config\Services::response();

		//if (isset($_GET["idVenta"])) {
		if (null !== $request->getPostGet('idVenta')) {

			$item = "id";
			$valor = $request->getPostGet("idVenta");

			$ventasModelo = new Ventas_modelo();
			$traerVenta = $ventasModelo->mdlMostrarVentas($item, $valor);

			/*=============================================
			ACTUALIZAR FECHA ÚLTIMA COMPRA
			=============================================*/

			$itemVentas = null;
			$valorVentas = null;

			$traerVentas = $ventasModelo->mdlMostrarVentas($itemVentas, $valorVentas);
			

			$guardarFechas = array();

			foreach ($traerVentas as $key => $value) {

				if ($value["id_cliente"] == $traerVenta["id_cliente"]) {

					array_push($guardarFechas, $value["fecha"]);
				}
			}

			$clientesModelo = new Clientes_modelo();

			if (count($guardarFechas) > 1) {


				if ($traerVenta["fecha"] > $guardarFechas[count($guardarFechas) - 2]) {

					$item = "ultima_compra";
					$valor = $guardarFechas[count($guardarFechas) - 2];
					$valorIdCliente = $traerVenta["id_cliente"];

					$comprasCliente = $clientesModelo->mdlActualizarCliente($item, $valor, $valorIdCliente);
				} else {

					$item = "ultima_compra";
					$valor = $guardarFechas[count($guardarFechas) - 1];
					$valorIdCliente = $traerVenta["id_cliente"];

					$comprasCliente = $clientesModelo->mdlActualizarCliente($item, $valor, $valorIdCliente);
				}
			} else {

				$item = "ultima_compra";
				$valor = "0000-00-00 00:00:00";
				$valorIdCliente = $traerVenta["id_cliente"];

				$comprasCliente = $clientesModelo->mdlActualizarCliente($item, $valor, $valorIdCliente);
			}

			/*=============================================
			FORMATEAR TABLA DE PRODUCTOS Y LA DE CLIENTES
			=============================================*/

			$productos =  json_decode($traerVenta["productos"], true);

			$totalProductosComprados = array();

			$productosModelo = new Productos_modelo();

			foreach ($productos as $key => $value) {

				array_push($totalProductosComprados, $value["cantidad"]);

				$item = "id";
				$valor = $value["id"];
				$orden = "id";

				$traerProducto = $productosModelo->mdlMostrarProductos($item, $valor, $orden);

				$item1a = "ventas";
				$valor1a = $traerProducto[0]["ventas"] - $value["cantidad"];

				$nuevasVentas = $productosModelo->mdlActualizarProducto($item1a, $valor1a, $valor);

				$item1b = "stock";
				$valor1b = $value["cantidad"] + $traerProducto[0]["stock"];

				$nuevoStock = $productosModelo->mdlActualizarProducto($item1b, $valor1b, $valor);
			}

			$itemCliente = "id";
			$valorCliente = $traerVenta["id_cliente"];

			$traerCliente = $clientesModelo->mdlMostrarClientes($itemCliente, $valorCliente);

			$item1a = "compras";
			$valor1a = $traerCliente[0]["compras"] - array_sum($totalProductosComprados);

			$comprasCliente = $clientesModelo->mdlActualizarCliente($item1a, $valor1a, $valorCliente);

			/*=============================================
			ELIMINAR VENTA
			=============================================*/

			$respuesta = $ventasModelo->mdlEliminarVenta($request->getPostGet('idVenta'));

			if ($respuesta == "ok") {

				return $response->setJSON(['status' => 'success']);
			}
		}
	}

	/*=============================================
	RANGO FECHAS
	=============================================*/

	static public function ctrRangoFechasVentas($fechaInicial, $fechaFinal)
	{

		$nuevaFecha = strtotime('+1 day', strtotime($fechaFinal));
		$fechaFinal = date('Y-m-d', $nuevaFecha);

		$ventasModelo = new Ventas_modelo();
		$respuesta = $ventasModelo->mdlRangoFechasVentas($fechaInicial, $fechaFinal);

		return $respuesta;
	}

	/*=============================================
	SUMA TOTAL VENTAS
	=============================================*/

	static public function ctrSumaTotalVentas()
	{
	
		$ventasModelo = new Ventas_modelo();
		$respuesta = $ventasModelo->mdlSumaTotalVentas();
		//var_dump($respuesta);exit();

		return $respuesta;
	}

	/*=============================================
				DESCARGAR Spreadsheet
	=============================================*/
	public function ctrDescargarReporteExcel()
	{
		$request = \Config\Services::request();
	
		if (null !== $request->getPostGet('fechaInicial')) {
			$fechaInicial = $request->getPostGet('fechaInicial');
			$fechaFinal = $request->getPostGet('fechaFinal');
			$ventas = Ventas::ctrRangoFechasVentas($fechaInicial, $fechaFinal);
		} else {
			$ventas = Ventas::ctrMostrarVentas(null, null);
		}
	
		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->getColumnDimension('A')->setWidth(15);
		$sheet->getColumnDimension('B')->setWidth(15);
		$sheet->getColumnDimension('C')->setWidth(22);
		$sheet->getColumnDimension('D')->setWidth(10);
		$sheet->getColumnDimension('E')->setWidth(30);
		$sheet->getColumnDimension('F')->setWidth(12);
		$sheet->getColumnDimension('G')->setWidth(12);
		$sheet->getColumnDimension('H')->setWidth(15);
		$sheet->getColumnDimension('I')->setWidth(18);
		$sheet->getColumnDimension('J')->setWidth(15);
		$sheet->getColumnDimension('K')->setWidth(20);

		$centrarEstilo = [
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
			],
			'borders' => [
				'allBorders' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					'color' => ['argb' => '000000'],
				],
			],
		];


		$sheet->getStyle('A1:K1')->applyFromArray($centrarEstilo); // Aplicar a la fila de encabezados
		$sheet->getStyle('A1:K' . $sheet->getHighestRow())->applyFromArray($centrarEstilo); // Aplicar al resto de las celdas
	  
		// Agregar encabezados de columna
		$sheet->setCellValue('A1', 'CÓDIGO');
		$sheet->setCellValue('B1', 'CLIENTE');
		$sheet->setCellValue('C1', 'VENDEDOR');
		$sheet->setCellValue('D1', 'CANTIDAD');
		$sheet->setCellValue('E1', 'PRODUCTOS');
		$sheet->setCellValue('F1', 'IMPUESTO');
		$sheet->setCellValue('G1', 'NETO');
		$sheet->setCellValue('H1', 'TOTAL');
		$sheet->setCellValue('I1', 'METODO DE PAGO');
		$sheet->setCellValue('J1', 'FECHA');
		$sheet->setCellValue('K1', 'TOTAL VENTAS');
		
	
		// Agregar datos de ventas
		$row = 2;
		$totalSum = 0;
		foreach ($ventas as $venta) {
			$clienteCtrl = new Clientes();
			$cliente = $clienteCtrl->ctrMostrarClientes("id", $venta["id_cliente"]);
			$vendedorCtrl = new Usuarios();
			$vendedor = $vendedorCtrl->ctrMostrarUsuarios("id", $venta["id_vendedor"]);
	
			$productos = json_decode($venta["productos"], true);
			$cantidad = '';
			$descripcion = '';
			foreach ($productos as $producto) {
				$cantidad .= $producto["cantidad"] . "\n";
				$descripcion .= $producto["descripcion"] . "\n";
			}
			
			$sheet->setCellValue('A' . $row, $venta["codigo"]);
			$sheet->setCellValue('B' . $row, $cliente[0]["nombre"]);
			$sheet->setCellValue('C' . $row, $vendedor[0]["nombre"]);
			$sheet->setCellValue('D' . $row, $cantidad);
			$sheet->getStyle('D' . $row)->getAlignment()->setWrapText(true)
			->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT)
			->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM);
			$sheet->setCellValue('E' . $row, $descripcion);
			$sheet->getStyle('E' . $row)->getAlignment()->setWrapText(true)
			->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT)
			->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM);
			$sheet->setCellValue('F' . $row, $venta["impuesto"]);
			$sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('$#,##0.00');
			$sheet->setCellValue('G' . $row, $venta["neto"]);
			$sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('$#,##0.00');
			$sheet->setCellValue('H' . $row, $venta["total"]);
			$sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('$#,##0.00');
			$sheet->setCellValue('I' . $row, $venta["metodo_pago"]);
			$sheet->setCellValue('J' . $row, substr($venta["fecha"], 0, 10));
			$totalSum += $venta["total"];
			$row++;
		}
		/*var_dump($totalSum);
		exit();*/
		$sheet->setCellValue('K2', $totalSum);
		$sheet->getStyle('K2')->getFont()->setSize(16);
		$sheet->getStyle('K2')->getNumberFormat()->setFormatCode('$#,##0.00');
	
		// Descargar archivo
		$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
		$filename = 'reporte.xlsx';
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
	}

}
