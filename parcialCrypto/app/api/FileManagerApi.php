<?php
// Author: Nicolas Luciano Rojas
require_once './db/AccesoDatos.php';
require_once './models/Criptomoneda.php';
require_once './models/Venta.php';
require_once './models/FileManager.php';

use Fpdf\Fpdf;

class FileManagerApi extends FileManager
{

    public function DescargaHortalizas($request, $response, $args)
    {
        $lista = Venta::obtenerTodos();
        if (count($lista) > 0) {
            $destination = ".\Reportes\\";
            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }
            $fecha = new DateTime(date("d-m-Y"));
            FileManager::guardarJson($lista, $destination . 'Venta' ."_". $fecha->format('d-m-Y') . '.csv');
            $ruta = $destination . 'Venta' . "_" . $fecha->format('d-m-Y') . '.csv';
            $payload = json_encode(array("Archivo en: " => $ruta));
        } else {
            $payload = json_encode(array("mensaje" => "No hubo movimientos"));
        }
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function leerCSV($request, $response, $args)
    {
        //leer un archivo csv desde postman y enviar los datos a la base de hortalizas
        $archivo = $request->getUploadedFiles();

        $data = $archivo['archivo'];
        $json = $data->getStream()->getContents();
        var_dump(json_decode($json));

        foreach (json_decode($json) as $key => $value) {
            echo $key . ": " . $value . "<br>";
/*             $hortaliza = new Hortaliza();

            $hortaliza->id = $value['id'];
            $hortaliza->nombre = $value['nombre'];
            $hortaliza->precio = $value['precio'];
            $hortaliza->cantidad = $value['cantidad'];
            $hortaliza->fecha = $value['fecha'];
            $hortaliza->crearHortaliza(); */
        }


 /*        foreach (json_decode($json) as $key => $value) {
            $hortaliza = new Hortaliza();
            $hortaliza->nombre = $value->nombre;
            $hortaliza->precio = $value->precio;
            $hortaliza->tipo = $value->tipo;
            $hortaliza->cantidad = $value->cantidad;
            $hortaliza->fecha = $value->fecha;
            $hortaliza->crearHortaliza();
        } */
        $payload = json_encode(array("mensaje" => "Archivo subido"));

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
        //Titulo
        $pdf->Cell(80);
        $pdf->Cell(30, 10, 'Nicolas Luciano Rojas', 0, 0, 'C');
        $pdf->Ln(20);
        //Titulo

        $pdf->Cell(40, 10, 'ID', 1);
        $pdf->Cell(40, 10, 'IDCripto', 1);
        $pdf->Cell(40, 10, 'Nombre Cliente', 1);
        $pdf->Cell(40, 10, 'Fecha', 1);
        $pdf->Cell(40, 10, 'Cantidad', 1);
        $pdf->Cell(40, 10, 'Foto', 1);
        $pdf->Ln();
        foreach ($lista as $cripto) {
            $pdf->Cell(40, 10, $cripto->id, 1);
            $pdf->Cell(40, 10, $cripto->idCripto, 1);
            $pdf->Cell(40, 10, $cripto->nombreCliente, 1);
            $pdf->Cell(40, 10, $cripto->fecha, 1);
            $pdf->Cell(40, 10, $cripto->cantidad, 1);
            //No envio la ruta de la foto por que ocupa mucho lugar en el pdf
            $pdf->Ln();
        } 

        $fecha = new DateTime(date("d-m-Y"));
        $destination = ".\Reportes\\";
        if (!file_exists($destination)) {
            mkdir($destination, 0777, true);
        }
        $pdf->Output('F', $destination . 'Ventas'."_" . $fecha->format('d-m-Y') . '.pdf');
        $payload = json_encode(array("mensaje" => 'archivo generado en' . $destination . 'Ventas' . "_" . $fecha->format('d-m-Y') . '.pdf'));
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }



} //clase
