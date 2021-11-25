<?php

class Criptomoneda
{
    //variables publicas
    public $id;
    public $precio;
    public $nombre;
    public $foto;
    public $nacionalidad;
    public $estado;


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

    public function getNacionalidad()
    {
        return $this->nacionalidad;
    }       

    public function setNacionalidad($nacionalidad)
    {
        $this->nacionalidad = $nacionalidad;
    }       

    public function getEstado()
    {
        return $this->estado;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }



    //endregion 





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



    public function crearcriptomoneda()
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("INSERT INTO criptomoneda (nombre,precio,foto,nacionalidad,estado) VALUES (:nombre,:precio,:foto,:nacionalidad,:estado)");
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_STR);
        $consulta->bindValue(':foto', $this->foto, PDO::PARAM_STR);
        $consulta->bindValue(':nacionalidad', $this->nacionalidad, PDO::PARAM_STR);
        $consulta->bindValue(':estado',$this->estado , PDO::PARAM_STR);
        $consulta->execute();
        return $objAccesoDatos->RetornarUltimoIdInsertado();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM criptomoneda");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'criptomoneda');
    }

    //validarcriptomoneda criptomoneda clave mail tipo
    public static function validarcriptomoneda($criptomoneda)
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM criptomonedas WHERE criptomoneda = :criptomoneda ");
        $consulta->bindValue(':criptomoneda', $criptomoneda->criptomoneda, PDO::PARAM_STR);
        $consulta->execute();
        $empleadoResultado = $consulta->fetchObject('criptomoneda');
        return $criptomoneda->Equals($empleadoResultado);
    }

    //equals comparar criptomoneda clave mail tipo propiedad por propiedad
    private function Equals($criptomoneda)
    {
        if ($this->criptomoneda == $criptomoneda->criptomoneda &&  password_verify($this->clave, $criptomoneda->clave)  && $this->mail == $criptomoneda->mail  && $this->tipo == $criptomoneda->tipo) {
            return true;
        }
        return false;
    }

    public static function obtenerPorUnidad($tipoUnidad)
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM criptomonedas WHERE tipoUnidad = :tipoUnidad");
        $consulta->bindValue(':tipoUnidad', $tipoUnidad, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "criptomoneda");
    }

    public static function obtenerPorNacionalidad($nacionalidad)
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM criptomoneda WHERE nacionalidad = :nacionalidad");
        $consulta->bindValue(':nacionalidad', $nacionalidad, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "criptomoneda");
    }


    public static function obtenercriptomoneda($id)
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM criptomoneda WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchObject('criptomoneda');
    }

    public static function borrarcriptomoneda($id)
    {
        $objAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDato->RetornarConsulta("update criptomoneda  set estado='off' WHERE id = :id");
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
                /*
                echo "<br> originalDir :" . $originalDir . $file . "</br>";
                echo "<br> destination :" . $destination . $file . "</br>"; */
                rename($originalDir . $file, $destination . $file);
                $output = true;
                break;
            }
        }
        return $output;
    }


    //modificar una criptomoneda
    public function Modificarcriptomoneda()
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("update criptomoneda set nombre = :nombre, precio = :precio, foto = :foto, nacionalidad = :nacionalidad, estado = :estado where id = :id");
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_STR);
        $consulta->bindValue(':foto', $this->foto, PDO::PARAM_STR);
        $consulta->bindValue(':nacionalidad', $this->nacionalidad, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        return $consulta->execute();
    }
}
