<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');

if($_POST){
    require_once '../src/config.php';
    $conexion = conexion();

    if($_POST['action'] == 'buscar'){

        $arr = array();
        $query_select = mysqli_query($conexion, "SELECT * from imp ORDER BY id_imp DESC");
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
