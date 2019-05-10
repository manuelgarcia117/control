<?php
error_reporting(0);
header('Access-Control-Allow-Origin: *');
session_start();
require_once("../conexion.php");
$conexion = new Conexion();
$textoBusqueda = $_POST["textoBusqueda"];
$arrayTextoBusqueda = explode(" ",$textoBusqueda);

$data = (object) array();

$sql = "SELECT * FROM usuario u, tipodocumento td WHERE ";
for($i=0;$i<count($arrayTextoBusqueda);$i++){
   if($i!=0){
       $sql.=" AND (u.usua_nombres LIKE '%".$arrayTextoBusqueda[$i]."%' OR u.usua_apellidos LIKE '%".$arrayTextoBusqueda[$i]."%' OR u.usua_documento LIKE '%".$arrayTextoBusqueda[$i]."%' OR u.usua_telefono LIKE '%".$arrayTextoBusqueda[$i]."%')";
   }
   else{
       $sql.=" (u.usua_nombres LIKE '%".$arrayTextoBusqueda[$i]."%' OR u.usua_apellidos LIKE '%".$arrayTextoBusqueda[$i]."%' OR u.usua_documento LIKE '%".$arrayTextoBusqueda[$i]."%' OR u.usua_telefono LIKE '%".$arrayTextoBusqueda[$i]."%')";
   }
}
$sql.="AND u.tido_id=td.tido_id AND u.usua_id<>0 AND u.usua_id<>1 ORDER by usua_nombres ASC";

$use = $conexion->prepare($sql);

$use ->execute();
$row = $use->fetchAll();
$count = $use->rowCount();
if($count>0){
	$array = array();
	foreach($row as $registro){
		$object = (object) array();
    	$object->id =  $registro['usua_id'];
    	$object->nombre = explode(" ",$registro['usua_nombres'])[0]." ".explode(" ",$registro['usua_apellidos'])[0];
    	$object->documento = $registro['usua_documento'];
    	array_push($array, $object);
	}
	$data->usuarios = $array;
	$data->estado = "OK";
}
else{
	$data->usuarios = [];
	$data->estado = "ERROR";
	$data->mensaje = "Error al cargar usuarios";
}
$conexion = null;
print_r(json_encode($data));
?>