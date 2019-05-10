<?php
error_reporting(0);
session_start();
header('Access-Control-Allow-Origin: *');
require_once("../conexion.php");
$conexion = new Conexion();
$documento = $_POST["documento"];
$tipoDocumento = 1;
$use = $conexion->prepare("select distinct * from usuario
                            where tido_id = ? and usua_documento=?");

$use->bindValue(1, $tipoDocumento);
$use->bindValue(2, $documento);
	
$use ->execute();
$count = $use->rowCount();
$row = $use->fetchAll();
$data = (object) array();
$array = array();

if ($count > 0) {
	foreach($row as $registro){
		$object = (object) array();
		$object->id =  $registro['usua_id'];
		$object->nombre =  explode(" ",$registro['usua_nombres'])[0]." ".explode(" ",$registro['usua_apellidos'])[0];
		array_push($array, $object);
	}
	$data->conductor = $array;
	$data->estado = "OK";
} else {
	$data->conductor = [];
}
$conexion = null;
print_r(json_encode($data));
?>