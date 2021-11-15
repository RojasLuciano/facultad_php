<?php
require_once './models/Venta.php';
require_once './interfaces/IApiUsable.php';
require_once './models/AutentificadorJWT.php';



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












}
