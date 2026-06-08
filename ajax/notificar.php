<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');

if($_POST){
    require_once '../src/config.php';
    $conexion = conexion();

    // Acciones de envío de WhatsApp
    if($_POST['action'] == 'mensualidad'){
        $mes = isset($_POST['mes']) ? mysqli_real_escape_string($conexion, $_POST['mes']) : date('m');
        $anio = isset($_POST['anio']) ? mysqli_real_escape_string($conexion, $_POST['anio']) : date('Y');

        // Buscar usuarios y lo que deben
        $query_select = mysqli_query($conexion, "SELECT id_usuario, nombre_usuario, telefono, nombre_imp, monto, id_imp, usu_imp.estado_pago as pago FROM `usu_imp` 
        INNER JOIN usuarios ON usu_imp.id_usu1=usuarios.id_usuario
        INNER JOIN imp ON usu_imp.id_imp1=imp.id_imp
        WHERE MONTH(imp.fecha_vencimiento_imp) = '$mes' AND YEAR(imp.fecha_vencimiento_imp) = '$anio'");
        
        if($query_select && mysqli_num_rows($query_select) > 0){
            $usuarios = array();
            while ($row = mysqli_fetch_assoc($query_select)){
                $id = $row['id_usuario'];
                if(!isset($usuarios[$id])){
                    $usuarios[$id] = array(
                        'nombre' => $row['nombre_usuario'],
                        'telefono' => $row['telefono'],
                        'total' => 0,
                        'pendientes' => array()
                    );
                }
                if($row['pago'] != '1' && $row['pago'] != 'true' && $row['pago'] != 'PAGADO' && $row['pago'] != 'SI'){
                    $usuarios[$id]['total'] += $row['monto'];
                    $usuarios[$id]['pendientes'][] = $row['nombre_imp'] . ' ($' . number_format($row['monto'], 2) . ')';
                }
            }

            $enviadosWhatsApp = 0;
            $erroresWhatsApp = 0;

            foreach($usuarios as $u){
                if($u['total'] > 0){
                    $mensaje = "¡Buenas buenas, *" . $u['nombre'] . "*! 👋\n";
                    $mensaje .= "Llegó ese hermoso momento del mes... ¡Pagar las cuentas de la casa! 💸🏠\n\n";
                    $mensaje .= "Esta vez te toca poner: *$" . number_format($u['total'], 2) . "*\n\n";
                    $mensaje .= "El detalle de los daños 📄:\n- " . implode("\n- ", $u['pendientes']) . "\n\n";
                    $mensaje .= "Aflojá la billetera cuando puedas así liquidamos todo rápido. ¡Gracias, familia! ❤️";

                    if(enviarWhatsApp($u['telefono'], $mensaje)){
                        $enviadosWhatsApp++;
                    } else {
                        $erroresWhatsApp++;
                    }

                    // Enviar al teléfono 2 si lo tiene
                    if(!empty($u['telefono2'])){
                        enviarWhatsApp($u['telefono2'], $mensaje);
                    }
                }
            }

            echo json_encode(array(
                "status" => "success", 
                "message" => "Notificaciones enviadas.<br>WhatsApp: $enviadosWhatsApp correctos, $erroresWhatsApp fallidos.",
                "whatsapp" => array()
            ));
        }else{
            echo json_encode(array("status" => "error", "message" => "No hay gastos pendientes en este mes."));
        }
    }
    
    // Notificar deuda a un usuario individual
    if($_POST['action'] == 'mensualidad_individual'){
        $id_usuario = mysqli_real_escape_string($conexion, $_POST['id_usuario']);
        $mes = isset($_POST['mes']) ? mysqli_real_escape_string($conexion, $_POST['mes']) : date('m');
        $anio = isset($_POST['anio']) ? mysqli_real_escape_string($conexion, $_POST['anio']) : date('Y');

        $query_select = mysqli_query($conexion, "SELECT id_usuario, nombre_usuario, telefono, telefono2, nombre_imp, monto, usu_imp.estado_pago as pago FROM `usu_imp` 
        INNER JOIN usuarios ON usu_imp.id_usu1=usuarios.id_usuario
        INNER JOIN imp ON usu_imp.id_imp1=imp.id_imp
        WHERE MONTH(imp.fecha_vencimiento_imp) = '$mes' AND YEAR(imp.fecha_vencimiento_imp) = '$anio' AND id_usuario = '$id_usuario'");
        
        if($query_select && mysqli_num_rows($query_select) > 0){
            $total = 0;
            $pendientes = array();
            $nombre = "";
            $telefono = "";
            $telefono2 = "";
            
            while ($row = mysqli_fetch_assoc($query_select)){
                $nombre = $row['nombre_usuario'];
                $telefono = $row['telefono'];
                $telefono2 = $row['telefono2'];
                if($row['pago'] != '1' && $row['pago'] != 'true' && $row['pago'] != 'PAGADO' && $row['pago'] != 'SI'){
                    $total += $row['monto'];
                    $pendientes[] = $row['nombre_imp'] . ' ($' . number_format($row['monto'], 2) . ')';
                }
            }
            
            if($total > 0){
                $mensaje = "Hola! 👀👋 Paso a recordarte (antes de mandar a los cobradores 🕵️‍♂️) los gastos de la casa.\n\n";
                $mensaje .= "El saldo pendiente es de: *$" . number_format($total, 2) . "*\n\n";
                $mensaje .= "Te paso los detalles:\n\n";
                $mensaje .= implode("\n", $pendientes) . "\n\n";
                $mensaje .= "Transferí los pesitos cuando puedas así quedamos al dia.. Abrazo! 🫂";
                
                $enviado = false;
                if(enviarWhatsApp($telefono, $mensaje)){
                    $enviado = true;
                }
                if(!empty($telefono2) && enviarWhatsApp($telefono2, $mensaje)){
                    $enviado = true;
                }

                if($enviado){
                    echo json_encode(array("status" => "success", "message" => "Mensaje enviado a $nombre con éxito."));
                } else {
                    echo json_encode(array("status" => "error", "message" => "Error al enviar el mensaje de WhatsApp."));
                }
            } else {
                echo json_encode(array("status" => "error", "message" => "El usuario no tiene deuda pendiente en este mes."));
            }
        }
    }

    // Notificar pago confirmado a un usuario individual
    if($_POST['action'] == 'pago_confirmado'){
        $id_usuario = mysqli_real_escape_string($conexion, $_POST['id_usuario']);
        $mes = isset($_POST['mes']) ? mysqli_real_escape_string($conexion, $_POST['mes']) : date('m');
        $anio = isset($_POST['anio']) ? mysqli_real_escape_string($conexion, $_POST['anio']) : date('Y');

        $query = mysqli_query($conexion, "SELECT nombre_usuario, telefono, telefono2 FROM usuarios WHERE id_usuario = '$id_usuario' LIMIT 1");
        if($query && mysqli_num_rows($query) > 0){
            $row = mysqli_fetch_assoc($query);
            $nombre = $row['nombre_usuario'];
            $telefono = $row['telefono'];
            $telefono2 = $row['telefono2'];
            
            $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
            $nombre_mes = $meses[intval($mes) - 1];

            $mensaje = "¡Eesaaaa, *" . $nombre . "*! 🥳✅\n\n";
            $mensaje .= "Acaba de impactar tu pago de los gastos de *$nombre_mes $anio*.\n\n";
            $mensaje .= "¡Muchas gracias por mantenerte al día y ser el orgullo de la familia! 😂🙌❤️";
            
            $enviado = false;
            if(enviarWhatsApp($telefono, $mensaje)){
                $enviado = true;
            }
            if(!empty($telefono2) && enviarWhatsApp($telefono2, $mensaje)){
                $enviado = true;
            }

            if($enviado){
                echo json_encode(array("status" => "success", "message" => "Mensaje de confirmación de pago enviado con éxito."));
            } else {
                echo json_encode(array("status" => "error", "message" => "Error al enviar el mensaje de confirmación de pago."));
            }
        }
    }
}

// Función auxiliar para enviar WhatsApp usando el microservicio
function enviarWhatsApp($telefono, $mensaje){
    if(empty($telefono)) return false;
    
    $tel = preg_replace('/[^0-9]/', '', $telefono);
    if(strlen($tel) == 10){
        $tel = '549' . $tel; // Formato AR
    }
    
    $wa_data = json_encode(array(
        'number' => $tel,
        'message' => $mensaje
    ));

    $wa_options = array(
        'http' => array(
            'method'  => 'POST',
            'content' => $wa_data,
            'header'=>  "Content-Type: application/json\r\n" .
                        "Accept: application/json\r\n"
        )
    );
    
    $wa_context  = stream_context_create($wa_options);
    $wa_result = @file_get_contents('http://whatsapp_bot_imp:3000/send', false, $wa_context);
    
    if($wa_result){
        $response_wa = json_decode($wa_result, true);
        if(isset($response_wa['success']) && $response_wa['success'] == true) {
            return true;
        }
    }
    return false;
}
?>
