<?php
header('Access-Control-Allow-Origin: *');
session_start();
require_once("../conexion.php");
date_default_timezone_set("America/Bogota");
$conexion = new Conexion();

$id = $_POST["id"];
$zona = $_POST["zona"];
$nit = strtoupper($_POST["nit"]);
$razonSocial = mb_convert_case($_POST["razonSocial"],MB_CASE_TITLE, "UTF-8");
$idSesion = $_POST["idSesion"];
$descripcion = mb_convert_case($_POST["descripcion"],MB_CASE_TITLE, "UTF-8");


$data = (object) array();

if($id==""){
    $use = $conexion->prepare("SELECT *
    							FROM 
    							punto WHERE punt_descripcion=?");
    $use->bindValue(1, $descripcion);

}
else{
    $use = $conexion->prepare("SELECT * FROM punto WHERE punt_descripcion=? AND punt_id<>?");
    $use->bindValue(1, $descripcion);
    $use->bindValue(2, $id);
}

$use ->execute();
$row = $use->fetchAll();
$count = $use->rowCount();
if($count==0){
    if($id==""){
        $use = $conexion->prepare("INSERT INTO punto(punt_descripcion,
                                                    punt_nit,
                                                    punt_razonsocial,
                                                    zona_id)
                                VALUES(?,?,?,?)");
        $use->bindValue(1, $descripcion);
        $use->bindValue(2, $nit);
        $use->bindValue(3, $razonSocial);
        $use->bindValue(4, $zona);
    }
    else{
        $use = $conexion->prepare("UPDATE punto set punt_descripcion=?,
                                                    punt_nit=?,
                                                    punt_razonsocial=?,
                                                    zona_id=?
                                            WHERE punt_id=?");
        $use->bindValue(1, $descripcion);
        $use->bindValue(2, $nit);
        $use->bindValue(3, $razonSocial);
        $use->bindValue(4, $zona);
        $use->bindValue(5, $id);
    }
    $status = $use->execute();
    if($status){
        $data->estado = "OK";
        if($id==""){
            $data->mensaje = "Patio registrado con éxito";
            $data->id = $conexion->lastInsertId();
            $data->fecha = $fecha;
        }
        else{
            $data->mensaje = "Patio modificado con éxito";
        }
    }
}
else{
	$data->estado = "ERROR";
	$data->mensaje = "Ya se encuentra registrado un punto con la descripción ingresada";
}
$conexion = null;
print_r(json_encode($data));
?>