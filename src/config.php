<?php


    function conexion(){

        $dbHost = getenv('DB_HOST') ?: 'localhost';
        $dbUser = getenv('DB_USER') ?: 'root';
        $dbPass = getenv('DB_PASS') ?: 'IT2017petro';
        $dbName = getenv('DB_NAME') ?: 'impuestos';

        $conexion = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);

        if (!mysqli_set_charset($conexion, "utf8")) {
            printf("Error cargando el conjunto de caracteres utf8: %s\n", mysqli_error($conexion));
            exit();
        } 

        return $conexion;
    }



?>