<?php
require_once '../src/config.php';
$con = conexion();

date_default_timezone_set('America/Argentina/Buenos_Aires');

$nombreUsu = $con->real_escape_string(htmlentities($_POST['nombreUsu']));



$ins1 = $con->query("INSERT INTO usuarios (nombre_usuario) VALUES ('$nombreUsu')");

if ($ins1) {

    echo 'success';
} else {
    echo 'fail';
}
