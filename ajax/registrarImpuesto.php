<?php

// require_once '../src/config.php';
// $con = conexion();

// date_default_timezone_set('America/Argentina/Buenos_Aires');

// // Asegúrate de que el método de la solicitud sea POST
// if ($_SERVER["REQUEST_METHOD"] == "POST") {
//     // Recibe los valores enviados desde el cliente
//     $id_imp = isset($_POST['id']) ? $_POST['id'] : '';
//     $nombre_imp = isset($_POST['nombre_imp']) ? $_POST['nombre_imp'] : '';
//     $proveedor_imp = isset($_POST['proveedor_imp']) ? $_POST['proveedor_imp'] : '';
//     $costo_imp = isset($_POST['costo_imp']) ? $_POST['costo_imp'] : '';
//     $ruta_comprobante_imp = isset($_POST['ruta_comprobante_imp']) ? $_POST['ruta_comprobante_imp'] : '';

//     // Prepara una consulta SQL para insertar los datos en la base
//     $query = "INSERT INTO historial (id_imp, nombre_imp_hist, proveedor_imp, costo_imp_hist, ruta_comprobante_imp) VALUES (?, ?, ?, ?, ?)";

//     // Prepara la sentencia
//     if ($stmt = $mysqli->prepare($query)) {
//         // Vincula los parámetros para marcadores
//         $stmt->bind_param("sssss", $id_imp, $nombre_imp, $proveedor_imp, $costo_imp, $ruta_comprobante_imp);

//         // Ejecuta la sentencia
//         if ($stmt->execute()) {
//             echo json_encode(array("status" => "success", "message" => "Impuesto registrado exitosamente."));
//         } else {
//             echo json_encode(array("status" => "error", "message" => "Error al registrar el impuesto."));
//         }

//         // Cierra la sentencia
//         $stmt->close();
//     } else {
//         echo json_encode(array("status" => "error", "message" => "Error al preparar la consulta."));
//     }

//     // Cierra la conexión
//     $mysqli->close();
// } else {
//     // No es un método POST, envía un error
//     echo json_encode(array("status" => "error", "message" => "Método de solicitud no válido."));
// }







// if ($file_name == '' || $file_name == null) {


//     $ins1 = $con->query("UPDATE imp set  nombre_imp ='$nombreImp', proveedor_imp='$proveedorImp', fecha_vencimiento_imp='$fechaVencimientoImp', costo_imp='$costoImp', tipo_imp='$tipoImp' where id_imp ='$idImp'");

//     if ($ins1) {

//         echo 'success';
//     } else {
//         echo 'fail';
//     }
// } else {
//     $new_name_file = null;

//     if ($file_name != '' || $file_name != null) {
//         $file_type = $_FILES['facturaImpEdit']['type'];
//         list($type, $extension) = explode('/', $file_type);
//         if ($extension == 'pdf') {
//             $dir = '../facturas/';
//             if (!file_exists($dir)) {
//                 mkdir($dir, 0777, true);
//             }
//             $file_tmp_name = $_FILES['facturaImpEdit']['tmp_name'];
//             $new_name_file = $dir . file_name($file_name) . '.' . $extension;
//             if (copy($file_tmp_name, $new_name_file)) {
//             }
//         }

//         move_uploaded_file($_FILES['facturaImpEdit']['tmp_name'], $new_name_file);

//         $sql3 = $con->query("UPDATE imp SET nombre_imp ='$nombreImp', proveedor_imp='$proveedorImp', fecha_vencimiento_imp='$fechaVencimientoImp', ruta_comprobante_imp='$new_name_file', costo_imp='$costoImp', tipo_imp='$tipoImp' where id_imp ='$idImp'");

//         if ($sql3) {
//             echo 'success';
//         } else {
//             echo 'fail';
//         }
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
