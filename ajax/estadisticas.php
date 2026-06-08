<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');

if($_POST){
    require_once '../src/config.php';
    $conexion = conexion();

    if(isset($_POST['action']) && $_POST['action'] == 'estadisticas'){
        
        // Gastos por categoría del año actual
        $anio = date('Y');
        $queryCat = mysqli_query($conexion, "SELECT tipo_imp, SUM(costo_imp) as total FROM imp WHERE YEAR(fecha_vencimiento_imp) = '$anio' GROUP BY tipo_imp");
        
        $categorias = array();
        $totales_cat = array();
        while($row = mysqli_fetch_assoc($queryCat)){
            $categorias[] = $row['tipo_imp'] ? $row['tipo_imp'] : 'OTROS';
            $totales_cat[] = $row['total'];
        }

        // Gastos de los últimos 6 meses
        $queryMeses = mysqli_query($conexion, "
            SELECT MONTH(fecha_vencimiento_imp) as mes, SUM(costo_imp) as total 
            FROM imp 
            WHERE fecha_vencimiento_imp >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
            GROUP BY MONTH(fecha_vencimiento_imp)
            ORDER BY fecha_vencimiento_imp ASC
        ");

        $meses = array();
        $totales_meses = array();
        $nombres_meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        
        while($row = mysqli_fetch_assoc($queryMeses)){
            $mes_index = intval($row['mes']) - 1;
            $meses[] = $nombres_meses[$mes_index];
            $totales_meses[] = $row['total'];
        }

        echo json_encode(array(
            "categorias" => $categorias,
            "totales_cat" => $totales_cat,
            "meses" => $meses,
            "totales_meses" => $totales_meses
        ));
    }
}
