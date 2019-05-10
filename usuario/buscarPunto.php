<?php
error_reporting(0);
header('Access-Control-Allow-Origin: *');
session_start();
require_once("../conexion.php");
$conexion = new Conexion();

$id = $_POST["id"];

$data = (object) array();

$use = $conexion->prepare("SELECT u.punt_id,(select z.zona_id from zona z, punto p where p.zona_id=z.zona_id and p.punt_id=u.punt_id) AS zona FROM usuario u where u.usua_id = ?");

$use->bindValue(1, $id);

$use ->execute();
$row = $use->fetchAll();
$count = $use->rowCount();
if($count>0){
	$array = array();
	foreach($row as $registro){
		$object = (object) array();
		$object->punto =  $registro['punt_id'];
		$object->zona =  $registro['zona'];
		array_push($array, $object);
	}
	$data->estado = "OK";
	$data->punto = $array;
}
else{
	$data->punto = [];
	$data->estado = "ERROR";
	$data->mensaje = "Error al cargar el punto";
}
$conexion = null;
print_r(json_encode($data));
?>