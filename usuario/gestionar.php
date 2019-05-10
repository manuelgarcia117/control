<?php
error_reporting(0);
header('Access-Control-Allow-Origin: *');
session_start();
require_once("../conexion.php");
date_default_timezone_set("America/Bogota");
$conexion = new Conexion();
$id = $_POST["id"];
$idSesion = $_POST["idSesion"];
$nombres = mb_convert_case($_POST["nombres"],MB_CASE_TITLE, "UTF-8");
$apellidos = mb_convert_case($_POST["apellidos"],MB_CASE_TITLE, "UTF-8");
$tipoDocumento = 1;
$documento = $_POST["documento"];
$clave = $_POST["clave"];
$telefono = $_POST["telefono"];
$fecha = date("Y-m-d H:i:s");


$data = (object) array();

if($id==""){
    $use = $conexion->prepare("SELECT *
    							FROM 
    							usuario WHERE tido_id = ? AND usua_documento=?");
    $use->bindValue(1, $tipoDocumento);
    $use->bindValue(2, $documento);
}
else{
    $use = $conexion->prepare("SELECT *
    							FROM 
    							usuario WHERE tido_id = ? AND usua_documento=?
    							AND usua_id<>?");
    $use->bindValue(1, $tipoDocumento);
    $use->bindValue(2, $documento);
    $use->bindValue(3, $id);
}

$use ->execute();
$row = $use->fetchAll();
$count = $use->rowCount();
if($count==0){
    if($id==""){
        $use = $conexion->prepare("SELECT *
    							FROM 
    							usuario WHERE usua_telefono = ?");
        $use->bindValue(1, $telefono);
    }
    else{
        $use = $conexion->prepare("SELECT *
    							FROM 
    							usuario WHERE usua_telefono = ? AND usua_id<>?");
        $use->bindValue(1, $telefono);
        $use->bindValue(2, $id);
    }
    $use ->execute();
    $row = $use->fetchAll();
    $count = $use->rowCount();
    if($count==0){
        if($id==""){
            $use = $conexion->prepare("INSERT INTO usuario(usua_nombres,
                                                            usua_apellidos,
                                                            usua_documento,
                                                            usua_fecharegistro,
                                                            usua_registro,
                                                            usua_telefono,
                                                            usua_clave,
                                                            tido_id)
                                    VALUES(?,?,?,?,?,?,?,?)");
            $use->bindValue(1, $nombres);
            $use->bindValue(2, $apellidos);
            $use->bindValue(3, $documento);
            $use->bindValue(4, $fecha);
            $use->bindValue(5, $idSesion);
            $use->bindValue(6, $telefono);
            $use->bindValue(7, $clave);
            $use->bindValue(8, $tipoDocumento);
        }
        else{
            if($clave==""){
                $use = $conexion->prepare("UPDATE usuario set usua_nombres=?,
                                                                usua_apellidos=?,
                                                                usua_documento=?,
                                                                usua_telefono=?,
                                                                tido_id=?
                                            WHERE usua_id=?");
                $use->bindValue(1, $nombres);
                $use->bindValue(2, $apellidos);
                $use->bindValue(3, $documento);
                $use->bindValue(4, $telefono);
                $use->bindValue(5, $tipoDocumento);
                $use->bindValue(6, $id);
            }
            else{
                $use = $conexion->prepare("UPDATE usuario set usua_nombres=?,
                                                                usua_apellidos=?,
                                                                usua_documento=?,
                                                                usua_telefono=?,
                                                                usua_clave=?,
                                                                tido_id=?
                                            WHERE usua_id=?");
                $use->bindValue(1, $nombres);
                $use->bindValue(2, $apellidos);
                $use->bindValue(3, $documento);
                $use->bindValue(4, $telefono);
                $use->bindValue(5, $clave);
                $use->bindValue(6, $tipoDocumento);
                $use->bindValue(7, $id);
            }
        }
        $status = $use->execute();
        if($status){
            $data->estado = "OK";
            if($id==""){
	            $data->mensaje = "Usuario registrado con éxito";
	            $data->id = $conexion->lastInsertId();
	            $data->fecha = $fecha;
            }
            else{
                $data->mensaje = "Usuario modificado con éxito";     
            }
        }
    }
    else{
        $data->estado = "ERROR";
	    $data->mensaje = "Ya se encuentra registrado un usuario con el número de teléfono ingresado";    
    }
}
else{
	$data->estado = "ERROR";
	$data->mensaje = "Ya se encuentra registrado un usuario con el número de documento ingresado";
}
$conexion = null;
print_r(json_encode($data));
?>