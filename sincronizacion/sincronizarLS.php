<?php
    error_reporting(0);
    header('Access-Control-Allow-Origin: *');
    date_default_timezone_set("America/Bogota");
    //CONEXION QUE PERMITE LA TRANSACCION
    $conexion = new PDO('mysql:host=localhost;dbname=control;charset=utf8', 'root', '', array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false
    ));
    $usuario = $_POST["usuario"];
    $ruta = $_POST["ruta"];
    $vehiculo = $_POST["vehiculo"];
    $data = (object) array();
    $conexion->beginTransaction();
    try{
        if(count($usuario)>0){
            for($i=0;$i<count($usuario);$i++){
                $use = $conexion->prepare("INSERT INTO usuario(usua_nombres,
                                                          usua_apellidos,
                                                          usua_documento,
                                                          tido_id,
                                                          usua_fechanacimiento,
                                                          usua_telefono,
                                                          usua_correo,
                                                          usua_clave,
                                                          usua_registro,
                                                          usua_fecharegistro) 
                                            SELECT ?,?,?,?,?,?,?,?,?,? FROM DUAL
                                            WHERE NOT EXISTS (SELECT 1 FROM usuario WHERE tido_id=? AND usua_documento=?)");
                $use->bindParam(1, mb_convert_case($usuario[$i]["nombres"],MB_CASE_TITLE, "UTF-8"));
                $use->bindParam(2, mb_convert_case($usuario[$i]["apellidos"],MB_CASE_TITLE, "UTF-8"));
                $use->bindParam(3, $usuario[$i]["documento"]);
                $use->bindParam(4, $usuario[$i]["tipodocumento"]);
                $use->bindParam(5, $usuario[$i]["fechanacimiento"]);
                $use->bindParam(6, $usuario[$i]["telefono"]);
                $use->bindParam(7, $usuario[$i]["correo"]);
                $use->bindParam(8, $usuario[$i]["clave"]);
                $use->bindParam(9, $usuario[$i]["registro"]);
                $use->bindParam(10, $usuario[$i]["fecharegistro"]);
                $use->bindParam(11, $usuario[$i]["tipodocumento"]);
                $use->bindParam(12, $usuario[$i]["documento"]);
                $use ->execute();
            }
        }
        if(count($vehiculo)>0){
            for($i=0;$i<count($vehiculo);$i++){
                $use = $conexion->prepare("INSERT INTO vehiculo(vehi_placa,
                                                              vehi_placatrailer,
                                                              vehi_vencimientosoat,
                                                              vehi_vencimientogases,
                                                              emtr_id,
                                                              vehi_propietario)
                                                              SELECT ?,?,?,?,?,(SELECT usua_id FROM usuario WHERE tido_id=? AND usua_documento=?) FROM DUAL
                                                              WHERE NOT EXISTS (SELECT 1 FROM vehiculo WHERE vehi_placa = ?)");        
                $use->bindParam(1, mb_convert_case($vehiculo[$i]["placa"],MB_CASE_UPPER, "UTF-8"));
                $use->bindParam(2, mb_convert_case($vehiculo[$i]["placatrailer"],MB_CASE_UPPER, "UTF-8"));
                $use->bindParam(3, $vehiculo[$i]["vencimientosoat"]);
                $use->bindParam(4, $vehiculo[$i]["vencimientogases"]);
                $use->bindParam(5, $vehiculo[$i]["empresatransportadora"]);
                $use->bindParam(6, $vehiculo[$i]["tipodocumentopropietario"]);
                $use->bindParam(7, $vehiculo[$i]["documentopropietario"]);
                $use->bindParam(8, mb_convert_case($vehiculo[$i]["placa"],MB_CASE_UPPER, "UTF-8"));
                $use ->execute();
            }
        }
        if(count($ruta)>0){
            for($i=0;$i<count($ruta);$i++){
                //compras
                if($ruta[$i]["tipo"]==2){
                        $use = $conexion->prepare("INSERT INTO ruta(ruta_codigo,
                                                                    ruta_tipo,
                                                                    ruta_fechacreacion,
                                                                    ruta_fechallegada,
                                                                    puor_id,
                                                                    pude_id,
                                                                    vehi_id,
                                                                    mate_id,
                                                                    usua_conductor,
                                                                    usua_destino,
                                                                    ruta_pesollegadavacio,
                                                                    ruta_pesollegadalleno,
                                                                    ruta_pesollegadaneto,
                                                                    ruta_cantidadllegada,
                                                                    ruta_observaciones,
                                                                    ruta_placatrailer,
                                                                    emtr_id)
                                                                SELECT ?,?,?,?,?,?,(SELECT vehi_id FROM vehiculo WHERE vehi_placa=?),
                                                                        ?,(SELECT usua_id FROM usuario WHERE tido_id=? AND usua_documento=?),
                                                                        ?,?,?,?,?,?,?,? FROM DUAL
                                                                WHERE NOT EXISTS (SELECT 1 FROM ruta WHERE ruta_codigo=?)");
                        $use->bindParam(1, $ruta[$i]["codigo"]);
                        $use->bindParam(2, $ruta[$i]["tipo"]);
                        $use->bindParam(3, $ruta[$i]["fechacreacion"]);
                        $use->bindParam(4, $ruta[$i]["fechallegada"]);
                        $use->bindParam(5, $ruta[$i]["origen"]);
                        $use->bindParam(6, $ruta[$i]["destino"]);
                        $use->bindParam(7, $ruta[$i]["placa"]);
                        $use->bindParam(8, $ruta[$i]["material"]);
                        $use->bindParam(9, $ruta[$i]["tipodocumentoconductor"]);
                        $use->bindParam(10, $ruta[$i]["documentoconductor"]);
                        $use->bindParam(11, $ruta[$i]["usuariodestino"]);
                        $use->bindParam(12, $ruta[$i]["pesollegadavacio"]);
                        $use->bindParam(13, $ruta[$i]["pesollegadalleno"]);
                        $use->bindParam(14, $ruta[$i]["pesollegadaneto"]);
                        $use->bindParam(15, $ruta[$i]["cantidadmaterialllegada"]);
                        $use->bindParam(16, $ruta[$i]["observaciones"]);
                        $use->bindParam(17, $ruta[$i]["placatrailer"]);
                        $use->bindParam(18, $ruta[$i]["empresatransportadora"]);
                        $use->bindParam(19, $ruta[$i]["codigo"]);
                        $use ->execute();
                        $idruta = $conexion->lastInsertId();
                        $carpeta = "../ruta/img/".$idruta.'/';
                        if (!file_exists($carpeta)) {
                            mkdir($carpeta, 0777, true);
                        }
                        if($ruta[$i]["imagenllegada"]!=""){
                    		$path = '../ruta/img/'.$idruta.'/';
                        	$img = $ruta[$i]["imagenllegada"];
                        	$img = str_replace('data:image/png;base64,', '', $img);
                        	$img = str_replace('data:image/jpeg;base64,', '', $img);
                        	$img = str_replace(' ', '+', $img);
                        	$dataimg = base64_decode($img);
                        	$file = $path.'llegada.png';
                        	$success = file_put_contents($file, $dataimg);
            		    }
                    
                }
                else
                //ventas
                if($ruta[$i]["tipo"]==3){
                    $use = $conexion->prepare("INSERT INTO ruta(ruta_codigo,
                                                                ruta_tipo,
                                                                ruta_fechacreacion,
                                                                ruta_fechasalida,
                                                                puor_id,
                                                                pude_id,
                                                                vehi_id,
                                                                mate_id,
                                                                usua_conductor,
                                                                usua_origen,
                                                                ruta_pesosalidavacio,
                                                                ruta_pesosalidalleno,
                                                                ruta_pesosalidaneto,
                                                                ruta_cantidadmaterial,
                                                                ruta_observacionessalida,
                                                                ruta_factura,
                                                                ruta_precinto,
                                                                ruta_placatrailer,
                                                                emtr_id)
                                                            SELECT ?,?,?,?,?,?,(SELECT vehi_id FROM vehiculo WHERE vehi_placa=?),
                                                                    ?,(SELECT usua_id FROM usuario WHERE tido_id=? AND usua_documento=?),
                                                                    ?,?,?,?,?,?,?,?,?,? FROM DUAL
                                                            WHERE NOT EXISTS (SELECT 1 FROM ruta WHERE ruta_codigo=?)");
                    $use->bindParam(1, $ruta[$i]["codigo"]);
                    $use->bindParam(2, $ruta[$i]["tipo"]);
                    $use->bindParam(3, $ruta[$i]["fechacreacion"]);
                    $use->bindParam(4, $ruta[$i]["fechasalida"]);
                    $use->bindParam(5, $ruta[$i]["origen"]);
                    $use->bindParam(6, $ruta[$i]["destino"]);
                    $use->bindParam(7, $ruta[$i]["placa"]);
                    $use->bindParam(8, $ruta[$i]["material"]);
                    $use->bindParam(9, $ruta[$i]["tipodocumentoconductor"]);
                    $use->bindParam(10, $ruta[$i]["documentoconductor"]);
                    $use->bindParam(11, $ruta[$i]["usuarioorigen"]);
                    $use->bindParam(12, $ruta[$i]["pesosalidavacio"]);
                    $use->bindParam(13, $ruta[$i]["pesosalidalleno"]);
                    $use->bindParam(14, $ruta[$i]["pesosalidaneto"]);
                    $use->bindParam(15, $ruta[$i]["cantidadmaterial"]);
                    $use->bindParam(16, $ruta[$i]["observacionessalida"]);
                    $use->bindParam(17, $ruta[$i]["factura"]);
                    $use->bindParam(18, $ruta[$i]["precinto"]);
                    $use->bindParam(19, $ruta[$i]["placatrailer"]);
                    $use->bindParam(20, $ruta[$i]["empresatransportadora"]);
                    $use->bindParam(21, $ruta[$i]["codigo"]);
                    $use ->execute();
                    $idruta = $conexion->lastInsertId();
                    $carpeta = "../ruta/img/".$idruta.'/';
                    if (!file_exists($carpeta)) {
                        mkdir($carpeta, 0777, true);
                    }
                    if($ruta[$i]["imagensalida"]!=""){
                		$path = '../ruta/img/'.$idruta.'/';
                    	$img = $ruta[$i]["imagensalida"];
                    	$img = str_replace('data:image/png;base64,', '', $img);
                    	$img = str_replace('data:image/jpeg;base64,', '', $img);
                    	$img = str_replace(' ', '+', $img);
                    	$dataimg = base64_decode($img);
                    	$file = $path.'salida.png';
                    	$success = file_put_contents($file, $dataimg);
        		    }
                }
                else{
                    //despachos
                    $use = $conexion->prepare("SELECT * FROM ruta WHERE ruta_codigo=?");
                    $use->bindParam(1, $ruta[$i]["codigo"]);
                    $use ->execute();
                    $row = $use->fetchAll();
                    $count = $use->rowCount();
                    if($count>0){
                        $fechasalida = $row[0]['ruta_fechasalida'];
                        $fechallegada = $row[0]['ruta_fechallegada'];
                        $idruta = $row[0]['ruta_id'];
                        //marcar una llegada que ya esta en la nube
                        if($fechasalida!=""&&$ruta[$i]["fechallegada"]!=""){
                            $use = $conexion->prepare("UPDATE ruta SET ruta_fechallegada=?, 
                                                                        pude_id= ?, 
                                                                        usua_destino = ?,
                                                                        ruta_pesollegadavacio = ?, 
                                                                        ruta_pesollegadalleno = ?, 
                                                                        ruta_pesollegadaneto=?, 
                                                                        ruta_observaciones=?,
                                                                        ruta_cantidadllegada=? 
                                                                        WHERE ruta_codigo=?");
                            $use->bindParam(1, $ruta[$i]["fechallegada"]);
                            $use->bindParam(2, $ruta[$i]["destino"]);
                            $use->bindParam(3, $ruta[$i]["usuariodestino"]);
                            $use->bindParam(4, $ruta[$i]["pesollegadavacio"]);
                            $use->bindParam(5, $ruta[$i]["pesollegadalleno"]);
                            $use->bindParam(6, $ruta[$i]["pesollegadaneto"]);
                            $use->bindParam(7, $ruta[$i]["observaciones"]);
                            $use->bindParam(8, $ruta[$i]["cantidadmaterialllegada"]);
                            $use->bindParam(9, $ruta[$i]["codigo"]);
                            $use ->execute();
                            $carpeta = "../ruta/img/".$idruta.'/';
                            if (!file_exists($carpeta)) {
                                mkdir($carpeta, 0777, true);
                            }
                            if($ruta[$i]["imagenllegada"]!=""){
                        		$path = '../ruta/img/'.$idruta.'/';
                            	$img = $ruta[$i]["imagenllegada"];
                            	$img = str_replace('data:image/png;base64,', '', $img);
                            	$img = str_replace('data:image/jpeg;base64,', '', $img);
                            	$img = str_replace(' ', '+', $img);
                            	$dataimg = base64_decode($img);
                            	$file = $path.'llegada.png';
                            	$success = file_put_contents($file, $dataimg);
                    		}
                        }else
                        //registrar ruta cuya recepcion ha sido marcada en linea
                        if($fechallegada!=""&&$ruta[$i]["fechasalida"]!=""){
                            $use = $conexion->prepare("UPDATE ruta SET ruta_cantidadmaterial=?, 
                                                                        ruta_fechacreacion= ?, 
                                                                        ruta_fechasalida = ?,
                                                                        puor_id = ?,
                                                                        pude_id = ?, 
                                                                        vehi_id = (SELECT vehi_id FROM vehiculo WHERE vehi_placa=?), 
                                                                        mate_id=?, 
                                                                        usua_conductor=(SELECT usua_id FROM usuario WHERE tido_id=? AND usua_documento=?),
                                                                        ruta_pesosalidavacio=?,
                                                                        ruta_pesosalidalleno=?,
                                                                        ruta_pesosalidaneto=?,
                                                                        ruta_observacionessalida=?,
                                                                        ruta_precinto=?,
                                                                        usua_origen=?,
                                                                        ruta_placatrailer = ?,
                                                                        emtr_id = ?
                                                                        WHERE ruta_codigo=?");
                            $use->bindParam(1, $ruta[$i]["cantidadmaterial"]);
                            $use->bindParam(2, $ruta[$i]["fechacreacion"]);
                            $use->bindParam(3, $ruta[$i]["fechasalida"]);
                            $use->bindParam(4, $ruta[$i]["origen"]);
                            $use->bindParam(5, $ruta[$i]["destino"]);
                            $use->bindParam(6, $ruta[$i]["placa"]);
                            $use->bindParam(7, $ruta[$i]["material"]);
                            $use->bindParam(8, $ruta[$i]["tipodocumentoconductor"]);
                            $use->bindParam(9, $ruta[$i]["documentoconductor"]);
                            $use->bindParam(10, $ruta[$i]["pesosalidavacio"]); 
                            $use->bindParam(11, $ruta[$i]["pesosalidalleno"]);
                            $use->bindParam(12, $ruta[$i]["pesosalidaneto"]);
                            $use->bindParam(13, $ruta[$i]["observacionessalida"]);
                            $use->bindParam(14, $ruta[$i]["precinto"]);
                            $use->bindParam(15, $ruta[$i]["usuarioorigen"]);
                            $use->bindParam(16, $ruta[$i]["placatrailer"]);
                            $use->bindParam(17, $ruta[$i]["empresatransportadora"]);
                            $use->bindParam(18, $ruta[$i]["codigo"]);
                            $use ->execute();
                            $carpeta = "../ruta/img/".$idruta.'/';
                            if (!file_exists($carpeta)) {
                                mkdir($carpeta, 0777, true);
                            }
                            if($ruta[$i]["imagensalida"]!=""){
                        		$path = '../ruta/img/'.$idruta.'/';
                            	$img = $ruta[$i]["imagensalida"];
                            	$img = str_replace('data:image/png;base64,', '', $img);
                            	$img = str_replace('data:image/jpeg;base64,', '', $img);
                            	$img = str_replace(' ', '+', $img);
                            	$dataimg = base64_decode($img);
                            	$file = $path.'salida.png';
                            	$success = file_put_contents($file, $dataimg);
                    		}
                        }
                    }
                    else{
                        if($ruta[$i]["tiporegistro"]==1){
                            $use = $conexion->prepare("INSERT INTO ruta(ruta_codigo,
                                                                        ruta_cantidadmaterial,
                                                                        ruta_fechacreacion,
                                                                        ruta_fechasalida,
                                                                        puor_id,
                                                                        pude_id,
                                                                        vehi_id,
                                                                        mate_id,
                                                                        usua_conductor,
                                                                        usua_origen,
                                                                        ruta_pesosalidavacio,
                                                                        ruta_pesosalidalleno,
                                                                        ruta_pesosalidaneto,
                                                                        ruta_observacionessalida,
                                                                        ruta_precinto,
                                                                        ruta_placatrailer,
                                                                        emtr_id)
                                                        VALUES(?,?,?,?,?,?,(SELECT vehi_id FROM vehiculo
                                                                WHERE vehi_placa =?),?,(SELECT usua_id
                                                                FROM usuario WHERE tido_id=? AND usua_documento=?),
                                                                ?,?,?,?,?,?,?,?)");
                            $use->bindParam(1, $ruta[$i]["codigo"]);
                            $use->bindParam(2, $ruta[$i]["cantidadmaterial"]);
                            $use->bindParam(3, $ruta[$i]["fechacreacion"]);
                            $use->bindParam(4, $ruta[$i]["fechasalida"]);
                            $use->bindParam(5, $ruta[$i]["origen"]);
                            $use->bindParam(6, $ruta[$i]["destino"]);
                            $use->bindParam(7, $ruta[$i]["placa"]);
                            $use->bindParam(8, $ruta[$i]["material"]);
                            $use->bindParam(9, $ruta[$i]["tipodocumentoconductor"]);
                            $use->bindParam(10, $ruta[$i]["documentoconductor"]);
                            $use->bindParam(11, $ruta[$i]["usuarioorigen"]);
                            $use->bindParam(12, $ruta[$i]["pesosalidavacio"]);
                            $use->bindParam(13, $ruta[$i]["pesosalidalleno"]);
                            $use->bindParam(14, $ruta[$i]["pesosalidaneto"]);
                            $use->bindParam(15, $ruta[$i]["observacionessalida"]);
                            $use->bindParam(16, $ruta[$i]["precinto"]);
                            $use->bindParam(17, $ruta[$i]["placatrailer"]);
                            $use->bindParam(18, $ruta[$i]["empresatransportadora"]);
                            $use ->execute();
                            $idruta = $conexion->lastInsertId();
                            $carpeta = "../ruta/img/".$idruta.'/';
                            if (!file_exists($carpeta)) {
                                mkdir($carpeta, 0777, true);
                            }
                            if($ruta[$i]["imagensalida"]!=""){
                        		$path = '../ruta/img/'.$idruta.'/';
                            	$img = $ruta[$i]["imagensalida"];
                            	$img = str_replace('data:image/png;base64,', '', $img);
                            	$img = str_replace('data:image/jpeg;base64,', '', $img);
                            	$img = str_replace(' ', '+', $img);
                            	$dataimg = base64_decode($img);
                            	$file = $path.'salida.png';
                            	$success = file_put_contents($file, $dataimg);
                    		}
                        }
                        else{
                            if($ruta[$i]["tiporegistro"]==2&&($ruta[$i]["usuarioorigen"]!=$ruta[$i]["usuariodestino"])){
                                $use = $conexion->prepare("INSERT INTO ruta(ruta_fechallegada,
                                                                            usua_destino,
                                                                            ruta_pesollegadavacio, 
                                                                            ruta_pesollegadalleno, 
                                                                            ruta_pesollegadaneto, 
                                                                            ruta_observaciones,
                                                                            ruta_cantidadllegada, 
                                                                            ruta_codigo)
                                                            VALUES(?,?,?,?,?,?,?,?)");
                                $use->bindParam(1, $ruta[$i]["fechallegada"]);
                                $use->bindParam(2, $ruta[$i]["usuariodestino"]);
                                $use->bindParam(3, $ruta[$i]["pesollegadavacio"]);
                                $use->bindParam(4, $ruta[$i]["pesollegadalleno"]);
                                $use->bindParam(5, $ruta[$i]["pesollegadaneto"]);
                                $use->bindParam(6, $ruta[$i]["observaciones"]);
                                $use->bindParam(7, $ruta[$i]["cantidadmaterialllegada"]);
                                $use->bindParam(8, $ruta[$i]["codigo"]);
                                $use ->execute();
                                $idruta = $conexion->lastInsertId();
                                $carpeta = "../ruta/img/".$idruta.'/';
                                if (!file_exists($carpeta)) {
                                    mkdir($carpeta, 0777, true);
                                }
                                if($ruta[$i]["imagenllegada"]!=""){
                            		$path = '../ruta/img/'.$idruta.'/';
                                	$img = $ruta[$i]["imagenllegada"];
                                	$img = str_replace('data:image/png;base64,', '', $img);
                                	$img = str_replace('data:image/jpeg;base64,', '', $img);
                                	$img = str_replace(' ', '+', $img);
                                	$dataimg = base64_decode($img);
                                	$file = $path.'llegada.png';
                                	$success = file_put_contents($file, $dataimg);
                    		    }
                            }
                            else{
                                $use = $conexion->prepare("INSERT INTO ruta(ruta_codigo,
                                                                            ruta_cantidadmaterial,
                                                                            ruta_fechacreacion,
                                                                            ruta_fechasalida,
                                                                            ruta_fechallegada,
                                                                            puor_id,
                                                                            pude_id,
                                                                            vehi_id,
                                                                            mate_id,
                                                                            usua_conductor,
                                                                            usua_origen,
                                                                            usua_destino,
                                                                            ruta_pesosalidavacio,
                                                                            ruta_pesosalidalleno,
                                                                            ruta_pesosalidaneto,
                                                                            ruta_pesollegadavacio,
                                                                            ruta_pesollegadalleno,
                                                                            ruta_pesollegadaneto,
                                                                            ruta_cantidadllegada,
                                                                            ruta_observaciones,
                                                                            ruta_observacionessalida,
                                                                            ruta_precinto,
                                                                            ruta_placatrailer,
                                                                            emtr_id)
                                                                VALUES(?,?,?,?,?,?,?,(SELECT vehi_id FROM vehiculo WHERE vehi_placa=?),?,(SELECT usua_id FROM usuario 
                                                                        WHERE tido_id=? AND usua_documento=?),?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                                $use->bindParam(1, $ruta[$i]["codigo"]);
                                $use->bindParam(2, $ruta[$i]["cantidadmaterial"]);
                                $use->bindParam(3, $ruta[$i]["fechacreacion"]);
                                $use->bindParam(4, $ruta[$i]["fechasalida"]);
                                $use->bindParam(5, $ruta[$i]["fechallegada"]);
                                $use->bindParam(6, $ruta[$i]["origen"]);
                                $use->bindParam(7, $ruta[$i]["destino"]);
                                $use->bindParam(8, $ruta[$i]["placa"]);
                                $use->bindParam(9, $ruta[$i]["material"]);
                                $use->bindParam(10, $ruta[$i]["tipodocumentoconductor"]);
                                $use->bindParam(11, $ruta[$i]["documentoconductor"]);
                                $use->bindParam(12, $ruta[$i]["usuarioorigen"]);
                                $use->bindParam(13, $ruta[$i]["usuariodestino"]);
                                $use->bindParam(14, $ruta[$i]["pesosalidavacio"]);
                                $use->bindParam(15, $ruta[$i]["pesosalidalleno"]);
                                $use->bindParam(16, $ruta[$i]["pesosalidaneto"]);
                                $use->bindParam(17, $ruta[$i]["pesollegadavacio"]);
                                $use->bindParam(18, $ruta[$i]["pesollegadalleno"]);
                                $use->bindParam(19, $ruta[$i]["pesollegadaneto"]);
                                $use->bindParam(20, $ruta[$i]["cantidadmaterialllegada"]);
                                $use->bindParam(21, $ruta[$i]["observaciones"]);
                                $use->bindParam(22, $ruta[$i]["observacionessalida"]);
                                $use->bindParam(23, $ruta[$i]["precinto"]);
                                $use->bindParam(24, $ruta[$i]["placatrailer"]);
                                $use->bindParam(25, $ruta[$i]["empresatransportadora"]);
                                $use ->execute(); 
                                $idruta = $conexion->lastInsertId();
                                $carpeta = "../ruta/img/".$idruta.'/';
                                if (!file_exists($carpeta)) {
                                    mkdir($carpeta, 0777, true);
                                }
                                if($ruta[$i]["imagensalida"]!=""){
                            		$path = '../ruta/img/'.$idruta.'/';
                                	$img = $ruta[$i]["imagensalida"];
                                	$img = str_replace('data:image/png;base64,', '', $img);
                                	$img = str_replace('data:image/jpeg;base64,', '', $img);
                                	$img = str_replace(' ', '+', $img);
                                	$dataimg = base64_decode($img);
                                	$file = $path.'salida.png';
                                	$success = file_put_contents($file, $dataimg);
                    		    }
                    		    if($ruta[$i]["imagenllegada"]!=""){
                            		$path = '../ruta/img/'.$idruta.'/';
                                	$img = $ruta[$i]["imagenllegada"];
                                	$img = str_replace('data:image/png;base64,', '', $img);
                                	$img = str_replace('data:image/jpeg;base64,', '', $img);
                                	$img = str_replace(' ', '+', $img);
                                	$dataimg = base64_decode($img);
                                	$file = $path.'llegada.png';
                                	$success = file_put_contents($file, $dataimg);
                    		    }
                            }
                        }
                    }
                }
            }
        }
        
        $data->estado = "OK";
        $data->mensaje = "TodO bien";
        $conexion->commit();
    }catch(Exception $e){
        $conexion->rollBack();
        $data->estado = "ERROR";
        $data->mensaje= $e->getMessage();
        //$data->mensaje= $mensajeaux;
    }
    $conexion = null;
    print_r(json_encode($data));
 ?>