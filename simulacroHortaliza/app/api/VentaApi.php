<?php
require_once './models/Venta.php';
require_once './interfaces/IApiUsable.php';
require_once './models/AutentificadorJWT.php';

use Fpdf\Fpdf;

class VentaApi extends Venta implements IApiUsable
{

    public function CargarUno($request, $response, $args)
    {
        echo "Cargar una venta";
        $parametros = $request->getParsedBody();
        $archivos = $request->getUploadedFiles();

        $venta = new Venta();
        $venta->idHortaliza = $parametros['idHortaliza'];
        //Traigo la hortaliza para obtener su nombre
        $hortaliza = Hortaliza::obtenerHortaliza($venta->idHortaliza);


        $venta->nombreCliente = $parametros['nombreCliente'];
        $venta->fecha = $parametros['fecha'];
        $venta->cantidad = $parametros['cantidad'];
        $venta->tipoUnidad = $parametros['tipoUnidad'];

        if ($parametros['nombreCliente'] != null) {
            $venta->AgregarFoto($archivos, $hortaliza->nombre);
        } else {
            $venta->AgregarFoto($archivos);
        }
        $venta->crearVenta();

        $payload = json_encode(array(
            "Respuesta" => "Venta cargado"
        ));
        $response->getBody()
            ->write($payload);
        return $response->withHeader('Content-Type', 'application/json')
            ->withStatus(201);
    }

    public function ListarPorClimaSecoYFechas($request, $response, $args)
    {
        echo "ListarPorClimaSecoYFechas";
        $desde = $args['desde'];
        $hasta = $args['hasta'];

        $lista = Venta::obtenerHortalizaPorClimaSecoYFechas($desde, $hasta);
        $payload = json_encode(array(
            "listaVenta" => $lista
        ));
        $response->getBody()
            ->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ListarUsuariosQueCompraron($request, $response, $args)
    {
        echo "ListarUsuariosQueCompraron";
        $nombre = $args['nombre'];
        $lista = Venta::obtenerVentasPorNombreHortaliza($nombre);
        $payload = json_encode(array(
            "listaVenta" => $lista
        ));
        $response->getBody()
            ->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ListarPorUnidad($request, $response, $args)
    {
        $lista = Venta::obtenerPorUnidad($args['tipoUnidad']);
        $payload = json_encode(array(
            "listaVenta" => $lista
        ));

        $response->getBody()
            ->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ListarPorClima($request, $response, $args)
    {
        $lista = Venta::obtenerPorClima($args['id']);
        $payload = json_encode(array(
            "listaVenta" => $lista
        ));
        $response->getBody()
            ->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $id = $args['id'];
        $Venta = Venta::obtenerVenta($id);
        $payload = json_encode(array(
            "Venta" => $Venta
        ));
        $response->getBody()
            ->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    //No se esta usando
    public function TraerTodos($request, $response, $args)
    {
        $lista = Venta::obtenerTodos();
        $payload = json_encode(array(
            "listaVenta" => $lista
        ));

        $response->getBody()
            ->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    //No se esta usando
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $id = $parametros['id'];
        $Venta = Venta::obtenerVenta($id);
        $Venta->idHortaliza = $parametros['idHortaliza'];
        $Venta->nombreCliente = $parametros['nombreCliente'];
        $Venta->fecha = $parametros['fecha'];
        $Venta->cantidad = $parametros['cantidad'];
        $Venta->tipoUnidad = $parametros['tipoUnidad'];
        $Venta->ModificarVenta();
        $payload = json_encode(array(
            "Respuesta" => "Venta modificado"
        ));
        $response->getBody()
            ->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    //No se esta usando
    public function BorrarUno($request, $response, $args)
    {
        $id = $args['id'];
        $Venta = Venta::obtenerVenta($id);
        $Venta->BorrarVenta();
        $payload = json_encode(array(
            "Respuesta" => "Venta borrado"
        ));
        $response->getBody()
            ->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }


    //Funcion para devolver un pdf con los datos de las ventas 
    public function generarPDF($request, $response, $args)
    {
        echo "<br>"."generarPDF"."<br>";
        $lista = Venta::obtenerTodos();
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(40, 10, 'Nombre', 1);
        $pdf->Cell(40, 10, 'Fecha', 1);
        $pdf->Cell(40, 10, 'Cantidad', 1);
        $pdf->Cell(40, 10, 'Tipo de Unidad', 1);
        $pdf->Cell(40, 10, 'Nombre Cliente', 1);
        $pdf->Ln();
        foreach ($lista as $venta) {
            $pdf->Cell(40, 10, $venta->idHortaliza, 1);
            $pdf->Cell(40, 10, $venta->fecha, 1);
            $pdf->Cell(40, 10, $venta->cantidad, 1);
            $pdf->Cell(40, 10, $venta->tipoUnidad, 1);
            $pdf->Cell(40, 10, $venta->nombreCliente, 1);
            $pdf->Ln();
        }

        $fecha = new DateTime(date("d-m-Y"));
        $destination = ".\Reportes\\";
        if (!file_exists($destination)) {
            mkdir($destination, 0777, true);
        }
        $pdf->Output('F', $destination . 'Ventas'. $fecha->format('d-m-Y') . '.pdf');
        $payload = json_encode(array("mensaje" => 'archivo generado en' . $destination . 'Ventas' . $fecha->format('d-m-Y') . '.pdf'));
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }









}
