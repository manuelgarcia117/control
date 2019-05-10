<?php
session_start();
header('Access-Control-Allow-Origin: *');
require_once("../conexion.php");
$conexion = new Conexion();

$idSesion = $_POST["idSesion"];

$use = $conexion->prepare("SELECT distinct v.vehi_placa,v.vehi_placatrailer,m.mate_descripcion, r.*,u.unid_abreviacion,
                            (select pt.punt_descripcion from punto pt
                            where pt.punt_id = r.puor_id) as origen,
                            (select pt.punt_descripcion from punto pt 
                            where pt.punt_id = r.pude_id) as destino,
                            (select u.usua_nombres from usuario u where u.usua_id=r.usua_origen) AS nombresdespachador, 
                            (select u.usua_apellidos from usuario u where u.usua_id=r.usua_origen) AS apellidosdespachador,
                            (select u.usua_documento from usuario u where u.usua_id=r.usua_origen) AS documentodespachador, 
                            (select u.usua_nombres from usuario u where u.usua_id=r.usua_conductor) AS nombresconductor,
                            (select u.usua_apellidos from usuario u where u.usua_id=r.usua_conductor) AS apellidosconductor,
                            (select u.usua_documento from usuario u where u.usua_id=r.usua_conductor) AS documentoconductor,
                            (select conf_nombreempresa from configuracion limit 1) AS nombreempresa,
                            (select conf_nitempresa from configuracion limit 1) AS nitempresa,
                            (select conf_direccionempresa from configuracion limit 1) AS direccionempresa,
                            (select et.emtr_descripcion from empresatransportadora et where et.emtr_id=r.emtr_id) AS empresatransportadora
                            FROM ruta r, material m, punto p, vehiculo v, unidad u
                            where r.usua_origen = $idSesion
                            and v.vehi_id=r.vehi_id and r.mate_id = m.mate_id and m.unid_id=u.unid_id
                            and r.ruta_tipo = 1 ORDER BY r.ruta_fechacreacion DESC limit 1");
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
	$object->nombredespachador = explode(" ",$registro['nombresdespachador'])[0]." ".explode(" ",$registro['apellidosdespachador'])[0];
	$object->documentodespachador = $registro['documentodespachador'];
	$object->nombreconductor = explode(" ",$registro['nombresconductor'])[0]." ".explode(" ",$registro['apellidosconductor'])[0];
	$object->documentoconductor = $registro['documentoconductor'];
	$object->cantidad = $registro['ruta_cantidadmaterial'];
	$object->vehiculo = $registro['vehi_placa'];
	$object->trailer = $registro['ruta_placatrailer'];
	$object->fechacreacion = $registro['ruta_fechacreacion'];
	$object->pesosalidasincarga = $registro['ruta_pesosalidavacio'];
	$object->pesosalidaconcarga = $registro['ruta_pesosalidalleno'];
	$object->pesosalidaneto = $registro['ruta_pesosalidaneto'];
	$object->referencia = $registro['ruta_codigo'];
	$object->observaciones = $registro['ruta_observaciones'];
	$object->observacionesSalida = $registro['ruta_observacionessalida'];
	$object->nombreempresa = $registro['nombreempresa'];
	$object->nitempresa = "NIT: ".$registro['nitempresa'];
	$object->precinto = $registro['ruta_precinto'];
	$object->direccionempresa = $registro['direccionempresa'];
	$object->empresatransportadora = $registro['empresatransportadora'];
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