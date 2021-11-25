<?php
// Author: Nicolas Luciano Rojas
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
require_once './api/CriptomonedaApi.php';
require_once './api/VentaApi.php';
require_once './api/FileManagerApi.php';

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
    1-(POST)verifique usuario:(JWT)
    verificar si el usuario( mail , tipo: {admin, cliente} y clave) coinciden con los guardados en la base de
    datos, retorna un objeto con la respuesta en OK y el tipo de perfil del usuario
    */
  $group->post('/verificar', \VerificacionMiddleware::class . ':VerificarUsuario');

  /*
      2-(POST)Alta cripto moneda( precio, nombre, foto, nacionalidad)->solo admin/(JWT)
    */
  $group->post('/alta', \CriptomonedaApi::class . ':CargarUno')
    ->add(\VerificacionMiddleware::class . ':VerificarAdmin');

  /*
    3-(1pt)(GET)listado de todas las cripto monedas -> sin autentificación
    */
  $group->get('/listar', \CriptomonedaApi::class . ':TraerTodos');

  /*  
    4-(GET)listado de todas las cripto de una nacionalidad pasada por parámetro-> sin autentificación
    */
  $group->get('/nacionalidad/{nacionalidad}', \CriptomonedaApi::class . ':ListarPorNacionalidad');

  /*
    5-(1pt)(GET)traer una cripto por ID->cualquier usuario registrado
    */
  $group->get('/id/{id}', \CriptomonedaApi::class . ':TraerUno')
    ->add(\VerificacionMiddleware::class . ':VerificarSiEsUsuario');

  /*
  6-(POST)Alta de ventaCripto (id,fecha,cantidad…y demás datos que crea necesarios) además de tener
  una imagen (jpg , jpeg ,png)asociada a la venta que será nombrada por el nombre de la cripto ,el
  nombre del cliente más la fecha en la carpeta /FotosCripto ->cualquier usuario registrado(JWT)
    */
 $group->post('/ventaCripto', \VentaApi::class . ':CargarUno')
   ->add(\VerificacionMiddleware::class . ':VerificarRegistrado');


  /* 7- (GET)Traer todas las ventas de cripto “alemanas” entre en 10 y 13 de junio ->solo admin(JWT)
    */
  $group->get('/ventas/{desde}/{hasta}', \VentaApi::class . ':ListarPorFechas')
    ->add(\VerificacionMiddleware::class . ':VerificarAdmin');


  /* 8-(GET)l Traer todos los usuarios que compraron la moneda eterium(o cualquier otra, buscada por
nombre)->solo admin(JWT)
  */
  $group->get('/buscar/{nombre}', \VentaApi::class . ':ListarUsuariosQueCompraron')
  ->add(\VerificacionMiddleware::class . ':VerificarAdmin');

  /* 
  9-(DELETE)borrado de una cripto por ID->solo admin (JWT
  */
  $group->delete('/borrar/{id}', \CriptomonedaApi::class . ':BorrarUno')
  ->add(\VerificacionMiddleware::class . ':VerificarAdmin');

  /* 
  10-(PUT) Puede Modificar los datos de una cripto incluso la imagen , y si la imagen ya existe debe
  guardarla en la carpeta /Backup dentro de fotos.->solo admin (JWT)
  */
  $group->post('/modificar/{id}', \CriptomonedaApi::class . ':ModificarUno')
  ->add(\VerificacionMiddleware::class . ':VerificarAdmin');

  /* 
  11-(GET)Un PDF listado de todas ventas.
  */
  $group->get('/pdf', \FileManagerApi::class . ':generarPDF');

});

$app->run();



  // /* 
  // CSV con hortalizas
  // */
  // $group->get('/csv', \FileManagerApi::class . ':DescargaHortalizas');

  // /* 
  // leer CSV con hortalizas
  // */
  // $group->post('/leer/csv', \FileManagerApi::class . ':leerCSV');


