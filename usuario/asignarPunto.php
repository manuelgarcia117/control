<?php
error_reporting(0);
header('Access-Control-Allow-Origin: *');
session_start();
require_once("../conexion.php");
$conexion = new Conexion();
$data = (object) array();

$id=$_POST["id"];
$punto = $_POST["punto"];

$use = $conexion->prepare("UPDATE usuario SET punt_id=? WHERE usua_id=?");
$use->bindValue(1, $punto);
$use->bindValue(2, $id);
$estado = $use->execute();
if($estado){
    $data->estado = "OK";
	$data->mensaje = "Punto asignado con exito";         
}
else{
    $data->estado = "ERROR";
	$data->mensaje = "Se ha presentado un error al asignar el punto";
}
$conexion = null;
print_r(json_encode($data));
?>