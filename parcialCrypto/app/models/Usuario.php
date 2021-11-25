<?php

class Usuario
{
    public $id;
    public $usuario;
    public $clave;
    public $mail;
    public $tipo;

    public function crearUsuario()
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("INSERT INTO usuarios (usuario, clave, mail,tipo) VALUES (:usuario, :clave, :mail, :tipo)");
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
        $consulta->bindValue(':clave', $claveHash);
        $consulta->bindValue(':mail', $this->mail, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
        $consulta->execute();
        return $objAccesoDatos->RetornarUltimoIdInsertado();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM usuarios");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    //validarusuario usuario clave mail tipo
    public static function validarUsuario($usuario)
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM usuarios WHERE usuario = :usuario ");
        $consulta->bindValue(':usuario', $usuario->usuario, PDO::PARAM_STR);
        $consulta->execute();
        $empleadoResultado = $consulta->fetchObject('Usuario');
        return $usuario->Equals($empleadoResultado);
    }

    //equals comparar usuario clave mail tipo propiedad por propiedad
    public function Equals($usuario)
    {
        if ($this->usuario == $usuario->usuario &&  password_verify($this->clave, $usuario->clave)  && $this->mail == $usuario->mail  && $this->tipo == $usuario->tipo) {
            return true;
        }
        return false;
    }



    public static function validarUsuarioYPassword($usuario, $clave)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select usuario,clave from usuarios where usuario=:usuario");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->execute();
        $empleadoResultado = $consulta->fetchObject('Usuario');
        return password_verify($clave, $empleadoResultado->clave);
    }

    public static function obtenerUsuario($usuario)
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM usuarios WHERE usuario = :usuario");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchObject('Usuario');
    }

    public  function modificarUsuario($id)
    {
        $objAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDato->RetornarConsulta("UPDATE usuarios SET usuario = :usuario, clave = :clave, mail = :mail,tipo = :tipo  WHERE id = :id");
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
        $consulta->bindValue(':clave', $claveHash, PDO::PARAM_STR);
        $consulta->bindValue(':mail', $this->mail, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->rowCount();
    }

    public static function borrarUsuario($id)
    {
        $objAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDato->RetornarConsulta("delete * from usuarios WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->rowCount();
    }
}
