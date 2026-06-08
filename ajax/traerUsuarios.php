<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');

if($_POST){
    require_once '../src/config.php';
    $conexion = conexion();

    if($_POST['action'] == 'buscar'){

        $mes = isset($_POST['mes']) ? mysqli_real_escape_string($conexion, $_POST['mes']) : date('m');
        $anio = isset($_POST['anio']) ? mysqli_real_escape_string($conexion, $_POST['anio']) : date('Y');

        $arr = array();
        $query_select = mysqli_query($conexion, "SELECT id_usuario, nombre_usuario, telefono, telefono2, telegram_id, nombre_imp, monto, id_imp, usu_imp.estado_pago as pago FROM `usu_imp` 
        INNER JOIN usuarios ON usu_imp.id_usu1=usuarios.id_usuario
        INNER JOIN imp ON usu_imp.id_imp1=imp.id_imp
        WHERE MONTH(imp.fecha_vencimiento_imp) = '$mes' AND YEAR(imp.fecha_vencimiento_imp) = '$anio'");
        if($query_select){
            $num_rows = mysqli_num_rows($query_select);
            if($num_rows > 0)
            {
                while ($datos=mysqli_fetch_assoc($query_select)){
                    $arr[]=$datos;
                }
                echo json_encode($arr, JSON_UNESCAPED_UNICODE);
            }else{
                echo json_encode(array("status" => "notData"));
            }
        }else{
            echo json_encode(array("status" => "error", "message" => "Error en la consulta SQL"));
        }
        
    }
} else {
    echo json_encode(array("status" => "error", "message" => "No se recibió ninguna acción POST válida"));
}
