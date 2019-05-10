<?php
error_reporting(0);
header('Access-Control-Allow-Origin: *');
session_start();
require_once("../conexion.php");
$conexion = new Conexion();

$id = $_POST["id"];

$data = (object) array();

$use = $conexion->prepare("DELETE FROM ruta
                            WHERE usua_origen=? OR usua_destino=? OR usua_conductor=?");
$use->bindValue(1, $id);
$use->bindValue(2, $id);
$use->bindValue(3, $id);
$status = $use->execute();

$use = $conexion->prepare("DELETE FROM vehiculo
                            WHERE vehi_propietario=?");
$use->bindValue(1, $id);
$status = $use->execute();

$use = $conexion->prepare("DELETE FROM usuariorol
                            WHERE usua_id=?");
$use->bindValue(1, $id);
$status = $use->execute();

$use = $conexion->prepare("DELETE FROM usuario
                            WHERE usua_registro=?");

$use->bindValue(1, $id);
$status = $use->execute();

$use = $conexion->prepare("DELETE FROM usuario
                            WHERE usua_id=?");

$use->bindValue(1, $id);
$status = $use->execute();
if($status){
    $data->estado = "OK";
    $data->mensaje = "Usuario eliminado exitosamente";   
}else{
    $data->estado = "ERROR";
    $data->mensaje = "Error al eliminar el usuario, intente nuevamente";    
}
$conexion = null;
print_r(json_encode($data));
?>