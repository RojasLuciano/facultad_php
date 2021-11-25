<?php
require_once './models/AutentificadorJWT.php';
require_once './models/Usuario.php';

use GuzzleHttp\Psr7\Response;

class VerificacionMiddleware
{
    //region Empleados
    public static function VerificarVendedor($request, $handler)
    {
        echo "VerificarVendedor";
        $parametros = $request->getParsedBody();
        // $usuario = $parametros['usuario']; 

        $logueado = Usuario::validarUsuarioYPassword($parametros['usuario'], $parametros['clave']);

        if ($logueado) {
            $esVendedor = Usuario::obtenerUsuario($parametros['usuario']);

            //TO DO: verificar que el usuario sea admin
            if ($esVendedor->tipo == "vendedor") {
                $response = $handler->handle($request);
                $payload = json_encode(array(
                        "<li>Perfil: " => "Acceso habilitado",
                        "status" => 200
                    ));
            } else {
                $response = new Response();
                $payload = json_encode(array(
                        "Perfil" => 'No tienes habilitado el acceso.',
                        "status" => 400
                    ));
            }
        } else {
            $newResponse = new Response();
            $newResponse->getBody()->write("Usuario o contraseña incorrectos");
            return $newResponse;
        }
        $response->getBody()
        ->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }


    /**
     * Funcion para validar el usuario
     */
    public function VerificarUsuario($request, $response)
    {
        echo "VerificarUsuario";
        $parametros = $request->getParsedBody();
        //creamos el usuario
        $usr = new Usuario();
        $usr->usuario = $parametros['usuario'];
        $usr->clave = $parametros['clave'];
        $usr->mail = $parametros['mail'];
        $usr->tipo = $parametros['tipo'];

        $respuesta = Usuario::validarUsuario($usr);

        if ($respuesta) {
            $ingreso = array(
                "usuario" => $usr->usuario,
                "clave" => password_hash($usr->clave, PASSWORD_DEFAULT)
            );
            $token = AutentificadorJWT::CrearToken($ingreso);

            if ($token == true) {
                $payload = json_encode(array(
                    "Respuesta" => "OK",
                    "Perfil" => $usr->tipo
                ));
            } else {
                $payload = json_encode(array(
                    "mensaje" => "Error al crear el token",
                    "status" => 400
                ));
            }
        } else {
            $payload = json_encode(array(
                "mensaje" => "Error al validar el empleado",
                "status" => 400
            ));
        }
        $response->getBody()
            ->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function VerificarRegistrado($request, $handler)
    {
        echo "VerificarRegistrado";
        $parametros = $request->getParsedBody();

        $logueado = Usuario::validarUsuarioYPassword($parametros['usuario'], $parametros['clave']);

        if ($logueado) {
            $esVendedor = Usuario::obtenerUsuario($parametros['usuario']);

            //Verificar si es vendedor o administrador
            if ($esVendedor->tipo == "admin" || $esVendedor->tipo == "cliente") {
                $response = $handler->handle($request);
                $payload = json_encode(array(
                    "<li>Perfil: " => "Acceso habilitado",
                    "status" => 200
                ));
            } else {
                $response = new Response();
                $payload = json_encode(array(
                    "Perfil" => 'No tienes habilitado el acceso.',
                    "status" => 400
                ));
            }
        } else {
            $newResponse = new Response();
            $newResponse->getBody()->write("Usuario o contraseña incorrectos");
            return $newResponse;
        }
        $response->getBody()
            ->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }


    public static function VerificarProveedor($request, $handler)
    {
        echo "VerificarProveedor";
        $parametros = $request->getParsedBody();

        $logueado = Usuario::validarUsuarioYPassword($parametros['usuario'], $parametros['clave']);

        if ($logueado) {
            $esVendedor = Usuario::obtenerUsuario($parametros['usuario']);
         
            if ($esVendedor->tipo == "proveedor") {
                $response = $handler->handle($request);
                $payload = json_encode(array(
                    "<li>Perfil: " => "Acceso habilitado",
                    "status" => 200
                ));
            } else {
                $response = new Response();
                $payload = json_encode(array(
                    "Perfil" => 'No tienes habilitado el acceso.',
                    "status" => 400
                ));
            }
        } else {
            $newResponse = new Response();
            $newResponse->getBody()->write("Usuario o contraseña incorrectos");
            return $newResponse;
        }
        $response->getBody()
            ->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }


    public static function VerificarAdmin($request, $handler)
    {
        echo "VerificarAdmin";
        $parametros = $request->getParsedBody();

        $logueado = Usuario::validarUsuarioYPassword($parametros['usuario'], $parametros['clave']);

        if ($logueado) {
            $esPerfil = Usuario::obtenerUsuario($parametros['usuario']);

            if ($esPerfil->tipo == "admin") {
                $response = $handler->handle($request);
                $payload = json_encode(array(
                    "<li>Perfil: " => "Acceso habilitado",
                    "status" => 200
                ));
            } else {
                $response = new Response();
                $payload = json_encode(array(
                    "Perfil" => 'No tienes habilitado el acceso.',
                    "status" => 400
                ));
            }
        } else {
            $newResponse = new Response();
            $newResponse->getBody()->write("Usuario o contraseña incorrectos");
            return $newResponse;
        }
        $response->getBody()
            ->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }


    public static function VerificarSiEsUsuario($request, $handler)
    {
        echo "VerificarSiEsUsuario";
        $parametros = $request->getParsedBody();
        $logueado = Usuario::validarUsuarioYPassword($parametros['usuario'], $parametros['clave']);
        if ($logueado) {
            $response = $handler->handle($request);
            $payload = json_encode(array(
                "<li>Perfil: " => "Acceso habilitado",
                "status" => 200
            ));
        } else {
            $response = new Response();
            $payload = json_encode(array(
                "Perfil" => 'No tienes habilitado el acceso.',
                "status" => 400
            ));
        }
        $response->getBody()
            ->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }



    /*   

    public function VerificarEmpleado($request, $handler)
    {

        $parametros = $request->getParsedBody();
        $sector = $parametros['sector'];

        if ($sector == "barra" || $sector == "cerveza" || $sector == "cocina" || $sector == "candy" || $sector == "admin") {
            $response = $handler->handle($request);
            $payload = json_encode(array(
                "mensaje: " => "El empleado pertenece al sector " . $sector,
                "status" => 200
            ));
        } else {
            $payload = json_encode(array(
                "mensaje" => 'Solo empleados',
                "status" => 401
            ));
        }

        $response->getBody()
            ->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

 
    public function VerificarMozo($request, $handler)
    {
        $parametros = $request->getParsedBody();
        // $idEmpleado = $parametros['empleado'];
        $empleado = Empleado::TraerEmpleado($parametros['empleado']);
        $response = $handler->handle($request); //con esto pasa al siguiente request

        if ($empleado->puesto == "mozo") {
            $payload = json_encode(array(
                "mensaje" => "El empleado es mozo",
                "status" => 200
            ));
            // $data = (array)$empleado;          
            // $payload = json_encode($data);      
        } else {
            $response = new Response();     //si hay bardo, meto esto y cuchau
            $payload = json_encode(array(
                "mensaje" => 'Solo mozos',
                "status" => 401
            ));
        }
        $response->getBody()->write($payload);

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(201);
    }


    public function VerificarSocio($request, $handler)
    {
        $parametros = $request->getParsedBody();
        // $idEmpleado = $parametros['empleado'];
        $usuario = Usuario::obtenerUsuario($parametros['usuario']);
        $response = $handler->handle($request); //con esto pasa al siguiente request

        if (
            $usuario->perfil == "socio" && $usuario->fechaBaja != NULL
        ) {
            $payload = json_encode(array(
                "mensaje" => "Usuario con perfil socio",
                "status" => 200
            ));
            // $data = (array)$empleado;          
            // $payload = json_encode($data);      
        } else {
            $response = new Response();     //si hay bardo, meto esto y cuchau
            $payload = json_encode(array(
                "mensaje" => 'El perfil del usuario no es socio',
                "status" => 401
            ));
        }
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }



    public static function ValidarToken($request, $handler)
    {
        //Tengo que meter el token en Authorization -> Bearer token -> token
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $esValido = false;

        try {
            AutentificadorJWT::VerificarToken($token);
            $esValido = true;
        } catch (Exception $e) {
            $payload = json_encode(array(
                'error' => $e->getMessage()
            ));
        }
        if ($esValido) {
            $response = $handler->handle($request);
            $payload = json_encode(array('Token valido'));
        } else {
            $response = new Response();
            $payload = json_encode(array(
                'Token invalido'
            ));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }


    public static function DevolverDatos($request, $handler)
    {

        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);

        try {

            $response = $handler->handle($request);
            $data = AutentificadorJWT::ObtenerData($token);
            $payload = json_encode(array(
                'datos token ' => $data
            ));
        } catch (Exception $e) {
            $payload = json_encode(array(
                'error token' => $e->getMessage()
            ));
        }
        $response->getBody()
            ->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }


    public static function DevolverPayLoad($request, $handler)
    {


        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $response = $handler->handle($request);
        try {
            $payload = json_encode(array(
                'payload' => AutentificadorJWT::ObtenerPayLoad($token)
            ));
        } catch (Exception $e) {

            $payload = json_encode(array(
                'error payload' => $e->getMessage()
            ));
        }

        $response->getBody()
            ->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
 */
} //class
