<?php
require_once './models/Hortaliza.php';
require_once './interfaces/IApiUsable.php';
require_once './models/AutentificadorJWT.php';

class HortalizaApi extends Hortaliza implements IApiUsable
{

    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $archivos = $request->getUploadedFiles();
        //cargar hortaliza con parametros
        $hortaliza = new Hortaliza();
        $hortaliza->nombre = $parametros['nombre'];
        $hortaliza->precio = $parametros['precio'];
        $hortaliza->clima = $parametros['clima'];
        $hortaliza->tipoUnidad = $parametros['tipoUnidad'];
        $hortaliza->AgregarFoto($archivos);
        $hortaliza->crearHortaliza();

        $payload = json_encode(array(
            "Respuesta" => "Hortaliza cargado"
        ));
        $response->getBody()
            ->write($payload);
        return $response->withHeader('Content-Type', 'application/json')
            ->withStatus(201);
    }

    public function ListarPorUnidad($request, $response, $args)
    {
        $lista = Hortaliza::obtenerPorUnidad($args['tipoUnidad']);
        $payload = json_encode(array(
            "listaHortaliza" => $lista
        ));

        $response->getBody()
            ->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ListarPorClima($request, $response, $args)
    {
        $lista = Hortaliza::obtenerPorClima($args['clima']);
        $payload = json_encode(array(
            "listaHortaliza" => $lista
        ));
        $response->getBody()
            ->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $id = $args['id'];
        $Hortaliza = Hortaliza::obtenerHortaliza($id);
        $payload = json_encode(array(
            "Hortaliza" => $Hortaliza
        ));
        $response->getBody()
            ->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }


    public function BorrarUno($request, $response, $args)
    {
        $HortalizaId = $args['id'];
        $filasAfectadas = Hortaliza::borrarHortaliza($HortalizaId);
        if ($filasAfectadas > 0) {
            $payload = json_encode(array(
                "<li>mensaje" => "Hortaliza dado de baja con exito",
                "status" => 200
            ));
        } else {
            $payload = json_encode(array(
                "<li>mensaje: " => "Error al eliminar el empleado",
                "status" => 400
            ));
        }
        $response->getBody()
            ->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
/* 
10-(PUT) Puede Modificar los datos de una hortaliza incluso la imagen , y si la imagen ya existe debe
guardarla en la carpeta /Backup dentro de fotos.->solo admin (JWT) */
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $archivos = $request->getUploadedFiles();

        $HortalizaId = $args['id'];
        $Hortaliza = Hortaliza::obtenerHortaliza($HortalizaId);
        $Hortaliza->nombre = $parametros['nombre'];
        $Hortaliza->precio = $parametros['precio'];
        $Hortaliza->clima = $parametros['clima'];
        $Hortaliza->tipoUnidad = $parametros['tipoUnidad'];
        if($Hortaliza->foto != null || $Hortaliza->foto != ""){
            $Hortaliza->MoverABackUP();
        }
        $Hortaliza->AgregarFoto($archivos);
        $respuesta = $Hortaliza->ModificarHortaliza();
        if ($respuesta) {
            $payload = json_encode(array(
                "mensaje" => "Hortaliza modificado con exito",
                "status" => 200
            ));
        } else {
            $payload = json_encode(array(
                "mensaje" => "Error al modificar el hortaliza",
                "status" => 400
            ));
        }
        $response->getBody()
            ->write($payload);
        return $response->withHeader('Content-Type', 'application/json')
            ->withStatus(201);
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Hortaliza::obtenerTodos();
        $payload = json_encode(array(
            "listaHortaliza" => $lista
        ));

        $response->getBody()
            ->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }


}
