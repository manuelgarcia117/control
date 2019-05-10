<?php
error_reporting(0);
header('Access-Control-Allow-Origin: *');
session_start();
require_once("../conexion.php");
$conexion = new Conexion();

$id = $_POST["idSesion"];

$data = (object) array();

$use = $conexion->prepare("SELECT p.punt_id, p.punt_descripcion FROM usuario u, punto p WHERE u.punt_id=p.punt_id AND u.usua_id=?");

$use->bindValue(1, $id);

$use ->execute();
$row = $use->fetchAll();
$count = $use->rowCount();
if($count>0){
	$array = array();
	foreach($row as $registro){
		$object = (object) array();
		$object->id =  $registro['punt_id'];
		$object->descripcion =  $registro['punt_descripcion'];
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