<?php
require_once '../src/config.php';
$conexion = conexion();

// Recibir el ID enviado desde el AJAX
$id = isset($_POST['id']) ? $_POST['id'] : null;

	$sql1="DELETE from imp where id_imp='$id'";
	$result1=mysqli_query($conexion,$sql1);



	echo $result1;
?>
