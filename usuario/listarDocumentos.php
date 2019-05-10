<?php
error_reporting(0);
session_start();
header('Access-Control-Allow-Origin: *');
require_once("../conexion.php");
$conexion = new Conexion();
date_default_timezone_set("America/Bogota");
$tipoDocumento = 1;
$documento = $_POST["textoBusqueda"];
$date = date("Y-m-d");;

$use = $conexion->prepare("SELECT * FROM usuario WHERE tido_id=$tipoDocumento AND usua_documento LIKE '$documento%' AND usua_id<>1 AND usua_id<>0 LIMIT 10");
$estado = $use ->execute();
$count = $use->rowCount();
$row = $use->fetchAll();
//print_r($row);
$data = (object) array();
$array = array();
if($estado){
    if($count>0){
        foreach($row as $registro){
            $object = (object) array();
        	$object->id =  $registro['usua_id'];
        	$object->nombre = explode(" ",$registro['usua_nombres'])[0]." ".explode(" ",$registro['usua_apellidos'])[0];
        	$object->documento = $registro['usua_documento'];
        	array_push($array, $object);
        }
        $data->usuarios = $array;
    }
    else{
        $data->usuarios = [];    
    }
    $data->estado = "OK";
}
else{
    $data->estado = "ERROR";
    $data->mensaje = "Error al cargar documentos"; 
}
$conexion = null;
print_r(json_encode($data));
?>