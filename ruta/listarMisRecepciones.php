<?php
session_start();
header('Access-Control-Allow-Origin: *');
require_once("../conexion.php");
$conexion = new Conexion();
date_default_timezone_set("America/Bogota");
$idSesion = $_POST["idSesion"];
$fecha1 = $_POST["fecha1"];
$fecha2 = $_POST["fecha2"];

$sql = "SELECT DISTINCT v.vehi_placa,r.ruta_id,r.ruta_codigo,r.ruta_fechacreacion from ruta r,vehiculo v 
                            Where r.usua_destino = $idSesion AND r.ruta_tipo=1";
if($fecha1!=""&&$fecha2!=""){
    $sql.=" AND date(r.ruta_fechallegada) between '$fecha1' AND '$fecha2'";    
}

$sql.= " AND r.vehi_id=v.vehi_id";

$use = $conexion->prepare($sql);
	
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
        	$object->fecha = $registro['ruta_fechacreacion'];
        	$object->referenciamostrar = str_replace(substr($registro['ruta_codigo'],0,16),substr($registro['ruta_codigo'],0,16)."<br>",$registro['ruta_codigo']);
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
    $data->mensaje = "Error al cargar despachos"; 
}
$conexion = null;
print_r(json_encode($data));
?>