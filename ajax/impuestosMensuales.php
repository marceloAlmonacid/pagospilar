<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');

if($_POST){
    require_once '../src/config.php';
    $conexion = conexion();

    if($_POST['action'] == 'buscar'){

        $mes = isset($_POST['mes']) ? mysqli_real_escape_string($conexion, $_POST['mes']) : date('m');
        $anio = isset($_POST['anio']) ? mysqli_real_escape_string($conexion, $_POST['anio']) : date('Y');

        $arr = array();
        // Filtramos por el mes y año de vencimiento
        $query_select = mysqli_query($conexion, "SELECT * FROM imp WHERE MONTH(fecha_vencimiento_imp) = '$mes' AND YEAR(fecha_vencimiento_imp) = '$anio' ORDER BY id_imp DESC");
        
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
