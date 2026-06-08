<?php
require_once '../src/config.php';
$con = conexion();

$query = "SELECT id_imp, nombre_imp, proveedor_imp, fecha_vencimiento_imp, costo_imp, ruta_comprobante_imp FROM imp ORDER BY fecha_vencimiento_imp DESC";
$result = $con->query($query);

$arr = array();
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $arr[] = array(
            "id_imp" => $row['id_imp'],
            "nombre_imp" => $row['nombre_imp'],
            "proveedor_imp" => $row['proveedor_imp'],
            "fecha_vencimiento_imp" => $row['fecha_vencimiento_imp'],
            "costo_imp" => $row['costo_imp'],
            "ruta_comprobante_imp" => $row['ruta_comprobante_imp']
        );
    }
}
echo json_encode($arr);
?>
