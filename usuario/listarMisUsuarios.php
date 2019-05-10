<?php
error_reporting(0);
session_start();
header('Access-Control-Allow-Origin: *');
require_once("../conexion.php");
$conexion = new Conexion();
date_default_timezone_set("America/Bogota");
$idSesion = $_POST["idSesion"];
$date = date("Y-m-d");;

$use = $conexion->prepare("SELECT * FROM usuario u, tipodocumento td
                            WHERE u.usua_registro=? AND date(u.usua_fecharegistro)=?
                            AND u.tido_id=td.tido_id");

$use->bindValue(1, $idSesion);
$use->bindValue(2, $date);

	
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
        	$object->documento = $registro['tido_abreviacion']." ".$registro['usua_documento'];
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
    $data->mensaje = "Error al cargar rutas de hoy"; 
}
$conexion = null;
print_r(json_encode($data));
?>