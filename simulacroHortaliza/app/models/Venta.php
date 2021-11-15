<?php
class Venta
{

    public $id;
    public $idHortaliza;
    public $nombreCliente;
    public $fecha;
    public $cantidad;
    public $tipoUnidad;
    public $foto;

    //region Getters y Setters
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getIdHortaliza()
    {
        return $this->idHortaliza;
    }

    public function setIdHortaliza($idHortaliza)
    {
        $this->idHortaliza = $idHortaliza;
    }

    public function getNombreCliente()
    {
        return $this->nombreCliente;
    }

    public function setNombreCliente($nombreCliente)
    {
        $this->nombreCliente = $nombreCliente;
    }

    public function getFecha()
    {
        return $this->fecha;
    }

    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }

    public function getCantidad()
    {
        return $this->cantidad;
    }

    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;
    }

    public function getTipoUnidad()
    {
        return $this->tipoUnidad;
    }

    public function setTipoUnidad($tipoUnidad)
    {
        $this->tipoUnidad = $tipoUnidad;
    }


    //endregion Getters y Setters


    public function crearVenta()
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("INSERT INTO venta (idHortaliza, nombreCliente, fecha, cantidad, tipoUnidad,foto)
            VALUES (:idHortaliza, :nombreCliente, :fecha, :cantidad, :tipoUnidad,:foto)");
        $consulta->bindValue(':idHortaliza', $this->idHortaliza, PDO::PARAM_INT);
        $consulta->bindValue(':nombreCliente', $this->nombreCliente, PDO::PARAM_STR);
        $consulta->bindValue(':fecha', $this->fecha, PDO::PARAM_STR);
        $consulta->bindValue(':cantidad', $this->cantidad, PDO::PARAM_INT);
        $consulta->bindValue(':tipoUnidad', $this->tipoUnidad, PDO::PARAM_STR);
        $consulta->bindValue(':foto', $this->foto, PDO::PARAM_STR);
        $consulta->execute();
        return $objAccesoDatos->RetornarUltimoIdInsertado();
    }

    function AgregarFoto($foto, $cliente = "")
    {
        $destino = "./FotosHortalizas/";
        if (!file_exists($destino)) {
            mkdir($destino, 0777, true);
        }
        $fecha = new DateTime(date("d-m-Y"));
        $nombre = $cliente . "_" . $this->nombreCliente . "_" . date_format($fecha, 'Y-m-d') . "." . pathinfo($foto['foto']->getClientFilename(), PATHINFO_EXTENSION);;
        $foto['foto']->moveTo($destino . $nombre);
        $this->foto = $destino . $nombre;
    }

    public static function obtenerHortalizaPorClimaSecoYFechas($desde, $hasta)
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("select venta.id,venta.idHortaliza,venta.nombreCliente,venta.fecha,venta.cantidad,venta.tipoUnidad,venta.foto from venta inner join hortalizas on venta.idHortaliza = hortalizas.id 
where hortalizas.clima = 'seco' AND venta.fecha BETWEEN :desde AND :hasta
");
        $consulta->bindValue(':desde', $desde, PDO::PARAM_STR);
        $consulta->bindValue(':hasta', $hasta, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Venta");
    }

    //Traer todos los usuarios que compraron zanahoria (o cualquier otra, buscada por  nombre
    public static function obtenerVentasPorNombreHortaliza($nombre)
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("select usuarios.id,usuarios.usuario,usuarios.clave,usuarios.mail,usuarios.tipo from usuarios inner join venta on usuarios.usuario = venta.nombreCliente
	inner join hortalizas on hortalizas.id = venta.idHortaliza
	where hortalizas.nombre = :nombre");
        $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Usuario");
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM venta");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Venta');
    }

    //validarVenta Venta clave mail tipo
    public static function validarVenta($Venta)
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM Ventas WHERE Venta = :Venta ");
        $consulta->bindValue(':Venta', $Venta->Venta, PDO::PARAM_STR);
        $consulta->execute();
        $empleadoResultado = $consulta->fetchObject('Venta');
        return $Venta->Equals($empleadoResultado);
    }

    //equals comparar Venta clave mail tipo propiedad por propiedad
    private function Equals($Venta)
    {
        if ($this->Venta == $Venta->Venta && password_verify($this->clave, $Venta->clave) && $this->mail == $Venta->mail && $this->tipo == $Venta->tipo) {
            return true;
        }
        return false;
    }

    public static function obtenerPorUnidad($tipoUnidad)
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM Ventas WHERE tipoUnidad = :tipoUnidad");
        $consulta->bindValue(':tipoUnidad', $tipoUnidad, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Venta");
    }

    public static function obtenerPorClima($clima)
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM Ventas WHERE clima = :clima");
        $consulta->bindValue(':clima', $clima, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Venta");
    }

    public static function obtenerVenta($id)
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM Ventas WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchObject('Venta');
    }

    public static function obtenerVentaPorClimaSecoYFechas($desde, $hasta)
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM Ventas WHERE clima = 'seco' AND fecha BETWEEN :desde AND :hasta");
        $consulta->bindValue(':desde', $desde, PDO::PARAM_STR);
        $consulta->bindValue(':hasta', $hasta, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchObject('Venta');
    }

    public function modificarVenta($id)
    {
        $objAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDato->RetornarConsulta("UPDATE Ventas SET Venta = :Venta, clave = :clave, mail = :mail,tipo = :tipo  WHERE id = :id");
        $consulta->bindValue(':Venta', $this->Venta, PDO::PARAM_STR);
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
        $consulta->bindValue(':clave', $claveHash, PDO::PARAM_STR);
        $consulta->bindValue(':mail', $this->mail, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->rowCount();
    }

    public static function borrarVenta($id)
    {
        $objAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDato->RetornarConsulta("delete * from Ventas WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->rowCount();
    }
}
