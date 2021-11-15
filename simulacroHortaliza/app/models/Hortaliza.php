<?php

class Hortaliza
{
    public $id;
    public $precio;
    public $nombre;
    public $clima;
    public $foto;
    public $tipoUnidad;

    //region Getters y Setters
    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }
    public function getPrecio()
    {
        return $this->precio;
    }
    public function setPrecio($precio)
    {
        $this->precio = $precio;
    }
    public function getNombre()
    {
        return $this->nombre;
    }
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }
    public function getFoto()
    {
        return $this->foto;
    }
    public function setFoto($foto)
    {
        $this->foto = $foto;
    }
    public function getClima()
    {
        return $this->clima;
    }
    public function setClima($clima)
    {
        $this->clima = $clima;
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


    function AgregarFoto($foto)
    {
        $destino = "./models/Fotos/";
        if (!file_exists($destino)) {
            mkdir($destino, 0777, true);
        }
        $fecha = new DateTime(date("d-m-Y"));
        $nombre = $this->nombre . "_" . date_format($fecha, 'Y-m-d') . "." . pathinfo($foto['foto']->getClientFilename(), PATHINFO_EXTENSION);;
        $foto['foto']->moveTo($destino . $nombre);
        $this->foto = $destino . $nombre;
    }



    public function crearHortaliza()
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("INSERT INTO hortalizas (precio, nombre, clima, tipoUnidad,foto) VALUES (:precio, :nombre, :clima, :tipoUnidad,:foto)");
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_STR);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':clima', $this->clima, PDO::PARAM_STR);
        $consulta->bindValue(':tipoUnidad', $this->tipoUnidad, PDO::PARAM_STR);
        $consulta->bindValue(':foto', $this->foto, PDO::PARAM_STR);
        $consulta->execute();
        return $objAccesoDatos->RetornarUltimoIdInsertado();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM Hortalizas");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Hortaliza');
    }

    //validarHortaliza Hortaliza clave mail tipo
    public static function validarHortaliza($Hortaliza)
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM Hortalizas WHERE Hortaliza = :Hortaliza ");
        $consulta->bindValue(':Hortaliza', $Hortaliza->Hortaliza, PDO::PARAM_STR);
        $consulta->execute();
        $empleadoResultado = $consulta->fetchObject('Hortaliza');
        return $Hortaliza->Equals($empleadoResultado);
    }

    //equals comparar Hortaliza clave mail tipo propiedad por propiedad
    private function Equals($Hortaliza)
    {
        if ($this->Hortaliza == $Hortaliza->Hortaliza &&  password_verify($this->clave, $Hortaliza->clave)  && $this->mail == $Hortaliza->mail  && $this->tipo == $Hortaliza->tipo) {
            return true;
        }
        return false;
    }

    public static function obtenerPorUnidad($tipoUnidad)
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM hortalizas WHERE tipoUnidad = :tipoUnidad");
        $consulta->bindValue(':tipoUnidad', $tipoUnidad, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Hortaliza");
    }

    public static function obtenerPorClima($clima)
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM hortalizas WHERE clima = :clima");
        $consulta->bindValue(':clima', $clima, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Hortaliza");
    }


    public static function obtenerHortaliza($id)
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM hortalizas WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchObject('Hortaliza');
    }

    public static function borrarHortaliza($id)
    {
        $objAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDato->RetornarConsulta("delete  from hortalizas WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->rowCount();
    }

    //mover una imagen a backup
    public function MoverABackUP()
    {
        $files = scandir(".\models\Fotos\\"); // es un array.
        $originalDir = ".\models\Fotos\\";    // si no lo tengo no puedo copiar la imagen
        $destination = ".\models\BackUp\\";
        if (!file_exists($destination)) {
            mkdir($destination, 0777, true);
        }
        $fileToFind = explode("/", $this->foto);
        $output = false;

        foreach ($files as $file) {
            if ($file == $fileToFind[3]) {
                /*                 echo "<br> originalDir :" . $originalDir . $file . "</br>";
                echo "<br> destination :" . $destination . $file . "</br>"; */
                rename($originalDir . $file, $destination . $file);
                $output = true;
                break;
            }
        }
        return $output;
    }


    //modificar una hortaliza
    public function ModificarHortaliza()
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("UPDATE hortalizas SET precio = :precio, nombre = :nombre, clima = :clima, tipoUnidad = :tipoUnidad, foto = :foto WHERE id = :id");
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_STR);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':clima', $this->clima, PDO::PARAM_STR);
        $consulta->bindValue(':tipoUnidad', $this->tipoUnidad, PDO::PARAM_STR);
        $consulta->bindValue(':foto', $this->foto, PDO::PARAM_STR);
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        return $consulta->execute();
    }
}
