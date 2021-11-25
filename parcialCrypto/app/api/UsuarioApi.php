<?php
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';
require_once './models/AutentificadorJWT.php';

class UsuarioApi extends Usuario implements IApiUsable
{

  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $usuario = $parametros['usuario'];
    $clave = $parametros['clave'];
    $mail = $parametros['mail'];
    $tipo = $parametros['tipo'];

    // Creamos el usuario
    $usr = new Usuario();
    $usr->usuario = $usuario;
    $usr->clave = $clave;
    $usr->mail = $mail;
    $usr->tipo = $tipo;
    $usr->crearUsuario();
    $payload = json_encode(array(
      "Respuesta" => "Usuario cargado"
    ));
    $response->getBody()
      ->write($payload);
    return $response->withHeader('Content-Type', 'application/json')
    ->withStatus(201);
  }

  public function TraerUno($request, $response, $args)
  {
    // Buscamos usuario por nombre
    $usr = $args['usuario'];
    $usuario = Usuario::obtenerUsuario($usr);
    $payload = json_encode($usuario);

    $response->getBody()
      ->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Usuario::obtenerTodos();
    $payload = json_encode(array(
      "listaUsuario" => $lista
    ));

    $response->getBody()
      ->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $usuario = $parametros['usuario'];
    $clave = $parametros['clave'];
    $mail = $parametros['mail'];
    $tipo = $parametros['tipo'];

    $usr = new Usuario();
    $usr->usuario = $usuario;
    $usr->clave = $clave;
    $usr->mail = $mail;
    $usr->tipo = $tipo;
    //$usr->modificarUsuario();
    $payload = json_encode(array(
      "Respuesta" => "Usuario modificado"
    ));
    $response->getBody()
      ->write($payload);
    return $response->withHeader('Content-Type', 'application/json')
    ->withStatus(201);
  }

  public function BorrarUno($request, $response, $args)
  {
    $usuarioId = $args['id'];

    $filasAfectadas = Usuario::darDeBajaUsuario($usuarioId);
    if ($filasAfectadas > 0) {

      // $new_log = new Logger();
      $new_log->idEmpleado = $usuarioId;
      $new_log->accion = "Usuario dado de baja";
      //$new_log->InsertarLog();
      $payload = json_encode(array(
        "<li>mensaje" => "Usuario dado de baja con exito",
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

  public function ActivarUno($request, $response, $args)
  {
    $usuarioId = $args['id'];

    $filasAfectadas = Usuario::activarUsuario($usuarioId);
    if ($filasAfectadas > 0) {

      //  $new_log = new Logger();
      $new_log->idEmpleado = $usuarioId;
      $new_log->accion = "Usuario reactivado";
      // $new_log->InsertarLog();
      $payload = json_encode(array(
        "<li>mensaje" => "Usuario reactivado con exito",
        "status" => 200
      ));
    } else {
      $payload = json_encode(array(
        "<li>mensaje: " => "Error al reactivar el usuario",
        "status" => 400
      ));
    }
    $response->getBody()
      ->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  } 

}
