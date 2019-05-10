<?php
session_start();
header('Access-Control-Allow-Origin: *');
require_once("../conexion.php");
date_default_timezone_set("America/Bogota");
$conexion = new Conexion();
$date = date("Y-m-d H:i:s");
$usuarioOrigen = $_POST["usuarioOrigen"];
$material = $_POST["material"];
$puntoOrigen = $_POST["puntoOrigen"];
$puntoDestino = $_POST["puntoDestino"];
$cantidadMaterial = $_POST["cantidadMaterial"];
$observaciones = $_POST["observaciones"];
$precinto = $_POST["precinto"];
$evidencia = $_POST["evidencia"];

$vehiculo = mb_convert_case($_POST["vehiculo"],MB_CASE_UPPER, "UTF-8");
$placaTrailer = mb_convert_case($_POST["placaTrailer"],MB_CASE_UPPER, "UTF-8");
$vencimientoSoat = $_POST["vencimientoSoat"];
$vencimientoGases = $_POST["vencimientoGases"];
$empresaTransportadora = $_POST["empresaTransportadora"];

$tipoDocumentoConductor = 1;
$documentoConductor = $_POST["documentoConductor"];
$nombresConductor = mb_convert_case($_POST["nombresConductor"],MB_CASE_TITLE, "UTF-8");
$apellidosConductor = mb_convert_case($_POST["apellidosConductor"],MB_CASE_TITLE, "UTF-8");
$telefonoConductor = $_POST["telefonoConductor"];

$tipoDocumentoPropietario = 1;
$documentoPropietario = $_POST["documentoPropietario"];
$nombresPropietario = mb_convert_case($_POST["nombresPropietario"],MB_CASE_TITLE, "UTF-8");
$apellidosPropietario = mb_convert_case($_POST["apellidosPropietario"],MB_CASE_TITLE, "UTF-8");
$telefonoPropietario = $_POST["telefonoPropietario"];


$peso1 = $_POST["peso1"];
$peso2 = $_POST["peso2"];

$pesoneto="";
if($peso1!=""&&$peso2!=""){
	$pesoneto = $peso2-$peso1;
}

$codigo=md5(time());
$cortarcadena = rand(0,24);
$codigo = substr($codigo, $cortarcadena,8);

$auxvehiculo = 1;
$auxconductor = 1;
$auxpropietario = 1;

$data = (object) array();

$use = $conexion->prepare("SELECT * FROM usuario WHERE usua_documento = ? AND tido_id= ?");
$use->bindParam(1, $documentoConductor);
$use->bindParam(2, $tipoDocumentoConductor);
$use ->execute();
$count = $use->rowCount();
if($count==0){
	$use = $conexion->prepare("INSERT INTO usuario(usua_nombres, 
													usua_apellidos,
													tido_id,
													usua_documento,
													usua_telefono,
													usua_fecharegistro,
													usua_registro)
													values(?,?,?,?,?,?,?)");
	$use->bindParam(1, $nombresConductor);
	$use->bindParam(2, $apellidosConductor);
	$use->bindParam(3, $tipoDocumentoConductor);
	$use->bindParam(4, $documentoConductor);
	$use->bindParam(5, $telefonoConductor);
	$use->bindParam(6, $date);
	$use->bindParam(7, $usuarioOrigen);
	$status = $use->execute();
	$ultimoIdUsuario = $conexion->lastInsertId();
	$data->idUsuario = $ultimoIdUsuario;
	if(!$status){
		$auxconductor = 0;
		$mensaje = "Error al registrar conductor";
		$estado = "ERROR";
	}
}
else{
	$row = $use->fetchAll();
	$data->idUsuario = $row[0]["usua_id"];
}

//registrar propietario
if($documentoPropietario!=""){
	$use = $conexion->prepare("SELECT * FROM usuario WHERE usua_documento = ? AND tido_id= ?");
	$use->bindParam(1, $documentoPropietario);
	$use->bindParam(2, $tipoDocumentoPropietario);
	$use ->execute();
	$count = $use->rowCount();
	if($count==0){
		$use = $conexion->prepare("INSERT INTO usuario(usua_nombres, 
														usua_apellidos,
														tido_id,
														usua_documento,
														usua_telefono,
														usua_fecharegistro,
														usua_registro)
														values(?,?,?,?,?,?,?)");
		$use->bindParam(1, $nombresPropietario);
		$use->bindParam(2, $apellidosPropietario);
		$use->bindParam(3, $tipoDocumentoPropietario);
		$use->bindParam(4, $documentoPropietario);
		$use->bindParam(5, $telefonoPropietario);
		$use->bindParam(6, $date);
		$use->bindParam(7, $usuarioOrigen);
		$status = $use->execute();
		$ultimoIdPropietario = $conexion->lastInsertId();
		$data->idPropietario = $ultimoIdPropietario;
		if(!$status){
			$auxpropietario = 0;
			$mensaje = "Error al registrar conductor";
			$estado = "ERROR";
		}
	}
	else{
		$row = $use->fetchAll();
		$data->idPropietario = $row[0]["usua_id"];
	}
}


$use = $conexion->prepare("SELECT * FROM vehiculo WHERE vehi_placa = ?");
$use->bindParam(1, $vehiculo);
$use ->execute();
$count = $use->rowCount();
if($count==0){
	$use = $conexion->prepare("INSERT INTO vehiculo(vehi_placa,
													vehi_placatrailer,
													vehi_vencimientosoat,
													vehi_vencimientogases,
													emtr_id,
													vehi_propietario)
													values(?,?,?,?,?,(select u.usua_id from usuario u where u.usua_documento=? and u.tido_id=?))");
	$use->bindParam(1, $vehiculo);
	$use->bindParam(2, $placaTrailer);
	$use->bindParam(3, $vencimientoSoat);
	$use->bindParam(4, $vencimientoGases);
	$use->bindParam(5, $empresaTransportadora);
	$use->bindParam(6, $documentoPropietario);
	$use->bindParam(7, $tipoDocumentoPropietario);
	$status = $use->execute();
	$ultimoIdVehiculo = $conexion->lastInsertId();
	$data->idVehiculo = $ultimoIdVehiculo;
	if(!$status){
		$auxvehiculo = 0;
		$mensaje = "Error al registrar vehículo";
		$estado = "ERROR";
	}
}
else{
	$row = $use->fetchAll();
	$data->idVehiculo = $row[0]["vehi_id"];
}

if($auxconductor==1&&$auxvehiculo==1&&$auxpropietario==1){
$use = $conexion->prepare("INSERT INTO ruta (
							ruta_codigo,
							ruta_cantidadllegada,
							ruta_fechacreacion,
							ruta_fechallegada,
							puor_id,
							pude_id,
							mate_id,
							usua_destino,
							ruta_pesollegadavacio,
							ruta_pesollegadalleno,
							ruta_pesollegadaneto,
							vehi_id,
							usua_conductor,
							ruta_observaciones,
							ruta_tipo,
							ruta_placatrailer,
							emtr_id
							) 
							VALUES (?,?,?,?,?,?,?,?,?,?,?,
							(SELECT vehi_id FROM vehiculo WHERE vehi_placa = ?),
							(SELECT usua_id FROM usuario WHERE usua_documento = ? AND tido_id= ?),?,?,?,?);");

$use->bindParam(1, $codigo);
$use->bindParam(2, $cantidadMaterial);
$use->bindParam(3, $date);
$use->bindParam(4, $date);
$use->bindValue(5, $puntoOrigen);
$use->bindValue(6, $puntoDestino);
$use->bindValue(7, $material);
$use->bindValue(8, $usuarioOrigen);
$use->bindValue(9, $peso1);
$use->bindValue(10, $peso2);
$use->bindValue(11, $pesoneto);
$use->bindValue(12, $vehiculo);
$use->bindValue(13, $documentoConductor);
$use->bindValue(14, $tipoDocumentoConductor);
$use->bindValue(15, $observaciones);
$use->bindValue(16, "2");
$use->bindValue(17, $placaTrailer);
$use->bindValue(18, $empresaTransportadora);
$status = $use->execute();
$lastId = $conexion->lastInsertId();
$data->idRuta = $lastId;
	if($status){
		$estado = "OK";
		$mensaje = "Compra registrada con éxito";
        $carpeta = "img/".$lastId;
        if (!file_exists($carpeta)) {
            mkdir($carpeta, 0777, true);
        }
		if($evidencia!=""){
    		define('UPLOAD_DIR', 'img/'.$lastId.'/');
        	$img = $evidencia;
        	$img = str_replace('data:image/png;base64,', '', $img);
        	$img = str_replace('data:image/jpeg;base64,', '', $img);
        	$img = str_replace(' ', '+', $img);
        	$dataImg = base64_decode($img);
        	$file = UPLOAD_DIR . 'llegada.png';
        	$success = file_put_contents($file, $dataImg);
		}	
		
	} else {
		$estado = "ERROR";
		$mensaje = "Error al registrar la compra";
	}
}
$data->estado = $estado;
$data->codigo = $codigo;
$data->fechaActual = $date;
$data->mensaje = $mensaje;
$conexion = null;
print_r(json_encode($data));
?>