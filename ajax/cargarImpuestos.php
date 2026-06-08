<?php
require_once '../src/config.php';
$con = conexion();

date_default_timezone_set('America/Argentina/Buenos_Aires');

$nombreImp = $con->real_escape_string(htmlentities($_POST['nombreImp']));
$proveedorImp = $con->real_escape_string(htmlentities($_POST['proveedorImp']));
$fechaVencimientoImp = $con->real_escape_string(htmlentities($_POST['fechaVencimientoImp']));
$costoImp = $con->real_escape_string(htmlentities($_POST['costoImp']));
$tipoImp = $con->real_escape_string(htmlentities($_POST['tipoImp']));

$usuariosSeleccionados = isset($_POST['usuariosSeleccionados']) ? $_POST['usuariosSeleccionados'] : [];
$numUsuarios = count($usuariosSeleccionados);
$costoPorUsuario = $numUsuarios > 0 ? round($costoImp / $numUsuarios, 2) : 0;

// Función para manejar la subida de archivos (Soporta PDF y Fotos)
function file_upload() {
    if(!isset($_FILES['facturaImp']) || $_FILES['facturaImp']['error'] == UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($_FILES['facturaImp']['error'] !== UPLOAD_ERR_OK) {
        // Retornar un mensaje de error especial si hubo un problema al subir (ej: supera tamaño)
        return "ERROR_UPLOAD_" . $_FILES['facturaImp']['error'];
    }

    $file_name = $_FILES['facturaImp']['name'];
    
    // Lista de extensiones permitidas
    $allowed_extensions = array('pdf', 'jpg', 'jpeg', 'png');
    $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if (in_array($file_extension, $allowed_extensions)) {
        $dir = '../facturas/';
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        $file_tmp_name = $_FILES['facturaImp']['tmp_name'];
        // Generar nombre unico para no sobreescribir
        $new_name_file = $dir . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
        
        if (move_uploaded_file($file_tmp_name, $new_name_file)) {
            // Devolver la ruta relativa sin el '../' para la BD, o como estaba antes
            return substr($new_name_file, 3); 
        } else {
            return "ERROR_MOVE";
        }
    } else {
        return "ERROR_EXTENSION";
    }
}

// Verifica si se subió un archivo
$new_name_file = file_upload();

if (strpos($new_name_file, 'ERROR_') === 0) {
    echo "Error al subir comprobante: " . $new_name_file;
    exit;
}

$sql = "INSERT INTO imp (nombre_imp, proveedor_imp, fecha_vencimiento_imp, costo_imp, tipo_imp, ruta_comprobante_imp) VALUES ('$nombreImp', '$proveedorImp', '$fechaVencimientoImp', '$costoImp', '$tipoImp', '$new_name_file')";
if ($con->query($sql)) {
    $idImp = $con->insert_id;

    foreach ($usuariosSeleccionados as $idUsuario) {
        $con->query("INSERT INTO usu_imp (id_usu1, id_imp1, monto) VALUES ('$idUsuario', '$idImp', '$costoPorUsuario')");
    }
    echo 'success';
} else {
    echo "Error: " . $sql . "<br>" . $con->error;
}
?>

