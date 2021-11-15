<?php
/*
Author: Nicolas Luciano Rojas
*/
require_once './db/AccesoDatos.php';
require_once './models/Hortaliza.php';
require_once './models/Venta.php';
require_once './models/FileManager.php';

use Fpdf\Fpdf;

class FileManagerApi extends FileManager
{

    public function DescargaHortalizas($request, $response, $args)
    {
        $lista = Hortaliza::obtenerTodos();
        if (count($lista) > 0) {
            $destination = ".\Reportes\\";
            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }
            $fecha = new DateTime(date("d-m-Y"));
            FileManager::guardarJson($lista, $destination . 'Hortaliza' ."_". $fecha->format('d-m-Y') . '.csv');
            $ruta = $destination . 'Hortaliza' . "_" . $fecha->format('d-m-Y') . '.csv';
            $payload = json_encode(array("Archivo en: " => $ruta));
        } else {
            $payload = json_encode(array("mensaje" => "No hubo movimientos"));
        }
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    //Funcion para devolver un pdf con los datos de las ventas 
    public function generarPDF($request, $response, $args)
    {
        echo "<br>" . "generarPDF" . "<br>";
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
        $pdf->Output('F', $destination . 'Ventas' . $fecha->format('d-m-Y') . '.pdf');
        $payload = json_encode(array("mensaje" => 'archivo generado en' . $destination . 'Ventas' . $fecha->format('d-m-Y') . '.pdf'));
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }



} //clase
?>