<?php
namespace App\Controllers;

use App\Controllers\Clientes;
use App\Controllers\Usuarios;
use App\Models\Productos_modelo;
use App\Models\Ventas_modelo;
use Mpdf\Mpdf;

class Factura extends BaseController {

    public function traerImpresionFactura() {

        //TRAEMOS LA INFORMACIÓN DE LA VENTA
        $request = \Config\Services::request();

        $itemVenta = "codigo";
        $valorVenta = $request->getPostGet('codigo');
        $ventasCtrl = new Ventas_modelo();
        $respuestaVenta = $ventasCtrl->mdlMostrarVentas($itemVenta, $valorVenta);
        //var_dump($respuestaVenta["fecha"]);exit();
        //var_dump($valorVenta,$respuestaVenta);
        
        $fecha = substr($respuestaVenta["fecha"], 0, -8);
        $productos = json_decode($respuestaVenta["productos"], true);
        $neto = number_format($respuestaVenta["neto"], 2);
        $impuesto = number_format($respuestaVenta["impuesto"], 2);
        $total = number_format($respuestaVenta["total"], 2);
        //var_dump($productos);exit();
        //TRAEMOS LA INFORMACIÓN DEL CLIENTE

        $itemCliente = "id";
        $valorCliente = $respuestaVenta["id_cliente"];
        $clientesCtrl = new Clientes();
        $respuestaCliente = $clientesCtrl->ctrMostrarClientes($itemCliente, $valorCliente);
        $respuestaCliente = [
            "nombre" => $respuestaCliente[0]["nombre"],

        ];

        //TRAEMOS LA INFORMACIÓN DEL VENDEDOR

        $itemVendedor = "id";
        $valorVendedor = $respuestaVenta["id_vendedor"];

        $usuariosCtrl = new Usuarios();
        $respuestaVendedor = $usuariosCtrl->ctrMostrarUsuarios($itemVendedor, $valorVendedor);
        //var_dump($respuestaVendedor);exit();
        $respuestaVendedor = [
            "nombre" => $respuestaVendedor[0]["nombre"],

        ];

        $mpdf = new Mpdf();

        //CREAMOS EL ARCHIVO PDF

        // ---------------------------------------------------------

        $bloque1 = <<<EOF

            <table>
                
                <tr>
                    
                    <td style="width:90px"><img src="
EOF;
                    
                    $bloque1 .= base_url() . "img/plantilla/logo-lineal.png";

                    $bloque1 .= <<<EOF
                    " width="150" height="50"></td>

                    <td style="background-color:white; width:200px">

                        <div style="font-size:10px; text-align:right; line-height:15px;">
                            

                            <br>
                            Dirección: Calle 44B 92-11

                        </div>

                    </td>

                    <td style="background-color:white; width:140px">

                        <div style="font-size:10px; text-align:right; line-height:15px;">
                            
                            <br>
                            Teléfono: 55 55 55 55 55
                            
                            <br>
                            administrativo@hird.com.mx

                        </div>
                        
                    </td>

                    
                    <td style="background-color:white; width:150px; text-align:center; color:red"><br><br>VENTA N.<br>$valorVenta</td>

                </tr>

            </table>

EOF;


        // ---------------------------------------------------------

        $bloque2 = <<<EOF

            <table>
                
                

            </table>

            <table style="font-size:10px; padding:5px 10px;">
            
                <tr>
                
                    <td style="border: 1px solid #666; background-color:white; width:390px">

                        Cliente: $respuestaCliente[nombre]

                    </td>

                    <td style="border: 1px solid #666; background-color:white; width:150px; text-align:right">
                    
                        Fecha: $fecha

                    </td>

                </tr>

                <tr>
                
                    <td style="border: 1px solid #666; background-color:white; width:540px">Vendedor: $respuestaVendedor[nombre]</td>

                </tr>

                <tr>
                
                <td style="border-bottom: 1px solid #666; background-color:white; width:540px"></td>

                </tr>

            </table>

EOF;


        // ---------------------------------------------------------

        $bloque3 = <<<EOF

            <table style="font-size:10px; padding:5px 10px;">

                <tr>
                
                <td style="border: 1px solid #666; background-color:white; width:260px; text-align:center">Producto</td>
                <td style="border: 1px solid #666; background-color:white; width:80px; text-align:center">Cantidad</td>
                <td style="border: 1px solid #666; background-color:white; width:100px; text-align:center">Valor Unit.</td>
                <td style="border: 1px solid #666; background-color:white; width:100px; text-align:center">Valor Total</td>

                </tr>

            </table>

EOF;

        // ---------------------------------------------------------
        //$productosHtml = '';

        $bloque4 = '';

        foreach ($productos as $key => $item) {

            $itemProducto = "descripcion";
            $valorProducto = $item["descripcion"];
            $orden = null;
            
            $productosCtrl = new Productos();
            $respuestaProducto = $productosCtrl->ctrMostrarProductos($itemProducto, $valorProducto, $orden);
            
            $valorUnitario = number_format($respuestaProducto[0]["precio_venta"], 2);

            $precioTotal = number_format($item["total"], 2);

            $bloque4 .= <<<EOF

                <table style="font-size:10px; padding:5px 10px;">

                    <tr>
                        
                        <td style="border: 1px solid #666; color:#333; background-color:white; width:260px; text-align:center">
                            $item[descripcion]
                        </td>

                        <td style="border: 1px solid #666; color:#333; background-color:white; width:80px; text-align:center">
                            $item[cantidad]
                        </td>

                        <td style="border: 1px solid #666; color:#333; background-color:white; width:100px; text-align:center">$ 
                            $valorUnitario
                        </td>

                        <td style="border: 1px solid #666; color:#333; background-color:white; width:100px; text-align:center">$ 
                            $precioTotal
                        </td>


                    </tr>

                </table>


EOF;

}

        // ---------------------------------------------------------

        $bloque5 = <<<EOF

	<table style="font-size:10px; padding:5px 10px;">

		<tr>

			<td style="color:#333; background-color:white; width:340px; text-align:center"></td>

			<td style="border-bottom: 1px solid #666; background-color:white; width:100px; text-align:center"></td>

			<td style="border-bottom: 1px solid #666; color:#333; background-color:white; width:100px; text-align:center"></td>

		</tr>
		
		<tr>
		
			<td style="border-right: 1px solid #666; color:#333; background-color:white; width:340px; text-align:center"></td>

			<td style="border: 1px solid #666;  background-color:white; width:100px; text-align:center">
				Neto:
			</td>

			<td style="border: 1px solid #666; color:#333; background-color:white; width:100px; text-align:center">
				$ $neto
			</td>

		</tr>

		<tr>

			<td style="border-right: 1px solid #666; color:#333; background-color:white; width:340px; text-align:center"></td>

			<td style="border: 1px solid #666; background-color:white; width:100px; text-align:center">
				Impuesto:
			</td>
		
			<td style="border: 1px solid #666; color:#333; background-color:white; width:100px; text-align:center">
				$ $impuesto
			</td>

		</tr>

		<tr>
		
			<td style="border-right: 1px solid #666; color:#333; background-color:white; width:340px; text-align:center"></td>

			<td style="border: 1px solid #666; background-color:white; width:100px; text-align:center">
				Total:
			</td>
			
			<td style="border: 1px solid #666; color:#333; background-color:white; width:100px; text-align:center">
				$ $total
			</td>

		</tr>


	</table>

EOF;

        // Agregar contenido al PDF
        $mpdf->WriteHTML($bloque1);
        $mpdf->WriteHTML($bloque2);
        $mpdf->WriteHTML($bloque3);
        $mpdf->WriteHTML($bloque4);

        $mpdf->WriteHTML($bloque5);

        // Guardar el PDF en un archivo
        $pdfFilePath = "factura.pdf";
        $mpdf->Output();
        die;

    }
}