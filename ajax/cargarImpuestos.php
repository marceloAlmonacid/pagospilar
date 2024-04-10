<?php
require_once '../src/config.php';
$con = conexion();

date_default_timezone_set('America/Argentina/Buenos_Aires');

$nombreImp = $con->real_escape_string(htmlentities($_POST['nombreImp']));
$proveedorImp = $con->real_escape_string(htmlentities($_POST['proveedorImp']));
$fechaVencimientoImp = $con->real_escape_string(htmlentities($_POST['fechaVencimientoImp']));
$costoImp = $con->real_escape_string(htmlentities($_POST['costoImp']));
$tipoImp = $con->real_escape_string(htmlentities($_POST['tipoImp']));
$file_name = $_FILES['facturaImp']['name'];

$usuariosSeleccionados = isset($_POST['usuariosSeleccionados']) ? $_POST['usuariosSeleccionados'] : [];
$numUsuarios = count($usuariosSeleccionados);
$costoPorUsuario = $numUsuarios > 0 ? round($costoImp / $numUsuarios, 2) : 0;

// Función para manejar la subida de archivos
function file_upload() {
    $file_name = $_FILES['facturaImp']['name'];
    $file_type = $_FILES['facturaImp']['type'];
    list($type, $extension) = explode('/', $file_type);
    if ($extension === 'pdf') {
        $dir = '../facturas/';
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        $file_tmp_name = $_FILES['facturaImp']['tmp_name'];
        $new_name_file = $dir . file_name($file_name) . '.' . $extension;
        if (move_uploaded_file($file_tmp_name, $new_name_file)) {
            return $new_name_file;
        }
    }
    return null;
}

// Verifica si se subió un archivo
$new_name_file = file_upload();

$sql = "INSERT INTO imp (nombre_imp, proveedor_imp, fecha_vencimiento_imp, costo_imp, tipo_imp, ruta_comprobante_imp) VALUES ('$nombreImp', '$proveedorImp', '$fechaVencimientoImp', '$costoImp', '$tipoImp', '$new_name_file')";
if ($con->query($sql)) {
    $idImp = $con->insert_id;

    foreach ($usuariosSeleccionados as $idUsuario) {
        $con->query("INSERT INTO usu_imp (id_usu1, id_imp1, monto) VALUES ('$idUsuario', '$idImp', '$costoPorUsuario')");
    }
    echo 'success';
} else {
    echo 'fail: ' . $con->error;
}

function file_name($string) {
    $string = strtolower($string);
    $find = array('á', 'é', 'í', 'ó', 'ú', 'ñ');
    $repl = array('a', 'e', 'i', 'o', 'u', 'n');
    $string = str_replace($find, $repl, $string);
    $find = array(' ', '&', '\r\n', '\n', '+');
    $string = str_replace($find, '-', $string);
    $find = array('/[^a-z0-9\-<>]/', '/[\-]+/', '/<[^>]*>/');
    $repl = array('', '-', '');
    $string = preg_replace($find, $repl, $string);
    return $string;
}



// require_once '../src/config.php';
// $con = conexion();

// date_default_timezone_set('America/Argentina/Buenos_Aires');



// $nombreImp = $con->real_escape_string(htmlentities($_POST['nombreImp']));
// $proveedorImp = $con->real_escape_string(htmlentities($_POST['proveedorImp']));
// $fechaVencimientoImp = $con->real_escape_string(htmlentities($_POST['fechaVencimientoImp']));
// $costoImp = $con->real_escape_string(htmlentities($_POST['costoImp']));
// $tipoImp = $con->real_escape_string(htmlentities($_POST['tipoImp']));
// $file_name = $_FILES['facturaImp']['name'];

// $usuariosSeleccionados = isset($_POST['usuariosSeleccionados']) ? $_POST['usuariosSeleccionados'] : [];
// $numUsuarios = count($usuariosSeleccionados);
// $costoPorUsuario = $numUsuarios > 0 ? round($costoImp / $numUsuarios, 2) : 0;


// echo $costoPorUsuario;

// if ($file_name == '' || $file_name == null) {


//     $ins1 = $con->query("INSERT INTO imp (nombre_imp, proveedor_imp, fecha_vencimiento_imp, costo_imp, tipo_imp) VALUES ('$nombreImp','$proveedorImp','$fechaVencimientoImp','$costoImp','$tipoImp')");

//     if ($ins1) {
//         $idImp = $con->insert_id;

//             foreach ($usuariosSeleccionados as $idUsuario) {
//                 $con->query("INSERT INTO usu_imp (id_usu1, id_imp1, monto) VALUES ('$idUsuario', '$idImp', '$costoPorUsuario')");
//             }

//         echo 'success';
//     } else {
//         echo 'fail';
//     }
// } else {

//     $new_name_file = null;
//     $file_type = $_FILES['facturaImp']['type'];
//     list($type, $extension) = explode('/', $file_type);
//     if ($extension == 'pdf') {
//         $dir = '../facturas/';
//         if (!file_exists($dir)) {
//             mkdir($dir, 0777, true);
//         }
//         $file_tmp_name = $_FILES['facturaImp']['tmp_name'];
//         $new_name_file = $dir . file_name($file_name) . '.' . $extension;
//         if (copy($file_tmp_name, $new_name_file)) {
//         }
//     }

//     move_uploaded_file($_FILES['file']['tmp_name'], $new_name_file);
//     $ins = $con->query("INSERT INTO imp (nombre_imp, proveedor_imp, fecha_vencimiento_imp, costo_imp, ruta_comprobante_imp, tipo_imp) VALUES ('$nombreImp','$proveedorImp','$fechaVencimientoImp','$costoImp','$new_name_file','$tipoImp')");

//     if ($ins) {

//         echo 'success';
//     } else {
//         echo 'fail';
//     }
// }


// function file_name($string)
// {

//     // Tranformamos todo a minusculas

//     $string = strtolower($string);

//     //Rememplazamos caracteres especiales latinos

//     $find = array('á', 'é', 'í', 'ó', 'ú', 'ñ');

//     $repl = array('a', 'e', 'i', 'o', 'u', 'n');

//     $string = str_replace($find, $repl, $string);

//     // Añadimos los guiones

//     $find = array(' ', '&', '\r\n', '\n', '+');
//     $string = str_replace($find, '-', $string);

//     // Eliminamos y Reemplazamos otros carácteres especiales

//     $find = array('/[^a-z0-9\-<>]/', '/[\-]+/', '/<[^>]*>/');

//     $repl = array('', '-', '');

//     $string = preg_replace($find, $repl, $string);

//     return $string;
// }
