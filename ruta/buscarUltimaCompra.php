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
                            (select u.usua_nombres from usuario u where u.usua_id=r.usua_destino) AS nombresrecepcionista, 
                            (select u.usua_apellidos from usuario u where u.usua_id=r.usua_destino) AS apellidosrecepcionista,
                            (select u.usua_documento from usuario u where u.usua_id=r.usua_destino) AS documentorecepcionista, 
                            (select u.usua_nombres from usuario u where u.usua_id=r.usua_conductor) AS nombresconductor,
                            (select u.usua_apellidos from usuario u where u.usua_id=r.usua_conductor) AS apellidosconductor,
                            (select u.usua_documento from usuario u where u.usua_id=r.usua_conductor) AS documentoconductor,
                            (select conf_nombreempresa from configuracion limit 1) AS nombreempresa,
                            (select conf_nitempresa from configuracion limit 1) AS nitempresa,
                            (select conf_direccionempresa from configuracion limit 1) AS direccionempresa,
                            (select et.emtr_descripcion from empresatransportadora et where et.emtr_id=r.emtr_id) AS empresatransportadora
                            FROM ruta r, material m, punto p, vehiculo v, unidad u
                            where r.usua_destino = $idSesion
                            and v.vehi_id=r.vehi_id and r.mate_id = m.mate_id and m.unid_id=u.unid_id
                            and r.ruta_tipo = 2 ORDER BY r.ruta_fechacreacion DESC limit 1");

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
	$object->nombrerecepcionista = explode(" ",$registro['nombresrecepcionista'])[0]." ".explode(" ",$registro['apellidosrecepcionista'])[0];
	$object->documentorecepcionista = $registro['documentorecepcionista'];
	$object->nombreconductor = explode(" ",$registro['nombresconductor'])[0]." ".explode(" ",$registro['apellidosconductor'])[0];
	$object->documentoconductor = $registro['documentoconductor'];
	$object->cantidad = $registro['ruta_cantidadllegada'];
	$object->vehiculo = $registro['vehi_placa'];
	$object->trailer = $registro['ruta_placatrailer'];
	$object->fechacreacion = $registro['ruta_fechacreacion'];
	$object->pesollegadasincarga = $registro['ruta_pesollegadavacio'];
	$object->pesollegadaconcarga = $registro['ruta_pesollegadalleno'];
	$object->pesollegadaneto = $registro['ruta_pesollegadaneto'];
	$object->referencia = $registro['ruta_codigo'];
	$object->observaciones = $registro['ruta_observaciones'];
	$object->observacionesSalida = $registro['ruta_observacionessalida'];
	$object->nombreempresa = $registro['nombreempresa'];
	$object->nitempresa = "NIT: ".$registro['nitempresa'];
	$object->direccionempresa = $registro['direccionempresa'];
	$object->empresatransportadora = $registro['empresatransportadora'];
	if($registro["ruta_pesollegadalleno"]!=""&&$registro["ruta_pesollegadadavacio"]!=""){
		$object->unidad = "kg";	
	}
	else{
		$object->unidad = $registro['unid_abreviacion'];	
	}
}
$data->ruta = $object;
$data->estado = $count > 0 ? "OK" : "ERROR";
$data->mensaje = $count > 0 ? "" : "No existe una compra con la referencia ingresada";
$conexion = null;
print_r(json_encode($data));
?>