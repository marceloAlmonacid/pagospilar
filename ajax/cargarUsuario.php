<?php
require_once '../src/config.php';
$con = conexion();

date_default_timezone_set('America/Argentina/Buenos_Aires');

$nombreUsu = $con->real_escape_string(htmlentities($_POST['nombreUsu']));



$ins1 = $con->query("INSERT INTO usuarios (nombre_usuario) VALUES ('$nombreUsu')");

if ($ins1) {

    // //TRAEMOS EL ID DEL REGISTRO QUE ACABAMOS DE INGRESAR
    // $sql1 = "SELECT id_usuario from usuarios ORDER BY id_usuario ASC";
    // $result1 = mysqli_query($con, $sql1);
    // while ($ver = mysqli_fetch_row($result1)) {
    //     $idUsu = $ver[0];
    // }

    // $ins2 = $con->query("INSERT INTO usu_imp (nombre_usuario) VALUES ('$nombreUsu')");


    echo 'success';
} else {
    echo 'fail';
}
