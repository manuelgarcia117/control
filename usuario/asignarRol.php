<?php
error_reporting(0);
header('Access-Control-Allow-Origin: *');
session_start();
require_once("../conexion.php");
$conexion = new Conexion();
$data = (object) array();

$id=$_POST["id"];
$roles = $_POST["roles"];

$use = $conexion->prepare("DELETE FROM usuariorol WHERE usua_id=?");
$use->bindValue(1, $id);
$estado = $use->execute();
if($estado){
    $sql = "INSERT INTO usuariorol(usua_id,rol_id)
            				VALUES";
    for($i=0;$i<count($roles);$i++){
        if($i<(count($roles)-1)){
            $sql.="($id,$roles[$i]),";
         }
         else
         if($i==(count($roles)-1)){
             $sql.="($id,$roles[$i])";
         }
    }
    $use = $conexion->prepare($sql);
    $status = $use->execute();
    if($status){
        $data->estado = "OK";
        $data->mensaje = "Roles asignados con éxito";
    }else{
        $data->estado = "ERROR";
	    $data->mensaje = "Se ha presentado un error al asignar los roles al usuario";
    }  
}
else{
    $data->estado = "ERROR";
	$data->mensaje = "Se ha presentado un error al asignar los roles al usuario";
}
$conexion = null;
print_r(json_encode($data));
?>