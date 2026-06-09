<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');

if($_POST){
    require_once '../src/config.php';
    $conexion = conexion();

    // Acciones de envío de WhatsApp
    if($_POST['action'] == 'mensualidad'){
        $mes = isset($_POST['mes']) ? mysqli_real_escape_string($conexion, $_POST['mes']) : date('m');
        $anio = isset($_POST['anio']) ? mysqli_real_escape_string($conexion, $_POST['anio']) : date('Y');

        $mes_anterior = (int)$mes - 1;
        $anio_anterior = (int)$anio;
        if ($mes_anterior == 0) {
            $mes_anterior = 12;
            $anio_anterior--;
        }

        // Obtener datos del mes anterior para comparar (costo total y monto individual)
        $query_prev = mysqli_query($conexion, "SELECT usu_imp.id_usu1 as id_usuario, imp.nombre_imp, usu_imp.monto, imp.costo_imp FROM `usu_imp` 
        INNER JOIN imp ON usu_imp.id_imp1=imp.id_imp
        WHERE MONTH(imp.fecha_vencimiento_imp) = '$mes_anterior' AND YEAR(imp.fecha_vencimiento_imp) = '$anio_anterior'");
        
        $prev_data = array();
        if($query_prev) {
            while($rp = mysqli_fetch_assoc($query_prev)){
                $prev_data[$rp['id_usuario']][$rp['nombre_imp']] = array(
                    'monto' => $rp['monto'],
                    'costo_total' => $rp['costo_imp']
                );
            }
        }

        // Buscar usuarios y lo que deben
        $query_select = mysqli_query($conexion, "SELECT id_usuario, nombre_usuario, telefono, telefono2, nombre_imp, monto, imp.costo_imp, id_imp, usu_imp.estado_pago as pago FROM `usu_imp` 
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
                        'telefono2' => $row['telefono2'],
                        'total' => 0,
                        'pendientes' => array(),
                        'incrementos_totales' => array(),
                        'incrementos_individuales' => array()
                    );
                }
                
                $monto_actual = $row['monto'];
                $costo_total_actual = $row['costo_imp'];
                $nombre_imp = $row['nombre_imp'];
                $str_item = $nombre_imp . ' ($' . number_format($monto_actual, 2) . ')';

                // Calcular si hubo incremento
                if (isset($prev_data[$id][$nombre_imp])) {
                    $monto_previo = $prev_data[$id][$nombre_imp]['monto'];
                    $costo_total_previo = $prev_data[$id][$nombre_imp]['costo_total'];
                    
                    if ($monto_actual > $monto_previo) {
                        $dif_individual = $monto_actual - $monto_previo;
                        $dif_total = $costo_total_actual - $costo_total_previo;
                        $porcentaje = ($costo_total_previo > 0) ? round(($dif_total / $costo_total_previo) * 100) : 100;
                        
                        $usuarios[$id]['incrementos_totales'][] = "*$nombre_imp:* de $" . number_format($costo_total_previo, 2) . " a $" . number_format($costo_total_actual, 2) . " (+$porcentaje%)";
                        $usuarios[$id]['incrementos_individuales'][] = "*$nombre_imp:* +$" . number_format($dif_individual, 2);
                    }
                }

                if($row['pago'] != '1' && $row['pago'] != 'true' && $row['pago'] != 'PAGADO' && $row['pago'] != 'SI'){
                    $usuarios[$id]['total'] += $monto_actual;
                    $usuarios[$id]['pendientes'][] = $str_item;
                }
            }

            $enviadosWhatsApp = 0;
            $erroresWhatsApp = 0;

            foreach($usuarios as $u){
                if($u['total'] > 0){
                    $mensaje = "¡Hola *" . $u['nombre'] . "*!\n";
                    $mensaje .= "Es momento de pagar los gastos de tu casa.\n\n";
                    $mensaje .= "Tu total es: *$" . number_format($u['total'], 2) . "*\n\n";
                    $mensaje .= "Detalle:\n- " . implode("\n- ", $u['pendientes']) . "\n\n";
                    
                    if (!empty($u['incrementos_totales'])) {
                        $mensaje .= "⚠️ *Aumentos en las facturas:*\n- " . implode("\n- ", $u['incrementos_totales']) . "\n\n";
                    }
                    if (!empty($u['incrementos_individuales'])) {
                        $mensaje .= "⚠️ *Aumentos en lo que pagas:*\n- " . implode("\n- ", $u['incrementos_individuales']) . "\n\n";
                    }
                    
                    $mensaje .= "Alias para transferir:\n*gira.barro*\n\n";
                    $mensaje .= "Gracias! ❤️\n\n_Mensaje generado por la App Pagos Pilar_\nhttps://pagospilar.dpdns.org";

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

        $mes_anterior = (int)$mes - 1;
        $anio_anterior = (int)$anio;
        if ($mes_anterior == 0) {
            $mes_anterior = 12;
            $anio_anterior--;
        }

        // Obtener datos del mes anterior para comparar
        $query_prev = mysqli_query($conexion, "SELECT imp.nombre_imp, usu_imp.monto, imp.costo_imp FROM `usu_imp` 
        INNER JOIN imp ON usu_imp.id_imp1=imp.id_imp 
        WHERE MONTH(imp.fecha_vencimiento_imp) = '$mes_anterior' AND YEAR(imp.fecha_vencimiento_imp) = '$anio_anterior' AND usu_imp.id_usu1 = '$id_usuario'");
        
        $prev_data = array();
        if($query_prev) {
            while($rp = mysqli_fetch_assoc($query_prev)){
                $prev_data[$rp['nombre_imp']] = array(
                    'monto' => $rp['monto'],
                    'costo_total' => $rp['costo_imp']
                );
            }
        }

        $query_select = mysqli_query($conexion, "SELECT id_usuario, nombre_usuario, telefono, telefono2, nombre_imp, monto, imp.costo_imp, usu_imp.estado_pago as pago FROM `usu_imp` 
        INNER JOIN usuarios ON usu_imp.id_usu1=usuarios.id_usuario
        INNER JOIN imp ON usu_imp.id_imp1=imp.id_imp
        WHERE MONTH(imp.fecha_vencimiento_imp) = '$mes' AND YEAR(imp.fecha_vencimiento_imp) = '$anio' AND id_usuario = '$id_usuario'");
        
        if($query_select && mysqli_num_rows($query_select) > 0){
            $total = 0;
            $pendientes = array();
            $incrementos_totales = array();
            $incrementos_individuales = array();
            $nombre = "";
            $telefono = "";
            $telefono2 = "";
            
            while ($row = mysqli_fetch_assoc($query_select)){
                $nombre = $row['nombre_usuario'];
                $telefono = $row['telefono'];
                $telefono2 = $row['telefono2'];
                
                $monto_actual = $row['monto'];
                $costo_total_actual = $row['costo_imp'];
                $nombre_imp = $row['nombre_imp'];
                $str_item = $nombre_imp . ' ($' . number_format($monto_actual, 2) . ')';

                if (isset($prev_data[$nombre_imp])) {
                    $monto_previo = $prev_data[$nombre_imp]['monto'];
                    $costo_total_previo = $prev_data[$nombre_imp]['costo_total'];
                    
                    if ($monto_actual > $monto_previo) {
                        $dif_individual = $monto_actual - $monto_previo;
                        $dif_total = $costo_total_actual - $costo_total_previo;
                        $porcentaje = ($costo_total_previo > 0) ? round(($dif_total / $costo_total_previo) * 100) : 100;
                        
                        $incrementos_totales[] = "*$nombre_imp:* de $" . number_format($costo_total_previo, 2) . " a $" . number_format($costo_total_actual, 2) . " (+$porcentaje%)";
                        $incrementos_individuales[] = "*$nombre_imp:* +$" . number_format($dif_individual, 2);
                    }
                }

                if($row['pago'] != '1' && $row['pago'] != 'true' && $row['pago'] != 'PAGADO' && $row['pago'] != 'SI'){
                    $total += $monto_actual;
                    $pendientes[] = $str_item;
                }
            }
            
            if($total > 0){
                $mensaje = "¡Hola *" . $nombre . "*!\n";
                $mensaje .= "Es momento de pagar los gastos de tu casa.\n\n";
                $mensaje .= "Tu total es: *$" . number_format($total, 2) . "*\n\n";
                $mensaje .= "Detalle:\n- " . implode("\n- ", $pendientes) . "\n\n";
                
                if (!empty($incrementos_totales)) {
                    $mensaje .= "⚠️ *Aumentos en las facturas:*\n- " . implode("\n- ", $incrementos_totales) . "\n\n";
                }
                if (!empty($incrementos_individuales)) {
                    $mensaje .= "⚠️ *Aumentos en lo que pagas:*\n- " . implode("\n- ", $incrementos_individuales) . "\n\n";
                }

                $mensaje .= "Alias para transferir:\n*gira.barro*\n\n";
                $mensaje .= "Gracias! ❤️\n\n_Mensaje generado por la App Pagos Pilar_\nhttps://pagospilar.dpdns.org";
                
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
