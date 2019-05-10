<?php
session_start();
header('Access-Control-Allow-Origin: *');
require_once("../conexion.php");
date_default_timezone_set("America/Bogota");
$conexion = new Conexion();

$date = date("Y-m-d H:i:s");

$id = $_POST["idRuta"];
$referencia= $_POST["referencia"];
$evidencia = $_POST["evidencia"];
$idSesion = $_POST["idSesion"];
$peso1 = $_POST["peso1"];
$peso2 = $_POST["peso2"];
$pesoneto="";
if($peso1!=""&&$peso2!=""){
	$pesoneto = $peso2-$peso1;
}
$cantidadMaterialLlegada = $_POST["cantidadMaterialLlegada"];
$observaciones = $_POST["observaciones"];

$data = (object) array();

$use = $conexion->prepare("SELECT *
							FROM 
							ruta
							WHERE ruta_codigo = ?
							AND ruta_fechallegada is not null");

$use->bindValue(1, $referencia);
	
$use ->execute();
$count = $use->rowCount();
$row = $use->fetchAll();
$estado = $count > 0 ? "ERROR" : "OK";
$mensaje = $count > 0 ? "La llegada de este trayecto ha sido marcada con anterioridad" : "";

if ($count == 0) {
	$use = $conexion->prepare("SELECT * FROM ruta WHERE ruta_codigo=?");
    $use->bindParam(1, $referencia);
    $use ->execute();
    $count = $use->rowCount();
    if($count>0){
    	$row = $use->fetchAll();
    	$idruta = $row[0]['ruta_id'];
		$use = $conexion->prepare("update ruta set ruta_fechallegada = ?, 
													usua_destino = ?,
													ruta_pesollegadavacio = ?, 
													ruta_pesollegadalleno = ?, 
													ruta_pesollegadaneto = ?, 
													ruta_observaciones = ?,
													ruta_cantidadllegada=?
		                            where ruta_codigo=?;");
	
		$use->bindParam(1, $date);
		$use->bindParam(2, $idSesion);
		$use->bindParam(3, $peso1);
		$use->bindParam(4, $peso2);
		$use->bindParam(5, $pesoneto);
		$use->bindParam(6, $observaciones);
		$use->bindParam(7, $cantidadMaterialLlegada);
		$use->bindParam(8, $referencia);
		$status = $use->execute();
	}
	else{
		$use = $conexion->prepare("INSERT INTO ruta(ruta_fechallegada, 
													usua_destino,
													ruta_pesollegadavacio, 
													ruta_pesollegadalleno, 
													ruta_pesollegadaneto, 
													ruta_observaciones,
													ruta_cantidadllegada,
		                            				ruta_codigo)
		                            				values(?,?,?,?,?,?,?,?)");
	
		$use->bindParam(1, $date);
		$use->bindParam(2, $idSesion);
		$use->bindParam(3, $peso1);
		$use->bindParam(4, $peso2);
		$use->bindParam(5, $pesoneto);
		$use->bindParam(6, $observaciones);
		$use->bindParam(7, $cantidadMaterialLlegada);
		$use->bindParam(8, $referencia);
		$status = $use->execute();
		$idruta = $conexion->lastInsertId();
	}
	
	if($status){
		$carpeta = "img/".$idruta;
        if (!file_exists($carpeta)) {
            mkdir($carpeta, 0777, true);
        }
		if($evidencia!=""){
    		define('UPLOAD_DIR', 'img/'.$idruta.'/');
        	$img = $evidencia;
        	$img = str_replace('data:image/png;base64,', '', $img);
        	$img = str_replace('data:image/jpeg;base64,', '', $img);
        	$img = str_replace(' ', '+', $img);
        	$dataimg = base64_decode($img);
        	$file = UPLOAD_DIR . 'llegada.png';
        	$success = file_put_contents($file, $dataimg);
		}
		$data->idRuta = $idruta;
		$data->fechaActual = $date;
		$estado = "OK";
		$mensaje = "Recepción marcada con éxito";
		
	} else {
		$estado = "ERROR";
		$mensaje = "Error al marcar la recepción";
	}
}
$data->estado = $estado;
$data->mensaje = $mensaje;
$conexion = null;
print_r(json_encode($data));
?>