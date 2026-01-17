<?php
/*------------------------------------------------------------------------------
FICHERO: modeloSocios.php
VERSION: PHP 7.3.21

DESCRIPCION: Este "Modelo" busca, inserta, y actualiza en (BBDD), pedida por
						 controladorSocios, y también por cPresidente, y cTesorero
LLAMADO: desde controladorSocios, cPresidente, y cTesorero					
	 
OBSERVACIONES:Necesita modeloUsuarios.php, y otras funciones diverersas  
              
2020-02-02: Muchas modificaciones para adaptarlo a PHP-PDO
2019-04-16: eliminarDatosSocios() Cambio el mensaje en pantalla después eliminar 
socio, para que sea distinto texto para socio que para el gestor en el caso de 
que se haya eliminado archivo con firma.
2018-10-06: Añado todo lo referente a eliminar archivo con la firma del socio 
para el caso de que tenga archivo
La parte de simpatizantes, no se utiliza la dejo por si algun día se utilizase,
aunque habría que reahacerla casi entera.
------------------------------------------------------------------------------*/
require_once "BBDD/MySQL/conexionMySQL.php";
require_once "BBDD/MySQL/modeloMySQL.php";
require_once './modelos/modeloUsuarios.php';
//------------------------------------------------------------------------------

/*---------------------------- Inicio buscarDatosSocioDesEncriptaCCCEX ---------
Descripcion: Busca todos los datos de un solo socio , con cuenta bancaria encriptada 
Llamada desde:controladorSocios.php:actualizarSocio(),mostrarDatosSocio()
              y eliminarSocio() y otros ....
OBSERVACIONES: EN ESTA VARIANTE -SE DESENCRIPTA- LAS CUENTAS BANCO NUMCUENTA NI CCEXTRANJERA

-- NO SE USA ACTUALMENTE 2014-01-24.	Para usar ahora habría que poner la cuenta IBAN
------------------------------------------------------------------------------*/
function buscarDatosSocioDesEncriptaCCCEX ($codUsuario,$anioCuota)
{$resBuscarDatosSocio['codError']='00000';
 $resBuscarDatosSocio['errorMensaje']='';
 $resBuscarDatosSocio['nomScript']='modeloSocios.php';
 $resBuscarDatosSocio['nomFuncion']='buscarDatosSocio';//se van a repetir
	$arrMensaje['textoCabecera']='Buscar datos del socio';		
	//$arrMensaje['textoBoton']='Salir de la aplicación';
	$arrMensaje['textoComentarios']="Error del sistema al buscar los datos del socio,
	vuelva a intentarlo pasado un tiempo ";	
	//$arrMensaje['enlaceBoton']='./index.php?controlador=controladorLogin&amp;accion=logOut';	
	
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	$conexionUsuariosDB=conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);
	if ($conexionUsuariosDB['codError']!=='00000')	
	{ $resBuscarDatosSocio=$conexionUsuariosDB;
   $arrMensaje['textoComentarios'].="Error del sistema al conectarse a la base de datos"; 	
	}
	else	//$conexionUsuariosDB['codError']=='00000'
	{$tablasBusqueda='USUARIO';//acaso sobre este apartado, para llegar aqui ya estará identificado como usuario
		$camposBuscados='*';
		$cadCondicionesBuscar=" WHERE USUARIO.CODUSER=\"".$codUsuario."\"";
    
		$resDatosUsuario=buscarEnTablas($tablasBusqueda,$cadCondicionesBuscar,
			                     								  $camposBuscados,$conexionUsuariosDB['conexionLink']);  
		//echo "<br /><br />1 modeloSocios:buscarDatosSocio:resDatosUsuario:";print_r($resDatosUsuario);	
																																		
		if ($resDatosUsuario['codError']!=='00000')
		{ $resBuscarDatosSocio['codError']=$resDatosUsuario['codError'];
    $resBuscarDatosSocio['errorMensaje']=$resDatosUsuario['errorMensaje'];
		  $resBuscarDatosSocio['nomScript']=$resDatosUsuario['nomScript'];//se van a repetir
		  $resBuscarDatosSocio['nomFuncion']=$resDatosUsuario['nomFuncion'];//se van a repetir
		}	
		elseif ($resDatosUsuario['numFilas']==0)
		{$resBuscarDatosSocio['codError']='80001'; //no encontrado
			$resBuscarDatosSocio['errorMensaje']="No existe ese socio";
		}	
	 else //$resDatosUsuario['codError']=='00000')
		{ 
			foreach ($resDatosUsuario['resultadoFilas'][0] as $indice => $contenido)                         
		 {//$resBuscarDatosSocio['valoresCampos']['datosFormUsuario'][$indice]['valorCampo'] = $contenido;		    
     $resBuscarDatosSocio['valoresCampos']['datosFormUsuario'][$indice]['valorCampo'] = 
				 $resDatosUsuario['resultadoFilas'][0][$indice]; 	        
		 } 			
		
	  $tablasBusqueda = 'PAIS, MIEMBRO LEFT JOIN PROVINCIA ON MIEMBRO.CODPROV=PROVINCIA.CODPROV';
			$camposBuscados = 'MIEMBRO.*, PAIS.NOMBREPAIS as nombrePaisDom, PROVINCIA.NOMPROVINCIA';		
			$cadCondicionesBuscar = " WHERE MIEMBRO.CODPAISDOM=PAIS.CODPAIS1 			
			                                AND MIEMBRO.CODUSER=\"".$codUsuario."\"";	
																													
			$resDatosMiembro = buscarEnTablas($tablasBusqueda,$cadCondicionesBuscar,
				                     							    	$camposBuscados,$conexionUsuariosDB['conexionLink']);
			//echo "<br /><br />2 modeloSocios:buscarDatosSocio:resDatosMiembro:";print_r($resDatosMiembro);	
																																									
			if ($resDatosMiembro['codError']!=='00000')
			{ $resBuscarDatosSocio['codError']=$resDatosMiembro['codError'];
	    $resBuscarDatosSocio['errorMensaje']=$resDatosMiembro['errorMensaje'];
			  $resBuscarDatosSocio['nomScript']=$resDatosMiembro['nomScript'];//se van a repetir
			  $resBuscarDatosSocio['nomFuncion']=$resDatosMiembro['nomFuncion'];//se van a repetir
			}	
			elseif ($resDatosMiembro['numFilas']==0)
			{$resBuscarDatosSocio['codError']='80001'; //no encontrado
				$resBuscarDatosSocio['errorMensaje']="No existe ese socio";
			}			
			else	//$resDatosMiembro['codError']=='00000'		
			{foreach ($resDatosMiembro['resultadoFilas'][0] as $indice => $contenido)                         
			 {//$resBuscarDatosSocio['valoresCampos']['datosFormMiembro'][$indice]['valorCampo']=$contenido;		    
	     $resBuscarDatosSocio['valoresCampos']['datosFormMiembro'][$indice]['valorCampo'] = 
					 $resDatosMiembro['resultadoFilas'][0][$indice]; 	        
			 }				
				//------- inicio buscar nombrePaisDoc, porque en la anterior join ya hay país Domicilio ------------------		
		  $tablasBusqueda = 'PAIS';
				$camposBuscados = 'PAIS.NOMBREPAIS as nombrePaisDoc';
				$cadCondicionesBuscar = 
				" WHERE PAIS.CODPAIS1=\"".$resBuscarDatosSocio['valoresCampos']['datosFormMiembro']['CODPAISDOC']['valorCampo']."\"";	
				$resDatosPaisDoc = buscarEnTablas($tablasBusqueda,$cadCondicionesBuscar,
				
					                     							    	$camposBuscados,$conexionUsuariosDB['conexionLink']);		
				//echo "<br /><br />3 modeloSocios:buscarDatosSocio:resDatosPaisDoc:";print_r($resDatosPaisDoc);	
				
				$resBuscarDatosSocio['valoresCampos']['datosFormMiembro']['nombrePaisDoc']['valorCampo']=
					  		$resDatosPaisDoc['resultadoFilas'][0]['nombrePaisDoc']; 
				
				if ($resDatosPaisDoc['codError'] !== '00000')
				{ $resBuscarDatosSocio['codError'] = $resDatosSocio['codError'];
		    $resBuscarDatosSocio['errorMensaje'] = $resDatosSocio['errorMensaje'];
				  $resBuscarDatosSocio['nomScript'] = $resDatosSocio['nomScript'];//se van a repetir
				  $resBuscarDatosSocio['nomFuncion'] = $resDatosSocio['nomFuncion'];//se van a repetir
				}	
				elseif ($resDatosPaisDoc['numFilas']==0)
				{$resBuscarDatosSocio['codError']='80001'; //no encontrado
					$resBuscarDatosSocio['errorMensaje']="No existe ese código país socio";
				}//-------------------fin buscar nombrePaisDoc ---------------------------
				else //$resDatosPaisDoc['codError']=='00000'
				{$fechaNacAux=$resDatosMiembro['resultadoFilas'][0]['FECHANAC'];
					$resBuscarDatosSocio['valoresCampos']['datosFormMiembro']['FECHANAC']['dia']['valorCampo']= substr($fechaNacAux,8,2);
					$resBuscarDatosSocio['valoresCampos']['datosFormMiembro']['FECHANAC']['mes']['valorCampo']= substr($fechaNacAux,5,2);
					$resBuscarDatosSocio['valoresCampos']['datosFormMiembro']['FECHANAC']['anio']['valorCampo']=substr($fechaNacAux,0,4);
						
					$resBuscarDatosSocio['valoresCampos']['datosFormDomicilio']['CODPAISDOM']['valorCampo']=
					  $resBuscarDatosSocio['valoresCampos']['datosFormMiembro']['CODPAISDOM']['valorCampo'];	
					$resBuscarDatosSocio['valoresCampos']['datosFormDomicilio']['nombrePaisDom']['valorCampo']=
					  $resBuscarDatosSocio['valoresCampos']['datosFormMiembro']['nombrePaisDom']['valorCampo'];	
					unset($resBuscarDatosSocio['valoresCampos']['datosFormMiembro']['nombrePaisDom']['valorCampo']);								
					$resBuscarDatosSocio['valoresCampos']['datosFormDomicilio']['DIRECCION']['valorCampo']=
					  $resBuscarDatosSocio['valoresCampos']['datosFormMiembro']['DIRECCION']['valorCampo'];					
	
					$resBuscarDatosSocio['valoresCampos']['datosFormDomicilio']['CP']['valorCampo']=
					  $resBuscarDatosSocio['valoresCampos']['datosFormMiembro']['CP']['valorCampo'];	
					$resBuscarDatosSocio['valoresCampos']['datosFormDomicilio']['LOCALIDAD']['valorCampo']=
					  $resBuscarDatosSocio['valoresCampos']['datosFormMiembro']['LOCALIDAD']['valorCampo'];	
					$resBuscarDatosSocio['valoresCampos']['datosFormDomicilio']['NOMPROVINCIA']['valorCampo']=
					  $resBuscarDatosSocio['valoresCampos']['datosFormMiembro']['NOMPROVINCIA']['valorCampo'];
				
					// inicio SOCIO	-----------------------
				 $tablasBusqueda='SOCIO,AGRUPACIONTERRITORIAL';
				 $camposBuscados='SOCIO.*,AGRUPACIONTERRITORIAL.NOMAGRUPACION';
					//$camposBuscados='SOCIO.*,AGRUPACIONTERRITORIAL.*';
					$cadCondicionesBuscar = " WHERE SOCIO.CODAGRUPACION=AGRUPACIONTERRITORIAL.CODAGRUPACION
					                             AND SOCIO.CODUSER=\"".$codUsuario."\"";  
																																		
					$resDatosSocio = buscarEnTablas($tablasBusqueda,$cadCondicionesBuscar,
						                     				      $camposBuscados,$conexionUsuariosDB['conexionLink']);	
																																					
	    //echo "<br><br>4 modeloSocios:buscarDatosSocio:resDatosSocio: ";print_r($resDatosSocio);	
																																																																		
					if ($resDatosSocio['codError'] !== '00000')
					{ $resBuscarDatosSocio['codError'] = $resDatosSocio['codError'];
			    $resBuscarDatosSocio['errorMensaje'] = $resDatosSocio['errorMensaje'];
					  $resBuscarDatosSocio['nomScript'] = $resDatosSocio['nomScript'];//se van a repetir
					  $resBuscarDatosSocio['nomFuncion'] = $resDatosSocio['nomFuncion'];//se van a repetir
					}	
					elseif ($resDatosSocio['numFilas']==0)
					{$resBuscarDatosSocio['codError']='80001'; //no encontrado
						$resBuscarDatosSocio['errorMensaje']="No existe ese socio";
					}
					else //$resDatosSocio['codError']=='00000'
					{foreach ($resDatosSocio['resultadoFilas'][0] as $indice => $contenido)                         
					 { $resBuscarDatosSocio['valoresCampos']['datosFormSocio'][$indice]['valorCampo'] = 
							 $resDatosSocio['resultadoFilas'][0][$indice]; 	        
					 }		
				  $fechaAltaAux=$resDatosSocio['resultadoFilas'][0]['FECHAALTA'];				
						$resBuscarDatosSocio['valoresCampos']['datosFormSocio']['FECHAALTA']['dia']['valorCampo']=
						 substr($fechaAltaAux,8,2);
						$resBuscarDatosSocio['valoresCampos']['datosFormSocio']['FECHAALTA']['mes']['valorCampo']=
						 substr($fechaAltaAux,5,2);
						$resBuscarDatosSocio['valoresCampos']['datosFormSocio']['FECHAALTA']['anio']['valorCampo']=
						 substr($fechaAltaAux,0,4);	
											
		    //-------------Inicio adaptación para forms de cuentas encriptadas -------------------------------
						$resBuscarDatosSocio['valoresCampos']['datosFormSocio']['ctaBanco']['CODENTIDAD']['valorCampo']=
						  $resBuscarDatosSocio['valoresCampos']['datosFormSocio']['CODENTIDAD']['valorCampo'];
						$resBuscarDatosSocio['valoresCampos']['datosFormSocio']['ctaBanco']['CODSUCURSAL']['valorCampo']=
						  $resBuscarDatosSocio['valoresCampos']['datosFormSocio']['CODSUCURSAL']['valorCampo'];
						$resBuscarDatosSocio['valoresCampos']['datosFormSocio']['ctaBanco']['DC']['valorCampo']=
						  $resBuscarDatosSocio['valoresCampos']['datosFormSocio']['DC']['valorCampo'];
						$resBuscarDatosSocio['valoresCampos']['datosFormSocio']['ctaBanco']['NUMCUENTA']['valorCampo']=
						  $resBuscarDatosSocio['valoresCampos']['datosFormSocio']['NUMCUENTA']['valorCampo'];

					 require_once "../../usuariosLibs/encriptar/encriptacionBase64.php";	
		    $resBuscarDatosSocio['valoresCampos']['datosFormSocio']['ctaBanco']['NUMCUENTA']['valorCampo'] = 
					   desEncriptarBase64($resBuscarDatosSocio['valoresCampos']['datosFormSocio']['NUMCUENTA']['valorCampo']);
								
      //echo "<br><br>5 modeloSocios:buscarDatosSocio:desencript:resBuscarDatosSocio['NUMCUENTA']: ";
				 	//print_r($resBuscarDatosSocio['valoresCampos']['datosFormSocio']['ctaBanco']['NUMCUENTA']['valorCampo']);
						
						$resBuscarDatosSocio['valoresCampos']['datosFormSocio']['CCEXTRANJERA']['valorCampo'] = 
					   desEncriptarBase64($resBuscarDatosSocio['valoresCampos']['datosFormSocio']['CCEXTRANJERA']['valorCampo']);
									
      //echo "<br><br>6 modeloSocios:buscarDatosSocio:desencript:resBuscarDatosSocio['CCEXTRANJERA']: ";
				 	//print_r($resBuscarDatosSocio['valoresCampos']['datosFormSocio']['CCEXTRANJERA']['valorCampo']);

						//------------------ Fin adaptación para forms de cuentas encriptadas)-------------------------------------
						//---------- Buscar cuota socio última ---------------------------------------------
						$codSocio = $resBuscarDatosSocio['valoresCampos']['datosFormSocio']['CODSOCIO']['valorCampo'];
						
	     $tablasBusqueda = 'CUOTAANIOSOCIO,AGRUPACIONTERRITORIAL';			
																																								
				  $camposBuscados = 'CUOTAANIOSOCIO.*,AGRUPACIONTERRITORIAL.NOMAGRUPACION';
				 
						if ( !isset($anioCuota) || $anioCuota ==NUL || $anioCuota =='')
			   { $anioCuota = "%";}
									
					 $cadCondicionesBuscar = " WHERE CUOTAANIOSOCIO.CODAGRUPACION=AGRUPACIONTERRITORIAL.CODAGRUPACION
					                             AND CUOTAANIOSOCIO.CODSOCIO=\"".$codSocio."\"". 
						                             "AND CUOTAANIOSOCIO.ANIOCUOTA LIKE \"".$anioCuota."\""; 
								
						$resDatosCuotaSocio = buscarEnTablas($tablasBusqueda,$cadCondicionesBuscar,
							                     				           $camposBuscados,$conexionUsuariosDB['conexionLink']);	
																																											
	    //echo "<br><br>7 modeloSocios:buscarDatosSocio:resDatosCuotaSocio: ";print_r($resDatosCuotaSocio);	
																																											
						if ($resDatosCuotaSocio['codError']!=='00000')
						{ $resBuscarDatosSocio['codError']=$resDatosCuotaSocio['codError'];
						  $resBuscarDatosSocio['errorMensaje']=$resDatosCuotaSocio['errorMensaje'];
								$resBuscarDatosSocio['nomScript']=$resDatosCuotaSocio['nomScript'];//se van a repetir
								$resBuscarDatosSocio['nomFuncion']=$resDatosCuotaSocio['nomFuncion'];//se van a repetir
						}
						elseif ($resDatosCuotaSocio['numFilas']==0)
						{$resBuscarDatosSocio['codError']='80001'; //no encontrado
							$resBuscarDatosSocio['errorMensaje']="No asignada cuota";
						}
						else //$resDatosCuotaSocio['codError']=='00000'
						{foreach ($resDatosCuotaSocio['resultadoFilas'] as $fila => $valorFila)
						 {
						  $indexAnio=$valorFila['ANIOCUOTA'];
								
								foreach ($valorFila as $col => $valorCol)
								{//if ($col!=='ANIOCUOTA')//se podría dejar aunque para evitar redundancia
									{ $auxCol[$col]['valorCampo'] = $valorCol;
									  if ($col=='FECHAPAGO'|| $col=='FECHAANOTACION')
											{ $auxCol[$col]['dia']['valorCampo']=	substr($valorCol,8,2);
											  $auxCol[$col]['mes']['valorCampo']=	substr($valorCol,5,2);
											  $auxCol[$col]['anio']['valorCampo']=	substr($valorCol,0,4);											 
											}
									}
								}
								$resBuscarDatosSocio['valoresCampos']['datosFormCuotaSocio'][$indexAnio]=$auxCol;
						 }				
							//echo "<br><br>****modeloSocios:buscarDatosSocio:resBuscarDatosSocio['valoresCampos']['datosFormCuotaSocio']";
							//print_r($resBuscarDatosSocio['valoresCampos']['datosFormCuotaSocio']);
						}//else $resDatosCuotaSocio['codError'] =='00000'					 				
					}//else $resDatosSocio['codError'] =='00000'	
				}//else $resDatosPaisDoc['codError'] =='00000'												
			}//else $resDatosMiembro['codError'] =='00000'				
		}//$resDatosUsuario['codError'] =='00000'
  
		//echo "<br><br>modeloSocios:buscarDatosSocios: ";print_r($resBuscarDatosSocio);
		
		if ($resBuscarDatosSocio['codError']!=='00000')//puede ser <80000 o 80001
		{ $arrMensaje['textoComentarios']="Error del sistema al buscar datos del socio, vuelva a intentarlo pasado un tiempo ";
		  
			require_once './modelos/modeloErrores.php'; //si es un error en tabla error, insertar errores 
			$resInsertarErrores=insertarError($resBuscarDatosSocio);			
			if ($resInsertarErrores['codError']!=='00000')
	  {$resBuscarDatosSocio['errorMensaje'] .= $resInsertarErrores['errorMensaje'];
				$arrMensaje['textoComentarios'].="Error del sistema al tratar ERRORES, vuelva a intentarlo pasado un tiempo ";
			}
		}//if $resDatosSocioEliminar['codError']!=='00000'
	}	//$conexionUsuariosDB['codError']=='00000'
	//echo "<br><br>modeloSocios:buscarDatosSocio:resBuscarDatosSocio: ";print_r($resBuscarDatosSocio);	

 return 	$resBuscarDatosSocio; 	
}
//------------------------------ Fin buscarDatosSocioDesEncriptaCCCEX  ---------


/*---------- Inicio buscarDatosSocio  (con IBAN y SIN-DesEncriptaCCCEX) --------
Busca "todos" los datos de un solo socio a partir de $codUsuario: = CODUSER y 
$anioCuota, busca tabla a tabla "sin joins" trata alguno de ellos y devuelve 
"todos" los datos en un array. 
Si ['numFilas'] == 0) devuelve ['codError'] = '80001'; no encontrado

RECIBE: $codUsuario = CODUSER y $anioCuota
DEVUELVE: array $resBuscarDatosSocio con los datos del socio y códigos de error

LLAMADA:controladorSocios.php:actualizarSocio(),mostrarDatosSocio()
y eliminarSocio(),pagarCuotaSocio() y otros ....
LLAMA: usuariosConfig/BBDD/MySQL/configMySQL.php:conexionDB(),
       modeloMySQL.php/buscarCadSql(),/modelos/modeloErrores.php:insertarError()
									
OBSERVACIONES: En una lugar de una join muy grande he decidido hacer consultas 
               individuales	y unirlas en un array.
               Es casi igual a modeloSocios.php:buscarDatosSocioCodSocio(), 
               pero recibe como parámetro $codUsuario = CODUSER en lugar 
															de $codSocio = CODSOCIO  
															
2020-01-08: modifico para incluir PHP: PDOStatement::bindParamValue													
------------------------------------------------------------------------------*/
function buscarDatosSocio($codUsuario,$anioCuota)
{
	//echo "<br /><br />0-1 modeloSocios:buscarDatosSocio:codUsuario: ";print_r($codUsuario);	
	//echo "<br /><br />0-2 modeloSocios:buscarDatosSocio:anioCuota: ";print_r($anioCuota);	

 $resBuscarDatosSocio['nomScript'] = 'modeloSocios.php';
 $resBuscarDatosSocio['nomFuncion'] = 'buscarDatosSocio';
	$resBuscarDatosSocio['codError'] = '00000';
 $resBuscarDatosSocio['errorMensaje'] = '';
	$arrMensaje['textoCabecera'] = 'Buscar datos del socio';
	$arrMensaje['textoComentarios'] = "Error del sistema al buscar los datos del socio, vuelva a intentarlo pasado un tiempo ";

	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";//si	
	
	require_once "BBDD/MySQL/conexionMySQL.php";
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB); 
	
	if ($conexionDB['codError'] !=='00000')	
	{ $resBuscarDatosSocio = $conexionDB;
   $arrMensaje['textoComentarios'] .="Error del sistema al conectarse a la base de datos"; 	
	}
	else	//$conexionDB['codError']=='00000'
	{			
		if (!isset($codUsuario) || empty($codUsuario) )
		{ $resBuscarDatosSocio['codError'] = '70601'; //no encontrado
				$resBuscarDatosSocio['errorMensaje'] = "Faltan variables-parámetros -codUsuario- necesarios para SQL";
		}
		else //if (isset($codUsuario) )
		{				
			$tablasBusqueda = 'USUARIO';//acaso sobre este apartado, para llegar aqui ya estará identificado como usuario
			$camposBuscados = '*';

			$cadCondicionesBuscar = " WHERE USUARIO.CODUSER = :codUsuario";
			
			$arrBind = array(':codUsuario' => $codUsuario);  
			//echo "<br /><br />1a modeloSocios:buscarDatosSocio:arrBind: ";print_r($arrBind);	

			$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";
			$resDatosUsuario = buscarCadSql ($cadSql,$conexionDB['conexionLink'],$arrBind); 
			
			//echo "<br /><br />1b modeloSocios:buscarDatosSocio:resDatosUsuario: ";print_r($resDatosUsuario);	
																																			
			if ($resDatosUsuario['codError'] !== '00000')
			{ $resBuscarDatosSocio['codError'] = $resDatosUsuario['codError'];
					$resBuscarDatosSocio['errorMensaje'] = $resDatosUsuario['errorMensaje'];
			}	
			elseif ($resDatosUsuario['numFilas'] == 0)
			{$resBuscarDatosSocio['codError'] = '80001'; //no encontrado
				$resBuscarDatosSocio['errorMensaje'] = "No existe ese socio";
			}	
			else //$resDatosUsuario['codError']=='00000')
			{ 
				foreach ($resDatosUsuario['resultadoFilas'][0] as $indice => $contenido)                         
				{  
						$resBuscarDatosSocio['valoresCampos']['datosFormUsuario'][$indice]['valorCampo'] = $resDatosUsuario['resultadoFilas'][0][$indice]; 	        
				} 			
			
				$tablasBusqueda = 'PAIS, MIEMBRO LEFT JOIN PROVINCIA ON MIEMBRO.CODPROV = PROVINCIA.CODPROV';
				$camposBuscados = 'MIEMBRO.*, PAIS.NOMBREPAIS as nombrePaisDom, PROVINCIA.NOMPROVINCIA';		
				
				$cadCondicionesBuscar = " WHERE MIEMBRO.CODPAISDOM = PAIS.CODPAIS1 AND MIEMBRO.CODUSER = :codUsuario";
			
				$arrBind = array(':codUsuario' => $codUsuario); 
				//echo "<br /><br />2a modeloSocios:buscarDatosSocio:arrBind: ";print_r($arrBind);				

				$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";
				$resDatosMiembro = buscarCadSql ($cadSql,$conexionDB['conexionLink'],$arrBind); 
				
				//echo "<br /><br />2b modeloSocios:buscarDatosSocio:resDatosMiembro: ";print_r($resDatosMiembro);	
																																										
				if ($resDatosMiembro['codError'] !== '00000')
				{ $resBuscarDatosSocio['codError'] = $resDatosMiembro['codError'];
						$resBuscarDatosSocio['errorMensaje'] = $resDatosMiembro['errorMensaje'];
				}	
				elseif ($resDatosMiembro['numFilas'] == 0)
				{$resBuscarDatosSocio['codError'] = '80001'; //no encontrado
					$resBuscarDatosSocio['errorMensaje'] = "No existe ese socio";
				}			
				else	//$resDatosMiembro['codError']=='00000'		
				{
					foreach ($resDatosMiembro['resultadoFilas'][0] as $indice => $contenido)                         
					{
							$resBuscarDatosSocio['valoresCampos']['datosFormMiembro'][$indice]['valorCampo'] = $resDatosMiembro['resultadoFilas'][0][$indice]; 	        
					}				
					//--inicio buscar nombrePaisDoc, en la anterior join ya hay país Domicilio--
					$tablasBusqueda = 'PAIS';
					$camposBuscados = 'PAIS.NOMBREPAIS as nombrePaisDoc';
					
					$cadCondicionesBuscar = " WHERE PAIS.CODPAIS1 = :codPaisDoc";	
					
					$arrBind = array(':codPaisDoc' => $resBuscarDatosSocio['valoresCampos']['datosFormMiembro']['CODPAISDOC']['valorCampo']); 

					$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";
					$resDatosPaisDoc = buscarCadSql ($cadSql,$conexionDB['conexionLink'],$arrBind); 
					
					//echo "<br /><br />3 modeloSocios:buscarDatosSocio:resDatosPaisDoc: ";print_r($resDatosPaisDoc);	
					
					$resBuscarDatosSocio['valoresCampos']['datosFormMiembro']['nombrePaisDoc']['valorCampo'] = $resDatosPaisDoc['resultadoFilas'][0]['nombrePaisDoc']; 
					
					if ($resDatosPaisDoc['codError'] !== '00000')
					{ $resBuscarDatosSocio['codError'] = $resDatosSocio['codError'];
							$resBuscarDatosSocio['errorMensaje'] = $resDatosSocio['errorMensaje'];
					}	
					elseif ($resDatosPaisDoc['numFilas'] == 0)
					{$resBuscarDatosSocio['codError'] = '80001'; //no encontrado
						$resBuscarDatosSocio['errorMensaje'] = "No existe ese código país socio";
						
					}//------------------- fin buscar nombrePaisDoc ----------------------------
					else //$resDatosPaisDoc['codError']=='00000'
					{
						$fechaNacAux=$resDatosMiembro['resultadoFilas'][0]['FECHANAC'];
						$resBuscarDatosSocio['valoresCampos']['datosFormMiembro']['FECHANAC']['dia']['valorCampo'] = substr($fechaNacAux,8,2);
						$resBuscarDatosSocio['valoresCampos']['datosFormMiembro']['FECHANAC']['mes']['valorCampo'] = substr($fechaNacAux,5,2);
						$resBuscarDatosSocio['valoresCampos']['datosFormMiembro']['FECHANAC']['anio']['valorCampo'] =substr($fechaNacAux,0,4);
							
						$resBuscarDatosSocio['valoresCampos']['datosFormDomicilio']['CODPAISDOM']['valorCampo'] = $resBuscarDatosSocio['valoresCampos']['datosFormMiembro']['CODPAISDOM']['valorCampo'];	
						$resBuscarDatosSocio['valoresCampos']['datosFormDomicilio']['nombrePaisDom']['valorCampo'] = $resBuscarDatosSocio['valoresCampos']['datosFormMiembro']['nombrePaisDom']['valorCampo'];	
						
						unset($resBuscarDatosSocio['valoresCampos']['datosFormMiembro']['nombrePaisDom']['valorCampo']);					
						
						$resBuscarDatosSocio['valoresCampos']['datosFormDomicilio']['DIRECCION']['valorCampo'] = $resBuscarDatosSocio['valoresCampos']['datosFormMiembro']['DIRECCION']['valorCampo'];	
						$resBuscarDatosSocio['valoresCampos']['datosFormDomicilio']['CP']['valorCampo'] = $resBuscarDatosSocio['valoresCampos']['datosFormMiembro']['CP']['valorCampo'];	
						$resBuscarDatosSocio['valoresCampos']['datosFormDomicilio']['LOCALIDAD']['valorCampo'] = $resBuscarDatosSocio['valoresCampos']['datosFormMiembro']['LOCALIDAD']['valorCampo'];	
						$resBuscarDatosSocio['valoresCampos']['datosFormDomicilio']['NOMPROVINCIA']['valorCampo']= $resBuscarDatosSocio['valoresCampos']['datosFormMiembro']['NOMPROVINCIA']['valorCampo'];
					
						//----------------------------------- inicio SOCIO	------------------------
						$tablasBusqueda = 'SOCIO,AGRUPACIONTERRITORIAL';
						//$camposBuscados = 'SOCIO.*,AGRUPACIONTERRITORIAL.*';//incluiría campos que se han añadido en tabla posteriomente y que no son necesarios aquí
						$camposBuscados = 'SOCIO.*,	AGRUPACIONTERRITORIAL.CODAGRUPACION, AGRUPACIONTERRITORIAL.NOMAGRUPACION,AGRUPACIONTERRITORIAL.EMAILCOORD';
						
						                  /*No son necesarios: AGRUPACIONTERRITORIAL.EMAIL, AGRUPACIONTERRITORIAL.EMAILSECRETARIO, AGRUPACIONTERRITORIAL.EMAILTESORERO,
																							  		AGRUPACIONTERRITORIAL.AMBITO,AGRUPACIONTERRITORIAL.ESTADO*/
      //Nota: No incuir 'OBSERVACIONES' podría interferir con otros campos de observaciones en algún formulario
				
						$cadCondicionesBuscar = " WHERE SOCIO.CODAGRUPACION = AGRUPACIONTERRITORIAL.CODAGRUPACION AND SOCIO.CODUSER = :codUsuario";  					
						
						$arrBind = array(':codUsuario' => $codUsuario); 

						$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";
						$resDatosSocio = buscarCadSql ($cadSql,$conexionDB['conexionLink'],$arrBind); 					
																																						
						//echo "<br><br>4 modeloSocios:buscarDatosSocio:resDatosSocio: ";print_r($resDatosSocio);	
																																																																			
						if ($resDatosSocio['codError'] !== '00000')
						{ $resBuscarDatosSocio['codError'] = $resDatosSocio['codError'];
								$resBuscarDatosSocio['errorMensaje'] = $resDatosSocio['errorMensaje'];
						}	
						elseif ($resDatosSocio['numFilas'] == 0)
						{$resBuscarDatosSocio['codError'] = '80001'; //no encontrado
							$resBuscarDatosSocio['errorMensaje'] = "No existe ese socio";
						}
						else //$resDatosSocio['codError']=='00000'
						{foreach ($resDatosSocio['resultadoFilas'][0] as $indice => $contenido)                         
							{ $resBuscarDatosSocio['valoresCampos']['datosFormSocio'][$indice]['valorCampo'] = $resDatosSocio['resultadoFilas'][0][$indice]; 	        
							}		
							$fechaAltaAux = $resDatosSocio['resultadoFilas'][0]['FECHAALTA'];				
							$resBuscarDatosSocio['valoresCampos']['datosFormSocio']['FECHAALTA']['dia']['valorCampo'] = substr($fechaAltaAux,8,2);
							$resBuscarDatosSocio['valoresCampos']['datosFormSocio']['FECHAALTA']['mes']['valorCampo'] = substr($fechaAltaAux,5,2);
							$resBuscarDatosSocio['valoresCampos']['datosFormSocio']['FECHAALTA']['anio']['valorCampo']= substr($fechaAltaAux,0,4);					
												
							//--- Inicio antiguo: adaptación para forms de cuentas encriptadas (se podrían encriptar IBAN socios) ---
							/*
							$resBuscarDatosSocio['valoresCampos']['datosFormSocio']['CUENTANOIBAN']['valorCampo']=$resBuscarDatosSocio['valoresCampos']['datosFormSocio']['CUENTANOIBAN']['valorCampo'];
							
							require_once "../../usuariosLibs/encriptar/encriptacionBase64.php";	
							$resBuscarDatosSocio['valoresCampos']['datosFormSocio']['CUENTANOIBAN']['valorCampo'] = desEncriptarBase64($resBuscarDatosSocio['valoresCampos']['datosFormSocio']['CUENTANOIBAN']['valorCampo']);												
							*/
							//------------------ Fin adaptación para forms de cuentas encriptadas)----
							
							//---------- Buscar cuota socio última -----------------------------------
							$codSocio = $resBuscarDatosSocio['valoresCampos']['datosFormSocio']['CODSOCIO']['valorCampo'];
							
							$tablasBusqueda = 'CUOTAANIOSOCIO,AGRUPACIONTERRITORIAL';																																								
							$camposBuscados = 'CUOTAANIOSOCIO.*,AGRUPACIONTERRITORIAL.NOMAGRUPACION';
						
							/*if ( !isset($anioCuota) || $anioCuota ==NULL || $anioCuota =='')
							{ $anioCuota = "%";}
									$cadCondicionesBuscar = " WHERE CUOTAANIOSOCIO.CODAGRUPACION=AGRUPACIONTERRITORIAL.CODAGRUPACION
																																			AND CUOTAANIOSOCIO.CODSOCIO=\"".$codSocio."\"".  
																																			"AND CUOTAANIOSOCIO.ANIOCUOTA LIKE \"".$anioCuota."\"";*/
																																			
							//if ( !isset($anioCuota) || $anioCuota == NULL || $anioCuota == '') antes
							//if ( !isset($anioCuota) || $anioCuota == NULL || $anioCuota == '%')
							if ( !isset($anioCuota) || empty($anioCuota) || $anioCuota == '%')
							{ $cadCondicionesBuscar = " WHERE CUOTAANIOSOCIO.CODAGRUPACION = AGRUPACIONTERRITORIAL.CODAGRUPACION
																																				AND CUOTAANIOSOCIO.CODSOCIO = :codSocio"; 
								
									//echo "<br><br>7a modeloSocios:buscarDatosSocio:cadCondicionesBuscar: ";print_r($cadCondicionesBuscar);																																				
																																			
									$arrBind = array(':codSocio' => $codSocio); 																																				
							}
							else
							{	$cadCondicionesBuscar = " WHERE CUOTAANIOSOCIO.CODAGRUPACION = AGRUPACIONTERRITORIAL.CODAGRUPACION
																																				AND CUOTAANIOSOCIO.CODSOCIO = :codSocio 
																																				AND CUOTAANIOSOCIO.ANIOCUOTA LIKE :anioCuota"; 
																																				
									//echo "<br><br>7b1 modeloSocios:buscarDatosSocio:cadCondicionesBuscar: ";print_r($cadCondicionesBuscar);	
									
									$arrBind = array(':codSocio' => $codSocio, ':anioCuota' => $anioCuota); 		
							}																								
							//echo "<br><br>7b-2 modeloSocios:buscarDatosSocio:arrBind: ";print_r($arrBind);		

							$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";
							$resDatosCuotaSocio = buscarCadSql ($cadSql,$conexionDB['conexionLink'],$arrBind); 						
																																												
							//echo "<br><br>7c modeloSocios:buscarDatosSocio:resDatosCuotaSocio: ";print_r($resDatosCuotaSocio);	
																																												
							if ($resDatosCuotaSocio['codError'] !== '00000')
							{ $resBuscarDatosSocio['codError'] = $resDatosCuotaSocio['codError'];
									$resBuscarDatosSocio['errorMensaje'] = $resDatosCuotaSocio['errorMensaje'];
							}
							elseif ($resDatosCuotaSocio['numFilas'] == 0)
							{ $resBuscarDatosSocio['codError'] = '80001'; //no encontrado
									$resBuscarDatosSocio['errorMensaje'] = "No asignada cuota";
							}
							else //$resDatosCuotaSocio['codError']=='00000'
							{
								foreach ($resDatosCuotaSocio['resultadoFilas'] as $fila => $valorFila)
								{
									$indexAnio = $valorFila['ANIOCUOTA'];
									
									foreach ($valorFila as $col => $valorCol)
									{//if ($col !=='ANIOCUOTA')//se podría dejar aunque para evitar redundancia
										{ $auxCol[$col]['valorCampo'] = $valorCol;
										
												if ($col == 'FECHAPAGO'|| $col == 'FECHAANOTACION')
												{ $auxCol[$col]['dia']['valorCampo'] =	substr($valorCol,8,2);
														$auxCol[$col]['mes']['valorCampo'] =	substr($valorCol,5,2);
														$auxCol[$col]['anio']['valorCampo'] =	substr($valorCol,0,4);											 
												}
										}
									}
									$resBuscarDatosSocio['valoresCampos']['datosFormCuotaSocio'][$indexAnio] = $auxCol;
								}				
								//echo "<br><br>****modeloSocios:buscarDatosSocio:resBuscarDatosSocio['valoresCampos']['datosFormCuotaSocio']";
								//print_r($resBuscarDatosSocio['valoresCampos']['datosFormCuotaSocio']);
							}//else $resDatosCuotaSocio['codError'] =='00000'					 				
						}//else $resDatosSocio['codError'] =='00000'	
					}//else $resDatosPaisDoc['codError'] =='00000'												
				}//else $resDatosMiembro['codError'] =='00000'				
			}//$resDatosUsuario['codError'] =='00000'
	 }//else if (isset($codUsuario) )
			
		//echo "<br><br>8 modeloSocios:buscarDatosSocios: ";print_r($resBuscarDatosSocio);
		
		if ($resBuscarDatosSocio['codError'] !== '00000')//puede ser <80000 o 80001
		{ $arrMensaje['textoComentarios'] = "Error del sistema al buscar datos del socio, vuelva a intentarlo pasado un tiempo ";
		  
			if ( isset($resBuscarDatosSocio['textoComentarios']) ) 
			{ $resBuscarDatosSocio['textoComentarios'] = ". modeloSocios.php:buscarDatosSocio:(): ".$resBuscarDatosSocio['textoComentarios'];}	
			else
			{	$resBuscarDatosSocio['textoComentarios'] = ". modeloSocios.php:buscarDatosSocio:(): ";}								
		
			require_once './modelos/modeloErrores.php'; //si es un error en tabla error, insertar errores 
			$resInsertarErrores = insertarError($resBuscarDatosSocio);	
			
			if ($resInsertarErrores['codError'] !=='00000')
	  {$resBuscarDatosSocio['errorMensaje'] .= $resInsertarErrores['errorMensaje'];
    //echo "<br /><br />8-1 modeloSocios:buscarDatosSocio_ERROR";	print_r($resBuscarDatosSocio);
				$arrMensaje['textoComentarios'] .= "Error del sistema al tratar ERRORES, vuelva a intentarlo pasado un tiempo ";
			}
		}//if $resDatosSocioEliminar['codError']!=='00000'
		
	}	//$conexionDB['codError']=='00000'
	//echo "<br><br>9 modeloSocios:buscarDatosSocio:resBuscarDatosSocio: ";print_r($resBuscarDatosSocio);	

 return 	$resBuscarDatosSocio; 	
}

/*--------------------- Fin buscarDatosSocio  --------------------------------*/

/*---------------------------- Inicio buscarDatosSocioCodSocio -----------------
Busca "todos" los datos de un solo socio a partir de $codSocio = CODSOCIO y 
$anioCuota, busca tabla a tabla sin joins y devuelve "todos" los datos en un array

RECIBE: $codSocio = CODSOCIO y $anioCuota	
DEVUELVE: array $resBuscarDatosSocio con los datos del socio y códigos de error	

LLAMADA: controladorSocios.php: pagarCuotaSocioSinCC(),
         modeloTesorero.php:mActualizarCuotasCobradasEnRemesaTes() 
LLAMA: usuariosConfig/BBDD/MySQL/configMySQL.php:conexionUsuariosDB(),
       modeloMySQL.php/buscarCadSql(),/modelos/modeloErrores.php:insertarError()	
								
OBSERVACIONES: En una lugar de una join muy grande he decidido hacer consultas 
               individuales	y unirlas en un array.
               Es casi igual a modeloSocios.php:buscarDatosSocio(), 
               pero recibe como parámetro $codSocio = CODSOCIO en lugar de  
															$codUsuario = CODUSER  
															
Agustín 2020-01-08: modifico para incluir PHP: PDOStatement::bindParamValue													
------------------------------------------------------------------------------*/
function buscarDatosSocioCodSocio($codSocio,$anioCuota)
{
	$resBuscarDatosSocio['codError'] = '00000';
 $resBuscarDatosSocio['errorMensaje'] = '';
 $resBuscarDatosSocio['nomScript'] = 'modeloSocios.php';
 $resBuscarDatosSocio['nomFuncion'] = 'buscarDatosSocio';//se van a repetir
	$arrMensaje['textoCabecera'] = 'Buscar datos del socio';		
	$arrMensaje['textoComentarios'] = "Error del sistema al buscar los datos del socio,	vuelva a intentarlo pasado un tiempo ";	
	//$arrMensaje['enlaceBoton']='./index.php?controlador=controladorLogin&amp;accion=logOut';	
	
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	require_once "BBDD/MySQL/conexionMySQL.php";
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB); 
	
	if ($conexionDB['codError']!=='00000')	
	{ $resBuscarDatosSocio = $conexionDB;
   $arrMensaje['textoComentarios'] .= "Error del sistema al conectarse a la base de datos"; 	
	}
	else	//$conexionDB['codError']=='00000'
	{
		//--------------------------- INICIO  SOCIO	---------------------------------- 

		$tablasBusqueda = 'SOCIO,AGRUPACIONTERRITORIAL';	
		$camposBuscados = 'SOCIO.*,AGRUPACIONTERRITORIAL.*';					
																																		
	 $cadCondicionesBuscar = " WHERE SOCIO.CODAGRUPACION = AGRUPACIONTERRITORIAL.CODAGRUPACION AND SOCIO.CODSOCIO = :codSocio "; 																																
																											
		$arrBind = array(':codSocio' => $codSocio); 																													
	
		$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";
		
		$resDatosSocio = buscarCadSql ($cadSql,$conexionDB['conexionLink'],$arrBind); 	
																																		
		//echo "<br><br>1 modeloSocios:buscarDatosSocioCodSocio:resDatosSocio: ";print_r($resDatosSocio);	
																																																															
		if ($resDatosSocio['codError'] !== '00000')
		{ $resBuscarDatosSocio['codError'] = $resDatosSocio['codError'];
				$resBuscarDatosSocio['errorMensaje'] = $resDatosSocio['errorMensaje'];
		}	
		elseif ($resDatosSocio['numFilas'] == 0)
		{$resBuscarDatosSocio['codError'] = '80001'; //no encontrado
			$resBuscarDatosSocio['errorMensaje'] = "No existe ese socio";
		}
		else //$resDatosSocio['codError']=='00000'
		{
			foreach ($resDatosSocio['resultadoFilas'][0] as $indice => $contenido)                         			
			{ $resBuscarDatosSocio['valoresCampos']['datosFormSocio'][$indice]['valorCampo'] = 
					$resDatosSocio['resultadoFilas'][0][$indice]; 	        
			}		
			
			$fechaAltaAux = $resDatosSocio['resultadoFilas'][0]['FECHAALTA'];				
			$resBuscarDatosSocio['valoresCampos']['datosFormSocio']['FECHAALTA']['dia']['valorCampo'] = substr($fechaAltaAux,8,2);
			$resBuscarDatosSocio['valoresCampos']['datosFormSocio']['FECHAALTA']['mes']['valorCampo'] = substr($fechaAltaAux,5,2);
			$resBuscarDatosSocio['valoresCampos']['datosFormSocio']['FECHAALTA']['anio']['valorCampo']= substr($fechaAltaAux,0,4);

			$codUsuario = $resDatosSocio['resultadoFilas'][0]['CODUSER'];								
				
			//--------------------------------------------- FIN SOCIO --------------------
		
			//--------------------------------------------- INICIO USUARIO ---------------		
			$tablasBusqueda = 'USUARIO';
			$camposBuscados = '*';
		
			$cadCondicionesBuscar = " WHERE USUARIO.CODUSER = :codUsuario";
			
			$arrBind = array(':codUsuario' => $codUsuario); 
				
			$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";
			$resDatosUsuario = buscarCadSql ($cadSql,$conexionDB['conexionLink'],$arrBind);
																																			
			//echo "<br /><br />2 modeloSocios:buscarDatosSocioCodSocio:resDatosUsuario:";print_r($resDatosUsuario);	
																																		
			if ($resDatosUsuario['codError'] !== '00000')
			{ $resBuscarDatosSocio['codError'] = $resDatosUsuario['codError'];
					$resBuscarDatosSocio['errorMensaje'] = $resDatosUsuario['errorMensaje'];
			}	
			elseif ($resDatosUsuario['numFilas'] == 0)
			{$resBuscarDatosSocio['codError'] = '80001'; //no encontrado
				$resBuscarDatosSocio['errorMensaje'] = "No existe ese socio";
			}	
			else //$resDatosUsuario['codError']=='00000')
			{ 
				foreach ($resDatosUsuario['resultadoFilas'][0] as $indice => $contenido)                         
				{//$resBuscarDatosSocio['valoresCampos']['datosFormUsuario'][$indice]['valorCampo'] = $contenido;		    
						$resBuscarDatosSocio['valoresCampos']['datosFormUsuario'][$indice]['valorCampo'] = $resDatosUsuario['resultadoFilas'][0][$indice]; 	        
				} 			
				//--------------------------------------------- FIN USUARIO -----------------
			
				//------------- INICIO MIEMBRO, PROVINCIA, PAISDOM  -------------------------
				
				$tablasBusqueda = 'PAIS, MIEMBRO LEFT JOIN PROVINCIA ON MIEMBRO.CODPROV=PROVINCIA.CODPROV';
				$camposBuscados = 'MIEMBRO.*, PAIS.NOMBREPAIS as nombrePaisDom, PROVINCIA.NOMPROVINCIA';						
																																				
				$cadCondicionesBuscar = " WHERE MIEMBRO.CODPAISDOM = PAIS.CODPAIS1 AND MIEMBRO.CODUSER = :codUsuario";
			
				$arrBind = array(':codUsuario' => $codUsuario); 				
		
				$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";
				$resDatosMiembro = buscarCadSql ($cadSql,$conexionDB['conexionLink'],$arrBind);			
																																						
				//echo "<br /><br />3 modeloSocios:buscarDatosSocioCodSocio:resDatosMiembro:";print_r($resDatosMiembro);	
																																										
				if ($resDatosMiembro['codError'] !== '00000')
				{ $resBuscarDatosSocio['codError'] = $resDatosMiembro['codError'];
						$resBuscarDatosSocio['errorMensaje'] = $resDatosMiembro['errorMensaje'];
				}	
				elseif ($resDatosMiembro['numFilas'] == 0)
				{$resBuscarDatosSocio['codError'] = '80001'; //no encontrado
					$resBuscarDatosSocio['errorMensaje'] = "No existe ese socio";
				}			
				else	//$resDatosMiembro['codError']=='00000'		
				{
					foreach ($resDatosMiembro['resultadoFilas'][0] as $indice => $contenido)                         
					{//$resBuscarDatosSocio['valoresCampos']['datosFormMiembro'][$indice]['valorCampo']=$contenido;		    
							$resBuscarDatosSocio['valoresCampos']['datosFormMiembro'][$indice]['valorCampo'] = $resDatosMiembro['resultadoFilas'][0][$indice]; 	        
					}	

					//----------------- INICIO preparar fecha nacimiento, y dirección ---------
					
					$fechaNacAux = $resDatosMiembro['resultadoFilas'][0]['FECHANAC'];
					
					$resBuscarDatosSocio['valoresCampos']['datosFormMiembro']['FECHANAC']['dia']['valorCampo'] = substr($fechaNacAux,8,2);
					$resBuscarDatosSocio['valoresCampos']['datosFormMiembro']['FECHANAC']['mes']['valorCampo'] = substr($fechaNacAux,5,2);
					$resBuscarDatosSocio['valoresCampos']['datosFormMiembro']['FECHANAC']['anio']['valorCampo']= substr($fechaNacAux,0,4);					
						
					$resBuscarDatosSocio['valoresCampos']['datosFormDomicilio']['CODPAISDOM']['valorCampo'] = $resBuscarDatosSocio['valoresCampos']['datosFormMiembro']['CODPAISDOM']['valorCampo'];	
					$resBuscarDatosSocio['valoresCampos']['datosFormDomicilio']['nombrePaisDom']['valorCampo'] = $resBuscarDatosSocio['valoresCampos']['datosFormMiembro']['nombrePaisDom']['valorCampo'];	
					
					unset($resBuscarDatosSocio['valoresCampos']['datosFormMiembro']['nombrePaisDom']['valorCampo']);	
					
					$resBuscarDatosSocio['valoresCampos']['datosFormDomicilio']['DIRECCION']['valorCampo'] = $resBuscarDatosSocio['valoresCampos']['datosFormMiembro']['DIRECCION']['valorCampo'];	
					$resBuscarDatosSocio['valoresCampos']['datosFormDomicilio']['CP']['valorCampo'] = $resBuscarDatosSocio['valoresCampos']['datosFormMiembro']['CP']['valorCampo'];	
					$resBuscarDatosSocio['valoresCampos']['datosFormDomicilio']['LOCALIDAD']['valorCampo'] = $resBuscarDatosSocio['valoresCampos']['datosFormMiembro']['LOCALIDAD']['valorCampo'];	
					$resBuscarDatosSocio['valoresCampos']['datosFormDomicilio']['NOMPROVINCIA']['valorCampo'] = $resBuscarDatosSocio['valoresCampos']['datosFormMiembro']['NOMPROVINCIA']['valorCampo'];		
										
					//----------------- FIN preparar fecha nacimiento, y dirección ------------
						
					//-------------------------------- FIN MIEMBRO, PROVINCIA, PAISDOM  --------
				
					//--INICIO buscar nombrePaisDoc, en la anterior join ya hay país Domicilio--
					
					$tablasBusqueda = 'PAIS';
					$camposBuscados = 'PAIS.NOMBREPAIS as nombrePaisDoc';					
					
					$cadCondicionesBuscar = " WHERE PAIS.CODPAIS1 = :codPaisDoc";	
					
					$arrBind = array(':codPaisDoc' => $resBuscarDatosSocio['valoresCampos']['datosFormMiembro']['CODPAISDOC']['valorCampo']); 				
		
					$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";
					$resDatosPaisDoc = buscarCadSql ($cadSql,$conexionDB['conexionLink'],$arrBind);							
					
					//cho "<br /><br />4 modeloSocios:buscarDatosSocioCodSocio:resDatosPaisDoc:";print_r($resDatosPaisDoc);	
					
					$resBuscarDatosSocio['valoresCampos']['datosFormMiembro']['nombrePaisDoc']['valorCampo'] = $resDatosPaisDoc['resultadoFilas'][0]['nombrePaisDoc']; 
					
					if ($resDatosPaisDoc['codError'] !== '00000')
					{ $resBuscarDatosSocio['codError'] = $resDatosSocio['codError'];
							$resBuscarDatosSocio['errorMensaje'] = $resDatosSocio['errorMensaje'];
					}	
					elseif ($resDatosPaisDoc['numFilas'] == 0)
					{ $resBuscarDatosSocio['codError'] = '80001'; //no encontrado
							$resBuscarDatosSocio['errorMensaje'] = "No existe ese código país socio";					
					}//---------------------------- FIN  buscar nombrePaisDoc ------------------
					
					else //$resDatosPaisDoc['codError']=='00000'
					{
							//------------------- INICIO Buscar cuota socio --------------------------		
							
							$tablasBusqueda = 'CUOTAANIOSOCIO,AGRUPACIONTERRITORIAL';																																								
							$camposBuscados = 'CUOTAANIOSOCIO.*,AGRUPACIONTERRITORIAL.NOMAGRUPACION';
						
							if ( !isset($anioCuota) || empty($anioCuota) || $anioCuota == '%')
							{ 
									$cadCondicionesBuscar = " WHERE CUOTAANIOSOCIO.CODAGRUPACION = AGRUPACIONTERRITORIAL.CODAGRUPACION
																																				AND CUOTAANIOSOCIO.CODSOCIO = :codSocio"; 
																																			
									$arrBind = array(':codSocio' => $codSocio); 																																				
							}
							else
							{ $cadCondicionesBuscar = " WHERE CUOTAANIOSOCIO.CODAGRUPACION = AGRUPACIONTERRITORIAL.CODAGRUPACION
																																				AND CUOTAANIOSOCIO.CODSOCIO = :codSocio 
																																				AND CUOTAANIOSOCIO.ANIOCUOTA LIKE :anioCuota";

									//echo "<br><br>5a modeloSocios:buscarDatosSocioCodSocio:cadCondicionesBuscar: ";print_r($cadCondicionesBuscar);																																				
																																				
									//****OJO ******	duda si valdría para todos los casos 	AND CUOTAANIOSOCIO.ANIOCUOTA = :anioCuota"
									$arrBind = array(':codSocio' => $codSocio, ':anioCuota' => $anioCuota); 									
							}
							
							//$resDatosCuotaSocio = buscarEnTablas($tablasBusqueda,$cadCondicionesBuscar,$camposBuscados,$conexionDB['conexionLink'],$arrBind);
							$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";
							$resDatosCuotaSocio = buscarCadSql ($cadSql,$conexionDB['conexionLink'],$arrBind);
																																												
							//echo "<br><br>5b modeloSocios:buscarDatosSocioCodSocio:resDatosCuotaSocio: ";print_r($resDatosCuotaSocio);	
																																												
							if ($resDatosCuotaSocio['codError'] !== '00000')
							{ $resBuscarDatosSocio['codError'] = $resDatosCuotaSocio['codError'];
									$resBuscarDatosSocio['errorMensaje'] = $resDatosCuotaSocio['errorMensaje'];
							}
							elseif ($resDatosCuotaSocio['numFilas'] == 0)
							{$resBuscarDatosSocio['codError'] = '80001'; //no encontrado
								$resBuscarDatosSocio['errorMensaje'] = "No asignada cuota";
							}
							else //$resDatosCuotaSocio['codError']=='00000'
							{
								foreach ($resDatosCuotaSocio['resultadoFilas'] as $fila => $valorFila)
								{
									$indexAnio=$valorFila['ANIOCUOTA'];
									
									foreach ($valorFila as $col => $valorCol)
									{//if ($col!=='ANIOCUOTA')//se podría dejar aunque para evitar redundancia
										{ $auxCol[$col]['valorCampo'] = $valorCol;
										
												if ($col == 'FECHAPAGO'|| $col == 'FECHAANOTACION')
												{ $auxCol[$col]['dia']['valorCampo'] =	substr($valorCol,8,2);
														$auxCol[$col]['mes']['valorCampo'] =	substr($valorCol,5,2);
														$auxCol[$col]['anio']['valorCampo']=	substr($valorCol,0,4);											 
												}
										}
									}
									$resBuscarDatosSocio['valoresCampos']['datosFormCuotaSocio'][$indexAnio] = $auxCol;
								}				
								//echo "<br><br>****modeloSocios:buscarDatosSocio:resBuscarDatosSocio['valoresCampos']['datosFormCuotaSocio']";
								//print_r($resBuscarDatosSocio['valoresCampos']['datosFormCuotaSocio']);
							}//else $resDatosCuotaSocio['codError'] =='00000'					 				
					}//else $resDatosSocio['codError'] =='00000'	
				}//else $resDatosPaisDoc['codError'] =='00000'												
			}//else $resDatosMiembro['codError'] =='00000'				
		}//$resDatosUsuario['codError'] =='00000'
  
		//echo "<br><br>modeloSocios:buscarDatosSociosCodSocio: ";print_r($resBuscarDatosSocio);
		
		if ($resBuscarDatosSocio['codError'] !== '00000')//puede ser <80000 o 80001
		{ $arrMensaje['textoComentarios'] = "Error del sistema al buscar datos del socio, vuelva a intentarlo pasado un tiempo ";
		  
				require_once './modelos/modeloErrores.php'; //si es un error en tabla error, insertar errores 
				$resInsertarErrores = insertarError($resBuscarDatosSocio);			
				
				if ($resInsertarErrores['codError'] !=='00000')
				{$resBuscarDatosSocio['errorMensaje'] .= $resInsertarErrores['errorMensaje'];
					$arrMensaje['textoComentarios'] .= "Error del sistema al tratar ERRORES, vuelva a intentarlo pasado un tiempo ";
				}
		}//if $resDatosSocioEliminar['codError']!=='00000'
	}	//$conexionDB['codError']=='00000'
	//echo "<br><br>6 modeloSocios:buscarDatosSocio::resBuscarDatosSocio: ";print_r($resBuscarDatosSocio);	

 return 	$resBuscarDatosSocio; 	
}
//-------------- Fin buscarDatosSocioCodSocio_PDO_bindParam --------------------

/*---------------------------- Inicio buscarDatosSocioConfirmar ----------------
Devuelve un array asociativo con los datos de un socio de la tabla 
"SOCIOSCONFIRMAR", que es la lista de socios pendientes de confirmar
y mensajes error.

RECIBE: $codUsuario = CODUSER 
DEVUELVE: array $reDatosSocioConfirmar con los datos del socio y códigos de error	

LLAMADA: controladorSocios.php:confirmarAltaSocio()
cPresidente:reenviarEmailConfirmarSocioAltaGestor(),confirmarAltaSocioPendientePorGestor
anularSocioPendienteConfirmarPres() 
LLAMA: usuariosConfig/BBDD/MySQL/configMySQL.php:conexionUsuariosDB(),
       modeloMySQL.php/buscarCadSql(),/modelos/modeloErrores.php:insertarError()
									
OBSERVACIONES: 															
Agustín 2020-02-08: modifico para incluir PHP: PDOStatement::bindParamValue													
------------------------------------------------------------------------------*/
function buscarDatosSocioConfirmar($codUsuario)
{
		$reDatosSocioConfirmar['nomScript'] = "modeloSocios.php";	
		$reDatosSocioConfirmar['nomFuncion' ] = "buscarDatosSocioConfirmar";
		$reDatosSocioConfirmar['codError'] = '00000';
		$reDatosSocioConfirmar['errorMensaje'] = '';
		$arrMensaje = array();	

		require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";//si	
		
		require_once "BBDD/MySQL/conexionMySQL.php";
		$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB); 
		
		if ($conexionDB['codError'] !== '00000')	
		{ $reDatosSocioConfirmar = $conexionDB;
				$arrMensaje['textoComentarios'] .= "Error del sistema al conectarse a la base de datos"; 	
		}
		else	//$conexionDB['codError']=='00000'
		{
			if (!isset($codUsuario) || empty($codUsuario) )	
			{ 
					$reDatosSocioConfirmar['codError'] = '70601'; //Fatan paramámetros necesarios para Selcet
					$reDatosSocioConfirmar['errorMensaje'] = "Error sistema: Falta el parámetro 'codUsuario' necesario para buscar en tabla SOCIOSCONFIRMAR";
					$reDatosSocioConfirmar['textoComentarios'] = "modeloSocios.php:buscarDatosSocioConfirmar()";	
					$arrMensaje['textoComentarios'] = "Error sistema: Falta el parámetro 'codUsuario' necesario para buscar en tabla SOCIOSCONFIRMAR";
			}
			else//if (isset($codUsuario) && !empty($codUsuario) )	
			{					
					$tablasBusqueda = 'SOCIOSCONFIRMAR';
					$camposBuscados = '*';		
				
					$cadCondicionesBuscar = " WHERE SOCIOSCONFIRMAR.CODUSER = :codUsuario";
					
					$arrBind = array(':codUsuario' => $codUsuario); 	
					
					$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";
					$reDatosSocioConfirmar = buscarCadSql ($cadSql,$conexionDB['conexionLink'],$arrBind);		

					//echo "<br /><br />1 modeloSocios:buscarDatosSocioConfirmar:reDatosSocioConfirmar: ";print_r($reDatosSocioConfirmar);
			}//else if (isset($codUsuario) && !empty($codUsuario) )		
			
		 if ($reDatosSocioConfirmar['codError'] !== '00000')
			{
					$reDatosSocioConfirmar['textoComentarios'] = "modeloSocios.php:buscarDatosSocioConfirmar(): ";	
					$arrMensaje['textoComentarios'] = "Error del sistema al buscar datos de socio/a pendientes por confirmar vuelva a intentarlo pasado un tiempo ";		
					
					require_once './modelos/modeloErrores.php';
					insertarError($reDatosSocioConfirmar);		
			}
			elseif ($reDatosSocioConfirmar['numFilas'] == 0)
			{ $reDatosSocioConfirmar['codError'] = '80001'; //no encontrado
					$reDatosSocioConfirmar['errorMensaje'] = "Error sistema: No existe ese socio en la tabla de pendientes por confirmar";
					$reDatosSocioConfirmar['textoComentarios'] = "modeloSocios.php:buscarDatosSocioConfirmar(): ";	
					$arrMensaje['textoComentarios'] = "Error sistema: No existe ese socio en la tabla de pendientes por confirmar";
								
					require_once './modelos/modeloErrores.php';
					insertarError($reDatosSocioConfirmar);				
			}

	 }//else	//$conexionDB['codError']=='00000'
	
	$reDatosSocioConfirmar['arrMensaje'] = $arrMensaje;
	//echo "<br /><br />2 modeloSocios:buscarDatosSocioConfirmar:reDatosSocioConfirmar: ";print_r($reDatosSocioConfirmar);
	
 return $reDatosSocioConfirmar;
}
/*------------------------------ Fin buscarDatosSocioConfirmar ----------------*/

/*------------------------------ Inicio buscarAgrupSocio -----------------------
Devuelve array asociativo: $agrupaSocio['CODAGRUPACION'] y 
$agrupaSocio['NOMAGRUPACION'] y mensajes error

LLAMADA:modeloSocios.php:buscarEmailCoordSecreTesor(),
LLAMA: modeloMySQL.php:buscarCadSql(),conexionMySQL.php:conexionDB()
              
Agustín 2020-01-08: modifico para incluir PHP: PDOStatement::bindParam																			
------------------------------------------------------------------------------*/
function buscarAgrupSocio($codUser)
{
	//echo "<br /><br />0- modeloSocios.php:buscarAgrupSocio:codUser: ";print_r($codUser);
	
	$agrupaSocio['codError'] = '00000';
	$agrupaSocio['errorMensaje'] = '';
	$agrupaSocio['nomFuncion'] = "buscarAgrupSocio";
 $agrupaSocio['nomScript'] = "modeloSocioss.php";
	$arrMensaje['textoCabecera'] = 'Buscar datos de la agrupación del socio';		
	
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";//si	
	
	require_once "BBDD/MySQL/conexionMySQL.php";
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB); 
	
	if ($conexionDB['codError'] !== '00000')	
	{ $agrupaSocio = $conexionDB;
   $arrMensaje['textoComentarios'] .= "Error del sistema al conectarse a la base de datos"; 	
	}
	else	//$conexionUsuariosDB['codError']=='00000'
	{
		$tablasBusqueda = 'AGRUPACIONTERRITORIAL,SOCIO';
		$camposBuscados = 'AGRUPACIONTERRITORIAL.*';
																															
		$cadCondicionesBuscar = " WHERE SOCIO.CODAGRUPACION = AGRUPACIONTERRITORIAL.CODAGRUPACION AND SOCIO.CODUSER = :codUser ";
																														
  $arrBind = array(':codUser' => $codUser); 
	
	 $cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";
	 
		$reAgrupaSocio = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);	
		
  //echo "<br /><br />2 modeloSocios.php:buscarAgrupSocio:reAgrupaSocio";print_r($reAgrupaSocio);
  
		if ($reAgrupaSocio['codError'] !== '00000')
  {$agrupaSocio =	$reAgrupaSocio;		 
	  $arrMensaje['textoComentarios'] = "Error del sistema al buscar datos agrupación socio, vuelva a intentarlo pasado un tiempo ";			
			
			require_once './modelos/modeloErrores.php';
		 insertarError($reAgrupaSocio);		
  }
  elseif ($reAgrupaSocio['numFilas'] == 0)
	 {$agrupaSocio['codError'] = '80001'; //no encontrado
	  $agrupaSocio['errorMensaje'] = "Error sistema: No existe ese socio o agrupación";
  }
  else
  { foreach ($reAgrupaSocio['resultadoFilas'][0] as $indice => $contenido)                         
    {      
      $agrupaSocio['resultadoFilas'][$indice] = $contenido; 
    }
  }
	}
	$agrupaSocio['arrMensaje'] = $arrMensaje;	
	
	//echo "<br /><br />3 modeloSocios.php:buscarAgrupSocio:reAgrupaSocio: ";print_r($agrupaSocio);	
	
 return $agrupaSocio;
}
//------------------------------ Fin buscarAgrupSocio --------------------------


/*---------------------- Inicio cadBuscarDatosAgrupaciones ----------------------
Forma la cadena select sql para buscar los datos de la tabla "AGRUPACIONTERRITORIAL"
por con el parámetros "$codAgrupacion", que podría ser una sola agrupación o todas "%"

Controla la existencia del parámetro "$codAgrupacion"

RECIBE: "$codAgrupacion"
DEVUELVE: array "$arrayCadBuscarDatosAgrup" cadena select ['cadSQL'] y ['arrBindValues']	
y codigos de error en caso de  que falte el parámetro "$codAgrupacion"
													
LLAMADA: cPresidente.php:listaAgrupacionesPres(),modeloSocios:buscarDatosAgrupacion()	
									
OBSERVACIONES: 
2022-01-08: Probada PDO y PHP 7.3.21
--------------------------------------------------------------------------------*/
function cadBuscarDatosAgrupaciones($codAgrupacion)
{
		//echo "<br><br>0-1 modeloSocios.php:cadBuscarDatosAgrupaciones:codAgrupacion: "; print_r($codAgrupacion);

		$arrayCadBuscarDatosAgrup['codError'] = '00000';
		$arrayCadBuscarDatosAgrup['errorMensaje'] = '';		
		
		$arrBind = array();

		/*---- Inicio Condiciones $codAgrupacion  ----------------------------------------*/
		
		if (!isset($codAgrupacion) || empty($codAgrupacion))
		{ 		
	   $arrayCadBuscarDatosAgrup['codError'] = "70601"; //Faltan paramámetros necesarios para Select
		  $arrayCadBuscarDatosAgrup['errorMensaje'] = "modeloSocios.php:cadBuscarDatosAgrupaciones. Error sistema: Falta el parámetro 'codAgrupacion' necesario para buscar en tabla AGRUPACIONTERRITORIAL";
				$arrayCadBuscarDatosAgrup['textoComentarios'] = $arrayCadBuscarDatosAgrup['errorMensaje'];
		}
		elseif ( $codAgrupacion == '%')
		{ 
		  $cadCondiccionBuscar  = "";
		}
		else
		{ 
    $cadCondiccionBuscar  = " WHERE  AGRUPACIONTERRITORIAL.CODAGRUPACION = :codAgrupacion ";  
				$arrBind[':codAgrupacion'] = $codAgrupacion;
		}

		//echo "<br><br>1 modeloSocios.php:cadBuscarDatosAgrupaciones:cadCondiccionBuscar: ";print_r($cadCondiccionBuscar);			
		
	 /*--- Fin Condiciones $codAgrupacion, -------------------------------------------*/															
			
  if ($arrayCadBuscarDatosAgrup['codError'] === '00000')			
  {//--- Inicio formar select ------------------------------------------------------
			
			$tablasBusqueda = 'AGRUPACIONTERRITORIAL';		 
			$camposBuscados = '*'; 					
			
			// como CODPAISDOM == '' para 'Europa Laica Estatal e Internacional'	  
			// $cadCondicionesOrden = " ORDER BY CODPAISDOM DESC, NOMAGRUPACION ASC ";//CODPAISDOM DESC para que quede al final 'Europa Laica Estatal e Internacional'	 
			$cadCondicionesOrden = " ORDER BY CODPAISDOM ASC, NOMAGRUPACION ASC ";//CODPAISDOM ASC para que quede al principio 'Europa Laica Estatal e Internacional'	 
			
			$cadCondicionesBuscarOrd = $cadCondiccionBuscar.$cadCondicionesOrden;
																				
			
			$cadBuscarDatosAgrupaciones = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscarOrd";	
																																																
			$arrayCadBuscarDatosAgrup['cadSQL'] = $cadBuscarDatosAgrupaciones;

			$arrayCadBuscarDatosAgrup['arrBindValues']	= $arrBind;
			
			//--- Fin formar select ---------------------------------------------------------
		}																																									
		//echo "<br><br>2 modeloSocios.php:cadBuscarDatosAgrupaciones:arrayCadBuscarDatosAgrup: "; print_r($arrayCadBuscarDatosAgrup);	
	
	return $arrayCadBuscarDatosAgrup;
}
/*---------------------- FIN cadBuscarDatosAgrupaciones -----------------------*/

/*---------- Inicio buscarDatosAgrupacion --------------------------------------
Se ejecuta la Select para buscar en la tabla 'AGRUPACIONTERRITORIAL' los datos 
de una agrupación o de todas (%)según el parámetros "$codAgrupacion", 
que podría ser una sola agrupación o todas "%".

Controla la existencia del parámetro "$codAgrupacion"

RECIBE: "$codAgrupacion" string que puede ser una sola agrupación o todas "%"

DEVUELVE: array "$datosAgrupacion" con los datos de una agrupación o de todas (%),
"$datosAgrupacion['resultadoFilas'][0]['EMAILCOORD']['valorCampo']" y los campos 
el número de filas y códigos de error.						

LLAMADA: modeloSocios.php:buscarEmailCoordSecreTesor(),
cPresidente.php:mostrarDatosAgrupacionPres(),actualizarDatosAgrupacionPres()
modeloBancos.php:agrupacionIBAN_papel()

LLAMA: 
modeloSocios.php:cadBuscarDatosAgrupaciones() (donde se forma la select)
modeloMySQL.php:buscarCadSql(),conexionMySQL.php:conexionDB()

Agustín 2020-01-08: modifico para incluir PHP: PDOStatement::bindParam																			
-------------------------------------------------------------------------------*/
function buscarDatosAgrupacion($codAgrupacion)
{
	//echo "<br><br>0-1 modeloSocios:buscarDatosAgrupacion:codAgrupacion: ";print_r($codAgrupacion); 
	
	$datosAgrupacion['nomScript'] = "modeloSocios.php";	
	$datosAgrupacion['nomFuncion' ] = "buscarDatosAgrupacion";
	$datosAgrupacion['codError'] = '00000';
	$datosAgrupacion['errorMensaje'] = '';	
	
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	require_once "BBDD/MySQL/conexionMySQL.php";
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB); 

	if ($conexionDB['codError']!=='00000')		
	{ $resBuscarDatosAgrupacion = $conexionDB;
	}
	else //$conexionDB['codError']=='00000'
	{
		//--- Inicio formar cadena Select ----------------------------
  /*$tablasBusqueda = 'AGRUPACIONTERRITORIAL';		 
  $camposBuscados = '*';
		if ( !isset($codAgrupacion) || empty($codAgrupacion) || $codAgrupacion == '%')
  { $cadCondicionesBuscar = ' ';	
	   $arrBind = NULL;
	 }
 	else
	 { $cadCondicionesBuscar = " WHERE AGRUPACIONTERRITORIAL.CODAGRUPACION = :codAgrupacion";																														
    $arrBind = array(':codAgrupacion' => $codAgrupacion);	   
	 }			
		$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";
		*/	//--- Fin formar cadena Select ---------------------------- 
		
		//-- Sustituido lo anterior por:
	
		$arrayCadBuscarDatosAgrup = cadBuscarDatosAgrupaciones($codAgrupacion);// en modeloSocios.php
		
		//echo "<br><br>1-1 modeloSocios:buscarDatosAgrupacion:arrayCadBuscarDatosAgrup: ";print_r($arrayCadBuscarDatosAgrup); 
 
	 if ($arrayCadBuscarDatosAgrup['codError'] !== '00000')	
  { 
	   $datosAgrupacion =	$arrayCadBuscarDatosAgrup;		 
	   $arrMensaje['textoComentarios'] = "Error: modeloSocios:buscarDatosAgrupacion(), al buscar datos agrupación, vuelva a intentarlo pasado un tiempo ";			
			
			 require_once './modelos/modeloErrores.php';
		  insertarError($reAgrupaSocio);
  }
		else //$arrayCadBuscarDatosAgrup['codError'] === '00000')	
		{					
			$cadSql = $arrayCadBuscarDatosAgrup['cadSQL'];
			$arrBind = $arrayCadBuscarDatosAgrup['arrBindValues'];
			
			$resBuscarDatosAgrupacion = buscarCadSql ($cadSql,$conexionDB['conexionLink'],$arrBind);	
																																														
			//echo "<br><br>1-2 modeloSocios:buscarDatosAgrupacion:resBuscarDatosAgrupacion: ";print_r($resBuscarDatosAgrupacion); 																																													
				
			if ($resBuscarDatosAgrupacion['codError'] !== '00000')
			{$datosAgrupacion =	$resBuscarDatosAgrupacion;		 
				$arrMensaje['textoComentarios'] = "Error: modeloSocios:buscarDatosAgrupacion(), del sistema al buscar datos agrupación, vuelva a intentarlo pasado un tiempo ";			
				
				require_once './modelos/modeloErrores.php';
				insertarError($reAgrupaSocio);		
			}
			elseif ($resBuscarDatosAgrupacion['numFilas'] === 0)
			{
				$datosAgrupacion['codError'] = '80005'; //no encontrado
				$datosAgrupacion['errorMensaje'] = "Error sistema: No existe esa agrupación";
				$arrMensaje['textoComentarios'] = "Error: : modeloSocios:buscarDatosAgrupacion() : No existe esa agrupación";	
			}
			else
			{ $datosAgrupacion = $resBuscarDatosAgrupacion; 
					$arrMensaje['textoComentarios'] = "";
			}
		}//else $arrayCadBuscarDatosAgrup['codError'] === '00000')	
		
	}//else $conexionDB['codError']=='00000' 
	
	$datosAgrupacion['arrMensaje'] = $arrMensaje;			
 
	//echo "<br><br>2 modeloSocios:buscarDatosAgrupacion:datosAgrupacion: ";print_r($datosAgrupacion); 

	return $datosAgrupacion;
}
//-------------------------- Fin buscarDatosAgrupacion -------------------------

/*---------- Inicio buscarDatosAreaGestion -------------------------------------
Devuelve un array con los campos del AREAGESTION ...................

RECIBE: como parámetro el código de la agrupación: $codAgrupacion
DEVUELVE: $datosAreaGestion formato:['resultadoFilas']['EMAILCOORD']['valorCampo']
y los campos de errores y mensajes de siempre

LLAMADA: modeloSocios.php:buscarEmailCoordSecreTesor()
LLAMA: modeloMySQL.php:buscarCadSql(),conexionMySQL.php:conexionDB()

Agustín 2020-01-08: modifico para incluir PHP: PDOStatement::bindParam																			
------------------------------------------------------------------------------*/
function buscarDatosAreaGestion($codAgrupacion)
{ 
 //echo "<br><br>1 modeloSocios:buscarDatosAreaGestion:codAgrupacion: ";print_r($codAgrupacion); 
	
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";//si	
	
	require_once "BBDD/MySQL/conexionMySQL.php";
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB); 

	if ($conexionDB['codError']!=='00000')		
	{ $resBuscarDatosAgrupacion = $conexionDB;
	}
	else
	{	 
  $camposBuscados = 'AREAGESTION.*';//	$camposBuscados = 'AREAGESTION.NOMAREAGESTION, AREAGESTION.EMAIL';
	 $tablasBusqueda = 'AREAGESTIONAGRUPACIONESCOORD,AREAGESTION';	
		 		
		if ( !isset($codAgrupacion) || empty($codAgrupacion) || $codAgrupacion =='%')
  { $cadCondicionesBuscar = ' ';	
	   $arrBind = NULL;
	 }	   
 	else
	 {
																				
			$cadCondicionesBuscar = "	WHERE AREAGESTIONAGRUPACIONESCOORD.CODAREAGESTIONAGRUP = AREAGESTION.CODAREAGESTION
		                           	AND AREAGESTIONAGRUPACIONESCOORD.CODAGRUPACION = :codAgrupacion "; 		
			
			$arrBind = array(':codAgrupacion' => $codAgrupacion);	  
  }

		$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";
	 $resBuscarDatosAreaGestion = buscarCadSql ($cadSql,$conexionDB['conexionLink'],$arrBind);			
		
  //echo "<br><br>2 modeloSocios:buscarDatosAreaGestion:resBuscarDatosAreaGestion: ";print_r($resBuscarDatosAreaGestion); 		
			
	 if ($resBuscarDatosAreaGestion['codError']!=='00000')
  {
			$datosAreaGestion =	$resBuscarDatosAreaGestion;		 
	  $arrMensaje['textoComentarios'] = "Error del sistema al buscar datos de ÁREA de GESTIÓN, vuelva a intentarlo pasado un tiempo ";			
			require_once './modelos/modeloErrores.php';
		 insertarError($reAgrupaSocio);		
  }
  elseif ($resBuscarDatosAreaGestion['numFilas'] == 0)
	 {$datosAreaGestion['codError'] = '80005'; //no encontrado
	  $datosAreaGestion['errorMensaje'] = "Error sistema: No existe esa ÁREA de GESTIÓN";
			$arrMensaje['textoComentarios'] = "Error del sistema al buscar datos agrupación: No existe ese AREA de GESTIONn";	
  }
  else
  { $datosAreaGestion = $resBuscarDatosAreaGestion; 
		  $arrMensaje['textoComentarios'] = "";
  }
	}
	$datosAreaGestion['arrMensaje'] = $arrMensaje;			
 //echo "<br><br>3 modeloSocios:buscarDatosAreaGestion:datosAreaGestion: ";print_r($datosAreaGestion); 

	return $datosAreaGestion;
}
//-------------------------- Fin buscarDatosAreaGestion ------------------------

/*---------------------- Inicio buscarEmailCoordSecreTesor ---------------------
Busca los datos de email de area gestión, coordinador, presidente, 
secretaría, tesorería y los devuelve en formato de array 
(se utilizarán para formar las direcciones de email de confirmación de alta 
y baja de socios)
													
LLAMADA: controladorSocios.php:confirmarAltaSocio(),eliminarSocio()
cPresidente.php: eliminarSocioPres(),altaSocioPorGestorPres(),confirmarAltaSocioPendientePorGestor()									
cCoordinador.php:eliminarSocioCoord(),altaSocioPorGestorCoord()									
cTesorero.php:altaSocioPorGestorTes()
									
LLAMA: modeloSocios.php: buscarAgrupSocio(), buscarDatosAgrupacion(), buscarDatosAreaGestion()									
				 
OBSERVACIONES:												
------------------------------------------------------------------------------*/
function buscarEmailCoordSecreTesor($codUser)
{//echo "<br><br>0-1 modeloSocios:buscarEmailCoordSecreTesor:codUser: ";print_r($codUser);
	
	$datosEmailCoordSecreTesor['codError'] = '00000';
	$datosEmailCoordSecreTesor['errorMensaje'] ='';
	$datosEmailCoordSecreTesor['nomFuncion'] = "buscarEmailCoordSecreTesor";
 $datosEmailCoordSecreTesor['nomScript'] = "modeloSocioss.php";
	$datosEmailCoordSecreTesor['textoCabecera'] = 'Buscar datos de email de coordinador, presidente, secretaria, tesoreria';
 	
	if ( !isset($codUser) || empty($codUser))
 { //echo "<br><br>1 modeloSocios:buscarEmailCoordSecreTesor:codUser: ";print_r($codUser);

   $datosEmailCoordSecreTesor['codError']  = '80201';
   $datosEmailCoordSecreTesor['errorMensaje'] = "Error: Falta parámentro 'codUser'";	 
   $arrMensaje['textoComentarios'] = "Error del sistema al buscar datos agrupación socio/a, vuelva a intentarlo pasado un tiempo ";			
		 require_once './modelos/modeloErrores.php';
	  insertarError($datosEmailCoordSecreTesor);		
 }
	else // !if ( !isset($codUser) || empty($codUser))
	{	
			$reDatosAgrupacionSocio = buscarAgrupSocio($codUser);
			//echo "<br><br>2 modeloSocios:buscarEmailCoordSecreTesor:reDatosAgrupacionSocio:";print_r($reDatosAgrupacionSocio);
			
			$agrupNom = $reDatosAgrupacionSocio['resultadoFilas']['NOMAGRUPACION'];	
				
			if ($reDatosAgrupacionSocio['codError'] !== '00000')
			{ 
					$datosEmailCoordSecreTesor =	$reDatosAgrupacionSocio;		 
					$arrMensaje['textoComentarios'] = "Error del sistema al buscar datos agrupación socio/a, vuelva a intentarlo pasado un tiempo ";			
					require_once './modelos/modeloErrores.php';
					insertarError($reDatosAgrupacionSocio);		
			}
			elseif ($reDatosAgrupacionSocio['resultadoFilas']['CODAGRUPACION'] !== '00000000')//NO ES agrupación ESTATAL o internacional
			{	  
						$datosEmailCoordSecreTesor['COORDINADOR']['email'] = $reDatosAgrupacionSocio['resultadoFilas']['EMAILCOORD'];
						$datosEmailCoordSecreTesor['COORDINADOR']['nombre'] = 'Coordinación de '.$reDatosAgrupacionSocio['resultadoFilas']['NOMAGRUPACION'];				

						$reDatosAgrupacionEstatal = buscarDatosAgrupacion('00000000');//para presidencia, tesoreria y secretaria estatal
						//echo"<br><br>3 modeloSocios:buscarEmailCoordSecreTesor:reDatosAgrupacionEstatal: ";print_r($reDatosAgrupacionEstatal);

						if ($reDatosAgrupacionEstatal['codError'] !== '00000')
						{$datosEmailCoordSecreTesor =	$reDatosAgrupacionEstatal;		 
							$arrMensaje['textoComentarios'] = "Error del sistema al buscar datos agrupación socio/a, vuelva a intentarlo pasado un tiempo ";			
							require_once './modelos/modeloErrores.php';
							insertarError($reDatosAgrupacionEstatal);		
						}
						else 
						{	     
								$datosEmailCoordSecreTesor['PRESIDENTE']['email'] = $reDatosAgrupacionEstatal['resultadoFilas'][0]['EMAILCOORD'];
								$datosEmailCoordSecreTesor['PRESIDENTE']['nombre'] = $reDatosAgrupacionEstatal['resultadoFilas'][0]['NOMAGRUPACION'];					
								$datosEmailCoordSecreTesor['TESORERO']['email'] = $reDatosAgrupacionEstatal['resultadoFilas'][0]['EMAILTESORERO'];
								$datosEmailCoordSecreTesor['TESORERO']['nombre'] = $reDatosAgrupacionEstatal['resultadoFilas'][0]['NOMAGRUPACION'];
								$datosEmailCoordSecreTesor['SECRETARIO']['email'] = $reDatosAgrupacionEstatal['resultadoFilas'][0]['EMAILSECRETARIO'];	
								$datosEmailCoordSecreTesor['SECRETARIO']['nombre'] = $reDatosAgrupacionEstatal['resultadoFilas'][0]['NOMAGRUPACION'];				
									
							/* PARA PRUEBAS DE DESARROLO, comentar asi no llegaría email a ellos
								$datosEmailCoordSecreTesor['PRESIDENTE']['email'] = 'bpresidencia@europalaica.org';
								$datosEmailCoordSecreTesor['PRESIDENTE']['nombre'] = 'Presidencia de Europa Laica';				
								$datosEmailCoordSecreTesor['TESORERO']['email'] = 'btesoreria@europalaica.org';			
								$datosEmailCoordSecreTesor['TESORERO']['nombre'] = 'Tesorerería de Europa Laica';
								$datosEmailCoordSecreTesor['SECRETARIO']['email'] = 'bsecretaria@europalaica.org';	
								$datosEmailCoordSecreTesor['SECRETARIO']['nombre'] = 'Secretaría de Europa Laica';
								*/				
								//echo "<br><br>4 modeloSocios:buscarEmailCoordSecreTesor:datosEmailCoordSecreTesor:";print_r($datosEmailCoordSecreTesor);		
							
								$reDatosAreaGestion = buscarDatosAreaGestion($reDatosAgrupacionSocio['resultadoFilas']['CODAGRUPACION']);
								
								//echo "<br><br>5-b-1 modeloSocios:buscarEmailCoordSecreTesor:reDatosAreaGestion: ";print_r($reDatosAreaGestion);
								
								if ($reDatosAreaGestion['codError'] !== '00000')
								{$datosEmailCoordSecreTesor =	$reDatosAreaGestion;		 
									$arrMensaje['textoComentarios'] = "Error del sistema al buscar datos área territorial de gestión, vuelva a intentarlo pasado un tiempo ";			
									require_once './modelos/modeloErrores.php';
									insertarError($reDatosAreaGestion);		
								}
								else 
								{	$datosEmailCoordSecreTesor['AREAGESTION'] = $reDatosAreaGestion['resultadoFilas'];
								
										//echo "<br><br>5-b-2 modeloSocios:buscarEmailCoordSecreTesor:datosEmailCoordSecreTesor['AREAGESTION']: ";print_r($datosEmailCoordSecreTesor['AREAGESTION']);
								}	
						}
			}		
			else //($reDatosAgrupacionSocio['resultadoFilas']['CODAGRUPACION'] == '00000000')//ES ESTATAL
			{
						$datosEmailCoordSecreTesor['PRESIDENTE']['email'] = $reDatosAgrupacionSocio['resultadoFilas']['EMAILCOORD'];	
						$datosEmailCoordSecreTesor['PRESIDENTE']['nombre'] = $reDatosAgrupacionSocio['resultadoFilas']['NOMAGRUPACION'];
						$datosEmailCoordSecreTesor['TESORERO']['email'] = $reDatosAgrupacionSocio['resultadoFilas']['EMAILTESORERO'];	
						$datosEmailCoordSecreTesor['TESORERO']['nombre'] = $reDatosAgrupacionSocio['resultadoFilas']['NOMAGRUPACION'];
						$datosEmailCoordSecreTesor['SECRETARIO']['email'] = $reDatosAgrupacionSocio['resultadoFilas']['EMAILSECRETARIO'];	
						$datosEmailCoordSecreTesor['SECRETARIO']['nombre'] = $reDatosAgrupacionSocio['resultadoFilas']['NOMAGRUPACION'];
						
					/* PARA PRUEBAS DE DESARROLO, asi no llegaría email a ellos
						$datosEmailCoordSecreTesor['PRESIDENTE']['email'] = 'bpresidencia@europalaica.org';
						$datosEmailCoordSecreTesor['PRESIDENTE']['nombre'] = 'Presidencia de Europa Laica';				
						$datosEmailCoordSecreTesor['TESORERO']['email'] = 'btesoreria@europalaica.org';			
						$datosEmailCoordSecreTesor['TESORERO']['nombre'] = 'Tesorerería de Europa Laica';
						$datosEmailCoordSecreTesor['SECRETARIO']['email'] = 'bsecretaria@europalaica.org';	
						$datosEmailCoordSecreTesor['SECRETARIO']['nombre'] ='Secretaría de Europa Laica';
						*/
						//echo"<br><br>6 modeloSocios:buscarEmailCoordSecreTesor:datosEmailCoordSecreTesor:";print_r($datosEmailCoordSecreTesor);	
			}
			$datosEmailCoordSecreTesor['NOMAGRUPACION'] = $agrupNom;
	}//else  !if ( !isset($codUser) || empty($codUser))
	
	if (isset($arrMensaje) && !empty($arrMensaje))
	{		$datosEmailCoordSecreTesor['arrMensaje'] = $arrMensaje;
	}	
 //echo"<br><br>7 modeloSocios:buscarEmailCoordSecreTesor:datosEmailCoordSecreTesor:";print_r($datosEmailCoordSecreTesor);	
	
	return 	$datosEmailCoordSecreTesor;	
}	
//------------------------------ Fin buscarEmailCoordSecreTesor ----------------


/*---------------------- Inicio buscarCuotasAnioEL -----------------------------
Busca datos de las cuotas que cobra de EL por anioCuota,$codCuota
y los devuelve en formato de array además de los códigod de errores
Se graban los errores.
													
LLAMADO: controladorSocios.php:altaSocio(),mostrarDatosSocio(), .....
        cPresidente.php:altaSocioPorGestorPres()
        cCoordinador.php:altaSocioPorGestorCoord()
        modeloPresCoord.php:altaSocioPendienteConfirmadaPorGestor()
        modeloSocios.php:altaSociosConfirmada()
								modelo/libs/prepMostrarActualizarCuotaSocio.php
								
								cTesorero.php:actualizarCuotasVigentesELTes()
									
LLAMA: usuariosConfig/BBDD/MySQL/configMySQL.php:conexionUsuariosDB(),
       modeloMySQL.php/buscarCadSql(),modeloErrores.php:insertarError()
				 
OBSERVACIONES:
Agustín: 2020-03-22: Incluyo PHP: PDOStatement::bindParam			

"modeloSocios.php:buscarCuotasAnioEL($anioCuota,$codCuota)", 
También sustituye a la función modeloTesorero.php:buscarCuotasEL() así se evita duplicidad ya que 
devolvía casi el mismo return.												
------------------------------------------------------------------------------*/	
function buscarCuotasAnioEL($anioCuota,$codCuota)
{
	//echo '<br><br>0-1 modeloSocios.php:buscarCuotasAnioEL:anioCuota: ';print_r($anioCuota);
	//echo '<br><br>0-2 modeloSocios.php:buscarCuotasAnioEL:codCuota: ';print_r($codCuota);
	
	$resDatosCuotasAnioEL['nomFuncion'] = "buscarCuotasAnioEL";
 $resDatosCuotasAnioEL['nomScript'] = "modeloSocios.php";
	$resDatosCuotasAnioEL['codError'] = '00000';
	$resDatosCuotasAnioEL['errorMensaje'] = '';	
 
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	require_once "BBDD/MySQL/conexionMySQL.php";
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);

	if ($conexionDB['codError'] !== '00000')	
	{ $datosCuotasAnioEL = $conexionDB;
	}
	else	 //$conexionDB['codError'] == '00000'
	{	
		$tablasBusqueda = "IMPORTEDESCUOTAANIO";
	 $camposBuscados = "*";
		
		/* Probado con los siguientes valores: 
		   $codCuota = "";$anioCuota = ''; $codCuota = NULL;$anioCuota = NULL;		$codCuota = "%";$anioCuota = '%';
		   $codCuota = "%en";$anioCuota = '20%'; $codCuota = "%e%";$anioCuota = '%20%'; $codCuota = "Joven";$anioCuota = '2020'; 
  */		
	 if ( !isset($anioCuota) || empty($anioCuota) || $anioCuota == '%')
		{ $anioCuota = "%";
	 }
	 if ( !isset($codCuota) || empty($codCuota) || $codCuota == '%')
		{ $codCuota = "%";	 }
		
		$arrBind = array(':anioCuota' => $anioCuota, ':codCuota' => $codCuota );

	 $cadCondicionesBuscar = " WHERE IMPORTEDESCUOTAANIO.ANIOCUOTA LIKE :anioCuota "." AND IMPORTEDESCUOTAANIO.CODCUOTA LIKE :codCuota ".
																										" ORDER BY IMPORTEDESCUOTAANIO.ANIOCUOTA, IMPORTEDESCUOTAANIO.CODCUOTA";
	
  $cadSql = " SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar ";
		//echo '<br><br>2-1 modeloSocios.php:buscarCuotasAnioEL:cadSql: ';print_r($cadSql);
	
		$datosCuotasAnioEL = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);		
		
		//echo '<br><br>2-2 modeloSocios.php:buscarCuotasAnioEL:datosCuotasAnioEL: ';print_r($datosCuotasAnioEL);
		
		if ($datosCuotasAnioEL['codError'] !== '00000')
		{$resDatosCuotasAnioEL = $datosCuotasAnioEL;		
			$resDatosCuotasAnioEL['errorMensaje'] = " modeloSocios.php:buscarCuotasAnioEL():numFilas=0: Error al buscar cuotas EL: ".$resDatosCuotasAnioEL['errorMensaje'];
		}	
		elseif ($datosCuotasAnioEL['numFilas'] == 0)//no es error SQL
		{ $resDatosCuotasAnioEL['codError'] = '80004'; //no encontradas cuotas que cumplan la condición
			 $resDatosCuotasAnioEL['errorMensaje'] = " modeloSocios.php:buscarCuotasAnioEL():numFilas=0: No hay cuotas EL que cumplan la condición ";
		}
		else
		{$indexAnio = $datosCuotasAnioEL['resultadoFilas'][0]['ANIOCUOTA'];
			
			foreach (	$datosCuotasAnioEL['resultadoFilas'] as $fila => $valorFila)
		 {
			 if ($indexAnio == $valorFila['ANIOCUOTA'])				
				{ 			
				 foreach ($valorFila as $col=>$valCol)
					{
						 $auxCol[$col]['valorCampo'] = $valCol;							
					}					
     $auxCuotas[$valorFila['CODCUOTA']] =	$auxCol;
 			}
				else
				{$auxAnios[$indexAnio] = $auxCuotas;	
				 $cuotasAnios['ANIOCUOTA'] = $auxAnios;				
					$indexAnio = $valorFila['ANIOCUOTA'];
					
					foreach ($valorFila as $col=>$valCol)
					{
						 $auxCol[$col]['valorCampo'] = $valCol;							
					}					
     $auxCuotas[$valorFila['CODCUOTA']] =	$auxCol;				
				}
		 }	
		 $auxAnios[$indexAnio] = $auxCuotas;	
			$cuotasAnios['ANIOCUOTA'] = $auxAnios;	
				
			//echo '<br><br>3 modeloSocios.php:buscarCuotasAnioEL:cuotasAnios: ';print_r($cuotasAnios);   			
			$resDatosCuotasAnioEL['datosFormCuotaSocio']['resultadoFilas'] = $cuotasAnios;			
		}
	}//$conexionDB['codError'] == '00000'
	
	$resDatosCuotasAnioEL['datosFormCuotaSocio']['numFilas'] = $datosCuotasAnioEL['numFilas'];	
 //echo '<br><br>4 modeloSocios.php:buscarCuotasAnioEL:resDatosCuotasAnioEL: ';print_r($resDatosCuotasAnioEL);
	
	if ($resDatosCuotasAnioEL['codError'] !== '00000')
	{	
   require_once './modelos/modeloErrores.php';
			insertarError($resDatosCuotasAnioEL);		
 }
	//echo '<br><br>5 modeloSocios.php:buscarCuotasAnioEL:resDatosCuotasAnioEL: ';print_r($resDatosCuotasAnioEL);
	
	return $resDatosCuotasAnioEL;
}
//------------------------------ Fin buscarCuotasAnioEL ------------------------


/*------------------------- Inicio altaSocios----------------------------------
En esta función se dan de alta los socios en la tabla USUARIO y quedarán 
(ESTADO=PENDIENTE-CONFIRMAR) y se guardan los datos personales 
en SOCIOSCONFIRMAR, y se envía un email pidiendo confirmación al socio
(Posteriormente cuando el socio confirma su alta se ponen a NULL los datos 
personales de SOCIOSCONFIRMAR, y se insertan en las tablas definitivas) 
								
RECIBE: un array con los campos del formulario ya validados
DEVUELVE:  un array con ['CODUSER'] y controles de errores

LLAMADA: desde controladorSocios: altaSocio(),
LLAMA: require "../../usuariosConfig/BBDD/MySQL/configMySQL.php"
       ./modelos/conexionMySQL.php:conexionDB() y transationPDO.php
        modeloUsuarios.php: buscarCodMax(),
								modeloMySQL.php:insertarUnaFila()
        modeloErrores.php:insertarError()							
							 Para encriptar contraseñas: sha1(), (ahora no lo uso CC o CEX:encriptarBase64)
							
OBSERVACIONES: Se graban los errores del sistema en ERRORES
																											
2020-05-03: modifico errores, 
añado transationPDO.php para incluir PHP: PDOStatement::bindParam																		
------------------------------------------------------------------------------*/
function altaSocios($resValidarCampos)
{
	//echo "<br><br>0 modeloSocios.php:altaSocios:resValidarCampos: ";var_dump($resValidarCampos);	echo "<br>";
	
 $resInsertar['nomScript'] = 'modeloSocios.php';	
	$resInsertar['nomFuncion'] = 'altaSocios';
	$resInsertar['codError'] = '00000';
	$resInsertar['errorMensaje'] = '';
		
	$arrMensaje['textoBoton'] = 'Salir de la aplicación';
	$arrMensaje['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';
	
 $datosSocio = $resValidarCampos['SOCIOSCONFIRMAR'];
	//echo "<br><br>1 modeloSocios.php:altaSocios:datosSocio: "; print_r($datosSocio);
 		
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	require_once "BBDD/MySQL/conexionMySQL.php";
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);
	
 //echo "<br><br>1-0 modeloSocios.php:altaSocios:conexionDB: ";var_dump($conexionDB);echo "<br>";
	if ($conexionDB['codError'] !== "00000")
	{ $resInsertar = $conexionDB;
   $resInsertar['textoComentarios'] = ". modeloSocios.php:altaSocios(): ";
	  $arrMensaje['textoComentarios'] .= "Error del sistema al conectarse a la base de datos";
	}	
 else //$conexionDB['codError']=="00000"
	{
		require_once("BBDD/MySQL/transationPDO.php");
		$resIniTrans = beginTransationPDO($conexionDB['conexionLink']);
		
		//echo "<br><br>1-1 modeloSocios.php:altaSocios:resIniTrans: ";var_dump($resIniTrans);	echo "<br>";
		//echo "<br><br>1-2 modeloSocios.php:altaSocios:conexionDB: ";var_dump($conexionDB);echo "<br>";
		
		if ($resIniTrans['codError'] !== '00000') //será ['codError'] = '70501';
		{$resIniTrans['errorMensaje'] .= 'Error en el sistema, no se ha podido iniciar la transación. ';
			$resIniTrans['numFilas'] = 0;
			$resInsertar = $resIniTrans;
			//echo "<br><br>1-3 modeloSocios.php:altaSocios:resIniTrans: ";var_dump($resIniTrans);	echo "<br>";						
		}			
		else //$resIniTrans['codError'] == '00000'
		{
			require_once './modelos/libs/buscarCodMax.php';
	  $resulBuscarCodMax = buscarCodMax('USUARIO','CODUSER',$conexionDB['conexionLink']);//no necesita pasar $arrBind, en modeloUsuarios.php:
	  
			//echo "<br><br>1-5 modeloSocios.php:altaSocios:resulBuscarCodMax: ";print_r($resulBuscarCodMax);
			
			if ($resulBuscarCodMax['codError'] !== '00000')
			{ $resInsertar = $resulBuscarCodMax;
			}
			else //$resulBuscarCodMax['codError']=='00000')
			{
				$arrValoresInser['usuario']['CODUSER'] = $resulBuscarCodMax['valorCampo'];	
				$arrValoresInser['socioConfirmar']['CODUSER'] = $resulBuscarCodMax['valorCampo'];
    //------- encripta pass usuario --------------------------------------------
	   $arrValoresInser['usuario']['PASSUSUARIO'] = sha1($datosSocio['PASSUSUARIO']['valorCampo']);//encripta pero habría que cambiar a uno más seguro
	   $arrValoresInser['usuario']['USUARIO'] = $datosSocio['USUARIO']['valorCampo'];		
				$arrValoresInser['usuario']['ESTADO']	= 'PENDIENTE-CONFIRMAR';	  	
				
 	  $arrValoresInser['socioConfirmar']['FECHANAC'] = $datosSocio['FECHANAC']['anio']['valorCampo'].'-'.
		                                                   $datosSocio['FECHANAC']['mes']['valorCampo'].'-'.
														                                       $datosSocio['FECHANAC']['dia']['valorCampo'];
	   unset($datosSocio['FECHANAC']);																																																			
																															
				//echo "<br><br>2-2 modeloSocios.php:altaSocios:arrValoresInser['socioConfirmar']['FECHANAC']: ";print_r($arrValoresInser['socioConfirmar']['FECHANAC']);																															
    //-------------- Inicio para cuentas bancarias -----------------------------
				if (isset($datosSocio['CUENTAIBAN']['valorCampo']) && !empty($datosSocio['CUENTAIBAN']['valorCampo']))
				{	$arrValoresInser['socioConfirmar']['MODOINGRESO'] = 'DOMICILIADA';	//NO SERIA NECESARIO AQUI		
				  $arrValoresInser['socioConfirmar']['CUENTAIBAN']  = $datosSocio['CUENTAIBAN']['valorCampo'];
	
						/*Para encriptar IBAN:
						  require_once "../../usuariosLibs/encriptar/encriptacionBase64.php";	
        $arrValoresInser['socioConfirmar']['CUENTAIBAN'] = encriptarBase64($datosSocio['CUENTAIBAN']['valorCampo']);		
						  echo "2-3 modeloSocios.php:altaSocios:['socioConfirmar']['CUENTAIBAN']: ";print_r($arrValoresInser['socioConfirmar']['CUENTAIBAN']);
						*/
				}				
				elseif (isset($datosSocio['CUENTANOIBAN']['valorCampo']) && !empty($datosSocio['CUENTANOIBAN']['valorCampo']))
				{$arrValoresInser['socioConfirmar']['MODOINGRESO'] = 'DOMICILIADA'; //NO SERIA NECESARIO aqui		
					 
					$arrValoresInser['socioConfirmar']['CUENTANOIBAN'] = $datosSocio['CUENTANOIBAN']['valorCampo'];					
					//echo "2-4 modeloSocios.php:altaSocios:['socioConfirmar'][CUENTANOIBAN']: ";print_r($datosSocio['CUENTANOIBAN']['valorCampo']);
				}
    //-------------- Fin para cuentas bancarias --------------------------------		
				else
				{//$arrValoresInser['socioConfirmar']['MODOINGRESO'] ='';								
					$arrValoresInser['socioConfirmar']['MODOINGRESO'] ='SIN-DATOS';			
				}				
				unset($datosSocio['CUENTAIBAN']);
				unset($datosSocio['CUENTANOIBAN']);
				unset($datosSocio['USUARIO']);
				unset($datosSocio['PASSUSUARIO']);	
				unset($datosSocio['RPASSUSUARIO']);				
				unset($datosSocio['privacidad']);
				unset($datosSocio['REMAIL']);		
				// las tres siguientes se podrían evitar si no se utilizasen como hidden en el formulario
				unset($datosSocio['CODCUOTAGeneral']);
				unset($datosSocio['IMPORTECUOTAANIOELGeneral']);
				unset($datosSocio['IMPORTECUOTAANIOELJoven']);
				unset($datosSocio['IMPORTECUOTAANIOELParado']);
								
				foreach ($datosSocio as $nomCampo => $valNomCampo)
				{
					$arrValoresInser['socioConfirmar'][$nomCampo] = $valNomCampo['valorCampo'];
				}
				
				if (isset($arrValoresInser['CODPAISDOM']) && $arrValoresInser['CODPAISDOM'] == 'ES')
				{$arrValoresInser['socioConfirmar']['CODPROV'] = substr($arrValoresInser['CP'],0,2);
				}
				else 
				{unset($arrValoresInser['socioConfirmar']['CODPROV']);//O $arrValoresInser['CODPROV']=NULL;
				}			
				
				$arrValoresInser['socioConfirmar']['FECHAREGISTRO']	= date('Y-m-d');	
				$arrValoresInser['socioConfirmar']['FECHAENVIOEMAILULTIMO']	= date('Y-m-d');	
				$arrValoresInser['socioConfirmar']['FECHACONFIRMACION_ANULACION']	= '0000-00-00';	
				$arrValoresInser['socioConfirmar']['NUMENVIOS']	= 1;
				
				//añadido el 2024-09-28 para evitar modeloMySQL.php, Línea: 398, Excepción previa: , getMessage(): 
				//SQLSTATE[HY000]: General error: 1364 Field 'TIPOMIEMBRO' doesn't have a default value		
												
				$arrValoresInser['socioConfirmar']['TIPOMIEMBRO']	= 'socio';
				$arrValoresInser['socioConfirmar']['ESTADOCUOTA']	= 'PENDIENTE-COBRO';				
				
				//echo "<br><br>3-1 modeloSocios.php:altaSocios:arrValoresInser: ";print_r($arrValoresInser);

				$resInsertarUsuario = insertarUnaFila('USUARIO',$arrValoresInser['usuario'],$conexionDB['conexionLink']);//no necesita pasar $arrBind lo trata internamente
				//echo "<br><br>3-1 modeloSocios.php:altaSocios:resInsertarUsuario: ";print_r($resInsertarUsuario);

				if ($resInsertarUsuario['codError'] !== '00000')			
		  {$resInsertar = $resInsertarUsuario;
		  }
		  else //$resInsertarUsuario=='00000'
		  {										
				 $resInsertarSociosConfirmar = insertarUnaFila('SOCIOSCONFIRMAR',$arrValoresInser['socioConfirmar'],$conexionDB['conexionLink']);//no necesita pasar $arrBind lo trata internamente
	
	    //echo "<br><br>4-2 modeloSocios.php:altaSocios:resInsertar:resInsertarSociosConfirmar: ";print_r($resInsertarSociosConfirmar);
	
					if ($resInsertarSociosConfirmar['codError'] !== '00000')			
			  {$resInsertar = $resInsertarSociosConfirmar;
			  }
			  else //$resInsertarSociosConfirmar['codError']=='00000'
			  {
						$resFinTrans = commitPDO($conexionDB['conexionLink']);
						
						//echo "<br><br>4-3 modeloSocios.php:altaSocios:resFinTrans: ";var_dump($resFinTrans);
						if ($resFinTrans['codError'] !== '00000') //será ['codError'] = '70502';
			   {$resFinTrans['errorMensaje'] = 'Error en el sistema, no se han podido registrar los datos del socio/a. ';
							$resFinTrans['numFilas'] = 0;	
					
					  $resInsertar = $resFinTrans;	
					  //echo "<br><br>4-4 modeloSocios.php:altaSocios:resInsertar: ";print_r($resInsertar);
			   }
				  else
				  {
							$resInsertar['codError'] = '00000';
		     $resInsertar['CODUSER'] = $arrValoresInser['usuario']['CODUSER']; 
							/*-------- para mostrar después de que el socio registre el alta ------*/
							
							$arrMensaje['textoCabecera'] = 'REGISTRADA PETICIÓN DE HACERTE SOCIO/A';
							
					  $arrMensaje['textoComentarios'] = "Hemos anotado tu solicitud para hacerte socio/a de Europa Laica. 
							<br /><br />
							Recibirás un mensaje en tu correo electrónico <strong>".$arrValoresInser['socioConfirmar']['EMAIL'].
							"</strong> para que <strong>confirmes</strong> tu deseo de ser socio/a de Europa Laica y validar tu email. 
							<br /><br />
							Una vez hayas validado tu email, podrás entrar en el -Área de Soci@s- como socia/o de Europa Laica y 
							modificar tus datos si lo deseas.		
       <br /><br />
       NOTA: Si no te llega el email, mira en la carpeta 'Correo no deseado' o en 'Spam'. 				
							<strong>hotmail</strong> y otros servidores de correos, a veces lo envían es esa carpeta. 
							Si no lo encuentras puedes ponerte en contacto con \"info@europalaica.org\" y te ayudaremos.								
							<br /><br /><br />							
						 Para ser socio/a de pleno derecho tienes que abonar la cuota anual.";
							
							/*	En Europa Laica NO aceptamos subvenciones, todas las aportaciones económicas que recibimos provienen 
							de las cuotas y donaciones de nuestras socias, socios y simpatizantes. (Ya está incluido en el formulario)*/
						}
				 }	//else $resInsertarSociosConfirmar['codError']=='00000'					
				}	//else $resInsertarUsuario['codError'] =='00000'				
	  }//else $resulBuscarCodMax['codError']=='00000'
			
			//echo "<br><br>5-0 modeloSocios:altaSocios:resInsertar: ";print_r($resInsertar);
				
			//---------------- Inicio tratamiento errores -------------------------------	
		
			//--- Inicio deshacer transación en las tablas modificadas ---------------				
			if ($resInsertar['codError'] !== '00000')
			{
				$resDeshacerTrans = rollbackPDO($conexionDB['conexionLink']);
				//echo "<br><br>5-1 modeloSocios.php:altaSocios:resDeshacerTrans: ";print_r($resDeshacerTrans);
					
				if ($resDeshacerTrans['codError'] !== '00000')//será ['codError'] = '70503';
				{ $resInsertar['errorMensaje'] .= $resDeshacerTrans['errorMensaje'];			
						//echo "<br><br>5-2 modeloSocios.php:altaSocios:resInsertar: ";print_r($resInsertar);
				}
			}//--- Fin deshacer transación en las tablas modificadas -----------------

		}//else $resIniTrans['codError'] == '00000'
		
	 //--- Inicio Grabar en tabla ERRORES, incluido error beginTransationPDO()--		
		if ($resInsertar['codError'] !== '00000' || (isset($resDeshacerTrans['codError']) && $resDeshacerTrans['codError'] !== '00000') )
		{	
				if ( isset($resInsertar['textoComentarios']) ) 
				{ $resInsertar['textoComentarios'] = ". modeloSocios.php:altaSocios(): ".$resInsertar['textoComentarios'];}	
				else
				{	$resInsertar['textoComentarios'] = ". modeloSocios.php:altaSocios(): ";}	//se guardará en tabla ERRORES, y se muestra en email a emailErrorWMaster()				
			
				require_once './modelos/modeloErrores.php'; //si es un error de conexión a la tabla error, insertar errores 
				$resInsertarErrores = insertarError($resInsertar);//no necesita pasar $arrBind
		
				if ($resInsertarErrores['codError'] !== '00000')
				{$resInsertar['codError'] = $resInsertarErrores['codError'];
					$resInsertar['errorMensaje'] .= $resInsertarErrores['errorMensaje'];				 
				}					
		}//--- Fin Grabar en tabla ERRORES, incluido error beginTransationPDO()----
		
	 //---------------- Fin tratamiento errores ------------------------------------	

 }//else $conexionDB['codError']=="00000"
	
	$resAltaSocios = $resInsertar;
	$resAltaSocios['arrMensaje'] = $arrMensaje;
	
	//echo "<br><br>6 modeloSocios.php:altaSocios:resAltaSocios: ";print_r($resAltaSocios);
	
 return 	$resAltaSocios; 	
}
/*------------------------------ Fin altaSocios (versión: PDO 2020-05-03)-------*/

/*------------------------- Inicio altaSociosConfirmada ------------------------
En esta función se dan de alta los socios de forma definitiva, a partir de los 
datos de la confirmación del alta por el socio, insertando las filas en las 
correspondientes tablas con datos que se reciben del controlador (a partir de la
 tabla "SOCIOSCONFIRMAR" en la que al final se ponen a NULL campos con datos 
	personales del socio en la tabla SOCIOSCONFIRMAR)
-Hay 3 llamadas a la función "insertarCuotaAnioSocio()", debido a que es 
necesario tener en cuenta que puede haber socios pendientes de confirmar del año
anterior, y eso complica bastante pues varían  ciertos datos que hay que tener 
en cuenta a la hora de insertar. 
Acaso se podría simplificar algo, reutilizando pero puede ser mas complejo.
								
RECIBE: $codUser
DEVUELVE: array con [CODUSER],[arrMensaje][textoCabecera],[arrMensaje][textoComentarios]
['codError']

LLAMADA: controladorSocios.php: confirmarAltaSocio(),
LLAMA: 
require "../../usuariosConfig/BBDD/MySQL/configMySQL.php
a varias funciones dentro de "modeloSocios.php":buscarDatosSocioConfirmar(),
insertarSocio(),insertarCuotaAnioSocio(),actualizarSocioConfirmar(),buscarCuotasAnioEL()
y otras en modeloUsuarios.php:actualizUsuario(),insertarUsuarioRol(),insertarMiembro()
modelos/modeloErrores.php:insertarError()
							
OBSERVACIONES:  Controlo la TRANSACTION, y se graban errores del sistema en ERRORES
Es muy parecida a modelosPresCoord.php:altaSocioPendienteConfirmadaPorGestor()
Incluye IBAN y DATOS PARA PAGO SEPA
Agustín 2020-03-25: modifico para incluir PHP: PDOStatement::bindParam																			
------------------------------------------------------------------------------*/
function altaSociosConfirmada($codUser)
{
	//echo "<br><br>0 modeloSocios.php:altaSociosConfirmada:codUser: ";var_dump($codUser);echo "<br>";
	
 $resInsertar['nomScript'] = "modeloSocios.php";	
	$resInsertar['nomFuncion'] = "altaSocios";
	$resInsertar['codError'] = '00000';
	$resInsertar['errorMensaje'] = '';
	$arrMensaje['textoCabecera'] = '';
 		
 require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	require_once "BBDD/MySQL/conexionMySQL.php";			
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);	

	if ($conexionDB['codError'] !== "00000")
	{ $resInsertar = $conexionDB;	
   $resInsertar['textoComentarios'] = ". modeloSocios.php:altaSociosConfirmada(): ";
	}
 else //$conexionDB['codError']=="00000"
	{
		require_once("BBDD/MySQL/transationPDO.php");
		$resIniTrans = beginTransationPDO($conexionDB['conexionLink']);
		
		//echo "<br><br>1-1 modeloSocios.php:altaSociosConfirmada:resIniTrans: ";var_dump($resIniTrans);	echo "<br>";
		//echo "<br><br>1-2 modeloSocios.php:altaSociosConfirmada:conexionsDB: ";var_dump($conexionsDB);echo "<br>";
		
		if ($resIniTrans['codError'] !== '00000') //será ['codError'] = '70501';
		{	$resIniTrans['errorMensaje'] = 'Error en el sistema, no se ha podido iniciar la transación. ';
				$resIniTrans['numFilas'] = 0;
							
				$resInsertar = $resIniTrans;				
				//echo "<br><br>1-3 modeloSocios.php:altaSociosConfirmada:resIniTrans: ";var_dump($resIniTrans);	echo "<br>";
		}			
		else //$resIniTrans['codError'] == '00000'
		{		
		  $resBuscarDatosSocioConfirmar = buscarDatosSocioConfirmar($codUser);//en modeloSocios.php busca en tabla SOCIOSCONFIRMAR
				
    //echo "<br><br>2-1 modeloSocios.php:altaSociosConfirmada:resBuscarDatosSocioConfirmar: "; print_r($resBuscarDatosSocioConfirmar);		
				
			 if ($resBuscarDatosSocioConfirmar['codError'] !== '00000')			
		  { $resInsertar = $resBuscarDatosSocioConfirmar;
		  }
		  else //($resDatosSocioConfirmar['codError']=='00000')
		  {	$datosSocioConfirmar =	$resBuscarDatosSocioConfirmar['resultadoFilas'][0];
				
			  //-- Inicio actualizar tabla USUARIO:  PENDIENTE-CONFIRMACIO-->alta -------

					$datosUsuario['ESTADO']['valorCampo'] = 'alta';
					$datosUsuario['OBSERVACIONES']['valorCampo'] = 'Alta inciada por usuario y finalizada por usuario';		
 
					$resActualizarUsuario = actualizUsuario('USUARIO',$codUser,$datosUsuario,$conexionDB['conexionLink']);//en modeloUsuarios.php     			
		
		   //echo "<br><br>2-2 modeloSocios.php:altaSociosConfirmada:resActualizarUsuario: "; print_r($resActualizarUsuario);
				 //-- Fin actualizar en tabla USUARIO:  PENDIENTE-CONFIRMACION --> alta ----
					
     //------------  Inicio insertarUsuarioRol =socio --------------------------
		   if ($resActualizarUsuario['codError'] !== '00000')			
		   {$resInsertar = $resActualizarUsuario;
		   }
					elseif ($resActualizarUsuario['numFilas'] <= 0)
					{ $resInsertar['codError'] = '80001';
							$resInsertar['errorMensaje'] = 'No se pudo modificar la tabla USUARIO';
					}						
		   else // $resActualizarUsuario['codError'] == '00000'
		   {$datosRolUsuario['CODUSER']['valorCampo'] =	$codUser;//devuelve siguiente a máx 				
					 $datosRolUsuario['CODROL']['valorCampo'] = '1';//rol socio			
						
				  $resInsertarUsuarioRol = insertarUsuarioRol($datosRolUsuario);//todos se dan de alta con rol socio codigo '1', en modeloUsuarios.php    
			   //echo "<br><br>2-3 modeloSocios.php:altaSociosConfirmada:insertarUsuarioRol: ";print_r($resInsertarUsuarioRol);   
				  //------------  Fin insertarUsuarioRol =socio ----------------------------
					
		    if ($resInsertarUsuarioRol['codError'] !== '00000')			
		    {$resInsertar = $resInsertarUsuarioRol;
		    }
			   else //$resInsertarUsuarioRol['codError']=='00000'
		    {
						 //---------------Inicio insertarMiembro en tabla MIEMBRO ----------------
						 // se podría hacer un for y despues eliminar los campos que sobren
						 $datosMiembro['CODUSER']['valorCampo'] = $codUser;
						 $datosMiembro['TIPOMIEMBRO']['valorCampo'] =	"socio";					 
	      $datosMiembro['CODPAISDOC']['valorCampo'] = $datosSocioConfirmar['CODPAISDOC'];
							$datosMiembro['TIPODOCUMENTOMIEMBRO']['valorCampo'] = $datosSocioConfirmar['TIPODOCUMENTOMIEMBRO'];
							$datosMiembro['NUMDOCUMENTOMIEMBRO']['valorCampo'] = $datosSocioConfirmar['NUMDOCUMENTOMIEMBRO'];
							$datosMiembro['APE1']['valorCampo'] = $datosSocioConfirmar['APE1'];
							$datosMiembro['APE2']['valorCampo'] = $datosSocioConfirmar['APE2'];
							$datosMiembro['NOM']['valorCampo'] = $datosSocioConfirmar['NOM'];
							$datosMiembro['SEXO']['valorCampo'] = $datosSocioConfirmar['SEXO'];
							$datosMiembro['FECHANAC']['valorCampo'] = $datosSocioConfirmar['FECHANAC'];
							$datosMiembro['TELFIJOCASA']['valorCampo'] = $datosSocioConfirmar['TELFIJOCASA'];
							$datosMiembro['TELFIJOTRABAJO']['valorCampo'] = $datosSocioConfirmar['TELFIJOTRABAJO'];							
							$datosMiembro['TELMOVIL']['valorCampo'] = $datosSocioConfirmar['TELMOVIL'];
							$datosMiembro['PROFESION']['valorCampo'] = $datosSocioConfirmar['PROFESION'];
							$datosMiembro['ESTUDIOS']['valorCampo'] = $datosSocioConfirmar['ESTUDIOS'];
							$datosMiembro['EMAIL']['valorCampo'] = $datosSocioConfirmar['EMAIL'];
							$datosMiembro['EMAILERROR']['valorCampo'] = 'NO';//un socio necesita un email para darse de alta siempre
							$datosMiembro['INFORMACIONEMAIL']['valorCampo'] = $datosSocioConfirmar['INFORMACIONEMAIL'];
							$datosMiembro['INFORMACIONCARTAS']['valorCampo'] = $datosSocioConfirmar['INFORMACIONCARTAS'];
							$datosMiembro['COLABORA']['valorCampo'] = $datosSocioConfirmar['COLABORA'];
							$datosMiembro['CODPAISDOM']['valorCampo'] = $datosSocioConfirmar['CODPAISDOM'];
							$datosMiembro['DIRECCION']['valorCampo'] = $datosSocioConfirmar['DIRECCION'];
							$datosMiembro['CP']['valorCampo'] = $datosSocioConfirmar['CP'];
							$datosMiembro['LOCALIDAD']['valorCampo'] = $datosSocioConfirmar['LOCALIDAD'];
						
							$datosMiembro['COMENTARIOSOCIO']['valorCampo'] = $datosSocioConfirmar['COMENTARIOSOCIO'];
							$datosMiembro['OBSERVACIONES']['valorCampo'] = $datosSocioConfirmar['OBSERVACIONES'];						
							
       //echo "<br><br>2-4 modeloSocios.php:altaSociosConfirmada:datosMiembro: ";print_r($datosMiembro);
							
							$resInsertarMiembro = insertarMiembro($datosMiembro);//en modeloUsuarios.php    
	      //echo "<br><br>2-5-0 modeloSocios.php:altaSociosConfirmada:resInsertarMiembro: ";print_r($resInsertarMiembro);
			    //--------------------- Fin insertarMiembro -----------------------------
	
							//--------------------- Inicio insertar Socio ---------------------------
			    if ($resInsertarMiembro['codError'] !== '00000')			
			    {$resInsertar = $resInsertarMiembro;
			    }
				   else //$resInsertarMiembro['codError']=='00000'
			    {$datosSocio['CODUSER']['valorCampo']  = $codUser;
							 $datosSocio['CODAGRUPACION']['valorCampo'] =  $datosSocioConfirmar['CODAGRUPACION'];
								$datosSocio['CODCUOTA']['valorCampo'] =  $datosSocioConfirmar['CODCUOTA'];

								$datosSocio['CUENTAIBAN']['valorCampo']   =  $datosSocioConfirmar['CUENTAIBAN'];
								$datosSocio['CUENTANOIBAN']['valorCampo'] =  $datosSocioConfirmar['CUENTANOIBAN'];								
															
								//--- $modoIngresoCuota y $ordenarCobroBanco, se utilizarán también mas adelante en insertarCuotaAnioSocio ---
							 if ( isset($datosSocioConfirmar['CUENTAIBAN']) && !empty($datosSocioConfirmar['CUENTAIBAN'])	)	
								{ $modoIngresoCuota  = 'DOMICILIADA';
							
							   if($datosSocioConfirmar['IMPORTECUOTAANIOSOCIO'] == 0 ) //Si existe cuenta pero IMPORTECUOTAANIOSOCIO = 0, será EXENTO.
										{	$ordenarCobroBanco = 'NO';	
										}
										else
										{ $ordenarCobroBanco = 'SI';
										}
								}
						  elseif ( isset($datosSocioConfirmar['CUENTANOIBAN']) && !empty($datosSocioConfirmar['CUENTANOIBAN']) )	
								{ 
							   $modoIngresoCuota  = 'DOMICILIADA';
          $ordenarCobroBanco = 'NO'; //por ahora no se pueden domiciliar estas cuentas CUENTANOIBAN en el B. Santander
								}								
								else
								{ $modoIngresoCuota  = 'SIN-DATOS';
          $ordenarCobroBanco = 'NO';
								}	
         /*---- las columnas SECUENCIAADEUDOSEPA = FRST y 
									 FECHAACTUALIZACUENTA=fecha alta, se añaden en función insertarSocio($datosSocio)
										 en este caso no hay que tratar datos del Formulario					
         ---------------------------------------------------------------------*/								
								
								$datosSocio['MODOINGRESO']['valorCampo'] =  $modoIngresoCuota;
							
								$datosSocio['IMPORTECUOTAANIOSOCIO']['valorCampo'] = $datosSocioConfirmar['IMPORTECUOTAANIOSOCIO'];	
						
					   //echo "<br><br>2-5-1a modeloSocios.php:altaSociosConfirmada:datosSocio: ";print_r($datosSocio);
								
					   $resInsertarSocio = insertarSocio($datosSocio);//en modeloSocios.php    
								/* Dentro de esta función insertarSocio(): halla Codmax. para el CODSOCIO  
								   DEVUELVE CODSOCIO y añade las columnas SECUENCIAADEUDOSEPA = FRST y 
											FECHAACTUALIZACUENTA	= fecha alta	
        */								
					   //echo "<br><br>2-5-1b modeloSocios.php:altaSociosConfirmada:resInsertarSocio: ";print_r($resInsertarSocio);
				    //---------------------------- Fin insertar Socio()---------------------	
															
								/*--------- Inicio insertar en tabla CuotaAnioSocio --------------------		
								  Si la confirmación se realiza en el año siguiente al que inició su registro, 
								 (probable si se registró a finales de diciembre), pueden haberse incrementado 
									 la cuota anual de EL y ser mayor que la elegida por el socio, en ese 
										caso hay que poner la cuota mayor de ambas para el año actual, pero 
										dejar la elegida por el socio en el año anterior por si ya la hubiese abonado								
								----------------------------------------------------------------------*/

				    if ($resInsertarSocio['codError'] !== '00000')			
				    {$resInsertar = $resInsertarSocio;
				    }
					   else //$resInsertarSocio['codError']=='00000'
				    {//---------------------- Inicio Insertar cuotaAnioSocio ---------------------------
				    
								 $datosCuotaSocio['ANIOCUOTA']['valorCampo']= $datosSocioConfirmar['ANIOCUOTA'];//date('Y') o  //date('Y')-1
				     $datosCuotaSocio['CODSOCIO']['valorCampo'] =	$resInsertarSocio['CODSOCIO'];//devuelve siguiente a máx  
									$datosCuotaSocio['CODCUOTA']['valorCampo'] = $datosSocioConfirmar['CODCUOTA'];									
									$datosCuotaSocio['CODAGRUPACION']['valorCampo'] = $datosSocioConfirmar['CODAGRUPACION'];
         $datosCuotaSocio['IMPORTECUOTAANIOSOCIO']['valorCampo'] = $datosSocioConfirmar['IMPORTECUOTAANIOSOCIO'];

									/*---------------------------------------------------------------------------------
									La función insertarCuotaAnioSocio() ya controla que no se pueda insertar una cuota 
								 IMPORTECUOTAANIOSOCIO <IMPORTECUOTAANIOEL, y lo corrige si es necesario.
								 Pero debido a que hay que controlar si es EXENTO (que puede variar según
									los años por ejemplo cambia en 2015) y no se conoce el valor de 
									IMPORTECUOTAANIOSOCIO para el año correspondiente a ese tipo de cuota, 
									necesario para if($datosCuotaSocio['IMPORTECUOTAANIOSOCIO']['valorCampo']!=0)
									o su equivalente if($importeCuotaAnioEL_anioActual!=0), se llama a la 
								 función "buscarCuotasAnioEL()" para obtener los valores de las cuotas 
									vigentes de EL para los años Y y $datosSocioConfirmar['ANIOCUOTA'] 
									(si se inició el proceso alta en año anterior (diciembre pero se da de 
									alta en enero) y las cuotas de EL exentas=0, pueden haber variado, de un año a otro
									-----------------------------------------------------------------------------------*/
									$tipoCuota = $datosCuotaSocio['CODCUOTA']['valorCampo'];
         
         $resImporteCuotaAnio = buscarCuotasAnioEL("%",$tipoCuota);//buscarCuotasAnioEL($anioActual,$tipoCuota);//en modeloSocios.php 	
					
         //echo "<br><br>2-5-2a modeloSocios.php:altaSociosConfirmada:resImporteCuotaAnio: ";print_r($resImporteCuotaAnio);													
						
									if ($resImporteCuotaAnio['codError'] !== '00000')//si ['numFilas']=0 también es incluido como error
									{ $resInsertar['codError'] = $resImporteCuotaAnio['codError'];
									  $resInsertar['errorMensaje'] .= $resImporteCuotaAnio['errorMensaje'];
									}
									else //$resImporteCuotaAnio['codError']=='00000'
									{								
									 /*if($datosSocioConfirmar['CODCUOTA']=='General')
									 {$datosCuotaSocio['ESTADOCUOTA']['valorCampo']='PENDIENTE-COBRO';}
									 else {$datosCuotaSocio['ESTADOCUOTA']['valorCampo']='EXENTO';}		para cuando los demás estaban exentos	*/	
																								
										$anioActual = date('Y');									
										$importeCuotaAnioEL_anioActual = $resImporteCuotaAnio['datosFormCuotaSocio']['resultadoFilas']['ANIOCUOTA'][$anioActual][$tipoCuota]['IMPORTECUOTAANIOEL']['valorCampo'];																					
	         //echo "<br><br>2-5-2b modeloSocios.php:altaSociosConfirmada:importeCuotaAnioEL_anioActual: ";print_r($importeCuotaAnioEL_anioActual);																						

										//if($datosCuotaSocio['IMPORTECUOTAANIOEL']['valorCampo'] != 0)//correspondiente al tipo de cuota del socio
										if($importeCuotaAnioEL_anioActual != 0)//equivale a anterior línea			
										{$datosCuotaSocio['ESTADOCUOTA']['valorCampo'] = 'PENDIENTE-COBRO';
										 //echo "<br><br>2-5-2b-1";
										}
										else
										{$datosCuotaSocio['ESTADOCUOTA']['valorCampo'] = 'EXENTO';//en 2014 entrará, pero no entrará en 2015 joven=5 porque cambia							
	          //echo "<br><br>2-5-2b-2";									
										}
										
										$datosCuotaSocio['MODOINGRESO']['valorCampo'] = $modoIngresoCuota;		

										//echo "<br><br>2-5-2c modeloSocios.php:altaSociosConfirmada:datosCuotaSocio: ";print_r($datosCuotaSocio);
										
										// 2024-10-07 añado las cuatro siguientes para evitar error en nueva versión MariaDB, 
										// aunque se modifico TAMBIÉN en tabla CUOTAANIOSOCIO con esos valores por defecto
										
										$datosCuotaSocio['IMPORTECUOTAANIOPAGADA']['valorCampo'] = 0.00;		
										$datosCuotaSocio['IMPORTEGASTOSABONOCUOTA']['valorCampo'] = 0.00;		
										$datosCuotaSocio['FECHAPAGO']['valorCampo'] = '0000-00-00';		
										$datosCuotaSocio['FECHAANOTACION']['valorCampo'] = '0000-00-00';														
										
										/*--- Inicio: registro y confirmación de alta realizado el socio en el
  										mismo año de inicio de registro. Inserta en CUATOAANIOSOCIO, y 
												la cuota correspondiente a la fecha de confirmación de alta del socio									
										  se inserta una sola fila los datos de SOCIOSCONFIRMAR 
										--------------------------------------------------------------------*/
										if ($datosSocioConfirmar['ANIOCUOTA'] == date('Y'))
										{
										 $datosCuotaSocio['ORDENARCOBROBANCO']['valorCampo'] =	$ordenarCobroBanco;	
											
											/*La función insertarCuotaAnioSocio() ya controla internamente que la
  											cuota elegida por el socio no pueda ser inferior a la cuota de EL 
													para este tipo de CODCUOTA y año concreto,y lo modifica dentro de 
													ella si es necesario, por lo que no es necesario controlarlo aquí.
           -------------------------------------------------------------------*/												
		
										 $resInsertarCuotaAnioSocio = insertarCuotaAnioSocio($datosCuotaSocio,$conexionDB['conexionLink']);//inserta una sola fila para año 'Y' y nada mas, necesita 'CODSOCIO', en modeloSocios.php 
						     //echo "<br><br>2-5-2d-1 modeloSocios.php:altaSociosConfirmada:resInsertarCuotaAnioSocio: ";print_r($resInsertarCuotaAnioSocio);
	 								}
										//-Fin:registro y confirmación alta realizado el socio en el mismo año-									
										
										/*Inicio:registro y confirmación alta realizado el socio en dos años distintos (consecutivos)- 
									   Se insertan dos filas en CUATOAANIOSOCIO: una para año el de 
												registro (año anterior), con los datos de SOCIOSCONFIRMAR por si hubiese
												abonado con paypal o transferencia antes de confirmar alta) Y otra para
												año de confirmación de alta (año actual), en la que hay que poner los datos 
										  de IMPORTECUOTAANIOSOCIO y IMPORTECUOTAANIOEL, adecuados al nuevo año 
										--------------------------------------------------------------------*/										
										elseif ($datosSocioConfirmar['ANIOCUOTA'] < date('Y'))//Se harán dos inserciones una para año de registro por el socio que esta en SOCIOSCONFIRMAR, y otra para año actual date(Y) 
										{                                                     
											/*Inicio insertar en año anterior por si hubiese abonado cuota año anterior por paypal o transferencia ---
											  La función insertarCuotaAnioSocio() ya controla que no se pueda insertar una cuota 
											  IMPORTECUOTAANIOSOCIO <IMPORTECUOTAANIOEL en un año, y lo corrige si es nescesario,
											-------------------------------------------------------------------*/
											
										 $anioAnterior = $datosSocioConfirmar['ANIOCUOTA'];
											
											$importeCuotaAnioEL_anioAnterior =	$resImporteCuotaAnio['datosFormCuotaSocio']['resultadoFilas']['ANIOCUOTA'][$anioAnterior][$tipoCuota]['IMPORTECUOTAANIOEL']['valorCampo'];	
           
											$datosCuotaSocio['ORDENARCOBROBANCO']['valorCampo'] = 'NO';//---- no se pasan órdenes cobro de años anteriores											
	          
											//echo "<br><br>2-5-2e modeloSocios.php:altaSociosConfirmada:importeCuotaAnioEL_anioActual: ";print_r($importeCuotaAnioEL_anioActual);													
											
											//if($datosSocioConfirmar['CODCUOTA']=='General')
		         //if($datosCuotaSocio['IMPORTECUOTAANIOEL']['valorCampo'] != 0)//correspondiente al tipo de cuota del socio	General, ...									
	          if($importeCuotaAnioEL_anioAnterior != 0)//correspondiente al tipo de cuota del socio	General, ...									
											{$datosCuotaSocio['ESTADOCUOTA']['valorCampo'] = 'NOABONADA';//en año anterior no puede ser PENDIENTE-CONFIRMAR
											 //echo "<br><br>2-5-2e-1 ";
											}
											else
											{$datosCuotaSocio['ESTADOCUOTA']['valorCampo'] = 'EXENTO';
											 //echo "<br><br>2-5-2e-2 ";
											}
										 $resInsertarCuotaAnioSocio = insertarCuotaAnioSocio($datosCuotaSocio,$conexionDB['conexionLink']);//inserta una fila con año anterior  ANIOCUOTA<date('Y') se cambia ESTADOCUOTA si procede			
		         //echo "<br><br>2-5-2e-3 modeloSocios.php:altaSociosConfirmada:resInsertarCuotaAnioSocio: ";print_r($resInsertarCuotaAnioSocio);
	 									
											//- Fin insertar en año anterior por si hubiese abonado cuota año anterior por paypal o transferencia ---
											
											//-Inicio insertar en año actual, despues de insertar fila en año anterior --
	 
											if ($resInsertarCuotaAnioSocio['codError'] == '00000') 		
											{$datosCuotaSocio['ANIOCUOTA']['valorCampo'] = date('Y');// se pone año "ANIOCUOTA" = date('Y'), año actual
														
											 $anioActual = date('Y');
											 //$tipoCuota = $datosCuotaSocio['CODCUOTA']['valorCampo'];//ya está anteriormente
																				
												$importeCuotaAnioEL_anioActual = $resImporteCuotaAnio['datosFormCuotaSocio']['resultadoFilas']['ANIOCUOTA'][$anioActual][$tipoCuota]['IMPORTECUOTAANIOEL']['valorCampo'];
            //echo "<br><br>2-5-2f modeloSocios.php:altaSociosConfirmada:importeCuotaAnioEL_anioActual: ";print_r($importeCuotaAnioEL_anioActual);		

												//las siguientes  2 líneas creo que no son necesarias porque ya están el control en la función 
												if ($datosCuotaSocio['IMPORTECUOTAANIOSOCIO']['valorCampo'] < $importeCuotaAnioEL_anioActual)
												{ $datosCuotaSocio['IMPORTECUOTAANIOSOCIO']['valorCampo'] = $importeCuotaAnioEL_anioActual;
												  //echo "<br><br>2-5-2f-1";
												}
												
									   //if($datosSocioConfirmar['CODCUOTA']=='General'y otros  que abonen)
	           if($importeCuotaAnioEL_anioActual != 0)//correspondiente a ese tipo de cuota, General,...												
											 { $datosCuotaSocio['ESTADOCUOTA']['valorCampo']='PENDIENTE-COBRO';
												  //echo "<br><br>2-5-2f-2";
												}
											 else
											 { $datosCuotaSocio['ESTADOCUOTA']['valorCampo']='EXENTO';
												  //echo "<br><br>2-5-2f-3";
												}																			
												$datosCuotaSocio['ORDENARCOBROBANCO']['valorCampo'] =	$ordenarCobroBanco;// Viene de condiciones previas											
						
												$resInsertarCuotaAnioSocio = insertarCuotaAnioSocio($datosCuotaSocio,$conexionDB['conexionLink']);//inserta una fila para año actual 'Y', se necesita 'CODSOCIO'
						      //echo "<br><br>2-5-2f-4-1 modeloSocios.php:altaSociosConfirmada:resInsertarCuotaAnioSocio: ";print_r($resInsertarCuotaAnioSocio);
											}//if ($resInsertarCuotaAnioSocio['codError']=='00000') 
											
											//--- Fin insertar en año actual despues de insertar en año anterior  ---------
											
			       }//elseif ($datosSocioConfirmar['ANIOCUOTA'] < date('Y'))
										//--- Fin: registro y confirmación alta realizado el socio en dos años distintos (consecutivos) ------- 									
										else
										{ $resInsertarCuotaAnioSocio['codError'] = '80303';
											 $resInsertarCuotaAnioSocio['errorMensaje'] = 'Error en AÑO CUOTA, no se ha podido finalizar transación';
										}
	      	  //---------------------- Fin Insertar cuotaAniosocio ---------------------------						
	
					     if ($resInsertarCuotaAnioSocio['codError'] !== '00000')			
					     {$resInsertar = $resInsertarCuotaAnioSocio;
					     }
										else //$resInsertarCuotaAnioSocio['codError']=='00000')		
										{/*---------------------- Inicio actualizarSocioConfirmar ()----------		
										  AHORA ACASO NO SEA NECESARIO ACTUALIZAR NADA O SOLO FECHACONFIRMACION_ANULACION
										  si se actualiza TODO AQUI, DESPUÉS NO SERA NECEASARIO AL ELIMINARSOCIO,
											 no se elimina porque daría problema con las select de Mostrar Pendiente de Confirmar, 
											 ya que sirve para indicar los que fueron capaces de confirmase por ellos mismos 
											 aquí empieza la diferencia con modeloPresCoord:altaSocioPendienteConfirmadaPorGestor:
												
										 	OJO: esto es diferente de "modeloPresCoord:altaSocioPendienteConfirmadaPorGestor()"	
											-------------------------------------------------------------------*/
											
											$datosSocioConfirmar['socioConfirmar']['FECHACONFIRMACION_ANULACION']['valorCampo'] = date('Y-m-d');
	
											$datosSocioConfirmar['socioConfirmar']['NUMDOCUMENTOMIEMBRO']['valorCampo'] = NULL;
											$datosSocioConfirmar['socioConfirmar']['APE1']['valorCampo'] = NULL;
											$datosSocioConfirmar['socioConfirmar']['APE2']['valorCampo'] = NULL;
											$datosSocioConfirmar['socioConfirmar']['NOM']['valorCampo'] = NULL;
											$datosSocioConfirmar['socioConfirmar']['TELFIJOCASA']['valorCampo'] = NULL;
											$datosSocioConfirmar['socioConfirmar']['TELFIJOTRABAJO']['valorCampo'] = NULL;
											$datosSocioConfirmar['socioConfirmar']['TELMOVIL']['valorCampo'] = NULL;
											$datosSocioConfirmar['socioConfirmar']['EMAIL']['valorCampo'] = NULL;
											$datosSocioConfirmar['socioConfirmar']['DIRECCION']['valorCampo'] = NULL;
											$datosSocioConfirmar['socioConfirmar']['COMENTARIOSOCIO']['valorCampo'] = NULL;	
											$datosSocioConfirmar['socioConfirmar']['OBSERVACIONES']['valorCampo'] = NULL;

											$datosSocioConfirmar['socioConfirmar']['CUENTAIBAN']['valorCampo'] = NULL;
											$datosSocioConfirmar['socioConfirmar']['CUENTANOIBAN']['valorCampo'] = NULL;
								
										 $tablaActualizar = 'SOCIOSCONFIRMAR';																					
			 
	          $resActualizarSocioConfirmar = actualizarSocioConfirmar($tablaActualizar,$codUser,$datosSocioConfirmar['socioConfirmar'],$conexionDB['conexionLink']);								
										 
					      //echo "<br><br>2-5-3-1 modeloSocios.php:altaSociosConfirmada:resInsertarCuotaAnioSocio:resActualizarSocioConfirmar: "; print_r($resActualizarSocioConfirmar);
							 
		         //---------------------- Fin actualizarSocioConfirmar ()-------------
	
											if ($resActualizarSocioConfirmar['codError'] !== '00000')			
					      {$resInsertar = $resActualizarSocioConfirmar;
					      }	
											elseif ($resActualizarSocioConfirmar['numFilas'] <= 0)
											{ $resInsertar['codError'] = '80001';
													$resInsertar['errorMensaje'] = 'No se pudo modificar la tabla SOCIOSCONFIRMAR';
											}													
							    else //$resActualizarSocioConfirmar['codError']=='00000'
						     {//----------------Inicio COMMIT ------------------------------------
												$resFinTrans = commitPDO($conexionDB['conexionLink']);
												
												//echo "<br><br>3-1 modeloSocios.php:altaSociosConfirmada:resFinTrans: ";var_dump($resFinTrans);
												
												if ($resFinTrans['codError'] !== '00000')//sera $resFinTrans['codError'] = '70502';		
												{$resFinTrans['errorMensaje'] = 'Error en el sistema, no se han podido finalizar transación del socio/a. ';
													$resFinTrans['numFilas'] = 0;	
												
													$resInsertar = $resFinTrans;	
													//echo "<br><br>3-2 modeloSocios.php:altaSociosConfirmada:resInsertar: ";print_r($resInsertar);
												}												
										  else
										  {$resInsertar['CODUSER'] = $codUser;
												 $resInsertar['codError'] = '00000';
													
             $arrMensaje['textoComentarios'] = "Estimado/a <strong>".$datosMiembro['NOM']['valorCampo']." ".$datosMiembro['APE1']['valorCampo'].
             "</strong> Has confirmado tu alta como socio/a de Europa Laica.
													<br /><br />Recibirás un mensaje en tu correo electrónico <strong>".$datosMiembro['EMAIL']['valorCampo'].
							      "</strong> con información relacionada con tu alta como socio/a. 
             <br /><br /><br />													
										   Para entrar en la zona privada de socios/as, puedes hacer clic en 
													<a href='./index.php?controlador=controladorLogin&amp;accion=validarLogin'>
													<strong>Entrar </strong></a> y utilizar el 'usuario' y 'contraseña' que elegiste al registrar tus 
													datos de socio/a. 
													<br /><br />
													Si no los recuerdas puedes recuperarlos en 
													<a href='./index.php?controlador=controladorLogin&amp;accion=recordarLogin'>
													<strong>¿Recordar contraseña?</strong></a>
													<br /><br />
											  Cuando hayas entrado desde el menú lateral podrás modificar o eliminar tus datos 
													(según la ley de protección de datos)
													<br /><br /><br />			
													Para ser socio/a de pleno derecho tienes que abonar, si aún no lo has hecho, 
													la cuota anual de la asociación Europa Laica.";
							
													/*En Europa Laica NO aceptamos subvenciones, todas las aportaciones económicas que recibimos 
													provienen de las cuotas y donaciones de nuestras socias, socios y simpatizantes. (Ya incluido en formulario)*/
													
													$arrMensaje['textoBoton'] = 'Entrar en la aplicación';
													$arrMensaje['enlaceBoton'] = './index.php?';
										  }
									   //--------------------Fin COMMIT -----------------------------------
										 }//else $resActualizarSocioConfirmar['codError']=='00000'
										}//else $resInsertarCuotaAnioSocio['codError']=='00000'
									}//else $resImporteCuotaAnio['codError']=='00000'
						  }//else $resInsertarSocio['codError']=='00000'
			    }//else $resInsertarMiembro['codError']=='00000'
		    }//else $resInsertarUsuarioRol['codError']=='00000'				
		   }//else $resInsertarUsuario['codError']=='00000'	
		  }//else $resDatosSocioConfirmar['codError']=='00000'			 
				//echo "<br><br>4-1 modeloSocios.php:altaSociosConfirmada:resInsertar: ";print_r($resInsertar);
				
				//---------------- Inicio tratamiento errores -------------------------------	
		
				//--- Inicio deshacer transación en las tablas modificadas ---------------					
				if ($resInsertar['codError'] !== '00000')
				{						
					$resDeshacerTrans = rollbackPDO($conexionDB['conexionLink']);
					//echo "<br><br>5-1 modeloSocios.php:altaSociosConfirmada:resDeshacerTrans: ";print_r($resDeshacerTrans);
						
					if ($resDeshacerTrans['codError'] !== '00000')//sera $resDeshacerTrans['codError'] = '70503';
					{ $resInsertar['errorMensaje'] .= $resDeshacerTrans['errorMensaje'];
							//echo "<br><br>5-2 modeloSocios.php:altaSociosConfirmada:resInsertar: ";print_r($resInsertar);
					}	
				}//--- Fin deshacer transación en las tablas modificadas -----------------
	 }//else $resIniTrans['codError'] == '00000'
		
		//--- Inicio Grabar en tabla ERRORES, incluido error beginTransationPDO()--
		if ($resInsertar['codError'] !== '00000')
		{												
			if ( isset($resInsertar['textoComentarios']) || (isset($resDeshacerTrans['codError']) && $resDeshacerTrans['codError'] !== '00000') )
			{ $resInsertar['textoComentarios'] = ". modeloSocios.php:altaSociosConfirmada(): ".$resInsertar['textoComentarios'];}	
			else
			{	$resInsertar['textoComentarios'] = ". modeloSocios.php:altaSociosConfirmada(): ";}							
					
			require_once './modelos/modeloErrores.php'; //si es un error de conexión a la tabla error, insertar errores 
			$resInsertarErrores = insertarError($resInsertar,$conexionDB['conexionLink']);
	
			if ($resInsertarErrores['codError'] !== '00000')
			{ $resInsertar['codError'] = $resInsertarErrores['codError'];
					$resInsertar['errorMensaje'] .= $resInsertarErrores['errorMensaje'];
			}			
		}//--- Fin Grabar en tabla ERRORES, incluido error beginTransationPDO()----
		
	 //---------------- Fin tratamiento errores ------------------------------------	

	}//else $conexionDB['codError'] == "00000"
	
	$resAltaSocios = $resInsertar;
	$resAltaSocios['arrMensaje'] = $arrMensaje;	
	
	//echo "<br><br>6 modeloSocios.php:altaSociosConfirmada:resAltaSocios: ";print_r($resAltaSocios);
	//Devuelve Array ( [CODUSER], [arrMensaje] [textoCabecera] => Confirmar alta socio /a, [textoComentarios] => Acabas de confirmar tu alta como socio/a en la base de datos de Europa Laica.

 return 	$resAltaSocios; 	
}
/*------------------------------ Fin altaSociosConfirmada ----------------------*/

/* ------------------------- Inicio anularSocioPendienteConfirmar --------------
Por propio socio, se eliminan los datos personales de un socio cuyo estado
USUARIO.ESTADO=PENDIENTE-CONFIRMAR que pasará a ser 
USUARIO.ESTADO=ANULADA-SOCITUD-REGISTRO, (se ponen a NULL en SOCIOSCONFIRMAR) y 
no se guardan datos en MIEMBROELIMINADO5ANIOS, ya que no ha llegado a ser socio
								
RECIBE: una variable $codUser 
DEVUELVE:  un array con ['CODUSER'] y controles de errores

LLAMADA: desde controladorSocios:anularAltaSocioPendienteConfirmar()
LLAMA: BBDD/MySQL/conexionMySQL.php:conexionDB(),
transationPDO.php:beginTransationPDO,commitPDO y rollbackPDO.
modeloSocios.php:actualizarSocioConfirmar(),modeloUsuarios.php:actualizUsuario()
modeloErrores.php:insertarError()

OBSERVACIONES:Es igual a cPresidente.php:anularAltaSocioPendienteConfirmarPres() 
              excepto el mensaje ver la posibilidad de fusionarlos solo en uno
															
2020-03-25: No necesita modificar para incluir PHP: PDOStatement::bindParam, 
las funciones llamadas aquí lo tratan internamente															
------------------------------------------------------------------------------*/
function anularSocioPendienteConfirmar($codUser)
{
	//echo "<br><br>0 modeloSocios.php:anularSocioPendienteConfirmar:codUser: "; print_r($codUser);

 $resAnularSocioPendiente['nomScript'] = "modeloSocios.php";	
	$resAnularSocioPendiente['nomFuncion'] = "anularSocioPendienteConfirmar";
	$resAnularSocioPendiente['codError'] = '00000';
	$resAnularSocioPendiente['errorMensaje'] = '';
	$arrMensaje['textoCabecera'] = '';

	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	require_once "BBDD/MySQL/conexionMySQL.php";			
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);	

	if ($conexionDB['codError'] !== "00000")
	{ $resAnularSocioPendiente = $conexionDB;
   $resAnularSocioPendiente['textoComentarios'] = ". modeloSocios.php:anularSocioPendienteConfirmar(): ";
	}
 else //$conexionDB['codError']=="00000"
	{
		require_once("BBDD/MySQL/transationPDO.php");
		$resIniTrans = beginTransationPDO($conexionDB['conexionLink']);
		
		//echo "<br><br>1-1 modeloSocios.php:anularSocioPendienteConfirmar:resIniTrans: ";var_dump($resIniTrans);	echo "<br><br>";
		//echo "<br><br>1-2 modeloSocios.php:anularSocioPendienteConfirmar:conexionsDB: ";var_dump($conexionsDB);echo "<br><br>";
		
		if ($resIniTrans['codError'] !== '00000') //será ['codError'] = '70501';	
		{ 
				$resIniTrans['errorMensaje'] = 'Error en el sistema, no se ha podido iniciar la transación. ';
				$resIniTrans['numFilas'] = 0;												
				$resAnularSocioPendiente = $resIniTrans;	
				
				//echo "<br><br>1-3 modeloSocios.php:anularSocioPendienteConfirmar:resIniTrans: ";var_dump($resIniTrans);	echo "<br><br>";		
		}			
		else //$resIniTrans['codError'] == '00000'
		{	
			/* NOTA: se dejan valores de algunos campos no personales para estadísticas*/		
			$arrValoresActualizar['usuario']['ESTADO']['valorCampo']	= 'ANULADA-SOCITUD-REGISTRO';	//no cambiar este texto
			$arrValoresActualizar['usuario']['USUARIO']['valorCampo']= NULL;  
			$arrValoresActualizar['usuario']['OBSERVACIONES']['valorCampo'] = NULL; 
			 	
			$arrValoresActualizar['socioConfirmar']['FECHACONFIRMACION_ANULACION']['valorCampo'] = date('Y-m-d');
			$arrValoresActualizar['socioConfirmar']['NUMDOCUMENTOMIEMBRO']['valorCampo']= NULL;
			$arrValoresActualizar['socioConfirmar']['APE1']['valorCampo'] = NULL;
			$arrValoresActualizar['socioConfirmar']['APE2']['valorCampo'] = NULL;
			$arrValoresActualizar['socioConfirmar']['NOM']['valorCampo'] = NULL;
			$arrValoresActualizar['socioConfirmar']['TELFIJOCASA']['valorCampo'] = NULL;
			$arrValoresActualizar['socioConfirmar']['TELFIJOTRABAJO']['valorCampo'] = NULL;
			$arrValoresActualizar['socioConfirmar']['TELMOVIL']['valorCampo'] = NULL;
			$arrValoresActualizar['socioConfirmar']['EMAIL']['valorCampo'] = NULL;
			$arrValoresActualizar['socioConfirmar']['DIRECCION']['valorCampo'] = NULL;
			$arrValoresActualizar['socioConfirmar']['COMENTARIOSOCIO']['valorCampo'] = NULL;	
			$arrValoresActualizar['socioConfirmar']['OBSERVACIONES']['valorCampo'] = NULL;
			$arrValoresActualizar['socioConfirmar']['CUENTAIBAN']['valorCampo'] = NULL;
			$arrValoresActualizar['socioConfirmar']['CUENTANOIBAN']['valorCampo']= NULL;	
			
			//echo "<br><br>3 modeloSocios:anularSocioPendienteConfirmar:arrValoresActualizar: ";print_r($arrValoresActualizar);
			
			require_once './modelos/modeloUsuarios.php';
   $resActualizarUsuario = actualizUsuario('USUARIO',$codUser,$arrValoresActualizar['usuario'],$conexionDB['conexionLink']);
		
			//echo "<br><br>4 modeloSocios.php:anularSocioPendienteConfirmar:resActualizarUsuario: ";print_r($resActualizarUsuario);
			
			if ($resActualizarUsuario['codError'] !== '00000')			
	  {
				 $resAnularSocioPendiente = $resActualizarUsuario;
	  }
			elseif ($resActualizarUsuario['numFilas'] <= 0)
			{ $resAnularSocioPendiente['codError'] = '80001';
			  $resAnularSocioPendiente['errorMensaje'] = 'No se pudo modificar la tabla USUARIO';
			}	
	  else //resActualizarUsuario['codError']=='00000'
	  {	   
    $resActualizarSociosConfirmar = actualizarSocioConfirmar('SOCIOSCONFIRMAR',$codUser,$arrValoresActualizar['socioConfirmar'],$conexionDB['conexionLink']);//en modeloSocios.php 				
		  //echo "<br><br>5 modeloSocios.php:anularSocioPendienteConfirmar:resActualizarSociosConfirmar: ";print_r($resActualizarSociosConfirmar); 
				
				if ($resActualizarSociosConfirmar['codError'] !== '00000')			
		  {$resAnularSocioPendiente = $resActualizarSociosConfirmar;
		  }
				elseif ($resActualizarSociosConfirmar['numFilas'] <= 0)
				{ $resAnularSocioPendiente['codError'] = '80001';
						$resAnularSocioPendiente['errorMensaje'] = 'No se pudo modificar la tabla SOCIOSCONFIRMAR';
				}	
	   else //$resActualizarSociosConfirmar['codError'] == 00000'
				{//----------------Inicio COMMIT -------------------------------------------
						$resFinTrans = commitPDO($conexionDB['conexionLink']);
						
						//echo "<br><br>6-1 modeloSocios.php:anularSocioPendienteConfirmar:resFinTrans: ";var_dump($resFinTrans);
						
						if ($resFinTrans['codError'] !== '00000')//será $resFinTrans['codError'] = '70502';				
						{ $resFinTrans['errorMensaje'] = 'Error en el sistema, no se han podido finalizar transación del socio/a. ';
						 	$resFinTrans['numFilas'] = 0;	
							 					
							 $resAnularSocioPendiente = $resFinTrans;	
							 //echo "<br><br>6-2 modeloSocios.php:anularSocioPendienteConfirmar:resAnularSocioPendiente: ";print_r($resAnularSocioPendiente);
						}
      else
	     {	$resAnularSocioPendiente['codError'] = '00000';
								$resAnularSocioPendiente['CODUSER'] = $codUser; 
								$arrMensaje['textoComentarios'] = "Atendiendo a tu petición, hemos anulado tu solicitud alta de como socio/a de Europa Laica 
								y se han borrado todos tus datos personales de nuestra base de datos. 
								<br /><br />Muchas gracias por tu interés en la asociación Europa Laica.
								<br /><br /> 
								Si en algún momento, decides hacerte socio/a de Europa Laica de nuevo, tendrás que volver a registrate de nuevo como nuevo/a socio/a";	
						}					
			 }//else $resActualizarSociosConfirmar['codError'] == 00000'
				//---------------- Fin COMMIT ----------------------------------------------
			}//else resActualizarUsuario['codError']=='00000'
			
			//echo "<br><br>7-1 modeloSocios.php:anularSocioPendienteConfirmar:resAnularSocioPendiente: ";print_r($resAnularSocioPendiente);
			
			//---------------- Inicio tratamiento errores -------------------------------	
   
			//--- Inicio deshacer transación en las tablas modificadas ---------------
	  if ($resAnularSocioPendiente['codError'] !== '00000')
	  { 
					$resDeshacerTrans = rollbackPDO($conexionDB['conexionLink']);
					//echo "<br><br>7-2 modeloSocios.php:anularSocioPendienteConfirmar:deshacerTrans: ";print_r($resDeshacerTrans);
						
					if ($resDeshacerTrans['codError'] !== '00000') //será $resDeshacerTrans['codError'] = '70503';
					{ $resAnularSocioPendiente['errorMensaje'] .= $resDeshacerTrans['errorMensaje'];
							//echo "<br><br>7-3 modeloSocios.php:anularSocioPendienteConfirmar:resAnularSocioPendiente: ";print_r($resAnularSocioPendiente);
					}
			}//--- Fin deshacer transación en las tablas modificadas -----------------
		}//else $resIniTrans['codError'] == '00000'	
		
	 //--- Inicio Grabar en tabla ERRORES, incluido error beginTransationPDO()--
		if ($resAnularSocioPendiente['codError'] !== '00000' || (isset($resDeshacerTrans['codError']) && $resDeshacerTrans['codError'] !== '00000') )
		{		
				if ( isset($resAnularSocioPendiente['textoComentarios']) ) 
				{ $resAnularSocioPendiente['textoComentarios'] = ". modeloSocios.php:anularSocioPendienteConfirmar(): ".$resAnularSocioPendiente['textoComentarios'];}	
				else
				{	$resAnularSocioPendiente['textoComentarios'] = ". modeloSocios.php:anularSocioPendienteConfirmar(): ";}	
			
				require_once './modelos/modeloErrores.php'; //si es un error de conexión a la tabla error, insertar errores 
				$resInsertarErrores = insertarError($resAnularSocioPendiente);//no necesita pasar $arrBind
		
				if ($resInsertarErrores['codError'] !== '00000')
				{$resAnularSocioPendiente['codError'] = $resInsertarErrores['codError'];
					$resAnularSocioPendiente['errorMensaje'] .= $resInsertarErrores['errorMensaje'];								
				}
	 }//--- Fin Grabar en tabla ERRORES, incluido error beginTransationPDO()----
		
	//---------------- Fin tratamiento errores ------------------------------------	

 }//else $conexionDB['codError']=="00000"
	
	$anularSocioPendienteConfirmar = $resAnularSocioPendiente;
	$anularSocioPendienteConfirmar['arrMensaje'] = $arrMensaje;	
	
	//echo "<br><br>8 modeloSocios.php:anularSocioPendienteConfirmar:anularSocioPendienteConfirmar: ";print_r($anularSocioPendienteConfirmar);
	
 return 	$anularSocioPendienteConfirmar; 	
}
/*------------------------------ Fin anularSocioPendienteConfirmar -------------*/

/*----------------- Inicio mConfirmarEmailPassAltaGestor -----------------------
En esta función a partir del email que recibe un socio, cuando un gestor le ha 
dado de alta, en la tabla USUARIO, cambia el ESTADO = 'alta-sin-password-gestor' 
a ESTADO = 'alta' y  introduce y guarda su nueva su contraseña.
En la tabla CONFIRMAREMAILALTAGESTOR, el campo FECHARESPUESTAEMAIL 
con la fecha del día de respuesta al email recibido.
							
RECIBE: un $codUserDesEncriptado, $passNoEncriptada
DEVUELVE: un array con ['CODUSER'] y controles de errores

LLAMADA: desde controladorSocios.php:confirmarEmailPassAltaSocioPorGestor(),
LLAMA: require "../../usuariosConfig/BBDD/MySQL/configMySQL.php"
						 modelos/conexionMySQL.php:conexionDB()
							BBDD/MySQL/transationPDO.php:beginTransationPDO(),commitPDO(),rollbackPDO()
							modeloUsuarios.php:actualizUsuario()							
							modeloPresCoord.php:actualizarConfirmarEmailAltaGestor()
							modeloErrores.php:insertarError()							
							Para encriptar contraseñas:sha1(),
							
OBSERVACIONES:
sha1() para encriptar	es antigua hay cambiar a función más segura password_hash()
														
2020-04-2: modifico para incluir transationPDO.php", no necesita otras 
modificaciones de PHP: PDOStatement::bindParam, están dentro de las funciones																
------------------------------------------------------------------------------*/
function mConfirmarEmailPassAltaGestor($codUserDesEncriptado,$passNoEncriptada)
{	
		//echo "<br><br>0-1 modeloSocios.php:mConfirmarEmailPassAltaGestor:codUserDesEncriptado: ";print_r($codUserDesEncriptado);
		//echo "<br><br>0-2 modeloSocios:mConfirmarEmailPassAltaGestor:passNoEncriptada: ";print_r($passNoEncriptada);
		
		$resConfirmarEmailPass['nomScript'] = 'modeloSocios.php';
		$resConfirmarEmailPass['nomFuncion'] = 'mConfirmarEmailPassAltaGestor';			
		$resConfirmarEmailPass['codError'] = '00000'; 	
		$resConfirmarEmailPass['errorMensaje'] = ''; 	
  $arrMensaje = array();

		require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";		
		require_once "BBDD/MySQL/conexionMySQL.php";
		$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);//Ya estará establecida la conexión  

		if ($conexionDB['codError'] !== "00000")
		{ $resConfirmarEmailPass = $conexionsDB;
	   $resConfirmarEmailPass['textoComentarios'] = ". modeloSocios.php:mConfirmarEmailPassAltaGestor(): ";			
		}	
		else //$conexionDB['codError']=="00000"
		{
				require_once("BBDD/MySQL/transationPDO.php");
				$resIniTrans = beginTransationPDO($conexionDB['conexionLink']);
				
				//echo "<br><br>1-1 modeloSocios.php:mConfirmarEmailPassAltaGestor:resIniTrans: ";var_dump($resIniTrans);	echo "<br>";
				//echo "<br><br>1-2 modeloSocios.php:mConfirmarEmailPassAltaGestor:conexionDB: ";var_dump($conexionsDB);echo "<br>";
				
				if ($resIniTrans['codError'] !== '00000') //será ['codError'] = '70501';
				{$resIniTrans['errorMensaje'] .= 'Error en el sistema, no se ha podido iniciar la transación. '.$resConfirmarEmailPass['nomScript'].":".$resConfirmarEmailPass['nomFuncion'];
					$resIniTrans['numFilas'] = 0;								
					$resConfirmarEmailPass = $resIniTrans;					
					//echo "<br><br>1-3 modeloSocios.php:mConfirmarEmailPassAltaGestor:resIniTrans: ";print_r($resIniTrans);echo "<br>";						
				}							
				else //$resIniTrans['codError'] == '00000'
				{//$passNoEncriptada = $resValidarCamposForm['datosFormUsuario']['PASSUSUARIO']['valorCampo'];
				
					$datosActUsuario['PASSUSUARIO']['valorCampo'] = sha1($passNoEncriptada);//función encriptar	antigua hay cambiar a password_hash() u otra
					$datosActUsuario['ESTADO']['valorCampo']	= 'alta';						
					
					$resActUsuario = actualizUsuario('USUARIO',$codUserDesEncriptado,$datosActUsuario,$conexionDB['conexionLink']);//modeloUsuarios.php falta pasar $conexionLinkDB
						
					//echo "<br><br>2 modeloSocios.php:mConfirmarEmailPassAltaGestor:resActUsuario: ";print_r($resActUsuario);	
					
					if ($resActUsuario['codError'] !== "00000")
					{ 
							$resConfirmarEmailPass = $resActUsuario;											 
					}
					elseif ($resActUsuario['numFilas'] <= 0)
					{ $resConfirmarEmailPass['codError'] = '80001';
							$resConfirmarEmailPass['errorMensaje'] = 'No se pudo modificar la tabla USUARIO';
					}						
					else//($resActUsuario['codError'] == '00000')
					{
						$arrayDatosConfimar['FECHARESPUESTAEMAIL']['valorCampo'] = date('Y-m-d');//mysql:(CURRENT_DATE());	
										
						$resEmailConfirmar = actualizarConfirmarEmailAltaGestor('CONFIRMAREMAILALTAGESTOR',$codUserDesEncriptado,$arrayDatosConfimar,$conexionDB['conexionLink']);//modeloPresCoord.php				
		
						//echo "<br><br>3 modeloSocios.php:mConfirmarEmailPassAltaGestor:resEmailConfirmar: "; print_r($resEmailConfirmar);
						
						if ($resEmailConfirmar['codError'] !== '00000')
						{ $resConfirmarEmailPass = $resEmailConfirmar;							
						} 
						else //$resEmailConfirmar['codError']=='00000'
						{ 								
							$resFinTrans = commitPDO($conexionDB['conexionLink']);				
							//echo "<br><br>4-1 modeloSocios.php:mConfirmarEmailPassAltaGestor:resFinTrans: ";var_dump($resFinTrans);
							
							if ($resFinTrans['codError'] !== '00000')//será ['codError'] = '70502';
							{$resFinTrans['errorMensaje'] = 'Error en el sistema al hacer commit, no se pudo hacer commit. '.$resConfirmarEmailPass['nomScript'].":".$resConfirmarEmailPass['nomFuncion'];
								$resFinTrans['numFilas'] = 0;							
								$resConfirmarEmailPass = $resFinTrans;	
							}
							else
							{$resConfirmarEmailPass['datosUsuario'] = $codUserDesEncriptado;//incluye ['CODUSER']=$codUserDesEncriptado, que acaso se necesite para controladorSocios.php

								$arrMensaje['textoComentarios'] = 	"Se ha confirmado correctamente la recepción del email por el socio/a y establecimiento de la contraseña
								<br /><br /><br /><br />Para ver o modificar tus datos haz clic en el siguiente enlace:".			
								"<a href = 'https://www.europalaica.com/usuarios/index.php' 	title ='Entrar en aplicación de gestión de socios/as de Europa Laica'>
																			<strong>https://www.europalaica.com/usuarios</strong>
								</a>  
								<br /><br /><br />Te pedirá tu \"usuario\" y \"contraseña\" que acabas de elegir para poder entrar.						
        <br /><br /> 
									Si no los recuerdas puedes recuperarlos en 
									<a href='./index.php?controlador=controladorLogin&amp;accion=recordarLogin'>
									<strong>¿Recordar contraseña?</strong></a>
									<br /><br />
									Cuando hayas entrado desde el menú lateral podrás modificar o eliminar tus datos 
									(según la ley de protección de datos)
									<br /><br /><br />			
									Para ser socio/a de pleno derecho tienes que abonar, si aún no lo has hecho, 
									la cuota anual de la asociación Europa Laica.";
         
									/*En Europa Laica NO aceptamos subvenciones, todas las aportaciones económicas que recibimos 
									provienen de las cuotas y donaciones de nuestras socias, socios y simpatizantes.*/
																
							}//else $resEmailConfimar['codError']=='00000'
						}//else $resEmailConfirmar['codError']=='00000'
					}//else $resActUsuario['codError'] == '00000'

					//echo "<br><br>5 modeloSocios.php:mConfirmarEmailPassAltaGestor:resConfirmarEmailPass: ";print_r($resConfirmarEmailPass);
					
					//---------------- Inicio tratamiento errores -------------------------------	
					
					//--- Inicio deshacer transación en las tablas modificadas ---------------					
					if ($resConfirmarEmailPass['codError'] !== '00000')
					{		
						$resDeshacerTrans = rollbackPDO($conexionDB['conexionLink']);
						//echo "<br><br>6-1 modeloSocios.php:mConfirmarEmailPassAltaGestor:resDeshacerTrans: ";print_r($resDeshacerTrans);
							
						if ($resDeshacerTrans['codError'] !== '00000')//será ['codError'] = '70503';
						{ 
						  $resConfirmarEmailPass['errorMensaje']  .= $resDeshacerTrans['errorMensaje'];
						}
					}//--- Fin deshacer transación en las tablas modificadas -----------------	
					
			 }//else $resIniTrans['codError'] == '00000'					
			
			 //--- Inicio Grabar en tabla ERRORES, incluido error beginTransationPDO()--
				if ($resConfirmarEmailPass['codError'] !== '00000' || (isset($resDeshacerTrans['codError']) && $resDeshacerTrans['codError'] !== '00000') )
				{		
					if ( isset($resConfirmarEmailPass['textoComentarios']) ) 
					{ $resConfirmarEmailPass['textoComentarios'] = ". modeloSocios.php:mConfirmarEmailPassAltaGestor(): ".$resConfirmarEmailPass['textoComentarios'];}	
					else
					{	$resConfirmarEmailPass['textoComentarios'] = ". modeloSocios.php:mConfirmarEmailPassAltaGestor(): ";}							
					
					require_once './modelos/modeloErrores.php'; //si es un error de conexión a la tabla error, insertar errores 
					$resInsertarErrores = insertarError($resConfirmarEmailPass,$conexionDB['conexionLink']);//no necesita pasar $arrBind
			
					if ($resInsertarErrores['codError'] !== '00000')
					{$resConfirmarEmailPass['codError'] = $resInsertarErrores['codError'];
						$resConfirmarEmailPass['errorMensaje'] .= $resInsertarErrores['errorMensaje'];						
					}				
			 }//--- Fin Grabar en tabla ERRORES, incluido error beginTransationPDO()----
		
	   //---------------- Fin tratamiento errores ------------------------------------	

		}//else $conexionDB['codError'] == "00000"	
				
		$resConfirmarEmailPass['arrMensaje'] = $arrMensaje;
		
		//echo "<br><br>7 modeloSocios.php:mConfirmarEmailPassAltaGestor:resConfirmarEmailPass: ";print_r($resConfirmarEmailPass);
		
  return	$resConfirmarEmailPass;
}			
/*----------------- Fin mConfirmarEmailPassAltaGestor --------------------------*/

/*------------------------------ Inicio eliminarDatosSocios --------------------
En esta función se borran los datos de los socios, en realidad, en la mayor parte
se ponen los campos con datos personales a NULL, se dejan los que no son datos 
personales que podrían servir para futuras estadísticas.
En algunas tablas se eliminar la filas correspondiente.
Además se inserta un fila en la tabla MIEMBROELIMINADO5ANIOS, para efectos 
fiscales si fuesen necesarios, y al 5 año se borran automáticamente al cierre de año

En caso de que hubiese un archivo con la firma de un socio debido a lata por gestor, también
se eliminaría el archivo del servidor.
Con commit y rollback. Se graban los errores del sistema en ERRORES
								
RECIBE: $datosUsuarioEliminar array con los campos hide de los form de eliminar socios
DEVUELVE: un array con los controles de errores, y mensajes 

LLAMADA: controladorSocios.php:eliminarSocio(), cPresidente:eliminarSocioPres(), 
         cCoordinador.php:eliminarSocioCoord(),cTesorero.php:eliminarSocioTes()
LLAMA: usuariosConfig/BBDD/MySQL/configMySQL.php
BBDD/MySQL/conexionMySQL.php:conexionDB(),
transationPDO.php:beginTransationPDO,commitPDO y rollbackPDO.
modeloUsuarios.php:buscarUnRolUsuario(),eliminarUsuarioTieneRol(),
actUsuarioEliminar(),insertarMiembroEliminado5Anios()
modeloSocios.php:actSocioEliminar(),buscarDatosSocio(),actualizCuotaAnioSocio()
modeloPresCoord.php:eliminarCoordinacionAreaGestion()
modeloArchivos.php:eliminarArchivo()
modeloErrores.php:insertarError()

OBSERVACIONES: probado PHP 7.3.21
2020-03-25: añado transationPDO.php. necesita modificar para incluir 
PHP: PDOStatement::bindParam, aunque la mayoria de funciones llamadas aquí lo tratan internamente				
------------------------------------------------------------------------------*/
function eliminarDatosSocios($datosUsuarioEliminar)
{
	//echo "<br><br>0-1 modeloSocios.php:eliminarDatosSocios:datosUsuarioEliminar: ";print_r($datosUsuarioEliminar);
	
	require_once './modelos/modeloUsuarios.php';
	require_once './modelos/modeloPresCoord.php';
	
	$resEliminarSocio['nomScript'] = "modeloSocios.php";	
	$resEliminarSocio['nomFuncion'] = "eliminarDatosSocios";	
 $resEliminarSocio['codError'] = '00000';
 $resEliminarSocio['errorMensaje'] = '';	
	
	$arrMensaje = array();
	
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	require_once "BBDD/MySQL/conexionMySQL.php";			
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);	
	//echo "<br><br>1-1 modeloSocios.php:eliminarDatosSocios:conexionsDB: ";var_dump($conexionDB);echo "<br><br>";

 if ($conexionDB['codError'] !== "00000")
	{ $resEliminarSocio = $conexionDB;	  
   $resEliminarSocio['textoComentarios'] = ". modeloSocios.php:eliminarDatosSocios(): ";	
	}	
 else //$conexionDB['codError']=="00000"
	{	
	 /*--- Inicio de punto de rollback para transación --------------------------*/
		require_once("BBDD/MySQL/transationPDO.php");
		$resIniTrans = beginTransationPDO($conexionDB['conexionLink']);	
		
		//echo "<br><br>1-2 modeloSocios.php:eliminarDatosSocios:resIniTrans: ";var_dump($resIniTrans);	echo "<br><br>";		
		
		if ($resIniTrans['codError'] !== '00000') //será ['codError'] = '70501';	
		{ $resIniTrans['errorMensaje'] = 'Error en el sistema, no se ha podido iniciar la transación. ';
				$resIniTrans['numFilas'] = 0;	
				$resEliminarSocio = $resIniTrans;			
		}			
		else //$resIniTrans['codError'] == '00000'
		{		
			$usuarioBuscado = $datosUsuarioEliminar['datosFormUsuario']['CODUSER'];
	  //echo "<br><br>2-0 modeloSocios.php:eliminarDatosSocios:usuarioBuscado: ";print_r($usuarioBuscado);
			/*---------------- Inicio buscar si es si es coordinador --------------------
			 Buscar si es si es coordinadorSi es coordinador y si lo es hay que borrale 
				también de tabla "COORDINAAREAGESTIONAGRUP"
   	--------------------------------------------------------------------------*/			
			$codRolBuscar = 6;	//6 es el rol de coordinador, 

	  $rolCoordinadorExiste = buscarUnRolUsuario($usuarioBuscado, $codRolBuscar);//en modeloUsuarios.php, para Si tiene rol de coordinador borrarlo de tabla COORDINAAREAGESTIONAGRU
			
			//echo "<br><br>2-1 modeloSocios.php:eliminarDatosSocios:rolCoordinadorExiste: ";print_r($rolCoordinadorExiste);
	
			if ($rolCoordinadorExiste['codError'] !== '00000')//probado error		
   { $resEliminarSocio = $rolCoordinadorExiste; 
   }
		 else // $rolCoordinadorExiste['codError'] == '00000'
			{$resEliminarCoordAreaGestion['codError'] = '00000';//Necesario por si no es coordinador y no entra en el siguiente if
			
			 if ($rolCoordinadorExiste['numFilas'] > 0)//es coordinador de área territorial => hay que borrar Area asignada
			 {
			  $datosCoordEliminar['CODUSER'] = $usuarioBuscado;
	    $datosCoordEliminar['CODAREAGESTIONAGRUP'] = '%'; //todos;

				 $resEliminarCoordAreaGestion = eliminarCoordinacionAreaGestion('COORDINAAREAGESTIONAGRUP',$datosCoordEliminar,$conexionDB['conexionLink']);//modeloPresCoord.php, borrar de tabla COORDINAAREAGESTIONAGRU 					

     //echo "<br><br>2-2 modeloSocios.php:eliminarDatosSocios:resEliminarCoordAreaGestion: ";print_r($resEliminarCoordAreaGestion);

					if ($resEliminarCoordAreaGestion['codError'] !== '00000')//probado error		
					{$resEliminarSocio = $resEliminarCoordAreaGestion;
					}
					elseif ($resEliminarCoordAreaGestion['numFilas'] <= 0)//probado error		
					{ $resEliminarSocio['codError'] = '80001';
							$resEliminarSocio['errorMensaje'] = 'No se pudo eliminar el coordinador de de la tabla COORDINAAREAGESTIONAGRUP';
							$resEliminarCoordAreaGestion = $resEliminarSocio;
					}
    }/*----------------- Fin buscar si es si es coordinador --------------------*/
			 
				if ($resEliminarCoordAreaGestion['codError'] == '00000')
			 {
					$codRol  = '%';//ELIMINAR todos los roles del usuario
	
					$reRolEliminar = eliminarUsuarioTieneRol('USUARIOTIENEROL',$usuarioBuscado,$codRol,$conexionDB['conexionLink']);//en modeloUsuarios.php
     //echo "<br><br>2-3 modeloSocios.php:eliminarDatosSocios:reRolEliminar: ";print_r($reRolEliminar);
					
			  if ($reRolEliminar['codError'] !== '00000')			
		   {$resEliminarSocio = $reRolEliminar;
	    }	
					elseif ($reRolEliminar['numFilas'] <= 0)
					{ $resEliminarSocio['codError'] = '80001';
							$resEliminarSocio['errorMensaje'] = 'No se pudo eliminar el rol de tabla USUARIOTIENEROL';
					}			
				 else //$reRolEliminar['codError']=='00000')//el usuario hay que ponerlo a baja
				 {$reActUsuarioEliminar = actUsuarioEliminar('USUARIO',$usuarioBuscado,$conexionDB['conexionLink']);//en modeloUsuarios.php       
			    		
				  //echo "<br><br>3-1 modeloSocios.php:eliminarDatosSocios:reActUsuarioEliminar: ";print_r($reActUsuarioEliminar);
					
				  if ($reActUsuarioEliminar['codError'] !== '00000')			
			   { $resEliminarSocio = $reActUsuarioEliminar;
	     }	
						elseif ($reActUsuarioEliminar['numFilas'] <= 0)
						{ $resEliminarSocio['codError'] = '80001';
								$resEliminarSocio['errorMensaje'] = 'No se pudo modificar la tabla USUARIO';
						}										
					 else //$reActUsuarioEliminar['codError']=='00000')
					 {
							$reActSocioEliminar = actSocioEliminar('SOCIO',$usuarioBuscado,$conexionDB['conexionLink']);//en modeloSocios.php, borrar datos bancarios 
							
			    //echo "<br><br>3-2 modeloSocios.php:eliminarDatosSocios:reActSocioEliminar: "; print_r($reActSocioEliminar);
				
					  if ($reActSocioEliminar['codError'] !== '00000')			
			    { $resEliminarSocio = $reActSocioEliminar;
				   }	
							elseif ($reActSocioEliminar['numFilas'] <= 0)
							{ $resEliminarSocio['codError'] = '80001';
									$resEliminarSocio['errorMensaje'] = 'No se pudo modificar la tabla SOCIO';
							}									
						 else //$reActSocioEliminar['codError']=='00000')
						 {
								/*---- Inicio actuaciones en tabla 'CUOTAANIOSOCIO' 'ORDENARCOBROBANCO']=N0 y borrar fila año siguiente ----*/
        $anioCuota = date("Y");

        $resDatosSocios = buscarDatosSocio($usuarioBuscado,$anioCuota);//en modeloSocio.php,	busca todos los datos incluido CUOTAANIOSOCIO, probado error, numFilas=0 es codError=80001
        
        //echo "<br><br>3-3 modeloSocios.php:eliminarDatosSocios:resDatosSocios: "; print_r($resDatosSocios);								
							
								if ($resDatosSocios['codError'] !== '00000')			
								{ $resEliminarSocio = $resDatosSocios;
				    }				
						  else //$resDatosSocios['codError']=='00000')
						  { $codSocio = $resDatosSocios['valoresCampos']['datosFormSocio']['CODSOCIO']['valorCampo'];						  	
										$campoCondiciones = $codSocio;		
										$arrayDatosAct['ORDENARCOBROBANCO']['valorCampo'] = 'NO';//para evitar que aparazca SI en listados si la cuota estuviese domiciliada, aunque aquí se borra el IBAN 
          //$arrayDatosAct['OBSERVACIONES']['valorCampo'] = NULL;//se refieren al pago de la cuota, raramente podría contener datos personales 										
										$arrayDatosAct['ANIOCUOTA']['valorCampo'] = $anioCuota;
	
										$resActualizCuotaAnioSocio = actualizCuotaAnioSocio('CUOTAANIOSOCIO',$campoCondiciones,$arrayDatosAct,$conexionDB['conexionLink']);//modeloSocios.php, probado error, numFilas=0, no es error
										
          //echo "<br><br>3-4 modeloSocios.php:eliminarDatosSocios:resActualizCuotaAnioSocio: "; print_r($resActualizCuotaAnioSocio);
								
          if ($resActualizCuotaAnioSocio['codError'] !== '00000')			
										{ $resEliminarSocio = $resActualizCuotaAnioSocio;
										}				
										else //$resActualizCuotaAnioSocio['codError']=='00000')//borra la fila del año siguiente de la tabla CUOTAANIOSOCIO, si NO existe devuelve numFilas=0, pero no es error 
										{ 
											$cadenaCondiciones = "CODSOCIO = :codSocio AND ANIOCUOTA = :anioCuotaSiguiente"; 											
											$anioCuotaSiguiente = $anioCuota + 1;
											$arrBind = array(':codSocio' => $codSocio, ':anioCuotaSiguiente' => $anioCuotaSiguiente);	
											
											$resCuotaSocioAnioSiguiente = borrarFilas('CUOTAANIOSOCIO',$cadenaCondiciones,$conexionDB['conexionLink'],$arrBind);//en modeloMySQL.php,  probado error, numFilas=0, no es error
											
           //echo "<br><br>3-5 modeloSocios.php:eliminarDatosSocios:resCuotaSocioAnioSiguiente: "; print_r($resCuotaSocioAnioSiguiente);
		         /*---- Fin actuaciones en tabla 'CUOTAANIOSOCIO' 'ORDENARCOBROBANCO']=N0 y borrar año siguiente ----*/
											
											if ($resCuotaSocioAnioSiguiente['codError'] !== '00000')			
											{ $resEliminarSocio = $resCuotaSocioAnioSiguiente;
											}		
           else//$resCuotaSocioAnioSiguiente['codError'] == '00000'
											{	
												$arrInsEliminado5Anios['TIPOMIEMBRO'] = 'socio';			
												$arrInsEliminado5Anios['CODUSER'] = $usuarioBuscado;
												$arrInsEliminado5Anios['CODPAISDOC'] = $datosUsuarioEliminar['datosFormMiembro']['CODPAISDOC'];
												$arrInsEliminado5Anios['TIPODOCUMENTOMIEMBRO'] = $datosUsuarioEliminar['datosFormMiembro']['TIPODOCUMENTOMIEMBRO'];
												$arrInsEliminado5Anios['NUMDOCUMENTOMIEMBRO'] = $datosUsuarioEliminar['datosFormMiembro']['NUMDOCUMENTOMIEMBRO'];						
												$arrInsEliminado5Anios['NOM'] = $datosUsuarioEliminar['datosFormMiembro']['NOM'];
												$arrInsEliminado5Anios['APE1'] = $datosUsuarioEliminar['datosFormMiembro']['APE1'];
												$arrInsEliminado5Anios['APE2'] = $datosUsuarioEliminar['datosFormMiembro']['APE2'];	
												
												if (isset($datosUsuarioEliminar['datosFormSocio']['OBSERVACIONES']) && !empty($datosUsuarioEliminar['datosFormSocio']['OBSERVACIONES']))
												{$arrInsEliminado5Anios['OBSERVACIONES'] = $datosUsuarioEliminar['datosFormSocio']['OBSERVACIONES'].". email: ".$datosUsuarioEliminar['datosFormMiembro']['EMAIL'];
												}
												else
												{	$arrInsEliminado5Anios['OBSERVACIONES'] = ". email: ".$datosUsuarioEliminar['datosFormMiembro']['EMAIL'];
												}												
												//echo "<br><br>4 modeloSocios.php:eliminarDatosSocios:arrInsEliminado5Anios: ";print_r($arrInsEliminado5Anios);
												
												$resInsertarEliminado5Anios = insertarMiembroEliminado5Anios($arrInsEliminado5Anios);//en modeloUsuarios.php, para temas contables	se mantendrán 5 años
												
												//echo "<br><br>5 modeloSocios.php:eliminarDatosSocios:resInsertarEliminado5Anios: ";print_r($resInsertarEliminado5Anios);
												
												if ($resInsertarEliminado5Anios['codError'] !== '00000')			
												{$resEliminarSocio = $resInsertarEliminado5Anios;
												}
												else //$resInsertarEliminado5Anios['codError']=='00000'
												{						   
													$reActMiembroEliminar = actMiembroEliminar('MIEMBRO',$usuarioBuscado,$conexionDB['conexionLink']);//modeloUsuarios.php, borrar datos personales 
													
             //echo "<br><br>6-2 modeloSocios.php:eliminarDatosSocios: reActMiembroEliminar: ";print_r($reActMiembroEliminar);
			
													if ($reActMiembroEliminar['codError'] !== '00000')			
													{$resEliminarSocio = $reActMiembroEliminar;
													}
													elseif ($reActMiembroEliminar['numFilas'] <= 0)
													{ $resEliminarSocio['codError'] = '80001';
															$resEliminarSocio['errorMensaje'] = 'No se pudo modificar la tabla MIEMBRO';
													}											
													else //$reActMiembroEliminar['codError']=='00000')
													{ 
														/*--------- Inicio eliminar archivo firma socio del servidor ---------
														A partir de 2018-10-20, si un gestor da de alta a un socio, el gestor 
														que da el alta tendrá que subir el formulario de alta en archivo pdf, 
														jpg, con la firma del socio.	Con la baja, se elimina el archivo.
														
														NOTA: En caso de que se produzca un error solo en el proceso de 
														eliminar Archivo	no efectuará rollback, ya que pudiera suceder que por 
														error alguien hubiese alterado el path del directorio o el nombre del 
														archivo (pero en "actMiembroEliminar()" se borrarán los datos 
														personales como nombre del archivo de esa persona en la tabla MIEMBRO
														como garantía de protección de datos)
														--------------------------------------------------------------------*/
														$cadEliminarArchivo = '';
																								
														if (isset($datosUsuarioEliminar['datosFormMiembro']['ARCHIVOFIRMAPD']) && !empty($datosUsuarioEliminar['datosFormMiembro']['ARCHIVOFIRMAPD']))																										    						
														{ 
																//$datosUsuarioEliminar['datosFormMiembro']['ARCHIVOFIRMAPD'] ='hhhh.jpg'; //Para pruebas error
																//$datosUsuarioEliminar['datosFormMiembro']['PATH_ARCHIVO_FIRMAS'] ='fdksdjjd.klm';
																
																require_once './modelos/modeloArchivos.php';	//la siguiente función no necesita acceder a la BBDD 
																$resEliminarArchivo = eliminarArchivo($datosUsuarioEliminar['datosFormMiembro']['PATH_ARCHIVO_FIRMAS'],$datosUsuarioEliminar['datosFormMiembro']['ARCHIVOFIRMAPD']);

																//echo "<br><br>7-1 modeloSocios.php:eliminarDatosSocios: resEliminarArchivo: ";print_r($resEliminarArchivo);
																
																if ($resEliminarArchivo['codError'] !== '00000') //dejo las dos opciones por si quiero anteponer algún otro comentario
																{ 
																		$cadEliminarArchivo = "<br /><br />".$resEliminarArchivo['arrMensaje']['textoComentarios'];			
																		//echo "<br><br>7-2 modeloSocios.php:eliminarDatosSocios: $cadEliminarArchivo";									
																}
																else //if ($resEliminarArchivo['codError'] =='00000') 
																{ 
																		$cadEliminarArchivo = "<br /><br />".$resEliminarArchivo['arrMensaje']['textoComentarios'];			
																		//echo "<br><br>7-3 modeloSocios.php:eliminarDatosSocios: $cadEliminarArchivo";									
																} 																	
														}//de if(isset($datosUsuarioEliminar['datosFormMiembro']['ARCHIVOFIRMAPD']) && !empty($datosUsuarioEliminar['datosFormMiembro']['ARCHIVOFIRMAPD']))								        
														
														//----------- Fin eliminar archivo firma socio del servidor ----------											
											
														//----------------Inicio COMMIT --------------------------------------
														$resFinTrans = commitPDO($conexionDB['conexionLink']);
														
													 //	echo "<br><br>8-1 modeloSocios.php:eliminarDatosSocios:resFinTrans: ";var_dump($resFinTrans);						
														
														if ($resFinTrans['codError'] !== '00000')//sera $resFinTrans['codError'] = '70502'	
														{$resFinTrans['errorMensaje'] = 'Error en el sistema, no se ha podido finalizar transación de eliminar socio/a. ';
															$resFinTrans['numFilas'] = 0;																							
															$resEliminarSocio = $resFinTrans;
														}
														//----------------Fin COMMIT -----------------------------------------
														else // $resFinTrans['codError'] == '00000'
														{
													 	//	echo "<br><br>8-2 modeloSocios.php:eliminarDatosSocios:_SESSION['vs_autentificadoGestor']: ";print_r($_SESSION['vs_autentificadoGestor']);
												
															//------------------ Inicio comentarios  eliminarDatosSocios --------
															$arrMensaje['textoComentarios'] = "Se han eliminado los datos personales de <b> ".
															$datosUsuarioEliminar['datosFormMiembro']['NOM']." ".$datosUsuarioEliminar['datosFormMiembro']['APE1'].
															" </b> en la base de datos de	Europa Laica";
															
															if ($datosUsuarioEliminar['datosFormMiembro']['EMAILERROR'] == 'NO') //cuando no tiene email da un notice
															{	$cadEmail =	"<br /><br /><br />Se ha enviado un correo a la dirección <b>".$datosUsuarioEliminar['datosFormMiembro']['EMAIL'].
																	" </b>para comunicar la baja";
															}
															else
															{ $cadEmail =	"<br /><br /><br />No se ha podido enviar un correo electrónico al socio/a para comunicar la baja, porque no tiene dirección de correo o es errónea";															
															}    														
															
															if (isset($_SESSION['vs_autentificadoGestor']) && $_SESSION['vs_autentificadoGestor'] == 'SI')//la baja la realiza un gestor
															{	$cadGracias =	"<br /><br />".$cadEliminarArchivo."<br /><br /><br />Si más adelante quiere hacerse socio/a de Europa Laica de nuevo, 
																														tendrá que registrase otra vez desde las web de Europa Laica";
															}											
															else // la baja la realiza el socio
															{ $cadGracias =	"<br /><br /><br /><br />Gracias por haber sido socio/a de Europa Laica durante un tiempo
																	<br /><br />Si más adelante quieres hacerte socio/a de Europa Laica de nuevo, 
																												podrás registrate desde las web de Europa Laica";															
															}
															//en controlador $cadEmail .= "<br /><br /><br /><br />Se ha enviado un email a Presidencia, Secretaría, Tesorería y Coordinación de la agrupación para informar de esta baja";
															
															$arrMensaje['textoComentarios'] .= $cadEmail.$cadGracias;	
															//------------------ Fin comentarios  eliminarDatosSocios -----------
															
														}//else $resFinTrans['codError'] == '00000'
													}//else $reActMiembroEliminar['codError']=='00000'
												}//else $resInsertarEliminado5Anios['codError']=='00000'	
											}//else $resCuotaSocioAnioSiguiente['codError']=='00000')
						   	}//else $resActualizCuotaAnioSocio['codError']=='00000')
							 }//else $resDatosSocios['codError']=='00000')
					  }//else $reActSocioEliminar['codError']=='00000'
			   }//else $reActUsuarioEliminar['codError']=='00000'
					}//else $reRolEliminar['codError']=='00000'
				}//else reEliminarCoordAreaGestion['codError']=='00000'
			}//else $rolCoordinadorExiste['codError'] == '00000' 																															
	 
			//echo "<br><br>9-1 modeloSocios.php:eliminarDatosSocios:resEliminarSocio: ";print_r($resEliminarSocio);
   
			//---------------- Inicio tratamiento errores -------------------------------	
   
			//--- Inicio deshacer transación en las tablas modificadas ---------------
			if ($resEliminarSocio['codError'] !== '00000')// Para deshacer transación de las tablas modificadas
			{ 		
					$resDeshacerTrans = rollbackPDO($conexionDB['conexionLink']);
					//echo "<br><br>9-2 modeloSocios.php:eliminarDatosSocios:resDeshacerTrans: ";print_r($resDeshacerTrans);
						
					if ($resDeshacerTrans['codError'] !== '00000')//será $resDeshacerTrans['codError'] = '70503';
					{ 		
							$resEliminarSocio['errorMensaje'] .= $resDeshacerTrans['errorMensaje'];		
					}
			}//--- Fin deshacer transación en las tablas modificadas -----------------
			
		}//else $resIniTrans['codError'] == '00000'	
		
	 //--- Inicio Grabar en tabla ERRORES, incluido error beginTransationPDO()--
		if ($resEliminarSocio['codError'] !== '00000' || (isset($resDeshacerTrans['codError']) && $resDeshacerTrans['codError'] !== '00000') )
		{		if ( isset($resEliminarSocio['textoComentarios']) ) 
					{ $resEliminarSocio['textoComentarios'] = ". modeloSocios.php:eliminarDatosSocios(): ".$resEliminarSocio['textoComentarios'];}	
					else
					{	$resEliminarSocio['textoComentarios'] = ". modeloSocios.php:eliminarDatosSocios(): ";}	
					
					require_once './modelos/modeloErrores.php';//si es un error de conexión a la tabla error, insertar errores 
					$resInsertarErrores = insertarError($resEliminarSocio);	
						
					if ($resInsertarErrores['codError'] !== '00000')
					{ $resEliminarSocio['codError'] = $resInsertarErrores['codError'];
							$resEliminarSocio['errorMensaje'] .= $resInsertarErrores['errorMensaje'];
					}				
		}//--- Fin Grabar en tabla ERRORES, incluido error beginTransationPDO()----
		
	 //---------------- Fin tratamiento errores ------------------------------------	
	
	}//else $conexionDB['codError']=="00000"

 $resEliminarSocio['arrMensaje'] = $arrMensaje;	
 
 //echo "<br><br>10 modeloSocios.php:eliminarSocios:resEliminarSocio: ";print_r($resEliminarSocio);	
	
 return 	$resEliminarSocio; 	
}
/*------------------------------ Fin eliminarDatosSocios -----------------------*/

/*------------------- Inicio actualizarDatosSocio --------------------------------
En esta función se actualizan los datos de los socios, por los socios.
Se actualizan las tablas: USUARIOS, MIEMBRO, SOCIO, CUOTAANIOSOCIO
Recibe datos validados de los formularios para la actualización.								 
También se controlan, ESTADOCUOTA, y según cuentas IBAN y NOIBAN si está 
domiciliado y orden de cobro a banco.
Controlando transacciones.
								
RECIBE: $resValidarCamposForm con datos del socio procedentes del formulario	
correpondiente
DEVUELVE: un array con los controles de errores, y mensajes 

LLAMADA: controladorSocios:actualizarSocio(),cPresidente.ph:actualizarSocioPres(),
         cCoordinador.php:actualizarSocioCoord(), cTesorero.php:actualizarDatosCuotaSocioTes()									
LLAMA: conexionMySQL.php:conexionDB() que incluye PDO y instance()
       transationPDO.php :beginTransationPDO(),commitPDO(),
							rollbackPDO()

							y a varias funciones dentro de "modeloSocios.php":buscarDatosSocio,
       actualizSocio(),actualizarSocioCoord(),actualizCuotaAnioSocio(),insertarCuotaAnioSocio()									
       modeloUsuarios.php:actualizUsuario(),actualizMiembro() 
							
OBSERVACIONES: 
2020-03-26: añado transationPDO.php. No necesita modificar para incluir 
PHP: PDOStatement::bindParam, las funciones llamadas aquí lo tratan internamente	
En transaciones no se contempla anidamiento por ahora.
Incluye control de IBAN y DATOS PARA PAGO SEPA
Probado con PHP 7.3.21
															
NOTA: Pendiente fragmentar esta función para hacer mas manejable y entendible
------------------------------------------------------------------------------*/
function actualizarDatosSocio($resValidarCamposForm) 
{
	//echo "<br><br>0 modeloSocios.php:actualizarDatosSocio:resValidarCamposForm: "; print_r($resValidarCamposForm);
	
	$resActDatosSocio['nomScript'] = 'modeloSocios.php';	
	$resActDatosSocio['nomFuncion'] = 'actualizarDatosSocio';
 $resActDatosSocio['codError'] = '00000';
 $resActDatosSocio['errorMensaje'] = '';
	$resActDatosSocio['textoComentarios'] = '';	
 $arrMensaje = array();
	
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	require_once "BBDD/MySQL/conexionMySQL.php";
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);
	
	if ($conexionDB['codError'] !== "00000")
	{ $resActDatosSocio = $conexionDB;
   $resActDatosSocio['textoComentarios'] = ". modeloSocios.php:actualizarDatosSocio(): ";
	}
	else //$conexionDB['codError']=="00000"	
	{		
		if	(isset($resValidarCamposForm['datosFormUsuario']['CODUSER']['valorCampo']) && !empty($resValidarCamposForm['datosFormUsuario']['CODUSER']['valorCampo']))		
		{	
	   $usuarioBuscado = $resValidarCamposForm['datosFormUsuario']['CODUSER']['valorCampo'];//desde un Gestor se actualiza un usuario
		}
		elseif (isset($_SESSION['vs_CODUSER']) && !empty($_SESSION['vs_CODUSER']))
		{
			 $usuarioBuscado = $_SESSION['vs_CODUSER'];//el propio usuario se actulaiza el mismo
		}

		require_once("BBDD/MySQL/transationPDO.php");		
		$resIniTrans = beginTransationPDO($conexionDB['conexionLink']);
		
		//echo "<br><br>1-1 modeloSocios.php:actualizarDatosSocio:resIniTrans: ";var_dump($resIniTrans);echo "<br>";
	
		if ($resIniTrans['codError'] !== '00000')//será ['codError'] = '70501';
		{$resIniTrans['errorMensaje'] = 'Error en el sistema, no se ha podido iniciar la transación. ';
			$resIniTrans['numFilas'] = 0;				
			$resActDatosSocio = $resIniTrans;			
			//echo "<br><br>1-1 modeloSocios:actualizarDatosSocio:resIniTrans: ";var_dump($resIniTrans);echo "<br>";
		}		 	
  else //($resIniTrans['codError'] == '00000')	= ($resActDatosSocio['codError'] == '00000')		
		{
			//------------Inicio: actualizar tabla USUARIO ------------------------------	

			$reActUsuario = actualizUsuario('USUARIO',$usuarioBuscado,$resValidarCamposForm['datosFormUsuario'],$conexionDB['conexionLink']);//puede que ['numFilas']=0 y no es error				 			
			
   //echo "<br><br>2 modeloSocios.php:actualizarDatosSocio:reActUsuario: "; print_r($reActUsuario);		
   			
			//------------Fin: actualizar  tabla USUARIO --------------------------------
 	 if ($reActUsuario['codError'] !== '00000')			
	  {$resActDatosSocio = $reActUsuario;
   }		
			else //$reActUsuario['codError']=='00000')
			{
				//------------Inicio: actualizar tabla MIEMBRO -----------------------------
				
			 unset($resValidarCamposForm['datosFormMiembro']['REMAIL']);
				//las dos siguientes están para no cambiar en el formulario "datosFormDomicilio-->datosFormMiembro"						
			 $resValidarCamposForm['datosFormMiembro'] = array_merge($resValidarCamposForm['datosFormMiembro'],$resValidarCamposForm['datosFormDomicilio']);
				
				unset($resValidarCamposForm['datosFormDomicilio']); 		  
    //echo "<br><br>3-1 modeloSocios.php:actualizarDatosSocio:resValidarCamposForm: ";print_r($resValidarCamposForm);
				
				$reActMiembro = actualizMiembro('MIEMBRO',$usuarioBuscado,$resValidarCamposForm['datosFormMiembro'],$conexionDB['conexionLink']);//puede que ['numFilas']=0 y no es error
				
		  //echo "<br><br>3-2 modeloSocios.php:actualizarDatosSocio:reActMiembro: ";print_r($reActMiembro);
    //------------Fin: actualizar  tabla MIEMBRO -------------------------------
			
				if ($reActMiembro['codError'] !== '00000')			
  	 {$resActDatosSocio = $reActMiembro;
	   }	
			 else //$reActMiembro['codError']=='00000')
			 {
					/*-- Inicio buscar datos ANIOCUOTA, IMPORTECUOTAANIOPAGADA y otros con CODUSER --	
					 la función devuelve error si no encuentra ningún socio que cumpla la condición		
					 buscarDatosSocio tiene su propia conexion, pero de cara a la transacion acaso habría 
						que enviar el link	por si hay error.					
					 "%"=todos los años de las cuotas del socio						
					-------------------------------------------------------------------------------*/

				 $resBuscarDatosSocio = buscarDatosSocio($usuarioBuscado,"%",$conexionDB['conexionLink']);//en modeloSocios.php
					
					//echo "<br><br>4-1 modeloSocios.php:actualizarDatosSocio:resBuscarDatosSocio['valoresCampos']: ";print_r($resBuscarDatosSocio['valoresCampos']); 		 
		   
					//-- Fin buscar ANIOCUOTA, IMPORTECUOTAANIOPAGADA y otros para CUOAANIOSOCIO --
					
		 	 if ($resBuscarDatosSocio['codError'] !=='00000')			
			  {$resActDatosSocio = $resBuscarDatosSocio;
		   }
					else //$resBuscarDatosSocio['codError']=='00000')	
					{
						//Primero se comprueba si ya tiene anotada cuota para el siguiente año Y+1
					 
						if (isset($resBuscarDatosSocio['valoresCampos']['datosFormCuotaSocio'][date('Y')+1]))//si ya tiene cuota año Y+1
		    { $anioCuota = date('Y')+1;
						  //echo "<br><br>4-2 modeloSocios.php:actualizarDatosSocio:anioCuota: ";print_r($anioCuota);														
						} 
						else
						{ $anioCuota = date('Y');
						  //echo "<br><br>4-3 modeloSocios.php:actualizarDatosSocio:anioCuota: ";print_r($anioCuota);													 
						}						
						/*--- Inicio Honorarios ----------------------------------------------------	
						Nota: desde 2015 los exentos solo pueden ser socios HONORARIOS y esos nuncan
						van a pagar cuota (aunque podrán hacer donaciones y se les anotará en donaciones)
						Con las 3 asignignaciones que se hacen en el if siguiente, ya se efectuan mas 
						adelante las modificaciones necesarias para honorarios y que son:
						$modoIngresoCuota = 'SIN-DATOS', $ordenarCobroBanco ='NO'
						$resValidarCamposForm['datosFormSocio']['SECUENCIAADEUDOSEPA']['valorCampo']=NULL; 
						$resValidarCamposForm['datosFormSocio']['FECHAACTUALIZACUENTA']['valorCampo']='0000-00-00';
      $resValidarCamposForm['datosFormSocio']['IMPORTECUOTAANIOSOCIO']['valorCampo']=0.00;		
						---------------------------------------------------------------------------*/
						//echo "<br><br>4-4 modeloSocios.php:actualizarDatosSocio:resValidarCamposForm['datosFormSocio']: ";print_r($resValidarCamposForm['datosFormSocio']);      
							
      if ( isset($resValidarCamposForm['datosFormSocio']['CODCUOTA']['valorCampo']) && $resValidarCamposForm['datosFormSocio']['CODCUOTA']['valorCampo'] == 'Honorario') //o en validar
						{ $resValidarCamposForm['datosFormSocio']['CUENTAIBAN']['valorCampo'] = NULL; //o en validar
				   	$resValidarCamposForm['datosFormSocio']['CUENTANOIBAN']['valorCampo'] = NULL;
								$resValidarCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']['valorCampo'] = 0.00;
						}	
      /*--- Fin Honorarios ------------------------------------------------------							
						
						/*--- Inicio determinar valores $modoIngresoCuota y $ordenarCobroBanco ----	
      $modoIngresoCuota se salva en las tablas SOCIO y CUOTAANIOSOCIO, 		
      $ordenarCobroBanco	se salva en la tabla CUOTAANIOSOCIO					
						Nota: ahora 2015 los exentos solo pueden ser socios HONORARIOS y esos nuncan
						van a pagar cuota (aunque podrán hacer donaciones y se les anotará en donaciones)
						Esto habría que programarlo mas sencillo, para adaptarse a la nueva situación 
						-------------------------------------------------------------------------*/
		    //echo "<br><br>4-4 modeloSocios.php:actualizarDatosSocio:resValidarCamposForm['datosFormCuotaSocio']: ";print_r($resValidarCamposForm['datosFormCuotaSocio']);		 
						
		    //---if que controla si en el formulario hay CUENTA  ---------------------
				  if ((isset($resValidarCamposForm['datosFormSocio']['CUENTAIBAN']['valorCampo'])   && !empty($resValidarCamposForm['datosFormSocio']['CUENTAIBAN']['valorCampo']))	|| 
								 	(isset($resValidarCamposForm['datosFormSocio']['CUENTANOIBAN']['valorCampo']) && !empty($resValidarCamposForm['datosFormSocio']['CUENTANOIBAN']['valorCampo']))
								 ) 							
				  {$modoIngresoCuota = 'DOMICILIADA';// puesto que hay CUENTAIBAN o CUENTANOIBAN
	
							if (isset($resValidarCamposForm['datosFormSocio']['CUENTANOIBAN']['valorCampo']) && !empty($resValidarCamposForm['datosFormSocio']['CUENTANOIBAN']['valorCampo']))        
       { $ordenarCobroBanco = 'NO';//puesto que CUENTANOIBAN no se pueden domiciliar por ahora		
         //echo "<br><br>5-1 modeloSocios.php:actualizarDatosSocio:resValidarCamposForm['datosFormCuotaSocio']: ";print_r($resValidarCamposForm['datosFormCuotaSocio']);						
							}
							elseif (isset($resValidarCamposForm['datosFormSocio']['CUENTAIBAN']['valorCampo']) && !empty($resValidarCamposForm['datosFormSocio']['CUENTAIBAN']['valorCampo'])) 								
							{ 
									/*-- if que controla si en el formulario se recogen valores "ORDENARCOBROBANCO" 
											para asignarlo como valor para "$ordenarCobroBanco" esto por ahora solo se hace
											en el rol de tesorería en el formulario "formActualizarDatosCuotaSocioTes.php". 
											Si no, es que se atualiza desde roles de Socio, Coord, o Presi y en este caso 
											si en la tabla SOCIO ya había CUENTAIBAN se deja el valor que tuviese	en 
											$ordenarCobroBanco	ese valor, solo lo cambia el tesorero cuando proceda.
									---------------------------------------------------------------------------*/	
									//echo "<br><br>5-2-2 modeloSocios.php:actualizarDatosSocio:resValidarCamposForm['datosFormCuotaSocio']['ORDENARCOBROBANCO']['valorCampo']: ";print_r($resValidarCamposForm['datosFormCuotaSocio']['ORDENARCOBROBANCO']['valorCampo']);										
																													
									if (isset($resValidarCamposForm['datosFormCuotaSocio']['ORDENARCOBROBANCO']['valorCampo']) && !empty($resValidarCamposForm['datosFormCuotaSocio']['ORDENARCOBROBANCO']['valorCampo'])) 
									{ //--- si la actualizacion procede de rol de tesorero -------------------------------------------
											$ordenarCobroBanco = $resValidarCamposForm['datosFormCuotaSocio']['ORDENARCOBROBANCO']['valorCampo'];	
											//echo "<br><br>5-2-3 modeloSocios.php:actualizarDatosSocio:ordenarCobroBanco: ";print_r($ordenarCobroBanco);									
									}	
									else // !if (($resValidarCamposForm['datosFormCuotaSocio']['ORDENARCOBROBANCO']['valorCampo']) && !empty($resValidarCamposForm ....
									{	//--- si la actualizacion procede de rol de socio u otros gestores distinto de tesorero --------
							
											if (isset($resBuscarDatosSocio['valoresCampos']['datosFormSocio']['CUENTAIBAN']['valorCampo']) && 
															!empty($resBuscarDatosSocio['valoresCampos']['datosFormSocio']['CUENTAIBAN']['valorCampo'])	) 
											{//echo "<br><br>5-3 modeloSocios.php:actualizarDatosSocio:resBuscarDatosSocio['valoresCampos']['datosFormCuotaSocio'][date('Y')]['ESTADOCUOTA']['valorCampo']: ";
												//print_r($resBuscarDatosSocio['valoresCampos']['datosFormCuotaSocio'][date('Y')]['ESTADOCUOTA']['valorCampo']);											
											
												if ($resBuscarDatosSocio['valoresCampos']['datosFormCuotaSocio'][date('Y')]['ESTADOCUOTA']['valorCampo'] =='ABONADA' && $anioCuota !== date('Y')+1)//aun no se ha insertado cuota
												{//echo "<br><br>5-3-1 modeloSocios:actualizarDatosSocio";
													$ordenarCobroBanco = 'SI'; //ya que será una inserción en fila Y+1 en tabla CUOTANIOSOCIO y sí tiene CC
												}
												else //se dejan los datos que tuviese ya que será actualización a otra distinta CC para pagar en el año actual "Y"
												{//echo "<br><br>5-3-2 modeloSocios:actualizarDatosSocio";
													$ordenarCobroBanco = $resBuscarDatosSocio['valoresCampos']['datosFormCuotaSocio'][$anioCuota]['ORDENARCOBROBANCO']['valorCampo'];							
												}											
											}
											else //si en tabla SOCIO, NO había CUENTA, es que el socio acaba de añadir la cuenta para domiciliar cobro y se supone que para pagar
											{//echo "<br><br>5-3-4 modeloSocios:actualizarDatosSocio";
												$ordenarCobroBanco = 'SI';	
											}					
									}//else !if (($resValidarCamposForm['datosFormCuotaSocio']['ORDENARCOBROBANCO']['valorCampo']) && !empty($resValidarCamposForm	
						 }//elseif ((isset($resValidarCamposForm['datosFormSocio']['CUENTAIBAN']['valorCampo']) &&   !empty($resValidarCamposForm['datosFormSocio']['CUENTAIBAN']['valorCampo']))						
				  }// if ((isset($resValidarCamposForm['datosFormSocio']['CUENTAIBAN']['valorCampo']) &&...(isset($resValidarCamposForm['datosFormSocio']['CUENTANOIBAN']['valorCampo']) && 
				  else //if !! ((isset($resValidarCamposForm['datosFormSocio']['CUENTAIBAN']['valorCampo']) && ....
					 {
							//echo "<br><br>5-4 modeloSocios:actualizarDatosSocio";
			    $modoIngresoCuota  = 'SIN-DATOS';	
				 	 $ordenarCobroBanco = 'NO';	
				 	}		
						
					 $resValidarCamposForm['datosFormSocio']['MODOINGRESO']['valorCampo'] =	$modoIngresoCuota;					
		    $resValidarCamposForm['datosFormSocio']['IMPORTECUOTAANIOSOCIO']['valorCampo'] = $resValidarCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']['valorCampo'];
	
				 	/*-- Fin determinar valores de $modoIngresoCuota y $ordenarCobroBanco --*/

      /*============ Inicio PARA SEPA añadido 2015-01-10 =======================
						para etiquetas SEPA "Secuencia del adeudo" <SeqTp> y "fecha firma" <DtOfSgntr>
						, si no hubiese fecha firma
						 NOTA: pudiera ser útil una función independiente, para poderla compartir
						========================================================================*/
				  if (isset($resValidarCamposForm['datosFormSocio']['CUENTAIBAN']['valorCampo']) &&  !empty($resValidarCamposForm['datosFormSocio']['CUENTAIBAN']['valorCampo']))
						{ 
					   // -----------Inicio  Ya había una CUENTAIBAN en la tabla SOCIO --------
	       if (isset($resBuscarDatosSocio['valoresCampos']['datosFormSocio']['CUENTAIBAN']['valorCampo']) && !empty($resBuscarDatosSocio['valoresCampos']['datosFormSocio']['CUENTAIBAN']['valorCampo'])) 
								{									
										if ($resBuscarDatosSocio['valoresCampos']['datosFormSocio']['CUENTAIBAN']['valorCampo'] !== $resValidarCamposForm['datosFormSocio']['CUENTAIBAN']['valorCampo'])	//Cuenta es distinta				
										{
												if (substr($resBuscarDatosSocio['valoresCampos']['datosFormSocio']['CUENTAIBAN']['valorCampo'],4,4) !== substr($resValidarCamposForm['datosFormSocio']['CUENTAIBAN']['valorCampo'],4,4))//distinta entidad bancaria,  												
												{//echo "<br><br>IBAN-a";														
													//Se ponen los valores de 'FRST' y fecha actual para etiquetas SEPA "Secuencia del adeudo" <SeqTp> y "fecha firma" <DtOfSgntr>, si no hubiese fecha firma 												
													$resValidarCamposForm['datosFormSocio']['SECUENCIAADEUDOSEPA']['valorCampo']		= 'FRST';	//distinta cuenta y distinta entidad bancaria: la etiqueta cobros SEPA "Secuencia del adeudo" <SeqTp>=FRST)			
													$resValidarCamposForm['datosFormSocio']['FECHAACTUALIZACUENTA']['valorCampo'] =	date('Y-m-d'); //fecha nuevo IBAN, se utilizará también para etiqueta cobros SEPA "fecha firma" <DtOfSgntr>, si no hubiese fecha firma 												
												}								
												//else //misma entidad bancaria y distinta cuenta
												//{echo "<br><br>IBAN-b";														
												  	/*Se deja como esté, pues aunque la cuanta cambie no afecta a etiqueta 
												  	SEPA "Secuencia del adeudo" <SeqTp> ni a SEPA "Secuencia del adeudo" <SeqTp>*/													
													  //$resValidarCamposForm['datosFormSocio']['SECUENCIAADEUDOSEPA']['valorCampo']= 'FRST o RCUR';//primera vez que se cobra en esa cuenta: la etiqueta cobros SEPA "Secuencia del adeudo" <SeqTp>=FRST)			
													  //$resValidarCamposForm['datosFormSocio']['FECHAACTUALIZACUENTA']['valorCampo']=	date('Y-m-d');//fecha nuevo IBAN, se utilizará también para etiqueta cobros SEPA "fecha firma" <DtOfSgntr>, si no hubiese fecha firma 												
												//}										
										}
									//else //cuenta igual se deja como esté {			}									
						  }	
								// ----------Fin  Ya había una CUENTAIBAN en la tabla SOCIO ------------
        else //nueva domiciliación IBAN
        {//Se ponen los valores de 'FRST' y fecha actual para etiquetas SEPA "Secuencia del adeudo" <SeqTp> y "fecha firma" <DtOfSgntr>, si no hubiese fecha firma 
								 //echo "<br><br>IBAN-c";
									$resValidarCamposForm['datosFormSocio']['SECUENCIAADEUDOSEPA']['valorCampo']		= 'FRST';	//distinta cuenta y distinta entidad bancaria: la etiqueta cobros SEPA "Secuencia del adeudo" <SeqTp>=FRST)			
									$resValidarCamposForm['datosFormSocio']['FECHAACTUALIZACUENTA']['valorCampo'] =	date('Y-m-d'); //fecha nuevo IBAN, se utilizará también para etiqueta cobros SEPA "fecha firma" <DtOfSgntr>, si no hubiese fecha firma 																										
								}									
						}	
						elseif (isset($resValidarCamposForm['datosFormSocio']['CUENTANOIBAN']['valorCampo']) &&  !empty($resValidarCamposForm['datosFormSocio']['CUENTANOIBAN']['valorCampo']))
						{ //echo "<br><br>IBAN-d";
							 if (isset($resBuscarDatosSocio['valoresCampos']['datosFormSocio']['CUENTANOIBAN']['valorCampo']) && !empty($resBuscarDatosSocio['valoresCampos']['datosFormSocio']['CUENTANOIBAN']['valorCampo'])) 
								{ //echo "<br><br>IBAN-e";
          if ($resBuscarDatosSocio['valoresCampos']['datosFormSocio']['CUENTANOIBAN']['valorCampo'] !== $resValidarCamposForm['datosFormSocio']['CUENTANOIBAN']['valorCampo'])	//Cuenta es distinta				
										{									
							    //Hay cuenta 	CUENTANOIBAN	y es distina la nueva
							    //echo "<br><br>IBAN-f";
							    $resValidarCamposForm['datosFormSocio']['SECUENCIAADEUDOSEPA']['valorCampo']		= NULL; //no hay cuenta IBAN			
							    $resValidarCamposForm['datosFormSocio']['FECHAACTUALIZACUENTA']['valorCampo'] =	date('Y-m-d');	//Hay cuenta CUENTANOIBAN	
										}
										else //misma  cuenta CUENTANOIBAN
										{//echo "<br><br>IBAN-g";														
											//Se deja como esté, pues aunque la cuanta cambie no afecta a etiqueta SEPA "Secuencia del adeudo" <SeqTp> ni a SEPA "Secuencia del adeudo" <SeqTp>												
										}			
        }
        else
        {	//No había y a ahora hay nueva cuenta 	CUENTANOIBAN	 y antes no la había
										//echo "<br><br>IBAN-h";
										$resValidarCamposForm['datosFormSocio']['SECUENCIAADEUDOSEPA']['valorCampo']		= NULL; //no hay cuenta IBAN
										$resValidarCamposForm['datosFormSocio']['FECHAACTUALIZACUENTA']['valorCampo'] =	date('Y-m-d');	//Hay cuenta CUENTANOIBAN
								}									
						}
      else
      {// No hay cuenta IBAN		ni CUENTANOIBAN	
							//echo "<br><br>IBAN-i";
							$resValidarCamposForm['datosFormSocio']['SECUENCIAADEUDOSEPA']['valorCampo']		= NULL; 
							$resValidarCamposForm['datosFormSocio']['FECHAACTUALIZACUENTA']['valorCampo'] =	'0000-00-00';	
						}		
      /*=========== Fin  PARA SEPA añadido 2015-01-10 ========================== 
						 para etiquetas SEPA "Secuencia del adeudo" <SeqTp> FRST, RCUR y
					 	"fecha firma" <DtOfSgntr>, si no hubiese fecha firma 		
      ========================================================================*/
									
				 	/* -------------------- Inicio  actualizar SOCIO -------------------------				
		     La encriptación c.bancos, si mas adelante se hiciese, sería en 
						 actualizSocio(), ya que se realizaría siempre	
							-----------------------------------------------------------------------*/	
							
				  $reActSocio = actualizSocio('SOCIO',$usuarioBuscado,$resValidarCamposForm['datosFormSocio'],$conexionDB['conexionLink']);//puede ser ['numFilas']=0 y no es error
						
				 //	echo "<br><br>6 modeloSocios.php:actualizarDatosSocio:reActSocio: ";print_r($reActSocio);
						
					 //------------------------- Fin  actualizar SOCIO ------------------------
					
				  if ($reActSocio['codError'] !== '00000')			
		    {$resActDatosSocio = $reActSocio;
			   }	
					 else //$reActSocio['codError']=='00000')
					 {				
				   /*== Inicio actualizCuotaAnioSocio() e	insertarCuotaAnioSocio() en CUOTAANIOSOCIO ==
							 Que necesita muchas preparaciones previas 
								Nota: Mejor hacer una función independiente para simplicar 
							=======================================================================*/				  
									
						 //------------- inicio obtener codSocio para CUOTAANIOSOCIO ------------- 	
							if	(isset($resValidarCamposForm['datosFormSocio']['CODSOCIO']['valorCampo']) && !empty($resValidarCamposForm['datosFormSocio']['CODSOCIO']['valorCampo'])
										)		
							{	$codSocio = $resValidarCamposForm['datosFormSocio']['CODSOCIO']['valorCampo'];//desde Gestor se usa esta función para actualizar un socio
							}
							elseif (isset($_SESSION['vs_CODSOCIO']) && !empty($_SESSION['vs_CODSOCIO']))//el propio socio se actualza por el mismo
							{$codSocio = $_SESSION['vs_CODSOCIO'];
							}							
							//else {// tratar error, pero no se debe producir}				
							//------------- fin obtener codSocio ------------------------------------  
							
		  			$resValidarCamposForm['datosFormCuotaSocio']['CODAGRUPACION']['valorCampo'] = $resValidarCamposForm['datosFormSocio']['CODAGRUPACION']['valorCampo'];									
						 $resValidarCamposForm['datosFormCuotaSocio']['CODCUOTA']['valorCampo'] = $resValidarCamposForm['datosFormSocio']['CODCUOTA']['valorCampo'];	
										
       $resValidarCamposForm['datosFormCuotaSocio']['MODOINGRESO']['valorCampo'] = $modoIngresoCuota;										
       $resValidarCamposForm['datosFormCuotaSocio']['ORDENARCOBROBANCO']['valorCampo'] =	$ordenarCobroBanco; 													
								
							//echo "<br><br>7-1a modeloSocios.php:actualizarDatosSocio: resValidarCamposForm:['datosCuotasEL']: ";print_r($resValidarCamposForm['datosCuotasEL']);							
	      //echo "<br><br>7-1b modeloSocios.php:actualizarDatosSocio: resValidarCamposForm:['datosFormCuotaSocio']: ";print_r($resValidarCamposForm['datosFormCuotaSocio']);
							
							if ($resBuscarDatosSocio['valoresCampos']['datosFormCuotaSocio'][$anioCuota]['ESTADOCUOTA']['valorCampo'] !== "ABONADA")																					
				   {
								/*--- Inicio actualizar en "CUOTAANIOSOCIOS" campos CODCUOTA,ESTADOCUOTA,IMPORTECUOTAANIOEL y otros ---
							  Según la asignación del if anterior $anioCuota=Y o $anioCuota=Y+1:
							  - Si $anioCuota=Y y ESTADOCUOTA !==ABONADA, se actualizará la fila "Y" 
								 - Si $anioCuota=Y y ESTADOCUOTA ==ABONADA, y no hay fila "Y+1" se insertará la fila "Y" 
							  Si $anioCuota=Y+1, siempre ESTADOCUOTA !==ABONADA, se actualizará la fila "Y+1" 
						  ----------------------------------------------------------------------*/
							 //echo "<br><br>7-2a modeloSocios:actualizarDatosSocio:resBuscarDatosSocio['valoresCampos']['datosFormCuotaSocio'][anioCuota]['ESTADOCUOTA']['valorCampo']:";
							 //print_r($resBuscarDatosSocio['valoresCampos']['datosFormCuotaSocio'][$anioCuota]['ESTADOCUOTA']['valorCampo']);		
								
								if ($resValidarCamposForm['datosFormCuotaSocio']['CODCUOTA']['valorCampo'] == "Honorario") 
								{ //echo "<br><br>7-2b modeloSocios:actualizarDatosSocio:resValidarCamposForm['datosFormCuotaSocio']: ";print_r($resValidarCamposForm['datosFormCuotaSocio']);
								  
										$resValidarCamposForm['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo'] ='EXENTO';//¿¿¿esta asignacion sobra																																																																																		
								  $resValidarCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOEL']['valorCampo'] ='0.00';//¿¿¿esta asignacion sobra
								}
								else //resValidarCamposForm['datosFormCuotaSocio']['CODCUOTA']['valorCampo']==General, Parado, Joven,
								{ //echo "<br><br>7-3a modeloSocios:actualizarDatosSocio:resValidarCamposForm['datosFormCuotaSocio']: ";print_r($resValidarCamposForm['datosFormCuotaSocio']);
								
								  if ($resBuscarDatosSocio['valoresCampos']['datosFormCuotaSocio'][$anioCuota]['CODCUOTA']['valorCampo'] == "Honorario")//antes era Honorario
										{//Al cambiar de Honorario a: General, Parado, Joven, habrá que poner 'PENDIENTE-COBRO'; 
											//echo "<br><br>7-3b modeloSocios:actualizarDatosSocio:resValidarCamposForm['datosFormCuotaSocio']: ";
											
											$resValidarCamposForm['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo'] = 'PENDIENTE-COBRO';
										}										
										if ($resBuscarDatosSocio['valoresCampos']['datosFormCuotaSocio'][$anioCuota]['IMPORTECUOTAANIOPAGADA']['valorCampo'] == 0)
										{
											//-- Para $anioCuota=Y+1	IMPORTECUOTAANIOPAGADA siempre será =0, para $anioCuota=Y, no	siempre será =0														 
																	
											//echo "<br /><br />7-3c modeloSocios.php:actualizarDatosSocios";	
											
											//Dejo 'ESTADOCUOTA' con los que tuviese que sería: PENDIENTE-COBRO
				       //--	En el caso de que 	NOABONADA-ERROR-CUENTA' o 'NOABONADA-DEVUELTA' aplicamos la siguiente condición: 
											
											if($resValidarCamposForm['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo'] == 'NOABONADA-ERROR-CUENTA' || 
														$resValidarCamposForm['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo'] == 'NOABONADA-DEVUELTA'
													)
											{//echo "<br><br>7-3d mmodeloSocios.php:actualizarDatosSocios";
											
												if ($resValidarCamposForm['datosFormSocio']['CUENTAIBAN']['valorCampo'] !== $resBuscarDatosSocio['valoresCampos']['datosFormSocio']['CUENTAIBAN']['valorCampo'] )
												{
													//echo "<br><br>7-3e mmodeloSocios:actualizarDatosSocios";	
													
													$resValidarCamposForm['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo'] = 'PENDIENTE-COBRO';												
												/* al cambiar la cuenta el socio, si no está pagada pondré 'PENDIENTE-COBRO' 
												(interpreto que la nueva cuenta CUENTAIBAN introducida por el socio es correcta y tiene fondos)	
												y si es igual cuanta que la anterior (sigue siendo errónea) y no 
												la cambio porque esta anotada por tesorero como NOABONADA-ERROR-CUENTA'
												o 'NOABONADA-DEVUELTA'. Para CUENTANOIBAN no lo cambio, lo dejo como estaba  
												------------------------------------------------------------------*/
												}
											}           													
										}	//if ($resBuscarDatosSocio['valoresCampos']['datosFormCuotaSocio'][$anioCuota]['IMPORTECUOTAANIOPAGADA']['valorCampo'] == 0)	
											
										elseif ($resBuscarDatosSocio['valoresCampos']['datosFormCuotaSocio'][$anioCuota]['IMPORTECUOTAANIOPAGADA']['valorCampo'] 
	                 >= $resValidarCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOEL']['valorCampo'])
	 								{
											//echo "<br><br>7-3f mmodeloSocios.php:actualizarDatosSocios";	
											
											//-- Puede suceder que al cambiar CODCUOTA General->Parado,Joven y ESTADOCUOTA=ABONADA-PARTE
											// el importe pagado parcialmente sea superior a correspondiente a 
											//esa cuota y entonce quede como ABONADA										
										 $resValidarCamposForm['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo'] = 'ABONADA';
											
											$resValidarCamposForm['datosFormCuotaSocio']['FECHAANOTACION']['valorCampo'] = date('Y-m-d');											
											$resValidarCamposForm['datosFormCuotaSocio']['FECHAPAGO']['valorCampo'] = date('Y-m-d');
											
											$resValidarCamposForm['datosFormCuotaSocio']['OBSERVACIONES']['valorCampo'] = 
											"Mensaje automático del programa: Estaba ABONADA-PARTE y había pagado ".$resBuscarDatosSocio['valoresCampos']['datosFormCuotaSocio'][$anioCuota]['IMPORTECUOTAANIOPAGADA']['valorCampo'].
											" euros y al cambiar de tipo de cuota a - ".$resValidarCamposForm['datosFormCuotaSocio']['CODCUOTA']['valorCampo'].
											" - ha quedado como pagada, con fecha ".date('Y-m-d');
										}
										else //IMPORTECUOTAANIOPAGADA']<['IMPORTECUOTAANIOEL']->importe pagado es inferior al correspondiente a esa cuota
										{ 
										 $resValidarCamposForm['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo'] = 'ABONADA-PARTE';										
										}	
								}//else //CODCUOTA==General, Parado, Joven,,	  

		      $reActCuotaAnioSocio = actualizCuotaAnioSocio('CUOTAANIOSOCIO',$codSocio,$resValidarCamposForm['datosFormCuotaSocio'],$conexionDB['conexionLink']);//puede ser ['numFilas']=0 y no es error
								
				    //echo "<br><br>7-4 modeloSocios.php:actualizarDatosSocio:reActCuotaAnioSocio: ";print_r($reActCuotaAnioSocio);	
						 }//if ($resValidarCamposForm['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo'] !=="ABONADA") 
								
							//----------------- Fin actualizCuotaAnioSocio ESTADOCUOTA	--------------

							else //$resBuscarDatosSocio['valoresCampos']['datosFormCuotaSocio'][$anioCuota]['ESTADOCUOTA']['valorCampo'] == "ABONADA"								
							{
								//--- Inicio insertar fila  "Y+1"  en "CUOTAANIOSOCIOS" ----------------
												 
							 if ($resValidarCamposForm['datosFormSocio']['CODCUOTA']['valorCampo'] == 'General')
								{$resValidarCamposForm['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo'] = 'PENDIENTE-COBRO';	
								}
								elseif ($resValidarCamposForm['datosFormSocio']['CODCUOTA']['valorCampo'] == 'Honorario')//no entrará aquí nunca
								{$resValidarCamposForm['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo'] = 'EXENTO';		
								}
								else //CODCUOTA== Joven, Parado, .... (se deja separado de General por si posteriormente se establece nuevos tratamientos)
								{$resValidarCamposForm['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo'] = 'PENDIENTE-COBRO';			 							
								}			
								
						  $arrValoresInserCuota = $resValidarCamposForm['datosFormCuotaSocio'];
								$arrValoresInserCuota['CODSOCIO']['valorCampo'] = $codSocio;	
					  
								$reActCuotaAnioSocio = insertarCuotaAnioSocio($arrValoresInserCuota,$conexionDB['conexionLink']);//Para EL PRÓXIMO AÑO, ya incluye conexion	
								
	       //echo "<br><br>7-5 modeloSocios.php:actualizarDatosSocio:reActCuotaAnioSocio: "; print_r($reActCuotaAnioSocio);				
				   
							}///$resBuscarDatosSocio['valoresCampos']['datosFormCuotaSocio'][$anioCuota]['ESTADOCUOTA']['valorCampo'] == "ABONADA"													
							//--- Fin insertar fila  "Y+1"  en "CUOTAANIOSOCIOS" --------------------							
							
							/*==FIN actualizCuotaAnioSocio() e insertarCuotaAnioSocio()en CUOTAANIOSOCIO==
							=======================================================================*/	
							//echo "<br><br>7-6 modeloSocios.php:actualizarDatosSocio:reActCuotaAnioSocio: "; print_r($reActCuotaAnioSocio);	
							
					  if ($reActCuotaAnioSocio['codError'] !=='00000')			
		   	 {$resActDatosSocio = $reActCuotaAnioSocio;
				   }	
						 else //$reActCuotaAnioSocio['codError']=='00000')
						 { //echo "<br><br>8-1 modeloSocios.php:actualizarDatosSocio:reActCuotaAnioSocio: ";print_r(reActCuotaAnioSocio);

									$resFinTrans = commitPDO($conexionDB['conexionLink']);//será ['codError'] = '70502';
									//echo "<br><br>8-2 modeloSocios.php:actualizarDatosSocio:resFinTrans: ";var_dump($resFinTrans);
									
									if ($resFinTrans['codError'] !=='00000')//será ['codError'] = '70502';								
									{ $resFinTrans['errorMensaje'] = 'Error en el sistema, no se han podido actualizar los datos del socio/a. ';
											$resFinTrans['numFilas'] = 0;																				
											$resActDatosSocio = $resFinTrans;	
											//echo "<br><br>8-3 modeloSocios.php:actualizarDatosSocio:resFinTrans: ";print_r($resFinTrans);
									}											
									else
									{//$_SESSION['vs_NOMUSUARIO']=$resValidarCamposForm['datosFormUsuario']['USUARIO']['valorCampo'];
										//echo "<br><br>8-4 modeloSocios.php:actualizarDatosSocio:Se han actualizado los datos del socio/a";
										$arrMensaje['textoComentarios'] = "Se han actualizado los datos del socio/a <strong>".
										$resValidarCamposForm['datosFormMiembro']['NOM']['valorCampo']." ".
										$resValidarCamposForm['datosFormMiembro']['APE1']['valorCampo']."<strong /><br /><br />";	
									}														
										
						 }//else $reActCuotaAnioSocio['codError']=='00000'
				  }//else $reActSocio['codError']=='00000'
				 }//else $resBuscarDatosSocio['codError']=='00000'
				}//else $reActMiembro['codError']=='00000'
		 }//else $reActUsuario['codError']=='00000'																																							
		
	  //echo "<br><br>8-5 modeloSocios.php:actualizarDatosSocio:resActDatosSocio: ";print_r($resActDatosSocio);
			
			//---------------- Inicio tratamiento errores -------------------------------	
   
			//--- Inicio deshacer transación en las tablas modificadas ---------------
	  if ($resActDatosSocio['codError'] !=='00000')
		 { //echo "<br><br>9-1 modeloSocios:actualizarDatosSocio:resActDatosSocio: ";print_r($resActDatosSocio);
				
				$resDeshacerTrans = rollbackPDO($conexionDB['conexionLink']);
				
				//echo "<br><br>9-2 modeloSocios.php:actualizarDatosSocio:deshacerTrans: ";var_dump($deshacerTrans);
					
			 if ($resDeshacerTrans['codError'] !== '00000')//será ['codError'] = '70503';
				{
				  $resActDatosSocio['errorMensaje'] .= $resDeshacerTrans['errorMensaje'];				
		  }	
   }//--- Fin deshacer transación en las tablas modificadas -----------------
			
  }//else $resIniTrans['codError'] == '00000'
		
	 //--- Inicio Grabar en tabla ERRORES, incluido error beginTransationPDO()--		
		if ($resActDatosSocio['codError'] !=='00000' || (isset($resDeshacerTrans['codError']) && $resDeshacerTrans['codError'] !== '00000') )
		{
			if ( isset($resActDatosSocio['textoComentarios']) ) 
			{ $resActDatosSocio['textoComentarios'] = ". modeloSocios.php:actualizarDatosSocio(): ".$resActDatosSocio['textoComentarios'];}	
			else
			{	$resActDatosSocio['textoComentarios'] = ". modeloSocios.php:actualizarDatosSocio(): ";}	

			require_once './modelos/modeloErrores.php'; 
			$resInsertarErrores = insertarError($resActDatosSocio,$conexionDB['conexionLink']);
			//echo "<br><br>9-3 modeloSocios.php:actualizarDatosSocio:resActDatosSocio: ";print_r($resActDatosSocio);
			
			if ($resInsertarErrores['codError'] !== '00000')
			{ $resActDatosSocio['codError'] = $resInsertarErrores['codError'];
					$resActDatosSocio['errorMensaje'] .= $resInsertarErrores['errorMensaje'];
			}					
		}//--- Fin Grabar en tabla ERRORES, incluido error beginTransationPDO()----
		
	//---------------- Fin tratamiento errores ------------------------------------	
	
	}//else $conexionDB['codError']=="00000"		
	
	$resActDatosSocio['arrMensaje'] = $arrMensaje;	
	
	//echo "<br><br>9-4 modeloSocios:actualizarDatosSocio:resActDatosSocio:";print_r($resActDatosSocio);
	
 return 	$resActDatosSocio; 	
}
/*---------------- Fin actualizarDatosSocio ------------------------------------*/

/*----------------------------- Inicio insertarSocio-----------------------------
En esta función se conecta a la BBDD, busca el CODSOCIO máximo en la 
tabla SOCIOS, inserta una fila con los datos del socio y devuelve el 
CODSOCIO (numero de socio) insertado, y los campos de error. 

RECIBE: un array con los campos de los formularios ya validados
DEVUELVE: un array con los controles de errores, y CODSOCIO(n. socio) insertado

LLAMADA: modeloSocios.php:altaSociosConfirmada(),
modeloPresCoord.php:mAltaSocioPorGestor(),altaSocioPendienteConfirmadaPorGestor()
LLAMA: a varias funciones 

OBSERVACIONES: Sin Encriptar cuentas bancos, incluye IBAN y NOIBAN, y datos 
para PAGO SEPA
No se requiere modificaciones para PDO en las funciones buscarCodMax() y
insertarUnaFila(), lo gestionan dentro de ellas con los par. que reciben
------------------------------------------------------------------------------*/
function insertarSocio($resulValidar)//se pasa el array :$resulValidar['datosFormSocio']
{
 //echo "<br><br>0 modeloSocios.php:insertarSocio:resulValidar: "; print_r($resulValidar);

	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";		
	require_once "BBDD/MySQL/conexionMySQL.php";	
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);	
	
	if ($conexionDB['codError'] !== "00000")
	{ $resulInserSocio = $conexionDB;
	}
	else
	{$tablasBusqueda = 'SOCIO';
		$camposBuscados = 'CODSOCIO';
  
		require_once './modelos/libs/buscarCodMax.php';
	 $resulBuscarCodMax = buscarCodMax($tablasBusqueda,$camposBuscados,$conexionDB['conexionLink']);
		
		if ($resulBuscarCodMax['codError'] !== '00000')
		{ $resulInserSocio = $resulBuscarCodMax;
		}
		else 
		{$arrValoresInser['CODSOCIO'] = $resulBuscarCodMax['valorCampo'];
			$arrValoresInser['CODUSER'] = $resulValidar['CODUSER']['valorCampo'];
			$arrValoresInser['CODAGRUPACION'] = $resulValidar['CODAGRUPACION']['valorCampo'];
			$arrValoresInser['CODCUOTA'] = $resulValidar['CODCUOTA']['valorCampo'];
			$arrValoresInser['IMPORTECUOTAANIOSOCIO'] = $resulValidar['IMPORTECUOTAANIOSOCIO']['valorCampo'];	
	
			$arrValoresInser['FECHAALTA'] = date('Y-m-d');//mysql:(CURRENT_DATE());
			$arrValoresInser['FECHABAJA'] = '0000-00-00';			

   $arrValoresInser['MODOINGRESO'] = $resulValidar['MODOINGRESO']['valorCampo'];

			if (isset($resulValidar['CUENTAIBAN']['valorCampo']) && !empty ($resulValidar['CUENTAIBAN']['valorCampo']))
			{	$arrValoresInser['CUENTAIBAN'] = $resulValidar['CUENTAIBAN']['valorCampo'];
		
		   //--------------------------------- PARA COBROS SEPA añadido enn 2015-11-10 --------------------------------------------------------------------------------------------------------
					
					$arrValoresInser['SECUENCIAADEUDOSEPA']		= 'FRST';	//primera vez que se cobra: la etiqueta cobros SEPA "Secuencia del adeudo" <SeqTp>=FRST)	
					$arrValoresInser['FECHAACTUALIZACUENTA']	= $arrValoresInser['FECHAALTA'];//fecha nuevo IBAN, se utilizará también para etiqueta cobros SEPA "fecha firma" <DtOfSgntr>, si no hubiese fecha firma 										
     //----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
			}
			elseif (isset($resulValidar['CUENTANOIBAN']['valorCampo']) && !empty($resulValidar['CUENTANOIBAN']['valorCampo'])) 
			{	$arrValoresInser['CUENTANOIBAN'] = $resulValidar['CUENTANOIBAN']['valorCampo'];
			
			  //$arrValoresInser['SECUENCIAADEUDOSEPA']		= NULL;	//Por defecto en la tabla se pondrá a NULL ya desde la configuracion de este campo en la tabla
					$arrValoresInser['FECHAACTUALIZACUENTA']	= $arrValoresInser['FECHAALTA'];// fecha de nueva cuenta NOIBAN,				
			}			
   else // No hay cuenta CUENTAIBAN ni CUENTANOIBAN
	  { //$arrValoresInser['SECUENCIAADEUDOSEPA']		= NULL; //Por defecto en la tabla se pondrá a NULL
			  $arrValoresInser['FECHAACTUALIZACUENTA']	= '0000-00-00';
			}	
   //echo "<br><br>2 insertarSocio:arrValoresInser:"; print_r($arrValoresInser); 
			
	  $resulInserSocio = insertarUnaFila('SOCIO',$arrValoresInser,$conexionDB['conexionLink']);
			if ($resulInserSocio)
			{	
			  $resulInserSocio['CODSOCIO'] = $arrValoresInser['CODSOCIO'];
			}
		}
	}	
 //echo "<br><br>3 insertarSocio:resulInserSocio:"; print_r($resulInserSocio); echo "<br>";
	
 return $resulInserSocio;
}
/*----------------------------- Fin insertarSocio ------------------------------*/


/*----------------------------- Inicio insertarSocioEncriptandoCCCEX ------------
Acción: En esta funciónse conecta a la BBDD, busca el CODSOCIO máximo en la 
        tabla SOCIOS, inserta una fila con los datos del socio y devuelve el 
								CODSOCIO (numero de socio) insertado, y los campos de error. 
						  Los números de cuentas bancarias se insertan encriptadas
Recibe: un array con los campos de los formularios ya validados
Devuelve: un array con los controles de errores, y CODSOCIO(n. socio) insertado
Llamada: desde modeloSocios: altaSocio(), 
         modeloPresCood.php:mAltaSocioPorGestor(), altaSocioPendienteConfirmadaPorGestor()
								
Llama:  /modelos/libs/buscarCodMax.php:buscarCodMax()
OBSERVACIONES: Los números de cuentas bancarias: NUMCUENTA y CCEXTRANJERA
               se insertan ENCRIPTADAS

NO se usa, y ademas habría que cambiar para CUENTAIBAN y CUENTANOIBAN
------------------------------------------------------------------------------*/
function insertarSocioEncriptandoCCCEX($resulValidar)//se pasa el array :$resulValidar['datosFormSocio']
{
 //echo "<br><br>1 insertarSocio: resulValidar:"; print_r($resulValidar);//
 
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	$conexionUsuariosDB=conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);		
	if ($conexionUsuariosDB['codError']!=="00000")
	{ $resulInserSocio=$conexionUsuariosDB;
	}
	else
	{$tablasBusqueda='SOCIO';
		$camposBuscados='CODSOCIO';
  require_once './modelos/libs/buscarCodMax.php';
	 $resulBuscarCodMax=buscarCodMax($tablasBusqueda,$camposBuscados,$conexionUsuariosDB['conexionLink']);
		
  //echo "<br><br>2 insertarSocio:resulBuscarCodMax:"; print_r($resulBuscarCodMax); 
		
		if ($resulBuscarCodMax['codError']!=='00000')
		{ $resulInserSocio=$resulBuscarCodMax;
		}
		else 
		{$arrValoresInser['CODSOCIO']=$resulBuscarCodMax['valorCampo'];
			$arrValoresInser['CODUSER']=$resulValidar['CODUSER']['valorCampo'];
			$arrValoresInser['CODAGRUPACION']=$resulValidar['CODAGRUPACION']['valorCampo'];
			$arrValoresInser['CODCUOTA']=$resulValidar['CODCUOTA']['valorCampo'];
			$arrValoresInser['IMPORTECUOTAANIOSOCIO']=$resulValidar['IMPORTECUOTAANIOSOCIO']['valorCampo'];	
	
			$arrValoresInser['FECHAALTA']=date('Y-m-d');//mysql:(CURRENT_DATE());
			$arrValoresInser['FECHABAJA']='0000-00-00';			

   $arrValoresInser['MODOINGRESO']=$resulValidar['MODOINGRESO']['valorCampo'];
		
			if (isset($resulValidar['CODENTIDAD']['valorCampo']) && !empty ($resulValidar['CODENTIDAD']['valorCampo']))
			{	$arrValoresInser['CODENTIDAD'] = $resulValidar['CODENTIDAD']['valorCampo'];
			  $arrValoresInser['CODSUCURSAL'] = $resulValidar['CODSUCURSAL']['valorCampo'];
			  $arrValoresInser['DC'] = $resulValidar['DC']['valorCampo'];
				 //$arrValoresInser['NUMCUENTA'] = $resulValidar['NUMCUENTA']['valorCampo'];
					
					require_once "../../usuariosLibs/encriptar/encriptacionBase64.php";	
     $arrValoresInser['NUMCUENTA'] = encriptarBase64($resulValidar['NUMCUENTA']['valorCampo']);
		   $arrValoresInser['CCEXTRANJERA'] = NULL;	
			}
			elseif (isset($resulValidar['CCEXTRANJERA']['valorCampo'])&& !empty($resulValidar['CCEXTRANJERA']['valorCampo']))
			{
			  require_once "../../usuariosLibs/encriptar/encriptacionBase64.php";
					$arrValoresInser['CCEXTRANJERA'] = encriptarBase64($resulValidar['CCEXTRANJERA']['valorCampo']);
					$arrValoresInser['CODENTIDAD'] = NULL;		
					$arrValoresInser['CODSUCURSAL'] = NULL;		
					$arrValoresInser['DC'] = NULL;		
					$arrValoresInser['NUMCUENTA'] = NULL;						
			}
			else
			{ $arrValoresInser['CCEXTRANJERA'] = NULL;
					$arrValoresInser['CODENTIDAD'] = NULL;		
					$arrValoresInser['CODSUCURSAL'] = NULL;		
					$arrValoresInser['DC'] = NULL;		
					$arrValoresInser['NUMCUENTA'] = NULL;		
			}
   //echo "<br><br>3 insertarSocio:arrValoresInser:"; print_r($arrValoresInser); 

	  $resulInserSocio=insertarUnaFila('SOCIO',$arrValoresInser,$conexionUsuariosDB['conexionLink']);
			if ($resulInserSocio)
			{	//$resulInserSocio['valorCampo']=$arrValoresInser['CODSOCIO'];
			  $resulInserSocio['CODSOCIO']=$arrValoresInser['CODSOCIO'];
			}
		}
	}	
 //echo "<br><br>4 insertarSocio:resulInserSocio:"; print_r($resulInserSocio); 
 return $resulInserSocio;
}
//----------------------------- Fin insertarSocioEncriptandoCCCEX ---------------

/*----------------------------- Inicio insertarCuotaAnioSocio -------------------
En esta función se conecta a la BBDD, busca datos en la tabla         
tabla IMPORTEDESCUOTAANIO, valida que la cantidad de la cuota elegida por el 
socio sea igual o superior a la correpondiente couta para ese tipo (acaso no sea 
necesario por ya estar validado previamente) y en caso de que sea válida 
inserta una fila CUOTAANIOSOCIO con los datos de CuotaAnioSocio 
						  
RECIBE: un array "$resulValidar" con los campos de los formularios ya validados 
y $conexionLinkDB.
DEVUELVE: un array con los controles de errores, 

LLAMADA: modeloSocios:altaSociosConfirmada(),actualizarDatosSocio()
modeloPresCoord():mAltaSocioPorGestor(), actualizarDatosSocioPorCoord()
modeloTesorero:actualizarIngresoCuotaAnio(),actualizaCuotaCCSocioTes()
LLAMA: require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
configMySQL.php:conexionDB(,)modeloMysSQL.php:buscarEnTablas(),insertarUnaFila()

OBSERVACIONES: 
Corrige si es necesario para que la cuota elegida por el socio sea siempre igual
o superior a la de cuota de EL para ese tipo de cuota
Utilizar "PDOStatement::bindParamValue, es necesario aquí para buscarEnTablas() 
pero no se requiere modificaciones para PDO en la función "insertarUnaFila()"
ya incluye la transformación interna de los datos recibidos "$campoCondiciones" 
y "$arrayDatosAct" para obtener "bindParamValue"	
Probado PHP: 7.3.21
------------------------------------------------------------------------------*/
function insertarCuotaAnioSocio($resulValidar,$conexionLinkDB)//recibe $resulValidar['datosFormSocio']
{ 
 //echo "<br><br>1-1 modeloSocios.php:insertarCuotaAnioSocio:resulValidar: "; print_r($resulValidar);
	//echo '<br><br>1-2 modeloSocios.php:insertarCuotaAnioSocio:conexionLinkDB: ';var_dump($conexionLinkDB);
	
 $resInsertarCuotaAnioSocio['nomScript'] = 'modeloSocios.php';
 $resInsertarCuotaAnioSocio['nomFuncion'] = 'insertarCuotaAnioSocio';		
 $resInsertarCuotaAnioSocio['codError'] = '00000';
 $resInsertarCuotaAnioSocio['errorMensaje'] = '';	
 
	if (!isset($conexionLinkDB) || empty($conexionLinkDB) || $conexionLinkDB == NULL) 
	{ 	
   require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
			require_once "./modelos/BBDD/MySQL/conexionMySQL.php";			
			$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);		
		 
			//echo "<br><br>2-1 modeloSocios.php:insertarCuotaAnioSocio:conexionDB: ";var_dump($conexionDB);				
 }		
	else
	{ $conexionDB['codError'] = "00000";
		 $conexionDB['conexionLink'] = $conexionLinkDB;  			
	 	//echo "<br><br>2-2 modeloSocios.php:insertarCuotaAnioSocio:conexionDB: ";var_dump($conexionDB);	 
	}	
	
	if ($conexionDB['codError'] !== "00000")
	{ $resInsertarCuotaAnioSocio['codError'] = $conexionDB['codError'];
	  $resInsertarCuotaAnioSocio['errorMensaje'] = $conexionDB['errorMensaje'];
	}
	else
	{	//echo '<br><br>2-3 modeloSocios.php:insertarCuotaAnioSocio:conexionDB: ';var_dump($conexionDB);	
			
			$tablasBusqueda = 'IMPORTEDESCUOTAANIO';
			$camposBuscados = 'IMPORTECUOTAANIOEL,NOMBRECUOTA';
																												
			$cadCondicionesBuscar = " WHERE IMPORTEDESCUOTAANIO.CODCUOTA = :CODCUOTA"." AND IMPORTEDESCUOTAANIO.ANIOCUOTA = :ANIOCUOTA";	

			$arrBind = array(':CODCUOTA' => $resulValidar['CODCUOTA']['valorCampo'], ':ANIOCUOTA' => $resulValidar['ANIOCUOTA']['valorCampo']);					
	
  	$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";
		
		 $resImporteCuotaAnio = buscarCadSql ($cadSql,$conexionDB['conexionLink'],$arrBind);			
			
   //echo "<br><br>3 modeloSocios.php:insertarCuotaAnioSocio:resImporteCuotaAnio: "; print_r($resImporteCuotaAnio);							
			
			if ($resImporteCuotaAnio['codError'] !== '00000')
			{ $resInsertarCuotaAnioSocio['codError'] = $resImporteCuotaAnio['codError'];
			  $resInsertarCuotaAnioSocio['errorMensaje'] = $resImporteCuotaAnio['errorMensaje'];
					$resInsertarCuotaAnioSocio['nomScript'] = $resImporteCuotaAnio['nomScript'];//se van a repetir
					$resInsertarCuotaAnioSocio['nomFuncion'] = $resImporteCuotaAnio['nomFuncion'];//se van a repetir
			}
			elseif ($resImporteCuotaAnio['numFilas'] == 0)
			{$resInsertarCuotaAnioSocio['codError'] = '80001'; //no encontrado
				$resInsertarCuotaAnioSocio['errorMensaje'] = "No asignada cuota";
			}
			else //$resDatosCuotaSocio['codError']=='00000'
			{	
				$arrValoresInser['ANIOCUOTA'] =	$resulValidar['ANIOCUOTA']['valorCampo'];				  
		  $arrValoresInser['CODSOCIO']  = $resulValidar['CODSOCIO']['valorCampo'];	
				$arrValoresInser['CODCUOTA']  = $resulValidar['CODCUOTA']['valorCampo'];
				$arrValoresInser['CODAGRUPACION'] = $resulValidar['CODAGRUPACION']['valorCampo'];				
				$arrValoresInser['ESTADOCUOTA']  = $resulValidar['ESTADOCUOTA']['valorCampo'];
				$arrValoresInser['MODOINGRESO']  = $resulValidar['MODOINGRESO']['valorCampo'];
				
				if ( isset($resulValidar['OBSERVACIONES']['valorCampo']) && !empty($resulValidar['OBSERVACIONES']['valorCampo']))
				{
					$arrValoresInser['OBSERVACIONES'] =  $resulValidar['OBSERVACIONES']['valorCampo'];
				}	
		
    $arrValoresInser['ORDENARCOBROBANCO']  = $resulValidar['ORDENARCOBROBANCO']['valorCampo'];				
		
				$arrValoresInser['IMPORTECUOTAANIOEL'] = $resImporteCuotaAnio['resultadoFilas'][0]['IMPORTECUOTAANIOEL'];
				
				if (isset($resImporteCuotaAnio['resultadoFilas'][0]['NOMBRECUOTA']) && !empty($resImporteCuotaAnio['resultadoFilas'][0]['NOMBRECUOTA']))
				{$arrValoresInser['NOMBRECUOTA']	= $resImporteCuotaAnio['resultadoFilas'][0]['NOMBRECUOTA'];
			 }
			
		  //Para asegurarse de que la cuota elegida por el socio sea siempre igual o superior a la de cuota de EL 
				//para ese tipo de cuota, siguiente if: (acaso ya esté controlado en la función previa de llamada)
	
				if ( $resulValidar['IMPORTECUOTAANIOSOCIO']['valorCampo'] < $arrValoresInser['IMPORTECUOTAANIOEL'])
				{
					$arrValoresInser['IMPORTECUOTAANIOSOCIO'] = $arrValoresInser['IMPORTECUOTAANIOEL'];
				}
				else
				{
					$arrValoresInser['IMPORTECUOTAANIOSOCIO'] = $resulValidar['IMPORTECUOTAANIOSOCIO']['valorCampo'];
	 		}
    //echo "<br><br>4 modeloSocios.php:insertarCuotaAnioSocio:arrValoresInser: "; print_r($arrValoresInser);
				    					
		  $resInsertarCuotaAnioSocio = insertarUnaFila('CUOTAANIOSOCIO',$arrValoresInser,$conexionDB['conexionLink']);	
			}	
	}	
 //echo "<br><br>4 modeloSocios.php:insertarCuotaAnioSocio:resInsertarCuotaAnioSocio: "; print_r($resInsertarCuotaAnioSocio);
	
 return $resInsertarCuotaAnioSocio;
}
/*----------------------------- Fin insertarCuotaAnioSocio --------------------*/

/*----------------------------- Inicio actualizarSocioConfirmar -----------------
Actualiza la tabla SOCIOSCONFIRMAR después CONFIRMADO, ANULADO o PLAZO-VENCIDO

RECIBE: un array "$resulValidar" con los campos de los formularios ya validados 
y $conexionLinkDB.
DEVUELVE: un array con los controles de errores, 

LLAMADA: modeloSocios:altaSociosConfirmada(),anularSocioPendienteConfirmar()
         cPresidente.php:reenviarEmailConfirmarSocioAltaGestor() 
LLAMA:  require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
configMySQL.php:conexionDB(,)modeloMysSQL.php:actualizarTabla()

OBSERVACIONES: 
No se requiere modificaciones para PDO en la función actualizarTabla(), 
lo gestiona dentro de ella con los par. que reciben									
--------------------------------------------------------------------------------*/ 
function actualizarSocioConfirmar($tablaActualizar,$codUser,$arrayDatos)     
{
	//echo '<br><br>0 modeloSocios.php:resActualizarSocioConfirmar:arrayDatosAct:';print_r($arrayDatos);
 
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	require_once "./modelos/BBDD/MySQL/conexionMySQL.php";
	
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);	
	
	if ($conexionDB['codError'] !== "00000")
	{ $resActualizarSocioConfirmar = $conexionDB;
	}
	else
	{	
	 $arrayCondiciones['CODUSER']['valorCampo'] = $codUser;
	 $arrayCondiciones['CODUSER']['operador'] = '=';
	 $arrayCondiciones['CODUSER']['opUnir'] = ' ';
			
		foreach ($arrayDatos as $indice => $contenido)                         
	 {      
	    $arrayDatosAct[$indice] = $contenido['valorCampo']; 
	 }		
	 //echo '<br><br>1 modeloSocios.php:resActualizarSocioConfirmar:arrayDatosAct:';print_r($arrayDatosAct);

		$resActualizarSocioConfirmar = actualizarTabla($tablaActualizar,$arrayCondiciones,$arrayDatosAct,$conexionDB['conexionLink']);																				
		//echo '<br><br>2 modeloSocios.php:resActualizarSocioConfirmar:';print_r($resActualizarSocioConfirmar);
	}
	return $resActualizarSocioConfirmar;
 } 
/*---------------------------------- Fin actualizarSocioConfirmar --------------*/

/*----------------------------- Inicio eliminarSocioConfirmar ------------------- 
Se borra la fila de la tabla 'SOCIOSCONFIRMAR' correspondientes a unA FILA de un
alta pendiente de confirmar por un socio.
 
LLAMADA: modeloPresCoord:altaSocioPendienteConfirmadaPorGestor(), 
OJO: NO se llama desde modeloSocios.php:altaSociosConfirmada()
LLAMA: modeloMysSQL.php:borrarFilas() 

OBSERVACIONES: Solo se produce esta situación, cuando es el gestor el que 
confirma el alta en lugar de confirmala el socio.
Se elimina fisicamente de SOCIOSCONFIRMAR para que no salga duplicado en
mostrar pendientes, etc. y casi toda la información para estadísticas, 
ya que también estaría en CONFIRMAREMAILALTAGESTOR y en MIEMBRO, ...	

Utiliza "PDOStatement::bindParamValue,

NOTA: ACASO DEBIERA ESTAR EN modeloPresCoord.php
------------------------------------------------------------------------------*/ 
function eliminarSocioConfirmar($tabla,$codUser,$conexionDB)     
{//echo '<br><br>0 modeloSocios.php:eliminarSocioConfirmar:codUser: ';print_r($codUser);
	
	$cadenaCondiciones = "CODUSER = :codUser"; 
	
	$arrBind = array(':codUser' => $codUser);	
 
	$resEliminarSocioConfirmar = borrarFilas($tabla,$cadenaCondiciones,$conexionDB,$arrBind); 
	
	//echo '<br><br>1 modeloSocios.php:eliminarSocioConfirmar:resEliminarSocioConfirmar: ';print_r($resEliminarSocioConfirmar);
	
	return $resEliminarSocioConfirmar;
 } 
/*----------------------------- Fin eliminarSocioConfirmar ---------------------*/

/*----------------------------- Inicio actualizSocio-NO-Encriptar ---------------
En esta función se actualiza una fila con los datos del socio en la tabla 'SOCIO'
a partir del  CODUSER  y un array con datos						  
RECIBE: un array con los campos de los datos a actualizar ya validados y el 
        campo $camposCondiciones con condiciones CODUSER,$conexionLinkDB
DEVUELVE: un array con los controles de errores, 

LLAMADA: modeloSocios.php:actualizarSocio(), 
modeloTesorero.php:actualizaCuotaCCSocioTes(), 
LLAMA: modeloMysSQL.php:actualizarTabla()

OBSERVACIONES: 
Ya incluye IBAN,  Sin Encriptar cuentas bancos  
Igual que modeloTesorero:actualizSocioPorCODSOCIO, excepto que la condición aquí 
es por CODUSER en lugar de por CODSOCIO
2020-03-27: No se requiere modificaciones para PDO en la función actualizarTabla(), 
lo gestiona dentro de ella con los par. que reciben			
------------------------------------------------------------------------------*/
function actualizSocio($tablaAct,$camposCondiciones,$arrayDatosAct,$conexionLinkDB)     
{//echo "<br><br>1 modeloSocios:actualizSocio:arrayDatosAct:";print_r($arrayDatosAct);
 
	$arrayCondiciones['CODUSER']['valorCampo'] = $camposCondiciones;
 $arrayCondiciones['CODUSER']['operador'] = '=';
 $arrayCondiciones['CODUSER']['opUnir'] = ' ';  

	foreach ($arrayDatosAct as $indice => $contenido)                         
 {      
   $arrayDatos[$indice] = $contenido['valorCampo']; 
 }
			
	$resActualizarSocio = actualizarTabla($tablaAct,$arrayCondiciones,$arrayDatos,$conexionLinkDB); 																					
	
	//echo '<br><br>2 modeloSocios:actualizSocio:resActualizarSocio:';print_r($resActualizarSocio);	
	
	return $resActualizarSocio;
 } 
/*----------------------------- Fin actualizSocio-NO-Encriptar -----------------*/

/*----------------------------- Inicio actualizSocio-SI-ENCRIPTACCYCCEX ---------
Descripción: Actualiza la tabla "SOCIO", Encriptando la c. banco NUMCUENTA o CCEXTRANJERA
LLAMADA: desde:modeloSocios.php:actualizarDatosSocio(), modeloTesorero.php:actualizaCuotaCCSocioTes(),
LLAMA: usuariosLibs/encriptar/encriptacionBase64.php";	
       modeloUsuarios.php:actualizarTabla()
OBSERVACIONES: Encripta la c. banco NUMCUENTA o CCEXTRANJERA	

Ya no se utiliza (ademas ahora sería con IBAN)					
-------------------------------------------------------------------------------------*/       
function actualizSocio_SI_ENCRIPTACCYCCEX($tablaAct,$camposCondiciones,$arrayDatosAct)     
{//echo "<br><br>1-1 modeloSocios:actualizSocio:arrayDatosAct:";print_r($arrayDatosAct);
 $arrayCondiciones['CODUSER']['valorCampo']= $camposCondiciones;
 $arrayCondiciones['CODUSER']['operador']= '=';
 $arrayCondiciones['CODUSER']['opUnir']= ' ';  

	foreach ($arrayDatosAct as $indice => $contenido)                         
 {      
   $arrayDatos[$indice] = $contenido['valorCampo']; 
 }
	//cuenta extranjera se trata en el bucle, pero la cuenta española esta compuesta
	//echo "<br><br>1-2 modeloSocios:actualizSocio:arrayDatos:";print_r($arrayDatos);
	unset($arrayDatos['ctaBanco']);
	
	//echo "<br><br>1-3 modeloSocios:actualizSocio:arrayDatos:";print_r($arrayDatos);
	
	//Si existe 	['NUMCUENTA'] se encripta
	if(isset($arrayDatosAct['ctaBanco']['NUMCUENTA']['valorCampo'])	&& 
	   !empty($arrayDatosAct['ctaBanco']['NUMCUENTA']['valorCampo'])
			)
	{
 	$arrayDatos['CODENTIDAD'] = $arrayDatosAct['ctaBanco']['CODENTIDAD']['valorCampo'];
	 $arrayDatos['CODSUCURSAL'] = $arrayDatosAct['ctaBanco']['CODSUCURSAL']['valorCampo'];
 	$arrayDatos['DC'] = $arrayDatosAct['ctaBanco']['DC']['valorCampo'];
		
 	//$arrayDatos['NUMCUENTA'] = $arrayDatosAct['ctaBanco']['NUMCUENTA']['valorCampo'];

	 require_once "../../usuariosLibs/encriptar/encriptacionBase64.php";	
  $arrayDatos['NUMCUENTA'] = encriptarBase64($arrayDatosAct['ctaBanco']['NUMCUENTA']['valorCampo']);
		$arrayDatos['CCEXTRANJERA'] = NULL;	
	}
	//Si existe 	['CCEXTRANJERA'] se encripta
	elseif(isset($arrayDatosAct['CCEXTRANJERA']['valorCampo'])	&& 
	       !empty($arrayDatosAct['CCEXTRANJERA']['valorCampo'])
							)
 {require_once "../../usuariosLibs/encriptar/encriptacionBase64.php";
		$arrayDatos['CCEXTRANJERA'] = encriptarBase64($arrayDatosAct['CCEXTRANJERA']['valorCampo']);
		$arrayDatos['CODENTIDAD'] = NULL;		
		$arrayDatos['CODSUCURSAL'] = NULL;		
		$arrayDatos['DC'] = NULL;		
		$arrayDatos['NUMCUENTA'] = NULL;		
	}
	else
	{$arrayDatos['CCEXTRANJERA'] = NULL;
		$arrayDatos['CODENTIDAD'] = NULL;		
		$arrayDatos['CODSUCURSAL'] = NULL;		
		$arrayDatos['DC'] = NULL;		
		$arrayDatos['NUMCUENTA'] = NULL;		
	}
	
 //echo '<br><br>2 modeloSocios:actualizSocio:arrayDatos';print_r($arrayDatos);	
			
	$resActualizarSocio = actualizarTabla($tablaAct,$arrayCondiciones,$arrayDatos); 																					
	//echo '<br><br>3 modeloSocios:actualizSocio:resActualizarSocio';print_r($resActualizarSocio);	
	return $resActualizarSocio;
 } 
/*----------------------------- Fin actualizSocio-SI-ENCRIPTACCYCCEX -----------*/

/*----------------------------- Inicio actualizCuotaAnioSocio -------------------
A partir de los parámetro recibidos, forma el array de condiciones de CODSOCIO y
ANIOCUOTA la función actualizarTabla_ParamPosicion() (UPDATE)
 
RECIBE: $tablaAct = CUOTAANIOSOCIO, un array $arrayDatosAct con los campos y 
        los datos a actualizar ya  validados y el $campoCondiciones con 
								condiciones CODSOCIO, y $conexionLinkDB
DEVUELVE: un array con los controles de errores, 

LLAMA: modeloMySQL.php:Socios.php:actualizarTabla_ParamPosicion(), en esta 
función se utilizán consultas mediante parámetros de posición (?) que permite 
repeticiones de nombres, mientras que en función modeloMySQL.php:actualizarTabla()
con parámetros tipo $arrBindValues([:APE2]=>cosgaya) no permite repeticiones de 
nombres. Daría lugar a algo como:
UPDATE CUOTAANIOSOCIO SET ANIOCUOTA = ?,CODCUOTA = ?,IMPORTECUOTAANIOSOCIO = ?
WHERE ANIOCUOTA = ? AND CODSOCIO = ? 
y $arrPosicionValues([2020],['General'],[50.20],[2020],[83])

LLAMADA: modeloSocios.php:actualizarDatosSocio(),eliminarDatosSocios()
modeloPresCoord.php:bajaSocioFallecido:
modeloTesorero:actualizarIngresoCuotaAnio(),actualizaCuotaCCSocioTes()

OBSERVACIONES: 2020-03-27:
La función actualizarTabla_ParamPosicion() ejecuta una UPDATE en 
una tabla mediante PDO y prepare() sentencias y execute() y utiliza 
consultas mediante parámetros de posición (?) que permite repeticiones de 
nombres, mientras que en función modeloMySQL.php:actualizarTabla() con 
parámetros tipo $arrBindValues([:APE2]=>cosgaya) no permite repeticiones de 
nombres. 
No se requiere modificaciones para PDO en la función actualizarTabla(), 
lo gestiona dentro de ella con los par. que reciben
------------------------------------------------------------------------------*/
function actualizCuotaAnioSocio($tablaAct,$campoCondiciones,$arrayDatosAct,$conexionLinkDB) 
{
		//echo "<br><br>0-1 modeloSocios.php:actualizCuotaAnioSocio:campoCondiciones:";print_r($campoCondiciones);
		//echo "<br><br>0-2 modeloSocios.php:actualizCuotaAnioSocio:arrayDatosAct:";print_r($arrayDatosAct);
		
		$arrayCondiciones['CODSOCIO']['valorCampo'] = $campoCondiciones;//CODSOCIO
		$arrayCondiciones['CODSOCIO']['operador'] = '=';
		$arrayCondiciones['CODSOCIO']['opUnir'] = ''; 
		$arrayCondiciones['CODSOCIO']['opUnir'] = 'AND'; 
		
		$arrayCondiciones['ANIOCUOTA']['valorCampo'] = $arrayDatosAct['ANIOCUOTA']['valorCampo'];
		$arrayCondiciones['ANIOCUOTA']['operador'] = '=';
		$arrayCondiciones['ANIOCUOTA']['opUnir'] = ''; 
		
		//echo "<br><br>1 modeloSocios.php:actualizCuotaAnioSocio:campoCondiciones:";print_r($campoCondiciones);
		
		//unset($arrayDatosAct['ANIOCUOTA']);
		//unset sería necesario en esta consulta, si quisiera utilizar modeloMySQL.php:actualizarTabla() 
		// para evitar las repeticiones de parámetro [:ANIOCUOTA] con bindParamValues , 
		//suponiendo que el año no se cambía nunca???  
		
		foreach ($arrayDatosAct as $indice => $contenido)                         
		{ 
				if (isset($contenido) && $contenido !==NULL && $contenido !=='')
				{
						$arrayDatos[$indice] = $contenido['valorCampo'];
				}
		}	
		//echo '<br><br>2 modeloSocios.php:actualizCuotaAnioSocio:arrayDatos: ';print_r($arrayDatos);
					
		//$resActualizarCuotaAnioSocio = actualizarTabla($tablaAct,$arrayCondiciones,$arrayDatos,$conexionLinkDB);//sería necesario //unset($arrayDatosAct['ANIOCUOTA']);
		
		$resActualizarCuotaAnioSocio = actualizarTabla_ParamPosicion($tablaAct,$arrayCondiciones,$arrayDatos,$conexionLinkDB);//no es necesario //unset($arrayDatosAct['ANIOCUOTA']);
		
		//echo '<br><br>3 modeloSocios.php:actualizCuotaAnioSocio:resActualizarCuotaAnioSocio:';print_r($resActualizarCuotaAnioSocio);
		
		return $resActualizarCuotaAnioSocio;
 } 
/*----------------------------- Fin actualizCuotaAnioSocio ----------------------*/

/*--------------------- Inicio actSocioEliminar ----------------------------------
No es exactamente eliminar, consiste en poner a NULL los datos personales.
Al dar de baja a un socio, por el mismo o por gestor, se actualiza la fila 
correspondiente ['CODUSER'] = $codUser; en la tabla 'SOCIO', poniendo los 
siguientes valores:['CUENTAIBAN'] = NULL, Y ['CUENTANOIBAN'] = NULL, los demás 
que no son personales, se dejarán para estadísticas. 

RECIBE: $tablaAct='SOCIO', $codUser con los datos de ese usuario y $conexionLinkDB
DEVUELVE: $resActSocioEliminar con el resultado de la actualizacion y código error

LLAMADO: modeloSocios.php:eliminarDatosSocios(),
         modeloPresCoord.php:bajaSocioFallecido()
LLAMA: modeloMySQL.php:actualizarTabla()

OBSERVACIONES: 2020-03-26: 
No es necesario utilizar aquí "PDOStatement::bindParamValue para la función 
"actualizarTabla()" ya incluye la transformación para PDO de los datos recibidos
 de "$arrayDatosAct" y $"arrayCondiciones" para obtener los "bindParamValue"
------------------------------------------------------------------------------*/ 
function actSocioEliminar($tablaAct,$codUser,$conexionLinkDB)     
{
	//echo '<br><br>1 modelosSocios.php:codUser: ';print_r($codUser); 
	
 $arrayCondiciones['CODUSER']['valorCampo'] = $codUser;
 $arrayCondiciones['CODUSER']['operador'] = '=';
 $arrayCondiciones['CODUSER']['opUnir'] = ' ';

	$arrayDatosAct['FECHABAJA'] = date('Y-m-d');//mysql:(CURRENT_DATE());
	/*$arrayDatosAct['CODENTIDAD']=NULL; Ya no se usan estos
	$arrayDatosAct['CODSUCURSAL']=NULL;
	$arrayDatosAct['DC']=NULL;
	$arrayDatosAct['NUMCUENTA']=NULL;
	$arrayDatosAct['CCEXTRANJERA']=NULL;
	*/
	$arrayDatosAct['CUENTAIBAN'] = NULL;
	$arrayDatosAct['CUENTANOIBAN'] = NULL;
	
	/* lo siguiente sería para que guardase los 12 primeros caracteres de la cuenta, 
	y serviría para tener alguna información estadística de los bancos utilizados 
	por los socios que se dieron de baja, pero sin saber la cuenta, para 
	preservar la privacidad
	
	if (isset($arrayDatosAct['CUENTAIBAN']) && !empty($arrayDatosAct['CUENTAIBAN']));
	{$arrayDatosAct['CUENTAIBAN']=substr($arrayDatosAct['CUENTAIBAN'],0,12);
	}
	else
	{$arrayDatosAct['CUENTAIBAN']=NULL;		
	}
	if (isset($arrayDatosAct['CUENTANOIBAN']) && !empty($arrayDatosAct['CUENTANOIBAN']));
	{$arrayDatosAct['CUENTANOIBAN']=substr($arrayDatosAct['CUENTANOIBAN'],0,12);
	}
	else
	{$arrayDatosAct['CUENTANOIBAN']=NULL;		
	}
	*/
	
	$resActSocioEliminar = actualizarTabla($tablaAct,$arrayCondiciones,$arrayDatosAct,$conexionLinkDB); 																					
	//echo '<br><br>2 modelosSocios.php:resActSocioEliminar: ';print_r($resActSocioEliminar);
	
	return $resActSocioEliminar;
 } 
/*----------------------------- Fin actSocioEliminar -----------------------------*/



/*-------------------------------Inicio cambioSimpSocio ---------------------------
Acción: No está implementada esta opción de simpatizantes: 
        En esta función se graba los datos cuando un simpatizante se hace 
        socio, controlando transacciones  con start transation y rollback.
Recibe: var de sesión, $resValidarCamposForm 
        $_SESSION['vs_NOMUSUARIO'], y $_SESSION['vs_CODDOMICILIO']
Devuelve: un array con los controles de errores, y mensajes 
Llamada: desde controladorSimpatizantes: simpatizanteAsocio(),
Llama: a varias funciones dentro de "modeloSocios.php" y otras en 
       modeloUsuarios.php 
OBSERVACIONES: Se graban los errores del sistema en ERRORES

**** NO SE USA ****+
------------------------------------------------------------------------------*/
function cambioSimpSocio($resValidarCamposForm)
{//echo "<br><br>modeloSocios:cambioSimpSocio0:resValidarCamposForm: ";print_r($resValidarCamposForm); 
	$resAltaSocioSimp['nomFuncion']="altaSocioSimp";
	$resAltaSocioSimp['nomScript']="modeloSocios.php";	
 $resAltaSocioSimp['codError']='00000';
 $resAltaSocioSimp['errorMensaje'] = '';
	
 $arrMensaje['textoCabecera']='Simpatizante se hace socio';
	//$arrMensaje['textoBoton']='Volver a menú';	
	//$arrMensaje['enlaceBoton']='./index.php?controlador=controladorLogin&amp;accion=mostrarMenuUserRol';
	
 //require "BBDD/MySQL/configMySQL.php";
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";			
	$conexionUsuariosDB=conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);	

	if ($conexionUsuariosDB['codError']!=="00000")
	{ $resAltaSocioSimp=$conexionUsuariosDB;
	  $arrMensaje['textoComentarios'].="Error del sistema al conectarse a la base de datos"; 
	}
	else //$conexionUsuariosDB['codError']=="00000"
	{ $iniTrans = "START TRANSACTION";
   $resIniTrans = mysql_query($iniTrans,$conexionUsuariosDB['conexionLink']);
		 if (!$resIniTrans)
	  { $resIniTrans['codError']='70501';   
			  $resIniTrans['errno']=mysql_errno(); 
	    $resIniTrans['errorMensaje']='Error en el sistema, no se ha podido iniciar la transación. '.
				           'Error mysql_query '.mysql_error();
	    $resIniTrans['numFilas']=0;	
			
			  $resAltaSocioSimp=$resIniTrans;	
			  //echo "<br><br>modeloSocios:cambioSimpSocio1-0:resAltaSocioSimp: ";print_r($resAltaSocioSimp);
	  }	
		 else //$resIniTrans
		 {
		 /*--- ACTIVAR SI SE QUIERE PERMITIR CAMBIAR EL USUARIO A LA VEZ QUE SE PASA DE SIMPA A SOCIO ----
    //$usuarioBuscado=$resValidarCamposForm['datosFormUsuario']['CODUSER']['valorCampo'];	 
	   //$usuarioBuscado=$_SESSION['vs_CODUSER'];
			 $reActUsuario=actualizUsuario('USUARIO',$_SESSION['vs_CODUSER'],$resValidarCamposForm['datosFormUsuario']);	    		
			 echo "<br><br>modeloSocios:actualizarDatosSocio1-1: reActUsuario: "; print_r($reActUsuario);//			
	 	 if ($reActUsuario['codError']!=='00000')			
		  {$resAltaSocioSimp=$reActUsuario;
	   }		
				else //$reActUsuario['codError']=='00000')
		  -----------------------------------------------------------------------------------------------*/				
				{
     $resValidarCamposForm['datosFormSocio']['CODUSER']['valorCampo']=$_SESSION['vs_CODUSER'];			
			  //echo "<br><br>modeloSocios:cambioSimpSocio1-2:SESSION['vs_NOMUSUARIO']:"; print_r($_SESSION['vs_NOMUSUARIO']);
		   
   		$reAltaSocio=insertarSocio($resValidarCamposForm['datosFormSocio']);
	    //echo "<br><br>modeloSocios:cambioSimpSocio1-2:reAltaSocio: "; print_r($reAltaSocio);//					
     //---------------------------------
     if ($reAltaSocio['codError']!=='00000')			
	    {$resAltaSocioSimp=$reAltaSocio;
	    }
		   else //$reAltaSocio['codError']=='00000'
	    {//--- Insertar cuota socio
						//$resInsertar['datosFormCuotaSocio']['ANIOCUOTA']['valorCampo']=date('Y');//ya está en $resInsertar['datosFormCuotaSocio']
	
	     $resValidarCamposForm['datosFormCuotaSocio']['CODSOCIO']['valorCampo']=	$reAltaSocio['CODSOCIO'];//devuelve siguiente a máx 
						$resValidarCamposForm['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo']='PENDIENTE-COBRO';		
						
						if ((isset($resValidarCamposForm['datosFormSocio']['ctaBanco']['DC']['valorCampo'] ) &&
						     $resValidarCamposForm['datosFormSocio']['ctaBanco']['DC']['valorCampo'] !==NULL &&
						     $resValidarCamposForm['datosFormSocio']['ctaBanco']['DC']['valorCampo'] !=='') || 
										(isset($resValidarCamposForm['datosFormSocio']['CCEXTRANJERA']['valorCampo']) && 
										 $resValidarCamposForm['datosFormSocio']['CCEXTRANJERA']['valorCampo'] !==NULL && 
											$resValidarCamposForm['datosFormSocio']['CCEXTRANJERA']['valorCampo'] !=='')) 
						{
							 $resValidarCamposForm['datosFormCuotaSocio']['MODOINGRESO']['valorCampo']='domiciliado';
						}
						else
						{
							 $resValidarCamposForm['datosFormCuotaSocio']['MODOINGRESO']['valorCampo']='';
						}							
							 $resValidarCamposForm['datosFormCuotaSocio']['CODCUOTA']['valorCampo']=
							  	$resValidarCamposForm['datosFormSocio']['CODCUOTA']['valorCampo'];
  					 $resValidarCamposForm['datosFormCuotaSocio']['CODAGRUPACION']['valorCampo']=
							  $resValidarCamposForm['datosFormSocio']['CODAGRUPACION']['valorCampo'];												
						//echo "<br><br>1-5-2 altaSocios:resInsertar['datosFormCuotaSocio']:";print_r($resInsertar['datosFormCuotaSocio']);
						
						//$resInsertarCuotaAnioSocio=insertarCuotaAnioSocio( $resInsertar['datosFormSocio']);//se necesita 'CODSOCIO'de SOCIO
      $resInsertarCuotaAnioSocio=insertarCuotaAnioSocio($resValidarCamposForm['datosFormCuotaSocio']);//se necesita 'CODSOCIO'
		    //echo "<br><br>1-5-3 altaSocios:resInsertarCuotaAnioSocio: ";print_r($resInsertarCuotaAnioSocio);
		
	     if ($resInsertarCuotaAnioSocio['codError']!=='00000')			
	     {$resAltaSocioSimp=$resInsertarCuotaAnioSocio;
	     }
		    else //$resInsertarCuotaAnioSocio['codError']=='00000'
	     {
//---------------------
				   /*if ($resValidarCamposForm['datosFormDomicilio']['CODPAISDOM']['valorCampo'] == 'ES')		
						 {$resValidarCamposForm['datosFormDomicilio']['DIREXTRANJERO']['valorCampo'] = NULL;}
						 unset($resValidarCamposForm['datosFormDomicilio']['controlDuplicidad']);
						 */	
					  unset($resValidarCamposForm['datosFormMiembro']['REMAIL']);
						 //las dos siguientes están para no cambiar en el formulario "datosFormDomicilio-->datosFormMiembro"						
					  $resValidarCamposForm['datosFormMiembro']=array_merge($resValidarCamposForm['datosFormMiembro'],
																																																												$resValidarCamposForm['datosFormDomicilio']);
					 	unset($resValidarCamposForm['datosFormDomicilio']); 		  						
//---------------------						
						 //echo "<br><br>modeloSocios:cambioSimpSocio1-3: resValidarCamposForm['datosFormMiembro']: ";
					  //print_r($resValidarCamposForm['datosFormMiembro']);
						 $resValidarCamposForm['datosFormMiembro']['TIPOMIEMBRO']['valorCampo'] = "socio";
					  $reActMiembro=actualizMiembro('MIEMBRO',$_SESSION['vs_CODUSER'],$resValidarCamposForm['datosFormMiembro']); 
				    
					  //echo "<br><br>modeloSocios:cambioSimpSocio1-3:reActMiembro:"; print_r($reActMiembro);
										
					  if ($reActMiembro['codError']!=='00000')			
		   	 {$resAltaSocioSimp=$reActMiembro;
				   }	
						 else //$reActMiembro['codError']=='00000')
						 {$resActUsuarioRol=actualizarUsuarioRol('USUARIOTIENEROL',$_SESSION['vs_CODUSER'],"2","1");//rol '2'->'1'
				    //echo "<br><br>modeloSocios:cambioSimpSocio:actualizarUsuarioRol:";print_r(resActUsuarioRol);
				
		      if ($resActUsuarioRol['codError']!=='00000')			
		      {$resAltaSocioSimp=$resInsertarUsuarioRol;
		      }					
						  else //$resActUsuarioRol['codError']=='00000'
						  {$reBajaSimp=actSimpEliminar('SIMPATIZANTE',$_SESSION['vs_CODUSER']);
	
					    if ($reBajaSimp['codError']!=='00000')			
	   	    {$resAltaSocioSimp=$reBajaSimp;
			      }	
							  else //$reBajaSimp['codError']=='00000')
							  {//echo "<br><br>modeloSocios:actualizarDatosSocio1-5:reActDomicilio: ";print_r($reActDomicilio);echo "<br>";	
									
							   $finTrans = "COMMIT";
					     $resFinTrans = mysql_query($finTrans,$conexionUsuariosDB['conexionLink']);
								  if (!$resFinTrans)
							   {$resFinTrans['codError']='70502';   
									  $resFinTrans['errno']=mysql_errno(); 
							    $resFinTrans['errorMensaje']='Error en el sistema, no se ha podido finalizar transación. '.
										           'Error mysql_query '.mysql_error();
							    $resFinTrans['numFilas']=0;	
									
									  $resAltaSocioSimp=$resFinTrans;	
									  //echo "<br><br>modeloSocios:cambioSimpSocio1-6-1:resAltaSocioSimp:";print_r($resAltaSocioSimp);
							   }
								  else
								  {//$_SESSION['vs_NOMUSUARIO']=$resValidarCamposForm['datosFormUsuario']['USUARIO']['valorCampo'];
	
								   $arrMensaje['textoComentarios']="Se ha efectuado el cambio de simpatizante a soci@ de Europa Laica".
									                                 " <br /><br /><br />". 
									                                 "Para entrar en la aplicación debes utilizar el mismo nombre de usuario 
																																										 y contraseña que ya utilizabas como simpatizante (puedes cambiarlos en el menú lateral)".
																									 //"<br /><br />"."Usuario:".$resValidarCamposForm['datosFormUsuario']['USUARIO']['valorCampo'].
																									 "<br /><br /><br />".
																									 "Si quieres ver tus datos o modificar los datos que ya habías introducido
																									  como simpatizante, elige la opción correspondiente en el menú lateral";
								  }
					    }	//$reBajaSimp['codError']=='00000')							
						  }	//$resActUsuarioRol['codError']=='00000'									
						 }	//$reActMiembro['codError']=='00000')		
     } //else $resInsertarCuotaAnioSocio['codError']=='00000'				
			 }//$reAltaSocio['codError']=='00000'
			}//$reActUsuario['codError']=='00000')																											
		}//$resIniTrans
	 //echo "<br><br>modeloSocios:cambioSimpSocio1-6-2:resAltaSocioSimp: ";print_r($resAltaSocioSimp);echo "<br>";
	
	 if ($resAltaSocioSimp['codError']!=='00000')
		{ //echo "<br><br>modeloSocios:cambioSimpSocio1-6-3:resAltaSocioSimp: ";print_r($resAltaSocioSimp);echo "<br>";
		
		  $deshacerTrans = "ROLLBACK"; //$sql = "ROLLBACK TO SAVEPOINT transEliminarSocio";
		  $resDeshacerTrans = mysql_query($deshacerTrans,$conexionUsuariosDB['conexionLink']);	
			 if (!$resDeshacerTrans)
		  { $resDeshacerTrans['codError']='70503';   
				  $resDeshacerTrans['errno']=mysql_errno(); 
		    $resDeshacerTrans['errorMensaje']='Error en el sistema, no se ha podido deshacer la transación. '.
					                                'Error mysql_query '.mysql_error();
		    $resDeshacerTrans['numFilas']=0;	
				
				  $resAltaSocioSimp=$resDeshacerTrans;
				  //echo "<br><br>modeloSocios:cambioSimpSocio1-6-4:resAltaSocioSimp: ";print_r($resAltaSocioSimp);echo "<br>";	
		  }	 
			 $arrMensaje['textoComentarios']="Error del sistema al pasar de simpatizante a soci@, vuelva a intentarlo pasado un tiempo ";
			
			require_once './modelos/modeloErrores.php'; //si es un error de conexión a la tabla error, insertar errores 
			$resInsertarErrores=insertarError($resAltaSocioSimp);	
				
			if ($resInsertarErrores['codError']!=='00000')
	  { $resAltaSocioSimp['errorMensaje'] .= $resInsertarErrores['errorMensaje'];
				$arrMensaje['textoComentarios'].="Error del sistema al tratar ERRORES, vuelva a intentarlo pasado un tiempo ";
			}		
		}//if ($resEliminarSocio['codError']!=='00000')	
	
		//echo "<br><br>modeloSocios:cambioSimpSocio1-7:resAltaSocioSimp: ";print_r($resAltaSocioSimp);echo "<br>";
	}//$conexionUsuariosDB['codError']=="00000"		
	$resAltaSocioSimp['arrMensaje']=$arrMensaje;	
 return 	$resAltaSocioSimp; 	
}
/*----------------------------- Fin cambioSimpSocio -----------------------------*/  
?>