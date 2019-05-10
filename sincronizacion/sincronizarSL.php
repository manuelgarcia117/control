<?php
    error_reporting(0);
    header('Access-Control-Allow-Origin: *');
    date_default_timezone_set("America/Bogota");
    $date = date("Y-m-d");
    $date2 = date("Y-m-d",strtotime($date."- 30 days"));
    require_once("../conexion.php");
    $conexion = new Conexion();
    $idSesion = $_POST["idSesion"];
    //$idSesion = 1;
    $data = (object) array();
    //backup de funciones
    $use = $conexion->prepare("SELECT DISTINCT * FROM funcionalidad WHERE (func_offline=0 or func_offline=2)");
    $array = array();
    $estado = $use ->execute();
    if($estado){
        $count = $use->rowCount();
        $row = $use->fetchAll();
        if($count>0){
            foreach($row as $registro){
                $object = (object) array();
                $object->id =  $registro['func_id'];
            	$object->descripcion = $registro['func_descripcion'];
            	$object->orden = $registro['func_orden'];
            	$object->url = $registro['func_url'];
            	$object->icono = $registro['func_icono'];
            	$object->offline = $registro['func_offline'];
            	array_push($array, $object);
            }
            $data->funcionalidad = $array;
            
            //backup de rolfuncionalidad
            $use = $conexion->prepare("SELECT DISTINCT rf.* FROM rolfuncionalidad rf, funcionalidad f WHERE f.func_id=rf.func_id AND (f.func_offline=0 OR f.func_offline=2)");
            $array = array();
            $estado = $use ->execute();
            if($estado){
                $count = $use->rowCount();
                $row = $use->fetchAll();
                if($count>0){
                    foreach($row as $registro){
                        $object = (object) array();
                        $object->id =  $registro['rofu_id'];
                    	$object->funcionalidad = $registro['func_id'];
                    	$object->rol = $registro['rol_id'];
                    	$object->principal = $registro['rofu_principal'];
                    	array_push($array, $object);    
                    }
                    $data->rolfuncionalidad = $array;
                    
                    //backup de rol
                    $use = $conexion->prepare("SELECT DISTINCT * FROM rol");
                    $array = array();
                    $estado = $use ->execute();
                    $row = $use->fetchAll();
                    if($estado){
                        foreach($row as $registro){
                            $object = (object) array();
                            $object->id =  $registro['rol_id'];
                        	$object->descripcion = $registro['rol_descripcion'];
                        	$object->prioridad = $registro['rol_prioridad'];
                        	array_push($array, $object);    
                        }
                        $data->rol = $array;
                        
                        //backup de usuarios
                        $use = $conexion->prepare("SELECT DISTINCT * FROM usuario");
                        $array = array();
                        $estado = $use ->execute();
                        $row = $use->fetchAll();
                        if($estado){
                            foreach($row as $registro){
                                $object = (object) array();
                                $object->id =  $registro['usua_id'];
                            	$object->nombres = $registro['usua_nombres'];
                            	$object->apellidos = $registro['usua_apellidos'];
                            	$object->documento = $registro['usua_documento'];
                            	$object->fechanacimiento = $registro['usua_fechanacimiento'];
                            	$object->telefono = $registro['usua_telefono'];
                            	$object->correo = $registro['usua_correo'];
                            	$object->clave = $registro['usua_clave'];
                            	$object->tipodocumento = $registro['tido_id'];
                            	$object->registro = $registro['usua_registro'];
                            	$object->fecharegistro = $registro['usua_fecharegistro'];
                            	$object->punto = $registro['punt_id'];
                            	array_push($array, $object);    
                            }
                            $data->usuario = $array;
                            
                            //BACKUP usuario rol
                            $use = $conexion->prepare("SELECT DISTINCT * FROM usuariorol");
                            $array = array();
                            $estado = $use ->execute();
                            $row = $use->fetchAll();
                            if($estado){
                                foreach($row as $registro){
                                    $object = (object) array();
                                    $object->id =  $registro['usro_id'];
                                	$object->rol = $registro['rol_id'];
                                	$object->usuario = $registro['usua_id'];
                                	array_push($array, $object);          
                                }
                                $data->usuariorol = $array;
                                
                                //backup para tipodocumento
                                $use = $conexion->prepare("SELECT DISTINCT * FROM tipodocumento");
                                $array = array();
                                $estado = $use ->execute();
                                $row = $use->fetchAll();
                                if($estado){
                                    foreach($row as $registro){
                                        $object = (object) array();
                                        $object->id =  $registro['tido_id'];
                                    	$object->descripcion = $registro['tido_descripcion'];
                                    	$object->abreviacion = $registro['tido_abreviacion'];
                                    	array_push($array, $object);         
                                    }
                                    $data->tipodocumento = $array;
                                    
                                    //backup para unidad
                                    $use = $conexion->prepare("SELECT DISTINCT * FROM unidad");
                                    $array = array();
                                    $estado = $use ->execute();
                                    $row = $use->fetchAll();
                                    if($estado){
                                        foreach($row as $registro){
                                            $object = (object) array();
                                            $object->id =  $registro['unid_id'];
                                        	$object->descripcion = $registro['unid_descripcion'];
                                        	$object->abreviacion = $registro['unid_abreviacion'];
                                        	array_push($array, $object);    
                                        }
                                        $data->unidad = $array;
                                        
                                        //backup para material
                                        $use = $conexion->prepare("SELECT DISTINCT * FROM material");
                                        $array = array();
                                        $estado = $use ->execute();
                                        $row = $use->fetchAll();
                                        if($estado){
                                            foreach($row as $registro){
                                                $object = (object) array();
                                                $object->id =  $registro['mate_id'];
                                            	$object->descripcion = $registro['mate_descripcion'];
                                            	$object->unidad = $registro['unid_id'];
                                            	array_push($array, $object);         
                                            }
                                            $data->material = $array;
                                            
                                            //backup para zona
                                            $use = $conexion->prepare("SELECT DISTINCT * FROM zona");
                                            $array = array();
                                            $estado = $use ->execute();
                                            $row = $use->fetchAll();
                                            if($estado){
                                                foreach($row as $registro){
                                                    $object = (object) array();
                                                    $object->id =  $registro['zona_id'];
                                                	$object->descripcion = $registro['zona_descripcion'];
                                                	array_push($array, $object);         
                                                }
                                                $data->zona = $array;
                                                
                                                //backup para punto
                                                $use = $conexion->prepare("SELECT DISTINCT * FROM punto");
                                                $array = array();
                                                $estado = $use ->execute();
                                                $row = $use->fetchAll();
                                                if($estado){
                                                    foreach($row as $registro){
                                                        $object = (object) array();
                                                        $object->id =  $registro['punt_id'];
                                                    	$object->descripcion = $registro['punt_descripcion'];
                                                    	$object->codigo = $registro['punt_codigo'];
                                                    	$object->direccion = $registro['punt_direccion'];
                                                    	$object->nit = $registro['punt_nit'];
                                                    	$object->razonsocial = $registro['punt_razonsocial'];
                                                    	$object->zona = $registro['zona_id'];
                                                    	array_push($array, $object);     
                                                    }
                                                    $data->punto = $array;
                                                    
                                                    //backup para vehiculo
                                                    $use = $conexion->prepare("SELECT DISTINCT * FROM vehiculo");
                                                    $array = array();
                                                    $estado = $use ->execute();
                                                    $row = $use->fetchAll();
                                                    if($estado){
                                                        foreach($row as $registro){
                                                            $object = (object) array();
                                                            $object->id =  $registro['vehi_id'];
                                                        	$object->placa = $registro['vehi_placa'];
                                                        	$object->placatrailer = $registro['vehi_placatrailer'];
                                                        	$object->descripcion = $registro['vehi_descripcion'];
                                                        	$object->vencimientosoat = $registro['vehi_vencimientosoat'];
                                                        	$object->vencimientogases = $registro['vehi_vencimientogases'];
                                                        	$object->descripcion = $registro['vehi_descripcion'];
                                                        	$object->propietario = $registro['vehi_propietario'];
                                                        	$object->empresatransportadora = $registro['emtr_id'];
                                                        	array_push($array, $object);     
                                                        }
                                                        $data->vehiculo = $array;
                                                        
                                                        //backup para empresatransportadora
                                                        $use = $conexion->prepare("SELECT DISTINCT * FROM empresatransportadora");
                                                        $array = array();
                                                        $estado = $use ->execute();
                                                        $row = $use->fetchAll();
                                                        if($estado){
                                                            foreach($row as $registro){
                                                                $object = (object) array();
                                                                $object->id =  $registro['emtr_id'];
                                                            	$object->descripcion = $registro['emtr_descripcion'];
                                                            	array_push($array, $object);     
                                                            }
                                                            $data->empresatransportadora = $array;
                                                            
                                                            //backup para ruta
                                                            // $use = $conexion->prepare("SELECT DISTINCT r.*,v.vehi_placa FROM ruta r,vehiculo v
                                                            //                             where (r.ruta_fechallegada IS NULL 
                                                            //                             OR (date(r.ruta_fechallegada) = ? 
                                                            //                             AND r.usua_destino=?) OR (date(r.ruta_fechallegada)=? AND r.ruta_tipo=?)) AND r.vehi_id=v.vehi_id");
                                                            $use = $conexion ->prepare("select distinct r.*,v.vehi_placa from ruta r,vehiculo v
                                                                                    where (((r.usua_origen=$idSesion AND r.ruta_tipo=1 OR
                                                                                          r.usua_destino=$idSesion AND r.ruta_tipo=2 OR
                                                                                          r.usua_origen=$idSesion AND r.ruta_tipo=3) 
                                                                                           AND date(r.ruta_fechacreacion)
                                                                                           between '$date2' AND '$date')
                                                                                    	  OR (r.ruta_tipo=1 AND r.pude_id=(select punt_id from usuario where usua_id=$idSesion) AND r.ruta_fechallegada IS NOT NULL))
                                                                                    AND r.vehi_id=v.vehi_id");
                                                            // $use->bindValue(1, $date);
                                                            // $use->bindValue(2, $idSesion);
                                                            // $use->bindValue(3, $date);
                                                            // $use->bindValue(4, 2);
                                                            $array = array();
                                                            $estado = $use ->execute();
                                                            $row = $use->fetchAll();
                                                            if($estado){
                                                                foreach($row as $registro){
                                                                    $object = (object) array();
                                                                    $object->id =  $registro['ruta_id'];
                                                                	$object->referencia = $registro['ruta_codigo'];
                                                                	$object->cantidadmaterial = $registro['ruta_cantidadmaterial'];
                                                                	$object->fechacreacion = $registro['ruta_fechacreacion'];
                                                                	$object->fechasalida = $registro['ruta_fechasalida'];
                                                                	$object->fechallegada = $registro['ruta_fechallegada'];
                                                                	$object->puntoorigen = $registro['puor_id'];
                                                                	$object->puntodestino = $registro['pude_id'];
                                                                	$object->vehiculo = $registro['vehi_id'];
                                                                	$object->placavehiculo = $registro['vehi_placa'];
                                                                	$object->material = $registro['mate_id'];
                                                                	$object->conductor = $registro['usua_conductor'];
                                                                	$object->usuarioorigen = $registro['usua_origen'];
                                                                	$object->usuariodestino = $registro['usua_destino'];
                                                                	$object->pesosalidavacio = $registro['ruta_pesosalidavacio'];
                                                                	$object->preciomaterial = $registro['ruta_preciomaterial'];
                                                                	$object->preciotransporte = $registro['ruta_preciotransporte'];
                                                                	$object->pesosalidalleno = $registro['ruta_pesosalidalleno'];
                                                                	$object->observaciones = $registro['ruta_observaciones'];
                                                                	$object->pesosalidaneto = $registro['ruta_pesosalidaneto'];
                                                                	$object->pesollegadavacio = $registro['ruta_pesollegadavacio'];
                                                                	$object->factura = $registro['ruta_factura'];
                                                                	$object->pesollegadalleno = $registro['ruta_pesollegadalleno'];
                                                                	$object->pesollegadaneto = $registro['ruta_pesollegadaneto'];
                                                                	$object->tipo = $registro['ruta_tipo'];
                                                                	$object->cantidadmaterialllegada = $registro['ruta_cantidadllegada'];
                                                                	$object->precinto = $registro['ruta_precinto'];
                                                                	$object->observacionessalida = $registro['ruta_observacionessalida'];
                                                                	$object->empresatransportadora = $registro['emtr_id'];
                                                                	$object->placatrailer = $registro['emtr_id'];
                                                                	array_push($array, $object);     
                                                                }
                                                                $data->ruta = $array;
                                                                //backup para configuracion
                                                                $use = $conexion->prepare("SELECT DISTINCT * FROM configuracion");
                                                                $array = array();
                                                                $estado = $use ->execute();
                                                                $row = $use->fetchAll();
                                                                if($estado){
                                                                    foreach($row as $registro){
                                                                        $object = (object) array();
                                                                        $object->id =  $registro['conf_id'];
                                                                    	$object->nombreempresa = $registro['conf_nombreempresa'];
                                                                    	$object->nitempresa = $registro['conf_nitempresa'];
                                                                    	$object->direccionempresa = $registro['conf_direccionempresa'];
                                                                    	array_push($array, $object);         
                                                                    }
                                                                    $data->configuracion = $array;
                                                                    $data->estado = "OK";
                                                                }
                                                                else{
                                                                    $mensaje = "Error al recuperar los datos del servidor, intente nuevamente";
                                                                    $estado = "ERROR";
                                                                    $data->estado = $estado;
                                                                    $data->mensaje= $mensaje;    
                                                                }
                                                            }
                                                            else{
                                                                $mensaje = "Error al recuperar los datos del servidor, intente nuevamente";
                                                                $estado = "ERROR";
                                                                $data->estado = $estado;
                                                                $data->mensaje= $mensaje;
                                                            }
                                                        }
                                                        else{
                                                            $mensaje = "Error al recuperar los datos del servidor, intente nuevamente";
                                                            $estado = "ERROR";
                                                            $data->estado = $estado;
                                                            $data->mensaje= $mensaje;
                                                        }
                                                        
                                                    }
                                                    else{
                                                        $mensaje = "Error al recuperar los datos del servidor, intente nuevamente";
                                                        $estado = "ERROR";
                                                        $data->estado = $estado;
                                                        $data->mensaje= $mensaje;
                                                    }
                                                }
                                                else{
                                                    $mensaje = "Error al recuperar los datos del servidor, intente nuevamente";
                                                    $estado = "ERROR"; 
                                                    $data->estado = $estado;
                                                    $data->mensaje= $mensaje;
                                                }
                                            }
                                            else{
                                                $mensaje = "Error al recuperar los datos del servidor, intente nuevamente";
                                                $estado = "ERROR";
                                                $data->estado = $estado;
                                                $data->mensaje= $mensaje;
                                            }
                                        }
                                        else{
                                            $mensaje = "Error al recuperar los datos del servidor, intente nuevamente";
                                            $estado = "ERROR";
                                            $data->estado = $estado;
                                            $data->mensaje= $mensaje;
                                        }
                                        
                                    }
                                    else{
                                        $mensaje = "Error al recuperar los datos del servidor, intente nuevamente";
                                        $estado = "ERROR";
                                        $data->estado = $estado;
                                        $data->mensaje= $mensaje;   
                                    }
                                }
                                else{
                                    $mensaje = "Error al recuperar los datos del servidor, intente nuevamente";
                                    $estado = "ERROR";
                                    $data->estado = $estado;
                                    $data->mensaje= $mensaje;     
                                }
                                
                            }
                            else{
                                $mensaje = "Error al recuperar los datos del servidor, intente nuevamente";
                                $estado = "ERROR";
                                $data->estado = $estado;
                                $data->mensaje= $mensaje;     
                            }
                            
                        }
                        else{
                            $mensaje = "Error al recuperar los datos del servidor, intente nuevamente";
                            $estado = "ERROR";
                            $data->estado = $estado;
                            $data->mensaje= $mensaje;  
                        }
                    }
                    else{
                        $mensaje = "Error al recuperar los datos del servidor, intente nuevamente";
                        $estado = "ERROR";
                        $data->estado = $estado;
                        $data->mensaje= $mensaje;   
                    }
                }          
            }
            else{
                $mensaje = "Error al recuperar los datos del servidor, intente nuevamente";
                $estado = "ERROR";
                $data->estado = $estado;
                $data->mensaje= $mensaje; 
            }
        }
    }
    else{
        $mensaje = "Error al recuperar los datos del servidor, intente nuevamente";
        $estado = "ERROR";
        $data->estado = $estado;
        $data->mensaje= $mensaje; 
    }
    $conexion = null;
    print_r(json_encode($data));
?>