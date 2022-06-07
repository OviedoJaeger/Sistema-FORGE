<?php

class Conexion{

    static public function conectar(){

        $link = new PDO("mysql:host=localhost;dbname=hirdcomm_demo",
                        "hirdcomm_admin",
                        "*************");

        $link->exec("set names utf8");

        return $link;

    }



}
