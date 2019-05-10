<?php
session_start();
header('Access-Control-Allow-Origin: *');
require_once("../conexion.php");
$conexion = new Conexion();

$referencia = $_POST["referencia"];


$use = $conexion->prepare("SELECT distinct v.vehi_placa,m.mate_descripcion, r.*,u.unid_abreviacion,
                            (select pt.punt_descripcion from punto pt 
                            where pt.punt_id = r.puor_id) as origen,
                            (select pt.punt_descripcion from punto pt 
                            where pt.punt_id = r.pude_id) as destino 
                            FROM ruta r, material m, punto p, vehiculo v, unidad u
                            where r.ruta_codigo = ? 
                            and v.vehi_id=r.vehi_id and r.mate_id = m.mate_id and m.unid_id=u.unid_id");

$use->bindValue(1, $referencia);

	
$use ->execute();
$count = $use->rowCount();
$row = $use->fetchAll();
$data = (object) array();

foreach($row as $registro){
	$object = (object) array();
	$object->id =  $registro['ruta_id'];
	$object->origen = $registro['origen'];
	$object->destino = $registro['destino'];
	$object->material = $registro['mate_descripcion'];
	$object->cantidad = $registro['ruta_cantidadmaterial'];
	$object->vehiculo = $registro['vehi_placa'];
	$object->fechacreacion = $registro['ruta_fechacreacion'];
	$object->pesollegadasincarga = $registro['ruta_pesollegadavacio'];
	$object->pesollegadaconcarga = $registro['ruta_pesollegadalleno'];
	$object->cantidadllegada = $registro['ruta_cantidadllegada'];
	$object->referencia = $registro['ruta_codigo'];
	$object->observaciones = $registro['ruta_observaciones'];
	$object->observacionesSalida = $registro['ruta_observacionessalida'];
	$object->precinto = $registro['ruta_precinto'];
	if($registro["ruta_pesosalidalleno"]!=""&&$registro["ruta_pesosalidavacio"]!=""){
		$object->unidad = "kg";	
	}
	else{
		$object->unidad = $registro['unid_abreviacion'];	
	}
}
$data->ruta = $object;
$data->estado = $count > 0 ? "OK" : "ERROR";
$data->mensaje = $count > 0 ? "" : "No existe una ruta con la referencia ingresada";
$conexion = null;
print_r(json_encode($data));
?>