<?php
require_once '../src/config.php';
$con = conexion();

$id_usuario = isset($_POST['id_usuario']) ? intval($_POST['id_usuario']) : 0;
$nombreUsu = $con->real_escape_string(htmlentities($_POST['nombreUsu']));
$telefono = isset($_POST['telefonoUsu']) ? $con->real_escape_string($_POST['telefonoUsu']) : '';
$telefono2 = isset($_POST['telefonoUsu2']) ? $con->real_escape_string($_POST['telefonoUsu2']) : '';

if ($id_usuario > 0) {
    $upd = $con->query("UPDATE usuarios SET nombre_usuario = '$nombreUsu', telefono = '$telefono', telefono2 = '$telefono2' WHERE id_usuario = $id_usuario");
    if ($upd) {
        echo 'success';
    } else {
        echo 'fail';
    }
} else {
    echo 'fail';
}
?>
