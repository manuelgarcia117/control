<?php
session_start();
header('Access-Control-Allow-Origin: *');
require_once("../conexion.php");
$conexion = new Conexion();
date_default_timezone_set("America/Bogota");
$idSesion = $_POST["idSesion"];
$date = date("Y-m-d");

$use = $conexion->prepare("SELECT DISTINCT time(r.ruta_fechallegada) as hora,v.vehi_placa,r.ruta_id,r.ruta_codigo from ruta r,vehiculo v 
                            Where r.usua_destino = ? AND r.ruta_tipo=1
                           	AND date(r.ruta_fechallegada) = ?
                            AND r.vehi_id=v.vehi_id");

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
        	$object->id =  $registro['ruta_id'];
        	$object->placa = $registro['vehi_placa'];
        	$object->hora = $registro['hora'];
        	$object->referencia = $registro['ruta_codigo'];
        	array_push($array, $object);
        }
        $data->llegadas = $array;
    }
    else{
        $data->llegadas = [];    
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