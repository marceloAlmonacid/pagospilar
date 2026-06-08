<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');

if($_POST){
    require_once '../src/config.php';
    $conexion=conexion();

    if($_POST['action'] == 'buscar'){

            $arr = array();
            $id = intval($_POST['id']);
            $query_select = mysqli_query($conexion, " SELECT * FROM usuarios");
            $num_rows = mysqli_num_rows($query_select);
            if($num_rows > 0)
            {
                // Obtenemos la consulta y lo almacenamos en un array
                while ($datos=mysqli_fetch_assoc($query_select)){
                    $arr[]=$datos;
                }

                // convertimos a formato JSON y lo pasamos por un echo
                echo json_encode($arr,JSON_UNESCAPED_UNICODE);
            }else{
                echo "notData";
            }
        }

}

?>