<?php
require_once './models/Criptomoneda.php';
require_once './interfaces/IApiUsable.php';
require_once './models/AutentificadorJWT.php';

class CriptomonedaApi extends Criptomoneda implements IApiUsable
{

    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $archivos = $request->getUploadedFiles();
        //cargar Criptomoneda con parametros
        $Criptomoneda = new Criptomoneda();
        $Criptomoneda->nombre = $parametros['nombre'];
        $Criptomoneda->precio = $parametros['precio'];
        $Criptomoneda->AgregarFoto($archivos);
        $Criptomoneda->nacionalidad = $parametros['nacionalidad'];
        $Criptomoneda->crearCriptomoneda();

        $payload = json_encode(array(
            "Respuesta" => "Criptomoneda cargado"
        ));
        $response->getBody()
            ->write($payload);
        return $response->withHeader('Content-Type', 'application/json')
            ->withStatus(201);
    }

    public function ListarPorUnidad($request, $response, $args)
    {
        $lista = Criptomoneda::obtenerPorUnidad($args['tipoUnidad']);
        $payload = json_encode(array(
            "listaCriptomoneda" => $lista
        ));

        $response->getBody()
            ->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ListarPorNacionalidad($request, $response, $args)
    {
        $lista = Criptomoneda::obtenerPorNacionalidad($args['nacionalidad']);
        $payload = json_encode(array(
            "listaCriptomoneda" => $lista
        ));
        $response->getBody()
            ->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $id = $args['id'];
        $Criptomoneda = Criptomoneda::obtenerCriptomoneda($id);
        $payload = json_encode(array(
            "Criptomoneda" => $Criptomoneda
        ));
        $response->getBody()
            ->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }


    public function BorrarUno($request, $response, $args)
    {
        $CriptomonedaId = $args['id'];
        $filasAfectadas = Criptomoneda::borrarCriptomoneda($CriptomonedaId);
        if ($filasAfectadas > 0) {
            $payload = json_encode(array(
                "<li>mensaje" => "Criptomoneda dado de baja con exito",
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
10-(PUT) Puede Modificar los datos de una cripto incluso la imagen , y si la imagen ya existe debe
guardarla en la carpeta /Backup dentro de fotos.->solo admin (JWT) */
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $archivos = $request->getUploadedFiles();

        $CriptomonedaId = $args['id'];
        $Criptomoneda = Criptomoneda::obtenerCriptomoneda($CriptomonedaId);
        $Criptomoneda->nombre = $parametros['nombre'];
        $Criptomoneda->precio = $parametros['precio'];
        $Criptomoneda->nacionalidad = $parametros['nacionalidad'];
        $Criptomoneda->estado = $parametros['estado'];


        if ($Criptomoneda->foto != null || $Criptomoneda->foto != "") {
            $Criptomoneda->MoverABackUP();
        }
        $Criptomoneda->AgregarFoto($archivos);
        $respuesta = $Criptomoneda->ModificarCriptomoneda();
        if ($respuesta) {
            $payload = json_encode(array(
                "mensaje" => "Criptomoneda modificado con exito",
                "status" => 200
            ));
        } else {
            $payload = json_encode(array(
                "mensaje" => "Error al modificar el Criptomoneda",
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
        $lista = Criptomoneda::obtenerTodos();
        $payload = json_encode(array(
            "listaCriptomoneda" => $lista
        ));

        $response->getBody()
            ->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
