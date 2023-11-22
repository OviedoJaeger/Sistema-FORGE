<?php
namespace App\Controllers;

use App\Controllers\Clientes;
use App\Controllers\Usuarios;
use App\Models\Productos_modelo;
use App\Models\Ventas_modelo;
use Mpdf\Mpdf;

class Ticket extends BaseController {

    public function ctrTicket() {

        //TRAEMOS LA INFORMACIÓN DE LA VENTA
        $request = \Config\Services::request();

        $itemVenta = "codigo";
        $valorVenta = $request->getPostGet('codigo');
        $ventasCtrl = new Ventas_modelo();
        $respuestaVenta = $ventasCtrl->mdlMostrarVentas($itemVenta, $valorVenta);

        $ventasControlador = new Ventas();

        $cambio = $request->getPostGet('cambio');
        $pago = $request->getPostGet('pago');

        //var_dump($respuestaVenta["fecha"]);exit();
        //var_dump($respuestaVenta);exit();
        
        $fecha = $respuestaVenta["fecha"];
        $productos = json_decode($respuestaVenta["productos"], true);
        $neto = number_format($respuestaVenta["neto"], 2);
        $impuesto = number_format($respuestaVenta["impuesto"], 2);
        $total = number_format($respuestaVenta["total"], 2);
        $forma = $respuestaVenta["metodo_pago"];
        
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

        $mpdf = new Mpdf(['format' => [80, 200]]);

        //CREAMOS EL ARCHIVO PDF

        // ---------------------------------------------------------


        $bloque1 = <<<EOF

        <div style="display: flex; justify-content: center; align-items: center;">
            <div style=" width:70px" >
                <img src="
        EOF;
        $bloque1 .= base_url() . "img/plantilla/logo-lineal.png";
        $bloque1 .= <<<EOF
                " style="width:150px; height:50px;">
            </div>
        
            <div style="background-color:white; width:200px; text-align:left; font-size:10px; line-height:15px;">
                <br>
                Dirección: Calle 44B 92-11
            </div>
        
            <div style="background-color:white; width:140px; text-align:left; font-size:10px; line-height:15px;">
                Teléfono: 55 55 55 55
                <br>
                administrativo@hird.com.mx
            </div>
        
            <div style="background-color:white; width:200px; text-align:center;">
                <br>VENTA N.<br>$valorVenta<br>
            </div>
        </div>
        
        EOF;

// ---------------------------------------------------------

$bloque2 = <<<EOF

<div style="padding: 15px 0; border-top: 1px solid #000; border-bottom: 1px solid #000;">
    <div style="font-size:10px; text-align:left; line-height:15px;">
        CLIENTE: $respuestaCliente[nombre]
        <br>
        VENDEDOR: $respuestaVendedor[nombre]
        <br>
        FECHA: $fecha
    </div>
</div>

EOF;

// ---------------------------------------------------------

$bloque3 = <<<EOF

<table style="width:100%; border-collapse: collapse;">  
    <tr style="font-size:50px; text-align:left; line-height:30px;">
        <td style="width:90px">Producto</td>
        <td style="width:80px">Cantidad</td>
        <td style="width:50px">Valor Unit.</td>
        <td style="width:50px">Valor Total</td>
    </tr>
</table>

EOF;

// ---------------------------------------------------------

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

    <table style="width:100%; border-collapse: collapse; font-size:10px; text-align:center; line-height:20px; padding:5px 10px;">
    <tr>
        <td style="width:40%; text-align:left" >$item[descripcion]</td>
        <td style="width:20%;">$item[cantidad]</td>
        <td style="width:25%;">$$valorUnitario</td>
        <td style="width:25%;">$$precioTotal</td>
    </tr>
</table>

EOF;
}

// ---------------------------------------------------------

/*$bloque5 = <<<EOF

<div style="padding: 15px 0; border-top: 1px solid #000;">
    <div style="display: flex; justify-content: space-between; font-size:10px; text-align:left; line-height:15px;">
        <div style="width:340px"></div>
        <div>Neto: $ $neto</div>
    </div>
    <div style="display: flex; justify-content: space-between; font-size:10px; text-align:left; line-height:15px;">
        <div style="width:340px"></div>
        <div>Impuesto: $ $impuesto</div>
    </div>
    <div style="display: flex; justify-content: space-between; font-size:10px; text-align:left; line-height:15px;">
        <div style="width:340px"></div>
        <div>Total: $ $total</div>
    </div>

    <div style="display: flex; justify-content: space-between; font-size:10px; text-align:left; line-height:15px;">
        <div style="width:340px"></div>
        <div>Pago con: $ $pago  &nbsp;&nbsp;&nbsp;&nbsp; Cambio: $ $cambio</div>
    </div>
</div>

EOF;*/

$bloque5 = <<<EOF

    <br><br>

    <table style="width:100%; border-collapse: collapse; font-size:30px; line-height:50px;">

        <tr>
            <td style="width:220px;"></td>
            <td style="padding-right: 50px;">Forma de pago:</td>
            <td>$forma</td>
        </tr>
        <tr>
            <td style="width:220px;"></td>
            <td style="padding-right: 50px;">Neto:</td>
            <td>$$neto</td>
        </tr>
        <tr>
            <td style="width:220px;"></td>
            <td style="padding-right: 50px;">Impuesto:</td>
            <td>$$impuesto</td>
        </tr>
        <tr>
            <td style="width:220px;"></td>
            <td style="padding-right: 50px;">Total:</td>
            <td>$$total</td>
        </tr>
        <tr>
            <td style="width:220px;"></td>
            <td style="padding-right: 50px;">Pago con:</td>
            <td>$$pago</td>
        </tr>
        <tr>
            <td style="width:220px;"></td>
            <td style="padding-right: 50px;">Cambio:</td>
            <td>$$cambio</td>
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