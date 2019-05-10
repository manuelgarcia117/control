<?php
error_reporting(0);
header('Access-Control-Allow-Origin: *');
session_start();
require_once("../conexion.php");
$conexion = new Conexion();
$id = $_POST["id"];
$use = $conexion->prepare("select distinct * from punto p where p.punt_id = ?");

$use->bindValue(1, $id);							
	
$use ->execute();
$count = $use->rowCount();
$row = $use->fetchAll();
$data = (object) array();
$array = array();

if ($count > 0) {
	foreach($row as $registro){
		$object = (object) array();
		$object->id =  $registro['punt_id'];
		$object->descripcion =  $registro['punt_descripcion'];
		$object->nit =  $registro['punt_nit'];
		$object->razonsocial =  $registro['punt_razonsocial'];
		$object->zona =  $registro['zona_id'];
		array_push($array, $object);
	}
	$data->punto = $array;
	$data->estado = "OK";
} else {
	$data->punto = [];
	$data->estado = "ERROR";
}
$conexion = null;
print_r(json_encode($data));
?>