<?php
error_reporting(0);
header('Access-Control-Allow-Origin: *');
session_start();
require_once("../conexion.php");
$conexion = new Conexion();
$textoBusqueda = $_POST["textoBusqueda"];
$idSesion = $_POST["idSesion"];
$idZona = $_POST["idZona"];
$tipo = $_POST["tipo"];
if($idZona!=""){
	$use = $conexion->prepare("select distinct * FROM punto WHERE zona_id=? ORDER BY punt_descripcion");
	$use->bindValue(1, $idZona);	
}
else
if($idSesion==""){
	if($tipo==1){
		$use = $conexion->prepare("select distinct * from punto where punt_id<>0 AND punt_descripcion LIKE '%$textoBusqueda%' ORDER BY punt_descripcion LIMIT 10");	
	}
	else{
		$use = $conexion->prepare("select distinct * from punto where punt_id<>0 AND punt_descripcion LIKE '%$textoBusqueda%' ORDER BY punt_descripcion");	
	}
}
else{
	$use = $conexion->prepare("select distinct *,(CASE WHEN punt_id = (select u.punt_id from usuario u where u.usua_id=?)
							THEN 1 ELSE 0 END) AS predeterminado from punto where punt_id<>0 
							AND zona_id=(SELECT distinct z.zona_id FROM zona z,punto p,usuario u
							WHERE u.punt_id=p.punt_id AND p.zona_id=z.zona_id AND u.usua_id=?) ORDER BY punt_descripcion");
	$use->bindValue(1, $idSesion);
	$use->bindValue(2, $idSesion);
}

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
		$object->codigo = $registro['punt_codigo'];
		if($idSesion!=""){
			$object->predeterminado = $registro['predeterminado'];
		}
		array_push($array, $object);
	}
	$data->puntos = $array;
	$data->estado = "OK";
	$data->login = false;
} else {
	$data->puntos = [];
	$data->estado = "ERROR";
	$data->login = true;
}
$conexion = null;
print_r(json_encode($data));
?>