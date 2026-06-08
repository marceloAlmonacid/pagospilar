<?php

require_once '../src/config.php';
$con = conexion();

date_default_timezone_set('America/Argentina/Buenos_Aires');

// Asegurarse de recibir datos JSON
$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

if ($contentType === "application/json") {
    // Recibir el contenido de la solicitud
    $content = trim(file_get_contents("php://input"));

    // Decodificar el JSON recibido en un array asociativo
    $data = json_decode($content, true);

    // Validar la presencia de los datos esenciales
    if (isset($data["usuarioId"]) && isset($data["impuestos"])) {
        $usuarioId = $data["usuarioId"];
        $impuestos = $data["impuestos"];
        $estado = isset($data["estado"]) ? filter_var($data["estado"], FILTER_VALIDATE_BOOLEAN) : true;
        $fechaActual  = date("Y-m-d H:i:s");

        // Iterar por los impuestos para procesar cada uno
        foreach ($impuestos as $impuesto) {
            $valor = $impuesto["valor"];

            if ($estado) {
                $con->query("UPDATE usu_imp SET estado_pago ='PAGADO', fecha_pago='$fechaActual' WHERE id_usu1 ='$usuarioId' AND id_imp1 ='$valor'");
            } else {
                $con->query("UPDATE usu_imp SET estado_pago = NULL, fecha_pago=NULL WHERE id_usu1 ='$usuarioId' AND id_imp1 ='$valor'");
            }
        }

        // También actualizamos la tabla global por compatibilidad heredada
        if ($estado) {
            $ins2 = $con->query("UPDATE usuarios SET pago ='SI' WHERE id_usuario ='$usuarioId'");
        } else {
            $ins2 = $con->query("UPDATE usuarios SET pago ='NO' WHERE id_usuario ='$usuarioId'");
        }

        echo json_encode(["success" => true, "message" => "Datos procesados correctamente"]);

    } else {
        echo json_encode(["success" => false, "message" => "Datos incompletos"]);
    }
} 
