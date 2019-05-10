<?php
session_start();
header('Access-Control-Allow-Origin: *');
require_once("../conexion.php");
date_default_timezone_set("America/Bogota");
$conexion = new Conexion();

$date = date("Y-m-d H:i:s");

$referencia = $_POST["referencia"];
$peso1 = $_POST["peso1"];
$peso2 = $_POST["peso2"];
$pesoneto="";
if($peso1!=""&&$peso2!=""){
	$pesoneto = $peso2-$peso1;
}
$cantidad = $_POST["cantidadMaterialLlegada"];
$observaciones = $_POST["observaciones"];

$use = $conexion->prepare("update ruta set ruta_pesollegadavacio = ?, 
											ruta_pesollegadalleno = ?, 
											ruta_pesollegadaneto = ?,
											ruta_cantidadllegada=?,
											ruta_observaciones=?
                            where ruta_codigo=?;");

$use->bindParam(1, $peso1);
$use->bindParam(2, $peso2);
$use->bindParam(3, $pesoneto);
$use->bindParam(4, $cantidad);
$use->bindParam(5, $observaciones);
$use->bindParam(6, $referencia);


$status = $use->execute();

if($status){
	$estado = "OK";
	$mensaje = "Cantidad de material modificada con éxito";
} else {
	$estado = "ERROR";
	$mensaje = "Error al modificar la cantidad de material";
}

$data = (object) array();
$data->estado = $estado;
$data->mensaje = $mensaje;
$conexion = null;
print_r(json_encode($data));
?>