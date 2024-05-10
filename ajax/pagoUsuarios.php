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
        // Aquí procesas la información recibida, como por ejemplo:
        $usuarioId = $data["usuarioId"];
        $impuestos = $data["impuestos"];
        $fechaActual  = date("Y-m-d H:i:s");

        // Iterar por los impuestos para procesar cada uno
        foreach ($impuestos as $impuesto) {
            $impuestoId = $impuesto["id"];
            $valor = $impuesto["valor"];

            // Aquí deberías agregar la lógica de almacenamiento o procesamiento en tu base de datos.
            $ins1 = $con->query("UPDATE usu_imp SET  estado_pago ='PAGADO', fecha_pago='$fechaActual' WHERE id_usu1 ='$usuarioId' AND id_imp1 ='$valor'");

            
        }

        

            $ins2 = $con->query("UPDATE usuarios SET  pago ='SI' WHERE id_usuario ='$usuarioId'");

            if($ins2){
                echo json_encode(["success" => true, "message" => "Datos procesados correctamente"]);
            }

        
        
    } else {
        echo json_encode(["success" => false, "message" => "Datos incompletos"]);
    }
} 
