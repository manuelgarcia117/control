<?php
error_reporting(0);
header('Access-Control-Allow-Origin: *');
session_start();
require_once("../conexion.php");
$conexion = new Conexion();

$id = $_POST["id"];

$data = (object) array();

$use = $conexion->prepare("SELECT r.rol_id, r.rol_descripcion, ISNULL((
                                SELECT r.rol_descripcion
                                FROM usuariorol ur
                                WHERE r.rol_id = ur.rol_id
                                AND ur.usua_id = ?
                            )) AS esta
                            FROM rol r WHERE rol_id<>5 ORDER by rol_prioridad DESC");

$use->bindValue(1, $id);

$use ->execute();
$row = $use->fetchAll();
$count = $use->rowCount();
if($count>0){
	$array = array();
	$arrayru = array();
	foreach($row as $registro){
		$object = (object) array();
		$object->id =  $registro['rol_id'];
		$object->descripcion =  $registro['rol_descripcion'];
		if($registro['esta']=="0"){
	        array_push($arrayru, $registro['rol_id']);           
		}
		array_push($array, $object);
	}
	$data->roles = $array;
	$data->rolesusua = $arrayru;
	$data->estado = "OK";
}
else{
	$data->roles = [];
	$data->estado = "ERROR";
	$data->mensaje = "Error al cargar datos de la jornada";
}
$conexion = null;
print_r(json_encode($data));
?>