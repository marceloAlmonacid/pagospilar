<?php

date_default_timezone_set('America/Argentina/Buenos_Aires');

if($_POST){
    require_once '../src/config.php';
    $conexion=conexion();

    if($_POST['action'] == 'buscar'){
        if(!empty($_POST['id']))
        {
            $arr = array();
            $id = intval($_POST['id']);
            $query_select = mysqli_query($conexion, 
            "SELECT 
            id_imp,
            nombre_imp,
            proveedor_imp ,
            fecha_vencimiento_imp,
            costo_imp,
            ruta_comprobante_imp,
            tipo_imp
            
             from imp  
             WHERE id_imp = '$id'");
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
}


?>