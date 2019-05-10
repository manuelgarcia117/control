<?php
header('Access-Control-Allow-Origin: *');
session_start();
require_once("../conexion.php");
date_default_timezone_set("America/Bogota");
$conexion = new Conexion();

$id = $_POST["id"];

$data = (object) array();

$use = $conexion->prepare("DELETE FROM ruta WHERE puor_id=? OR pude_id=?");
$use->bindValue(1, $id);
$use->bindValue(2, $id);
$use ->execute();

$use = $conexion->prepare("DELETE FROM punto WHERE punt_id=?");
$use->bindValue(1, $id);
$estado=$use ->execute();
if($estado){
    $data->estado = "OK";
    $data->mensaje = "Punto eliminado con éxito";    
}
else{
    $data->estado = "ERROR";
    $data->mensaje = "Error al eliminar  el punto";
}
$conexion = null;
print_r(json_encode($data));
?>