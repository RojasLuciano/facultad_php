<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';
require_once './middlewares/VerificacionMiddleware.php';
require_once './api/UsuarioApi.php';
require_once './api/HortalizaApi.php';
require_once './api/VentaApi.php';

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();
// Set base path
$app->setBasePath('/app');

/*
TEMPLATE PARCIAL
php -S localhost:100 -t app
composer update
composer require firebase/php-jwt
composer require fpdf/fpdf
*/

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes
$app->group('/parcial', function (RouteCollectorProxy $group) {

  $group->post('/altausuario', \UsuarioApi::class . ':CargarUno');

  /*
    1-(POST)verifique usuario:( crea JWT)
    verificar si el usuario( mail , tipo: {comprador, vendedor,proveedor} y clave) coinciden con los
    guardados en la base de datos, retorna un objeto con la respuesta en OK y el tipo de perfil del usuario
    */
  $group->post('/verificar', \VerificacionMiddleware::class . ':VerificarUsuario');

  /*
    2-(POST)Alta hortaliza( precio, nombre, foto, clima{seco; húmedo;todos},tipoUnidad{kilo; bolsa; paquete}
    ->solo vendedor/(JWT)
    */
  $group->post('/alta', \HortalizaApi::class . ':CargarUno')
    ->add(\VerificacionMiddleware::class . ':VerificarVendedor');

  /*
    3-(1pt)(GET)listado de todas las hortalizas por un tipoUnidad recibido por parametros -> sin
    autentificación
    */
  $group->get('/tipo/{tipoUnidad}', \HortalizaApi::class . ':ListarPorUnidad');

  /*
    4-(GET)listado de todas las hortalizas de un clima pasado por parámetro-> sin autentificación
    */
  $group->get('/clima/{clima}', \HortalizaApi::class . ':ListarPorClima');

  /*
    5-(1pt)(GET)traer una hortaliza por ID->cualquier usuario registrado
    */
  $group->get('/id/{id}', \HortalizaApi::class . ':TraerUno')
    ->add(\VerificacionMiddleware::class . ':VerificarSiEsUsuario');

  /*
    6-(POST)Alta de ventaHortaliza(id,fecha,cantidad,tipoUnidad …y demás datos que crea necesarios)
    además de tener una imagen (jpg , jpeg ,png)asociada a la venta que será nombrada por el nombre
    de la hortaliza,el nombre del cliente más la fecha en la carpeta /FotosHortalizas ->usuario
    registrado( vendedor,proveedor ...JWT)
    */
  $group->post('/ventaHortaliza', \VentaApi::class . ':CargarUno')
    ->add(\VerificacionMiddleware::class . ':VerificarRegistrado');

  /* 7- (GET)Traer todas las ventas de hortalizas de clima seco entre en 10 y 13 de junio ->solo
    vendedor(JWT)
    */
  $group->get('/fechas/{desde}/{hasta}', \VentaApi::class . ':ListarPorClimaSecoYFechas')
    ->add(\VerificacionMiddleware::class . ':VerificarVendedor');

  /* 8-(GET)l Traer todos los usuarios que compraron zanahoria (o cualquier otra, buscada por
  nombre)->solo proveedor(JWT) 
  */
  $group->get('/buscar/{nombre}', \VentaApi::class . ':ListarUsuariosQueCompraron')
  ->add(\VerificacionMiddleware::class . ':VerificarProveedor');

  /* 
  9-(DELETE)borrado de una hortaliza por ID->solo vendedor(JWT)
  */
  $group->delete('/borrar/{id}', \HortalizaApi::class . ':BorrarUno')
  ->add(\VerificacionMiddleware::class . ':VerificarVendedor');

  /* 
  10-(PUT) Puede Modificar los datos de una hortaliza incluso la imagen , y si la imagen ya existe debe
  guardarla en la carpeta /Backup dentro de fotos.->solo admin (JWT)
  */
  $group->post('/modificar/{id}', \HortalizaApi::class . ':ModificarUno')
  ->add(\VerificacionMiddleware::class . ':VerificarAdmin');

  /* 
  11-(GET)Un PDF listado de todas ventas.
  */
  $group->get('/pdf', \VentaApi::class . ':generarPDF');

});

//})->add(\VerificacionMiddleware::class . ':VerificarVendedor')


/*   $group->delete('[/{id}]', \UsuarioApi::class . ':BorrarUno'); //Dar de baja
  $group->put('/{id}', \UsuarioApi::class . ':ModificarUno');
  $group->get('[/]', \UsuarioApi::class . ':TraerTodos');
  $group->get('/{usuario}', \UsuarioApi::class . ':TraerUno');
  $group->post('/{id}', \UsuarioApi::class . ':ActivarUno'); //Reactivar */

$app->run();
