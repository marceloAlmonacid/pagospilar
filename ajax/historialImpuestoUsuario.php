<?php
require_once '../src/config.php';
$con = conexion();

if($_POST){
    $id_usu = $con->real_escape_string($_POST['id_usu']);
    $nombre_imp = $con->real_escape_string($_POST['nombre_imp']);
    $anio = isset($_POST['anio']) ? $con->real_escape_string($_POST['anio']) : date('Y');

    // Inicializar los 12 meses con null para que el gráfico los contemple
    $meses = array();
    for($i = 1; $i <= 12; $i++) {
        $mesStr = str_pad($i, 2, "0", STR_PAD_LEFT);
        $meses[$mesStr] = null;
    }

    $query = "SELECT MONTH(i.fecha_vencimiento_imp) as mes, SUM(ui.monto) as monto 
              FROM usu_imp ui 
              INNER JOIN imp i ON ui.id_imp1 = i.id_imp 
              WHERE ui.id_usu1 = '$id_usu' AND i.nombre_imp = '$nombre_imp' 
              AND YEAR(i.fecha_vencimiento_imp) = '$anio'
              GROUP BY MONTH(i.fecha_vencimiento_imp)";

    $result = $con->query($query);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $m = str_pad($row['mes'], 2, "0", STR_PAD_LEFT);
            $meses[$m] = $row['monto'];
        }
        
        $arrFinal = array();
        foreach($meses as $mesNum => $monto) {
            $arrFinal[] = array(
                "fecha" => "$anio-$mesNum-01",
                "monto" => $monto
            );
        }
        echo json_encode($arrFinal);
    } else {
        echo json_encode(array("status" => "error"));
    }
}
