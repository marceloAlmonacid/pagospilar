<?php

require_once '../src/config.php';
$con = conexion();

date_default_timezone_set('America/Argentina/Buenos_Aires');

$idImp = $con->real_escape_string(htmlentities($_POST['idImpEdit']));
$nombreImp = $con->real_escape_string(htmlentities($_POST['nombreImpEdit']));
$proveedorImp = $con->real_escape_string(htmlentities($_POST['proveedorImpEdit']));
$tipoImp = $con->real_escape_string(htmlentities($_POST['tipoImpEdit']));
$fechaVencimientoImp = $con->real_escape_string(htmlentities($_POST['fechaVencimientoImpEdit']));
$costoImp = $con->real_escape_string(htmlentities($_POST['costoImpEdit']));
$tieneActa = $con->real_escape_string(htmlentities($_POST['tiene_acta']));


$file_name = $_FILES['facturaImpEdit']['name'];


if ($file_name == '' || $file_name == null) {


    $ins1 = $con->query("UPDATE imp set  nombre_imp ='$nombreImp', proveedor_imp='$proveedorImp', fecha_vencimiento_imp='$fechaVencimientoImp', costo_imp='$costoImp', tipo_imp='$tipoImp' where id_imp ='$idImp'");

    if ($ins1) {

        echo 'success';
    } else {
        echo 'fail';
    }
} else {
    $new_name_file = null;

    if ($file_name != '' || $file_name != null) {
        $file_type = $_FILES['facturaImpEdit']['type'];
        list($type, $extension) = explode('/', $file_type);
        if ($extension == 'pdf') {
            $dir = '../facturas/';
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }
            $file_tmp_name = $_FILES['facturaImpEdit']['tmp_name'];
            $new_name_file = $dir . file_name($file_name) . '.' . $extension;
            if (copy($file_tmp_name, $new_name_file)) {
            }
        }

        move_uploaded_file($_FILES['facturaImpEdit']['tmp_name'], $new_name_file);

        $sql3 = $con->query("UPDATE imp SET nombre_imp ='$nombreImp', proveedor_imp='$proveedorImp', fecha_vencimiento_imp='$fechaVencimientoImp', ruta_comprobante_imp='$new_name_file', costo_imp='$costoImp', tipo_imp='$tipoImp' where id_imp ='$idImp'");

        if ($sql3) {
            echo 'success';
        } else {
            echo 'fail';
        }
    }
}



function file_name($string)
{

    // Tranformamos todo a minusculas

    $string = strtolower($string);

    //Rememplazamos caracteres especiales latinos

    $find = array('á', 'é', 'í', 'ó', 'ú', 'ñ');

    $repl = array('a', 'e', 'i', 'o', 'u', 'n');

    $string = str_replace($find, $repl, $string);

    // Añadimos los guiones

    $find = array(' ', '&', '\r\n', '\n', '+');
    $string = str_replace($find, '-', $string);

    // Eliminamos y Reemplazamos otros carácteres especiales

    $find = array('/[^a-z0-9\-<>]/', '/[\-]+/', '/<[^>]*>/');

    $repl = array('', '-', '');

    $string = preg_replace($find, $repl, $string);

    return $string;
}
