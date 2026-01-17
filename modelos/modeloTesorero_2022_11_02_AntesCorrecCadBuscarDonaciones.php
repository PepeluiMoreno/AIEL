<?php
/*----------------------------------------------------------------------------------------------------------------------
FICHERO: modeloTesorero.php SOLO CAMBIADO COMETERIOS INCIO ARCHIVO
VERSION: PHP 7.3.21

DESCRIPCION: Este "Modelo" contiene funciones del tesorero, aunque el tesorero 
											  utiliza otras funciones existentes en modeloUsuarios y modeloSocios
LLAMADO: desde "controladorTesorero.php, pero también se llama desde algunas otras funciones para reutizar
						 
OBSERVACIONES: Necesita modeloUsuarios.php, ya que hay funciones compartidas 

----------------------------------------------------------------------------------------------------------------------*/

require_once "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	

require_once "BBDD/MySQL/modeloMySQL.php";
require_once './modelos/modeloUsuarios.php';
//-----------------------------------------------------------------------------


/*=== INICIO FUNCIONES PARA MOSTRAR, ACTUALIZAR PAGOS Y CUOTAS SOCIOS  ============================

==================================================================================================*/	

/*---------------------------- Inicio cadBuscarCuotasSocioCodUser --------------------------------
Descripción: Forma la cadena select sql para busca los datos de las 
             cuotas pagadas y no pagadas de un socio o todos los socios de una 
             determinada agrupación territorial, incluidas las bajas
													
Llamada: cTesorero.php:mostrarIngresosCuotas(),para pasarla a la función
         "/modelos/libs/mPaginarLib.php"
OBSERVACIONES: actualmente recibe siempre $codAgrup='%' ya que el tesorero
               es único para todas las agrupaciones, pero se ha dejado la 
               opción para si pudiera haber tesoreros de cada agrupación.
															OJO esta función incluye a los que son bajs, aunque no hayan pagado
															
------ SIN UTILIZAR AÚN -----
--------------------------------------------------------------------------*/
function cadBuscarCuotasSocioCodUser($codUser,$codAreaCoordinacion='%',$codAgrup='%',$anioCuotas='%',$estadoSocio='%',$estadoCuota='%')
{//$estadoCuota='ABONADA';
 //$estadoCuota='PENDIENTE-COBRO';
	if ( !isset($codUser) || empty($codUser) || $codUser =='%' )
 { $condicionCodUser = '';		}
	else 
	{ $condicionCodUser = " AND USUARIO.CODUSER = '$codUser' ";	}

	if ( !isset($codAgrup) || empty($codAgrup) || $codAgrup =='%' )
 { $condicionAgrup = '';		}
	else 
	{ $condicionAgrup = " AND SOCIO.CODAGRUPACION = '$codAgrup' ";	}
	
	if(!isset($codAreaCoordinacion)||empty($codAreaCoordinacion)||$codAreaCoordinacion =='%' )
 {//$condicionAgrup = " AND AREACOORDINACIONINCLUYEAGRUPACION.CODAREACOORDINACION like '$codAgrup' ";	
	 $condicionAreaCoordinacion = "";		}
	else 
	{ $condicionAreaCoordinacion= " AND AREAGESTIONAGRUPACIONESCOORD.CODAREAGESTIONAGRUP='$codAreaCoordinacion' ";	}	
	
	if ( !isset($anioCuotas) || empty($anioCuotas) || $anioCuotas =='%' )
 { $condicionAnioCuotas = '';		}
	else 
	{ $condicionAnioCuotas = " AND CUOTAANIOSOCIO.ANIOCUOTA = '$anioCuotas' ";	}	

	if ( !isset($estadoSocio) || empty($estadoSocio) || $estadoSocio =='%' )
 { $condicionEstadoSocio = '';		}
	elseif($estadoSocio == 'alta')
	{ $condicionEstadoSocio = " AND SUBSTRING(USUARIO.ESTADO,'1',4) = '$estadoSocio' ";	}	
	else { $condicionEstadoSocio = " AND USUARIO.ESTADO = '$estadoSocio' ";	}//por si se quieren otras búsquedas		
	
	if ( !isset($estadoCuota) || empty($estadoCuota) || $estadoCuota =='%' )
 { $condicionEstadoCuota = '';		}
	else 
	{ $condicionEstadoCuota = " AND CUOTAANIOSOCIO.ESTADOCUOTA = '$estadoCuota' ";	}	


 //$condicionEstadoSocio = " AND USUARIO.ESTADO = 'baja' ";
 $tablasBusqueda="USUARIO,MIEMBRO,SOCIO,AGRUPACIONTERRITORIAL,CUOTAANIOSOCIO,AREAGESTIONAGRUPACIONESCOORD";	
	//ojo sin el disnct da resultados repetidos debido AND AREAGESTIONAGRUPACIONESCOORD.CODAGRUPACION=AGRUPACIONTERRITORIAL.CODAGRUPACION".
	$tablasBusqueda =
	"USUARIO,SOCIO,AGRUPACIONTERRITORIAL,CUOTAANIOSOCIO,AREAGESTIONAGRUPACIONESCOORD,MIEMBRO 
	 LEFT JOIN MIEMBROELIMINADO5ANIOS ON MIEMBRO.CODUSER =MIEMBROELIMINADO5ANIOS.CODUSER ";
	
	$camposBuscados=
	"DISTINCT 
			CUOTAANIOSOCIO.ANIOCUOTA,USUARIO.ESTADO, 

			/*UPPER(CONCAT(APE1,' ',IFNULL(APE2,''),', ',NOM)) as apeNom,*/																		
			UPPER(CONCAT(IFNULL(MIEMBRO.APE1,MIEMBROELIMINADO5ANIOS.APE1),' ',IFNULL(MIEMBRO.APE2,IFNULL(MIEMBROELIMINADO5ANIOS.APE2,'')),', ',IFNULL(MIEMBRO.NOM,MIEMBROELIMINADO5ANIOS.NOM) )) as apeNom,																		
			AGRUPACIONTERRITORIAL.NOMAGRUPACION as Agrupacion_Actual, SOCIO.CODAGRUPACION as Codigo_Agrup_Actual,
			CUOTAANIOSOCIO.IMPORTECUOTAANIOEL,CUOTAANIOSOCIO.IMPORTECUOTAANIOSOCIO,
			CUOTAANIOSOCIO.IMPORTECUOTAANIOPAGADA,CUOTAANIOSOCIO.IMPORTEGASTOSABONOCUOTA, 
			DATE_FORMAT(CUOTAANIOSOCIO.FECHAPAGO,'%Y-%m-%d') FECHAPAGO,
			DATE_FORMAT(CUOTAANIOSOCIO.FECHAANOTACION,'%Y-%m-%d') FECHAANOTACION,
			CUOTAANIOSOCIO.MODOINGRESO,CUOTAANIOSOCIO.ESTADOCUOTA,
			(CUOTAANIOSOCIO.IMPORTECUOTAANIOPAGADA - CUOTAANIOSOCIO.IMPORTECUOTAANIOEL) saldo, 
			CUOTAANIOSOCIO.CODAGRUPACION as CodAgrup_PagoCuota, CUOTAANIOSOCIO.OBSERVACIONES,

			SOCIO.CODENTIDAD,SOCIO.CODSUCURSAL,SOCIO.DC,SOCIO.NUMCUENTA,SOCIO.CCEXTRANJERA,

			MIEMBRO.CODPAISDOM,MIEMBRO.DIRECCION,UPPER(MIEMBRO.LOCALIDAD) as LOCALIDAD,MIEMBRO.NOMPROVINCIA,

			MIEMBRO.TIPOMIEMBRO,MIEMBRO.TELFIJOCASA,MIEMBRO.TELMOVIL,MIEMBRO.EMAIL,EMAILERROR,INFORMACIONEMAIL,INFORMACIONCARTAS,COLABORA,
															
			SOCIO.FECHAALTA,SOCIO.FECHABAJA,SOCIO.CODSOCIO,
			USUARIO.CODUSER,USUARIO.USUARIO";

  $cadCondicionesBuscar =
  " WHERE USUARIO.CODUSER=MIEMBRO.CODUSER  
				AND USUARIO.CODUSER=SOCIO.CODUSER   
				AND SOCIO.CODSOCIO=CUOTAANIOSOCIO.CODSOCIO   
				AND SOCIO.CODAGRUPACION=AREAGESTIONAGRUPACIONESCOORD.CODAGRUPACION  
				AND AREAGESTIONAGRUPACIONESCOORD.CODAGRUPACION=AGRUPACIONTERRITORIAL.CODAGRUPACION ".																										
				$condicionCodUser.$condicionAreaCoordinacion.$condicionAgrup.$condicionEstadoSocio.$condicionAnioCuotas.$condicionEstadoCuota.
				" ORDER BY ANIOCUOTA DESC, apeNom";
										
							
	$condicionCodUser.$cadBuscarCuotasSocios="SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";
		
	return $cadBuscarCuotasSocios;
}
//------------------------------ Fin cadBuscarCuotasSocioCodUser ---------------------------------

/*---------------------- Inicio cadBuscarCuotasSocios --------------------------------------------
Forma la cadena select sql para busca datos de los socios incluido las cuotas 
pagadas y no pagadas de todos los socios de una determinada agrupación 
territorial o todas según valores de $codAreaCoordinacion,$codAgrupacion,$anioCuotas,
el valor de $estadoSocio se podrán incluir las bajas. 

RECIBE: $codAreaCoordinacion,$codAgrupacion,$anioCuotas,$estadoSocio,$estadoCuota
DEVUELVE: array con cadena select ['cadSQL'] y ['arrBindValues']	
correspondiente a esa select.
													
LLAMADA: 
controladores\libs\cCoordinadorSociosApeNomPaginarInc.php	
controladores\libs\cPresidenteSociosApeNomPaginarInc.php		
controladores\libs\cTesoreroCuotasSociosApeNomPaginarInc.php
y creo que en otros	también
LLAMA: ninguna		
									
OBSERVACIONES: 2020-04-13: Se incluye $arrBindValues para PDO y PHP 7

NOTA: Se sustituye campos $camposBuscados:
"SOCIO.CODENTIDAD,SOCIO.CODSUCURSAL,SOCIO.DC,SOCIO.NUMCUENTA,SOCIO.CCEXTRANJERA,"
	por campos	"SOCIO.CUENTAIBAN,SOCIO.CUENTANOIBAN,", por lo que ya se pueden 
	eliminar esos campos antiguos de la tabla "SOCIO"
------------------------------------------------------------------------------*/
function cadBuscarCuotasSocios($codAreaCoordinacion,$codAgrupacion,$anioCuotas,$estadoSocio,$estadoCuota)
{
	//echo "<br><br>0 modeloTesorero.php:cadBuscarCuotasSocios:codAreaCoordinacion: "; print_r($codAreaCoordinacion);	
	
	//$estadoCuota ='ABONADA'; para pruebas
 //$estadoCuota ='PENDIENTE-COBRO';
	
 $arrBind = array();
	
 if (!isset($codAgrupacion) || empty($codAgrupacion) || $codAgrupacion =='%')
 { $condicionAgrup = '';	}
	else
	{ $condicionAgrup  = " AND SOCIO.CODAGRUPACION = :codAgrupacion ";//acaso no sea necesario el like aquí
   
			$arrBind[':codAgrupacion'] = $codAgrupacion;
 }		

	if(!isset($codAreaCoordinacion)||empty($codAreaCoordinacion)||$codAreaCoordinacion =='%')
 { $condicionAreaCoordinacion = "";		
 }
	else 
	{ $condicionAreaCoordinacion =" AND AREAGESTIONAGRUPACIONESCOORD.CODAREAGESTIONAGRUP = :codAreaCoordinacion ";
			
			$arrBind[':codAreaCoordinacion'] = $codAreaCoordinacion; 
 }

 if ( !isset($anioCuotas) || empty($anioCuotas) || $anioCuotas =='%')
 { $condicionAnioCuotas = '';		}
	else
	{ $condicionAnioCuotas = " AND CUOTAANIOSOCIO.ANIOCUOTA = :anioCuotas ";
			
			$arrBind[':anioCuotas'] = $anioCuotas; 
 }		

	if ( !isset($estadoSocio) || empty($estadoSocio) || $estadoSocio =='%' )
 { $condicionEstadoSocio = '';		}
	elseif($estadoSocio == 'alta')
	{ 
	  $condicionEstadoSocio = " AND SUBSTRING(USUARIO.ESTADO,'1',4) = :estadoSocio ";	
			
			$arrBind[':estadoSocio'] = $estadoSocio; 
	}	
	else //por si se quieren otras búsquedas		
	{ 
   $condicionEstadoSocio = " AND USUARIO.ESTADO = :estadoSocio ";	
			
			$arrBind[':estadoSocio'] = $estadoSocio; 
	}

	if ( !isset($estadoCuota) || empty($estadoCuota) || $estadoCuota =='%' )
 { $condicionEstadoCuota = '';		}
	else 
	{ 
   $condicionEstadoCuota = " AND CUOTAANIOSOCIO.ESTADOCUOTA = :estadoCuota ";	
			
			$arrBind[':estadoCuota'] = $estadoCuota;
 }	

 //$condicionEstadoSocio = " AND USUARIO.ESTADO = 'baja' ";//para prueba
	
 //$tablasBusqueda = "USUARIO,MIEMBRO,SOCIO,AGRUPACIONTERRITORIAL,CUOTAANIOSOCIO,AREAGESTIONAGRUPACIONESCOORD";	
	
	$tablasBusqueda = 
	"USUARIO,SOCIO,AGRUPACIONTERRITORIAL,CUOTAANIOSOCIO,AREAGESTIONAGRUPACIONESCOORD,MIEMBRO 
	  LEFT JOIN MIEMBROELIMINADO5ANIOS ON MIEMBRO.CODUSER = MIEMBROELIMINADO5ANIOS.CODUSER ";
	
	//ojo sin el distinct da resultados repetidos debido AND AREAGESTIONAGRUPACIONESCOORD.CODAGRUPACION=AGRUPACIONTERRITORIAL.CODAGRUPACION".
	$camposBuscados=
	"DISTINCT 
		CUOTAANIOSOCIO.ANIOCUOTA,USUARIO.ESTADO, 

		/*UPPER(CONCAT(APE1,' ',IFNULL(APE2,''),', ',NOM)) as apeNom,*/																		
		UPPER(CONCAT(IFNULL(MIEMBRO.APE1,MIEMBROELIMINADO5ANIOS.APE1),' ',IFNULL(MIEMBRO.APE2,IFNULL(MIEMBROELIMINADO5ANIOS.APE2,'')),', ',IFNULL(MIEMBRO.NOM,MIEMBROELIMINADO5ANIOS.NOM) )) as apeNom,																		
		AGRUPACIONTERRITORIAL.NOMAGRUPACION as Agrupacion_Actual, SOCIO.CODAGRUPACION as Codigo_Agrup_Actual,
		CUOTAANIOSOCIO.IMPORTECUOTAANIOEL,CUOTAANIOSOCIO.IMPORTECUOTAANIOSOCIO,
		CUOTAANIOSOCIO.IMPORTECUOTAANIOPAGADA,CUOTAANIOSOCIO.IMPORTEGASTOSABONOCUOTA, 
		DATE_FORMAT(CUOTAANIOSOCIO.FECHAPAGO,'%Y-%m-%d') FECHAPAGO,
		DATE_FORMAT(CUOTAANIOSOCIO.FECHAANOTACION,'%Y-%m-%d') FECHAANOTACION,
		CUOTAANIOSOCIO.MODOINGRESO,CUOTAANIOSOCIO.ORDENARCOBROBANCO,CUOTAANIOSOCIO.ESTADOCUOTA,
		(CUOTAANIOSOCIO.IMPORTECUOTAANIOPAGADA - CUOTAANIOSOCIO.IMPORTECUOTAANIOEL) saldo, 
		CUOTAANIOSOCIO.CODAGRUPACION as CodAgrup_PagoCuota, CUOTAANIOSOCIO.OBSERVACIONES,

		/*SOCIO.CODENTIDAD,SOCIO.CODSUCURSAL,SOCIO.DC,SOCIO.NUMCUENTA,SOCIO.CCEXTRANJERA,*/
		SOCIO.CUENTAIBAN,SOCIO.CUENTANOIBAN,

		MIEMBRO.CODPAISDOM,MIEMBRO.DIRECCION,UPPER(MIEMBRO.LOCALIDAD) as LOCALIDAD,MIEMBRO.NOMPROVINCIA,

		MIEMBRO.TIPOMIEMBRO,MIEMBRO.TELFIJOCASA,MIEMBRO.TELMOVIL,MIEMBRO.EMAIL,EMAILERROR,INFORMACIONEMAIL,INFORMACIONCARTAS,COLABORA,
													
		SOCIO.FECHAALTA,SOCIO.FECHABAJA,SOCIO.CODSOCIO,
		USUARIO.CODUSER,USUARIO.USUARIO";

 $cadCondicionesBuscar =
	 " WHERE USUARIO.CODUSER = MIEMBRO.CODUSER 
					AND USUARIO.CODUSER = SOCIO.CODUSER 
					AND SOCIO.CODSOCIO = CUOTAANIOSOCIO.CODSOCIO	
					AND SOCIO.CODAGRUPACION = AREAGESTIONAGRUPACIONESCOORD.CODAGRUPACION
					AND AREAGESTIONAGRUPACIONESCOORD.CODAGRUPACION = AGRUPACIONTERRITORIAL.CODAGRUPACION ".				
					$condicionAreaCoordinacion.$condicionAgrup.$condicionEstadoSocio.$condicionAnioCuotas.$condicionEstadoCuota.
					" ORDER BY ANIOCUOTA DESC, apeNom";
											
							
	$cadSelectCuotasSocios = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";	
																																														
	$arrBuscarCuotasSocios['cadSQL'] = $cadSelectCuotasSocios;

 $arrBuscarCuotasSocios['arrBindValues']	= $arrBind;
																																										
 //echo "<br><br>1 modeloTesorero.php:cadBuscarCuotasSocios:arrBuscarCuotasSocios: "; print_r($arrBuscarCuotasSocios);	
	
	return $arrBuscarCuotasSocios;
}
//------------------------------ Fin cadBuscarCuotasSocios ---------------------------------------

/*---------------------- Inicio cadBuscarCuotasSociosApeNom --------------------------------------
Forma la cadena select sql para busca los datos de las cuotas pagadas y no 
pagadas y otros datos del socio elegido por Ape1 y Ape2 y otros parámetros:
$codAreaCoordinacion,$codAgrupacion,$anioCuotas

RECIBE: $codAreaCoordinacion,$cadApe1,$cadApe2,$codAgrupacion,$anioCuotas
DEVUELVE: array "$arrBuscarCuotasSocios" cadena select ['cadSQL'] y ['arrBindValues']	
													
LLAMADA: 
controladores\libs\cPresCoordSociosApeNomPaginarInc.php
controladores\libs\cTesoreroCuotasSociosApeNomPaginarInc.php
y creo que en otros	también
LLAMA: eliminarAcentos.php:cambiarAcentosEspeciales()					
									
OBSERVACIONES: 
2020-08-27: PDO y PHP7
2020-04-13: En esta función se incluye $arrBindValues para PDO.

OJO: incluye a los que están de baja aunque no hayan pagado

NOTA: Se sustituye campos $camposBuscados:
"SOCIO.CODENTIDAD,SOCIO.CODSUCURSAL,SOCIO.DC,SOCIO.NUMCUENTA,SOCIO.CCEXTRANJERA,"
	por campos	"SOCIO.CUENTAIBAN,SOCIO.CUENTANOIBAN,", por lo que ya se pueden 
	eliminar esos campos antiguos de la tabla "SOCIO"
------------------------------------------------------------------------------*/
function cadBuscarCuotasSociosApeNom($codAreaCoordinacion,$cadApe1,$cadApe2,$codAgrupacion ='%',$anioCuotas ='%')
{
		//echo "<br><br>0 modeloTesorero.php:cadBuscarCuotasSociosApeNom:codAreaCoordinacion: "; print_r($codAreaCoordinacion);	
		
		require_once './modelos/libs/eliminarAcentos.php';
		
		$arrBind = array();
		
		//--- Inicio condiciones $cadApe1,$cadApe2,$codAreaCoordinacion,$codAgrupacion,$anioCuotas ---
		
		/*---------- Inicio APE1 y AP2 -------------------------------------------
  NOTA: Previamente desde las funciones de llamada controlo el caso de que sean a la vez: empty($cadApe1) y empty($cadApe2) 
		y en ese caso no permito ="%" cambio valores: $cadApe1='---******---' y $cadApe2='---******---' estos caracteres no están
		permitidos para los apellidos por lo que la select devolverá 0 filas en el caso de que los dos estén empty
		y envío "errorMensaje"="Al menos uno de los apellidos no puede estar vacío"
		-------------------------------------------------------------------------*/
		if ( !isset($cadApe1) || empty($cadApe1) || $cadApe1 == '%' )//OJO: !isset($cadApe1) || empty($cadApe1) considera = "%", pues se puede buscar solo por un APE2 
		{ 
		 $condicionApe1 = '';			
			//echo "<br><br>2-1 modeloTesorero.php:cadBuscarCuotasSociosApeNom:";
		}
		else
		{$cadApe1 = cambiarAcentosEspeciales($cadApe1);
	  
			//si el socio es baja, APE1 está disponible 5 años en la tabla MIEMBROELIMINADO5ANIOS para efectos de tesoreria y fiscales
			
			//$condicionApe1 =" AND (MIEMBRO.APE1 LIKE '$cadApe1' OR  MIEMBROELIMINADO5ANIOS.APE1 LIKE '$cadApe1') ";
			$condicionApe1 =" AND (MIEMBRO.APE1 LIKE :cadApe1Miembro OR  MIEMBROELIMINADO5ANIOS.APE1 LIKE :cadApe1ELIMINADO5ANIOS) ";//necesario el like aquí		
		
			$arrBind[':cadApe1Miembro'] = $cadApe1; 
			$arrBind[':cadApe1ELIMINADO5ANIOS'] = $cadApe1;
			
			//echo "<br><br>2-2 modeloTesorero.php:cadBuscarCuotasSociosApeNom:";
		}

		if ( !isset($cadApe2) || empty($cadApe2) || $cadApe2 == '%' )//OJO: !isset($cadApe2) || empty($cadApe2) considera = "%", pues se puede buscar solo por un APE1 
		{ $condicionApe2 = '';			
		  //echo "<br><br>3-1 modeloTesorero.php:cadBuscarCuotasSociosApeNom:";
		}
		else
		{$cadApe2 = cambiarAcentosEspeciales($cadApe2);
   
			//si el socio es baja, APE2 puede estar disponible 5 años en la tabla MIEMBROELIMINADO5ANIOS para efectos de tesoreria y fiscales
			//$condicionApe2 =" AND (MIEMBRO.APE2 LIKE '$cadApe2' OR  MIEMBROELIMINADO5ANIOS.APE2 LIKE '$cadApe2') ";
			$condicionApe2 =" AND (MIEMBRO.APE2 LIKE :cadApe2Miembro OR  MIEMBROELIMINADO5ANIOS.APE2 LIKE :cadApe2ELIMINADO5ANIOS) ";//necesario el like aquí
			
			$arrBind[':cadApe2Miembro'] = $cadApe2; 
			$arrBind[':cadApe2ELIMINADO5ANIOS'] = $cadApe2;
			//echo "<br><br>3-2 modeloTesorero.php:cadBuscarCuotasSociosApeNom:";
		}
  //---------- Fin APE1 y AP2 ------------------------------------------------------
		
		if (!isset($codAgrupacion) || empty($codAgrupacion) || $codAgrupacion =='%')
		{ $condicionAgrup = '';	}
		else
		{ //$condicionAgrup = " AND SOCIO.CODAGRUPACION LIKE '$codAgrupacion' ";   
				$condicionAgrup  = " AND SOCIO.CODAGRUPACION LIKE :codAgrupacion ";//acaso no sea necesario el like aquí
				
				$arrBind[':codAgrupacion'] = $codAgrupacion;
		}		
		
		if(!isset($codAreaCoordinacion)||empty($codAreaCoordinacion)||$codAreaCoordinacion =='%')
		{ $condicionAreaCoordinacion = "";		}
		else 
		{ //$condicionAreaCoordinacion =" AND AREAGESTIONAGRUPACIONESCOORD.CODAREAGESTIONAGRUP = '$codAreaCoordinacion' ";
				$condicionAreaCoordinacion=" AND AREAGESTIONAGRUPACIONESCOORD.CODAREAGESTIONAGRUP = :codAreaCoordinacion ";
				
				$arrBind[':codAreaCoordinacion'] = $codAreaCoordinacion; 
		}
				
		if ( !isset($anioCuotas) || empty($anioCuotas) || $anioCuotas =='%')
		{ $condicionAnioCuotas = '';	}
		else
		{ //$condicionAnioCuotas = " AND CUOTAANIOSOCIO.ANIOCUOTA = '$anioCuotas' ";
				$condicionAnioCuotas = " AND CUOTAANIOSOCIO.ANIOCUOTA = :anioCuotas ";
				
				$arrBind[':anioCuotas'] = $anioCuotas; 
		}		

  $cadCondicionesBuscar = 		
  " WHERE USUARIO.CODUSER = MIEMBRO.CODUSER 
					AND USUARIO.CODUSER = SOCIO.CODUSER 
					AND SOCIO.CODSOCIO = CUOTAANIOSOCIO.CODSOCIO 																																																																												
					AND SOCIO.CODAGRUPACION = AREAGESTIONAGRUPACIONESCOORD.CODAGRUPACION
					AND AREAGESTIONAGRUPACIONESCOORD.CODAGRUPACION = AGRUPACIONTERRITORIAL.CODAGRUPACION ".
					$condicionAreaCoordinacion.$condicionApe1.$condicionApe2.$condicionAgrup.$condicionAnioCuotas .																																												
					" ORDER BY ANIOCUOTA DESC, apeNom";
  
	 //----- Fin condiciones condiciones $cadApe1,$cadApe2,$codAreaCoordinacion,$codAgrupacion,$anioCuotas ---
		
		//$tablasBusqueda="USUARIO,MIEMBRO,SOCIO,AGRUPACIONTERRITORIAL,CUOTAANIOSOCIO,AREAGESTIONAGRUPACIONESCOORD";	
		
 	$tablasBusqueda = 	
	 "USUARIO,SOCIO,AGRUPACIONTERRITORIAL,CUOTAANIOSOCIO,AREAGESTIONAGRUPACIONESCOORD,MIEMBRO 	
	  LEFT JOIN MIEMBROELIMINADO5ANIOS ON MIEMBRO.CODUSER = MIEMBROELIMINADO5ANIOS.CODUSER ";
			
	 //ojo sin el distinct da resultados repetidos debido AND AREAGESTIONAGRUPACIONESCOORD.CODAGRUPACION=AGRUPACIONTERRITORIAL.CODAGRUPACION".
	 $camposBuscados =
	 "DISTINCT 
			CUOTAANIOSOCIO.ANIOCUOTA,																	
		
			UPPER(CONCAT(IFNULL(MIEMBRO.APE1,MIEMBROELIMINADO5ANIOS.APE1),' ',IFNULL(MIEMBRO.APE2,IFNULL(MIEMBROELIMINADO5ANIOS.APE2,'')),', ',IFNULL(MIEMBRO.NOM,MIEMBROELIMINADO5ANIOS.NOM))) as apeNom, 
																				
			CUOTAANIOSOCIO.IMPORTECUOTAANIOEL,CUOTAANIOSOCIO.IMPORTECUOTAANIOSOCIO,
			CUOTAANIOSOCIO.IMPORTECUOTAANIOPAGADA,CUOTAANIOSOCIO.IMPORTEGASTOSABONOCUOTA, 
			DATE_FORMAT(CUOTAANIOSOCIO.FECHAPAGO,'%Y-%m-%d') FECHAPAGO,
			DATE_FORMAT(CUOTAANIOSOCIO.FECHAANOTACION,'%Y-%m-%d') FECHAANOTACION,
			CUOTAANIOSOCIO.MODOINGRESO,CUOTAANIOSOCIO.ORDENARCOBROBANCO,CUOTAANIOSOCIO.ESTADOCUOTA,
			(CUOTAANIOSOCIO.IMPORTECUOTAANIOPAGADA - CUOTAANIOSOCIO.IMPORTECUOTAANIOEL) saldo, CUOTAANIOSOCIO.OBSERVACIONES,
			
			/*SOCIO.CODENTIDAD,SOCIO.CODSUCURSAL,SOCIO.DC,SOCIO.NUMCUENTA,SOCIO.CCEXTRANJERA,*/
			SOCIO.CUENTAIBAN,SOCIO.CUENTANOIBAN,

			SOCIO.CODSOCIO,SOCIO.CODAGRUPACION,SOCIO.FECHAALTA,SOCIO.FECHABAJA,

			MIEMBRO.CODPAISDOM,MIEMBRO.DIRECCION,UPPER(MIEMBRO.LOCALIDAD) as LOCALIDAD,MIEMBRO.NOMPROVINCIA,

			MIEMBRO.TIPOMIEMBRO,MIEMBRO.TELFIJOCASA,MIEMBRO.TELMOVIL,MIEMBRO.EMAIL,MIEMBRO.EMAILERROR,INFORMACIONEMAIL,INFORMACIONCARTAS,COLABORA,
			
			AGRUPACIONTERRITORIAL.NOMAGRUPACION as Agrupacion_Actual,
			
		USUARIO.ESTADO,USUARIO.CODUSER,USUARIO.USUARIO";

		
	 $cadSelectCuotasSocios = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";	
		

		$arrBuscarCuotasSocios['cadSQL'] = $cadSelectCuotasSocios;

		$arrBuscarCuotasSocios['arrBindValues']	= $arrBind;
																																											
		//echo "<br><br>1 modeloTesorero.php:cadBuscarCuotasSocios:arrBuscarCuotasSocios: "; print_r($arrBuscarCuotasSocios);	
		
		return $arrBuscarCuotasSocios;
}
/*---------------------- FIN cadBuscarCuotasSociosApeNom PDObindVal -----------------------------*/

/*------------------------------ Inicio buscarDatosSocioOrdenesCobro ------------------------------

Devuelve un array asociativo con los datos de un socio y CUOTAS (tabla CUOTAANIOSOCIO), y los 
datos de ORDENES_COBRO en el caso de ese socio (CODSOCIO) tenga una orden de cobro en una remesa 
con cuota domiciliada.

En ORDENES_COBRO, se busca por:

a) CODSOCIO= '$codsocio' AND ANIOCUOTA='$anioCuota' AND ESTADOCUOTA='PENDIENTE-COBRO' 
(por si no ha sido ABONADA por el banco y NO está anotada en tabla CUOTAANIOSOCIO)

b) CODSOCIO= '$codsocio' AND NOMARCHIVOSEPAXML = '$nombreArchivoSEPAXML' 
(si ya ha sido ABONADA por el banco y está anotada en tabla CUOTAANIOSOCIO))

RECIBE: $codUser, $anioCuota
DEVUELVE: un array $reDatosSocioOrdenesCobro, con datos del estado de abono del la cuota del socio
en el año elegido de tabla CUOTAANIOSOCIO, y si los hubiese datos de la orden de cobro del banco
para cuotas domiciliadas de tabla ORDENES_COBRO, y datos errores y numFilas.																												

LLAMADA: cTesorero.php: actualizarIngresoCuota(),
LLAMA :modeloSocios.php:buscarDatosSocio()
usuariosConfig/BBDD/MySQL/configMySQL.php:conexionDB(),modeloMySQL.php:buscarCadSql()
modeloErrores.php:insertarError()

OBSERVACIONES:
2020-11-08: Adapto para PDO:bindParaValue. Probado PHP 7.3.21
2017-11-14: añado función
--------------------------------------------------------------------------------------------------*/
function buscarDatosSocioOrdenesCobro($codUser, $anioCuota)		
{//echo "<br /><br />0-1 modeloTesorero:buscarDatosSocioOrdenesCobro:codUser: ";print_r($codUser);
 //echo "<br /><br />0-2 modeloTesorero:buscarDatosSocioOrdenesCobro:anioCuota: ";print_r($anioCuota);
	
	require_once "./modelos/modeloSocios.php";		
	require_once "./modelos/BBDD/MySQL/modeloMySQL.php";	
	require_once './modelos/modeloErrores.php';
	
	$reDatosSocioOrdenesCobro['nomScript'] = "modeloTesorero.php";
	$reDatosSocioOrdenesCobro['nomFuncion'] = "buscarDatosSocioOrdenesCobro";
	$reDatosSocioOrdenesCobro['codError'] = '00000';
	$reDatosSocioOrdenesCobro['errorMensaje'] = '';
	$nomScriptFuncionError = 'modeloTesorero.php:buscarDatosSocioOrdenesCobro(). Error: ';
	
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	require_once "./modelos/BBDD/MySQL/conexionMySQL.php";			
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);
	
	if ($conexionDB['codError'] !== '00000')	
	{ $reDatosSocioConfirmar = $conexionDB;
   $reDatosSocioConfirma['textoComentarios'] .= "Error del sistema al conectarse a la base de datos"; 	
	}
	else	//$conexionDB['codError']=='00000'
	{
		$reDatosSocio = buscarDatosSocio($codUser,$anioCuota);//en modeloSocios.php probado error OK
		
  //echo "<br /><br />2-1 modeloTesorero:buscarDatosSocioOrdenesCobro:reDatosSocio: ";print_r($reDatosSocio);
		
  if ($reDatosSocio['codError'] !== '00000') // ya incluye que si devuelve = 0 filas es error 
  { $reDatosSocioOrdenesCobro['codError'] = $reDatosSocio['codError'];		
				$reDatosSocioOrdenesCobro['errorMensaje'] = $reDatosSocio['errorMensaje'];				
    $reDatosSocioOrdenesCobro['textoComentarios'] = $nomScriptFuncionError.".Error del sistema, al buscar datos socio en función modeloSocios.php:buscarDatosSocio(). ";	
		}
		else // ($reDatosSocio['codError']=='00000')
		{
			//$reDatosSocioOrdenesCobro = $reDatosSocio;//se quita de aqui y hay que ponerlo al final			
			$codsocio = $reDatosSocio['valoresCampos']['datosFormSocio']['CODSOCIO']['valorCampo'];
			
	  /*--- Inicio buscar si en tabla ORDENES_COBRO hay orden ESTADOCUOTA='PENDIENTE-COBRO' para ese socio ---
			  Puede haber más de una orden en ORDENES_COBRO para un socio en un AÑO, pero para cada socio solo una 
					con ESTADOCUOTA = 'PENDIENTE-COBRO',  (aún no cobrada por el banco, cuando tesorería anota el cobro 
					de esa remesa que pondrá a toda la remesa ESTADOCUOTA = 'ABONADA', y después si fuese devuelta 
					individualmente se pondrá para ese socio y orden cobro ESTADOCUOTA = NOABONADA-DEVUELTA)
					Al anotar la remesa como cobrada también se anota el	nombre NOMARCHIVOSEPAXML en tabla CUOTAANIOSOCIO. 
			*/	

			$cadSql = "SELECT * FROM ORDENES_COBRO 
													 WHERE CODSOCIO = :codSocio AND ANIOCUOTA = :anioCuota AND ESTADOCUOTA = 'PENDIENTE-COBRO' ";//solo puede haber una fila con ESTADOCUOTA='PENDIENTE-COBRO'			
														
   $arrBind = array(':codSocio' => $codsocio, ':anioCuota' => $anioCuota); 
 
			//echo "<br><br>3-1 modeloTesorero:buscarDatosSocioOrdenesCobro:cadSql: ";print_r($cadSql);echo "<br>";						
				
			$arrOrdenesCobroSocioPendiente = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);//en BBDD/MySQL/modeloMySQL.php, probado error		
			
			//echo "<br><br>3-2 modeloTesorero:buscarDatosSocioOrdenesCobro:arrOrdenesCobroSocioPendiente: ";print_r($arrOrdenesCobroSocioPendiente);echo "<br>";		

			if ($arrOrdenesCobroSocioPendiente['codError'] !== '00000')
			{	$reDatosSocioOrdenesCobro['codError'] = $arrOrdenesCobroSocioPendiente['codError'];		
					$reDatosSocioOrdenesCobro['errorMensaje'] = $arrOrdenesCobroSocioPendiente['errorMensaje'];				
					$reDatosSocioOrdenesCobro['textoComentarios'] = $nomScriptFuncionError.".Error del sistema, al buscar en tabla ORDENES_COBRO con función buscarCadSql(). ";
			} 
			elseif ($arrOrdenesCobroSocioPendiente['numFilas'] !== 0)//sería numFilas =1, /*SÍ, tiene una y solo una orden en ORDENES_COBRO pendiente con ESTADOCUOTA = 'PENDIENTE-COBRO'*/
			{	    					
					foreach ($arrOrdenesCobroSocioPendiente['resultadoFilas'][0] as $campo => $contenidoCampo) //Es necesario para bloquear y poner en rojo 
					{  							
							$arrOrdenCobroOrdenCobro[$campo]['valorCampo'] = $contenidoCampo;//se preparan algunos datos de la select para mostrar algunos en el formulario y poner bloqueado formulario
					}		
					//echo "<br><br>3-3 modeloTesorero:buscarDatosSocioOrdenesCobro:arrOrdenCobroOrdenCobro: ";print_r($arrOrdenCobroOrdenCobro);echo "<br>";			
					
			}//$arrOrdenesCobroSocio['numFilas'] != 0; 
			
			/*--- Fin buscar si en tabla ORDENES_COBRO hay orden ESTADOCUOTA='PENDIENTE-COBRO' para ese socio ---*/
			
			else//if ($arrOrdenesCobroSocioPendiente['numFilas'] == 0)//NO tiene ORDENES_COBRO pendiente con ESTADOCUOTA = 'PENDIENTE-COBRO', 
			{
				/*------- Inicio buscar si hay orden de cobro anterior del mismo año de ese socio -------------
				  NO es error ya que hay socios que no tienen órdenes de pagos al no tener cuota domiciliada, 
			   pero como puede que tenga datos de anteriores remesas de ese año y estar devuelta o abonada 
				  hay que buscar en ORDENES_COBRO por NOMARCHIVOSEPAXML pero sólo sólo si hay datos de ese campo 
				  NOMARCHIVOSEPAXML en tabla CUOTAANIOSOCIO para ese socio, para obtener sus datos y mostrarlos 
						en el formulario	y en caso de que haya devolución, poder anotar la devolución
				*/				
			  //echo "<br /><br />4-1 modeloTesorero:buscarDatosSocioOrdenesCobro:reDatosSocio['datosFormCuotaSocio']: ";print_r($reDatosSocio['valoresCampos']['datosFormCuotaSocio'][$anioCuota]);		
					
			  if (isset($reDatosSocio['valoresCampos']['datosFormCuotaSocio'][$anioCuota]['NOMARCHIVOSEPAXML']['valorCampo']) && 
								 !empty($reDatosSocio['valoresCampos']['datosFormCuotaSocio'][$anioCuota]['NOMARCHIVOSEPAXML']['valorCampo']) )
					{					
 					$nombreArchivoSEPAXML = $reDatosSocio['valoresCampos']['datosFormCuotaSocio'][$anioCuota]['NOMARCHIVOSEPAXML']['valorCampo'] ; 

      $cadSql = "SELECT *		FROM ORDENES_COBRO 
																	WHERE CODSOCIO = :codSocio AND NOMARCHIVOSEPAXML = :nombreArchivoSEPAXML ";		
																	
					 $arrBind = array(':codSocio' => $codsocio, ':nombreArchivoSEPAXML' => $nombreArchivoSEPAXML); 
						
						//echo "<br><br>4-2 modeloTesorero:buscarDatosSocioOrdenesCobro:cadSql: ";print_r($cadSql);echo "<br>";	
								
						$arrOrdenesCobroSocioAnterior = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);//en BBDD/MySQL/modeloMySQL.php, probado error ok	
						
						//echo "<br><br>4-3 modeloTesorero:buscarDatosSocioOrdenesCobro:arrOrdenesCobroSocioAnterior: ";print_r($arrOrdenesCobroSocioAnterior);

						if ( $arrOrdenesCobroSocioAnterior['codError'] !== '00000' )
						{ $reDatosSocioOrdenesCobro['codError'] = $arrOrdenesCobroSocioAnterior['codError'];		
				   	$reDatosSocioOrdenesCobro['errorMensaje'] = $arrOrdenesCobroSocioAnterior['errorMensaje'];				
					   $reDatosSocioOrdenesCobro['textoComentarios'] = $nomScriptFuncionError.".Error del sistema, al buscar en tabla ORDENES_COBRO con función buscarCadSql(). ";									
						}
						elseif ($arrOrdenesCobroSocioAnterior['numFilas'] == 0) //ERROR: NO encontrado ORDENES_COBRO con NOMARCHIVOSEPAXML = $nombreArchivoSEPAXML y con ESTADOCUOTA != 'PENDIENTE-COBRO', 
			   { $reDatosSocioOrdenesCobro['codError'] = '80001';	
        $reDatosSocioOrdenesCobro['errorMensaje'] = 'ERROR: NO encontrado ORDENES_COBRO con el nombre correspondiente NOMARCHIVOSEPAXML';						
        $reDatosSocioOrdenesCobro['textoComentarios'] = $nomScriptFuncionError.".Error del sistema, al buscar en tabla ORDENES_COBRO con función buscarCadSql(). ";	
						}//else $arrOrdenesCobroSocioAnterior['numFilas'] == 0 
						
      else //arrOrdenesCobroSocioAnterior['numFilas'] !== 0 //solo debe devolver una fila correspondiente a la orden cobro de un socio para esa anterior remesa						
      {	
							foreach ($arrOrdenesCobroSocioAnterior['resultadoFilas'][0] as $campo => $contenidoCampo)//se preparan los datos de la select para mostrar en el formulario
							{  
									//$resultadoFilasAux[$campo]['valorCampo'] = $contenidoCampo;
									$arrOrdenCobroOrdenCobro[$campo]['valorCampo'] = $contenidoCampo;//se preparan algunos datos de la select para mostrar algunos en el formulario 
							}	
				   //Se prepara la fecha de devolución en formato adecuado para el formulario, si no está devuelta 
							if (isset($arrOrdenesCobroSocioAnterior['resultadoFilas'][0]['FECHADEVOLUCION']) && !empty($arrOrdenesCobroSocioAnterior['resultadoFilas'][0]['FECHADEVOLUCION']) )
							{									
         $fechaDevAux = $arrOrdenesCobroSocioAnterior['resultadoFilas'][0]['FECHADEVOLUCION'];	//puede tener el valor '0000-00-00' si no ha sido devuelta								
									$arrOrdenCobroOrdenCobro['FECHADEVOLUCION']['dia']['valorCampo']  = substr($fechaDevAux,8,2);
									$arrOrdenCobroOrdenCobro['FECHADEVOLUCION']['mes']['valorCampo']  = substr($fechaDevAux,5,2);
									$arrOrdenCobroOrdenCobro['FECHADEVOLUCION']['anio']['valorCampo'] = substr($fechaDevAux,0,4);
							}
							//echo "<br><br>4-4 modeloTesorero:buscarDatosSocioOrdenesCobro:arrOrdenCobroOrdenCobro: ";print_r($arrOrdenCobroOrdenCobro);echo "<br>";	
						}//else arrOrdenesCobroSocioAnterior['numFilas'] !== 0 						
		  	}//if (isset($reDatosSocio['valoresCampos']['datosFormCuotaSocio'][$anioCuota]['NOMARCHIVOSEPAXML']['valorCampo']) &&
					
					/*------- Fin buscar si hay orden de cobro anterior del mismo año de ese socio -------------*/
					
			}//elseif ($arrOrdenesCobroSocioPendiente['numFilas'] == 0) //NO tiene ORDENES_COBRO pendiente con ESTADOCUOTA = 'PENDIENTE-COBRO',			
  }//else $reDatosSocio['codError'] =='00000'
		
		//echo "<br /><br />5 modeloTesorero:buscarDatosSocioOrdenesCobro:reDatosSocioOrdenesCobro: ";print_r($reDatosSocioOrdenesCobro);
		
		if ( $reDatosSocioOrdenesCobro['codError'] !== '00000' )
		{	
				insertarError($reDatosSocioOrdenesCobro);		
		}
		else
		{ $reDatosSocioOrdenesCobro = $reDatosSocio;//aquí $reDatosSocio contiene todos los datos socio incluidos los contenidos en CUOTAANIOSOCIO de ese año 
				
				if ( isset($arrOrdenCobroOrdenCobro) && !empty(array_filter($arrOrdenCobroOrdenCobro)))
	  	{ $reDatosSocioOrdenesCobro['valoresCampos']['formOrdenesCobro'] = $arrOrdenCobroOrdenCobro;//de tabla "ORDENES_COBRO"	
				}	
  }		
	}//else	$conexionDB['codError']=='00000'		

	//echo "<br /><br />6 modeloTesorero:buscarDatosSocioOrdenesCobro:reDatosSocioOrdenesCobro: ";print_r($reDatosSocioOrdenesCobro);
	
 return $reDatosSocioOrdenesCobro;
}
/*------------------------------ Fin buscarDatosSocioOrdenesCobro --------------------------------*/


/*----------------------------- Inicio actualizarIngresoCuotaAnio ----------------------------------
Se actualiza, por parte del tesorero, la tabla CUOTANIOSOCIO los campos en relación
con el ingreso de una cuota del socio correspondiente al año elegido que puede ser el año actual o 
años anteriores.
En esta función es donde se anotan las posibles devoluciones de las ORDENES_COBRO  devueltas por 
el banco.
$arrayDatosActOrdenesCobro['IMPORTECUOTAANIOPAGADA'] = ORDENES_COBRO.IMPORTECUOTAANIOPAGADA es el 
importe pagado en la orden de cobro. En general coincidirá con CUOTAANIOSOCIO.IMPORTECUOTAANIOPAGADA,
pero NO coincidirá si fuese el caso de ESTADOCUOTA = ABONADA-PARTE, 
en ese caso ORDENES_COBRO.IMPORTECUOTAANIOPAGADA < CUOTAANIOSOCIO.IMPORTECUOTAANIOPAGADA. 
							
En el caso de que haya filas de CUOTAANIOSOCIO(Y+1) próximo año, si después se corrige el pago de 
la cuota en en CUOTAANIOSOCIO(Y) y se pone como no pagada: (PENDIENTE-COBRO,ABONADA-PARTE,
NOABONADA-ERROR-CUENTA,NOABONADA-DEVUELTA) se elimina para ese socio la fila  CUOTAANIOSOCIO(Y+1)
ya que no puede existir fila correspondiente para CUOTAANIOSOCIO (Y+1) mientras no esté abonada el
año actual (Y)

LLAMADA: cTesorero.php:actualizarIngresoCuota() 
LLAMA: modeloTesorero.php:actualizSocioPorCODSOCIO()
       modeloPresCoord.php:buscarDatosMiembro()
       modeloSocios.php:actualizCuotaAnioSocio()
							modeloMySQL.php:borrarFilas(),actualizarTabla()
							usuariosConfig/BBDD/MySQL/configMySQL.php	
	      BBDD/MySQL/conexionMySQL.php:conexionDB()
							BBDD/MySQL/transationPDO.php:beginTransationPDO()
       commitPDO(),rollbackPDO()
       modeloErrores.php:insertarError()

OBSERVACIONES: añado para PDO MySQL: BBDD/MySQL/transationPDO.php 
se incluye $arrBindValues para PDO y PHP 7.3.21

Se actualiza el ingreso de una cuota del socio de ese año por parte del tesorero, 
controlando las opciones para ESTADOCUOTA,ORDENARCOBROBANCO,MODOINGRESO, de la tabla CUOTAANIOSOCIO,
y en SOCIO se actualiza los valores de FRST->RCUR, y eliminacion de CUENTAIBAN en caso de devolucion.
En caso de devolución se anota actualiza la tabla ORDENES_COBRO.

NOTA: Se podría fragmentar en varias funciones para que no sea tan largo
--------------------------------------------------------------------------------------------------*/
function actualizarIngresoCuotaAnio($arrayDatosValidarCamposForm)
{
	//echo "<br><br>0-1 modeloTesorero:actualizarIngresoCuotaAnio:arrayDatosValidarCamposForm: ";print_r($arrayDatosValidarCamposForm);

	$arrayDatosAct = $arrayDatosValidarCamposForm['formIngresoCuota'];//para acortar nombre
 
	$actualizarIngresoCuotaAnio['nomScript'] = "modeloTesorero.php";	
	$actualizarIngresoCuotaAnio['nomFuncion'] = "actualizarIngresoCuotaAnio";
 $actualizarIngresoCuotaAnio['codError'] = '00000';
 $actualizarIngresoCuotaAnio['errorMensaje'] = '';
	$arrMensaje['textoComentarios'] ='';	

	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	require_once "BBDD/MySQL/conexionMySQL.php";			
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);	

 //echo "<br><br>0-2 modeloTesorero:actualizarIngresoCuotaAnio:conexionsDB: ";var_dump($conexionsDB);echo "<br>";

	if ($conexionDB['codError'] !== "00000")
	{$actualizarIngresoCuotaAnio = $conexionDB;	  
	}
	else //$conexionDB['codError']=="00000"
	{
  require_once("BBDD/MySQL/transationPDO.php");
		$resIniTrans = beginTransationPDO($conexionDB['conexionLink']);
		
		//echo "<br><br>0-3 modeloTesorero:actualizarIngresoCuotaAnio:resIniTrans: ";var_dump($resIniTrans);echo "<br>";		
		if ($resIniTrans['codError'] !== '00000') //será ['codError'] = '70501';
		{ $resIniTrans['errorMensaje'] = 'Error en el sistema, no se ha podido iniciar la transación. ';
				$resIniTrans['numFilas'] = 0;							
				$actualizarIngresoCuotaAnio = $resIniTrans;									
	 }
		else// $resIniTrans['codError'] == '00000'
		{$arrayDatosAct['FECHAPAGO']['valorCampo'] = $arrayDatosAct['FECHAPAGO']['anio']['valorCampo']."-".
																			                            $arrayDatosAct['FECHAPAGO']['mes']['valorCampo']."-".
																																											    $arrayDatosAct['FECHAPAGO']['dia']['valorCampo'];
																																															
			$arrayDatosAct['FECHAANOTACION']['valorCampo'] = date('Y-m-d');//mysql:(CURRENT_DATE());   	
					
   if ($arrayDatosAct['ANIOCUOTA']['valorCampo'] > date('Y'))// no se puede anotar ingresos de años futuros
			{
			 //No entrará aquí porque se controla en cTesorero.php:actualizarIngresoCuota() y allí se genera el mensaje.
				//echo '<br />1-1: NO SE PUEDEN ANOTAR CUOTAS MAS ALLÁ DEL AÑO ACTUAL';
			}		
			//---- Inicio Modificar Ingreso cuota años anteriores (corrección) ------------------------------
	  elseif ($arrayDatosAct['ANIOCUOTA']['valorCampo'] < date('Y'))//Será corrección de años anteriores de ABONADA,ABONADA-PARTE,NOABONADA,NOABONADA-DEVUELTA, O ERROR CUENTA		
	 	{
				//echo '<br />1-2';
				//------- Inicio IMPORTECUOTAANIOPAGADA == 0 ---------------------------------------------
				if ($arrayDatosAct['IMPORTECUOTAANIOPAGADA']['valorCampo']== 0)
				{ //echo '<br />1-2-1';		
			
					 //-----  Para actualizar tabla CUOTAANIOSOCIO al final de la función --------------
						$arrayDatosAct['ORDENARCOBROBANCO']['valorCampo'] = 'NO';
						$arrayDatosAct['MODOINGRESO']['valorCampo'] = 'SIN-DATOS';	        
						$arrayDatosAct['CUENTAPAGO']['valorCampo'] = NULL;		
						//$arrayDatosAct['OBSERVACIONES']['valorCampo'] //lo que tenga el formulario;
						
						if ($arrayDatosAct['ESTADOCUOTA']['valorCampo']== 'ABONADA' || $arrayDatosAct['ESTADOCUOTA']['valorCampo']== 'NOABONADA' || 
						    $arrayDatosAct['ESTADOCUOTA']['valorCampo']== 'ABONADA-PARTE') //es una corrección de un año anterior
						{ //echo '<br />1-2-2';
								$arrayDatosAct['ESTADOCUOTA']['valorCampo'] = 'NOABONADA';//es corrección en año anterior
						}
						elseif ($arrayDatosAct['ESTADOCUOTA']['valorCampo']== 'NOABONADA-DEVUELTA' || $arrayDatosAct['ESTADOCUOTA']['valorCampo']== 'NOABONADA-ERROR-CUENTA')								
						{ //---Todo este else es exactamente el mismo que el de más adelante para anotación de devolución en el mismo año de la remesa
						
						  //---Pudiera suceder que en la última orden de cobro (noviembre o diciembre), 
								//---alguna devolución se produzca en enero del siguiente año ya que las devoluciones se pueden retrasar hasta dos meses, 
						  //echo '<br /><br />1-2-3 ';//se deja como estaba $arrayDatosAct['ESTADOCUOTA']['valorCampo'] = NOABONADA-ERROR-CUENTA o NOABONADA-DEVUELTA;//corrección año anteriores
								
								//-----  Para actualizar tabla CUOTAANIOSOCIO al final de la función --------------

								$arrayDatosAct['ESTADOCUOTA']['valorCampo'] = 'NOABONADA-DEVUELTA'; 
								$arrayDatosAct['ORDENARCOBROBANCO']['valorCampo'] = 'NO';
								$arrayDatosAct['MODOINGRESO']['valorCampo'] = 'SIN-DATOS';
								$cuentaErrorDevuelta = $arrayDatosValidarCamposForm['formOrdenesCobro']['CUENTAIBAN']['valorCampo'];		
								$arrayDatosAct['CUENTAPAGO']['valorCampo'] = NULL;								
								$observaciones = "-".date('Y-m-d')."- HABLAR CON SOCIO/A. DEVUELTO EN CUENTA: ".$cuentaErrorDevuelta;
								$arrayDatosAct['OBSERVACIONES']['valorCampo'] .= $observaciones;
								//----------------------------------------------------------------------------------			
								
								//-----  Para actualizar tabla SOCIO más abajo en esta función----------------------
								$arrayDatosActSocio['CUENTAIBAN']['valorCampo'] = NULL;	
								$arrayDatosActSocio['SECUENCIAADEUDOSEPA']['valorCampo'] = NULL;	
								$arrayDatosActSocio['FECHAACTUALIZACUENTA']['valorCampo'] = '0000-00-00';	
        $arrayDatosActSocio['MODOINGRESO']['valorCampo'] = 'SIN-DATOS';	
								//----------------------------------------------------------------------------------	
						}//	else//será $arrayDatosAct['ESTADOCUOTA']['valorCampo']== NOABONADA-ERROR-CUENTA o NOABONADA-DEVUELTA, 
						else // incluiría a EXENTO en el caso de que haya algún honorario que tendrá IMPORTECUOTAANIOEL=0								
						{ // no se hace nada porque no pagan cuota									
						}										
				}//if ($arrayDatosAct['IMPORTECUOTAANIOPAGADA']['valorCampo']== 0)				
				//------------------ Fin IMPORTECUOTAANIOPAGADA == 0 ----------------------------------------	
			
				//-- Inicio ABONA-PARTE:IMPORTECUOTAANIOPAGADA < IMPORTECUOTAANIOEL y IMPORTECUOTAANIOPAGADA !=0 --
				elseif($arrayDatosAct['IMPORTECUOTAANIOPAGADA']['valorCampo'] < $arrayDatosAct['IMPORTECUOTAANIOEL']['valorCampo'])//ABONADA-PARTE no es pago domiciliado,cobra todo o nada
				{ //echo '<br />1-3-1'; 
				
				  //-----  Para actualizar tabla CUOTAANIOSOCIO al final de la función -------------------
						$arrayDatosAct['ESTADOCUOTA']['valorCampo'] = 'ABONADA-PARTE';
						$arrayDatosAct['ORDENARCOBROBANCO']['valorCampo'] = 'NO';	
						//$arrayDatosAct['MODOINGRESO']['valorCampo'] == el del formulario	y no debiera ser DOMICILIADO			
						$arrayDatosAct['CUENTAPAGO']['valorCampo'] = NULL;	//nunca será pago domiciliado, se cobra todo o nada	
						//$arrayDatosAct['OBSERVACIONES']['valorCampo'] // lo que tenga el formulario;								
						//--------------------------------------------------------------------------------------
					
				}//elseif($arrayDatosAct['IMPORTECUOTAANIOPAGADA']['valorCampo'] < $arrayDatosAct['IMPORTECUOTAANIOEL']['valorCampo'])			
				//-- Fin ABONA-PARTE: IMPORTECUOTAANIOPAGADA < IMPORTECUOTAANIOEL  y IMPORTECUOTAANIOPAGADA != 0 ---
				
				//------- Inicio ABONADA: ['IMPORTECUOTAANIOPAGADA'] >= ['IMPORTECUOTAANIOEL'] --------------				
				elseif($arrayDatosAct['IMPORTECUOTAANIOPAGADA']['valorCampo'] >= $arrayDatosAct['IMPORTECUOTAANIOEL']['valorCampo'])//IMPORTECUOTAANIOEL es la cuota que le corresponde en EL segun tipos
				{ //echo '<br />1-4-1'; print_r($arrayDatosAct);
				
      //-----  Para actualizar tabla CUOTAANIOSOCIO al final de la función -------------------
						$arrayDatosAct['ESTADOCUOTA']['valorCampo'] = 'ABONADA';
						$arrayDatosAct['ORDENARCOBROBANCO']['valorCampo'] = 'NO'; 
						//$arrayDatosAct['MODOINGRESO']['valorCampo'] == el del formulario				
						//$arrayDatosAct['OBSERVACIONES']['valorCampo'] // lo que tenga el formulario;
							
						if (isset($arrayDatosAct['MODOINGRESO']['valorCampo']) && $arrayDatosAct['MODOINGRESO']['valorCampo'] !== 'DOMICILIADA')
						{ //echo '<br />1-4-2'; 
								$arrayDatosAct['CUENTAPAGO']['valorCampo'] = NULL;
						}
						else //$arrayDatosAct['MODOINGRESO']['valorCampo'] == 'DOMICILIADA'
						{ //$arrayDatosAct['CUENTAPAGO']['valorCampo'] = Se deja con lo que tenga	
								//echo '<br />1-4-3';							
						}	
						//--------------------------------------------------------------------------------------
				}//elseif($arrayDatosAct['IMPORTECUOTAANIOPAGADA']['valorCampo']>=$arrayDatosAct['IMPORTECUOTAANIOEL']['valorCampo'])	
				//-------- Fin ABONADA: ['IMPORTECUOTAANIOPAGADA'] >= ['IMPORTECUOTAANIOEL'] ----------------		
	
   	//echo "<br><br>1-5-a modeloTesorero:actualizarIngresoCuotaAnio:arrayDatosAct: ";print_r($arrayDatosAct);
    
			}//elseif ($arrayDatosAct['ANIOCUOTA']['valorCampo'] < date('Y'))
			//---- Fin Modificar Ingreso cuota años anteriores (corrección)----------------------------------
			
			//---- Inicio Anotar o modificar Ingreso cuota año actual ---------------------------------------
	  elseif ($arrayDatosAct['ANIOCUOTA']['valorCampo'] == date('Y'))			
	 	{
				//--Inicio ABONA=0,o ABONADA-PARTE (año= Y actual)nunca pago domiciliado pues cobra todo o nada-   
				
    if($arrayDatosAct['IMPORTECUOTAANIOPAGADA']['valorCampo'] < $arrayDatosAct['IMPORTECUOTAANIOEL']['valorCampo'])				
				{	
				  //------- Inicio IMPORTECUOTAANIOPAGADA'] == 0 ------------------------------------------
				  if ($arrayDatosAct['IMPORTECUOTAANIOPAGADA']['valorCampo'] == 0)
						{ //echo '<br />2-1';														
					   //En ABONADA-PARTE, ABONADA de estos casos no se entrará porque se controla en validar
								if ($arrayDatosAct['ESTADOCUOTA']['valorCampo']=='PENDIENTE-COBRO' || $arrayDatosAct['ESTADOCUOTA']['valorCampo']=='ABONADA' || 
								    $arrayDatosAct['ESTADOCUOTA']['valorCampo']=='ABONADA-PARTE') //es una corrección en año actual de comentarios u otros datos
								{ //echo '<br />2-1-1 ';
								
										//-----  Para actualizar tabla CUOTAANIOSOCIO al final de la función -------------
										$arrayDatosAct['ESTADOCUOTA']['valorCampo'] = 'PENDIENTE-COBRO'; 
										//$arrayDatosAct['ORDENARCOBROBANCO']['valorCampo'] = //se deja valor formulario
										//$arrayDatosAct['MODOINGRESO']['valorCampo'] = //se deja valor formulario	
										$arrayDatosAct['CUENTAPAGO']['valorCampo'] = NULL;	
										//$arrayDatosAct['OBSERVACIONES']['valorCampo'] // lo que tenga el formulario;				
										//--------------------------------------------------------------------------------
										
										//-----  Para actualizar tabla SOCIO más abajo en esta función--------------------
										// si tiene CUENTAIBAN poner SECUENCIAADEUDOSEPA a FRST (como medida preventiva) 
										// por si es una correción previa de ABONADA que estaba anotada como RCUR pero en realidad no se ha cobrado nunca por banco
										if (isset($arrayDatosValidarCamposForm['datosFormSocio']['CUENTAIBAN']['valorCampo']))
										{ //echo '<br />2-1-2 ';
												$arrayDatosActSocio['SECUENCIAADEUDOSEPA']['valorCampo'] = 'FRST';
										}	//------------------------------------------------------------------------------
								}       
								elseif ($arrayDatosAct['ESTADOCUOTA']['valorCampo'] =='NOABONADA-DEVUELTA' || $arrayDatosAct['ESTADOCUOTA']['valorCampo'] =='NOABONADA-ERROR-CUENTA')							
								{ //echo '<br />2-1-3 ';			
							
										//-----  Para actualizar tabla CUOTAANIOSOCIO al final de la función -------------
										//echo '<br />2-1-3-1a ESTADOCUOTA_ANTES_REMESA: ';print_r($arrayDatosValidarCamposForm['formOrdenesCobro']['ESTADOCUOTA_ANTES_REMESA']['valorCampo']);//bien sale ABONADA-PARTE
										//echo '<br />2-1-3-1b IMPORTECUOTAANIOPAGADA_ANTES_REMESA: ';print_r($arrayDatosValidarCamposForm['formOrdenesCobro']['IMPORTECUOTAANIOPAGADA_ANTES_REMESA']['valorCampo']);

										if ($arrayDatosValidarCamposForm['formOrdenesCobro']['ESTADOCUOTA_ANTES_REMESA']['valorCampo'] == 'ABONADA-PARTE' ) //en EXENTO no se daría esta situación
										{	
											//echo '<br /><br />2-1-3-2a';
											$arrayDatosAct['ESTADOCUOTA']['valorCampo'] = 'ABONADA-PARTE'; 
											$arrayDatosAct['IMPORTECUOTAANIOPAGADA']['valorCampo'] = $arrayDatosValidarCamposForm['formOrdenesCobro']['IMPORTECUOTAANIOPAGADA_ANTES_REMESA']['valorCampo'];
           $arrayDatosAct['FECHAPAGO']['valorCampo'] = $arrayDatosValidarCamposForm['formOrdenesCobro']['FECHAPAGO_ANTES_REMESA']['valorCampo'];											
											$observacionesAclaracion =". Previamente a esta devolución, en fecha ".$arrayDatosAct['FECHAPAGO']['valorCampo']." había realizado un ingreso de ".
																																					$arrayDatosValidarCamposForm['formOrdenesCobro']['IMPORTECUOTAANIOPAGADA_ANTES_REMESA']['valorCampo']." nuevo estado cuota: ABONADA-PARTE";		
	          //echo "<br /><br />2-1-3-2b arrayDatosAct['FECHAPAGO']['valorCampo']:";print_r($arrayDatosAct['FECHAPAGO']['valorCampo']);																																			
										}	
										else 
										{	//echo '<br /><br />2-1-3-2c '; 
												$arrayDatosAct['ESTADOCUOTA']['valorCampo'] = 'NOABONADA-DEVUELTA';											
												$observacionesAclaracion = ' ';
										}													
										
										$arrayDatosAct['ORDENARCOBROBANCO']['valorCampo'] = 'NO';
										$arrayDatosAct['MODOINGRESO']['valorCampo'] = 'SIN-DATOS';
										$cuentaErrorDevuelta = $arrayDatosValidarCamposForm['formOrdenesCobro']['CUENTAIBAN']['valorCampo'];		
										$arrayDatosAct['CUENTAPAGO']['valorCampo'] = NULL;
										
										$observaciones = "-".date('Y-m-d')."- HABLAR CON SOCIO/A. DEVUELTO EN CUENTA: ".$cuentaErrorDevuelta.
	                          ' Cantidad devuelta '. $arrayDatosValidarCamposForm['formOrdenesCobro']['CUOTADONACIONPENDIENTEPAGO']['valorCampo'].' euros '.
		                         $arrayDatosAct['ESTADOCUOTA']['valorCampo']. ' Se elimina cuenta para evitar devolución siguiente orden';
										$arrayDatosAct['OBSERVACIONES']['valorCampo'] .= $observaciones.$observacionesAclaracion;
										//--------------------------------------------------------------------------------
								
										//-----  Para actualizar tabla SOCIO mas abajo en esta función--------------------
										$arrayDatosActSocio['CUENTAIBAN']['valorCampo'] = NULL;	
										$arrayDatosActSocio['SECUENCIAADEUDOSEPA']['valorCampo'] = NULL;	
										$arrayDatosActSocio['FECHAACTUALIZACUENTA']['valorCampo'] = '0000-00-00';		
          $arrayDatosActSocio['MODOINGRESO']['valorCampo'] = 'SIN-DATOS';								
										//--------------------------------------------------------------------------------											
										
								}//	else será $arrayDatosAct['ESTADOCUOTA']['valorCampo']== NOABONADA-ERROR-CUENTA o NOABONADA-DEVUELTA
								else // incluiría a EXENTO en el caso de que haya algún honorario que tendrá IMPORTECUOTAANIOEL=0								
								{ // no se hace nada porque no pagan cuota									
								}	
								//echo '<br><br>2-1-4';	
						}//if ($arrayDatosAct['IMPORTECUOTAANIOPAGADA']['valorCampo']== 0)				
						//------------------ Fin IMPORTECUOTAANIOPAGADA'] == 0 -----------------------------------	
					
						//--Inicio ABONA-PARTE:[IMPORTECUOTAANIOPAGADA']<['IMPORTECUOTAANIOEL'] y IMPORTECUOTAANIOPAGADA']!= 0 --
						elseif($arrayDatosAct['IMPORTECUOTAANIOPAGADA']['valorCampo'] < $arrayDatosAct['IMPORTECUOTAANIOEL']['valorCampo'])//ABONADA-PARTE nunca será pago domiciliado, se cobra todo o nada
						{	//echo '<br />2-2-1';
								
								// ----  Para actualizar tabla CUOTAANIOSOCIO al final de la función ----------------
								$arrayDatosAct['ESTADOCUOTA']['valorCampo'] = 'ABONADA-PARTE';     				
								//$arrayDatosAct['ORDENARCOBROBANCO']['valorCampo'] = el del formulario		
								//$arrayDatosAct['MODOINGRESO']['valorCampo'] == el del formulario				
								$arrayDatosAct['CUENTAPAGO']['valorCampo'] = NULL;	//nunca será pago domiciliado, se cobra todo o nada	
								//$arrayDatosAct['OBSERVACIONES']['valorCampo'] // lo que tenga el formulario;								
								//-----------------------------------------------------------------------------------
								
								//-----  Para actualizar tabla SOCIO mas abajo en esta función-----------------------
								// si tiene CUENTAIBAN se SECUENCIAADEUDOSEPA a FRST (pero como medida preventiva) 
								// por si es una correción previa de ABONADA anotada RCUR pero en realidad no se ha cobrado nunca por banco
								if (isset($arrayDatosValidarCamposForm['datosFormSocio']['CUENTAIBAN']['valorCampo']))
								{ //echo '<br />2-2-2';								
										$arrayDatosActSocio['SECUENCIAADEUDOSEPA']['valorCampo'] = 'FRST';
								}//----------------------------------------------------------------------------------
								
						}//elseif($arrayDatosAct['IMPORTECUOTAANIOPAGADA']['valorCampo'] < $arrayDatosAct['IMPORTECUOTAANIOEL']['valorCampo'])						
						//-- Fin ABONA-PARTE: [IMPORTECUOTAANIOPAGADA']<['IMPORTECUOTAANIOEL'] y IMPORTECUOTAANIOPAGADA']!= 0 --	
					
						/*-- Inicio eliminar filas de CUOTAANIOSOCIO(Y+1) próximo año, al corregir en año actual (Y) 
						  el estado previo ABONADA(pagada) a otros estados (no pagada).						
						  Si la cuota de año (Y) fue pagada y el socio o un gestor han modificado sus datos 
								personales posteriormente (cambiar cuota, IBAN, ...) existirá una fila CUOTAANIOSOCIO(Y+1) 
								para  ese socio.						  
								Si después se corrige el pago de la cuota en en CUOTAANIOSOCIO(Y) y se pone como no pagada: 
								Casos posibles:(PENDIENTE-COBRO,ABONADA-PARTE,NOABONADA-ERROR-CUENT,NOABONADA-DEVUELTA) 
								si existiese CUOTAANIOSOCIO(Y+1), se debe eliminar para ese socio la fila  CUOTAANIOSOCIO(Y+1)
								ya que no puede existir fila correspondiente para CUOTAANIOSOCIO (Y+1) mientras no esté 
								abonada el año actual (Y)
						*/
						// se podría hacer una función function eliminarFilaCuotaAnioSocio($codSocio,$anioSiguiente) 
						
						if ( $actualizarIngresoCuotaAnio['codError'] == '00000' && $arrayDatosAct['ANIOCUOTA']['valorCampo'] == date('Y') &&
										( $arrayDatosAct['ESTADOCUOTA']['valorCampo'] == 'PENDIENTE-COBRO' || $arrayDatosAct['ESTADOCUOTA']['valorCampo'] == 'ABONADA-PARTE' ||
												$arrayDatosAct['ESTADOCUOTA']['valorCampo'] == 'NOABONADA-DEVUELTA' || $arrayDatosAct['ESTADOCUOTA']['valorCampo'] == 'NOABONADA-ERROR-CUENTA')
									)
						{							
								$anioSiguiente = date('Y')+1;
														
        $cadenaCondiciones  = " CODSOCIO = :codSocio AND CUOTAANIOSOCIO.ANIOCUOTA = :anioSiguiente "; 															
																				
						  $arrBind = array(':codSocio' => $arrayDatosAct['CODSOCIO']['valorCampo'], ':anioSiguiente' => $anioSiguiente);
								
								$resEliminarCuotaAnioSocio = borrarFilas('CUOTAANIOSOCIO',$cadenaCondiciones,$conexionDB['conexionLink'],$arrBind);//en modeloMySQL.php";					
								
        //echo '<br><br>2-2-3 modeloTesorero:actualizarIngresoCuotaAnio:resEliminarCuotaAnioSocio: ';print_r($resEliminarCuotaAnioSocio);									
        /*Si no hay error de sistema retornará $resEliminarCuotaAnioSocio['codError'] == '00000', 
								  y [numFilas] == 1 o [numFilas] == 0, según que en CUOTAANIOSOCIO(Y+1) haya fila o no para ese socio  
								*/
								if ($resEliminarCuotaAnioSocio['codError'] !== '00000')
								{ 
									 $actualizarIngresoCuotaAnio['codError'] = $resEliminarCuotaAnioSocio['codError'];         											
								}
						}//if ($actualizarIngresoCuotaAnio['codError']='00000' $arrayDatosAct['ANIOCUOTA']['valorCampo'] == date('Y') && ....)						
					// -- Fin eliminar filas de CUOTAANIOSOCIO(Y+1) próximo año, al corregir en año actual (Y)--											
      						
			 }//if($arrayDatosAct['IMPORTECUOTAANIOPAGADA']['valorCampo'] < $arrayDatosAct['IMPORTECUOTAANIOEL']['valorCampo'])// ABONA =0, O ABONADA-PARTE  
				
				//-- Fin ABONA =0, O ABONADA-PARTE (año = Y actual)-------------------------------------------- 
				
				//--Inicio ABONADA:IMPORTECUOTAANIOPAGADA>=$arrayDatosAct['IMPORTECUOTAANIOEL']y (año=Y actual)-
				
				elseif($arrayDatosAct['IMPORTECUOTAANIOPAGADA']['valorCampo'] >= $arrayDatosAct['IMPORTECUOTAANIOEL']['valorCampo'])//IMPORTECUOTAANIOEL: cuota EL segun tipos cuotas
				{
					//echo '<br />2-3-1 modeloTesorero:actualizarIngresoCuotaAnio:arrayDatosAct: '; print_r($arrayDatosAct);
					if ($arrayDatosAct['ESTADOCUOTA']['valorCampo'] !=='EXENTO')//en caso de exento no se cambia nada, habrá pagado 0 € y su estado seguirá ESTADOCUOTA=EXENTO 
					{
						//-----  Para actualizar tabla CUOTAANIOSOCIO al final de la función ----------------
						$arrayDatosAct['ESTADOCUOTA']['valorCampo'] = 'ABONADA';
						$arrayDatosAct['ORDENARCOBROBANCO']['valorCampo'] = 'NO';//ha pagado igual o más que la cuota de EL no se cobrará la diferencia a lo elegido
						//$arrayDatosAct['MODOINGRESO']['valorCampo'] = el del formulario				
						//$arrayDatosAct['OBSERVACIONES']['valorCampo'] = lo que tenga el formulario;
							
						//--- Pago de esta cuota 	NO DOMICILIADA (por otros sistemas) -------------------------
						if ($arrayDatosAct['MODOINGRESO']['valorCampo'] !== 'DOMICILIADA')
						{ //echo '<br />2-3-2'; 
								$arrayDatosAct['CUENTAPAGO']['valorCampo'] = NULL;							
								
								// ----  Para actualizar tabla SOCIO más abajo en esta función-----------------------
								if (isset($arrayDatosValidarCamposForm['datosFormSocio']['CUENTAIBAN']['valorCampo']))
								{//echo '<br />2-3-3';									
									/* si tiene CUENTAIBAN se SECUENCIAADEUDOSEPA a FRST (pero como medida preventiva) 
									   por si es una correción previa de anotada como ABONADA domicliada y anotada RCUR pero 
									   en realidad no se ha cobrado nunca	por banco y es una rectificación
									*/
									$arrayDatosActSocio['SECUENCIAADEUDOSEPA']['valorCampo'] = 'FRST';
									
								}//----------------------------------------------------------------------------------
						} 
						//---- Pago de esta cuota 	SÍ DOMICILIADA ---------------------------------------------				
						else //arrayDatosAct['MODOINGRESO']['valorCampo'] == 'DOMICILIADA')//ACASO MAS CLARO poner elseif ($arrayDatosAct['MODOINGRESO']['valorCampo'] == 'DOMICILIADA')
						{ 					  									
								//echo '<br />2-3-5:arrayDatosValidarCamposForm[datosFormSocio][CUENTAIBAN]: ';print_r($arrayDatosValidarCamposForm['datosFormSocio']['CUENTAIBAN']);									
								//echo '<br />2-3-6:arrayDatosAct[MODOINGRESO][valorCampo]: ';print_r($arrayDatosAct['MODOINGRESO']['valorCampo']);	
								
								if (isset($arrayDatosValidarCamposForm['datosFormSocio']['CUENTAIBAN']['valorCampo']) && !empty($arrayDatosValidarCamposForm['datosFormSocio']['CUENTAIBAN']['valorCampo']) )
								{ //echo '<br />2-3-7';
										
									$arrayDatosAct['CUENTAPAGO']['valorCampo'] = $arrayDatosValidarCamposForm['datosFormSocio']['CUENTAIBAN']['valorCampo'];								
									$observaciones = "-Pago domiciliado cobrado ".$arrayDatosAct['FECHAPAGO']['valorCampo']." con CUENTA: ".$arrayDatosAct['CUENTAPAGO']['valorCampo'];							
									$arrayDatosAct['OBSERVACIONES']['valorCampo'] .= $observaciones.". Gestor CODUSER: ".$_SESSION['vs_CODUSER'];							
										
									// ----  Para actualizar tabla SOCIO al final de la función ----------------								
									$arrayDatosActSocio['SECUENCIAADEUDOSEPA']['valorCampo'] = 'RCUR';				        
																		
									//echo "<br><br>2-3-8 modeloTesorero:actualizarIngresoCuotaAnio:arrayDatosActSocio: ";print_r($arrayDatosActSocio);		
								}
								else //no hay CUENTANOIBAN existente aunque hayan elegido Domiciliada en el formulario (error), acaso sobre este eslse
								{ //echo '<br />2-3-10';
										$cuentaPago = NULL;		
										//$arrayDatosAct['OBSERVACIONES']['valorCampo'] = lo del formulario									
								}								
						}//else //arrayDatosAct['MODOINGRESO']['valorCampo'] == 'DOMICILIADA')	
					}	//if $arrayDatosAct['ESTADOCUOTA']['valorCampo'] !=='EXENTO' 				
				}//elseif($arrayDatosAct['IMPORTECUOTAANIOPAGADA']['valorCampo']>=$arrayDatosAct['IMPORTECUOTAANIOEL']['valorCampo'])	
				//-------- Fin ABONADA: ['IMPORTECUOTAANIOPAGADA'] >= ['IMPORTECUOTAANIOEL'] y (año=Y actual)---
	
				//-------  Inicio actualizar tabla SOCIO Datos cuenta bancaria ---------------------------------
				if (isset($arrayDatosValidarCamposForm['datosFormSocio']['CUENTAIBAN']['valorCampo']) && ($actualizarIngresoCuotaAnio['codError']=='00000'))
				{	//echo "<br><br>2-4-1 modeloTesorero:actualizarIngresoCuotaAnio:arrayDatosActSocio: ";print_r($arrayDatosActSocio);		

						$reActSocio = actualizSocioPorCODSOCIO('SOCIO',$arrayDatosAct['CODSOCIO']['valorCampo'],$arrayDatosActSocio,$conexionDB['conexionLink']);//función en modeloTesorero
						//echo "<br><br>2-4-2 modeloTesorero:actualizarIngresoCuotaAnio:reActSocio: ";print_r($reActSocio);
						
						if ($reActSocio['codError']!=='00000')			
						{$arrMensaje['textoComentarios'].= $reActSocio['errorMensaje']. " al actualizar la tabla SOCIO. CODERROR: ".$reActSocio['codError'].
					                                    " Se ha producido un error del sistema al intentar actualizar la tabla SOCIO con nuevos datos de ingreso de cuota socio/a: ";
							$actualizarIngresoCuotaAnio = $reActSocio;
						}	
						else //$reActSocio['codError']=='00000')
						{$actualizarIngresoCuotaAnio['codError'] ='00000';											
						}								
				}	
				//------------------------  Fin actualizar tabla SOCIO -----------------------------------------		
				
		 }//elseif ($arrayDatosAct['ANIOCUOTA']['valorCampo'] == date('Y'))//Será una Anotación o corrección en año actual		
			
			//---- Fin Anotar modificar Ingreso cuota año actual (y) ----------------------------------------
			
			//-- Inicio actualizar CUOTAANIOSOCIO, Anotar o modificar Ingreso cuota año actual(Y) o anteriores -- 
			if ($actualizarIngresoCuotaAnio['codError']=='00000')			
	  { 
		  //---- Para buscar el nombre del gestor en MIEMBRO para OBSERVACIONES ------- 	

				$codUser = $_SESSION['vs_CODUSER'];

				require_once './modelos/modeloPresCoord.php';				
    $arrDatosGestor = buscarDatosMiembro($codUser,$conexionDB['conexionLink'],"100");
    //echo "<br><br>3-1 modeloTesorero:actualizarIngresoCuotaAnio:arrDatosGestor: ";print_r($arrDatosGestor);echo "<br><br>";				
								
				if ($arrDatosGestor['codError'] !=='00000')
				{$actualizarIngresoCuotaAnio = $arrDatosGestor;				
					$actualizarIngresoCuotaAnio['textoComentarios'] = $arrDatosGestor['errorMensaje']. " al buscar en tabla MIEMBRO. CODERROR: ".$arrDatosGestor['codError'];
				}  
    elseif ($arrDatosGestor['numFilas'] <= 0) // sí es error 
    {	$actualizarIngresoCuotaAnio['codError'] = '80001'; //no encontrado 
						$actualizarIngresoCuotaAnio['errorMensaje'] = "Error: No se han encontrado los datos del gestor en la tabla MIEMBRO";						
						$actualizarIngresoCuotaAnio['textoComentarios'] = $arrDatosGestor['errorMensaje']. "Error al buscar en tabla MIEMBRO. CODERROR: ".$actualizarIngresoCuotaAnio['codError'];
				} 
    //---- Fin buscar nombre del gestor en MIEMBRO para OBSERVACIONES --------------				
				else //$arrDatosGestor['numFilas'] !=0; 
				{	
				 //echo "<br><br>3-2a modeloTesorero:actualizarIngresoCuotaAnio:arrayDatosValidarCamposForm['formOrdenesCobro']['ADD_IMPORTEGASTOSDEVOLUCION']['valorCampo']: ";
     //print_r($arrayDatosValidarCamposForm['formOrdenesCobro']['ADD_IMPORTEGASTOSDEVOLUCION']['valorCampo']);
		
 		 	/*Nota: si queremos impedir modificar ADD_IMPORTEGASTOSDEVOLUCION en años anteriores, date('Y')-1
					  a CUOTAANIOSOCIO activaríamos el siguiente if, 	(también habría que hacerlo en ORDENES_COBRO)				
     if ($arrayDatosAct['ANIOCUOTA']['valorCampo'] <=date('Y'))	)	
			  {$arrayDatosAct['IMPORTEGASTOSABONOCUOTA']['valorCampo'] = $arrayDatosAct['IMPORTEGASTOSABONOCUOTA']['valorCampo'] +
			                                                             $arrayDatosAct['ADD_IMPORTEGASTOSABONOCUOTA']['valorCampo'];
					}																																																					 			 					
					else
					*/	
					{	
					 if (isset($arrayDatosValidarCamposForm['formOrdenesCobro']['ADD_IMPORTEGASTOSDEVOLUCION']['valorCampo']))//sólo de devolución de cuota domiciliada por banco
						{ $gastosDevolucionCuotaDomiciliada = $arrayDatosValidarCamposForm['formOrdenesCobro']['ADD_IMPORTEGASTOSDEVOLUCION']['valorCampo'];//sólo de devolución
						}							
						else
						{	$gastosDevolucionCuotaDomiciliada = 0.0;	
						}
					
					 $arrayDatosAct['IMPORTEGASTOSABONOCUOTA']['valorCampo'] = $arrayDatosAct['IMPORTEGASTOSABONOCUOTA']['valorCampo'] +
			                                                             $arrayDatosAct['ADD_IMPORTEGASTOSABONOCUOTA']['valorCampo'] + //los de PayPal, transferecias, ... No domiciliados		
																																																																$gastosDevolucionCuotaDomiciliada;
																																																										 	 		 //$arrayDatosValidarCamposForm['formOrdenesCobro']['ADD_IMPORTEGASTOSDEVOLUCION']['valorCampo'];//sólo de devolución
				 }																																																									
					unset($arrayDatosAct['ADD_IMPORTEGASTOSABONOCUOTA']);				
					
					$nomGestor = $arrDatosGestor['resultadoFilas'][0]['NOM']." ".  $arrDatosGestor['resultadoFilas'][0]['APE1']; 				
					$arrayDatosAct['OBSERVACIONES']['valorCampo'] .= ". ".date('Y-m-d')." . Gestor: ".$nomGestor;//". CODUSER: ".$_SESSION['vs_CODUSER'];	
					
					//echo "<br><br>3-2b modeloTesorero:actualizarIngresoCuotaAnio:arrayDatosAct: ";print_r($arrayDatosAct);
					
					require_once './modelos/modeloSocios.php';		
					$actualizarIngresoCuotaAnio = actualizCuotaAnioSocio('CUOTAANIOSOCIO',$arrayDatosAct['CODSOCIO']['valorCampo'],$arrayDatosAct,$conexionDB['conexionLink']);
														
					//echo "<br><br>3-3 modeloTesorero:actualizarIngresoCuotaAnio:actualizarIngresoCuotaAnio: ";print_r($actualizarIngresoCuotaAnio);
					
					if ($actualizarIngresoCuotaAnio['codError']!=='00000')			
					{ $arrMensaje['textoComentarios'].= $actualizarIngresoCuotaAnio['errorMensaje']. " al actualizar la CUOTAANIOSOCIO. CODERROR: ".$actualizarIngresoCuotaAnio['codError'].
																																								" Se ha producido un error del sistema al intentar actualizar la tabla CUOTAANIOSOCIO con nuevos datos de ingreso de cuota socio/a: ";
					}	
					else //$reActSocio['codError']=='00000')
					{ $actualizarIngresoCuotaAnio['codError'] ='00000';	
							//$arrMensaje['textoComentarios'].=" Se han actualizado los datos de ingreso cuota socio/a <br /><br />";			 
					}	
		 	} //	else //$arrDatosGestor['numFilas'] !=0; 						
			}//if ($actualizarIngresoCuotaAnio ['codError']=='00000')
   
		 //----------- Inicio Para actualizar tabla ORDENES_COBRO	----------------------------------------
	
			if ($actualizarIngresoCuotaAnio['codError']=='00000')			
			{//echo "<br><br>4-0 modeloTesorero:modeloTesorero:arrayDatosValidarCamposForm['formOrdenesCobro'] : ";print_r($arrayDatosValidarCamposForm['formOrdenesCobro']);

    //echo "<br><br>4-0-a modeloTesorero:actualizarIngresoCuotaAnio:arrayDatosAct['NOMARCHIVOSEPAXML']['valorCampo']: ";print_r($arrayDatosAct['NOMARCHIVOSEPAXML']['valorCampo']);
    //echo "<br><br>4-0-b modeloTesorero:actualizarIngresoCuotaAnio:arrayDatosValidarCamposForm['formOrdenesCobro']['NOMARCHIVOSEPAXML']['valorCampo']: ";print_r($arrayDatosValidarCamposForm['formOrdenesCobro']['NOMARCHIVOSEPAXML']['valorCampo']);				

		  //if (isset($arrayDatosValidarCamposForm['formOrdenesCobro']['NOMARCHIVOSEPAXML']['valorCampo']) && !empty($arrayDatosValidarCamposForm['formOrdenesCobro']['NOMARCHIVOSEPAXML']['valorCampo']))
				if (isset($arrayDatosAct['NOMARCHIVOSEPAXML']['valorCampo']) && !empty($arrayDatosAct['NOMARCHIVOSEPAXML']['valorCampo']))//$arrayDatosAct['NOMARCHIVOSEPAXML']['valorCampo']=$arrayDatosValidarCamposForm['formIngresoCuota']['NOMARCHIVOSEPAXML']['valorCampo'];
				{			   		
					//1ª condición
					$arrayCondicionesOrdenesCobro['CODSOCIO']['valorCampo']= $arrayDatosAct['CODSOCIO']['valorCampo'];
					$arrayCondicionesOrdenesCobro['CODSOCIO']['operador']= '=';
					$arrayCondicionesOrdenesCobro['CODSOCIO']['opUnir']= 'AND'; 
					//2ª condición
					$arrayCondicionesOrdenesCobro['NOMARCHIVOSEPAXML']['valorCampo'] = $arrayDatosAct['NOMARCHIVOSEPAXML']['valorCampo'];
					$arrayCondicionesOrdenesCobro['NOMARCHIVOSEPAXML']['operador']= '='; 
					$arrayCondicionesOrdenesCobro['NOMARCHIVOSEPAXML']['opUnir']= ' ';	
					
		  
					//echo "<br><br>4-1-1 modeloTesorero:actualizarIngresoCuotaAnio:arrayDatosValidarCamposForm['formOrdenesCobro']['ADD_IMPORTEGASTOSDEVOLUCION']['valorCampo']: ";print_r($arrayDatosValidarCamposForm['formOrdenesCobro']['ADD_IMPORTEGASTOSDEVOLUCION']['valorCampo']);																					
					//echo "<br><br>4-1-2 modeloTesorero:actualizarIngresoCuotaAnio:arrayDatosValidarCamposForm['formOrdenesCobro']['IMPORTEGASTOSDEVOLUCION']['valorCampo']: ";print_r($arrayDatosValidarCamposForm['formOrdenesCobro']['IMPORTEGASTOSDEVOLUCION']['valorCampo']);	
					/* ADD_IMPORTEGASTOSDEVOLUCION:Gastos de devolución deben ser imputados a ORDENES_CUOTAS de esa 
					   remesa NOMARCHIVOSEPAXML, y también se acumularán a los anteriores que ya hubiese en CUOTAANIOSOCIO 
								Para año actual = date('Y'), solo se permite modificar el campo ESTADOCUOTA, IMPORTECUOTAANIOPAGADA, FECHADEVOLUCION
								y FECHAANOTACION para evitar inconsistencias
					*/					
     /*Nota: si queremos impedir modificar ORDENES_COBRO de devoluciones en años anteriores, date ('Y')-1
					  activaríamos el siguiente if, de ese modo solo podríamos anotar MOTIVODEVOLUCION,FECHAANOTACION 
					*/
     //if ($arrayDatosAct['ANIOCUOTA']['valorCampo'] == date('Y')), similar para CUOTAANIOSOCIO	
					{
						$arrayDatosActOrdenesCobro['ESTADOCUOTA'] = $arrayDatosValidarCamposForm['formOrdenesCobro']['ESTADOCUOTA']['valorCampo'];
																
						//Si es una devolución de cuota domiciliada previamente se habrá tratado en: validarCamposTesorero.php:validarCamposFormActualizarIngresoCuota() 
						//y se habrá asignado "$arrayDatosValidarCamposForm['formOrdenesCobro']['IMPORTECUOTAANIOPAGADA']['valorCampo'] =	0.00"
						
						$arrayDatosActOrdenesCobro['IMPORTECUOTAANIOPAGADA'] = $arrayDatosValidarCamposForm['formOrdenesCobro']['IMPORTECUOTAANIOPAGADA']['valorCampo'];
								
				  /*$arrayDatosActOrdenesCobro['IMPORTECUOTAANIOPAGADA'] = ORDENES_COBRO.IMPORTECUOTAANIOPAGADA es el importe pagado en la orden de cobro. 
						  En general coincidirá con CUOTAANIOSOCIO.IMPORTECUOTAANIOPAGADA, pero NO coincidirá si fuese el caso de ESTADOCUOTA = ABONADA-PARTE, 
						  en ese caso ORDENES_COBRO.IMPORTECUOTAANIOPAGADA < CUOTAANIOSOCIO.IMPORTECUOTAANIOPAGADA. */								
						
						$arrayDatosActOrdenesCobro['FECHADEVOLUCION'] = $arrayDatosValidarCamposForm['formOrdenesCobro']['FECHADEVOLUCION']['anio']['valorCampo']."-".
																																																						$arrayDatosValidarCamposForm['formOrdenesCobro']['FECHADEVOLUCION']['mes']['valorCampo']."-".
																																																						$arrayDatosValidarCamposForm['formOrdenesCobro']['FECHADEVOLUCION']['dia']['valorCampo'];
					
					$arrayDatosActOrdenesCobro['IMPORTEGASTOSDEVOLUCION'] = $arrayDatosValidarCamposForm['formOrdenesCobro']['IMPORTEGASTOSDEVOLUCION']['valorCampo'] +
																																																													$arrayDatosValidarCamposForm['formOrdenesCobro']['ADD_IMPORTEGASTOSDEVOLUCION']['valorCampo'];																																																						
			 	}
     // Si se ha introducido MOTIVODEVOLUCION en el formulario se guarda en ORDENES_COBRO
					if (isset($arrayDatosValidarCamposForm['formOrdenesCobro']['MOTIVODEVOLUCION']['valorCampo']) && !empty($arrayDatosValidarCamposForm['formOrdenesCobro']['MOTIVODEVOLUCION']['valorCampo']) )		
     {	
					  $arrayDatosActOrdenesCobro['MOTIVODEVOLUCION'] .= $arrayDatosValidarCamposForm['formOrdenesCobro']['MOTIVODEVOLUCION']['valorCampo'].". CODGESTOR: ".$_SESSION['vs_CODUSER'];
					}
					else
					{			
				   $arrayDatosActOrdenesCobro['MOTIVODEVOLUCION'] = $arrayDatosValidarCamposForm['formOrdenesCobro']['MOTIVODEVOLUCION']['valorCampo'];
					}
				
					$arrayDatosActOrdenesCobro['FECHAANOTACION'] = date('Y-m-d');// en CUOTAANIOSOCIO se hace en la función pero faltaría aquí para órdenes	
					
     //echo "<br><br>4-2 modeloTesorero:actualizarIngresoCuotaAnio:arrayDatosActOrdenesCobro : ";print_r($arrayDatosActOrdenesCobro);															

					$reActOrdenesCobro  = actualizarTabla('ORDENES_COBRO',$arrayCondicionesOrdenesCobro,$arrayDatosActOrdenesCobro,$conexionDB['conexionLink']);//en modeloMySQL.php
					//echo "<br><br>4-3 modeloTesorero:actualizarIngresoCuotaAnio:reActOrdenesCobro : ";print_r($reActOrdenesCobro );							

					if ($reActOrdenesCobro['codError'] !== '00000')		
					{	$actualizarIngresoCuotaAnio = $reActOrdenesCobro;													
							$arrMensaje['textoComentarios'] = $reActOrdenesCobro['errorMensaje']. " al actualizar la tabla ORDENES_COBRO. CODERROR: ".$reActOrdenesCobro['codError']. 
								" Se ha producido un error del sistema al intentar actualizar la tabla ORDENES_COBRO con nuevos datos de ingreso de cuota socio/a: ";;									
					}	
					else //$reActOrdenesCobro['codError']=='00000')					
					{	$actualizarIngresoCuotaAnio['codError'] ='00000';
					}
     //echo "<br><br>4-4 modeloTesorero::actualizarIngresoCuotaAnio:arrayDatosActOrdenesCobro : ";print_r($arrayDatosActOrdenesCobro);	
    }	//if (isset($arrayDatosAct['NOMARCHIVOSEPAXML']['valorCampo']) && !empty($arrayDatosAct['NOMARCHIVOSEPAXML']['valorCampo']))	
																		
			}//if ($actualizarIngresoCuotaAnio['codError']=='00000')			
			//----------> Fin Para actualizar tabla ORDENES_COBRO	-------------------------------------------			
		
   //---------------- Inicio COMMIT ----------------------------------
	  if ($actualizarIngresoCuotaAnio['codError']=='00000')			
   {
				$resFinTrans = commitPDO($conexionDB['conexionLink']);
				
				//echo "<br><br>5-1 modeloTesorero:actualizarIngresoCuotaAnio:resFinTrans: ";var_dump($resFinTrans);													
				if ($resFinTrans['codError'] !== '00000')//será $resFinTrans['codError'] = '70502'; 									
				{ $resFinTrans['errorMensaje'] = 'Error en el sistema, no se ha podido finalizar transación. ';
						$resFinTrans['numFilas'] = 0;													
						$actualizarIngresoCuotaAnio = $resFinTrans;						
				}											
			 else
			 {//$_SESSION['vs_NOMUSUARIO']=$resValidarCamposForm['datosFormUsuario']['USUARIO']['valorCampo'];
					$arrMensaje['textoComentarios'] = "Se han actualizado los datos del ingreso de la cuota del socio/a: ".
				 $arrayDatosValidarCamposForm['datosFormMiembro']['NOM']['valorCampo']. " ".$arrayDatosValidarCamposForm['datosFormMiembro']['APE1']['valorCampo'];	
			 }
	  }//$actualizarIngresoCuotaAnio['codError']=='00000')
   /*---------------- Fin COMMIT -------------------------------------*/
			
			//echo "<br><br>6 modeloTesorero:actualizarIngresoCuotaAnio:actualizarIngresoCuotaAnio: ";print_r($actualizarIngresoCuotaAnio);

   /*--- Inicio error, ROLLBACK --------------------------------------*/
	  if ($actualizarIngresoCuotaAnio['codError']!=='00000')
		 {								
				$deshacerTrans = rollbackPDO($conexionDB['conexionLink']);				
							
				if ($deshacerTrans ['codError'] !== '00000')//será $deshacerTrans['codError'] = '70503';			
				{ $actualizarIngresoCuotaAnio['errorMensaje'] .= $resDeshacerTrans['errorMensaje'];											
				}	
			}
	 }//else $resIniTrans['codError'] == '00000'	
		
		if ($actualizarIngresoCuotaAnio['codError'] !== '00000'|| (isset($resDeshacerTrans['codError']) && $resDeshacerTrans['codError'] !== '00000') )
		{		
			if ( isset($actualizarIngresoCuotaAnio['textoComentarios']) ) 
			{ $actualizarIngresoCuotaAnio['textoComentarios'] = ". modeloTesorero:actualizarIngresoCuotaAnio(): ".$actualizarIngresoCuotaAnio['textoComentarios'];}	
			else
			{	$actualizarIngresoCuotaAnio['textoComentarios'] = ". modeloTesorero:actualizarIngresoCuotaAnio(): ";}								
											
				require_once './modelos/modeloErrores.php'; //si es un error de conexión a la tabla error, insertar errores 
				$resInsertarErrores = insertarError($actualizarIngresoCuotaAnio);	

				if ($resInsertarErrores['codError']!=='00000')
				{ $actualizarIngresoCuotaAnio['errorMensaje'] .= $resInsertarErrores['errorMensaje'];
						$arrMensaje['textoComentarios'].=" Error del sistema al tratar ERRORES, vuelva a intentarlo pasado un tiempo ";
				}		
		}//if $actualizarIngresoCuotaAnio['codError'] !=='00000')
	 //}//else $resIniTrans['codError'] == '00000'
	}//else $conexionDB['codError']=="00000"
	/*---------------- Fin Error ---------------------------------------------*/
	
	$actualizarIngresoCuotaAnio['arrMensaje'] = $arrMensaje;//mensaje con nombre			
	
	//echo '<br><br>7 modeloTesorero:actualizarIngresoCuotaAnio: ';print_r($actualizarIngresoCuotaAnio);
	
	return $actualizarIngresoCuotaAnio;
	} 
/*----------------------------- Fin actualizarIngresoCuotaAnio ------------------------------------*/

/*----------------------------- Inicio actualizSocioPorCODSOCIO-------------------------------------
Acción: En esta función se actualiza una fila con los datos del socio en la tabla SOCIO, a partir del 
        CODUSER  y un array con datos (cuenta bancaria, y cuota elegida, ..)						  
Recibe: un array con los campos de los datos a actualizar ya validados y el campo condiciones CODSOCIO
Devuelve: un array con los controles de errores, 
Llamada: modeloTesorero.php:actualizarIngresoCuotaAnioSocio(), ...
Llama: a actualizarTabla()

OBSERVACIONES: Sin Encriptar cuentas bancos. 
               Igual que modeloSocios:actualizSocio, excepto que la condición aquí es por CODSOCIO 
															en lugaR de por CODUSER            
--------------------------------------------------------------------------------------------------*/       
function actualizSocioPorCODSOCIO($tablaAct,$camposCondiciones,$arrayDatosAct,$conexionLinkDB)     
{//echo "<br><br>1 modeloTesorero:actualizSocioPorCODSOCIO:arrayDatosAct:";print_r($arrayDatosAct);
 $arrayCondiciones['CODSOCIO']['valorCampo']= $camposCondiciones;
 $arrayCondiciones['CODSOCIO']['operador']= '=';
 $arrayCondiciones['CODSOCIO']['opUnir']= ' ';  

	foreach ($arrayDatosAct as $indice => $contenido)                         
 {      
   $arrayDatos[$indice] = $contenido['valorCampo']; 
 }
	//cuenta extranjera se trata en el bucle, pero la cuenta española esta compuesta
	//unset($arrayDatos['ctaBanco']);
/*	$arrayDatos['CODENTIDAD'] = $arrayDatosAct['ctaBanco']['CODENTIDAD']['valorCampo'] ;
	$arrayDatos['CODSUCURSAL'] = $arrayDatosAct['ctaBanco']['CODSUCURSAL']['valorCampo'];
	$arrayDatos['DC'] = $arrayDatosAct['ctaBanco']['DC']['valorCampo'];
	$arrayDatos['NUMCUENTA'] = $arrayDatosAct['ctaBanco']['NUMCUENTA']['valorCampo'];
*/
//trata IBAN
			
	$resActualizarSocio = actualizarTabla($tablaAct,$arrayCondiciones,$arrayDatos,$conexionLinkDB); 																					
	//echo '<br><br>2 modeloTesorero:actualizSocioPorCODSOCIO:resActualizarSocio:';print_r($resActualizarSocio);	
	return $resActualizarSocio;
 } 
//----------------------------- Fin actualizSocioPorCODSOCIO ---------------------------------------

/*------------------- Inicio actualizaCuotaCCSocioTes ----------------------------------------------
Acción: Se actualiza cuota elegida por el socio y datos bancarios IBAN y NOIBAN, por parte del tesorero.
        Se actualizan los datos de la tabla MIEMBRO (solo email),SOCIOS y CUOTAANIOSOCIO
								Recibe datos validados de los formularios para la actualización.
								También se controlan, ESTADOCUOTA, y según cuentas IBAN y NOIBAN si está domiciliado
								y orden de cobro a banco.								
        controlando transacciones con start transation y rollback.
Recibe: $resValidarCamposForm        
Devuelve: un array con los controles de errores, y mensajes 
Llamada: desde cTesorero: actualizarDatosCuotaSocioTes(),
Llama: modeloSocios.php:buscarDatosSocio(),actualizSocio(),actualizCuotaAnioSocio(),
        insertarCuotaAnioSocio()
								modeloUsuarios.php:actualizMiembro()
								usuariosConfig/BBDD/MySQL/configMySQL.php;	
	       BBDD/MySQL/conexionMySQL.phpconexionDB()
        BBDD/MySQL/transationPDO.php:beginTransationPDO(),commit(),rollback()								

OBSERVACIONES: Se graban los errores del sistema en ERRORES

NOTA: YA NO SE UTILIZA HA SIDO SUSTITUIDA POR modeloSocios.php:actualizarDatosSocio()
---------------------------------------------------------------------------------*/
//function actualizaCuotaCCSocioTes_old($resValidarCamposForm)
function actualizaCuotaCCSocioTes($resValidarCamposForm)
{
 //echo "<br><br>0-1 modeloTesorero:actualizaCuotaCCSocioTes:resValidarCamposForm: "; print_r($resValidarCamposForm);
					
	require_once './modelos/modeloSocios.php'; 
	require_once './modelos/modeloUsuarios.php'; 
					
 $resActDatosSocio['nomScript'] = "modeloTesorero.php";	
	$resActDatosSocio['nomFuncion'] = "actualizaCuotaCCSocioTes";
 $resActDatosSocio['codError'] = '00000';
 $resActDatosSocio['errorMensaje'] = '';
	
	$arrMensaje['textoComentarios'] ='';	
	
 require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	require_once "BBDD/MySQL/conexionMySQL.php";	
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);	

	if ($conexionDB['codError'] !== "00000")
	{ $resActDatosSocio = $conexionDB;	  
	}
	else //$conexionDB['codError']=="00000"
	{
  require_once("BBDD/MySQL/transationPDO.php");
		$resIniTrans = beginTransationPDO($conexionDB['conexionLink']);
		
		//echo "<br><br>1-1 modeloTesorero:actualizaCuotaCCSocioTes:resIniTrans: ";var_dump($resIniTrans);echo "<br>";
		if ($resIniTrans['codError'] !== '00000') //será ['codError'] = '70501';
		{ $resIniTrans['errorMensaje'] = 'Error en el sistema, no se ha podido iniciar la transación. ';
				$resIniTrans['numFilas'] = 0;							
				$resInsertar = $resIniTrans;
		 	$resActDatosSocio = $resIniTrans;
		}
		else //$resIniTrans['codError'] == '00000'
		{
		  $usuarioBuscado = $resValidarCamposForm['datosFormUsuario']['CODUSER']['valorCampo'];
				
	   //echo "<br><br>1-2 modeloSocios:actualizarDatosSocio:usuarioBuscado:"; print_r($usuarioBuscado);
			
			 //------------ Inicio: actualizar tabla MIEMBRO --------------------------------------------						
 
				//echo "<br><br>2-0 modeloTesorero:actualizaCuotaCCSocioTes:resValidarCamposForm['datosFormMiembro']: "; print_r($resValidarCamposForm['datosFormMiembro']);

    //unset($resInsertar['datosFormMiembro']['REMAIL']);	
						
				if ($resValidarCamposForm['datosFormMiembro']['EMAILERROR']['valorCampo']	== 'FALTA')																								
    { $resValidarCamposForm['datosFormMiembro']['EMAIL']['valorCampo' ]= 'falta'.$usuarioBuscado.'@falta.com';							
				}				
				
    //echo "<br><br>2-1 modeloTesorero:actualizaCuotaCCSocioTes:resValidarCamposForm['datosFormDomicilio']: "; print_r($resValidarCamposForm['datosFormDomicilio']);				
			 $resValidarCamposForm['datosFormMiembro'] = array_merge($resValidarCamposForm['datosFormMiembro'],$resValidarCamposForm['datosFormDomicilio']);
				
				unset($resValidarCamposForm['datosFormDomicilio']); 		 				
				//echo "<br><br>2-2 modeloTesorero:actualizaCuotaCCSocioTes:resValidarCamposForm['datosFormMiembro']: "; print_r($resValidarCamposForm['datosFormMiembro']);
				
				$reActMiembro = actualizMiembro('MIEMBRO',$usuarioBuscado,$resValidarCamposForm['datosFormMiembro'],$conexionDB['conexionLink']);//en modeloUsuarios.php						
		  //echo "<br><br>2-3 modeloTesorero:actualizaCuotaCCSocioTes: "; print_r($reActMiembro);				
				
    //------------Fin: actualizar  tabla MIEMBRO -----------------------------------------------
			
				if ($reActMiembro['codError']!=='00000')			
  	 {$resActDatosSocio = $reActMiembro;
	   }	
			 else //$reActMiembro['codError']=='00000')
			 {
					//---- Inicio determinar valores de $modoIngresoCuota y $ordenarCobrobanco ------
					
		   //--- Inicio if que controla si en el formulario hay CUENTAIBAN -----------------
					if ((isset($resValidarCamposForm['datosFormSocio']['CUENTAIBAN']['valorCampo'] ) &&  !empty($resValidarCamposForm['datosFormSocio']['CUENTAIBAN']['valorCampo'])) || 
									(isset($resValidarCamposForm['datosFormSocio']['CUENTANOIBAN']['valorCampo']) && !empty($resValidarCamposForm['datosFormSocio']['CUENTANOIBAN']['valorCampo']))
								)		 
				 { $modoIngresoCuota ='DOMICILIADA';

							if (isset($resValidarCamposForm['datosFormSocio']['CUENTANOIBAN']['valorCampo']) &&  !empty($resValidarCamposForm['datosFormSocio']['CUENTANOIBAN']['valorCampo']))        
       { $ordenarCobrobanco = 'NO';//puesto que CUENTANOIBAN no se pueden domiciliar por ahora		
         //echo "<br><br>2-4 modeloSocios:actualizarDatosSocio:resValidarCamposForm['datosFormCuotaSocio']: "; print_r($resValidarCamposForm['datosFormCuotaSocio']);						
							}
							elseif (isset($resValidarCamposForm['datosFormSocio']['CUENTAIBAN']['valorCampo']) && !empty($resValidarCamposForm['datosFormSocio']['CUENTAIBAN']['valorCampo'])) 								
							{							
								if($resValidarCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']['valorCampo'] ==0 )
								{//echo "<br><br>2-4a modeloTesorero:actualizaCuotaCCSocioTes: resValidarCamposForm['datosFormCuotaSocio']: "; print_r($resValidarCamposForm['datosFormCuotaSocio']);
									$ordenarCobrobanco = 'NO';	
								}
								//hasta aquí $ordenarCobroBanco es igual a  modeloSocios.php:actualizarDatosSocio
								else //hay CUENTAIBAN y IMPORTECUOTAANIOSOCIO != 0 //no EXENTO
								{//-- lo que haya elegido el tesorero
									//echo "<br><br>2-4b modeloTesorero:actualizaCuotaCCSocioTes: resValidarCamposForm['datosFormCuotaSocio']: "; print_r($resValidarCamposForm['datosFormCuotaSocio']);	
									$ordenarCobrobanco = $resValidarCamposForm['datosFormCuotaSocio']['ORDENARCOBROBANCO']['valorCampo'];			
								}
								/*if($resValidarCamposForm['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo'] =='NOABONADA-ERROR-CUENTA' || 
											$resValidarCamposForm['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo'] =='NOABONADA-DEVUELTA'
										)
								{echo "<br><br>2-4c modeloTesorero:actualizaCuotaCCSocioTes: resValidarCamposForm['datosFormCuotaSocio']: "; print_r($resValidarCamposForm['datosFormCuotaSocio']);	 
									$resValidarCamposForm['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo'] = 'PENDIENTE-COBRO';	
								}*/
						 }
	    }//if ((isset($resValidarCamposForm['datosFormSocio']['CUENTAIBAN']['valorCampo'] ) &&  ....		(isset($resValidarCamposForm['datosFormSocio']['CUENTANOIBAN']['valorCampo']) && !
					
		   else// !if ((isset($resValidarCamposForm['datosFormSocio']['CUENTAIBAN']['valorCampo'] ) &&  !empty($resValidarCamposForm['datosFormSocio']['CUENTAIBAN']['valorCampo'])) || 
		   {$modoIngresoCuota = 'SIN-DATOS';
						$ordenarCobrobanco = 'NO';	
		   }				
					//---- Fin determinar valores de $modoIngresoCuota y $ordenarCobrobanco --------
					
					//---- Inicio  actualizar SOCIO ------------------------------------------------
	    $resValidarCamposForm['datosFormSocio']['MODOINGRESO']['valorCampo'] =	$modoIngresoCuota;
					
	    $resValidarCamposForm['datosFormSocio']['IMPORTECUOTAANIOSOCIO']['valorCampo'] = $resValidarCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOSOCIO']['valorCampo'];
					
				 //echo "<br><br>2-4d modeloTesorero:actualizaCuotaCCSocioTes: resValidarCamposForm['datosFormSocio']: "; print_r($resValidarCamposForm['datosFormSocio']);
					
     //******************************								
					//----- Inicio buscar en BBDD ANIOCUOTA, IMPORTECUOTAANIOPAGADA y otros datos -------					
					// ojo: la función devuelve error si no encuentra ningún socio que cumpla la condición					 
					//---------------------------------------------------------------------------
					$codUsuario = $resValidarCamposForm['datosFormUsuario']['CODUSER']['valorCampo'];

					$resBuscarDatosSocio = buscarDatosSocio($codUsuario,"%");//"%"=todos los años de las cuotas del socio" en modeloSocios.php
	
					//echo "<br><br>2-5b modeloTesorero:actualizaCuotaCCSocioTes:resBuscarDatosSocio:"; print_r($resBuscarDatosSocio);
					if ($resBuscarDatosSocio['codError'] !== '00000')			
					{$resActDatosSocio = $resBuscarDatosSocio;
					}					
					else //$resBuscarDatosSocio['codError']=='00000')	
					{					
      //----- Fin buscar en BBDD ANIOCUOTA, IMPORTECUOTAANIOPAGADA y otros datos ---------

      //***********
      // -- Inicio PARA SEPA añadido 2015-01-10: para etiquetas SEPA "Secuencia del adeudo" <SeqTp> y "fecha firma" (fecha anotación CUENTAIBAN ) <DtOfSgntr>, si no hubiese fecha firma 	--------------------------
						//   NOTA: pudiera ser útil una función independiente, para poderla compartir

						//-------------------------------------------------------------------------------------------
				  if (isset($resValidarCamposForm['datosFormSocio']['CUENTAIBAN']['valorCampo']) && !empty($resValidarCamposForm['datosFormSocio']['CUENTAIBAN']['valorCampo']))
						{ 
					   // ------------------------Inicio  Ya había una CUENTAIBAN en la tabla SOCIO ----------------------------------------
	       if (isset($resBuscarDatosSocio['valoresCampos']['datosFormSocio']['CUENTAIBAN']['valorCampo']) && !empty($resBuscarDatosSocio['valoresCampos']['datosFormSocio']['CUENTAIBAN']['valorCampo'])) 
								{									
										if ($resBuscarDatosSocio['valoresCampos']['datosFormSocio']['CUENTAIBAN']['valorCampo'] !== $resValidarCamposForm['datosFormSocio']['CUENTAIBAN']['valorCampo'])	//Cuenta es distinta				
										{
												if (substr($resBuscarDatosSocio['valoresCampos']['datosFormSocio']['CUENTAIBAN']['valorCampo'],4,4) !== substr($resValidarCamposForm['datosFormSocio']['CUENTAIBAN']['valorCampo'],4,4))//distinta entidad bancaria,  												
												{//echo "<br><br>SEPA-a";														
													//Se ponen los valores de 'FRST' y fecha actual para etiquetas SEPA "Secuencia del adeudo" <SeqTp> y "fecha firma" <DtOfSgntr>, si no hubiese fecha firma 												
													$resValidarCamposForm['datosFormSocio']['SECUENCIAADEUDOSEPA']['valorCampo']		= 'FRST'; //distinta cuenta y distinta entidad bancaria: la etiqueta cobros SEPA "Secuencia del adeudo" <SeqTp>=FRST)			
													$resValidarCamposForm['datosFormSocio']['FECHAACTUALIZACUENTA']['valorCampo'] =	date('Y-m-d'); //fecha nuevo IBAN, se utilizará también para etiqueta cobros SEPA "fecha firma" <DtOfSgntr>, si no hubiese fecha firma 												
												}								
												else //misma entidad bancaria y distinta cuenta
												{//echo "<br><br>SEPA-b";														
												   //Se deja como esté, oues aunque la cuanta cambie no afecta a etiqueta SEPA "Secuencia del adeudo" <SeqTp> ni a SEPA "Secuencia del adeudo" <SeqTp>												
												}										
										}
									//else //cuenta igual se deja como esté
						  }	// ------------------------Fin  Ya había una CUENTAIBAN en la tabla SOCIO ----------------------------------------	
        else //En tabla SOCIO No había CUENTAIBAN	y a ahora se introduce una cuenta CUENTAIBAN	  
        {
									//Se ponen los valores de 'FRST' y fecha actual para etiquetas SEPA "Secuencia del adeudo" <SeqTp> y "fecha firma" <DtOfSgntr>, si no hubiese fecha firma 
								 //echo "<br><br>SEPA-c";
									$resValidarCamposForm['datosFormSocio']['SECUENCIAADEUDOSEPA']['valorCampo']		= 'FRST';	//distinta cuenta y distinta entidad bancaria: la etiqueta cobros SEPA "Secuencia del adeudo" <SeqTp>=FRST)			
									$resValidarCamposForm['datosFormSocio']['FECHAACTUALIZACUENTA']['valorCampo'] =	date('Y-m-d'); //fecha nuevo IBAN, se utilizará también para etiqueta cobros SEPA "fecha firma" <DtOfSgntr>, si no hubiese fecha firma 																										
								}									
						}	//if (isset($resValidarCamposForm['datosFormSocio']['CUENTAIBAN']['valorCampo']) &&  !empty($resValidarCamposForm['datosFormSocio']['CUENTAIBAN']['valorCampo']))
						
					 elseif (isset($resValidarCamposForm['datosFormSocio']['CUENTANOIBAN']['valorCampo']) && !empty($resValidarCamposForm['datosFormSocio']['CUENTANOIBAN']['valorCampo']))
						{ 
						  // ------------------------Inicio  Ya había una CUENTANOIBAN en la tabla SOCIO ----------------------------------------						
							 if (isset($resBuscarDatosSocio['valoresCampos']['datosFormSocio']['CUENTANOIBAN']['valorCampo']) && !empty($resBuscarDatosSocio['valoresCampos']['datosFormSocio']['CUENTANOIBAN']['valorCampo'])) 
								{ //echo "<br><br>SEPA-e";
          if ($resBuscarDatosSocio['valoresCampos']['datosFormSocio']['CUENTANOIBAN']['valorCampo'] !== $resValidarCamposForm['datosFormSocio']['CUENTANOIBAN']['valorCampo'])	//Cuenta es distinta				
										{									
							    //Hay cuenta 	CUENTANOIBAN	y es distina la nueva
							    //echo "<br><br>SEPA-f";
							    $resValidarCamposForm['datosFormSocio']['SECUENCIAADEUDOSEPA']['valorCampo']		= NULL; //no hay cuenta IBAN			
							    $resValidarCamposForm['datosFormSocio']['FECHAACTUALIZACUENTA']['valorCampo'] =	date('Y-m-d');	//Hay cuenta CUENTANOIBAN	
										}
										else //misma  cuenta CUENTANOIBAN
										{//echo "<br><br>SEPA-g";														
											//Se deja como esté, pues aunque la cuanta cambie no afecta a etiqueta SEPA "Secuencia del adeudo" <SeqTp> ni a SEPA "Secuencia del adeudo" <SeqTp>												
										}			
        }	// ------------------------Fin  Ya había una CUENTANOIBAN en la tabla SOCIO ----------------------------------------	
        else
        {	//En tabla SOCIO No había CUENTANOIBAN	y a ahora se introduce una cuenta CUENTANOIBAN	 
										//echo "<br><br>SEPA-h";
										$resValidarCamposForm['datosFormSocio']['SECUENCIAADEUDOSEPA']['valorCampo']		= NULL; //no hay cuenta IBAN
										$resValidarCamposForm['datosFormSocio']['FECHAACTUALIZACUENTA']['valorCampo'] =	date('Y-m-d');	//Hay cuenta CUENTANOIBAN
								}									
						}//elseif (isset($resValidarCamposForm['datosFormSocio']['CUENTANOIBAN']['valorCampo']) && !empty($resValidarCamposForm['datosFormSocio']['CUENTANOIBAN']['valorCampo']))
						
      else //en el formulario No hay cuenta CUENTAIBAN		ni CUENTANOIBAN	
      {
							//echo "<br><br>SEPA-i";
							$resValidarCamposForm['datosFormSocio']['SECUENCIAADEUDOSEPA']['valorCampo']		= NULL; 
							$resValidarCamposForm['datosFormSocio']['FECHAACTUALIZACUENTA']['valorCampo'] =	'0000-00-00';	
						}		
      // -- Fin PARA SEPA añadido 2015--1-10: para etiquetas SEPA "Secuencia del adeudo" <SeqTp> FRST, RCUR y "fecha firma" <DtOfSgntr>, si no hubiese fecha firma 		--------------------------
      //+++++++++++++*************
			
	     //echo "<br><br>2-5c-1 modeloTesorero:actualizaCuotaCCSocioTes: resValidarCamposForm[datosFormSocio]: "; print_r($resValidarCamposForm['datosFormSocio']);	 
						$reActSocio = actualizSocio('SOCIO',$usuarioBuscado,$resValidarCamposForm['datosFormSocio'],$conexionDB['conexionLink']);//en modeloSocios.php
						//echo "<br><br>2-5c-2 modeloTesorero:actualizaCuotaCCSocioTes: reActSocio: "; print_r($reActSocio);
					
						// --- fin actualizar SOCIO ------------------			
						
						if ($reActSocio['codError']!=='00000')			
						{ //echo "<br><br>2-6a";
								$resActDatosSocio=$reActSocio;
						}	
						else //$reActSocio['codError']=='00000')
						{//echo "<br><br>2-6b";
							if	(isset($resValidarCamposForm['datosFormSocio']['CODSOCIO']['valorCampo']) && 	!empty($resValidarCamposForm['datosFormSocio']['CODSOCIO']['valorCampo'])
										)		
							{//echo "<br><br>2-6c";	
								$codSocio = $resValidarCamposForm['datosFormSocio']['CODSOCIO']['valorCampo'];
							}
							elseif (isset($_SESSION['vs_CODSOCIO']) && $_SESSION['vs_CODSOCIO']!==NULL && $_SESSION['vs_CODSOCIO']!=='')
							{//echo "<br><br>2-6d";
								$codSocio=$_SESSION['vs_CODSOCIO'];//para cuando el tesorero se actualiza sus propios datos????
							}
							else 
							{//echo "tratar error, pero no se debe producir";
							}		
							// $codSocio=$resValidarCamposForm['datosFormSocio']['CODSOCIO']['valorCampo']; acaso sea mejor solo este																																																			
						
							//----- Inicio Preparar campos para actualizar o insertar en  CUOTAANIOSOCIO ----
							
							$resValidarCamposForm['datosFormCuotaSocio']['CODAGRUPACION']['valorCampo']=$resValidarCamposForm['datosFormSocio']['CODAGRUPACION']['valorCampo'];		//se asigna nueva agrupacion 
							$resValidarCamposForm['datosFormCuotaSocio']['CODCUOTA']['valorCampo']=$resValidarCamposForm['datosFormSocio']['CODCUOTA']['valorCampo'];		//se asigna nueva cuota
							$resValidarCamposForm['datosFormCuotaSocio']['MODOINGRESO']['valorCampo'] = $modoIngresoCuota;
							$resValidarCamposForm['datosFormCuotaSocio']['ORDENARCOBROBANCO']['valorCampo'] =	$ordenarCobrobanco;  
									
							//echo "<br><br>2-6e modeloTesorero:actualizaCuotaCCSocioTes:resValidarCamposForm['datosFormCuotaSocio']:"; print_r($resValidarCamposForm['datosFormCuotaSocio']);
									
							/*------- Actualizar ESTADOCUOTA --------------------------------							
								
							//----- Inicio buscar ANIOCUOTA, IMPORTECUOTAANIOPAGADA y otros datos !Se ha subido a mas arriba¡ ------------
							// ojo: la función devuelve error si no encuentra ningún socio que cumpla la condición					 
							//-------------------------------------------------------------------------------
							$codUsuario = $resValidarCamposForm['datosFormUsuario']['CODUSER']['valorCampo'];
							require_once './modelos/modeloSocios.php'; 
							$resBuscarDatosSocio = buscarDatosSocio($codUsuario,"%");//"%"=todos los años de las cuotas del socio"							
							if ($resBuscarDatosSocio['codError']!=='00000')			
							{	$resActDatosSocio = $resBuscarDatosSocio;
							}	
							else  //$resBuscarDatosSocio['codError'] =='00000')	
							{*///----- Fin buscar ANIOCUOTA, IMPORTECUOTAANIOPAGADA y otros datos ------------

						 if (isset($resBuscarDatosSocio['valoresCampos']['datosFormCuotaSocio'][date('Y')+1]))//si ya tiene cuota año Y+1
	      { $anioCuota = date('Y')+1;
							  //echo "<br><br>3-a modeloTesorero:actualizaCuotaCCSocioTes:anioCuota: ";print_r($anioCuota);														
							} 
							else
							{ $anioCuota = date('Y');
							  //echo "<br><br>3-b modeloTesorero:actualizaCuotaCCSocioTes:anioCuota: ";print_r($anioCuota);													 
							}	
																																
							if ($resBuscarDatosSocio['valoresCampos']['datosFormCuotaSocio'][$anioCuota]['ESTADOCUOTA']['valorCampo']!=="ABONADA")//nuevo General,Joven,Parado																									
				   {
								//--- Inicio actualizar /insertar en "CUOTAANIOSOCIOS" campos CODCUOTA,ESTADOCUOTA,IMPORTECUOTAANIOEL y otros ---
							 
								// Según la asignación del if anterior $anioCuota=Y o $anioCuota=Y+1
							 // Si $anioCuota=Y y ESTADOCUOTA!==ABONADA, se actualizará la fila "Y" 
								// Si $anioCuota=Y y ESTADOCUOTA==ABONADA, y no hay fila "Y+1" se insertará la fila "Y" 
							 // Si $anioCuota=Y+1, siempre ESTADOCUOTA!==ABONADA, se actualizará la fila "Y+1" 
						  //----------------------------------------------------------------------------------------------------------------								
							 //echo "<br><br>3-c modeloTesorero:actualizaCuotaCCSocioTes:resBuscarDatosSocio['valoresCampos']['datosFormCuotaSocio'][anioCuota]['ESTADOCUOTA']['valorCampo']:";
							 //print_r($resBuscarDatosSocio['valoresCampos']['datosFormCuotaSocio'][date('Y')]['ESTADOCUOTA']['valorCampo']);		
								
								if ($resValidarCamposForm['datosFormCuotaSocio']['CODCUOTA']['valorCampo'] =="Honorario")
								{ 
							   $resValidarCamposForm['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo'] ='EXENTO';
								  $resValidarCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOEL']['valorCampo'] ='0.00';
								}
								else ///resValidarCamposForm['datosFormCuotaSocio']['CODCUOTA']['valorCampo']==General, Parado, Joven,
								{ 
								  if ($resBuscarDatosSocio['valoresCampos']['datosFormCuotaSocio'][$anioCuota]['IMPORTECUOTAANIOPAGADA']['valorCampo']==0)
									 {//echo '<br /><br />3-d';	
											//-- Para $anioCuota=Y puede = 0, pero también puede estar pagada en parte y tener valores !=0,
										 // Para $anioCuota=Y+1		siempre será =0																		

	          //$resValidarCamposForm['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo'] = 'PENDIENTE-COBRO';
											//En caso de previo ESTADOCUOTA: Devuelta o error,  Si el tesorero actualiza estos datos, supongo que coorrige la situación de error en cuenta, y se podrá cobra de nuevo
											//Dejo 'ESTADOCUOTA' con los que tuviese que sería:  PENDIENTE-COBRO
				       
											//--			En el caso de que 	NOABONADA-ERROR-CUENTA' o 'NOABONADA-DEVUELTA' aplicamos la siguiente condición: 					
											if($resValidarCamposForm['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo'] == 'NOABONADA-ERROR-CUENTA' || 
														$resValidarCamposForm['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo'] == 'NOABONADA-DEVUELTA'
													)
											{//echo "<br><br>7-3d mmodeloSocios:actualizarDatosSocios";	
												if ($resValidarCamposForm['datosFormSocio']['CUENTAIBAN']['valorCampo'] !== $resBuscarDatosSocio['valoresCampos']['datosFormSocio']['CUENTAIBAN']['valorCampo'] )
												{//echo "<br><br>7-3e mmodeloSocios:actualizarDatosSocios";	
													$resValidarCamposForm['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo'] = 'PENDIENTE-COBRO';
												 //al cambiar la cuenta el socio, si no está pagada pondré 'PENDIENTE-COBRO' (interpreto que la nueva cuenta CUENTAIBAN introducida por el socio es correcta y tiene fondos )	
												 //y si es igual cuanta que la anterior (sigue siendo errónea) y no la cambio porque esta anotada por tesorero como NOABONADA-ERROR-CUENTA' o 'NOABONADA-DEVUELTA'
												 // Para CUENTANOIBAN no lo cambio, lo dejo como estaba 
												}
											}
          //--				
										}
										elseif ($resBuscarDatosSocio['valoresCampos']['datosFormCuotaSocio'][$anioCuota]['IMPORTECUOTAANIOPAGADA']['valorCampo'] 
	                 >= $resValidarCamposForm['datosFormCuotaSocio']['IMPORTECUOTAANIOEL']['valorCampo'])
																		//-- Puede suceder que al cambiar CODCUOTA General->Parado,Joven y ESTADOCUOTA=ABONADA-PARTE
																		// el importe pagado parcialmente sea superior a correspondiente a esa cuota 
	 								{//echo '<br /><br />3-e';			
										 $resValidarCamposForm['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo']='ABONADA';				
												
											$resValidarCamposForm['datosFormCuotaSocio']['FECHAANOTACION']['valorCampo'] = date('Y-m-d');											
											$resValidarCamposForm['datosFormCuotaSocio']['FECHAPAGO']['valorCampo'] = date('Y-m-d');
											
											$resValidarCamposForm['datosFormCuotaSocio']['OBSERVACIONES']['valorCampo'] = 
											"Mensaje automático del programa: Estaba ABONADA-PARTE y había pagado ".$resBuscarDatosSocio['valoresCampos']['datosFormCuotaSocio'][$anioCuota]['IMPORTECUOTAANIOPAGADA']['valorCampo'].
											" euros y al cambiar de tipo de cuota a - ".$resValidarCamposForm['datosFormCuotaSocio']['CODCUOTA']['valorCampo'].
											" - ha quedado como pagada, con fecha ".date('Y-m-d');
										}
										else //IMPORTECUOTAANIOPAGADA']<['IMPORTECUOTAANIOEL']-> importe pagado sea inferior al correspondiente a esa cuota 									
										{//echo '<br /><br />3-f'; 
										 $resValidarCamposForm['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo']='ABONADA-PARTE';										
										}									
								}//else //CODCUOTA==General, Parado, Joven,,	  
				    //echo "<br><br>3-g modeloSocios:actualizaCuotaCCSocioTes:resValidarCamposForm['datosFormCuotaSocio']: "; print_r($resValidarCamposForm['datosFormCuotaSocio']);	
										
		      $reActCuotaAnioSocio = actualizCuotaAnioSocio('CUOTAANIOSOCIO',$codSocio,$resValidarCamposForm['datosFormCuotaSocio'],$conexionDB['conexionLink']);//en modeloSocios.php 
								
				    //echo "<br><br>3-h modeloSocios:actualizaCuotaCCSocioTes: reActCuotaAnioSocio: "; print_r($reActCuotaAnioSocio);	
						 }//if ($resValidarCamposForm['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo'] !=="ABONADA")                                 
							else //==ABONADA 
							{
								//--- Inicio insertar fila fila "Y+1"  en "CUOTAANIOSOCIOS" -------------------------------------------	
												 
							 if ($resValidarCamposForm['datosFormSocio']['CODCUOTA']['valorCampo']=='General')
								{$resValidarCamposForm['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo']='PENDIENTE-COBRO';	
								}
								elseif ($resValidarCamposForm['datosFormSocio']['CODCUOTA']['valorCampo']=='Honorario')//no entrará aquí nunca
								{$resValidarCamposForm['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo']='EXENTO';		
								}
								else //CODCUOTA== Joven, Parado
								{$resValidarCamposForm['datosFormCuotaSocio']['ESTADOCUOTA']['valorCampo']='PENDIENTE-COBRO';			 							
								}			
								
						  $arrValoresInserCuota = $resValidarCamposForm['datosFormCuotaSocio'];
								$arrValoresInserCuota['CODSOCIO']['valorCampo'] = $codSocio;							
	
			     //echo "<br><br>3-i modeloTesorero:actualizaCuotaCCSocioTes:arrValoresInserCuota:";print_r($arrValoresInserCuota);
						  
								$reActCuotaAnioSocio = insertarCuotaAnioSocio($arrValoresInserCuota,$conexionDB['conexionLink']);//Para EL PRÓXIMO AÑO en modeloSocios.php
				    //echo "<br><br>3-j modeloTesorero:actualizaCuotaCCSocioTes:reActCuotaAnioSocio:"; print_r($reActCuotaAnioSocio);							
				   }//else ==ABONADA 
	      
							//echo "<br><br>3-k modeloTesorero:actualizaCuotaCCSocioTes:reActCuotaAnioSocio:"; 
							
						 //--- Fin actualizar /insertar en "CUOTAANIOSOCIOS" campos CODCUOTA,ESTADOCUOTA,IMPORTECUOTAANIOEL y otros ---				
						
					  if ($reActCuotaAnioSocio['codError']!=='00000')			
		   	 {$resActDatosSocio = $reActCuotaAnioSocio;
				   }	
						 else //$reActCuotaAnioSocio['codError']=='00000')
						 {//echo "<br><br>4-1 modeloTesorero:actualizaCuotaCCSocioTes:reActCuotaAnioSocio:";print_r(reActCuotaAnioSocio);

								/*---------------- Inicio COMMIT --------------------------------*/												
								$resFinTrans = commitPDO($conexionDB['conexionLink']);
								
								//echo "<br><br>4-2 modeloTesorero:actualizaCuotaCCSocioTes:resActDatosSocio::resFinTrans: ";var_dump($resFinTrans);													
								if ($resFinTrans['codError'] !== '00000')//será $resFinTrans['codError'] = '70502'; 									
								{ $resFinTrans['errorMensaje'] = 'Error en el sistema, no se ha podido finalizar transación. ';
										$resFinTrans['numFilas'] = 0;	
										$arrMensaje['textoComentarios'] = 'Error en el sistema, no se ha podido da de alta al socio/a. Pruebe de nuevo pasado un tiempo. ';				
										
										$resActDatosSocio = $resFinTrans;	
								  //echo "<br><br>4-3 modeloTesorero:actualizaCuotaCCSocioTes:resActDatosSocio: ";print_r($resActDatosSocio);
								}											
							 else
							 { //$_SESSION['vs_NOMUSUARIO']=$resValidarCamposForm['datosFormUsuario']['USUARIO']['valorCampo'];
									 $arrMensaje['textoComentarios'] = "Se han actualizado los datos del socio <br /><br />";
								  //.$resValidarCamposForm['datosFormMiembro']['NOM']['valorCampo'];	//si se quiere poner el nombre		
							 }
						 }//$reActCuotaAnioSocio['codError']=='00000')
					 }//$reActSocio['codError']=='00000')		
			  }//else //$resBuscarDatosSocio['codError']=='00000')				
			
		   //echo "<br><br>4-4 modeloTesorero:actualizaCuotaCCSocioTes:resActDatosSocio: ";print_r($resActDatosSocio);echo "<br>";
		
					if ($resActDatosSocio['codError']!=='00000')
					{ //echo "<br><br>5-1 modeloTesorero:actualizaCuotaCCSocioTes:resActDatosSocio: ";print_r($resActDatosSocio);echo "<br>";
				
						$deshacerTrans = "ROLLBACK"; //$sql = "ROLLBACK TO SAVEPOINT transEliminarSocio";
						$resDeshacerTrans = mysql_query($deshacerTrans,$conexionUsuariosDB['conexionLink']);	
						if (!$resDeshacerTrans)
						{ $resDeshacerTrans['codError'] = '70503';   
								$resDeshacerTrans['errno'] = mysql_errno(); 
								$resDeshacerTrans['errorMensaje'] = 'Error en el sistema, no se ha podido deshacer la transación. '.'Error mysql_query '.mysql_error();
								$resDeshacerTrans['numFilas'] = 0;	
						
								$resActDatosSocio = $resDeshacerTrans;
								//echo "<br><br>5-2 modeloTesorero:actualizaCuotaCCSocioTes:resActDatosSocio: ";print_r($resActDatosSocio);echo "<br>";	
						}	 
						$arrMensaje['textoComentarios'] ="Error del sistema al intentar actualizar los datos del socio, vuelva a intentarlo pasado un tiempo ";
					
						require_once './modelos/modeloErrores.php'; //si es un error de conexión a la tabla error, insertar errores 
						$resInsertarErrores=insertarError($resActDatosSocio);	
						
						if ($resInsertarErrores['codError']!=='00000')
						{$resActDatosSocio['errorMensaje'] .= $resInsertarErrores['errorMensaje'];
							$arrMensaje['textoComentarios'].="Error del sistema al tratar ERRORES, vuelva a intentarlo pasado un tiempo ";
						}		
					}//if ($resActDatosSocio['codError']!=='00000')
			 }//$reActMiembro['codError']=='00000')	
  }//$resIniTrans
		
		//echo "<br><br>6 modeloTesorero:actualizaCuotaCCSocioTes:resActDatosSocio:";print_r($resActDatosSocio);echo "<br>";
	}//$conexionUsuariosDB['codError']=="00000"		
	
	$resActDatosSocio['arrMensaje'] = $arrMensaje;	
	
 return 	$resActDatosSocio; 	
}
//----------------------------- Fin actualizaCuotaCCSocioTes --------------------------------------- 

/*=== FIN FUNCIONES PARA MOSTRAR, ACTUALIZAR PAGOS Y CUOTAS SOCIOS  ================================

==================================================================================================*/	


/*============================== INICIO MOSTRAR TABLAS TOTALES PAGOS CUOTAS ========================

==================================================================================================*/	

/*---------------------------- Inicio buscarTotalesAniosPagosCuota ---------------------------------
Ejecuta una consulta para busca los cuotas pagadas y no pagadas de los socios en todos los años.
Incluye las bajas del año, ya que pudieran haber pagado previamente a la baja. 
Los datos de la consulta los trata y prepara para la mostrar los totales de cuotas de los años, 
mediante varios acumuladores y totalizadores, de por diferentes conceptos de información: 

$acumuladorPagosAnio['numSociosAnio'] 
$acumuladorPagosAnio['IMPORTECUOTAANIOPAGADA']		
$acumuladorPagosAnio['totalAnioAbonada']		
.......
$totalPagosCuotasAnios.

Al final retorna la información en formato array listo para mostrarlo en el formulario 
vistas/tesorero/vTotalesCuotasInc.php

LLAMADA: cTesorero:mostrarTotalesCuotas()

LLAMA: modeloTesorero.php:cadBuscarTotalesPagosCuotaAgrup(),
modelos/BBDD/MySQL/modeloMySQL.php:buscarCadSql()	 
modelos/modeloErrores.php	

OBSERVACIONES: incluye PDO, $arrBind y modificaciones para PHP 7.3.21
--------------------------------------------------------------------------------------------------*/
function buscarTotalesAniosPagosCuota($codAgrupPagoCuota,$anioCuotas,$estadoSocio,$estadoCuota)
{
	//echo "<br><br>0-1 modeloTesorero:buscarTotalesAniosPagosCuota:anioCuotas: ";print_r($anioCuotas);
 
	$totalPagosCuotasAnios = array();
	$reTotalPagosCuotasAnios = array();
	
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	require_once "./modelos/BBDD/MySQL/conexionMySQL.php";			
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);

	if ($conexionDB['codError'] !== '00000')	
	{ 
   $reTotalPagosCuotasAnios  = $conexionDB;
	}
	else	
	{ //Nota: en lugar de utilizar esta función ya existente se podría hacer una específica más ligera

			$arrBuscarTotalesPagosCuotaAgrup = cadBuscarTotalesPagosCuotaAgrup($codAgrupPagoCuota,$anioCuotas,$estadoSocio,$estadoCuota);//modeloTesorero.php:
	  //echo "<br><br>1 modeloTesorero:buscarTotalesAniosPagosCuota:arrBuscarTotalesPagosCuotaAgrup: ";print_r($arrBuscarTotalesPagosCuotaAgrup);		
	  
			$cadSelectBuscarCuotas = $arrBuscarTotalesPagosCuotaAgrup['cadSQL'];

	  $arrBind = $arrBuscarTotalesPagosCuotaAgrup['arrBindValues'];					
	
   $resBuscarCuotas = buscarCadSql($cadSelectBuscarCuotas,$conexionDB['conexionLink'],$arrBind);//en modeloMySQL.php	
			//echo "<br><br>2 modeloTesorero:buscarTotalesAniosPagosCuota:resBuscarCuotas: ";print_r($resBuscarCuotas);		
	  
			if ($resBuscarCuotas['codError'] !== '00000')
			{			 
				$reTotalPagosCuotasAnios = $resBuscarCuotas;		
				$reTotalPagosCuotasAnios['textoCabecera'] = 'Totales pagos cuotas por años';
				$reTotalPagosCuotasAnios['textoComentarios'] =".Error del sistema, vuelva a intentarlo pasado un tiempo ";
				
				require_once './modelos/modeloErrores.php';		
				insertarError($reTotalPagosCuotasAnios,$conexionDB['conexionLink']);		
			}	
			else
			{//----------- Inicio Puesta a cero de acumuladores ----------------
		
				$acumuladorPagosAnio['numSociosAnio'] = 0;
				$acumuladorPagosAnio['IMPORTECUOTAANIOPAGADA'] = 0;
							
				$acumuladorPagosAnio['totalAnioCuotasEL'] = 0;								
				$acumuladorPagosAnio['totalAnioAbonada'] = 0;				
				$acumuladorPagosAnio['totalGastosPagosAnio'] = 0;					
				$acumuladorPagosAnio['numSociosAnioAbonada'] = 0;	
				$acumuladorPagosAnio['totalAnioNoAbonada_y_PendienteCobro'] = 0;
				$acumuladorPagosAnio['numSociosNoAbonadaAnio_y_PendientesCobro'] = 0;
    $acumuladorPagosAnio['totalAnioDevuelta'] = 0;
				$acumuladorPagosAnio['numSociosDevuelta'] = 0;
    $acumuladorPagosAnio['totalAnioErrorCuenta'] = 0;
				$acumuladorPagosAnio['numSociosErrorCuenta'] = 0;
    $acumuladorPagosAnio['totalAnioAbonaParte'] = 0;
				$acumuladorPagosAnio['numSociosAbonaParte'] = 0;
				$acumuladorPagosAnio['totalSociosAnioExentos'] = 0;
    $acumuladorPagosAnio['numSociosAnioExentos'] = 0;
				$acumuladorPagosAnio['totalAnioCuotaDonacion'] = 0;
				$acumuladorPagosAnio['numSociosAnioCuotaDonacion'] = 0;	

				$acumuladorPagosAnio['totalDOMICILIADA'] = 0;
				$acumuladorPagosAnio['numDOMICILIADA'] = 0;
				$acumuladorPagosAnio['totalPAYPAL'] = 0;	
				$acumuladorPagosAnio['numPAYPAL'] = 0;
				$acumuladorPagosAnio['totalTRANSFERENCIA'] = 0;
				$acumuladorPagosAnio['numTRANSFERENCIA'] = 0;
				$acumuladorPagosAnio['totalMETALICO'] = 0;	
				$acumuladorPagosAnio['numMETALICO'] = 0;		
				$acumuladorPagosAnio['totalSIN-DATOS'] = 0;	
				$acumuladorPagosAnio['numSIN-DATOS'] = 0;

				$acumuladorPagosAnio['numGeneral'] = 0;
				$acumuladorPagosAnio['totalGeneral'] = 0;
				$acumuladorPagosAnio['numParado'] = 0;
				$acumuladorPagosAnio['totalParado'] = 0;				
				$acumuladorPagosAnio['numJoven'] = 0;
				$acumuladorPagosAnio['totalJoven'] = 0;				
				$acumuladorPagosAnio['numHonorario'] = 0;		
				$acumuladorPagosAnio['totalHonorario'] = 0;				
			
				$acumuladorPagosAnio['totalAnioDefictGastosPayPal'] = 0; 
				$acumuladorPagosAnio['numSociosAnioDefictGastosPayPal'] = 0;
		  //----------- Fin Puesta a cero de acumuladores ------------------
				
				//$yearActual = $resBuscarCuotas['resultadoFilas'][0]['ANIOCUOTA'];//no sirve puede ser un año > que actual
				
				$yearActual = date('Y');				
				$yearContador	= $yearActual;		
    //$yearFin = $yearActual - 5;			//$yearFin = 2009;	//No son útiles				
				
				//-------- Inicio bucle foreach ----------------------------------------------	
				foreach ($resBuscarCuotas['resultadoFilas'] as  $fila => $contenidoFila)
				{
					//echo "<br><br>fila=";print_r($fila);echo "contenido=";print_r($contenidoFila);
					
					if ($contenidoFila['ANIOCUOTA'] <= $yearActual)
				 {
						if ($contenidoFila['ANIOCUOTA'] !== $yearContador )
						{ 					  
	       $totalPagosCuotasAnios[$yearContador] = $acumuladorPagosAnio;
								
								foreach ($acumuladorPagosAnio as $campoAcumulador => $valCampoAcumulador )//poner acumuladores a 0
								{	$acumuladorPagosAnio[$campoAcumulador] = 0;
								}								
								$yearContador = $contenidoFila['ANIOCUOTA'];								
						}
						
				 	$acumuladorPagosAnio['numSociosAnio']++ ;
						
					 //suma de las cuotas obligatorias que debieran que pagar todos los socios (cada uno según CODCUOTA de socio:General,Parado,Joven)				  
				  $acumuladorPagosAnio['totalAnioCuotasEL']      += $contenidoFila['IMPORTECUOTAANIOEL'];
						
						//suma de todas las cuotas pagadas por los socios, incluyen todos los tipos de cuota, pagadas parte, exentos que pagan y también las cuotas+donción, y la real de Paypal
      $acumuladorPagosAnio['IMPORTECUOTAANIOPAGADA'] += $contenidoFila['IMPORTECUOTAANIOPAGADA'];
			   
						$acumuladorPagosAnio['totalGastosPagosAnio'] += $contenidoFila['IMPORTEGASTOSABONOCUOTA'];//nuevo	
      
						//------------------	Acumuladores según tipo cuota ---------------						
						switch ($contenidoFila['CODCUOTA'])
						{case 'General':
														$acumuladorPagosAnio['totalGeneral'] += $contenidoFila['IMPORTECUOTAANIOPAGADA'];
														$acumuladorPagosAnio['numGeneral'] ++;
														break;				 
							case 'Parado':
														$acumuladorPagosAnio['totalParado'] += $contenidoFila['IMPORTECUOTAANIOPAGADA'];
														$acumuladorPagosAnio['numParado'] ++;
														break;
							case 'Joven':
														$acumuladorPagosAnio['totalJoven'] += $contenidoFila['IMPORTECUOTAANIOPAGADA'];
														$acumuladorPagosAnio['numJoven'] ++;
														break; 																
							case 'Honorario':
														$acumuladorPagosAnio['totalHonorario'] += $contenidoFila['IMPORTECUOTAANIOPAGADA'];
														$acumuladorPagosAnio['numHonorario'] ++;
														break;								
						}
      //----------------------------------------------------------------							
						
						//-- Inicio	Acumuladores ESTADOCUOTA=ABONADA y según modo ingreso --		
						if ($contenidoFila['ESTADOCUOTA'] == 'ABONADA')
						{ $acumuladorPagosAnio['numSociosAnioAbonada']++ ;								

								//suma de las cuotas obligatorias de con ESTADO=ABONADA aunque haya abonado más como donación
								$acumuladorPagosAnio['totalAnioAbonada'] += $contenidoFila['IMPORTECUOTAANIOEL'];	
        
								
								switch ($contenidoFila['MODOINGRESO'])
								{ case 'DOMICILIADA':
																$acumuladorPagosAnio['totalDOMICILIADA'] += $contenidoFila['IMPORTECUOTAANIOPAGADA'];
																$acumuladorPagosAnio['numDOMICILIADA'] ++;
																break; 
										case 'PAYPAL':
																$acumuladorPagosAnio['totalPAYPAL'] += $contenidoFila['IMPORTECUOTAANIOPAGADA'];
																$acumuladorPagosAnio['numPAYPAL'] ++;
																break;
										case 'TRANSFERENCIA':
																$acumuladorPagosAnio['totalTRANSFERENCIA'] += $contenidoFila['IMPORTECUOTAANIOPAGADA'];
																$acumuladorPagosAnio['numTRANSFERENCIA'] ++;
																break;
										case 'METALICO':
																$acumuladorPagosAnio['totalMETALICO'] += $contenidoFila['IMPORTECUOTAANIOPAGADA'];
																$acumuladorPagosAnio['numMETALICO'] ++;
																break;
										case 'SIN-DATOS':
																$acumuladorPagosAnio['totalSIN-DATOS'] += $contenidoFila['IMPORTECUOTAANIOPAGADA'];
																$acumuladorPagosAnio['numSIN-DATOS'] ++;
																break;																
								}        		
						}//if ($contenidoFila['ESTADOCUOTA'] == 'ABONADA')						
      //------ Fin	Acumuladores ESTADOCUOTA=ABONADA y según modo ingreso -----		
						
						//------ Inicio	Acumuladores otros ESTADOCUOTA --------------------------		
						if ($contenidoFila['ESTADOCUOTA'] == 'ABONADA-PARTE')
						{ $acumuladorPagosAnio['totalAnioAbonaParte'] += $contenidoFila['IMPORTECUOTAANIOPAGADA'];
						  $acumuladorPagosAnio['numSociosAbonaParte']++ ;
        $acumuladorPagosAnio['numSociosNoAbonadaAnio_y_PendientesCobro']++ ;//LE CONSIDERO QUE NO HA PAGADO
								$acumuladorPagosAnio['totalAnioNoAbonada_y_PendienteCobro'] += $contenidoFila['IMPORTECUOTAANIOEL'];								
						}
						
						if ($contenidoFila['ESTADOCUOTA'] == 'EXENTO')
						{ 
						  $acumuladorPagosAnio['numSociosAnioExentos']++ ;
						}						
																
						if ($contenidoFila['IMPORTECUOTAANIOPAGADA'] > $contenidoFila['IMPORTECUOTAANIOEL'])//Pagan más que su cuota correspondiente: Dona en cuota, o exento que paga	
						{if ($contenidoFila['ESTADOCUOTA'] == 'EXENTO')
						 {$acumuladorPagosAnio['totalSociosAnioExentos'] += $contenidoFila['IMPORTECUOTAANIOPAGADA'];						  
						 }
							else
							{$acumuladorPagosAnio['totalAnioCuotaDonacion'] += $contenidoFila['IMPORTECUOTAANIOPAGADA'] - $contenidoFila['IMPORTECUOTAANIOEL'];//es la parte que dona en la cuota
								$acumuladorPagosAnio['numSociosAnioCuotaDonacion']++ ;
							}	
						}
													
						if ($contenidoFila['ESTADOCUOTA'] == 'PENDIENTE-COBRO' || $contenidoFila['ESTADOCUOTA'] == 'NOABONADA')
						{ $acumuladorPagosAnio['totalAnioNoAbonada_y_PendienteCobro'] += $contenidoFila['IMPORTECUOTAANIOEL'];
								$acumuladorPagosAnio['numSociosNoAbonadaAnio_y_PendientesCobro']++ ;
						}
					
						if ($contenidoFila['ESTADOCUOTA'] == 'NOABONADA-DEVUELTA')	//=if ($contenidoFila['ESTADOCUOTA'] == 'DEVUELTA')
						{ $acumuladorPagosAnio['totalAnioDevuelta'] += $contenidoFila['IMPORTECUOTAANIOEL'];
						  $acumuladorPagosAnio['numSociosDevuelta']++ ;
						}	
						
						if ($contenidoFila['ESTADOCUOTA'] == 'NOABONADA-ERROR-CUENTA')//=if ($contenidoFila['ESTADOCUOTA'] == 'ERROR-CUENTA')
						{ $acumuladorPagosAnio['totalAnioErrorCuenta'] += $contenidoFila['IMPORTECUOTAANIOEL'];
						  $acumuladorPagosAnio['numSociosErrorCuenta']++ ;
						}
						//------ Fin	Acumuladores otros ESTADOCUOTA ----------------------------		
						
					}//if ($contenidoFila['ANIOCUOTA'] <= $yearActual)			
			
				}//-------- Fin bucle foreach ------------------------------------------------			
				
				$totalPagosCuotasAnios[$yearContador] =  $acumuladorPagosAnio;//Para que no se pierda el último año
				
				$reTotalPagosCuotasAnios['codError'] = '00000';
				$reTotalPagosCuotasAnios['resultadoFilas'] = $totalPagosCuotasAnios;	
				
				//echo '<br><br>3 modeloTesorero:buscarTotalesAniosPagosCuota:reTotalPagosCuotasAnios: ';print_r($reTotalPagosCuotasAnios);
			}			
		}
	//echo '<br><br>4 modeloTesorero:buscarTotalesAniosPagosCuota:reTotalPagosCuotasAnios: ';print_r($reTotalPagosCuotasAnios);
	
	return $reTotalPagosCuotasAnios;
}
/*---------------------------- Fin buscarTotalesAniosPagosCuota ----------------------------------*/

/*---------------------------- Inicio  buscarTotalesPagosCuotaAgrup --------------------------------
Ejecuta una consulta para buscar los cuotas pagadas y no pagadas de los socios de una AGRUPACIÓN 
y las prepara para mostrarlas por agrupaciones para un año concreto.
Incluye las bajas del año, ya que pudieran haber pagado oreviamente a la baja. 
Los datos de la consulta los trata y prepara para la mostrar los totales de cuotas de los años,
mediante varios acumuladores y totalizadores, de por diferentes conceptos de información: 

$acumuladorPagosAgrup['numSociosAnio']
$acumuladorPagosAgrup['IMPORTECUOTAANIOPAGADA']
.......

Al final retorna la información en formato array $reTotalPagosCuotasAgrup listo para mostrarlo 
en el formulario vistas/tesorero/vTotalesCuotasAnioAgrupInc.php

LLAMADA: cTesorero.php:mostrarTotalesCuotasAnioAgrup()

LLAMA: modeloTesorero.php:cadBuscarTotalesPagosCuotaAgrup(),
modelos/BBDD/MySQL/modeloMySQL.php:buscarCadSql()	 
modelos/modeloErrores.php

OBSERVACIONES: incluye PDO, $arrBind y modificaciones para PHP 7.3.21
--------------------------------------------------------------------------------------------------*/
function buscarTotalesPagosCuotaAgrup($codAgrupPagoCuota,$anioCuotas,$estadoSocio,$estadoCuota)
{
	//echo "<br><br>0-1 modeloTesorero:buscarTotalesPagosCuotaAgrup:anioCuotas: ";print_r($anioCuotas);
 //echo "<br><br>0-2 modeloTesorero:buscarTotalesPagosCuotaAgrup:codAgrupPagoCuota: ";print_r($codAgrupPagoCuota);
	
	$totalPagosCuotasAgrup = array();
	$reTotalPagosCuotasAgrup = array();

	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	require_once "./modelos/BBDD/MySQL/conexionMySQL.php";			
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);

	if ($conexionDB['codError'] !== '00000')	
	{ $reTotalPagosCuotasAgrup = $conexionDB;   
	}
	else	
	{ 
			$arrBuscarTotalesPagosCuotaAgrup = cadBuscarTotalesPagosCuotaAgrup($codAgrupPagoCuota,$anioCuotas,$estadoSocio,$estadoCuota);//en modeloTesorero.php
	  //echo "<br><br>1 modeloTesorero:buscarTotalesPagosCuotaAgrup:arrBuscarTotalesPagosCuotaAgrup: ";print_r($arrBuscarTotalesPagosCuotaAgrup);		
	  
			$cadSelectBuscarCuotas = $arrBuscarTotalesPagosCuotaAgrup['cadSQL'];

	  $arrBind = $arrBuscarTotalesPagosCuotaAgrup['arrBindValues'];					
	
   $resBuscarCuotas = buscarCadSql($cadSelectBuscarCuotas,$conexionDB['conexionLink'],$arrBind);	
			//echo '<br><br>2 modeloTesorero:buscarTotalesPagosCuotaAgrup:resBuscarCuotas: ';print_r($resBuscarCuotas);
			
			if ($resBuscarCuotas['codError'] !== '00000')
			{
				$reTotalPagosCuotasAgrup = $resBuscarCuotas;			
			 $reTotalPagosCuotasAgrup['textoCabecera'] ='Totales cuotas';
				$reTotalPagosCuotasAgrup['textoComentarios'] =".Error del sistema, vuelva a intentarlo pasado un tiempo ";
		
				require_once './modelos/modeloErrores.php';
				insertarError($reTotalPagosCuotasAgrup,$conexionDB['conexionLink']);		
			}	
			else
			{//----------- Inicio Puesta a cero de acumuladores ----------------				
		
				$acumuladorPagosAgrup['numSociosAnio'] = 0;
				$acumuladorPagosAgrup['IMPORTECUOTAANIOPAGADA'] = 0;
				$acumuladorPagosAgrup['totalAnioCuotasEL'] = 0;								
				$acumuladorPagosAgrup['totalAnioAbonada'] = 0;
				$acumuladorPagosAgrup['numSociosAnioAbonada'] = 0;
				
    $acumuladorPagosAgrup['totalGastosPagosAnio'] = 0;	
	
				$acumuladorPagosAgrup['totalAnioNoAbonada_y_PendienteCobro'] = 0;
				$acumuladorPagosAgrup['numSociosNoAbonadaAnio_y_PendientesCobro'] = 0;

    $acumuladorPagosAgrup['totalAnioDevuelta'] = 0;
				$acumuladorPagosAgrup['numSociosDevuelta'] = 0;
    $acumuladorPagosAgrup['totalAnioErrorCuenta'] = 0;
				$acumuladorPagosAgrup['numSociosErrorCuenta'] = 0;
    $acumuladorPagosAgrup['totalAnioAbonaParte'] = 0;
				$acumuladorPagosAgrup['numSociosAbonaParte'] = 0;				
    $acumuladorPagosAgrup['totalSociosAnioExentos'] = 0;
				$acumuladorPagosAgrup['numSociosAnioExentos'] = 0;
			 $acumuladorPagosAgrup['totalAnioCuotaDonacion'] = 0;
				$acumuladorPagosAgrup['numSociosAnioCuotaDonacion'] = 0;

				$acumuladorPagosAgrup['totalDOMICILIADA'] = 0;
				$acumuladorPagosAgrup['numDOMICILIADA'] = 0;
				$acumuladorPagosAgrup['totalPAYPAL'] = 0;	
				$acumuladorPagosAgrup['numPAYPAL'] = 0;
				$acumuladorPagosAgrup['totalTRANSFERENCIA'] = 0;
				$acumuladorPagosAgrup['numTRANSFERENCIA'] = 0;
				$acumuladorPagosAgrup['totalMETALICO'] = 0;	
				$acumuladorPagosAgrup['numMETALICO'] = 0;		
				$acumuladorPagosAgrup['totalSIN-DATOS'] = 0;	
				$acumuladorPagosAgrup['numSIN-DATOS'] = 0;	

				$acumuladorPagosAgrup['numGeneral'] = 0;
				$acumuladorPagosAgrup['totalGeneral'] = 0;
				$acumuladorPagosAgrup['numParado'] = 0;
				$acumuladorPagosAgrup['totalParado'] = 0;				
				$acumuladorPagosAgrup['numJoven'] = 0;
				$acumuladorPagosAgrup['totalJoven'] = 0;				
				$acumuladorPagosAgrup['numHonorario'] = 0;		
				$acumuladorPagosAgrup['totalHonorario'] = 0;				
				
				//$acumuladorPagosAgrup['totalAnioDefictGastosPayPal'] = 0;
				//$acumuladorPagosAgrup['numSociosAnioDefictGastosPayPal'] = 0;		
				
				//----------- Fin Puesta a cero de acumuladores ------------------				

    $totales = $acumuladorPagosAgrup; //para inicializar y que no de warning
			
				$agrupacionActual = $resBuscarCuotas['resultadoFilas'][0]['NomAgrup_PagoCuota'];
				
				//-------- Inicio bucle foreach ----------------------------------------------	
				foreach ($resBuscarCuotas['resultadoFilas'] as  $fila => $contenidoFila)
				{//echo "<br><br>fila=";print_r($fila);echo "contenido=";print_r($contenidoFila);
					
			  if ($contenidoFila['NomAgrup_PagoCuota'] !== $agrupacionActual)
					{ 					  
       $totalPagosCuotasAgrup[$agrupacionActual] = $acumuladorPagosAgrup;
							
							foreach ($acumuladorPagosAgrup as $campoAcumulador => $valCampoAcumulador )//poner acumuladores a 0
							{	$acumuladorPagosAgrup[$campoAcumulador] = 0;
							}								
							$agrupacionActual = $contenidoFila['NomAgrup_PagoCuota'];								
					}
					
			 	$acumuladorPagosAgrup['numSociosAnio']++ ;		
								
				 //suma de las cuotas obligatorias que debieran que pagar todos los socios (cada uno según CODCUOTA de socio:General,Parado,Joven)				  
					$acumuladorPagosAgrup['totalAnioCuotasEL'] += $contenidoFila['IMPORTECUOTAANIOEL'];
					
					//suma de todas las cuotas pagadas por los socios, incluyen todos los tipos de cuota y también las cuotas+donción, y la real de Paypal
				 $acumuladorPagosAgrup['IMPORTECUOTAANIOPAGADA'] += $contenidoFila['IMPORTECUOTAANIOPAGADA'];
					
					$acumuladorPagosAgrup['totalGastosPagosAnio'] +=$contenidoFila['IMPORTEGASTOSABONOCUOTA'];//nuevo		

					//------------------	Acumuladores según tipo cuota ---------------			
					switch ($contenidoFila['CODCUOTA'])
					{case 'General':
													$acumuladorPagosAgrup['totalGeneral'] += $contenidoFila['IMPORTECUOTAANIOPAGADA'];
													$acumuladorPagosAgrup['numGeneral'] ++;
													break;				 
						case 'Parado':
													$acumuladorPagosAgrup['totalParado'] += $contenidoFila['IMPORTECUOTAANIOPAGADA'];
													$acumuladorPagosAgrup['numParado'] ++;
													break;
						case 'Joven':
													$acumuladorPagosAgrup['totalJoven'] += $contenidoFila['IMPORTECUOTAANIOPAGADA'];
													$acumuladorPagosAgrup['numJoven'] ++;
													break; 																
						case 'Honorario':
													$acumuladorPagosAgrup['totalHonorario'] += $contenidoFila['IMPORTECUOTAANIOPAGADA'];
													$acumuladorPagosAgrup['numHonorario'] ++;
													break;								
					}
     //------------------------------------------------------------------									
					
					//-- Inicio	Acumuladores ESTADOCUOTA=ABONADA y según modo ingreso --	
					if ($contenidoFila['ESTADOCUOTA'] == 'ABONADA')
					{	$acumuladorPagosAgrup['numSociosAnioAbonada']++ ;
							
							//suma de las cuotas obligatorias de con ESTADO=ABONADA aunque haya abonado más como donación
							$acumuladorPagosAgrup['totalAnioAbonada'] += $contenidoFila['IMPORTECUOTAANIOEL'];
		
							switch ($contenidoFila['MODOINGRESO'])
							{ case 'DOMICILIADA':
															$acumuladorPagosAgrup['totalDOMICILIADA'] += $contenidoFila['IMPORTECUOTAANIOPAGADA'];
															$acumuladorPagosAgrup['numDOMICILIADA'] ++;
															break; 
									case 'PAYPAL':
															$acumuladorPagosAgrup['totalPAYPAL'] += $contenidoFila['IMPORTECUOTAANIOPAGADA'];
															$acumuladorPagosAgrup['numPAYPAL'] ++;
															break;
									case 'TRANSFERENCIA':
															$acumuladorPagosAgrup['totalTRANSFERENCIA'] += $contenidoFila['IMPORTECUOTAANIOPAGADA'];
															$acumuladorPagosAgrup['numTRANSFERENCIA'] ++;
															break;
									case 'METALICO':
															$acumuladorPagosAgrup['totalMETALICO'] += $contenidoFila['IMPORTECUOTAANIOPAGADA'];
															$acumuladorPagosAgrup['numMETALICO'] ++;
															break;
									case 'SIN-DATOS':
															$acumuladorPagosAgrup['totalSIN-DATOS'] += $contenidoFila['IMPORTECUOTAANIOPAGADA'];
															$acumuladorPagosAgrup['numSIN-DATOS'] ++;
															break;																
							}														
					}	//-- Fin	Acumuladores ESTADOCUOTA=ABONADA y según modo ingreso ----	
					
					//------ Inicio	Acumuladores otros ESTADOCUOTA ----------------------	
					
					if ($contenidoFila['ESTADOCUOTA'] == 'PENDIENTE-COBRO' || $contenidoFila['ESTADOCUOTA'] == 'NOABONADA')
					{ $acumuladorPagosAgrup['totalAnioNoAbonada_y_PendienteCobro'] += $contenidoFila['IMPORTECUOTAANIOEL'];
							$acumuladorPagosAgrup['numSociosNoAbonadaAnio_y_PendientesCobro']++ ;
					}
					
					if ($contenidoFila['ESTADOCUOTA'] == 'ABONADA-PARTE')
					{ $acumuladorPagosAgrup['totalAnioAbonaParte'] += $contenidoFila['IMPORTECUOTAANIOPAGADA'];
					  $acumuladorPagosAgrup['numSociosAbonaParte']++ ;
       $acumuladorPagosAgrup['numSociosNoAbonadaAnio_y_PendientesCobro']++ ;//LE CONSIDERO QUE NO HA PAGADO
							$acumuladorPagosAgrup['totalAnioNoAbonada_y_PendienteCobro'] += $contenidoFila['IMPORTECUOTAANIOEL'];
					}			
			
					if ($contenidoFila['ESTADOCUOTA'] == 'EXENTO')
					{ 
					  $acumuladorPagosAgrup['numSociosAnioExentos']++ ;
					}
													
					if ($contenidoFila['IMPORTECUOTAANIOPAGADA'] > $contenidoFila['IMPORTECUOTAANIOEL'])//Pagan más que su cuota correspondiente: Dona en cuota, o exento que paga			
					{
						if ($contenidoFila['ESTADOCUOTA'] == 'EXENTO')
					 {$acumuladorPagosAgrup['totalSociosAnioExentos'] += $contenidoFila['IMPORTECUOTAANIOPAGADA'];
					 }
						else
						{$acumuladorPagosAgrup['totalAnioCuotaDonacion'] += $contenidoFila['IMPORTECUOTAANIOPAGADA'] - $contenidoFila['IMPORTECUOTAANIOEL'];
							$acumuladorPagosAgrup['numSociosAnioCuotaDonacion']++ ;
						}	
					}										

					if ($contenidoFila['ESTADOCUOTA'] == 'NOABONADA-DEVUELTA')
					{ $acumuladorPagosAgrup['totalAnioDevuelta'] += $contenidoFila['IMPORTECUOTAANIOEL'];
					  $acumuladorPagosAgrup['numSociosDevuelta']++ ;
					}	
					
					if ($contenidoFila['ESTADOCUOTA'] == 'NOABONADA-ERROR-CUENTA')
					{ $acumuladorPagosAgrup['totalAnioErrorCuenta'] += $contenidoFila['IMPORTECUOTAANIOEL'];
					  $acumuladorPagosAgrup['numSociosErrorCuenta']++ ;
					}
					//------ Fin	Acumuladores otros ESTADOCUOTA -------------------------	
					
			}//-------- Fin bucle foreach -------------------------------------------------	
			
			$totalPagosCuotasAgrup[$agrupacionActual] = $acumuladorPagosAgrup;	//Para que no se pierda la última
			
			//echo '<br><br>3 modeloTesorero:buscarTotalesPagosCuotaAgrup:agrupacionActual: ';print_r($agrupacionActual);
	  
			//--------- Inicio cálculo de las columnas de totales -----------------
			foreach($totalPagosCuotasAgrup as $agrupacionActual => $contenidoAgrupa)
			{				
				foreach($contenidoAgrupa as $campo => $valCampo)
				{
					 $totales[$campo] += $valCampo;
				}
			}
			//echo '<br><br>4 modeloTesorero:buscarTotalesPagosCuotaAgrup:totales: ';print_r($totales);
			//--------- Fin cálculo de las columnas de totales --------------------
			
			$reTotalPagosCuotasAgrup['codError'] = '00000';
			$reTotalPagosCuotasAgrup['totales'] = $totales;
			$reTotalPagosCuotasAgrup['anioCuotas'] = $anioCuotas;
			$reTotalPagosCuotasAgrup['resultadoFilas'] = $totalPagosCuotasAgrup;	

			//echo '<br><br>5 modeloTesorero:buscarTotalesPagosCuotaAgrup:reTotalPagosCuotasAgrup: ';print_r($reTotalPagosCuotasAgrup);
		}
 }
	//echo '<br><br>6 modeloTesorero:buscarTotalesPagosCuotaAgrup:reTotalPagosCuotasAgrup: ';print_r($reTotalPagosCuotasAgrup);
	
	return $reTotalPagosCuotasAgrup;
}
/*---------------------------- Fin  buscarTotalesPagosCuotaAgrup ---------------------------------*/	

/*---------------------------- Inicio  cadBuscarTotalesPagosCuotaAgrup -----------------------------
Forma la cadena SELECT para buscar los cuotas pagadas y no pagadas de los socios incluyendo los
parámetros $codAgrupPagoCuota,$anioCuotas,$estadoSocio,$estadoCuota para las condiciones de 
búsqueda.
 
LLAMADA: modelosTesorero.php:buscarTotalesAniosPagosCuota(),buscarTotalesPagosCuotaAgrup()

OBSERVACIONES: incluye PDO, $arrBind y modificaciones para PHP 7.3.21

--------------------------------------------------------------------------------------------------*/
function cadBuscarTotalesPagosCuotaAgrup($codAgrupPagoCuota,$anioCuotas,$estadoSocio,$estadoCuota)
{
	//echo "<br><br>0-1 modeloTesorero:cadBuscarTotalesPagosCuotaAgrup:anioCuotas: ";print_r($anioCuotas);
	
 $arrBind = array();
	
	if ( !isset($codAgrupPagoCuota) || empty($codAgrupPagoCuota) || $codAgrupPagoCuota =='%' )
 { $condicionAgrup = '';		}
	else 
	{ $condicionAgrup = " AND SOCIO.CODAGRUPACION = :codAgrupPagoCuota ";	
			$arrBind [':codAgrupPagoCuota'] = $codAgrupPagoCuota;
 }

	if ( !isset($anioCuotas) || empty($anioCuotas) || $anioCuotas =='%' )
 { $condicionAnioCuotas = '';		}
	else 
	{ $condicionAnioCuotas = " AND CUOTAANIOSOCIO.ANIOCUOTA = :anioCuotas ";	
   $arrBind [':anioCuotas'] = $anioCuotas;
	}	

	if ( !isset($estadoSocio) || empty($estadoSocio) || $estadoSocio =='%' )
 { $condicionEstadoSocio = '';		}
	elseif($estadoSocio == 'alta')//puede ser también "alta-sin-password-excel", "alta-sin-password-gestor"
	{ 
	  $condicionEstadoSocio = " AND SUBSTRING(USUARIO.ESTADO,'1',4) = :estadoSocio ";	
			$arrBind [':estadoSocio'] = $estadoSocio;
	}	
	else 
	{ $condicionEstadoSocio = " AND USUARIO.ESTADO = :estadoSocio ";	//por si se quieren otras búsquedas	
   $arrBind [':estadoSocio'] = $estadoSocio;			
	}
	
	if ( !isset($estadoCuota) || empty($estadoCuota) || $estadoCuota =='%' )
 { $condicionEstadoCuota = '';		}
	else 
	{ $condicionEstadoCuota = " AND CUOTAANIOSOCIO.ESTADOCUOTA = :estadoCuota ";
	 	$arrBind [':estadoCuota'] = $estadoCuota;
 }	

 $tablasBusqueda =" USUARIO,SOCIO,AGRUPACIONTERRITORIAL,CUOTAANIOSOCIO ";	
	
 $camposBuscados =" CUOTAANIOSOCIO.ANIOCUOTA,
																				USUARIO.ESTADO, USUARIO.CODUSER,
																				
																				SOCIO.CODAGRUPACION as Codigo_Agrup_Actual,
																				
																				CUOTAANIOSOCIO.CODAGRUPACION as CodAgrup_PagoCuota,
																				AGRUPACIONTERRITORIAL.NOMAGRUPACION as NomAgrup_PagoCuota,	
																				
																				CUOTAANIOSOCIO.CODCUOTA,										
																				
																				CUOTAANIOSOCIO.IMPORTECUOTAANIOEL,CUOTAANIOSOCIO.IMPORTECUOTAANIOSOCIO,
																				CUOTAANIOSOCIO.IMPORTECUOTAANIOPAGADA, CUOTAANIOSOCIO.IMPORTEGASTOSABONOCUOTA,

																				CUOTAANIOSOCIO.MODOINGRESO,CUOTAANIOSOCIO.ESTADOCUOTA ";

 $cadCondicionesBuscar =" WHERE USUARIO.CODUSER=SOCIO.CODUSER 
																										AND SOCIO.CODSOCIO=CUOTAANIOSOCIO.CODSOCIO																									
																										AND CUOTAANIOSOCIO.CODAGRUPACION	= AGRUPACIONTERRITORIAL.CODAGRUPACION ".
																										
																										$condicionAgrup.$condicionEstadoSocio.$condicionAnioCuotas.$condicionEstadoCuota.																										
																										
																										"ORDER BY ANIOCUOTA DESC, AGRUPACIONTERRITORIAL.CODPAISDOM DESC, NomAgrup_PagoCuota ASC";
																										
						                   	/*	"AGRUPACIONTERRITORIAL.CODPAISDOM DESC"; es para que agrupación Europa Laica Estatal e Internacional salga la última	
																										     ya que CODPAISDOM = '--'*/
																															
 $cadBuscarTotalesPagosCuotaAgrup = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";
	
 //echo "<br><br>1 modeloTesorero:cadBuscarTotalesPagosCuotaAgrup:cadBuscarTotalesPagosCuotaAgrup: ";print_r($cadBuscarTotalesPagosCuotaAgrup);	
	
	$arrBuscarTotalesPagosCuotaAgrup['cadSQL'] = $cadBuscarTotalesPagosCuotaAgrup;
	
	$arrBuscarTotalesPagosCuotaAgrup['arrBindValues']	= $arrBind;
	
 //echo "<br><br>2 modeloTesorero:cadBuscarTotalesPagosCuotaAgrup:arrBuscarTotalesPagosCuotaAgrup: ";print_r($arrBuscarTotalesPagosCuotaAgrup);	
	
	return $arrBuscarTotalesPagosCuotaAgrup;
}
/*---------------------------- Fin  cadBuscarTotalesPagosCuotaAgrup ------------------------------*/

/*============================== FIN MOSTRAR TABLAS TOTALES PAGOS CUOTAS ==========================

==================================================================================================*/	


/*==== INICIO: FUNCIONES RELACIONADAS CON ACTUALIZAR CUOTAS VIGENTES  ==============================
- buscarCuotasEL()
- buscarImportesCuotaSocioElegida(). No usada ahora
- buscarCuotasSocioElegidaCuotasAnioSocio()
- actualizarCuotasVigentesEL()
==================================================================================================*/	

/*---------------- Inicio buscarCuotasEL (Ahora no se utiliza) ---------------------------------------
Descripción: Busca los datos de las cuotas de EL en la tabla IMPORTEDESCUOTAANIO por valor de los 
campos: $anioCuota,$codCuota

RECIBE: $anioCuota: año que puede ser todos = % o un año concreto
        $codCuota: Tipo de cuota que puede ser todos = % o uno concreto: General,Joven,Parado,Honorario 
        
DEVUELVE: array "resDatosCuotasAnioEL" con [resultadoFilas] organizado por [ANIOCUOTA] y para cada año los distintintos 
          tipos de cuotas con sus datos (Hay una rendundancia en el dato ANIOCUOTA sin problemas).
										También incluye datos de [codError] , [numFilas]
													

LLAMADA: cTesorero.php:cuotasVigentesELTes()
LLAMA: modeloMySQL.php:buscarCadSql(), 
       modelos/modeloErrores.php:insertarError() 
							BBDD/MySQL/configMySQL.php:conexionDB()

OBSERVACIONES: incluye PDO, $arrBind y modificaciones para PHP 7.3.21

Si devuelve datosCuotasAnioEL['numFilas'] === 0), se trata como error lógico ['codError'] = '80001'

Ahora se utiliza la función: "modeloSocios.php:buscarCuotasAnioEL($anioCuota,$codCuota)", 
que ya existía	y así se evita duplicidad ya que devolvía casi el mismo return.		
----------------------------------------------------------------------------------------------------*/
function buscarCuotasEL($anioCuota,$codCuota)
{
	//echo '<br><br>0-1 modeloTesorero:buscarCuotasEL:anioCuota: ';print_r($anioCuota);
	//echo '<br><br>0-2 modeloTesorero:buscarCuotasEL:codCuota: ';print_r($codCuota);
	
	require_once './modelos/modeloErrores.php';
		
	$resDatosCuotasAnioEL['nomScript'] = "modeloTesorero.php";
	$resDatosCuotasAnioEL['nomFuncion'] = "buscarCuotasEL";
	$resDatosCuotasAnioEL['codError'] = '00000';
	$resDatosCuotasAnioEL['errorMensaje'] = '';
	$resDatosCuotasAnioEL['textoComentarios'] = '';
	$nomScriptFuncionError = 'Error: modeloTesorero.php: buscarCuotasEL(): ';

	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);

	if ($conexionDB['codError'] !== '00000')	
	{ $datosCuotasAnioEL = $conexionDB;
	}
	else	// $conexionDB['codError'] == '00000'	
	{	$arrBind = array();
 
  if ( (!isset($anioCuota) || empty($anioCuota)) || (!isset($codCuota) || empty($codCuota)) )		
		{ 
	   $resDatosCuotasAnioEL['codError'] = '70601';
	   $resDatosCuotasAnioEL['errorMensaje'] = 'ERROR: Faltan datos necesarios (anioCuota, codCuota) para buscar en SELECT: -buscarCuotasEL()-';		
    $resDatosCuotasAnioEL['textoComentarios'] = $nomScriptFuncionError." Error del sistema, vuelva a intentarlo pasado un tiempo (modeloTesorero.php: buscarCuotasEL)";
    insertarError($resDatosCuotasAnioEL);						
		}
		else //!if ( (!isset($anioCuota) || empty($anioCuota)) || (!isset($codCuota) || empty($codCuota)) )		
		{			
			if ( $anioCuota == '%')//todos los años
			{ 
					$condicionAnioCuota = " IMPORTEDESCUOTAANIO.ANIOCUOTA like '%' ";
			}
			else
			{ $condicionAnioCuota = " IMPORTEDESCUOTAANIO.ANIOCUOTA = :anioCuota ";

					$arrBind = array(':anioCuota' => $anioCuota);  
			}			

			if ( $codCuota == '%') //todos los tipos de cuotas
			{ 
					$condicionCodCuotaElegida = " ";
			}
			else
			{
					$condicionCodCuotaElegida = " AND IMPORTEDESCUOTAANIO.CODCUOTA = :codCuota ";
			
					$arrBind[':codCuota'] =  $codCuota;
			}	

			$tablasBusqueda = "IMPORTEDESCUOTAANIO";
			$camposBuscados = "*";  
					
			$cadCondicionesBuscar = " WHERE ".$condicionAnioCuota.$condicionCodCuotaElegida. 
		                        	" ORDER BY IMPORTEDESCUOTAANIO.ANIOCUOTA, IMPORTEDESCUOTAANIO.CODCUOTA";			

			//echo '<br><br>1-1 modeloTesorero:buscarCuotasEL:arrBind: ';print_r($arrBind);
			
			$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";
			
			echo '<br><br>1-2 modeloTesorero:buscarCuotasEL:cadSql: ';print_r($cadSql);
			
			$datosCuotasAnioEL = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);//en BBDD/MySQL/modeloMySQL.php"; 																																								
																																							
			echo '<br><br>2 modeloTesorero:buscarCuotasEL:datosCuotasAnioEL: ';print_r($datosCuotasAnioEL );																																							
																																							
			if ($datosCuotasAnioEL['codError'] !== '00000')
			{		
    $resDatosCuotasAnioEL['codError'] = $datosCuotasAnioEL['codError'];
				$resDatosCuotasAnioEL['errorMensaje'] = $nomScriptFuncionError.$datosCuotasAnioEL['errorMensaje'];			
				$resDatosCuotasAnioEL['textoComentarios'] = $nomScriptFuncionError." Error del sistema, en (modeloTesorero.php: buscarCuotasEL)";

				insertarError($resDatosCuotasAnioEL);		
			}	
			elseif ($datosCuotasAnioEL['numFilas'] === 0)
			{ $resDatosCuotasAnioEL['codError'] = '80001'; 
					$resDatosCuotasAnioEL['errorMensaje'] = "No se han encontrado importes de las cuotas vigentes para las condiciones de búsqueda";
					$resDatosCuotasAnioEL['textoComentarios'] = 	$resDatosCuotasAnioEL['errorMensaje'];
			}
			else// $datosCuotasAnioEL['codError'] == '00000') and ($datosCuotasAnioEL['numFilas'] !== 0)
			{
    //---- Inicio obtener array con formato más adecuado para el formulario --------------		
				
				$ultimaFila = $datosCuotasAnioEL['numFilas'];
				$f = 0;	
				
				$indexAnio = $datosCuotasAnioEL['resultadoFilas'][0]['ANIOCUOTA'];
				
				while ( $f < $ultimaFila)
				{
						$valorFila = $datosCuotasAnioEL['resultadoFilas'][$f];
					
						if ($indexAnio !== $valorFila['ANIOCUOTA'])		
						{
							$auxAnios[$indexAnio] = $auxCuotas;							
							$cuotasAnios['ANIOCUOTA'] = $auxAnios;							
							$indexAnio = $valorFila['ANIOCUOTA'];
						}	

						foreach ($valorFila as $col => $valCol)
						{
								$auxCol[$col] = $valCol;
						}					
						$auxCuotas[$valorFila['CODCUOTA']] =	$auxCol;					
								
						$f++;
				}
				$auxAnios[$indexAnio] = $auxCuotas;	
				$cuotasAnios['ANIOCUOTA'] = $auxAnios;						
					
	   echo '<br><br>3 modeloTesorero:buscarCuotasEL:cuotasAnios: ';print_r($cuotasAnios);//	será algo parecido a:
				/* $cuotasAnios = Array ( [ANIOCUOTA] => 
				................ Anteriores 
				Array ( [2009] => Array ( 
				[General] => Array ( [ANIOCUOTA] => 2009 [CODCUOTA] => General [IMPORTECUOTAANIOEL] => 30.00 [NOMBRECUOTA] => [DESCRIPCIONCUOTA] => Cuota general sin bonificaciones )
				[Honorario] => Array ( [ANIOCUOTA] => 2009 [CODCUOTA] => Honorario [IMPORTECUOTAANIOEL] => 0.00 [NOMBRECUOTA] => [DESCRIPCIONCUOTA] => Honorario ) 
				[Joven] => Array ( [ANIOCUOTA] => 2009 [CODCUOTA] => Joven [IMPORTECUOTAANIOEL] => 0.00 [NOMBRECUOTA] => [DESCRIPCIONCUOTA] => Persona entre 18 y 25 años ) 
				[Parado] => Array ( [ANIOCUOTA] => 2009 [CODCUOTA] => Parado [IMPORTECUOTAANIOEL] => 0.00 [NOMBRECUOTA] => [DESCRIPCIONCUOTA] => Persona en paro ) ) 

				[2010] => Array ( 
				[General] => Array ( [ANIOCUOTA] => 2010 [CODCUOTA] => General [IMPORTECUOTAANIOEL] => 30.00 [NOMBRECUOTA] => [DESCRIPCIONCUOTA] => Cuota general sin bonificaciones ) 
				[Honorario] => Array ( [ANIOCUOTA] => 2010 [CODCUOTA] => Honorario [IMPORTECUOTAANIOEL] => 0.00 [NOMBRECUOTA] => [DESCRIPCIONCUOTA] => Honorario ) 
				[Joven] => Array ( [ANIOCUOTA] => 2010 [CODCUOTA] => Joven [IMPORTECUOTAANIOEL] => 0.00 [NOMBRECUOTA] => [DESCRIPCIONCUOTA] => Persona entre 18 y 25 años ) 
				[Parado] => Array ( [ANIOCUOTA] => 2010 [CODCUOTA] => Parado [IMPORTECUOTAANIOEL] => 0.00 [NOMBRECUOTA] => [DESCRIPCIONCUOTA] => Persona en paro ) )
				.................. Posteriores)
				*/				
				//---- Fin obtener array con formato más adecuado para el formulario -----------------	
				
    $resDatosCuotasAnioEL['resultadoFilas'] = $cuotasAnios;
			
			}//else $datosCuotasAnioEL['codError'] == '00000') and ($datosCuotasAnioEL['numFilas'] !== 0)    			
		}//else !if ( (!isset($anioCuota) || empty($anioCuota)) || (!isset($codCuota) || empty($codCuota)) )		
	}//else	 $conexionDB['codError'] == '00000'	
	
 echo '<br><br>4 modeloTesorero:buscarCuotasEL:resDatosCuotasAnioEL: ';print_r($resDatosCuotasAnioEL);
	
	return $resDatosCuotasAnioEL;
}
/*------------------------------ Fin buscarCuotasEL ------------------------------------------------*/

/*---------------- Inicio buscarImportesCuotaSocioElegida--------------------------------------------
Descripción: Busca los datos en las tablas SOCIO según CODCUOTA de los socios 
             en columna	IMPORTECUOTAANIOSOCIO al darse de alta o al modificar su cuota .
Llamada: cTesorero.php:
Devuelve: un array con [resultadoFilas] organizado por [CODUSER] y para cada socio con los datos 
          contenidos en la tabla socio, según las condiciones de la consulta.
										También incluye datos de [codError] , [numFilas]
NOTA: ACTUALMENTE NO SE USA 2013-10-02										
----------------------------------------------------------------------*/
function buscarImportesCuotaSocioElegida($codCuota)
{//echo '<br><br>1 modeloTesorero:buscarImportesCuotasSocioElegida:codCuota :';print_r($codCuota);

	$resImportesCuotaSocioElegida['codError']='00000';
	$resImportesCuotaSocioElegida['errorMensaje']='';
	$resImportesCuotaSocioElegida['nomFuncion']="buscarImporteCuotaSocioElegida";
 $resImportesCuotaSocioElegida['nomScript']="modeloTesorero.php";
 
	//require "BBDD/MySQL/configMySQL.php";
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	$conexionDB=conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);

	if ($conexionDB['codError']!=='00000')	
	{ $resImportesCuotaSocioElegida=$conexionDB;
	}
	else	
	{	
	 if ( !isset($codCuota) || empty($codCuota) || $codCuota =='%' )
		{ $condicionCodCuotaElegida = ' ';}
		else
		{		
			$condicionCodCuotaElegida = " AND SOCIO.CODCUOTA = :codCuota";
		}		
	
	 $tablasBusqueda = "SOCIO,USUARIO";
	 $camposBuscados = "SOCIO.*, USUARIO.*";

	 $cadCondicionesBuscar = " WHERE SOCIO.CODUSER=USUARIO.CODUSER".
		                        " AND USUARIO.ESTADO LIKE 'alta%'".
		                        $condicionCodCuotaElegida.
																										" ORDER BY USUARIO.CODUSER";																					

  $arrBind = array(':codCuota' => $codCuota);  

		$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";
		
		echo '<br><br>1 modeloTesorero:buscarImportesCuotasSocioElegida:cadSql: ';print_r($cadSql);
		
		$importesCuotaSocioElegida = buscarCadSql ($cadSql,$conexionDB['conexionLink'],$arrBind); 
																																					
  echo '<br><br>2 modeloTesorero:buscarImportesCuotasSocioElegida:datosImportesCuotaSocioElegida: ';print_r($datosImportesCuotaSocioElegida);
		
		$resImportesCuotaSocioElegida['resultadoFilas'] = $importesCuotaSocioElegida['resultadoFilas'];	
		$resImportesCuotaSocioElegida['numFilas'] = $importesCuotaSocioElegida['numFilas'];	
																																							
		if ($importesCuotaSocioElegida['codError'] !== '00000')
		{$resImportesCuotaSocioElegida['codError'] = $importesCuotaSocioElegida['codError'];
		 $resImportesCuotaSocioElegida['errorMensaje'] = $importesCuotaSocioElegida['errorMensaje'];	
		 $resImportesCuotaSocioElegida['arrMensaje']['textoCabecera']='Gestión de socios/as';
			$resImportesCuotaSocioElegida['arrMensaje']['textoComentarios'].=".Error del sistema, vuelva a intentarlo pasado un tiempo (modeloTesorero: buscarCuotasEL)";
		 $resImportesCuotaSocioElegida['arrMensaje']['textoBoton']='Salir de la aplicación';
		 $resImportesCuotaSocioElegida['arrMensaje']['enlaceBoton']='./index.php?controlador=controladorLogin&amp;accion=logOut';
	
			require_once './modelos/modeloErrores.php';
			insertarError($resImportesCuotaSocioElegida);		
		}	
		elseif ($importesCuotaSocioElegida['numFilas'] == 0)
		{ $resImportesCuotaSocioElegida['codError'] = '80004'; //no tiene cuotas, está vacía
			 $resImportesCuotaSocioElegida['errorMensaje'] = "No hay ninguna cuota anotada";
		}		
	}
 
	echo '<br><br>3 modeloTesorero:buscarImportesCuotaSocioElegida:resImportesCuotaSocioElegida:';print_r($resImportesCuotaSocioElegida);
	
	return $resImportesCuotaSocioElegida;
}
//------------------------------ Fin buscarImportesCuotaSocioElegida --------------------------------

/*---------------- Inicio buscarCuotasSocioElegidaCuotasAnioSocio------------------------------------
Busca en las tablas SOCIO, CUOTAANIOSOCIO datos en relación a las cuotas anuales de Europa Laica 
y a las cuotas elegidas por los socios en columna	IMPORTECUOTAANIOSOCIO al darse de alta o 
al modificar su cuota.
Incluye grabación de errores

RECIBE: $codCuota (General, Joven, Parado, Honorario), $anioCuota (se espera Y+1 como norma general)

DEVUELVE: un array "resImportesCuotaSocioElegida" con [resultadoFilas] organizado por [CODSOCIO]
          y con los datos según las condiciones de la consulta.	También incluye datos [codError],[numFilas]								
													
LLAMADA: modeloTesorero:actualizarCuotasVigentesEL()
LLAMA: modeloMySQL.php:buscarEnTablas(), 
       modelos/modeloErrores.php:insertarError()

OBSERVACIONES: incluye PDO, $arrBind y modificaciones para PHP 7.3.21	

Puede devolver 	['numFilas'] = 0, pero no se considerá error en esta función						
---------------------------------------------------------------------------------------------------*/
function buscarCuotasSocioElegidaCuotasAnioSocio($codCuota,$anioCuota)
{
	//echo '<br><br>0-1 modeloTesorero:buscarCuotasSocioElegidaCuotasAnioSocio:anioCuota: ';print_r($anioCuota);
	//echo '<br><br>0-2 modeloTesorero:buscarCuotasSocioElegidaCuotasAnioSocio:codCuota: ';print_r($codCuota);
 
	require_once './modelos/modeloErrores.php';
 
	$resImportesCuotaSocioElegida['nomScript'] = 'modeloTesorero.php';	
	$resImportesCuotaSocioElegida['nomFuncion'] = 'buscarCuotasSocioElegidaCuotasAnioSocio';
	$resImportesCuotaSocioElegida['codError'] = '00000';
	$resImportesCuotaSocioElegida['errorMensaje'] = '';	
 
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);

	if ($conexionDB['codError'] !== '00000')	
	{ $resImportesCuotaSocioElegida = $conexionDB;
	}
	else	//$conexionDB['codError'] == '00000'
	{	
  if ( (!isset($anioCuota) || empty($anioCuota)) || (!isset($codCuota) || empty($codCuota)) )		
		{	$resImportesCuotaSocioElegida['codError'] = '70601';
	   $resImportesCuotaSocioElegida['errorMensaje'] = 'ERROR: Faltan datos necesarios para buscar buscar en SELECT: modeloTesorero.php: buscarCuotasSocioElegidaCuotasAnioSocio()';
			 insertarError($resImportesCuotaSocioElegida);							
		}
		else //!if ( (!isset($anioCuota) || empty($anioCuota)) || (!isset($codCuota) || empty($codCuota)) )		
		{	
		 $arrBind = array();			
			
			if ( $anioCuota == '%')
			{ $condicionAnioCuotaElegida = ' ';
		 }
			else
			{
		 		$condicionAnioCuotaElegida = " AND CUOTAANIOSOCIO.ANIOCUOTA = :anioCuota ";				
			 	$arrBind = array(':anioCuota' => $anioCuota );			
			}				
		
			if ( $codCuota == '%')
			{ $condicionCodCuotaElegida = ' ';
		 }
			else
			{
			 	$condicionCodCuotaElegida = " AND CUOTAANIOSOCIO.CODCUOTA = :codCuota ";
					$arrBind[':codCuota'] =  $codCuota;
			}
		
			$tablasBusqueda = "SOCIO,CUOTAANIOSOCIO";
			$camposBuscados = "SOCIO.CODSOCIO,SOCIO.CODCUOTA as socioCODCUOTA,SOCIO.IMPORTECUOTAANIOSOCIO as socioIMPORTECUOTAANIOSOCIO,
																						CUOTAANIOSOCIO.ANIOCUOTA,CUOTAANIOSOCIO.CODCUOTA as cuotaniosocioCODCUOTA,CUOTAANIOSOCIO.IMPORTECUOTAANIOEL,
																						CUOTAANIOSOCIO.IMPORTECUOTAANIOSOCIO as cuotaniosocioIMPORTECUOTAANIOSOCIO,CUOTAANIOSOCIO.IMPORTECUOTAANIOPAGADA,CUOTAANIOSOCIO.ESTADOCUOTA";
																						
			$cadCondicionesBuscar = " WHERE SOCIO.CODSOCIO = CUOTAANIOSOCIO.CODSOCIO".		                       
																											$condicionCodCuotaElegida.$condicionAnioCuotaElegida.
																											" ORDER BY SOCIO.CODSOCIO";	

			//echo '<br><br>1-1 modeloTesorero:buscarCuotasSocioElegidaCuotasAnioSocio:arrBind: ';print_r($arrBind);	
			
			$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";
			
			//echo '<br><br>1-2 modeloTesorero:buscarCuotasSocioElegidaCuotasAnioSocio:cadSql: ';print_r($cadSql);	
			
			$importesCuotaSocioElegida = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind); 																																						
																																							
			//echo '<br><br>2 modeloTesorero:buscarCuotasSocioElegidaCuotasAnioSocio:importesCuotaSocioElegida: ';print_r($importesCuotaSocioElegida);

			if ($importesCuotaSocioElegida['codError'] !== '00000')
			{
				$resImportesCuotaSocioElegida['codError'] = $importesCuotaSocioElegida['codError'];				
				$resImportesCuotaSocioElegida['errorMensaje'] = $importesCuotaSocioElegida['errorMensaje'];
				$resImportesCuotaSocioElegida['textoComentarios'] .= 'ERROR en sistema: modeloTesorero.php:buscarCuotasSocioElegidaCuotasAnioSocio:buscarCadSql()';

				insertarError($resImportesCuotaSocioElegida);		
			}	
			/*	elseif ($importesCuotaSocioElegida['numFilas'] == 0)//no es error se puede dar esa situación se puede tratar despues
			{  $resImportesCuotaSocioElegida['codError'] = '80001'; //no hay cuotas que cumplan esas condiciones, consulta devuelve 0 filas 
					 $resImportesCuotaSocioElegida['errorMensaje'] = "No hay cuotas que cumplan esas condiciones de búsqueda";
			}	*/
			else
			{ //echo '<br><br>3 modeloTesorero:buscarCuotasSocioElegidaCuotasAnioSocio:resImportesCuotaSocioElegida:';print_r($resImportesCuotaSocioElegida);
				
     $resImportesCuotaSocioElegida['resultadoFilas'] = $importesCuotaSocioElegida['resultadoFilas'];	
			  $resImportesCuotaSocioElegida['numFilas'] = $importesCuotaSocioElegida['numFilas'];					
			}
			
  }//else !if ( (!isset($anioCuota) || empty($anioCuota)) || (!isset($codCuota) || empty($codCuota)) )					
	}//else $conexionDB['codError'] !== '00000'	
 
	//echo '<br><br>4 modeloTesorero:buscarCuotasSocioElegidaCuotasAnioSocio:resImportesCuotaSocioElegida: ';print_r($resImportesCuotaSocioElegida);
	
	return $resImportesCuotaSocioElegida;
}
/*------------------------------ Fin buscarCuotasSocioElegidaCuotasAnioSocio -----------------------*/


/*----------------------------- Inicio actualizarCuotasVigentesEL ------------------------------------
-Actualiza la tabla "IMPORTEDESCUOTAANIO", para modificar la columna del importe de la cuota anual de 
EL -IMPORTECUOTAANIOEL- por el nuevo importe que se recibe en $arrayDatos, procedente del formulario
para un año concreto -ANIOCUOTA-	y un tipo de cuota -CODCUOTA- concreto.			

Después con la función "buscarCuotasSocioElegidaCuotasAnioSocio()" se busca en las tablas "CUOTAANIOSOCIO"
y "SOCIO", los datos de los socios/as que ya tienen ya anotada la cuota para el año próximo (Y+1)
en "CUOTAANIOSOCIO", (son los socios que ya tienen abonada la cuota del año actual, y que además 
después de abonada, ellos mismos o los gestores han actualizado datos suyos para el próximo año)  

Con el array obtenido "$reCuotasSocioElegidaCuotasAnioSocio" en un WHILE se comprueban las condiciones
y se actualiza en la tabla "CUOTAANIOSOCIO" el importe de la cuota anual de EL -IMPORTECUOTAANIOEL- 
al valor ['IMPORTECUOTAANIOEL_NUEVO'] (nueva cuota anual elegida), y el campo cuota anual elegida por 
el socio -IMPORTECUOTAANIOSOCIO-, se actualizará según los casos, y para ello habrá que tener en cuenta
la cuota elegida por en la tabla SOCIO al darse de alta o al modificarla.

Un contador "$contadorCambiosCUOTAANIOSOCIO" indicará el número de importes de cuotas modificas 

RECIBE: $arrayDatos, que contiene ['ANIOCUOTA']=(Y+1) y ['CODCUOTA'], 
        desde cTesorero.php:actualizarCuotasVigentesELTes (procede de formulario)
DEVUELVE: $resActualizarCuotasVigentesEL con los resultado o errores de las operaciones 
 													
LLAMADA: cTesorero.php:actualizarCuotasVigentesELTes()
LLAMA: modeloTesorero:buscarCuotasSocioElegidaCuotasAnioSocio(),modeloMySQL.php:actualizarTabla()

OBSERVACIONES: incluye PDO, $arrBind y modificaciones para PHP 7.3.21
-----------------------------------------------------------------------------------------------------*/ 
function actualizarCuotasVigentesEL($arrayDatos)     
{
	//echo '<br><br>0-1 modeloTesorero:actualizarCuotasVigentesEL:arrayDatos: ';print_r($arrayDatos);

	$resActualizarCuotasVigentesEL['nomScript'] = "modeloTesorero.php";	
	$resActualizarCuotasVigentesEL['nomFuncion'] = "actualizarCuotasVigentesEL";
 $resActualizarCuotasVigentesEL['codError'] = '00000';
 $resActualizarCuotasVigentesEL['errorMensaje'] = '';
	$resActualizarCuotasVigentesEL['textoComentarios'] ='';
	$textoErrores = '';
	$nomScriptFuncionError = " Error: modeloTesorero.php:actualizarCuotasVigentesEL(): "; 
	
 require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";		
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);		
	
	if ($conexionDB['codError'] !== "00000")
	{ $resActualizarCuotasVigentesEL = $conexionDB;
	}
	else //$conexionDB['codError']=="00000"
	{
		if (!isset($arrayDatos) || empty($arrayDatos))
		{$resActualizarCuotasVigentesEL['codError'] = '70601';
			$resActualizarCuotasVigentesEL['errorMensaje'] = $nomScriptFuncionError.": Faltan algunas variables-parámetros (arrayDatos) necesarios para SQL -actualizarCuotasVigentesEL()- ";	 
			$resActualizarCuotasVigentesEL['textoComentarios'] = $nomScriptFuncionError;
		}	
  else // !if (!isset($arrayDatos) || empty($arrayDatos))
		{			
			require_once("BBDD/MySQL/transationPDO.php");
			$resIniTrans = beginTransationPDO($conexionDB['conexionLink']);

			if ($resIniTrans['codError'] !== '00000') //será ['codError'] = '70501';
			{	$resIniTrans['numFilas'] = 0;
					$resActualizarCuotasVigentesEL['codError'] = $resIniTrans['codError'];
					$resActualizarCuotasVigentesEL['errorMensaje'] = $resIniTrans['errorMensaje'];
					$textoErrores = "Error del sistema en modeloTesorero.php:actualizarCuotasVigentesEL() al iniciar la transación";					
					//echo "<br><br>1-2 modeloTesorero:actualizarCuotasVigentesEL:resActualizarCuotasVigentesEL: ";var_dump($resActualizarCuotasVigentesEL);	echo "<br>";
			}			
			else //$resIniTrans['codError'] == '00000				
			{
				/*--- Inicio Actualizar la tabla "IMPORTEDESCUOTAANIO" ---------------------------------------------------------
						Se  Actualizar el campo -IMPORTECUOTAANIOEL- en las filas que cumplen la concición 'ANIOCUOTA' y 'CODCUOTA'		
				--------------------------------------------------------------------------------------------------------------*/		
				$arrDatosActNuevoEL['IMPORTECUOTAANIOEL'] = $arrayDatos['IMPORTECUOTAANIOEL_NUEVO']['valorCampo'];//sustiye  me gusta esta asignacion machaca valores previos	
				
				$arrayCondiciones['ANIOCUOTA']['valorCampo'] = $arrayDatos['ANIOCUOTA']['valorCampo'];
				$arrayCondiciones['ANIOCUOTA']['operador'] = '=';
				$arrayCondiciones['ANIOCUOTA']['opUnir'] = ' AND ';
				
				$arrayCondiciones['CODCUOTA']['valorCampo'] = $arrayDatos['CODCUOTA']['valorCampo'];
				$arrayCondiciones['CODCUOTA']['operador'] = '=';
				$arrayCondiciones['CODCUOTA']['opUnir'] = ' ';

				$resActualizarImportesCuotasEL = actualizarTabla('IMPORTEDESCUOTAANIO',$arrayCondiciones,$arrDatosActNuevoEL,$conexionDB['conexionLink']);//modeloMySQL.php, error OK	
				
				//echo '<br><br>2 modeloTesorero:actualizarCuotasVigentesEL:resActualizarImportesCuotasEL: ';print_r($resActualizarImportesCuotasEL);
				
				if ($resActualizarImportesCuotasEL['codError'] !== '00000')//Nota si fuese ['numFilas']=0 es que se ha dejado el mismo valor y que no habría que cambiar lo siguiente 
				{			
					$resActualizarCuotasVigentesEL['codError'] = $resActualizarImportesCuotasEL['codError'];
					$resActualizarCuotasVigentesEL['errorMensaje'] = $resActualizarImportesCuotasEL['errorMensaje'];								
					$textoErrores = "Error del sistema en modeloTesorero.php:actualizarCuotasVigentesEL(), al actualizar tabla IMPORTEDESCUOTAANIO con función actualizarTabla()";							
				}
				else //$resActualizarImportesCuotasEL['codError'] =='00000')
				{
					//Nota: si fuese ['numFilas'] = 0 quiere decir que se ha dejado el mismo valor en IMPORTEDESCUOTAANIO y que no habría que cambiar nada "CUOTAANIOSOCIO" en lo siguiente
					if ($resActualizarImportesCuotasEL['numFilas'] === 0) 
					{		
						//	Si ['numFilas'] === 0 no necesita hacer commitPDO // tampoco rollback $resActualizarCuotasVigentesEL['codError'] = '80001';
						$resActualizarCuotasVigentesEL['textoComentarios'] = "<br /><br />No se ha producido ningún cambio en el importe de las cuotas vigentes de EL para el tipo de cuota 
																																																											<strong>".$arrayDatos['CODCUOTA']['valorCampo']. "- </strong>manteniéndose el mismo importe que tenía - <strong>".	
																																																											$arrayDatos['IMPORTECUOTAANIOEL_NUEVO']['valorCampo']." euros</strong> - para el año <strong>".
																																																											$arrayDatos['ANIOCUOTA']['valorCampo']."</strong>";
					}				
					else// será if ($resActualizarImportesCuotasEL['numFilas'] === 1)
					{			
						$resActualizarCuotasVigentesEL['textoComentarios'] = "<br /><br />Se ha actualizado el importe mínimo de las cuotas vigentes de EL tipo -<strong>".
																																																											$arrayDatos['CODCUOTA']['valorCampo']."-</strong> del anterior importe de <strong>".
																																																											$arrayDatos['IMPORTECUOTAANIOEL']['valorCampo']." euros</strong>, al nuevo importe de <strong>".
																																																											$arrayDatos['IMPORTECUOTAANIOEL_NUEVO']['valorCampo'].
																																																											" euros</strong>, que será el importe vigente durante el año <strong>".
																																																											$arrayDatos['ANIOCUOTA']['valorCampo']."</strong>";

						/*--- Fin Actualizar en tabla "IMPORTEDESCUOTAANIO" ----------------------------------------------------------*/    					

						/*-- Inicio Actualizar en tabla "CUOTAANIOSOCIO" las filas que cumplen la condición 'ANIOCUOTA'=Y+1 y 'CODCUOTA'	
								El campo -IMPORTECUOTAANIOEL- se actualizará a ['IMPORTECUOTAANIOEL_NUEVO']['valorCampo']
								y el campo -IMPORTECUOTAANIOSOCIO- dependerá de los casos, y habrá que tener en cuenta la cuota 
								elegida por en la tabla SOCIO al darse de alta o al modificarla, pues si la nueva cuota IMPORTECUOTAANIOEL_NUEVO
								fuese inferior a la elegida se mantendría la elegida para ese socio. 
								Para comprobar se busca con la función "buscarCuotasSocioElegidaCuotasAnioSocio()" en las tablas 
								"CUOTAANIOSOCIO" y "SOCIO" y después con el array obtenido comprobar las condiciones y actualizar fila a fila
						--------------------------------------------------------------------------------------------------------------*/
			
						$reCuotasSocioElegidaCuotasAnioSocio = buscarCuotasSocioElegidaCuotasAnioSocio($arrayDatos['CODCUOTA']['valorCampo'],$arrayDatos['ANIOCUOTA']['valorCampo']);
						//modeloTesorero.php, error ok

						//echo "<br><br>3-1 modeloTesorero:actualizarCuotasVigentesEL:reCuotasSocioElegidaCuotasAnioSocio: ";print_r($reCuotasSocioElegidaCuotasAnioSocio);

						if ($reCuotasSocioElegidaCuotasAnioSocio['codError'] !== '00000' )			
						{			
							$resActualizarCuotasVigentesEL['codError'] = $reCuotasSocioElegidaCuotasAnioSocio['codError'];
							$resActualizarCuotasVigentesEL['errorMensaje'] = $reCuotasSocioElegidaCuotasAnioSocio['errorMensaje'];								
							$textoErrores = "Error del sistema en modeloTesorero.php:actualizarCuotasVigentesEL(), al buscar en CUOTAANIOSOCIO y SOCIO con la función
																								buscarCuotasSocioElegidaCuotasAnioSocio()";			
						}
						elseif ($reCuotasSocioElegidaCuotasAnioSocio['numFilas'] === 0 )	//no es error se puede darse esa situación (por ejemplo Honorario) se podría tratar en cTesorero???
						{ 
							//No poner $reCuotasSocioElegidaCuotasAnioSocio['codError'] = '80001'; por no haría commitPDO de la anterior actualizarTabla('IMPORTEDESCUOTAANIO',...)
							
							$resActualizarCuotasVigentesEL['numFilas'] = $reCuotasSocioElegidaCuotasAnioSocio['numFilas'];//será = 0
							$resActualizarCuotasVigentesEL['textoComentarios'] .= "<br /><br />En el año <strong>".$arrayDatos['ANIOCUOTA']['valorCampo'].
																																																													"</strong> no se han encontrado socios/as con cuotas tipo -<strong>".$arrayDatos['CODCUOTA']['valorCampo'].
																																																													"</strong>- a los que haya que actualizar sus cuotas al nuevo importe para el año <strong>"
																																																													.$arrayDatos['ANIOCUOTA']['valorCampo']."</strong>" ;		
						}
						else //if ($reCuotasSocioElegidaCuotasAnioSocio['codError'] !== '00000' )	&& elseif ($reCuotasSocioElegidaCuotasAnioSocio['numFilas'] === 0 )	
						{//--- Inicio actualizar en CUOTAANIOSOCIO al nuevo importe de cuota vigente para el tipo de cuota de ese socio ---		
							
							$f = 0;
							$totalFilas = $reCuotasSocioElegidaCuotasAnioSocio['numFilas'];
							$resActualizarCuotaAnioSocio['codError'] = '00000';
							
							$fila = $reCuotasSocioElegidaCuotasAnioSocio['resultadoFilas'];		
							
							//-- En caso de error en actualizarTabla() cambiará el valor $resActualizarCuotaAnioSocio['codError']!=='00000'	y sale del bucle	
			
							$contadorCambiosCUOTAANIOSOCIO = 0;			
			
							while ($f < $totalFilas && $resActualizarCuotaAnioSocio['codError'] === '00000')																														    
							{
								//echo "<br><br>3-3-1 modeloTesorero:actualizarCuotasVigentesEL:fila: ";print_r($fila[$f]);
															
								$arrayDatosCuotaSocio['IMPORTECUOTAANIOEL'] = $arrayDatos['IMPORTECUOTAANIOEL_NUEVO']['valorCampo'];//para todos 	
							
								if ($fila[$f]['cuotaniosocioIMPORTECUOTAANIOSOCIO'] <= $arrayDatos['IMPORTECUOTAANIOEL_NUEVO']['valorCampo'])
								{ //echo "<br><br>3-3-2 modeloTesorero:actualizarCuotasVigentesEL:fila: ";print_r($fila[$f]);
							
										$arrayDatosCuotaSocio['IMPORTECUOTAANIOSOCIO'] = $arrayDatos['IMPORTECUOTAANIOEL_NUEVO']['valorCampo'];
										
										if ($fila[$f]['cuotaniosocioIMPORTECUOTAANIOSOCIO'] < $arrayDatos['IMPORTECUOTAANIOEL_NUEVO']['valorCampo'])
										{	//echo "<br><br>3-3-3 modeloTesorero:actualizarCuotasVigentesEL:fila: ";print_r($fila[$f]);								
												$contadorCambiosCUOTAANIOSOCIO++;
										}				
								}			
								else //($fila[$f]['cuotaniosocioIMPORTECUOTAANIOSOCIO'] > $arrayDatos['IMPORTECUOTAANIOEL_NUEVO']['valorCampo'])//baja sobre anterior
								{ //echo "<br><br>3-3-4 modeloTesorero:actualizarCuotasVigentesEL:fila: ";print_r($fila[$f]);
								
									if ($fila[$f]['socioIMPORTECUOTAANIOSOCIO'] > $arrayDatos['IMPORTECUOTAANIOEL_NUEVO']['valorCampo'])
									{	//echo "<br><br>3-3-5 modeloTesorero:actualizarCuotasVigentesEL:fila: ";print_r($fila[$f]);
								
											$arrayDatosCuotaSocio['IMPORTECUOTAANIOSOCIO'] = $fila[$f]['socioIMPORTECUOTAANIOSOCIO'];//la que tiene tabla SOCIO es mayor

											if ($fila[$f]['socioIMPORTECUOTAANIOSOCIO'] < $fila[$f]['cuotaniosocioIMPORTECUOTAANIOSOCIO'])
											{ //echo "<br><br>3-3-6 modeloTesorero:actualizarCuotasVigentesEL:fila: ";print_r($fila[$f]);
													$contadorCambiosCUOTAANIOSOCIO++;	
											}				
									} 
									else //($fila['socioIMPORTECUOTAANIOSOCIO'] <= $arrayDatos['IMPORTECUOTAANIOEL_NUEVO']['valorCampo']) 
									{ //echo "<br><br>3-3-7 modeloTesorero:actualizarCuotasVigentesEL:fila: ";print_r($fila[$f]);
									
											$arrayDatosCuotaSocio['IMPORTECUOTAANIOSOCIO'] = $arrayDatos['IMPORTECUOTAANIOEL_NUEVO']['valorCampo'];	
											$contadorCambiosCUOTAANIOSOCIO++;			
									}
								}				
								
								$arrayCondicionesCuotaSocio['CODSOCIO']['valorCampo'] = $fila[$f]['CODSOCIO'];
								$arrayCondicionesCuotaSocio['CODSOCIO']['operador'] = '=';
								$arrayCondicionesCuotaSocio['CODSOCIO']['opUnir'] = ' AND ';
									
								$arrayCondicionesCuotaSocio['ANIOCUOTA']['valorCampo'] = $arrayDatos['ANIOCUOTA']['valorCampo'];
								$arrayCondicionesCuotaSocio['ANIOCUOTA']['operador'] = '=';
								$arrayCondicionesCuotaSocio['ANIOCUOTA']['opUnir'] = ' ';					
		
								/*-- En este bucle se actualiza CUOTAANIOSOCIO una fila cada vez para los socios que cumpla la condición			
										Cuando actualizarTabla() no modifica ningún campo, por ser iguales a los que ya tenía, 
										no modifica nada y devuelve resActualizarCuotaAnioSocionumfilas=0	y no da error.
										Hacien la suma "$contadorCambiosCUOTAANIOSOCIO" se podría comprobar si coincide con "$reCuotasSocioElegidaCuotasAnioSocio['numFilas']"
										Es está función siempre coincidirá "$contadorCambiosCUOTAANIOSOCIO" = "$reCuotasSocioElegidaCuotasAnioSocio['numFilas']"
								*/							

								$resActualizarCuotaAnioSocio = actualizarTabla('CUOTAANIOSOCIO',$arrayCondicionesCuotaSocio,$arrayDatosCuotaSocio,$conexionDB['conexionLink']);//error ok 
								
								//echo "<br><br>3-6 modeloTesorero:actualizarCuotasVigentesEL:resActualizarCuotaAnioSocio: ";print_r($resActualizarCuotaAnioSocio);		
								
								//$contadorCambiosCUOTAANIOSOCIO = $contadorCambiosCUOTAANIOSOCIO + $resActualizarCuotaAnioSocio['numFilas'];//!= no sirve $contadorCambiosCUOTAANIOSOCIO++;								
								
								$f++;						
							}//fin while
							
							//echo "<br><br>4 modeloTesorero:actualizarCuotasVigentesEL:resActualizarCuotaAnioSocio: ";print_r($resActualizarCuotaAnioSocio);	
				
							if ($resActualizarCuotaAnioSocio['codError'] !== '00000')			
							{ 
									$resActualizarCuotasVigentesEL['codError'] = $resActualizarCuotaAnioSocio['codError'];
									$resActualizarCuotasVigentesEL['errorMensaje'] = $resActualizarCuotaAnioSocio['errorMensaje'];								
									$textoErrores = "Error del sistema en modeloTesorero.php:actualizarCuotasVigentesEL(), al actualizar CUOTAANIOSOCIO con la función actualizarTabla()";		
							}					
							else //$resActualizarCuotaAnioSocio['codError']=='00000'
							{	
									$resActualizarCuotasVigentesEL['textoComentarios'] .= "<br /><br />Se han modificado el importe de las cuotas tipo -<strong>".$arrayDatos['CODCUOTA']['valorCampo'].
																																																														"</strong>- de <strong>".$contadorCambiosCUOTAANIOSOCIO.
																																																														"</strong> socios/as, del total de ".$reCuotasSocioElegidaCuotasAnioSocio['numFilas'].
																																																														" que ya tenían anotada la cuota para el año próximo (con cuota abonada en el año actual), 
																																																														como consecuencia del cambio del anterior  importe de <strong>".$arrayDatos['IMPORTECUOTAANIOEL']['valorCampo'].
																																																														" euros</strong>, al nuevo importe mínimo de <strong>".$arrayDatos['IMPORTECUOTAANIOEL_NUEVO']['valorCampo'].
																																																														" euros</strong>, que será el vigente para el año <strong>".$arrayDatos['ANIOCUOTA']['valorCampo']."</strong>";
							}
							/*--- Fin actualizar en CUOTAANIOSOCIO al nuevo importe de cuota vigente para el tipo de cuota de ese socio --*/			
							
						}//else if ($reCuotasSocioElegidaCuotasAnioSocio['codError'] !== '00000' )	&& elseif ($reCuotasSocioElegidaCuotasAnioSocio['numFilas'] === 0 )
						/*--- Fin actualizar en CUOTAANIOSOCIO al nuevo importe de cuota vigente para el tipo de cuota de ese socio --*/									
					
						if ($resActualizarCuotasVigentesEL['codError'] === '00000')																																		
						{	//----------------Inicio COMMIT ------------------------------------
								//echo "<br><br>5-1 modeloTesorero:actualizarCuotasVigentesEL:resActualizarCuotasVigentesEL:commitPDO: ";print_r($resActualizarCuotasVigentesEL);	
								
								$resFinTrans = commitPDO($conexionDB['conexionLink']);
								
								if ($resFinTrans['codError'] !== '00000')//sera $resFinTrans['codError'] = '70502';		
								{$resFinTrans['errorMensaje'] = 'Error en el sistema, no se han podido finalizar transación del socio/a. ';
									$resFinTrans['numFilas'] = 0;							
									$resActualizarCuotasVigentesEL = $resFinTrans;	
									
									//echo "<br><br>5-2 modeloTesorero:actualizarCuotasVigentesEL:resActualizarCuotasVigentesEL: ";print_r($resActualizarCuotasVigentesEL);
								}												
								else //resFinTrans['codError'] == '00000'
								{
									$resActualizarCuotasVigentesEL['textoComentarios'] .= "<br /><br /><br />Se efectuado el proceso de actualización de los importes 
																																												de las cuotas vigentes de Europa Laica sin errores detectados<br /><br />";							
								}
						}//else $resActualizarCuotasVigentesEL['codError']=='00000'				
					}//elseif ($resActualizarImportesCuotasEL['numFilas'] === 1)
				}//else $resActualizarCuotasVigentesEL['codError'] =='00000')

				//echo "<br><br>6 modeloTesorero:actualizarCuotasVigentesEL:resActualizarCuotasVigentesEL: ";print_r($resActualizarCuotasVigentesEL);
					
				//---------------- Inicio tratamiento errores -------------------------------	
   
			 //--- Inicio deshacer transación en las tablas modificadas ---------------
				if ($resActualizarCuotasVigentesEL['codError'] !== '00000')
				{				
						$resDeshacerTrans = rollbackPDO($conexionDB['conexionLink']);						
						
						if ($resDeshacerTrans['codError'] !== '00000')//sera $resDeshacerTrans['codError'] = '70503';
						{ $resDeshacerTrans['errorMensaje'] .= 'Error en el sistema, no se ha podido deshacer la transación. ';
								$resDeshacerTrans['numFilas'] = 0;
								$resActualizarCuotasVigentesEL = $resDeshacerTrans;	
						}	
				}//--- Fin deshacer transación en las tablas modificadas -----------------	
			}//else $resIniTrans['codError'] == '00000				
	 }//else // !if (!isset($arrayDatos) || empty($arrayDatos))
		
		//--- Inicio Grabar en tabla ERRORES, incluido error beginTransationPDO()--	
			
		if ($resActualizarCuotasVigentesEL['codError'] !== '00000')
		{				
				$resActualizarCuotasVigentesEL['textoComentarios'] = $textoErrores." Función: ".$nomScriptFuncionError;			
				
				require_once './modelos/modeloErrores.php'; //si es un error de conexión a la tabla error, insertar errores 
				$resInsertarErrores = insertarError($resActualizarCuotasVigentesEL);						
				
				if ($resInsertarErrores['codError'] !== '00000')
				{ $resActualizarCuotasVigentesEL['codError'] = $resInsertarErrores['codError'];
						$resActualizarCuotasVigentesEL['errorMensaje'] .= $resInsertarErrores['errorMensaje'];
				}				
		}//if ($resActualizarCuotasVigentesEL['codError']!=='00000')	
	 //--- Fin Grabar en tabla ERRORES, incluido error beginTransationPDO()----
		
	 //---------------- Fin tratamiento errores ------------------------------------	
	}//$conexionDB['codError']=="00000"
 
	//echo "<br><br>7 modeloTesorero:actualizarCuotasVigentesEL:resActualizarCuotasVigentesEL: ";print_r($resActualizarCuotasVigentesEL);

	return $resActualizarCuotasVigentesEL;
} 
/*---------------------------------- Fin actualizarCuotasVigentesEL ------------------------------------*/


/*==== FIN: FUNCIONES RELACIONADAS CON ACTUALIZAR CUOTAS VIGENTES  ==============================================
================================================================================================================*/	


/*=============== INICIO: FUNCIONES RELACIONADAS CON DONACIONES =================================================
================================================================================================================*/

/*---------------------------- Inicio buscarDonante -------------------------------
Busca los datos personales de un donante en la las tablas existieseen las tablas. 
Primero en MIEMBRO(socio o simpatizante) y después en DONANTE partir del los 
datos de Nº documento (NIF,NIE,Pasaporte)

LLAMADA: cTesorero:anotarIngresoDonacionMenu()
LLLAMA: modeoTesorero.php:buscarMiembroNumDocEmail(),buscarDonanteNumDocEmail() 

OBSERVACIONES: En esta función no se necesitan $arrBindValues para PDO, lo incluyen
algunas de las funciones que utiliza
2020-09-18: probada PHP 7.3.21
---------------------------------------------------------------------------------*/
function buscarDonante($codDocEmail)
{
	//echo "<br><br>0-1 modeloTesorero:buscarDonante:codDocEmail:";print_r($codDocEmail);

	$resDonanteSocSim =	buscarMiembroNumDocEmail($codDocEmail);	//busca si es socio		
	//echo "<br><br>2 modeloTesorero:buscarDonante:resDonanteSocSim: ";print_r($resDonanteSocSim);										
		
	if ($resDonanteSocSim['codError'] == '00000' && $resDonanteSocSim['numFilas'] !== 1)//si no se encuentra en MIEMBRO busca en DONACION
	{		
		$resDonanteSocSim = buscarDonanteNumDocEmail($codDocEmail);//busca si ya es donante previo	
		//echo "<br><br>3 modeloTesorero:buscarDonante:resDonanteSocSim: ";print_r($resDonanteSocSim);
	}	
	
	if ($resDonanteSocSim['codError'] !== '00000')
	{$resDonanteSocSim['arrMensaje']['textoCabecera'] = 'Anotar donaciones';
		$resDonanteSocSim['arrMensaje']['textoComentarios'].=".Error del sistema, vuelva a intentarlo pasado un tiempo ";

		require_once './modelos/modeloErrores.php';
		insertarError($resDonanteSocSim);		//creo que no sería necesario lo incluyen las funciones que se llaman
	}				

	//echo '<br><br>4 modeloTesorero:buscarDonante:resDonanteSocSim: ';print_r($resDonanteSocSim);
	return $resDonanteSocSim;
}
//---------------------------- Fin buscarDonante -------------------------------


/*---------------------------- Inicio buscarMiembroNumDocEmail ------------------
Busca los datos personales en MIEMBRO (socio o simpatizante), a partir 
del los datos del documento (NIF,NIE,Pasaporte  o email) 

LLAMADA: modeloTesorero:buscarDonante()
LLAMA: modelos/BBDD/MySQL/modeloMySQL.php:buscarCadSql()
modelos/modeloErrores.php:insertarError()
usuariosConfig/BBDD/MySQL/configMySQL.php";	
modelos/BBDD/MySQL/conexionMySQL.php:conexionDB()

OBSERVACIONES: En esta función utiliza $arrBindValues para PDO, 
2020-09-18: probada PHP 7.3.21
----------------------------------------------------------------------------------*/
function buscarMiembroNumDocEmail($codDocEmail)
{
	//echo "<br><br>0-1 modeloTesorero:buscarMiembroNumDocEmail:codDocEmail: "; print_r($codDocEmail);
	
	require_once './modelos/modeloErrores.php';
 require_once "./modelos/BBDD/MySQL/modeloMySQL.php";			 
	
	$resDonanteSocSim['nomScript'] = "modeloTesorero.php";
	$resDonanteSocSim['nomFuncion'] = "buscarMiembroNumDocEmail";	
	$resDonanteSocSim['codError'] = '00000';
	$resDonanteSocSim['errorMensaje'] = '';
 $resDonanteSocSim['textoComentarios'] = '';

	$arrBind = array();
	
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	require_once "./modelos/BBDD/MySQL/conexionMySQL.php";			
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);	
	
	if ($conexionDB['codError']!=='00000')	
	{ $resDonanteSocSim = $conexionDB;
	}
	else// $conexionDB['codError'] =='00000'
	{
		$tablasBusqueda = 'MIEMBRO';
		$camposBuscados = 'TIPOMIEMBRO,CODPAISDOC,NUMDOCUMENTOMIEMBRO,TIPODOCUMENTOMIEMBRO,APE1,APE2,NOM,SEXO,EMAIL,TELFIJOCASA,TELMOVIL';
	
		if ( isset($codDocEmail['EMAIL']['valorCampo']) && !empty($codDocEmail['EMAIL']['valorCampo']))
	 { 	   
				$cadCondicionesBuscar = " WHERE EMAIL = :email ";

			 $arrBind[':email'] = $codDocEmail['EMAIL']['valorCampo'];
		}
		else
		{																									
   $cadCondicionesBuscar = " WHERE CODPAISDOC = :codPaisDoc ".
		                         " AND TIPODOCUMENTOMIEMBRO = :tipoDoc ".
																									 	" AND NUMDOCUMENTOMIEMBRO = :numDoc ";																												

   $arrBind[':codPaisDoc'] = $codDocEmail['CODPAISDOC']['valorCampo'];
			$arrBind[':tipoDoc'] = $codDocEmail['TIPODOCUMENTOMIEMBRO']['valorCampo'];
			$arrBind[':numDoc'] = $codDocEmail['NUMDOCUMENTOMIEMBRO']['valorCampo'];																		
		}																						

  $cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";
		//echo "<br /><br />1 modeloTesorero:buscarMiembroNumDocEmail:cadSql: ";print_r($cadSql);
		
		$resBuscarDonanteSocSim = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);//en modeloMySQL.php";	probado error
		
		//echo '<br><br>2 modeloTesorero:buscarMiembroNumDocEmail:resBuscarDonanteSocSim: ';print_r($resBuscarDonanteSocSim);
																										
		if ($resBuscarDonanteSocSim['codError'] !== '00000')
		{$resDonanteSocSim['errorMensaje'] = $resBuscarDonanteSocSim['errorMensaje']; 
			$resDonanteSocSim['codError'] = $resBuscarDonanteSocSim['codError'];
			$resDonanteSocSim['textoComentarios'] = $resDonanteSocSim['nomScript'].": ".$resDonanteSocSim['nomFuncion'].".Error del sistema, al buscar los datos del donante. ";	
			insertarError($resDonanteSocSim);		
		}	
		else //$resBuscarDonanteSocSim['codError']=='00000')
		{ 		  			
		  if ($resBuscarDonanteSocSim['numFilas'] == 1)
				{//$resDonanteSocSim['codError'] = '00000'; 
		   //$resDonanteSocSim['errorMensaje'] = ""; 				
					
					foreach ($resBuscarDonanteSocSim['resultadoFilas'][0] as $indice => $contenido)                         
				 { 
					   $aux['resultadoFilas']['datosFormDonacion'][$indice]['valorCampo'] = $contenido;
				 }
					
					$aux['resultadoFilas']['datosFormDonacion']['TIPODONANTE']['valorCampo'] = $aux['resultadoFilas']['datosFormDonacion']['TIPOMIEMBRO']['valorCampo'];
     unset($aux['resultadoFilas']['datosFormDonacion']['TIPOMIEMBRO']['valorCampo']);		
					
					$resDonanteSocSim['resultadoFilas'] = $aux['resultadoFilas'];					
			 }
				
				$resDonanteSocSim['numFilas'] = $resBuscarDonanteSocSim['numFilas']; //será 1 o bien 0
 	}//else $resBuscarDonanteSocSim['codError']=='00000')
		
	}//else $conexionDB['codError'] =='00000'
	
 //echo '<br><br>4 modeloTesorero:buscarMiembroNumDocEmail: ';print_r($resDonanteSocSim);echo '<br><br>';
		
	return $resDonanteSocSim;
}
//---------------------------- Fin buscarMiembroNumDocEmail ----------------------

/*---------------------------- Inicio buscarDonanteNumDocEmail -------------------
Busca los datos personales en DONACION (socio o simpatizante), a partir 
del los datos del documento (NIF,NIE,Pasaporte  o email) 

LLAMADA: modeloTesorero:buscarDonante()
LLAMA: modelos/BBDD/MySQL/modeloMySQL.php:buscarCadSql()
modelos/modeloErrores.php:insertarError()
usuariosConfig/BBDD/MySQL/configMySQL.php";	
modelos/BBDD/MySQL/conexionMySQL.php:conexionDB()

OBSERVACIONES: En esta función utiliza $arrBindValues para PDO, 
2020-09-18: probada PHP 7.3.21

Nota: la condición CONTROLERROR != 'ERROR-ANULADA'es para que NO busque las donaciones 
anuladas por error o duplicidad, y que se mantiene para no eliminar 
físicamente esas filas y dejar un hueco ya que están ordenadas según CODDONACION
-----------------------------------------------------------------------------------*/
function buscarDonanteNumDocEmail($codDocEmail)
{
	//echo "<br><br>0-1 modeloTesorero:buscarDonanteNumDocEmail:codDocEmail: "; print_r($codDocEmail);

	require_once './modelos/modeloErrores.php';
 require_once "./modelos/BBDD/MySQL/modeloMySQL.php";			 
	
	$resDonanteSocSim['nomScript'] = "modeloTesorero.php";
	$resDonanteSocSim['nomFuncion'] = "buscarDonanteNumDocEmail";	
	$resDonanteSocSim['codError'] = '00000';
	$resDonanteSocSim['errorMensaje'] = '';
 $resDonanteSocSim['textoComentarios'] = '';

	$arrBind = array();
	
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	require_once "./modelos/BBDD/MySQL/conexionMySQL.php";			
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);

	if ($conexionDB['codError'] !=='00000')	
	{ $resDonanteSocSim = $conexionDB;
	}
	else// $conexionDB['codError'] =='00000'
	{
	 $tablasBusqueda = 'DONACION';
		$camposBuscados = 'TIPODONANTE,CODPAISDOC,NUMDOCUMENTOMIEMBRO,TIPODOCUMENTOMIEMBRO,
		                   APE1,APE2,NOM,SEXO,EMAIL,TELFIJOCASA,TELMOVIL';
																					
		$condicionControlError = " AND CONTROLERROR != 'ERROR-ANULADA' ";					
	
		if ( isset($codDocEmail['EMAIL']['valorCampo']) && !empty($codDocEmail['EMAIL']['valorCampo']))
	 { 	   
				$cadCondicionesBuscar = " WHERE EMAIL = :email ".$condicionControlError;

			 $arrBind[':email'] = $codDocEmail['EMAIL']['valorCampo'];
		}
		else
		{																									
   $cadCondicionesBuscar = " WHERE CODPAISDOC = :codPaisDoc ".
		                         " AND TIPODOCUMENTOMIEMBRO = :tipoDoc ".
																									 	" AND NUMDOCUMENTOMIEMBRO = :numDoc".$condicionControlError;  																										

   $arrBind[':codPaisDoc'] = $codDocEmail['CODPAISDOC']['valorCampo'];
			$arrBind[':tipoDoc'] = $codDocEmail['TIPODOCUMENTOMIEMBRO']['valorCampo'];
			$arrBind[':numDoc'] = $codDocEmail['NUMDOCUMENTOMIEMBRO']['valorCampo'];																		
		}																						
	
		$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";
		//echo "<br /><br />1 modeloTesorero:buscarDonanteNumDocEmail:cadSql: ";print_r($cadSql);
		
		$resBuscarDonanteSocSim = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);//en modeloMySQL.php";	probador error
		
		//echo '<br><br>2 modeloTesorero:buscarDonanteNumDocEmail:resBuscarDonanteSocSim: ';print_r($resBuscarDonanteSocSim);
																							
		if ($resBuscarDonanteSocSim['codError'] !== '00000')
		{$resDonanteSocSim['errorMensaje'] = $resBuscarDonanteSocSim['errorMensaje']; 
			$resDonanteSocSim['codError'] = $resBuscarDonanteSocSim['codError'];
			$resDonanteSocSim['textoComentarios'] = $resDonanteSocSim['nomScript'].": ".$resDonanteSocSim['nomFuncion'].".Error del sistema, al buscar los datos del donante. ";	
			insertarError($resDonanteSocSim);		
		}	
		else//$resDonanteSocSim['codError'] =='00000')
		{ 
		  $resDonanteSocSim['numFilas'] = $resBuscarDonanteSocSim['numFilas'];
		
		  if ($resDonanteSocSim['numFilas'] >= 1)
				{
					$resDonanteSocSim['codError'] = '00000';//sobra está al principio 
		   $resDonanteSocSim['errorMensaje'] = ""; 
					
					foreach ($resBuscarDonanteSocSim['resultadoFilas'][0] as $indice => $contenido)                         
				 {					
       $aux['resultadoFilas']['datosFormDonacion'][$indice]['valorCampo'] = $contenido;
				 }
					
					$resDonanteSocSim['resultadoFilas'] = $aux['resultadoFilas']; 
					$resDonanteSocSim['numFilas'] = 1;//solo envío la primera fila si hay varias con el mismo donante
			 }
 	}
	}
	//echo '<br><br>3 modeloTesorero:buscarDonanteNumDocEmail:resDonanteSocSim: ';print_r($resDonanteSocSim);
	
	return $resDonanteSocSim;
}
//---------------------------- Inicio buscarDonanteNumDocEmail ---------------------

/*------------------- Inicio insertarDonacion --------------------------------------
Se insertan los datos de una donación previamente se busca el PK última en DONACION 
para insertar con el siguiente valor de PK 

RECIBE: $resulValidar  array     
DEVUELVE: un array con los controles de errores, y mensajes 

LLAMADA: desde controladorTesorero.php:anotarIngresoDonacion(),
LLAMA: modelos/BBDD/MySQL/modeloMySQL.php:insertarUnaFila()
modelos/libs/buscarCodMax.php
modelos/modeloErrores.php:insertarError()
usuariosConfig/BBDD/MySQL/configMySQL.php";	
modelos/BBDD/MySQL/conexionMySQL.php:conexionDB()

OBSERVACIONES: PHP 7.3.21. En esta función no se necesitan $arrBindValues para PDO, lo incluyen
algunas de las funciones que utiliza
-----------------------------------------------------------------------------------*/
function insertarDonacion($resulValidar)
{
	//echo "<br><br>0-1 modeloTesorero:insertarDonacion:resulValidar: "; print_r($resulValidar);
	
	require_once './modelos/modeloErrores.php';
 require_once "./modelos/BBDD/MySQL/modeloMySQL.php";			 
	
	$resInsertarDonacion['nomScript'] = "modeloTesorero.php";	
	$resInsertarDonacion['nomFuncion'] = "insertarDonacion";	
 $resInsertarDonacion['codError'] = '00000';
 $resInsertarDonacion['errorMensaje'] = '';	 
	$resInsertarDonacion['textoComentarios'] = '';
	$nomScriptFuncionError = ' modeloTesorero.php:insertarDonacion:(). Error: ';	

	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	require_once "./modelos/BBDD/MySQL/conexionMySQL.php";			
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);
 	
	if ($conexionDB['codError'] !=='00000')	
	{ $resInsertarDonacion = $conexionDB;
	}
	else //$conexionDB['codError'] =='00000'
	{
		if (!isset($resulValidar) || empty($resulValidar))
		{ 
			$resInsertarDonacion['codError'] = '70601';
			$resInsertarDonacion['errorMensaje'] = $nomScriptFuncionError.": Faltan algunas variables-parámetros (resulValidar) necesarios para SQL -insertarUnaFila()- ";	 
			$resInsertarDonacion['textoComentarios'] = $nomScriptFuncionError;
		}	
  else
  {			
			$tablasBusqueda = 'DONACION';
			$camposBuscados = 'CODDONACION';
			
			require_once './modelos/libs/buscarCodMax.php';
			$resulBuscarCodMax = buscarCodMax($tablasBusqueda,$camposBuscados,$conexionDB['conexionLink']);//busca el PK última en DONACION 
			
			//echo "<br>1 modeloTesorero:insertarIngresoDonacion:resulBuscarCodMax: "; print_r($resulBuscarCodMax);
			
			if ($resulBuscarCodMax['codError']!=='00000')
			{ $resInsertarDonacion = $resulBuscarCodMax;
					$resInsertarDonacion['textoComentarios'] = "Error del sistema al insertar donación en: buscarCodMax(). ";	
			}
			else // $resulBuscarCodMax['codError']=='00000'
			{$arrValoresInser['CODDONACION'] = $resulBuscarCodMax['valorCampo'];
			
				$fechaCompacta = $resulValidar['datosFormDonacion']['FECHAINGRESO']['anio']['valorCampo'].'-'.
																					$resulValidar['datosFormDonacion']['FECHAINGRESO']['mes']['valorCampo'].'-'.
																					$resulValidar['datosFormDonacion']['FECHAINGRESO']['dia']['valorCampo'];

				$resulValidar['datosFormDonacion']['FECHAINGRESO']['valorCampo'] = $fechaCompacta;																																					
				$resulValidar['datosFormDonacion']['FECHAANOTACION']['valorCampo'] = date('Y-m-d');
				
				if (!isset($resulValidar['datosFormDonacion']['NUMDOCUMENTOMIEMBRO']['valorCampo']) ||
								empty($resulValidar['datosFormDonacion']['NUMDOCUMENTOMIEMBRO']['valorCampo']))
				{ 
						$resulValidar['datosFormDonacion']['CODPAISDOC']['valorCampo'] = NULL;
						$resulValidar['datosFormDonacion']['TIPODOCUMENTOMIEMBRO']['valorCampo'] = NULL;
				}	// si no hay NIF ni otros tipos de documentos que estos dos campos tampoco tengan valores																																		
				
				//echo "<br>2 modeloTesorero:insertarIngresoDonacion:resulValidar: "; print_r($resulValidar);//

				foreach ($resulValidar['datosFormDonacion'] as $nomCampo=>$valNomCampo)
				{
					$arrValoresInser[$nomCampo] = $valNomCampo['valorCampo'];
				}
					
				//echo "<br>3 modeloTesorero:insertar:arrValoresInser: "; print_r($arrValoresInser);
				
				$resInsertarFilaDonacion = insertarUnaFila('DONACION',$arrValoresInser,$conexionDB['conexionLink']);	//en modeloMySQL.php probado error		

				//echo "<br>4 modeloTesorero:insertarDonacion:resInsertarFilaDonacion: "; print_r($resInsertarFilaDonacion); 

				if ($resInsertarFilaDonacion['codError'] !== '00000')
				{ $resInsertarDonacion = $resInsertarFilaDonacion;
						$resInsertarDonacion['textoComentarios'] = "Error del sistema al insertar donación en: insertarUnaFila(). ";
				}		
				
			}//else $resulBuscarCodMax['codError'] == '00000'		
		
		}
	 //echo "<br>5 modeloTesorero:insertarDonacion:resInsertarDonacion: "; print_r($resInsertarDonacion); 
		
		if ($resInsertarDonacion['codError'] !== '00000')
		{ 
				$resInsertarDonacion['textoComentarios'] .= $nomScriptFuncionError;					 
				insertarError($resInsertarDonacion);	
		}	
  else
		{ $resInsertarDonacion = $resInsertarFilaDonacion;				//creo que no es necesario	
		}			
	}//else $conexionDB['codError'] == '00000'		
	
 //echo "<br>6 modeloTesorero:insertarDonacion:resInsertarDonacion: "; print_r($resInsertarDonacion); 
	
 return $resInsertarDonacion;
}
/*----------------------------- Fin insertarDonacion ------------------------------*/


/*---------------------- Inicio cadBuscarDonacionesApeNom -----------------------
DESCRIPCIÓN: Forma la cadena select sql para busca por APE1 y APE2 los datos de 
todas las donaciones realizadas, ordenado por: ORDER BY apeNom ASC, FECHAINGRESO DESC 
													
El campo IMPORTEDONACION, lo recupera también con otros formatos númericos para 
mayor flexibilidad de uso.
												
-la condición CONTROLERROR != 'ERROR-ANULADA' es para que NO busque las donaciones 
	anuladas por error o duplicidad, y que se mantiene para no eliminar 
	físicamente esas filas y dejar un hueco ya que están ordenadas según CODDONACION															
													
LLAMADA: cTesorero.php:mostrarDonaciones():/controladores/libs/cTesoreroDonacionesApeNomPaginarInc.php
         para pasarla a la función  "/modelos/libs/mPaginarLib.php"
									
LLAMA: modelos/libs/eliminarAcentos.php:cambiarAcentosEspeciales()								
								
OBSERVACIONES:
2020-09-15: Adaptada para PDO BindValues y PHP 7.3.21 
2020_01_22: añado en select TIPODOCUMENTOMIEMBRO,NUMDOCUMENTOMIEMBRO,CODPAISDOC
2018_10_03: añado CONCEPTO  para  que incluya "GENERAL", "COSTAS_MEDALLA_VIRGEN_MERITO_POLICIAL",
CONCEPTO_DONACION, y también SEXO.
2017-02-03: Añadido select para totales MPORTEDONACION_PUNTO_DECIMAL, GASTOSDONACION 															
------------------------------------------------------------------------------------*/
function cadBuscarDonacionesApeNom($cadApe1,$cadApe2)
{
	//echo "<br><br>0-1 modeloTesorero:cadBuscarDonacionesApeNom: ".$cadApe1.$cadApe2;
	
	require_once './modelos/libs/eliminarAcentos.php';
		
	$arrBind = array();		

 $condicionControlError = " CONTROLERROR != 'ERROR-ANULADA' ";		

			
	/*---------- Inicio APE1 y AP2 -------------------------------------------
	NOTA: Previamente desde las funciones de llamada controlo el caso de que sean a la vez: empty($cadApe1) y empty($cadApe2) 
	y en ese caso no permito ="%" cambio valores: $cadApe1='---******---' y $cadApe2='---******---' estos caracteres no están
	permitidos para los apellidos por lo que la select devolverá 0 filas en el caso de que los dos estén empty
	y envío "errorMensaje"="Al menos uno de los apellidos no puede estar vacío"
	-------------------------------------------------------------------------*/
	if ( !isset($cadApe1) || empty($cadApe1) || $cadApe1 == '%' )//OJO: !isset($cadApe1) || empty($cadApe1) considera = "%", pues se puede buscar solo por un APE2 
	{ 
		$condicionApe1 = '';			
		//echo "<br><br>2-1 modeloTesorero:cadBuscarDonacionesApeNom: ";
	}
	else
	{
		$cadApe1 = cambiarAcentosEspeciales($cadApe1);
		
		//$condicionApe1 = " AND DONACION.APE1 LIKE '$cadApe1' ";//necesario el like aquí
		$condicionApe1 = " AND DONACION.APE1 LIKE :cadApe1 ";//necesario el like aquí		
	
		$arrBind[':cadApe1'] = $cadApe1; 
			
		//echo "<br><br>2-2 modeloTesorero:cadBuscarDonacionesApeNom: ";
	}

	if ( !isset($cadApe2) || empty($cadApe2) || $cadApe2 == '%' )//OJO: !isset($cadApe2) || empty($cadApe2) considera = "%", pues se puede buscar solo por un APE1 
	{ 
	  $condicionApe2 = '';			
			//echo "<br><br>3-1 modeloTesorero:cadBuscarDonacionesApeNom: ";
	}
	else
	{
		$cadApe2 = cambiarAcentosEspeciales($cadApe2);
		
		//$condicionApe2 = " AND DONACION.APE2 LIKE '$cadApe2' ";//necesario el like aquí
		$condicionApe2 = " AND DONACION.APE2 LIKE :cadApe2 ";//necesario el like aquí
		
		$arrBind[':cadApe2'] = $cadApe2; 
		
		//echo "<br><br>3-2 modeloTesorero:cadBuscarDonacionesApeNom: ";
	}
	//---------- Fin APE1 y AP2 --------------------------------------------------
	
 $tablasBusqueda = " DONACION ";	
		
	$camposBuscados = " CODDONACION, DATE_FORMAT(FECHAINGRESO, '%Y-%m-%d ') as FECHAINGRESO, 
	                    CONCAT(APE1,' ',IFNULL(APE2,''),', ',NOM) as apeNom, SEXO,
																					
																					IMPORTEDONACION,
																					IMPORTEDONACION AS IMPORTEDONACION_PUNTO_DECIMAL,	
																					REPLACE(IMPORTEDONACION,'.',',') AS IMPORTEDONACION_COMA_DECIMAL,																						
																					
																					MODOINGRESO,CONCEPTO,TIPODONANTE,EMAIL,DATE_FORMAT(FECHAANOTACION, '%Y-%m-%d ') as FECHAANOTACION ";
																

 /*$cadCondicionesBuscar = " WHERE CODAGRUPACION LIKE '$condicionAgrupacion' ".$condicionControlError.$condicionApe1.$condicionApe2.	
																									  	" ORDER BY apeNom ASC, FECHAINGRESO DESC ";
*/																												
 $cadCondicionesBuscar = " WHERE ".$condicionControlError.$condicionApe1.$condicionApe2.	
																									  	" ORDER BY apeNom ASC, FECHAINGRESO DESC ";																												
				
	$cadBuscarDonacionesApeNom = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";	
																																														
	//echo '<br><br>4-1 modeloTesorero:cadBuscarDonacionesApeNom: ';print_r($cadBuscarDonacionesApeNom);
	
 $arrBuscarDonacionesApeNom['cadSQL'] = $cadBuscarDonacionesApeNom;

 $arrBuscarDonacionesApeNom['arrBindValues']	= $arrBind;
	
	//echo '<br><br>4-2 modeloTesorero:arrBuscarDonacionesApeNom): ';print_r($arrBuscarDonacionesApeNom);
		
	return $arrBuscarDonacionesApeNom;
}
/*------------------------------ Fin cadBuscarDonacionesApeNom ---------------------*/

/*---------------------------- Inicio cadBuscarDonaciones ---------------------------
DESCRIPCIÓN: Forma la cadena select sql para busca los datos de las todas las 
donaciones recibidas por la asociación, ordenados por: 
DONACION.FECHAINGRESO DESC, apeNom

Los campos IMPORTEDONACION, GASTOSDONACION los recupera también con otros formatos 
númericos para mayor flexibilidad de uso si fuese necesario

-actualmente recibe siempre $codAgrup ='%' ya que el tesorero
	es único para todas las agrupaciones, pero se ha dejado la 
	opción para si pudiera haber tesoreros de cada agrupación.
-la condición CONTROLERROR != 'ERROR-ANULADA' es para que NO busque las donaciones 
	anuladas por error o duplicidad, y que se mantiene para no eliminar 
	físicamente esas filas y dejar un hueco ya que están ordenadas según CODDONACION															
													
LLAMADA: cTesorero.php:mostrarDonaciones():/controladores/libs/cTesoreroDonacionesApeNomPaginarInc.php
         para pasarla a la función  "/modelos/libs/mPaginarLib.php"
									modeloTesorero.php:buscarTotalesAniosDonaciones(),exportarDonacionesExcel()
									
OBSERVACIONES: Adaptada para PDO BindValues y PHP 7.3.21 												
-----------------------------------------------------------------------------------*/
function cadBuscarDonaciones($codAgrup,$anioDonacion,$tipoDonante)
{
 //echo '<br><br>0-1 modeloTesorero:arrBuscarDonaciones:codAgrup: $codAgrup,anioDonacion: $anioDonacion,tipoDonante: $tipoDonante';
 
	$arrBind = array();
	 
	$condicionControlError = " CONTROLERROR != 'ERROR-ANULADA' ";	
	
 if (isset($anioDonacion) && !empty($anioDonacion) && $anioDonacion !=='%' )
	{ 
   $anioInf = $anioDonacion.'-00-00';
	  $anioSup = ($anioDonacion+1).'-00-00';
			
			//$cadCondicionAnioDonacion =	" AND DONACION.FECHAINGRESO BETWEEN '$anioInf' AND '$anioSup' ";	
			
			//$cadCondicionAnioDonacion =	" AND (DONACION.FECHAINGRESO >= :anioInf AND DONACION.FECHAINGRESO <= :anioSup) ";	
			
			$cadCondicionAnioDonacion =	" AND DONACION.FECHAINGRESO BETWEEN :anioInf AND :anioSup ";	//da error PDO			
			
			$arrBind[':anioInf'] = $anioInf;
			$arrBind[':anioSup'] = $anioSup;
	}
	else
	{ $cadCondicionAnioDonacion =	" ";				
	}
	
	if ( !isset($tipoDonante) || empty($tipoDonante) || $tipoDonante =='%' )
 { $condicionTipoDonante = '';		
 }
	else 
	{ 
   $condicionTipoDonante = " AND DONACION.TIPODONANTE = :tipoDonante ";

   $arrBind[':tipoDonante'] = $tipoDonante;			
 }
	
	if ( !isset($codAgrup) || empty($codAgrup) || $codAgrup =='%' )
 { $condicionAgrup = '';		
 }
	else 
	{ $condicionAgrup = " AND DONACION.CODAGRUPACION = :codAgrup ";
   
			$arrBind[':codAgrup'] = $codAgrup;	
 }
			
 $tablasBusqueda = "DONACION";	
	
	$camposBuscados = " CODDONACION, SUBSTR(FECHAINGRESO, 1,4) as anioDonacion,
																					DATE_FORMAT(FECHAINGRESO, '%Y-%m-%d ') as FECHAINGRESO, 
																					DATE_FORMAT(FECHAANOTACION, '%Y-%m-%d ') as FECHAANOTACION,
																					UPPER(CONCAT(APE1,' ',IFNULL(APE2,''),', ',NOM)) as apeNom, SEXO,
																					TIPODOCUMENTOMIEMBRO, NUMDOCUMENTOMIEMBRO, CODPAISDOC,
																					
																					IMPORTEDONACION,
																					IMPORTEDONACION AS IMPORTEDONACION_PUNTO_DECIMAL,	
																					REPLACE(IMPORTEDONACION,'.',',') AS IMPORTEDONACION_COMA_DECIMAL,	
																					
																					GASTOSDONACION,
																					GASTOSDONACION AS GASTOSDONACION_PUNTO_DECIMAL,	
																					REPLACE(GASTOSDONACION,'.',',') AS GASTOSDONACION_COMA_DECIMAL,
																					
																					MODOINGRESO,CONCEPTO,TIPODONANTE,EMAIL ";																
																					

 $cadCondicionesBuscar = " WHERE ".$condicionControlError.$cadCondicionAnioDonacion.$condicionTipoDonante.$condicionAgrup.																																		
																									        "	ORDER BY DONACION.FECHAINGRESO DESC, apeNom";//MANTENER ESTE ORDEN NECESARIO PARA function buscarTotalesAniosDonaciones()
							
	$cadBuscarDonaciones = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";	
																																														
	//echo '<br><br>1 modeloTesorero:cadBuscarDonaciones: ';print_r($cadBuscarDonaciones);
	
 $arrBuscarDonaciones['cadSQL'] = $cadBuscarDonaciones;

 $arrBuscarDonaciones['arrBindValues']	= $arrBind;
	
	//echo '<br><br>2 modeloTesorero:cadBuscarDonaciones:arrBuscarDonaciones: ';print_r($arrBuscarDonaciones);
	
	return $arrBuscarDonaciones;
}
//------------------------------ Fin cadBuscarDonaciones --------------------------*/

/*---------------------------- Inicio buscarIngresoDonacion ------------------------
Busca en tabla DONACION todos los datos de una donación concreta por 
columna CODDONACION

LLAMADA: modeloTesorero:mostrarIngresoDonacion(),modificarIngresoDonacionTes(),
anularDonacionErroneaTes()

LLAMA: modelos/BBDD/MySQL/modeloMySQL.php:buscarCadSql() 
usuariosConfig/BBDD/MySQL/configMySQL.php,conexionMySql.ph:conexionDB()
modelos/modeloErrores.php:insertarError()

OBSERVACIONES: PHP 7.3.21. En esta función se usan $arrBindValues para PDO.

//------------------------------------------------------------------------------*/
function buscarIngresoDonacion($codDonacion)
{
	//echo "<br><br>0-1 modeloTesorero:buscarIngresoDonacion:codDonacion: ";print_r($codDonacion);

	require_once './modelos/modeloErrores.php';
 require_once "./modelos/BBDD/MySQL/modeloMySQL.php";			 

	$resBuscarDonacion['nomScript'] = " modeloTesorero.php";
	$resBuscarDonacion['nomFuncion'] = "buscarIngresoDonacion";	
	$resBuscarDonacion['codError'] = '00000';
	$resBuscarDonacion['errorMensaje'] = '';
 $resBuscarDonacion['textoComentarios'] = '';	
	$nomScriptFuncionError = 'modeloTesorero.php:buscarIngresoDonacion() Error: ';	
	 	
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	require_once "./modelos/BBDD/MySQL/conexionMySQL.php";			
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);

	if ($conexionDB['codError'] !== '00000')	
	{ $resBuscarDonacion = $conexionDB;
	}
	else	
	{$tablasBusqueda = "DONACION,AGRUPACIONTERRITORIAL ";			
		
	 $camposBuscados =" CODDONACION, SEXO,APE1,APE2,NOM,NUMDOCUMENTOMIEMBRO,TIPODOCUMENTOMIEMBRO,CODPAISDOC,
																					TELFIJOCASA,TELMOVIL,DONACION.EMAIL,
																					IMPORTEDONACION,GASTOSDONACION,MODOINGRESO,TIPODONANTE,
																					DATE_FORMAT(FECHAANOTACION, '%Y-%m-%d ') as FECHAANOTACION,DATE_FORMAT(FECHAINGRESO, '%Y-%m-%d ') as FECHAINGRESO,
																					CONCEPTO,DONACION.OBSERVACIONES,DONACION.CODAGRUPACION,AGRUPACIONTERRITORIAL.NOMAGRUPACION ";
																							
  $cadCondicionesBuscar = " WHERE CODDONACION = :codDonacion ".
		                          " AND DONACION.CODAGRUPACION=AGRUPACIONTERRITORIAL.CODAGRUPACION ";		
    
		$arrBind = array(':codDonacion' => $codDonacion); 	
		
  $cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";
		//echo "<br /><br />1 modeloTesorero:buscarIngresoDonacion:cadSql: ";print_r($cadSql);
		
		$resDonacion = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);//modeloMySQL.php";																																
		
  //echo '<br><br>2 modeloTesorero:buscarIngresoDonacion:resDonacion: ' ;print_r($resDonacion);
				
		if ($resDonacion['codError'] !== '00000')
		{
			$resBuscarDonacion['codError'] = $resDonacion['codError'];
   $resBuscarDonacion['errorMensaje'] = $resDonacion['errorMensaje']; 			
			$resBuscarDonacion['textoComentarios'] = $nomScriptFuncionError.".Error del sistema, al buscar donación por campo CODDONACION. ";	
			insertarError($resBuscarDonacion);			
		}	
		elseif ($resDonacion['numFilas'] !== 1)//es un error lógico
		{			
			$resBuscarDonacion['codError'] = '80001'; //no encontrado	 
		 $resBuscarDonacion['errorMensaje']	= $resBuscarDonacion['nomScript'].":".$resBuscarDonacion['nomFuncion']." No encontrada donación ";
			$resBuscarDonacion['textoComentarios'] .= $nomScriptFuncionError." No encontrada donación por campo CODDONACION";
			insertarError($resBuscarDonacion);			
		}			
		else//$resDonacion['codError']=='00000') && $resDonacion['numFilas'] == 1
		{ 
				$resBuscarDonacion['numFilas'] = $resDonacion['numFilas'];//o = 1
			
				foreach ($resDonacion['resultadoFilas'][0] as $indice => $contenido)                         
				{
						$aux['resultadoFilas']['datosFormDonacion'][$indice]['valorCampo'] = $contenido;
				}		
				$resBuscarDonacion['resultadoFilas'] = $aux['resultadoFilas'];
				
				$fechaIngresoAux = $resBuscarDonacion['resultadoFilas']['datosFormDonacion']['FECHAINGRESO']['valorCampo'];				
							
				$resBuscarDonacion['resultadoFilas']['datosFormDonacion']['FECHAINGRESO']['dia']['valorCampo']  = substr($fechaIngresoAux,8,2);
				$resBuscarDonacion['resultadoFilas']['datosFormDonacion']['FECHAINGRESO']['mes']['valorCampo']  = substr($fechaIngresoAux,5,2);
				$resBuscarDonacion['resultadoFilas']['datosFormDonacion']['FECHAINGRESO']['anio']['valorCampo'] = substr($fechaIngresoAux,0,4);		
			 
 	}//else $resDonacion['codError']=='00000') && $resDonacion['numFilas'] == 1
	}
	//echo '<br><br>3 modeloTesorero:buscarIngresoDonacion:resBuscarDonacion:' ;print_r($resBuscarDonacion);
	
	return $resBuscarDonacion;
}
/*---------------------------- Fin buscarIngresoDonacion -------------------------*/

/*------------------ Inicio actualizarDonacion ------------------------------------
Se actualizan, modifican, los datos de una donación en tabla DONACION

LLAMADA: desde controladorTesorero: modificarIngresoDonacionTes(),
LLAMA:modelos/BBDD/MySQL/modeloMySQL.php:actualizarTabla_ParamPosicion()
(Esta función ejecuta una UPDATE en una tabla mediante PDO y prepare sentencias 
y consultas parametrizadas para dificultar las injections.)
usuariosConfig/BBDD/MySQL/configMySQL.php,conexionMySql.ph:conexionDB()
modelos/modeloErrores.php:insertarError()

OBSERVACIONES: La función para PDO "actualizarTabla_ParamPosicion()" utiliza 
consultas parametrizadascomo alternativa a $arrBindValues 
2020-09-27: probada PHP 7.3.21
------------------------------------------------------------------------------*/
function actualizarDonacion($resulValidar)
{
	//echo "<br><br>0-1 modeloTesorero:actualizarDonacion:resulValidar: "; print_r($resulValidar);
	
	require_once './modelos/modeloErrores.php';
 require_once "./modelos/BBDD/MySQL/modeloMySQL.php";		
	
	$actDonacion['nomScript'] = "modeloTesorero.php";	
	$actDonacion['nomFuncion'] = "actualizarDonacion";
	$actDonacion['codError'] = '00000';
 $actDonacion['errorMensaje'] = '';
 $actDonacion['textoComentarios'] = '';
	$nomScriptFuncionError = ' modeloTesorero.php:actualizarDonacion:(). Error: ';		

 require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	require_once "./modelos/BBDD/MySQL/conexionMySQL.php";
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);		
	
	if ($conexionDB['codError'] !== "00000")
	{ $actDonacion = $conexionDB;
	}
	else //$conexionDB['codError'] == "00000"
	{
		
		if (!isset($resulValidar) || empty($resulValidar))
		{ 
			$actDonacion['codError'] = '70601';
			$actDonacion['errorMensaje'] = $nomScriptFuncionError.": Faltan algunas variables-parámetros (resulValidar) necesarios para SQL -actualizarTabla_ParamPosicion()- ";	 
			$actDonacion['textoComentarios'] = $nomScriptFuncionError;
		}	
  else// !	if (!isset($resulValidar) || empty($resulValidar))
  {			
			$tablaAct ='DONACION';

			$fechaCompacta = $resulValidar['datosFormDonacion']['FECHAINGRESO']['anio']['valorCampo'].'-'.
																				$resulValidar['datosFormDonacion']['FECHAINGRESO']['mes']['valorCampo'].'-'.
																				$resulValidar['datosFormDonacion']['FECHAINGRESO']['dia']['valorCampo'];

			$resulValidar['datosFormDonacion']['FECHAINGRESO']['valorCampo'] = $fechaCompacta;											
																																					
			$resulValidar['datosFormDonacion']['FECHAANOTACION']['valorCampo'] = date('Y-m-d');	
			
			//echo "<br>1 modeloTesorero:actualizarDonacion:resulValidar: "; print_r($resulValidar);

			foreach ($resulValidar['datosFormDonacion'] as $nomCampo=>$valNomCampo)
			{
				$arrValoresAct[$nomCampo] = $valNomCampo['valorCampo'];
			}
				
			//echo "<br>2 modeloTesorero:actualizarDonacion:arrValoresAct: "; print_r($arrValoresAct);
		
			$arrayCondiciones['CODDONACION']['valorCampo'] = $resulValidar['datosFormDonacion']['CODDONACION']['valorCampo'];
			$arrayCondiciones['CODDONACION']['operador'] = '=';
			$arrayCondiciones['CODDONACION']['opUnir'] = ' ';
			
			$resActDonacion = actualizarTabla_ParamPosicion($tablaAct,$arrayCondiciones,$arrValoresAct,$conexionDB['conexionLink']);	
			
			if ($resActDonacion['codError'] !== '00000')
			{ $actDonacion = $resActDonacion;
					$actDonacion['textoComentarios'] = $nomScriptFuncionError." Error del sistema al actualizar donación en: actualizarTabla_ParamPosicion(). ";
			}					
			
		}//else !	if (!isset($resulValidar) || empty($resulValidar))
		
		//echo "<br>3 modeloTesorero:actualizarDonacion:resActDonacion : "; print_r($resActDonacion );
		
		if ($actDonacion['codError'] !== '00000')
		{			
			$resInsertarErrores = insertarError($actDonacion,$conexionDB['conexionLink']);
		}
		else
		{ $actDonacion = $resActDonacion;				//creo que no es necesario	
		}	
		
	}//else $conexionDB['codError'] == "00000"

 //echo "<br>4 modeloTesorero:actualizarDonacion:actDonacion: "; print_r($actDonacion); 
	
 return $actDonacion;
}
/*----------------------------- Fin actualizarDonacion ---------------------------*/

/*------------------ Inicio anularDonacionErronea ---------------------------------
Se actualizan los datos de una donación, para anular algunos campos, con el campo 
CONTROLERROR ='ERROR-ANOTACION' y el campo de obervaciones del tesorero
En lugar de eliminar fisicamente que se perdería la secuencia de CODDONACION,
se pone ese campo CONTROLERROR ='ERROR-ANOTACION' para ue no se muestren

RECIBE: $arrValoresAct, que entre otros campos contiene "CODDONACION"    
DEVUELVE: un array con los controles de errores, y mensajes 

LLAMADA: desde cTesorero.php:anularDonacionErroneaTes(),
LLAMA:  configMySQL.php:conexionDB()y modeloMySQL.php:actualizarTabla()
modeloErrores.php;

OBSERVACIONES: Aquí no se necesitan $arrBindValues, la función actualizarTabla()
para PDO "actualizarTabla()" lo incluye internamentamente $arrBindValues 
2020-09-27: probada PHP 7.3.21
-------------------------------------------------------------------------------*/
function anularDonacionErronea($arrValoresAct)
{
	//echo "<br><br>0-1 modeloTesorero:anularDonacionErronea:arrValoresAct: "; print_r($arrValoresAct);
	//echo "<br><br>0-2 modeloTesorero:anularDonacionErronea:_SESSION: "; print_r($_SESSION);
	
 require_once "./modelos/BBDD/MySQL/modeloMySQL.php";	
	require_once './modelos/modeloErrores.php';
	
	$actDonacion['nomFuncion'] = "anularDonacionErronea";
	$actDonacion['nomScript'] = "modeloTesorero.php";	
 $actDonacion['codError'] = '00000';
 $actDonacion['errorMensaje'] = '';	
 $actDonacion['textoComentarios'] = '';
	$nomScriptFuncionError = ' modeloTesorero.php:anularDonacionErronea:(). Error: ';		 

 require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	require_once "./modelos/BBDD/MySQL/conexionMySQL.php";				
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);
	
	if ($conexionDB['codError'] !== "00000")
	{ $actDonacion = $conexionDB;
	}
	else
	{		
		if (!isset($arrValoresAct) || empty($arrValoresAct))
		{ 
			$actDonacion['codError'] = '70601';
			$actDonacion['errorMensaje'] = $nomScriptFuncionError.": Faltan algunas variables-parámetros (arrValoresAct) necesarios para SQL -actualizarTabla_ParamPosicion()- ";	 
			$actDonacion['textoComentarios'] = $nomScriptFuncionError;
		}	
  else
  {	
			$tablaAct = 'DONACION';					
																																					
			$arrValoresAct['FECHAANOTACION'] = date('Y-m-d');		
			$arrValoresAct['CONTROLERROR'] = 'ERROR-ANULADA';
			$arrValoresAct['OBSERVACIONES'] = 'ANULADA. Importe donación antes de anular: '.$arrValoresAct['IMPORTEDONACION'].' euros '.
																																					'Gasto de la donación antes de anular: '.$arrValoresAct['GASTOSDONACION'].' euros. Fecha ingreso donación:'.$arrValoresAct['FECHAINGRESO'].'. '.
																																					$arrValoresAct['OBSERVACIONES'].' Gestor: '.$_SESSION['vs_CODUSER'];
			$arrValoresAct['IMPORTEDONACION'] = 0.00;		
			$arrValoresAct['GASTOSDONACION'] = NULL;//por defecto siempre es NULL mejor que $arrValoresAct['GASTOSDONACION'] = 0.00; 
			$arrValoresAct['FECHAINGRESO'] = '0000-00-00';

			//echo "<br><br>1 modeloTesorero:anularDonacionErronea:arrValoresAct: "; print_r($arrValoresAct);

			$arrayCondiciones['CODDONACION']['valorCampo'] = $arrValoresAct['CODDONACION'];
			$arrayCondiciones['CODDONACION']['operador'] = '=';
			$arrayCondiciones['CODDONACION']['opUnir'] = ' '; 
			
			$resActDonacion = actualizarTabla_ParamPosicion($tablaAct,$arrayCondiciones,$arrValoresAct,$conexionDB['conexionLink']);		

   if ($resActDonacion['codError'] !== '00000')
			{ $actDonacion = $resActDonacion;
					$actDonacion['textoComentarios'] = $nomScriptFuncionError." Error del sistema al actualizar donación en: actualizarTabla_ParamPosicion(). ";
			}							
			
			//echo "<br><br>2 modeloTesorero:actualizarDonacionErronea:resActDonacion: "; print_r($resActDonacion);
		}
		if ($resActDonacion['codError'] !== '00000')
		{
			$resInsertarErrores = insertarError($actDonacion,$conexionDB['conexionLink']);
		}
		else
		{ $actDonacion = $resActDonacion;					//creo que no es necesario	
		}	
		
	}//else $conexionDB['codError'] == "00000"
	
 //echo "<br><br>3 modeloTesorero:actualizarDonacionErronea:actDonacion: "; print_r($$actDonacion);
	 
 return $actDonacion;
}
/*------------------------ Fin anularDonacionErronea -----------------------------*/

/*---------------------------- Inicio buscarTotalesAniosDonaciones ----------------
Busca los donaciones a partir de una select sobre la tabla "DONACION" y las 
y calcula y desglosa los totales para ser mostrados con formato de tabla en 
vistas/tesorero/vTotalesDonacionesInc.php que es llamada desde la función 
cTesorero:mostrarTotalesDonaciones()

Entre otros campos incluye: 
Nº total donantes, 	Tipo de donante (socios, donantes identificados, anónimos)
Modo de ingreso, Gastos donación, Total donaciones €,  

RECIBE: $codAgrup = "%"; $anioDonacion = "%"; $tipoDonante = "%"
DEVUELVE: array  $reTotalDonacionesAnios, que contiene totales donaciones y 
gastos con punto decimal, y los contadores de totales en enteros

LLAMADA: cTesorero:mostrarTotalesDonaciones()
LLAMA: modeloTesorero.php:cadBuscarDonaciones()
modelos/BBDD/MySQL/modeloMySQL.php:buscarCadSql()
modeloEmail.php:emailErrorWMaster()

OBSERVACIONES: En esta función usa $arrBindValues para PDO, que se prepararon
previamente en cadBuscarDonaciones()
2020-09-30: probada PHP 7.3.21

modifico IMPORTEDONACION_PUNTO_DECIMAL, GATOSDONACION_PUNTO_DECIMAL, para totales,
------------------------------------------------------------------------------*/
function buscarTotalesAniosDonaciones($codAgrup,$anioDonacion,$tipoDonante)
{
 //echo "<br><br>0-1 modeloTesorero:buscarTotalesAniosDonaciones:anioDonacion: ";print_r($anioDonacion);
	
	$resBuscarDonaciones['codError'] = '70601';
	$resBuscarDonaciones['errorMensaje'] = '';
	$resBuscarDonaciones['textoComentarios'] = '';
	$nomScriptFuncionError = ' modeloTesorero.php:buscarTotalesAniosDonaciones:(). Error: ';	
	
 require_once "./modelos/BBDD/MySQL/modeloMySQL.php";	
	require_once './modelos/modeloErrores.php';
	
 require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	require_once "./modelos/BBDD/MySQL/conexionMySQL.php";				
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);

	if ($conexionDB['codError']!=='00000')	
	{ $reTotalDonacionesAnios = $conexionDB;
	}
	else	//$conexionDB['codError'] =='00000'
	{ 
	 if ( !isset($codAgrup) || empty($codAgrup) || !isset($anioDonacion) || empty($anioDonacion) || !isset($tipoDonante) || empty($tipoDonante) )
		{ 
			$reTotalDonacionesAnios['codError'] = '70601';
			$reTotalDonacionesAnios['errorMensaje'] = $nomScriptFuncionError.": Faltan algunas variables-parámetros (codAgrup,anioDonacion,tipoDonante) necesarios para SQL -cadBuscarDonaciones()- ";	 
			$reTotalDonacionesAnios['textoComentarios'] = $nomScriptFuncionError;
		}	
  else //!if (!isset($codAgrup)||empty($codAgrup)||!isset($anioDonacion)||empty($anioDonacion)||!isset($tipoDonante)||empty($tipoDonante) )
  {			  
			$arrSelectBuscarDonaciones = cadBuscarDonaciones($codAgrup,$anioDonacion,$tipoDonante);//en modeloTesorero.php
			
			//echo "<br><br>2-1 modeloTesorero:buscarTotalesAniosDonaciones:arrSelectBuscarDonaciones: ";print_r($arrSelectBuscarDonaciones);	
			
			$cadSql  = $arrSelectBuscarDonaciones['cadSQL'];
			$arrBind = $arrSelectBuscarDonaciones['arrBindValues'];				
	  															
   $resBuscarDonaciones = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);//modeloMySQL.php, probado error Ok	
   
			//echo "<br><br>2-2 modeloTesorero:buscarTotalesAniosDonaciones:resBuscarDonaciones: ";print_r($resBuscarDonaciones);																
			
			if ($resBuscarDonaciones['codError'] !== '00000')
			{$reTotalDonacionesAnios = $resBuscarDonaciones;			 
				$reTotalDonacionesAnios['textoComentarios'] .= "modeloTesorero.php:buscarTotalesAniosDonaciones()";				
			}	
			else //IMPORTEDONACION, MODOINGRESO, TIPODONANTE
			{//echo '<br><br>3 modeloTesorero:buscarTotalesAniosDonaciones:resBuscarDonaciones: ';print_r($resBuscarDonaciones);
				
				$acumuladorDonacionesAnio['numDonantesAnio'] = 0;
    $acumuladorDonacionesAnio['totalAnioDonaciones'] = 0.0;				
				$acumuladorDonacionesAnio['numDonantesSociosAnio'] = 0;
				$acumuladorDonacionesAnio['totalDonacionesSociosAnio'] = 0.0;
				$acumuladorDonacionesAnio['numDonantesIdentificados'] = 0;	
				$acumuladorDonacionesAnio['totalDonacionesIdentificados'] = 0.0;				
				$acumuladorDonacionesAnio['numDonantesAnonimos'] = 0;
				$acumuladorDonacionesAnio['totalDonacionesAnonimos'] = 0.0;
				$acumuladorDonacionesAnio['totalGASTOSDONACION'] = 0.0;
				
    //--- 
				$acumuladorDonacionesAnio['numTRANSFERENCIA'] = 0;
				$acumuladorDonacionesAnio['totalTRANSFERENCIA'] = 0;				
				$acumuladorDonacionesAnio['numPAYPAL'] = 0;
				$acumuladorDonacionesAnio['totalPAYPAL'] = 0;				
				$acumuladorDonacionesAnio['numMETALICO'] = 0;
				$acumuladorDonacionesAnio['totalMETALICO'] = 0;				
				$acumuladorDonacionesAnio['numCHEQUE'] = 0;
				$acumuladorDonacionesAnio['totalCHEQUE'] = 0;				
				$acumuladorDonacionesAnio['numTARJETA'] = 0;
				$acumuladorDonacionesAnio['totalTARJETA'] = 0;				
				$acumuladorDonacionesAnio['numDOMICILIADA'] = 0;
				$acumuladorDonacionesAnio['totalDOMICILIADA'] = 0;				
				$acumuladorDonacionesAnio['numSIN-DATOS'] = 0;
				$acumuladorDonacionesAnio['totalSIN-DATOS'] = 0;				
    //---				
				
				$yearActual = $resBuscarDonaciones['resultadoFilas'][0]['anioDonacion'];
				//$yearFin = $yearActual - 5;				
				
				foreach ($resBuscarDonaciones['resultadoFilas'] as  $fila => $contenidoFila)
				{ //echo "<br><br>fila=";print_r ($fila);echo "contenido=";print_r($contenidoFila);
						
				  //if ($contenidoFila['anioDonacion'] !== $yearActual || $yearActual < $yearFin )
      if ($contenidoFila['anioDonacion'] !== $yearActual )							
						{ 					  
	       $totalDonacionesAnios[$yearActual] = $acumuladorDonacionesAnio;
								
								foreach ($acumuladorDonacionesAnio as $campoAcumulador => $valCampoAcumulador )//poner acumuladores a 0
								{	$acumuladorDonacionesAnio[$campoAcumulador] = 0;
								}								
								$yearActual = $contenidoFila['anioDonacion'];								
						}
				  //$acumuladorDonacionesAnio['totalAnioDonaciones'] += $contenidoFila['IMPORTEDONACION']; //'.',',') AS IMPORTEDONACION,	
						$acumuladorDonacionesAnio['totalAnioDonaciones'] += $contenidoFila['IMPORTEDONACION_PUNTO_DECIMAL'];//Para que calcule decimales en totales correctamente
						$acumuladorDonacionesAnio['numDonantesAnio']++ ;
					 //$acumuladorDonacionesAnio['totalGASTOSDONACION'] += $contenidoFila['GASTOSDONACION'];//Para que anote los gastos de donación cargados a EL por PayPal y otros por otros bancos
						$acumuladorDonacionesAnio['totalGASTOSDONACION'] += $contenidoFila['GASTOSDONACION_PUNTO_DECIMAL'];//Para que anote los gastos de donación cargados a EL por PayPal y otros por otros bancos
	
						if ($contenidoFila['TIPODONANTE'] == 'socio')
						{ //$acumuladorDonacionesAnio['totalDonacionesSociosAnio'] += $contenidoFila['IMPORTEDONACION'];
					   $acumuladorDonacionesAnio['totalDonacionesSociosAnio'] += $contenidoFila['IMPORTEDONACION_PUNTO_DECIMAL'];
								
								$acumuladorDonacionesAnio['numDonantesSociosAnio']++ ;
						}	
						if ($contenidoFila['TIPODONANTE'] == 'IDENTIFICADO-NO-SOCIO')
						{ //$acumuladorDonacionesAnio['totalDonacionesIdentificados'] += $contenidoFila['IMPORTEDONACION'];
					   $acumuladorDonacionesAnio['totalDonacionesIdentificados'] += $contenidoFila['IMPORTEDONACION_PUNTO_DECIMAL'];
								
								$acumuladorDonacionesAnio['numDonantesIdentificados']++ ;
						}	
						if ($contenidoFila['TIPODONANTE'] == 'ANONIMO')
						{ //$acumuladorDonacionesAnio['totalDonacionesAnonimos'] += $contenidoFila['IMPORTEDONACION'];
					   $acumuladorDonacionesAnio['totalDonacionesAnonimos'] += $contenidoFila['IMPORTEDONACION_PUNTO_DECIMAL'];
								$acumuladorDonacionesAnio['numDonantesAnonimos']++ ;
						}	
						//---						
						switch ($contenidoFila['MODOINGRESO'])	
						{ case 'TRANSFERENCIA':	
														$acumuladorDonacionesAnio['numTRANSFERENCIA'] ++; 
														//$acumuladorDonacionesAnio['totalTRANSFERENCIA'] += $contenidoFila['IMPORTEDONACION'];
														$acumuladorDonacionesAnio['totalTRANSFERENCIA'] += $contenidoFila['IMPORTEDONACION_PUNTO_DECIMAL'];
														break;		
								case 'PAYPAL':	
														$acumuladorDonacionesAnio['numPAYPAL'] ++; 
														//$acumuladorDonacionesAnio['totalPAYPAL'] += $contenidoFila['IMPORTEDONACION'];
              $acumuladorDonacionesAnio['totalPAYPAL'] += $contenidoFila['IMPORTEDONACION_PUNTO_DECIMAL'];														
														break;		
								case 'METALICO':	
														$acumuladorDonacionesAnio['numMETALICO'] ++; 
														//$acumuladorDonacionesAnio['totalMETALICO'] += $contenidoFila['IMPORTEDONACION'];	
														$acumuladorDonacionesAnio['totalMETALICO'] += $contenidoFila['IMPORTEDONACION_PUNTO_DECIMAL'];
														break;		
								case 'CHEQUE':	
														$acumuladorDonacionesAnio['numCHEQUE'] ++; 
														//$acumuladorDonacionesAnio['totalCHEQUE'] += $contenidoFila['IMPORTEDONACION'];
              $acumuladorDonacionesAnio['totalCHEQUE'] += $contenidoFila['IMPORTEDONACION_PUNTO_DECIMAL'];														
														break;		
								case 'TARJETA':	
														$acumuladorDonacionesAnio['numTARJETA'] ++; 
														//$acumuladorDonacionesAnio['totalTARJETA'] += $contenidoFila['IMPORTEDONACION'];	
														$acumuladorDonacionesAnio['totalTARJETA'] += $contenidoFila['IMPORTEDONACION_PUNTO_DECIMAL'];
														break;		
								case 'DOMICILIADA':	
														$acumuladorDonacionesAnio['numDOMICILIADA'] ++; 
														//$acumuladorDonacionesAnio['totalDOMICILIADA'] += $contenidoFila['IMPORTEDONACION'];	
														$acumuladorDonacionesAnio['totalDOMICILIADA'] += $contenidoFila['IMPORTEDONACION_PUNTO_DECIMAL'];
														break;		
								case 'SIN-DATOS':	
														$acumuladorDonacionesAnio['numSIN-DATOS'] ++; 
														//$acumuladorDonacionesAnio['totalSIN-DATOS'] += $contenidoFila['IMPORTEDONACION'];	
														$acumuladorDonacionesAnio['totalSIN-DATOS'] += $contenidoFila['IMPORTEDONACION_PUNTO_DECIMAL'];	
														break;
						}
      //--						
				}// foreach 
				$totalDonacionesAnios[$yearActual] =  $acumuladorDonacionesAnio;//Para que no se pierda el último año
				
				$reTotalDonacionesAnios['codError'] = '00000';
				$reTotalDonacionesAnios['resultadoFilas'] = $totalDonacionesAnios;	
			
			}		
			//echo '<br><br>4 modeloTesorero:buscarTotalesAniosDonaciones:reTotalDonacionesAnios: ';print_r($reTotalDonacionesAnios);
			
			if ($reTotalDonacionesAnios['codError'] !== '00000')
			{
				$reTotalDonacionesAnios['textoComentarios'] .= ". modeloTesorero.php:buscarTotalesAniosDonaciones()";				
				insertarError($reTotalDonacionesAnios);
			}	
		}//else !if (!isset($codAgrup)||empty($codAgrup)||!isset($anioDonacion)||empty($anioDonacion)||!isset($tipoDonante)||empty($tipoDonante) )			
	}//else	$conexionDB['codError'] =='00000'
	
	//echo '<br><br>5 modeloTesorero:buscarTotalesAniosDonaciones:reTotalDonacionesAnios: ';print_r($reTotalDonacionesAnios);
	
	return $reTotalDonacionesAnios;
}
/*--------------------------- Fin buscarTotalesAniosDonaciones -------------------*/

/*---------------------------- Inicio exportarDonacionesExcel ----------------------
A partir de una select sobre la tabla "DONACION" se exporta a un fichero Excel,
todas las donaciones de un año o todos, con la información individual de cada donación. 

Permite elegir por año de donación, y por tipos de donates: Todos,Socios,
Simpatizantes (no socios),Anónimos		

Llama a la función exportarExcelDonaciones(), para generar un archivo Excel y el buffer
del PC que despues se podrá descargar.											
		
RECIBE: $codAgrupacion (puede esta vacio, o %),$anioDonacion,
$tipoDonante (Todos, Socio, Simpatizantes, Anonimo )		

LLAMADA: cTesorero:excelDonacionesTesorero()
LLAMA:  modeloTesorero.php:cadBuscarDonaciones()
        modelos/BBDD/MySQL/modeloMySQL.php: buscarCadSql()
        modelos/libs/exportarExcel.php:exportarExcelDonaciones.php()
						  modeloEmail.php:emailErrorWMaster()		

OBSERVACIONES: OJO NO PONER NINGUNA SALIDA A PANTALLA (echo, etc.) daría error:
               para formar el buffer de salida a excel utiliza "header()" y no puede 
															haber ningúna salida delante.

PHP 7.3.21. Esta función usa $arrBindValues para PDO, se preparan previamente en cadBuscarDonaciones()


Nota: función exportarExcelDonaciones($arrDatosDonaciones,$nomFile) para decimales y otros
campos funciona mejor que exportarExcel($arrDatosDonacionesExcel,$nomFile)

Falta controlar el buffer para que vuelva el control, ahora solo se hace en caso
de error en la consulta SQL
-----------------------------------------------------------------------------------*/
function exportarDonacionesExcel($codAgrupacion,$anioDonacion,$tipoDonante)	
{
	//echo "<br><br>0-1 modeloTesorero:exportarDonacionesExcel:anioDonacion:";print_r($anioDonacion);
 //echo "<br><br>0-2 modeloTesorero:exportarDonacionesExcel:tipoDonante:";print_r($tipoDonante); 	
	
	require_once "./modelos/BBDD/MySQL/modeloMySQL.php";	
	require_once './modelos/modeloErrores.php';
	
	$resExportarDonaciones['nomScript'] = "modeloTesorero.php";	
	$resExportarDonaciones['nomFuncion'] = "exportarDonacionesExcel";	
	$resExportarDonaciones['codError'] = '00000';
	$resExportarDonaciones['errorMensaje'] = '';
 $resExportarDonaciones['textoComentarios'] = '';	
	 
//---------------- Inicio ejecución de cadena sql SELECT --------------------------
	
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	require_once "./modelos/BBDD/MySQL/conexionMySQL.php";		
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);
	
	if ($conexionDB['codError']!=='00000')	
	{
  	$resExportarDonaciones = $conexionDB;
	}     
	else //if ($conexionDB['codError']=='00000')
	{
		if ( !isset($codAgrupacion) || empty($codAgrupacion) || !isset($anioDonacion) || empty($anioDonacion) || !isset($tipoDonante) || empty($tipoDonante) )
	 { $resExportarDonaciones['codError'] = '70601';
				$resExportarDonaciones['errorMensaje'] = 'Faltan algunas variables-parámetros ($codAgrupacion, $anioDonacion,$tipoDonante) necesarios para SQL -cadBuscarDonaciones()- ';	
		}
		else //!if ( !isset($codAgrupacion) || empty($codAgrupacion) || !isset($anioDonacion) 
		{
			$arrSelectBuscarDonaciones = cadBuscarDonaciones($codAgrupacion,$anioDonacion,$tipoDonante);//en modeloTesorero.php
							
			//echo "<br><br>2 modeloTesorero:exportarDonacionesExcel:arrSelectBuscarDonaciones: ";print_r($arrSelectBuscarDonaciones);		
			
			$cadSql = $arrSelectBuscarDonaciones['cadSQL'];
			$arrBind = $arrSelectBuscarDonaciones['arrBindValues'];	
		
			$arrDatosDonaciones = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);//Se podría hacer una consulta más restrigida	

			//echo "<br><br>3 modeloTesorero:exportarDonacionesExcel:arrDatosDonaciones : ";print_r($arrDatosDonaciones );

			if ($arrDatosDonaciones['codError'] !== '00000')			
			{$resExportarDonaciones = $arrDatosDonaciones;
				$resExportarDonaciones['textoComentarios'] .= 'modeloTesorero.php:exportarDonacionesExcel().';		
			}
			elseif ($arrDatosDonaciones['numFilas'] <= 0)
			{ $resExportarDonaciones['codError'] = '80001';	
					$resExportarDonaciones['textoComentarios'] =' No se han encontrado datos que cumplan las condiciones de búsqueda elegidas';
					$resExportarDonaciones['errorMensaje'] =' No se han encontrado datos que cumplan las condiciones de búsqueda elegidas'; 				
			}
			else	//arrDatosDonaciones['numFilas']>0)
			{ 			
					//------- Inicio cambio Nombre en campos IMPORTEDONACION, y GASTOSDONACION ---   		
					$aux = array();
					$f = 0;
					$fUltima = $arrDatosDonaciones['numFilas'];
						
					while ($f < $fUltima)//acaso sobre renombrar aquí y sea suficiente hacerlo en exportarExcelDonaciones, o al revés
					{	 
						foreach ($arrDatosDonaciones['resultadoFilas'][$f] as $campo => $valCampo)                         
						{
							//if para eliminar estos campos, son ineccesarios ya se trata "exportarExcelDonaciones()"						
							if ($campo !== 'IMPORTEDONACION_PUNTO_DECIMAL' && $campo !== 'GASTOSDONACION_PUNTO_DECIMAL' &&
											$campo !== 'IMPORTEDONACION_COMA_DECIMAL' && $campo !== 'GASTOSDONACION_COMA_DECIMAL'	)							
							{						
									$aux[$f][$campo] = $valCampo;								
							}							
						}//foreach 					
						
						$f++;
					}		
					$arrDatosDonaciones['resultadoFilas']  = $aux;
				
					//echo "<br><br>4 modeloTesorero:exportarDonacionesExcel:arrDatosDonaciones: ";print_r($arrDatosDonaciones);				
					//------- Fin cambio Nombre en campos IMPORTEDONACION, y GASTOSDONACION ------				 
						
					$nomFile = 'Donaciones_interno';
					
					//require_once './modelos/libs/exportarExcelDonaciones.php'; //movido a /modelos/libs/exportarExcel.php'
					require_once './modelos/libs/exportarExcel.php';
					$resExportarDonaciones = exportarExcelDonaciones($arrDatosDonaciones,$nomFile);//formato salida con decimales (,) bien para Excel	
					
			}//else	arrDatosDonaciones['numFilas']>0)

		}//else //!if ( !isset($codAgrupacion) || empty($codAgrupacion) || !isset($anioDonacion) 
		
		//echo '<br><br>5 modeloTesorero:exportarDonacionesExcel:resExportarDonaciones: ';print_r($resExportarDonaciones); 
	
		if ($resExportarDonaciones['codError'] !== '00000' && $resExportarDonaciones['codError'] !== '80001'	)
		{ 
				$resExportarDonaciones['textoComentarios'] .= 'modeloTesorero.php:exportarDonacionesExcel(): ';		
				insertarError($resExportarDonaciones);				
		}
		//else	arrDatosDonaciones['numFilas']>0) 

	}//if ($conexionDB['codError']=='00000')	

 //echo '<br><br>6 modeloTesorero:exportarDonacionesExcel:resExportarDonaciones: ';print_r($resExportarDonaciones);//no se verá porque el buffer está cautivo

	return $resExportarDonaciones;	
}
/*---------------------------- Fin exportarDonacionesExcel ------------------------*/

/*---------------------------- Inicio buscarDonacionConceptos -----------------------
Busca los datos personales en tabla "DONACIONCONCEPTOS" a partir del campo "CONCEPTO" 
y ordenados ascendente por el campo "FECHACREACIONCONCEPTO"

RECIBE: string "$conceptoDonacion" puede ser "GENERAL", "COSTAS_MEDALLA_VIRGEN_MERITO_POLICIAL",
o cualquier otro similar que se pueda añadir en el futuro.

DEVUELVE: un array con los campos DEVUELVE: un array con los valores de los campos;
"CONCEPTO,NOMBRECONCEPTO,FECHACREACIONCONCEPTO,OBSERVACIONES" y controles de errores, y mensajes 
 
LLAMADA: cTesorero.php:donacionConceptos()

LLAMA: modelos/BBDD/MySQL/modeloMySQL.php:buscarCadSql()
modelos/modeloErrores.php:insertarError()
usuariosConfig/BBDD/MySQL/configMySQL.php";	
modelos/BBDD/MySQL/conexionMySQL.php:conexionDB()

OBSERVACIONES: En esta función utiliza $arrBindValues para PDO, 
2022-01-24: probada PHP 7.3.21
------------------------------------------------------------------------------------*/
function buscarDonacionConceptos($conceptoDonacion)
{
	//echo "<br><br>0-1 modeloTesorero:buscarDonacionConceptos:conceptoDonacion: "; print_r($conceptoDonacion);

	require_once './modelos/modeloErrores.php';
 require_once "./modelos/BBDD/MySQL/modeloMySQL.php";			 
	
	$resDonacionConceptos['nomScript'] = "modeloTesorero.php";
	$resDonacionConceptos['nomFuncion'] = "buscarDonacionConceptos";	
	$resDonacionConceptos['codError'] = '00000';
	$resDonacionConceptos['errorMensaje'] = '';
 $resDonacionConceptos['textoComentarios'] = '';
	$nomScriptFuncionError = ' modeloTesorero.php:buscarDonacionConceptos:(). Error: ';
	
	$arrBind = array();
	
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	require_once "./modelos/BBDD/MySQL/conexionMySQL.php";			
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);

	if ($conexionDB['codError'] !=='00000')	
	{ $resDonacionConceptos = $conexionDB;
	}
	else// $conexionDB['codError'] =='00000'
	{		
		if (!isset($conceptoDonacion) || empty($conceptoDonacion))
		{ 
			$resDonacionConceptos['codError'] = '70601';
			$resDonacionConceptos['errorMensaje'] = $nomScriptFuncionError.": Faltan algunas variables-parámetros (CONCEPTO) necesarios para SQL - buscarDonacionConceptos()- ";	 
			$resDonacionConceptos['textoComentarios'] = $resDonacionConceptos['errorMensaje'];
			insertarError($resDonacionConceptos);	
		}	
		else //if !(!isset($conceptoDonacion) || empty($conceptoDonacion))
	 {			
			if ($conceptoDonacion == '%')
			{ 
					$cadCondicionesBuscar = "";				
			}
			else	
			{ 
					$cadCondicionesBuscar = " WHERE CONCEPTO  = :conceptoDonacion "; 
					
					$arrBind[':conceptoDonacion'] = $conceptoDonacion;			
			}			
			
			$tablasBusqueda = "DONACIONCONCEPTOS";	

			$camposBuscados = " * ";	
			
			$ordenacion = " ORDER BY FECHACREACIONCONCEPTO ASC ";
		
			$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar $ordenacion ";
			
			//echo "<br /><br />1 modeloTesorero:buscarDonacionConceptos:cadSql: ";print_r($cadSql);
			
			$resBuscarDonacionConceptos = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);//en modeloMySQL.php";	probador error
			
			//echo '<br><br>2-1 modeloTesorero:buscarDonacionConceptos:resBuscarDonacionConceptos: ';print_r($resBuscarDonacionConceptos);
																				
			if ($resBuscarDonacionConceptos['codError'] !== '00000')
			{$resDonacionConceptos['errorMensaje'] = $resBuscarDonacionConceptos['errorMensaje']; 
				$resDonacionConceptos['codError'] = $resBuscarDonacionConceptos['codError'];
				$resDonacionConceptos['textoComentarios'] = $resDonacionConceptos['nomScript'].": ".$resDonacionConceptos['nomFuncion'].".Error del sistema, al buscar los datos de -DONACIONCONCEPTOS- .";	
				insertarError($resDonacionConceptos);		
			}	
			else//$resBuscarDonacionConceptos['codError'] =='00000')
			{ 
					$resDonacionConceptos['numFilas'] = $resBuscarDonacionConceptos['numFilas'];   
																							
					if ($resBuscarDonacionConceptos['numFilas'] >= 1)			
					{											
						$fila = 0;
						
						while ( $fila < $resBuscarDonacionConceptos['numFilas'])
						{				
							foreach ($resBuscarDonacionConceptos['resultadoFilas'][$fila] as $indice => $contenido)
							{		
									$resDonacionConceptos['resultadoFilas'][$fila][$indice]['valorCampo'] = $contenido;
							}
							
							$fila++;
				  }
      //echo "<br><br>2-3 modeloTesorero:buscarDonacionConceptos:resDonacionConceptos['resultadoFilas']: ";print_r($resDonacionConceptos['resultadoFilas']);					
					}
					
			}//else $resBuscarDonacionConceptos['codError'] =='00000')

		}//else if !(!isset($conceptoDonacion) || empty($conceptoDonacion))		
	}//else $conexionDB['codError'] =='00000'
	
	//echo '<br><br>3 modeloTesorero:buscarDonacionConceptos:resDonacionConceptos: ';print_r($resDonacionConceptos);
	
	return $resDonacionConceptos;
}
/*---------------------------- Inicio buscarDonacionConceptos ---------------------*/

/*------------------- Inicio insertarDonacionConceptos -----------------------------
Se inserta una fila en en la tabla "DONACIONCONCEPTOS", previamente se comprueba 
que no existe ya para evitar error repetición PK.

RECIBE: $arrDatosDonacionConcepto  array con los campos procedentes del formulario
y ya validados.   
DEVUELVE: un array con los controles de errores, y mensajes 

LLAMADA: desde controladorTesorero.php:aniadirDonacionConceptoTes(),
LLAMA:modeloTesorero.php:buscarDonacionConceptos()
modelos/BBDD/MySQL/modeloMySQL.php:insertarUnaFila()
modelos/modeloErrores.php:insertarError()
usuariosConfig/BBDD/MySQL/configMySQL.php";	
modelos/BBDD/MySQL/conexionMySQL.php:conexionDB()

OBSERVACIONES: PHP 7.3.21. En esta función no se necesitan $arrBindValues para PDO, 
lo incluyen algunas de las funciones que utiliza
-----------------------------------------------------------------------------------*/
function insertarDonacionConceptos($arrDatosDonacionConcepto)
{
	//echo "<br><br>0-1 modeloTesorero:insertarDonacionConceptos:arrDatosDonacionConcepto: "; print_r($arrDatosDonacionConcepto);
	
	require_once './modelos/modeloErrores.php';
 require_once "./modelos/BBDD/MySQL/modeloMySQL.php";			 
	
	$resInsertarDonacionConceptos['nomScript'] = "modeloTesorero.php";	
	$resInsertarDonacionConceptos['nomFuncion'] = "insertarDonacionConceptos";	
 $resInsertarDonacionConceptos['codError'] = '00000';
 $resInsertarDonacionConceptos['errorMensaje'] = '';	 
	$resInsertarDonacionConceptos['textoComentarios'] = '';
	$nomScriptFuncionError = ' modeloTesorero.php:insertarDonacionConceptos:(). Error: ';	

	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	require_once "./modelos/BBDD/MySQL/conexionMySQL.php";			
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);
 	
	if ($conexionDB['codError'] !=='00000')	
	{ $resInsertarDonacionConceptos = $conexionDB;
	}
	else //$conexionDB['codError'] =='00000'
	{
		if (!isset($arrDatosDonacionConcepto) || empty($arrDatosDonacionConcepto))
		{ 
			$resInsertarDonacionConceptos['codError'] = '70601';
			$resInsertarDonacionConceptos['errorMensaje'] = $nomScriptFuncionError.": Faltan algunas variables-parámetros (arrDatosDonacionConcepto) necesarios para SQL -insertarUnaFila()- ";	 
			$resInsertarDonacionConceptos['textoComentarios'] = $nomScriptFuncionError;
		}	
  else //!if (!isset($arrDatosDonacionConcepto) || empty($arrDatosDonacionConcepto))
  {		
   //--- Inicio  mejor en validación evitar error repetición PK -------------------	
			$conceptoDonacion = $arrDatosDonacionConcepto['CONCEPTO']['valorCampo'];  
			
			$resulDonacionConceptos = buscarDonacionConceptos($conceptoDonacion);//en modeloTesorero.php, busca el PK en DONACIONCONCEPTO			
			
			//echo "<br><br>1 modeloTesorero:insertarDonacionConceptos:resulDonacionConceptos: "; print_r($resulDonacionConceptos);
			
			if ($resulDonacionConceptos['codError'] !== '00000')
			{ $resInsertarDonacionConceptos = $resulDonacionConceptos;
					$resInsertarDonacionConceptos['textoComentarios'] = "Error del sistema al insertar donación en: buscarCodMax(). ";	
			}
			elseif ($resulDonacionConceptos['numFilas'] === 1)
			{				
				 $resInsertarDonacionConceptos['codError'] = '80002';
			  $resInsertarDonacionConceptos['errorMensaje'] = $nomScriptFuncionError.": Ya existe ese concepto- ";	 
			  $resInsertarDonacionConceptos['textoComentarios'] = $nomScriptFuncionError;
			}
			//--- Fin  mejor en validación evitar error repetición PK ----------------------	
			
			else // $resulDonacionConceptos['codError']=='00000'
			{								
					$arrValoresInser['CONCEPTO'] = $arrDatosDonacionConcepto['CONCEPTO']['valorCampo'];			
					$arrValoresInser['NOMBRECONCEPTO'] = $arrDatosDonacionConcepto['NOMBRECONCEPTO']['valorCampo'];								
					$arrValoresInser['FECHACREACIONCONCEPTO'] = date('Y-m-d');				
					$arrValoresInser['OBSERVACIONES'] = $arrDatosDonacionConcepto['OBSERVACIONES']['valorCampo'];
									
					//echo "<br><br>2 modeloTesorero:insertarIngresoDonacion:arrValoresInser: "; print_r($arrValoresInser);
				
					$resInsertarFilaDonacionConceptos = insertarUnaFila('DONACIONCONCEPTOS',$arrValoresInser,$conexionDB['conexionLink']);	//en modeloMySQL.php probado error		

					//echo "<br><br>3 modeloTesorero:insertarDonacionConceptos:resInsertarFilaDonacionConceptos: "; print_r($resInsertarFilaDonacionConceptos); 

					if ($resInsertarFilaDonacionConceptos['codError'] !== '00000')
					{ $resInsertarDonacionConceptos = $resInsertarFilaDonacionConceptos;
							$resInsertarDonacionConceptos['textoComentarios'] = "Error del sistema al insertar donación en: insertarUnaFila(). ";
					}		
					
				}//else $resulDonacionConceptos['codError'] == '00000'		
		}//else !if (!isset($arrDatosDonacionConcepto) || empty($arrDatosDonacionConcepto))
	 
		//echo "<br>4 modeloTesorero:insertarDonacionConceptos:resInsertarDonacionConceptos: "; print_r($resInsertarDonacionConceptos); 
		
		if ($resInsertarDonacionConceptos['codError'] !== '00000')
		{ 
				$resInsertarDonacionConceptos['textoComentarios'] .= $nomScriptFuncionError;					 
				insertarError($resInsertarDonacionConceptos);	
		}	
  else
		{ $resInsertarDonacionConceptos = $resInsertarFilaDonacionConceptos;				//creo que no es necesario	
		}			
	}//else $conexionDB['codError'] == '00000'		
	
 //echo "<br><br>5 modeloTesorero:insertarDonacionConceptos:resInsertarDonacionConceptos: "; print_r($resInsertarDonacionConceptos); 
	
 return $resInsertarDonacionConceptos;
}
/*----------------------------- Fin insertarDonacionConceptos ---------------------*/

/*=============== FIN FUNCIONES RELACIONADAS CON DONACIONES =====================================================
================================================================================================================*/


/*==== INICIO: FUNCIONES RELACIONADAS CON ÓRDENES COBRO CUOTAS BANCOS ===========================================
       - Funciones "Estado de las órdenes cobro domiciliadas en bancos":descargarAchivoSEPAXML()...
       - GENERAR ARCHIVO B.SANTANDER "SEPA-XML", "ARCHIVOS CUOTAS EXCEL BANCOS, ARCHIVOS CUOTAS EXCEL USO INTERNO
							- Envío email próximo cobro cuota domiciliada, Envío email cuota no domiciliada aún NO pagada,
							- Exportar lista Emails socios cuota domciliada pendientes pagar cuota
							- Exportar lista Emails socios cuota no domciliada, pendientes pagar cuota				
							
							NOTA: podría hacer un fichero independiente con nombre similar a: cTesoreroCuotasBancos.php
================================================================================================================*/

/*== INCIO: Funciones usadas en "Estado de las órdenes cobro domiciliadas en bancos" =====================
     - mEliminarOrdenesCobroUnaRemesa(): Eliminar remesa de tabla "ORDENES_COBRO" (error o anulación) 
     - descargarAchivoSEPAXML(): Descargar Archivo SEPA de remesa 
     - mActualizarCuotasCobradasEnRemesaTes():Actualizar pagos remesa en tablas CUOTAANIOSOCIO,ORDENES_COBRO,REMESAS_SEPAXML
========================================================================================================*/

/*------------------------------ Inicio buscarDatosRemesasOrdenesCobro -----------------------------
Devuelve un array asociativo con los datos de las REMESAS_SEPAXML y ORDENES_COBRO asociadas, 
para mostrar el listado de remesas

LLAMADA: cTesorero.php:estadoOrdenesCobroRemesasTes()
LLAMA: usuariosConfig/BBDD/MySQL/configMySQL.php:conexionDB(),modeloMySQL.php:buscarCadSql()

OBSERVACIONES: 
2020-12-14: probado PHP 7.3.21
--------------------------------------------------------------------------------------------------*/
function buscarDatosRemesasOrdenesCobro()		
{//echo "<br /><br />1 modeloTesorero:buscarDatosRemesasOrdenesCobro:codUser: ";print_r($codUser);
 //echo "<br /><br />2 modeloTesorero:buscarDatosRemesasOrdenesCobro:anioCuota: ";print_r($anioCuota);
	
	require_once "./modelos/BBDD/MySQL/modeloMySQL.php";		
	require_once './modelos/modeloErrores.php'; 
				
	$remesasOrdenesCobro['nomScript'] = "modeloTesorero.php";	
	$remesasOrdenesCobro['nomFuncion'] = "buscarDatosRemesasOrdenesCobro";	
	$remesasOrdenesCobro['codError'] = '00000';
	$remesasOrdenesCobro['errorMensaje'] = '';
	
	$arrMensaje = array();
	
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	require_once "BBDD/MySQL/conexionMySQL.php";			
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);
	
	if ($conexionDB['codError'] !== '00000')	
	{ $remesasOrdenesCobro['codError'] = $conexionDB['codError'];
			$remesasOrdenesCobro['errorMensaje'] = $conexionDB['errorMensaje'];
			$arrMensaje['textoComentarios'] .= "Error del sistema al conectarse a la base de datos"; 	
	}
	else	//$conexionDB['codError']=='00000'
	{  
  $cadSql = "SELECT * FROM REMESAS_SEPAXML ORDER BY NOMARCHIVOSEPAXML DESC";	
	 
	 $arrRemesa = buscarCadSql($cadSql,$conexionDB['conexionLink']);//en modelos/BBDD/MySQL/modeloMySQL.php		
 	
  //echo "<br /><br />3-1 modeloTesorero:buscarDatosRemesasOrdenesCobro:arrRemesa: ";print_r($arrRemesa);

  if ($arrRemesa['codError'] !== '00000') // ya incluye si devuelve = 0 filas es error 
  { $remesasOrdenesCobro = $arrRemesa;	  
	
				$textoErrores = $arrRemesa['errorMensaje']. " al buscar datos en la tabla REMESAS_SEPAXML. CODERROR: ".$arrRemesa['codError'];

    //echo "<br /><br />3-2 modeloTesorero:buscarDatosRemesasOrdenesCobro:arrRemesa: ";print_r($arrRemesa);echo "<br />";				
		}
		elseif($arrRemesa['numFilas'] <= 0)
		{//echo "<br><br>3-3 modeloTesorero:buscarDatosRemesasOrdenesCobro:arrRemesa: ";print_r($arrRemesa);	
			
			$remesasOrdenesCobro['codError'] = '80001';
			$remesasOrdenesCobro['errorMensaje'] = ". No se han encontrado datos al buscar en la tabla REMESAS_SEPAXML";	
			$textoErrores = $remesasOrdenesCobro['errorMensaje'].". CODERROR: ".$remesasOrdenesCobro['codError'];
		}  		
		else // ($arrRemesa['codError']=='00000')// y $arrRemesa['numFilas'] >= 0
		{ 		
				$cadSql = "SELECT NOMARCHIVOSEPAXML, SUM(IMPORTEGASTOSDEVOLUCION) AS IMPORTEGASTOSDEVOLUCION, COUNT(ORDENES_COBRO.ESTADOCUOTA)  AS TOTALORDENES, 
				           COUNT(IF(ORDENES_COBRO.ESTADOCUOTA = 'NOABONADA-DEVUELTA', 1, NULL))  AS NUMRECIBOSDEVUELTOS
				           FROM ORDENES_COBRO 
															GROUP BY NOMARCHIVOSEPAXML ORDER BY NOMARCHIVOSEPAXML DESC"; 

				$arrOrdenesCobro = buscarCadSql($cadSql,$conexionDB['conexionLink']); 
    //echo "<br><br>4-1 modeloTesorero:buscarDatosRemesasOrdenesCobro:arrOrdenesCobro: ";print_r($arrOrdenesCobro);	
				
				if ($arrOrdenesCobro['codError'] !== '00000')
				{					
					$remesasOrdenesCobro = $arrOrdenesCobro;			
					//echo "<br><br>4-2 modeloTesorero:buscarDatosRemesasOrdenesCobro:arrOrdenesCobro: ";print_r($arrOrdenesCobro);	
					
					$textoErrores = $arrOrdenesCobro['errorMensaje']. " al buscar datos en la tabla ORDENES_COBRO. CODERROR: ".$arrOrdenesCobro['codError'];

				}  
    elseif($arrOrdenesCobro['numFilas'] <= 0)
    {//echo "<br><br>4-3 modeloTesorero:buscarDatosRemesasOrdenesCobro:arrOrdenesCobro: ";print_r($arrOrdenesCobro);	
					
					$remesasOrdenesCobro['codError'] = '80001';
					$remesasOrdenesCobro['errorMensaje'] = ". No se han encontrado datos al buscar en la tabla ORDENES_COBRO";	
					$textoErrores = $remesasOrdenesCobro['errorMensaje'].". CODERROR: ".$remesasOrdenesCobro['codError'];
				}    				
				else // $arrOrdenesCobro['codError']!=='00000' y $arrOrdenesCobro['numFilas'] >= 0
				{ //echo "<br><br>4-4 modeloTesorero:buscarDatosRemesasOrdenesCobro:arrOrdenesCobro: ";print_r($arrOrdenesCobro);	
				  
						if ($arrOrdenesCobro['numFilas'] !== $arrRemesa['numFilas']) //Sería un error ya que las órdenes de cobros están agrupadas por NOMARCHIVOSEPAXML. Error probado OK
						{ //echo "<br><br>4-5 modeloTesorero:buscarDatosRemesasOrdenesCobro. Error distinto número de filas. ";								  
								$remesasOrdenesCobro['codError'] = '70700';//acaso mejor  Inconsistencia resultados de tablas que seía un error del sistema
						  $remesasOrdenesCobro['errorMensaje'] = ". Inconsistencia en numFilas al buscar en las tablas REMESAS_SEPAXML y ORDENES_COBRO";	
								$textoErrores = $remesasOrdenesCobro['errorMensaje'].". CODERROR: ".$remesasOrdenesCobro['codError'];
						}
      else
      {							
							 $f = 0;	$numFilas = $arrOrdenesCobro['numFilas'];
							
							 $arrRemesaAux = 	$arrRemesa['resultadoFilas'];	
							
							 while ($f < $numFilas) //añadimos los campos procedentes de sumatosrios y acumadores de ORDENES_COBRO
							 {		
									$arrRemesaAux[$f]['IMPORTEGASTOSDEVOLUCION'] = $arrOrdenesCobro['resultadoFilas'][$f]['IMPORTEGASTOSDEVOLUCION'];  
									$arrRemesaAux[$f]['NUMRECIBOSDEVUELTOS'] = $arrOrdenesCobro['resultadoFilas'][$f]['NUMRECIBOSDEVUELTOS']; 										
								
									$f++;
							 }
								$remesasOrdenesCobro['resultadoFilas'] = $arrRemesaAux;
						}				 
				}// $arrOrdenesCobro['numFilas'] >=0; 		
  }//else ($arrRemesa['codError']=='00000')


  //echo "<br /><br />5 modeloTesorero:buscarDatosRemesasOrdenesCobro:remesasOrdenesCobro: ";print_r($remesasOrdenesCobro);

		//---------------- Inicio tratamiento de errores --------------------------------------		
		if ($remesasOrdenesCobro['codError'] !== '00000')
		{//echo "<br><br>6-1 modeloTesorero:buscarDatosRemesasOrdenesCobro:remesasOrdenesCobro: ";print_r($remesasOrdenesCobro);		
			//echo "<br><br>6-2 modeloTesorero:buscarDatosRemesasOrdenesCobro:textoErrores: ";print_r($textoErrores);echo "<br><br>";		

			$arrMensaje['textoComentarios'] = " ERROR: No se han podido encontrar las órdenes de cobro de cuotas domiciliadas en las tablas REMESAS_SEPAXML y ORDENES_COBRO. 
																																							. Vuelva a intentarlo pasado un tiempo y en caso de siga el error,	avise al administrador de esta aplicación, e indique el mensaje de error.<br /><br/> ".
																																							$textoErrores.". Gestor CODUSER: ".$_SESSION['vs_CODUSER']." Función: ".	$remesasOrdenesCobro['nomScript'].": ".$remesasOrdenesCobro['nomFuncion'];
			$arrInsertarErrores = $remesasOrdenesCobro;		
			$arrInsertarErrores['textoComentarios'] =  "Error: modeloTesorero:buscarDatosRemesasOrdenesCobro(): ".$textoErrores." Función: ".	
			$remesasOrdenesCobro['nomScript'].": ".$remesasOrdenesCobro['nomFuncion'].". Gestor CODUSER: ".$_SESSION['vs_CODUSER'];;	
																																					
			$remesasOrdenesCobro['arrMensaje']['textoComentarios'] = $arrMensaje['textoComentarios'];		

			$resInsertarErrores = insertarError($arrInsertarErrores);	
			
			if ($resInsertarErrores['codError'] !== '00000')
			{ $remesasOrdenesCobro['errorMensaje'] .= $resInsertarErrores['errorMensaje'];
					$remesasOrdenesCobro['arrMensaje']['textoComentarios'].="Error del sistema al tratar ERRORES, vuelva a intentarlo pasado un tiempo ";
			}		
		}//if ($remesasOrdenesCobro['codError']!=='00000')	
	}//else	$conexionDB['codError']=='00000'			
	//----------------------- Fin tratamiento de errores --------------------------------------		
	
	//echo "<br /><br />7 modeloTesorero:buscarDatosRemesasOrdenesCobro:remesasOrdenesCobro: ";print_r($remesasOrdenesCobro);
 return $remesasOrdenesCobro;
}
/*------------------------------ Fin buscarDatosRemesasOrdenesCobro -------------------------------*/

/*---------------------- Inicio cadBuscarOrdenesCobroRemesaApeNom ----------------------------------
Forma la cadena select sql para busca los datos de órdenes de cobro de una remesa concreta dado 
por "$nomAchivoRemesaSEPAXML" y con los parámetros formulario recibidos: Ape1 y Ape2, 
$codAreaCoordinacion,$codAgrupacion,$estadoOrdenCobro

RECIBE: Ape1 y Ape2, $codAreaCoordinacion,$codAgrupacion,$estadoOrdenCobro, además de 
"$nomAchivoRemesaSEPAXML".
DEVUELVE: array "$arrBuscarCuotasSocios" cadena select ['cadSQL'] y ['arrBindValues']	
													
LLAMADA: controladores/libs/cTesoreroOrdenesCobroUnaRemesaPaginar.phpcTesoreroOrdenesCobroUnaRemesaPaginar()//es una función 	
que a su vez es llamada desde cTesorero.php:mostrarOrdenesCobroUnaRemesaTes()

LLAMA: eliminarAcentos.php:cambiarAcentosEspeciales()					
									
OBSERVACIONES: 
2021-06-09: Probada PDO y PHP 7.3.21
--------------------------------------------------------------------------------------------------*/
function cadBuscarOrdenesCobroRemesaApeNom($codAreaCoordinacion='%',$codAgrupacion ='%',$estadoOrdenCobro = '%',$cadApe1,$cadApe2,$nomAchivoRemesaSEPAXML)
{
	//echo "<br><br>0-1 modeloTesorero.php:cadBuscarOrdenesCobroRemesaApeNom:codAreaCoordinacion: "; print_r($codAreaCoordinacion);
	//echo "<br><br>0-2 modeloTesorero.php:cadBuscarOrdenesCobroRemesaApeNom:codAgrupacion: "; print_r($codAgrupacion);	
	//echo "<br><br>0-3 modeloTesorero.php:cadBuscarOrdenesCobroRemesaApeNom:estadoOrdenCobro: "; print_r($estadoOrdenCobro);	
	//echo "<br><br>0-4 modeloTesorero.php:cadBuscarOrdenesCobroRemesaApeNom:cadApe1 "; print_r($cadApe1);
	//echo "<br><br>0-5 modeloTesorero.php:cadBuscarOrdenesCobroRemesaApeNom:cadApe2 "; print_r($cadApe2);	
	//echo "<br><br>0-6 modeloTesorero.php:cadBuscarOrdenesCobroRemesaApeNom:nomAchivoRemesaSEPAXML "; print_r($nomAchivoRemesaSEPAXML);						
		
	require_once './modelos/libs/eliminarAcentos.php';		
	
	$arrOrdenesCobroRemesa['codError'] = '00000';
	$arrOrdenesCobroRemesa['errorMensaje'] = '';
	
	$arrBind = array();
	
	//-- Incicio condiciones $condicionAreaCoordinacion,$condicionAgrup,$condicionEstadoOrdenCobro,$condicionApe1,$condicionApe2,$condicionNomAchivoRemesa --
	if(!isset($codAreaCoordinacion)||empty($codAreaCoordinacion)||$codAreaCoordinacion =='%')
 { $condicionAreaCoordinacion = "";		
 }
	else 
	{ $condicionAreaCoordinacion =" AND AREAGESTIONAGRUPACIONESCOORD.CODAREAGESTIONAGRUP = :codAreaCoordinacion ";			
			$arrBind[':codAreaCoordinacion'] = $codAreaCoordinacion; 
 }
	
 if (!isset($codAgrupacion) || empty($codAgrupacion) || $codAgrupacion =='%')
 { $condicionAgrup = '';	}
	else
	{ $condicionAgrup  = " AND ORDENES_COBRO.CODAGRUPACION = :codAgrupacion ";//acaso no sea necesario el like aquí   
			$arrBind[':codAgrupacion'] = $codAgrupacion;
 }
		

	if ( !isset($estadoOrdenCobro) || empty($estadoOrdenCobro) || $estadoOrdenCobro =='%' )	
 { 
   $condicionEstadoOrdenCobro = '';		
 }
	else 
	{	$condicionEstadoOrdenCobro = " AND ORDENES_COBRO.ESTADOCUOTA = :estadoOrdenCobro ";
			$arrBind[':estadoOrdenCobro'] = $estadoOrdenCobro;
 }	
		
		//--- Inicio condiciones $cadApe1,$cadApe2,$codAreaCoordinacion,$codAgrupacion,$anioCuotas ---		
		/*---------- Inicio APE1 y AP2 -------------------------------------------
  NOTA: Previamente desde las funciones de llamada controla el caso de que la búsqueda sea por 
		APE1 Y APE2, y no permite llegar a esta función con los dos valores empty a la vez: empty($cadApe1) y empty($cadApe2) 
		En el caso de búsqueda sea por ESTADOCUOTA o por CODAGRUPACION, el valor recibido será NULL
		y lo trata como $cadApe1 =="%" y $cadApe2 =="%" para que la select incluya a todos.
		-------------------------------------------------------------------------*/
		if ( !isset($cadApe1) || empty($cadApe1) || $cadApe1 == '%' )//OJO:!isset($cadApe1)||empty($cadApe1) considera="%", pues se puede buscar solo por un APE2 
		{$condicionApe1 = '';			
			//echo "<br><br>2-1 modeloTesorero.php:cadBuscarCuotasSociosApeNom:cadApe1: ";print_r($cadApe1);
		}
		else
		{$cadApe1 = cambiarAcentosEspeciales($cadApe1);	  
			
			//si el socio es baja, APE1 está disponible 5 años en la tabla MIEMBROELIMINADO5ANIOS para efectos de tesorería 
		
			$condicionApe1 = " AND (MIEMBRO.APE1 LIKE :cadApe1Miembro OR  MIEMBROELIMINADO5ANIOS.APE1 LIKE :cadApe1ELIMINADO5ANIOS) ";//necesario el like aquí		
			$arrBind[':cadApe1Miembro'] = $cadApe1; 
			$arrBind[':cadApe1ELIMINADO5ANIOS'] = $cadApe1;			
			//echo "<br><br>2-2 modeloTesorero.php:cadBuscarCuotasSociosApeNom:";
		}
		if ( !isset($cadApe2) || empty($cadApe2) || $cadApe2 == '%' )//OJO:!isset($cadApe2)||empty($cadApe2) considera="%", pues se puede buscar solo por un APE1 
		{ 
		  $condicionApe2 = '';			
				//echo "<br><br>3-1 modeloTesorero.php:cadBuscarCuotasSociosApeNom:cadApe2: ";print_r($cadApe2);
		}
		else
		{$cadApe2 = cambiarAcentosEspeciales($cadApe2);   
			
			$condicionApe2 = " AND (MIEMBRO.APE2 LIKE :cadApe2Miembro OR  MIEMBROELIMINADO5ANIOS.APE2 LIKE :cadApe2ELIMINADO5ANIOS) ";//necesario el like aquí			
			$arrBind[':cadApe2Miembro'] = $cadApe2; 
			$arrBind[':cadApe2ELIMINADO5ANIOS'] = $cadApe2;
			//echo "<br><br>3-2 modeloTesorero.php:cadBuscarCuotasSociosApeNom:";
		} //---------- Fin APE1 y AP2 ------------------------------------------------------

	
		if( !isset($nomAchivoRemesaSEPAXML) || empty($nomAchivoRemesaSEPAXML) )
		{ $arrOrdenesCobroRemesa['codError'] = '70601';//poner valor adecuado
				$arrOrdenesCobroRemesa['errorMensaje'] = 'Error: Falta nombre archivo Remesa SEPA XML';
		}
		else 
		{ $condicionNomAchivoRemesa =" AND ORDENES_COBRO.NOMARCHIVOSEPAXML = :nomAchivoRemesaSEPAXML ";		
	
				$arrBind[':nomAchivoRemesaSEPAXML'] = $nomAchivoRemesaSEPAXML; //$arrBind[':nomAchivoRemesaSEPAXML']="SEPA_ISO20022CORE_2020-11-17H09-57-47.xml";
		}

	 //--- Fin condiciones $condicionAreaCoordinacion,$condicionAgrup,$condicionEstadoOrdenCobro,$condicionApe1,$condicionApe2,$condicionNomAchivoRemesa ---.
		
	 $cadCondicionesBuscar = " WHERE ORDENES_COBRO.CODSOCIO = SOCIO.CODSOCIO 
																										 	AND SOCIO.CODUSER = MIEMBRO.CODUSER".
																											
																											$condicionAreaCoordinacion.$condicionAgrup.$condicionEstadoOrdenCobro.$condicionApe1.$condicionApe2.$condicionNomAchivoRemesa.																																							
                           
																											" ORDER BY ANIOCUOTA DESC, apeNom";	
																											//" ORDER BY MIEMBRO.APE1 ASC, MIEMBRO.APE2 ASC, MIEMBRO.NOM ASC ";
																											
		$tablasBusqueda = "ORDENES_COBRO, SOCIO, MIEMBRO	
	                    LEFT JOIN MIEMBROELIMINADO5ANIOS ON MIEMBRO.CODUSER = MIEMBROELIMINADO5ANIOS.CODUSER ";
		
	 $camposBuscados =	" MIEMBRO.EMAIL,MIEMBRO.APE1, MIEMBRO.APE2,MIEMBRO.NOM, 
		                    UPPER(CONCAT(IFNULL(MIEMBRO.APE1,MIEMBROELIMINADO5ANIOS.APE1),' ',IFNULL(MIEMBRO.APE2,IFNULL(MIEMBROELIMINADO5ANIOS.APE2,'')),', ',IFNULL(MIEMBRO.NOM,MIEMBROELIMINADO5ANIOS.NOM))) as apeNom, 
																						SOCIO.CODUSER, ORDENES_COBRO.* ";
		
		$cadSelectOrdenesCobroRemesa = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";	
																																															
		$arrOrdenesCobroRemesa['cadSQL'] = $cadSelectOrdenesCobroRemesa;

		$arrOrdenesCobroRemesa['arrBindValues']	= $arrBind;
																																											
		//echo "<br><br>4 modeloTesorero.php:cadBuscarOrdenesCobroRemesaApeNom:arrOrdenesCobroRemesa: "; print_r($arrOrdenesCobroRemesa);	
	
	return $arrOrdenesCobroRemesa;
}
/*---------------------- FIN cadBuscarOrdenesCobroRemesaApeNom -----------------------------------*/


/*------------------- Inicio mEliminarOrdenesCobroUnaRemesa ----------------------------------------
Se eliminan las órdenes de cobro generadas para una remesa SEPA XML en las tablas 
"REMESAS_SEPAXML" y "ORDENES_COBRO" y que tienen el campo ANOTADO_EN_CUOTAANIOSOCIO ='NO'
y el archivo correspondiente que se guardó en el servidor en el directorio:
$dirSEPAXML = '/../upload/TESORERIA/SEPAXML_ISO20022', para después subirlo al banco	

Para buscar las órdenes de cobro de esa remesa en las tablas "ORDENES_COBRO" y 
"REMESAS_SEPAXML" y se utiliza el campo "NOMARCHIVOSEPAXML" que es el nombre 
archivo de orden de cobro de esa remesa SEPA XML generado y subido a la web del 
banco para su cobro, de nombre similar "SEPA_ISO20022CORE_2021_03_03H08_43_36.xml"																										
						
								
RECIBE: "$nombreArchivoRemesa": el nombre del el archivo ordenes cobro SEPA-XML
"$dirSEPAXML" el directorio donde está el archivo ordenes cobro: 
"/../upload/TESORERIA/SEPAXML_ISO20022" 
ambos procedentes de cTesorero.php:eliminarOrdenesCobroUnaRemesaTes()

DEVUELVE: un array con información de las filas borradas en tabla y borrado de archivo
y los controles de errores, y mensajes 

LLAMADA: cTesorero.php:eliminarOrdenesCobroUnaRemesaTes()
LLAMA: modeloMySQL.php:borrarFilas(), modeloArchivos.php:eliminarArchivo()
usuariosConfig/BBDD/MySQL/configMySQL.php:conexionDB(),
modelos/modeloErrores.php:insertarError()

OBSERVACIONES: probado con PHP 7.3.21 
Para eliminar la remesa se utiliza el campo "NOMARCHIVOSEPAXML" de nombre similar 
"SEPA_ISO20022CORE_2021_03_03H08_43_36.xml" que incluye fecha hasta segundos	para 
evitar duplicidades.															

El control de transaciones incluso al eliminar el archivo volvería la tabla a valores previos
------------------------------------------------------------------------------------------------*/
function  mEliminarOrdenesCobroUnaRemesa($dirSEPAXML,$nombreArchivoRemesa)
{
	//echo "<br><br>0-1-modeloTesorero:mEliminarOrdenesCobroUnaRemesa:nombreArchivoRemesa: "; print_r($nombreArchivoRemesa);
 //echo "<br><br>0-2-modeloTesorero:mEliminarOrdenesCobroUnaRemesa:dirSEPAXML: "; print_r($dirSEPAXML);
	
 $dirSEPAXML = '/../upload/TESORERIA/SEPAXML_ISO20022';//colgará de raiz: $_SERVER['DOCUMENT_ROOT'] Podría recibirse como parámetro

 $resEliminarOrdenesCobro['nomScript'] = 'modeloTesorero.php';	
 $resEliminarOrdenesCobro['nomFuncion'] = 'mEliminarOrdenesCobroUnaRemesa';
	$resEliminarOrdenesCobro['codError'] = '00000';
 $resEliminarOrdenesCobro['errorMensaje'] = '';
	$textoErrores = '';
	
	//$dirSEPAXML = '/../upload/TESORERIA/SEPAXML_ISO20022';//colgará de raiz:$_SERVER['DOCUMENT_ROOT']
	
 require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	require_once "BBDD/MySQL/conexionMySQL.php";			
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);	
	
	if ($conexionDB['codError'] !== "00000")
	{ $resEliminarOrdenesCobro  = $conexionDB;
   $textoErrores = $conexionDB['errorMensaje']. " al conectarse a la base de datos. CODERROR: ".$conexionDB['codError'];					   
	}
	else //$conexionDB['codError']=="00000"
	{		
		require_once("BBDD/MySQL/transationPDO.php");
		$resIniTrans = beginTransationPDO($conexionDB['conexionLink']);
		
		//echo "<br><br>1-1 modeloTesorero:mEliminarOrdenesCobroUnaRemesa:resIniTrans: ";var_dump($resIniTrans);	echo "<br>";
		
		if ($resIniTrans['codError'] !== '00000') //será ['codError'] = '70501';
		{	$resIniTrans['errorMensaje'] = 'Error en el sistema, no se ha podido iniciar la transación. ';
				$resIniTrans['numFilas'] = 0;							
				$resEliminarOrdenesCobro = $resIniTrans;	
				//echo "<br><br>2-1 modeloTesorero:mEliminarOrdenesCobroUnaRemesa:resEliminarOrdenesCobro: ";print_r($resEliminarOrdenesCobro);
		}			
		else //$resIniTrans
		{ 			
   if ( (!isset($nombreArchivoRemesa) || empty($nombreArchivoRemesa)) || (!isset($dirSEPAXML) || empty($dirSEPAXML)) )										
			{ $resEliminarOrdenesCobro['codError'] = '50000';		
					$resEliminarOrdenesCobro['errorMensaje'] = 'Error en el sistema, falta paramétro de nombre Archivo Remesa o del directorio de almacenamiento del archivo. ';	
     $textoErrores = $resEliminarOrdenesCobro['errorMensaje'].$resEliminarOrdenesCobro['codError'];					
					//echo "<br><br>3-1 modeloTesorero:mEliminarOrdenesCobroUnaRemesa:resEliminarOrdenesCobro: "; print_r($resEliminarOrdenesCobro);			
			}
			else //if ( (isset($nombreArchivoRemesa) && !empty($nombreArchivoRemesa))
			{	
				//-- Inicio Eliminar todas las órdenes de cobro de una remesa concreta en la tabla ORDENES_COBRO ----/$cadenaCondiciones = "FECHA_CREACION_ARCHIVO_SEPA = '$fechaCreacionArchivoRemesa' AND ANOTADO_EN_CUOTAANIOSOCIO ='NO' ";
				
    $cadenaCondiciones = "NOMARCHIVOSEPAXML = '$nombreArchivoRemesa' AND ANOTADO_EN_CUOTAANIOSOCIO = 'NO'";
							
				$reEliminarOrdenCobro = borrarFilas('ORDENES_COBRO',$cadenaCondiciones,$conexionDB['conexionLink']);					

				//echo "<br><br>3-2 modeloTesorero:mEliminarOrdenesCobroUnaRemesa:reEliminarOrdenCobro: ";print_r($reEliminarOrdenCobro);				
							
				//-- Fin Eliminar las todas las órdenes de cobro de una remesa concreta en la tabla ORDENES_COBRO ----
				
				if ($reEliminarOrdenCobro['codError'] !== '00000' || $reEliminarOrdenCobro['numFilas'] === 0)	//otra opción es probar si es cero filas		
				{ $resEliminarOrdenesCobro = $reEliminarOrdenCobro;
						$textoErrores = $reEliminarOrdenCobro['errorMensaje']. " al eliminar filas de la tabla ORDENES_COBRO. CODERROR: ".$reEliminarOrdenCobro['codError'];							
						
						//echo "<br><br>3-3 modeloTesorero:mEliminarOrdenesCobroUnaRemesa:resEliminarOrdenesCobro:";print_r($resEliminarOrdenesCobro);	
				}			
				else //$reEliminarOrdenCobro['codError']=='00000' && || $reEliminarOrdenCobro['numFilas'] == 0
				{ 					
						//-- Inicio Eliminar una remesa concreta en la tabla REMESAS_SEPAXML ---- 		
						
						$cadenaCondiciones = "NOMARCHIVOSEPAXML = '$nombreArchivoRemesa' AND ANOTADO_EN_CUOTAANIOSOCIO ='NO' ";   	
						
						$reEliminarRemesa = borrarFilas('REMESAS_SEPAXML',$cadenaCondiciones,$conexionDB['conexionLink']);					

						//echo "<br><br>4-1 modeloTesorero:mEliminarOrdenesCobroUnaRemesa:reEliminarRemesa: ";print_r($reEliminarRemesa);	
						
						//-- Fin Eliminar una remesa concreta en la tabla REMESAS_SEPAXML ----

						if ($reEliminarRemesa['codError'] !== '00000' || $reEliminarRemesa['numFilas'] === 0)			
						{ $resEliminarOrdenesCobro = $reEliminarRemesa;				
								$textoErrores = $reEliminarRemesa['errorMensaje']. " al eliminar filas de la tabla REMESAS_SEPAXML. CODERROR: ".$reEliminarRemesa['codError'];					
								
								//echo "<br><br>4-2 modeloTesorero:mEliminarOrdenesCobroUnaRemesa:resEliminarOrdenesCobro:";print_r($resEliminarOrdenesCobro);	
						}
						else //$reEliminarRemesa['codError']!=='00000')			
						{	/*--------- Inicio eliminar archivo de orden de cobro del servidor ---------
								También hay que eliminar el	archivo de orden de cobro de esa remesa SEPA XML
								generado y subido al servidor	dispnible para descagar y llevar a web del 
								banco para su cobro, nombre similar SEPA_ISO20022CORE_2021_03_03H08_43_36.xml)	
								----------------------------------------------------------------------------*/
								$cadEliminarArchivo = '';

								require_once './modelos/modeloArchivos.php';	
								$resEliminarArchivo = eliminarArchivo($dirSEPAXML,$nombreArchivoRemesa);//esta función no necesita acceder a BBDD 

								//echo "<br><br>4-3 modeloTesorero:mEliminarOrdenesCobroUnaRemesa:resEliminarArchivo: ";print_r($resEliminarArchivo);
								
								/*-- Decido no controlar el error en la función "eliminarArchivo()", porque creo que es más importante y necesario
								  poder restaurar las tablas 'ORDENES_COBRO' y 'REMESAS_SEPAXML' a su situación previa a antes de generar la remesa,
										porque pudiera ser que por error se hubiese borrado o modificado el nombre manualmente en el servidor el
										archivo "SEPA_ISO20022CORE-fcha.xml" correspondiente a esa remesa. Es más importante revertir las tablas.
										Se mantiene y envía el mensaje de error de eliminarArchivo() en "$cadEliminarArchivo", si le hubiese, 
										pero no se hace rollback, con lo cual se mantiene la restauración de las tablas.
        */
        /* Nota: Lo dejo comentado por si decido activar el control de error más adelante								
								if ($resEliminarArchivo['codError'] !== '00000') //dejo las dos opciones por si quiero anteponer algún otro comentario
								{ 		
										$cadEliminarArchivo = "<br /><br />".$resEliminarArchivo['arrMensaje']['textoComentarios'];										
										$resEliminarOrdenesCobro['codError'] = $resEliminarArchivo['codError'];					
	         $resEliminarOrdenesCobro['errorMensaje'] = $resEliminarArchivo['errorMensaje'];															
								}
								else//$resEliminarArchivo['codError'] =='00000')
        */								
								{ 
									$cadEliminarArchivo = "<br /><br />".$resEliminarArchivo['arrMensaje']['textoComentarios'];			
									
									//----------- Fin eliminar archivo firma socio del servidor --------------					
							
									//----------------Inicio COMMIT ------------------------------------
									$resFinTrans = commitPDO($conexionDB['conexionLink']);
									
									//echo "<br><br>5-1 modeloTesorero:mEliminarOrdenesCobroUnaRemesa:resFinTrans: ";var_dump($resFinTrans);
									
									if ($resFinTrans['codError'] !== '00000')//sera $resFinTrans['codError'] = '70502';		
									{$resFinTrans['errorMensaje'] = 'Error en el sistema, no se han podido finalizar transación de Eliminar Ordenes Cobro Remesa. ';
										$resFinTrans['numFilas'] = 0;	
									
										$resEliminarOrdenesCobro = $resFinTrans;	
										//echo "<br><br>5-2 modeloTesorero:mEliminarOrdenesCobroUnaRemesa:resEliminarOrdenesCobro:";print_r($resEliminarOrdenesCobro);
									}			
									else
									{								
										$arrMensaje['textoComentarios']="<br /><br /><br />Se han eliminado todos los datos anotados previamente en las tablas 
																																					correspondientes a la remesa con nombre del archivo : $nombreArchivoRemesa	que por algún motivo no fueron cobradas por el banco".
										"<br /><br /><br /><br />Se han eliminado correctamente ".$reEliminarRemesa['numFilas']." fila de la tabla  REMESAS_SEPAXML".
										"<br /><br />Se han eliminado correctamente ".$reEliminarOrdenCobro['numFilas'].
										" filas en la tabla ORDENES_COBRO <br /><br />".$cadEliminarArchivo;					

										$resEliminarOrdenesCobro['arrMensaje']['textoComentarios'] = $arrMensaje['textoComentarios'];
										//echo "<br><br>5-3 modeloTesorero:mEliminarOrdenesCobroUnaRemesa:resEliminarOrdenesCobro:";print_r($resEliminarOrdenesCobro); 					
									}
								}//else $resEliminarArchivo['codError'] =='00000') 	
						}//$reEliminarRemesa['codError']!=='00000')			
				}//else $reEliminarOrdenCobro['codError']=='00000'	
	 	}//else if ( (isset($nombreArchivoRemesa) && !empty($nombreArchivoRemesa))
		}//else $resIniTrans
	 
		//echo "<br><br>5-4 modeloTesorero:mEliminarOrdenesCobroUnaRemesa:resEliminarOrdenesCobro: ";print_r($resEliminarOrdenesCobro); 		
		
		//----------------------- Inicio tratamiento de errores --------------------------------------
		if ($resEliminarOrdenesCobro['codError'] !== '00000')
		{//echo "<br><br>6-1 modeloTesorero:mEliminarOrdenesCobroUnaRemesa:resEliminarOrdenesCobro: ";print_r($resEliminarOrdenesCobro);
			
			if ($resIniTrans['codError'] !== '00000') 
			{	
					$resDeshacerTrans = rollbackPDO($conexionDB['conexionLink']);
					//echo "<br><br>5-1 modeloSocios.php:altaSociosConfirmada:resDeshacerTrans: ";print_r($resDeshacerTrans);
						
					if ($resDeshacerTrans['codError'] !== '00000')//sera $resDeshacerTrans['codError'] = '70503';
					{ $resDeshacerTrans['errorMensaje'] = 'Error en el sistema, no se ha podido deshacer la transación. ';
							$resDeshacerTrans['numFilas'] = 0;
							$resEliminarOrdenesCobro = $resDeshacerTrans;
							//echo "<br><br>6-2 modeloTesorero:mEliminarOrdenesCobroUnaRemesa:resEliminarOrdenesCobro: ";print_r($resEliminarOrdenesCobro);
					}		
			}				
	
			$arrMensaje['textoComentarios'] = "Por errores producidos en el proceso no se ha podido borrar la remesa de ordenes de cobro: ".$nombreArchivoRemesa.
																																					" de las tablas REMESAS_SEPAXML y ORDENES_COBRO, ".$cadEliminarArchivo.
																																					"	vuelve a intentarlo pasado un tiempo y en caso de siga el error, avisa al administrador de esta aplicación, e indica el mensaje de error.<br /><br/> ".
																																					$textoErrores.". Gestor CODUSER: ".$_SESSION['vs_CODUSER']." Función: ".	$resEliminarOrdenesCobro['nomScript'].": ".$resEliminarOrdenesCobro['nomFuncion'];

			$arrInsertarErrores = $resEliminarOrdenesCobro;		
			$arrInsertarErrores['textoComentarios'] =  $textoErrores." Función: ".	$resEliminarOrdenesCobro['nomFuncion'].": ".$resEliminarOrdenesCobro['nomFuncion'].". Gestor CODUSER: ".$_SESSION['vs_CODUSER'];;	
																																					
			$resEliminarOrdenesCobro['arrMensaje']['textoComentarios'] = $arrMensaje['textoComentarios'];	
			//echo "<br><br>6-3 modeloTesorero:mEliminarOrdenesCobroUnaRemesa:resEliminarOrdenesCobro: ";print_r($resEliminarOrdenesCobro);		
			
			require_once './modelos/modeloErrores.php'; //si es un error insertar en tabla ERROR	
			$resEliminarErrores = insertarError($arrInsertarErrores);
		 //echo "<br><br>6-4 modeloTesorero:mEliminarOrdenesCobroUnaRemesa:resEliminarErrores: ";print_r($resEliminarErrores);	
			
			if ($resEliminarErrores['codError'] !=='00000')
			{ $resEliminarOrdenesCobro['errorMensaje'] .= $resEliminarErrores['errorMensaje'];
					$resEliminarOrdenesCobro['arrMensaje']['textoComentarios'].="Error del sistema al tratar ERRORES, ".$resEliminarOrdenesCobro['errorMensaje']." CODERROR: ".$resEliminarErrores['codError'].
																																				" vuelva a intentarlo pasado un tiempo ";
			}
	 }//if (mEliminarOrdenesCobroUnaRemesa['codError']!=='00000')	
	 //----------------------- Fin tratamiento de errores --------------------------------------		
 }//else //$conexionUsuariosDB['codError']=="00000" // *** este else debiera ir detras de insertarERRor
		
 //echo "<br>7-modeloTesorero:mEliminarOrdenesCobroUnaRemesa:resEliminarOrdenesCobro: "; print_r($resEliminarOrdenesCobro); 
	
 return $resEliminarOrdenesCobro;
}
/*------------------- Fin mEliminarOrdenesCobroUnaRemesa -----------------------------------------*/

/*---------------- Inicio descargarAchivoSEPAXML.php -----------------------------------------------
DESCRIPCION: Descarga del servidor el archivo de ordenes de cobro para subir a Web del B. Santander
"$nomArchivo" generado desde cTesorero.php:exportarCuotasXMLBancos():crearEscribirArchivoSEPAXML()
que será algo parecido a "SEPA_ISO20022CORE_2021_03_02H20_08_38.xml" (tipo XML).

Se encontrará en "directorio"= "/../upload/TESORERIA/SEPAXML_ISO20022", que es de acceso privado,
por los que sólo se puede descargar mediante "header" ya que en los directorios de acceso privado 
no se puede utilizar "href".								

RECIBE:
- $directorio (sin nomArchivo viene de cTesorero.php:descargarAchivoOrdenesCobroRemesaTes()) que 
  será path relativo al directorio raíz: $directorio= "/../upload/TESORERIA/SEPAXML_ISO20022" 
		esta por encima de raíz	con acceso restringido  
- $nomArchivo: nom. archivo con extensión, desde cTesorero.php:descargarAchivoOrdenesCobroRemesaTes(),
  que a su vez vendrá del formulario "/vistas/tesoro/:vEstadoOrdenesCobroRemesasTes.php" 
  ejemplo: SEPA_ISO20022CORE_2017-12-19H09-57-04.xml

DEVUELVE: la descarga del archivo XML con las órdenes de cobro para B.Santander	y un array con los 
          campos de errores	de falta de datos 
													
LLAMADA:cTesorero.php:descargarAchivoOrdenesCobroSEPAXMLTes()
LLAMA: modelos/modeloErrores.php:insertarError()

OBSERVACIONES:  PHP 7.3.21
Al utilizar header, no poner ninguna salida tipo echo ...
Aunque esta funcion se podría incluir dentro del modeloArchivos.php, para mayor independencia y
seguridad frente o posibles cambios, la dejo en modeloTesorero.php, para uso exclusivo ya que es 
una función muy crítica.

Se podría definir: define('APLICATION_ROOT', getcwd());echo "<br />aplicacion-root: ".APLICATION_ROOT;
echo "<br><br>directorio actual 1:";echo getcwd();//directorio/actual	

nodo50 "max_execution_time": 130seg, cambiar temporalmente con: 
ini_set('max_execution_time', 200);
nodo50 "memory_limit": 4096 M, se puede cambiar temporalmente con: 
ini_set('memory_limit', '8000M');
nodo50: max_file_uploads	20
ver con: phpinfo();
-----------------------------------------------------------------------------------------------------*/
function descargarAchivoSEPAXML($directorio,$nomArchivo)	
{
	//echo "<br><br>0-1 modeloTesorero.php:descargarAchivoSEPAXML:directorio: ";print_r($directorio);  
	//echo "<br><br>0-2 modeloTesorero.php:descargarAchivoSEPAXML:nomArchivo: ";print_r($nomArchivo);
	
	$arrDescargarAchivoSEPAXML['nomScript'] = 'modeloTesorero.php';	
 $arrDescargarAchivoSEPAXML['nomFuncion'] = 'descargarAchivoSEPAXML';
	$arrDescargarAchivoSEPAXML['codError'] = '00000';
	$arrDescargarAchivoSEPAXML['errorMensaje'] = '';
	$arrDescargarAchivoSEPAXML['textoComentarios'] = '';
	
	set_time_limit(240);//sería 4 minutos//set_time_limit(0); para ampliar el tiempo de ejecución	a indefinido
 
	if (!isset($directorio) || empty($directorio) || !isset($nomArchivo) || empty($nomArchivo)) 
	{		
		$arrDescargarAchivoSEPAXML['codError'] = '50040'; // deben existir
		$arrDescargarAchivoSEPAXML['errorMensaje'] = 'Error: faltan parámetros $directorio o $nomArchivo del archivo SEPA-XML para poder descargarlo';		
  $arrDescargarAchivoSEPAXML['textoComentarios'] = " Función: ".	$arrDescargarAchivoSEPAXML['nomScript'].": ".$arrDescargarAchivoSEPAXML['nomFuncion'].". Gestor CODUSER: ".$_SESSION['vs_CODUSER'];	
	}	
	else 
 {  
  //$directorio = "/usuarios_desarrollo/controladores";//ok directorio público, para pruebas se puede acceder por url href	
		//$nomArchivo = "esUnArchivo_falso.txt";		
					
	 $directorioSubirPath = $_SERVER['DOCUMENT_ROOT'].$directorio;//será: /home/virtualmin/europalaica.com/public_html/../upload/TESORERIA/SEPAXML_ISO20022	 
		$directorio = realpath($directorioSubirPath);//será: /home/virtualmin/europalaica.com/upload/TESORERIA/SEPAXML_ISO20022
		
		//echo "<br><br>2-1 modeloTesorero.php:descargarAchivoSEPAXML:directorio: ".$directorio;
		
		$directorio_y_archivo = $directorio.'/'.$nomArchivo;

  //echo "<br><br>2-2 modeloTesorero.php:descargarAchivoSEPAXML:directorio_y_archivo: ".$directorio_y_archivo;//=/home/virtualmin/europalaica.com/upload/TESORERIA/SEPAXML_ISO20022/SEPA_ISO20022CORE_2021_03_02H20_08_38.xml
		
		//------- Inicio comprobar la extensión del archivo --------- 
		/* Formas de obtener la extension:
		$arrNomExtAchivo = explode(".",$nomArchivo);//devuelve un array de string a partir del nombre del archivo obtenidos por la separación por el punto "."		
		$iExt = count($arrNomExtAchivo)-1; //por si habiese varios puntos incluidos en el nombre, se toma el último que corresponde solo a extensión	
		$extension = $arrNomExtAchivo[$iExt]; //extensión del archivo que debiera ser "xml"	
  //---
		//$fileInfo = pathinfo($directorio_y_archivo); echo "<br><br>3-0-1 cTesorero:fileInfo:";print_r($fileInfo);				 
		//$directorio = $fileInfo['dirname']; echo "<br><br>3-0-2 cTesorero:directorio: ".$directorio;//directorio absoluto:  /home/virtualmin/europalaica.com/upload/TESORERIA/SEPAXML_ISO20022
		//$nombreArchivo = $fileInfo['basename']; echo "<br><br>3-0-3 cTesorero:nombreArchivo: ".$nombreArchivo;//nombre archivo
		//$extension = $fileInfo['extension']; echo "<br><br>3-0-4 cTesorero:extension: ".$extension;//=xml, necesita previo pathinfo($directorio_y_archivo);
		//$extension = pathinfo($directorio_y_archivo,PATHINFO_EXTENSION); echo "<br><br>3-0-5 cTesorero:extension: ".$extension;//=xml, solo devuelve ['extension']
		//------- Fin comprobar la extensión del archivo ------------- 
		*/
		$tipoMIME = "application/xml";//para el archivo que se descargará tipo "xml"
		$extension = pathinfo($nomArchivo,PATHINFO_EXTENSION); //echo "<br><br>3-0-6 cTesorero:extension: ".$extension;//=xml lo más simple ['extension']

		if ($extension !== 'xml')
		{ //echo "<br><br>3-1 modeloTesorero.php:descargarAchivoSEPAXML:directorio_y_archivo: ";print_r($directorio_y_archivo);
			$arrDescargarAchivoSEPAXML['codError'] = '50045';
			$arrDescargarAchivoSEPAXML['errorMensaje'] = 'Error: Extensión no adecuada'.$directorio_y_archivo;
		}
		//------- Fin comprobar la extensión del archivo -------------
		else //$extension == 'xml'
		{
			//echo "<br><br>4-1 modeloTesorero.php:descargarAchivoSEPAXML:directorio_y_archivo: ";print_r($directorio_y_archivo);
					
			if (!file_exists($directorio_y_archivo))// pregunta si existe directorio o fichero (acaso preguntar primero por directorio )
			{
					//echo "<br><br>4-2 modeloTesorero.php:descargarAchivoSEPAXML:directorio_y_archivo: ";print_r($directorio_y_archivo);
					$arrDescargarAchivoSEPAXML['codError'] = '50040';
					$arrDescargarAchivoSEPAXML['errorMensaje'] = 'Error: No existe el path al archivo'.$directorio_y_archivo;
     $arrDescargarAchivoSEPAXML['textoComentarios'] = " Error: modeloTesorero.php:descargarAchivoSEPAXML() en file_exists() ";					
			}
			elseif (!is_readable($directorio_y_archivo))// pregunta si el fichero es legible (solo para archivos)
			{ 
					//echo "<br><br>4-3 modeloTesorero.php:descargarAchivoSEPAXML:directorio_y_archivo: ";print_r($directorio_y_archivo);
					$arrDescargarAchivoSEPAXML['codError'] = '50015';
					$arrDescargarAchivoSEPAXML['errorMensaje'] = 'Error: El archivo no se pùede leer '.$directorio_y_archivo;
					$arrDescargarAchivoSEPAXML['textoComentarios'] = " Error: modeloTesorero.php:descargarAchivoSEPAXML() en is_readable() "; 
			}
			else // is_readable($directorio_y_archivo))// pregunta si el fichero es legible (solo para archivos)
			{ 
					//echo "<br><br>5-1 modeloTesorero.php:descargarAchivoSEPAXML:directorio_y_archivo: ";print_r($directorio_y_archivo);
					
					//----- Inicio formato estandar, sin control de error en readfile()	-------------------------------------							
					header("Content-type:".$tipoMIME);	//para indicar el tipo de datos del archivo, para XML sería //header("Content-type: application/xml");						
					header('Content-disposition: attachment; filename="'.$nomArchivo.'"');	//alguien recomienda doble "						
					//header("Content-disposition: attachment; filename=".$nomArchivo);							
					header("Pragma: public"); 
					header("Expires: -1"); 
					//header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); //Fecha en el pasado	
					header("Cache-Control: no-cache"); 
					header("Cache-Control: public, must-revalidate, post-check=0, pre-check=0"); 						
					header("Content-Length: ".filesize($directorio_y_archivo));								
					//http_response_code(400); // this will get previous response code 200 and set a new one to 400 , No se como se usa es para códigos de respuesta???	
											
					ob_clean();//Esta función desecha el contenido que hubiese en búfer de salida e impide mostrar todas las salidas previas de echo y demás en el navegador.
					flush();//Vaciar el búfer de salida del sistema
					
					$resReadFile = readfile($directorio_y_archivo);//controlar aquí el error es complicado, mejor la siguiente opción					
		
					exit;		
					//----- Fin formato estandar sin control de error en readfile() -----------------------------------------
			
					//echo "<br><br>5-2 modeloTesorero.php:descargarAchivoSEPAXML:directorio_y_archivo: ";print_r($directorio_y_archivo);			
				
			}//else is_readable($directorio_y_archivo))// pregunta si el fichero es legible (solo para archivos)
	 }//else $extension == 'xml' 
	}//else if (isset($_POST['NOMARCHIVOSEPAXML']) && !empty($_POST[NOMARCHIVOSEPAXML'])) 
	
 //echo "<br><br>6 modeloTesorero.php:descargarAchivoSEPAXML:arrDescargarAchivoSEPAXML: ";print_r($arrDescargarAchivoSEPAXML);
	
	if ($arrDescargarAchivoSEPAXML['codError'] !== '00000')
	{
		require_once './modelos/modeloErrores.php'; 
		$resDescargarAchivoErrores = insertarError($arrDescargarAchivoSEPAXML);
		
		if ($resDescargarAchivoErrores['codError'] !== '00000')
		{ $arrDescargarAchivoSEPAXML['errorMensaje'] .= $resDescargarAchivoErrores['errorMensaje'];
				$arrDescargarAchivoSEPAXML['textoComentarios'] .= "Error del sistema al tratar ERRORES, ".$resDescargarAchivoErrores['errorMensaje']." CODERROR: ".$resDescargarAchivoErrores['codError'];
		}	
 }		
		
	return $arrDescargarAchivoSEPAXML;
}
/*---------------- Fin descargarAchivoSEPAXML.php ---------------------------------------------------*/

/*------------------- Inicio mActualizarCuotasCobradasEnRemesaTes ----------------------------------
.Se actualizan los pagos de las cuotas en la tabla "CUOTAANIOSOCIO" (ESTADOCUOTA = ABONADA y
otros campos), a partir de las órdenes de cobro anotadas en la tabla "ORDENES_COBRO" correspondientes 
a una remesa SEPA XML y que tienen el campo ANOTADO_EN_CUOTAANIOSOCIO ='NO'.
.Se actualizan las correspondiente filas en tabla "ORDENES_COBRO" de la remesa SEPA XML 
(ANOTADO_EN_CUOTAANIOSOCIO ='SI' y otros campos.
.En tabla "SOCIO" el campo se pone a SECUENCIAADEUDOSEPA = 'RCUR' si es el mismo IBAN 
.Por último se actualizan varios campos de esa remesa en SEPA-XML en la tabla "REMESAS_SEPAXML"
(IMPORTEGASTOSREMESA,FECHAPAGO,ANOTADO_EN_CUOTAANIOSOCIO ='SI',FECHAANOTACIONPAGO)
.Se elimina el archivo "NOMARCHIVOSEPAXML" del servidor una vez actualizas las tablas antes citadas 

En la función "modeloTesorero.php:cambiosDatosSociosEnRemesaEnviadasTes()" se buscan los posibles
cambios realizados por algunos socios o gestores y que modifican sus datos, por ejemplo: se dan de baja,
cambian IBAN, pagan por otros medios (PayPal), modificando su cuota, etc. dando lugar a diferencias 
entre valores de algunos campos que contiene la remesa y los existentes en el momento en la BBDD. 
En esa función se genera strings, con datos de las variaciones si se han producido, y que se utilizarán
para guardarlos en los campos [OBSERVACIONES] de tablas "CUOTAANIOSOCIO" y "ORDENES_COBRO" de cada 
correspondiente socio, y también todos los cambios en el campo [OBSERVACIONES] en tabla "REMESAS_SEPAXML"
Esta llamará tantas veces como órdenes de cobro de socios existan en la remesa, dentro de un bucle
"while ($fila < $ultimaF && $resActualizarCuotasCobradas['codError'] =='00000')"

Al final, se generan los datos para mostrar el listado, con el número de actualizaciones en 
cada tabla, y las variaciónes que se hayan producido y que afecten a la remesa: Bajas, Pagos por 
otros medios, Cambios de cuotas, Cambios de Cuenta IBAN.

Para buscar las órdenes de cobro de esa remesa se utiliza el campo "NOMARCHIVOSEPAXML" ya que es 
que ese el nombre del archivo SEPA XML generado y subido a la web del banco para su cobro

Se tratan los posibles errores.
								
RECIBE: "$arrDatosArchivoRemesa" desde cTesorero.php:actualizarCuotasCobradasEnRemesaTes()
        procedente $_POST["datosFormOrdenCobroRemesa"], con datos de la remesa enviada al banco,
								desde el formulario: vistas/tesoro/vEstadoOrdenesCobroRemesasTes.php, que entre otros 
								incluye 'NOMARCHIVOSEPAXML' y 'DIRECTORIOARCHIVOREMESA'								
								
DEVUELVE: un array con los controles de errores, y mensajes 

LLAMADA: cTesorero.php:actualizarCuotasCobradasEnRemesaTes()         
LLAMA: modeloTesorero.php:cambiosDatosSociosEnRemesaEnviadasTes() 
modeloMySQL.php:buscarCadSql(),actualizarTabla(),
modeloSocios.php:buscarDatosSocioCodSocio(),modeloTesorero.php:actualizarRemesa()
modelos/modeloArchivos.php:eliminarArchivo()
/usuariosConfig/BBDD/MySQL/configMySQL.php:conexionDB(),.....
modelos/modeloErrores.php:insertarError()							

ACLARACIONES:
ORDENES_COBRO.CUOTADONACIONPENDIENTEPAGO = CuotaDonacionPendienteCobro=(CUOTAANIOSOCIO.IMPORTECUOTAANIOSOCIO-CUOTAANIOSOCIO.IMPORTECUOTAANIOPAGADA)=cantidad que SE ORDENA COBRAR al banco
(en general CUOTAANIOSOCIO.IMPORTECUOTAANIOPAGADA=0, excepto en el caso de que CUOTAANIOSOCIO.ESTADOCUOTA='ABONADO_PARTE' pero no la cuota elegida completa)

$arrayDatosActOrdenesCobro['IMPORTECUOTAANIOPAGADA'] = ORDENES_COBRO.IMPORTECUOTAANIOPAGADA es el importe pagado en la orden de cobro. 
ORDENES_COBRO.IMPORTECUOTAANIOPAGADA inicialmente tendrá el valor de 0€. Aquí en esta función después de “actualizarTabla('ORDENES_COBRO',...)” 
(a la vez actualizará las tablas CUOTAANIOSOCIO, SOCIO, ORDENES_COBRO, REMESAS_SEPAXML), el campo pasará a tener el valor pagado en 
la remesa por el socio y que es ORDENES_COBRO.CUOTADONACIONPENDIENTEPAGO.
En caso de posterior “Devolución” volverá a tener el valor 0€. 

ORDENES_COBRO.IMPORTECUOTAANIOPAGADA es el importe pagado en la orden de cobro, en general coincidirá con CUOTAANIOSOCIO.IMPORTECUOTAANIOPAGADA, 
pero NO coincidirá si fuese el caso de ESTADOCUOTA=ABONADA-PARTE, en ese caso ORDENES_COBRO.IMPORTECUOTAANIOPAGADA < CUOTAANIOSOCIO.IMPORTECUOTAANIOPAGADA. 		
	
OBSERVACIONES: Probada PHP 7.3.21	
2020-11-11:modifico para incluir PHP:DOStatement:bindParamValue. 
---------------------------------------------------------------------------------------------------*/
function mActualizarCuotasCobradasEnRemesaTes($arrDatosArchivoRemesa)
{	
	//echo "<br><br>0-1 modeloTesorero:mActualizarCuotasCobradasEnRemesaTes:arrDatosArchivoRemesa: "; print_r($arrDatosArchivoRemesa);
		
	require_once "./modelos/modeloSocios.php";//para buscarDatosSocioCodSocio($codSocio,$anioCuota);
	require_once './modelos/modeloErrores.php';
 require_once "./modelos/BBDD/MySQL/modeloMySQL.php";	
	require_once './modelos/modeloArchivos.php';	//esta función no necesita acceder a la BBDD 
	
	$resActualizarCuotasCobradas['nomScript'] = "modeloTesorero.php";	
	$resActualizarCuotasCobradas['nomFuncion'] = "mActualizarCuotasCobradasEnRemesaTes";
 $resActualizarCuotasCobradas['codError'] = '00000';
 $resActualizarCuotasCobradas['errorMensaje'] = '';	 

 //--- Inicio procedentes de formulario vistas/tesoro/vEstadoOrdenesCobroRemesasTes.php -----
 $nombreArchivoSEPAXML = $arrDatosArchivoRemesa['NOMARCHIVOSEPAXML']['valorCampo']; 
	$dirSEPAXML = $arrDatosArchivoRemesa['DIRECTORIOARCHIVOREMESA']['valorCampo'];
	$anioCuota = $arrDatosArchivoRemesa['ANIOCUOTA']['valorCampo']; 
 $fechaOrdenCobroArchivoRemesa = $arrDatosArchivoRemesa['FECHAORDENCOBRO']['valorCampo']; 
	$fechaCreacionArchivoRemesa = $arrDatosArchivoRemesa['FECHA_CREACION_ARCHIVO_SEPA']['valorCampo'];	
	
	$fechaPagoBanco = $arrDatosArchivoRemesa['FECHAPAGO']['anio']['valorCampo']."-".$arrDatosArchivoRemesa['FECHAPAGO']['mes']['valorCampo']."-".$arrDatosArchivoRemesa['FECHAPAGO']['dia']['valorCampo'];																																															
	//-----------------------------------------------------------------------------------------
	
 require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	require_once "BBDD/MySQL/conexionMySQL.php";
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);	
	
	if ($conexionDB['codError'] !== "00000")
	{ $resActualizarCuotasCobradas['codError'] = $conexionDB['codError'];
   $resActualizarCuotasCobradas['errorMensaje'] = $conexionDB['errorMensaje'];
   $textoErrores = "Error del sistema en modeloTesorero.php:mActualizarCuotasCobradasEnRemesaTes() al conectarse a la base de datos";					   
	}
 else //$conexionDB['codError']=="00000"
	{
		require_once("BBDD/MySQL/transationPDO.php");
		$resIniTrans = beginTransationPDO($conexionDB['conexionLink']);
		
		//echo "<br><br>1-1 modeloTesorero:mActualizarCuotasCobradasEnRemesaTes::resIniTrans: ";var_dump($resIniTrans);	echo "<br>";
				
		if ($resIniTrans['codError'] !== '00000') //será ['codError'] = '70501';
		{	$resIniTrans['numFilas'] = 0;
    $resActualizarCuotasCobradas['codError'] = $resIniTrans['codError'];
    $resActualizarCuotasCobradas['errorMensaje'] = $resIniTrans['errorMensaje'];
    $textoErrores = "Error del sistema en modeloTesorero.php:mActualizarCuotasCobradasEnRemesaTes() al iniciar la transación";							
				//echo "<br><br>1-2 modeloTesorero:mActualizarCuotasCobradasEnRemesaTes:resActualizarCuotasCobradas: ";var_dump($resActualizarCuotasCobradas);	echo "<br>";
		}			
		else //$resIniTrans['codError'] == '00000
		{	
    /*-- Inicio buscar las órdenes de cobro de una remesa, en "ORDENES_COBRO" por el campo "NOMARCHIVOSEPAXML" --
				  a partir del valor recibido deasde el fomulario  formActualizarCuotasCobradasEnRemesaTes.php	 -----------*/
						
			 $cadSql = "SELECT *	FROM ORDENES_COBRO WHERE NOMARCHIVOSEPAXML = :nombreArchivoSEPAXML ";																
														
    $arrBind = array(':nombreArchivoSEPAXML' => $nombreArchivoSEPAXML); 
				
				$arrOrdenesCobroRemesa = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);//en modelos/BBDD/MySQL/modeloMySQL.php, error ok		
				
    //echo "<br><br>2-1 modeloTesorero:mActualizarCuotasCobradasEnRemesaTes:arrOrdenesCobroRemesa: ";print_r($arrOrdenesCobroRemesa);				
				
				if ($arrOrdenesCobroRemesa['codError'] !== '00000')
				{ $resActualizarCuotasCobradas['codError'] = $arrOrdenesCobroRemesa['codError'];
						$resActualizarCuotasCobradas['errorMensaje'] = $arrOrdenesCobroRemesa['errorMensaje'];						
					 $textoErrores = "Error del sistema en modeloTesorero.php:mActualizarCuotasCobradasEnRemesaTes(), al buscar en tabla ORDENES_COBRO con la función buscarCadSql()";
				} 
    elseif ($arrOrdenesCobroRemesa['numFilas'] <= 0)
    { $resActualizarCuotasCobradas['codError'] = '80001'; 
      $resActualizarCuotasCobradas['errorMensaje'] = "No se han encontrado filas que cumplan las condiciones al buscar en tabla ORDENES_COBRO";	  						
					 $textoErrores = "Error lógico en modeloTesorero.php:mActualizarCuotasCobradasEnRemesaTes(). No se han encontrado filas que cumplan las condiciones en tabla ORDENES_COBRO con la función buscarCadSql()";					
				}				
				/*-- Fin buscar los datos de las órdenes de cobro de una remesa ------------------------------------------*/
				else //$arrOrdenesCobroRemesa['codError']=='00000'
				{					
     $contadorCuotasAnioSociosActualizados = 0;
					$contadorSociosCobrosActualizados = 0;					
					$contadorOrdenesCobrosActualizados = 0;
					
					$resActualizarCuotasCobradas['codError'] = '00000';
					$fila = 0; 
					$ultimaF = $arrOrdenesCobroRemesa['numFilas'];

     $ordenesCobroRemesa = $arrOrdenesCobroRemesa['resultadoFilas'];//para acortar nombres		 					
					
					/*--- Inicio de bucle para actualizar CUOTAANIOSOCIO, ORDENES_COBRO y SOCIO ------------------------------*/
					while ($fila < $ultimaF && $resActualizarCuotasCobradas['codError'] === '00000')
					{						
							//-- Buscar datos completos del socio en varias tablas, incluidos los existentes  tabla CUOTAANIOSOCIO --
							
							$reDatosSocioActuales = buscarDatosSocioCodSocio($ordenesCobroRemesa[$fila]['CODSOCIO'],$anioCuota);//en modeloSocios.php, error ok
       //echo "<br><br>3-1-1 modeloTesorero:mActualizarCuotasCobradasEnRemesaTes:reDatosSocioActuales: ";print_r($reDatosSocioActuales);		

							if ($reDatosSocioActuales['codError'] !== '00000')
							{$resActualizarCuotasCobradas['codError'] = $reDatosSocioActuales['codError'];
						  $resActualizarCuotasCobradas['errorMensaje'] = $reDatosSocioActuales['errorMensaje'];								
								$textoErrores = "Error en modeloTesorero.php:mActualizarCuotasCobradasEnRemesaTes(), al buscar con la función buscarDatosSocioCodSocio()";
							}      
							else //$reDatosSocioOrdenesCobro['codError']=='00000'
							{$datosSocioActuales = $reDatosSocioActuales['valoresCampos'];
	
							 /*---- Inicio buscar datos en MIEMBROELIMINADO5ANIOS solo para socio que ha sido baja -------------
           para buscar Nombre en la tabla que los mantiene por 5 años que es necesario para	siguiente ---*/
 										
								$arrBuscarMiembroEliminado = '';//para evitar notice
								if ($datosSocioActuales['datosFormUsuario']['ESTADO']['valorCampo'] == 'baja')
								{ 
									$cadSql = "SELECT * FROM MIEMBROELIMINADO5ANIOS WHERE CODUSER = :codUser ";
									
									//echo "<br><br>3-2-1 modeloTesorero:mActualizarCuotasCobradasEnRemesaTes:datosSocioActuales['datosFormUsuario']['CODUSER']['valorCampo']: ";print_r($datosSocioActuales['datosFormUsuario']['CODUSER']['valorCampo']);				
									
         $arrBind = array(':codUser' => $datosSocioActuales['datosFormUsuario']['CODUSER']['valorCampo']); 
				
				     $arrBuscarMiembroEliminado = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);//en modelos/BBDD/MySQL/modeloMySQL.php			
									//echo "<br><br>3-2-2 modeloTesorero:mActualizarCuotasCobradasEnRemesaTes:arrBuscarMiembroEliminado: ";print_r($arrBuscarMiembroEliminado);				
									
									if ($arrBuscarMiembroEliminado['codError'] !== '00000')
									{$resActualizarCuotasCobradas['codError'] = $arrBuscarMiembroEliminado['codError'];
						    $resActualizarCuotasCobradas['errorMensaje'] = $arrBuscarMiembroEliminado['errorMensaje'];								
							  	$textoErrores = "Error del sistema en modeloTesorero.php:mActualizarCuotasCobradasEnRemesaTes(), al buscar en tabla MIEMBROELIMINADO5ANIOS con la función buscarCadSql()";						
									}   
									elseif ($arrBuscarMiembroEliminado['numFilas'] <= 0)
									{ $resActualizarCuotasCobradas['codError'] = '80001'; 
											$resActualizarCuotasCobradas['errorMensaje'] = "No se han encontrado filas que cumplan las condiciones al buscar en tabla ORDENES_COBRO";	  						
											$textoErrores = "Error lógico en modeloTesorero.php:mActualizarCuotasCobradasEnRemesaTes(). No se han encontrado filas que cumplan las condiciones en tabla MIEMBROELIMINADO5ANIOS con la función buscarCadSql()";	
         }											
									else //$arrBuscarMiembroEliminado['codError']=='00000'
									{$arrBuscarMiembroEliminado = $arrBuscarMiembroEliminado['resultadoFilas'][0];	
									}
								}
								/*-------- Fin buscar datos en MIEMBROELIMINADO5ANIOS para socio que ha sido baja ----------------*/							
								
								if ($resActualizarCuotasCobradas['codError'] == '00000')//1	
								{									
									/*---- Inicio cambiosDatosSociosEnRemesaEnviadasTes() --------------------------------------------
										Buscar posibles cambios realizados por socios o gestores: bajas,	cambian IBAN, etc. dando lugar a 
										diferencias entre valores de algunos campos que contiene la remesa y los existentes en el momento 
										otras tablas de BBDD. Se genera strings, con datos de las variaciones producidas, que se utilizarán
										para guardarlos en los campos [OBSERVACIONES] de tablas "CUOTAANIOSOCIO" y "ORDENES_COBRO" de cada 
										correspondiente socio, y todos los cambios en el campo [OBSERVACIONES] en tabla "REMESAS_SEPAXML"
										Y mostrar listado de esto al final -------------------------------------------------------------*/									
 
									$arrCambiosDatosSociosEnRemesa = cambiosDatosSociosEnRemesaEnviadasTes($anioCuota,$datosSocioActuales,$ordenesCobroRemesa[$fila],$arrBuscarMiembroEliminado);//en modeloTesorero.php, no requiere contro errores
									
									//echo "<br><br>3-3-1 modeloTesorero:mActualizarCuotasCobradasEnRemesaTes:arrCambiosDatosSociosEnRemesa: ";print_r($arrCambiosDatosSociosEnRemesa); 	

									$observacionesCambios = $arrCambiosDatosSociosEnRemesa['observacionCambiosDatosSociosEnRemesa'];

									if (isset($arrCambiosDatosSociosEnRemesa['listaCambiosDatosSociosEnRemesa']) && !empty($arrCambiosDatosSociosEnRemesa['listaCambiosDatosSociosEnRemesa']))
									{ 	
											$encabezadoCambiosDatos = "<br /><br /><strong> AVISO: CAMBIOS EN DATOS DE LOS SOCIOS/AS INCLUIDOS EN LA REMESA QUE PUEDEN DAR LUGAR A RECLAMACIONES</strong>". 
																																							"<br />Desde el día que se envió esta remesa al banco hasta hoy, se han producido modificaciones en los datos de algunos socios/as, ".
																																							"puede ser conveniente contactar con el socio/a. ".
																																							"En el caso de bajas de socios/as incluidos en la remesa, puedes encontrar algunos datos (borrados por privacidad de datos), ".
																																							"en el archivo EXCEL, generado el día de creación de la remesa.<br />";	

											$listadoCambiosDatosSociosEnRemesaEnviadasTes = $encabezadoCambiosDatos.$arrCambiosDatosSociosEnRemesa['listaCambiosDatosSociosEnRemesa'];
											//echo "<br><br>3-3-2 modeloTesorero:mActualizarCuotasCobradasEnRemesaTes:listadoCambiosDatosSociosEnRemesaEnviadasTes: ";print_r($listadoCambiosDatosSociosEnRemesaEnviadasTes); 	
									}
						
									/*---- Fin buscarCambiosDatosSociosEnRemesaEnviadasTes.php --------------------------------*/

									/*----------- Inicio actualizar tabla SOCIOS de FRST a RCUR	--------------------------------
          Solo para cuando cumplen la condición misma CUENTAIBAN (no se ha cambiado después de generar ordenes SEPA), 
									 Cuando se quita CUENTAIBAN o es BAJA se considera distinta cuenta.------------------------*/
									if ($ordenesCobroRemesa[$fila]['CUENTAIBAN'] == $datosSocioActuales['datosFormSocio']['CUENTAIBAN']['valorCampo'])//si la cuenta no ha cambiado pasará a ser RCUR
									{ //1ª condición 
											$arrayCondicionesSocio['CODSOCIO']['valorCampo'] = $ordenesCobroRemesa[$fila]['CODSOCIO'];
											$arrayCondicionesSocio['CODSOCIO']['operador'] = '=';
											$arrayCondicionesSocio['CODSOCIO']['opUnir'] = ' ';

											$arrayDatosActSocio['SECUENCIAADEUDOSEPA'] = 'RCUR';
									
											$reActSocio = actualizarTabla('SOCIO',$arrayCondicionesSocio,$arrayDatosActSocio,$conexionDB['conexionLink']);//en modelos/BBDD/MySQL/modeloMySQL.php, error ok									
											//echo "<br><br>3-4-1 modeloTesorero:mActualizarCuotasCobradasEnRemesaTes:reActSocio: ";print_r($reActSocio);echo "<br><br>";
									
											if ($reActSocio['codError'] !== '00000')			
											{ $resActualizarCuotasCobradas['codError'] = $reActSocio['codError'];
						       $resActualizarCuotasCobradas['errorMensaje'] = $reActSocio['errorMensaje'];								
							  	   $textoErrores = "Error del sistema en modeloTesorero.php:mActualizarCuotasCobradasEnRemesaTes(), al actualizar la tabla SOCIO con la función actualizarTabla()";																			
											}
											//OJO ($reActSocio['numFilas'] = 0) NO ES ERROR,  ya que si ya era RCUR, el UPDATE al actualiza a RCUR y devuelve = 0 filas									
											else //($reActSocio['codError']!='00000')		
											{
												if ($reActSocio['numFilas'] != 0)
												{ $contadorSociosCobrosActualizados++;
												}	
											}											
									}//if ($ordenesCobroRemesa[$fila]['CUENTAIBAN'] == $datosSocioActuales['datosFormSocio']['CUENTAIBAN']['valorCampo'])			

									/*---------- Fin actualizar tabla SOCIOS de FRST a RCUR -----------------------------------*/
									
									if ($resActualizarCuotasCobradas['codError'] == '00000')	//2
									{								
											/*- Inicio actualizar CUOTAANIOSOCIO incluye ACUMULAR valores IMPORTEGASTOSABONOCUOTA,IMPORTECUOTAANIOPAGADA,OBSERVACIONES -*/											
											/*if ($datosSocioActuales['datosFormCuotaSocio'][$anioCuota]['IMPORTECUOTAANIOSOCIO']['valorCampo'] !== $ordenesCobroRemesa[$fila]['IMPORTECUOTAANIOSOCIO'])
											{	*** En los casos en que se cumpla la siguiente condición, *** Aunque se avisa al Tesorero, que debe revisar y actualizar los datos, 
													por si no lo hace	AQUI SE PODRÍA INSERTAR EN "CUOTAANIOSOCIO" NUEVOS VALORES ELEGIDOS PARA EL AÑO SIGUIENTE (Y+1), 
													PARA QUE LAS MODIFICACIONES DE IMPORTECUOTAANIOSOCIO, CODCUOTA,CUENTAIBAN, CODAGRUPACION, ETC. SE ANOTEN PARA EL PRÓXIMO AÑO
													SI YA HUBIESE ANOTACIÓN EN AÑO SIGUIENTE (Y+1)PARA ESTE SOCIO HABRÍA QUE HACER UPDATE "CUOTAANIOSOCIO" EN LUGAR DE INSERT										
													(habría que comprobar que ya está pagada por socio por otros medios, que es condición necesaria, y además que SÍ existe fila de año siguiente)													
													Se verá la conveniencia de hacerlo al revisar la aplicación de cierre de año, creo que ya está contemplado 					
											}*/
											
											//-- 1ª condicción -----------------
											$arrCondicionesCuotaAnioSocio['CODSOCIO']['valorCampo'] = $ordenesCobroRemesa[$fila]['CODSOCIO'];			 
											$arrCondicionesCuotaAnioSocio['CODSOCIO']['operador'] = '=';
											$arrCondicionesCuotaAnioSocio['CODSOCIO']['opUnir'] = 'AND';							
											//-- 2ª condicción -----------------
											$arrCondicionesCuotaAnioSocio['ANIOCUOTA']['valorCampo'] = $anioCuota;
											$arrCondicionesCuotaAnioSocio['ANIOCUOTA']['operador'] = '=';
											$arrCondicionesCuotaAnioSocio['ANIOCUOTA']['opUnir'] = ' ';	//el último no tiene operador para unir y se deja vacio
											
											$arrDatosActCuotaAnioSocio['ORDENARCOBROBANCO'] = 'NO';		
											$arrDatosActCuotaAnioSocio['FECHAPAGO'] = $fechaPagoBanco;

											$arrDatosActCuotaAnioSocio['FECHAANOTACION'] = date('Y-m-d');			

           //echo "<br><br>3-5-1 modeloTesorero:mActualizarCuotasCobradasEnRemesaTes:datosSocioActuales['datosFormCuotaSocio'][$anioCuota]['IMPORTECUOTAANIOPAGADA']['valorCampo']: ";print_r($datosSocioActuales['datosFormCuotaSocio'][$anioCuota]['IMPORTECUOTAANIOPAGADA']['valorCampo']);													
																																																																													
											$arrDatosActCuotaAnioSocio['IMPORTECUOTAANIOPAGADA'] = $datosSocioActuales['datosFormCuotaSocio'][$anioCuota]['IMPORTECUOTAANIOPAGADA']['valorCampo'] + 
																																																																		$ordenesCobroRemesa[$fila]['CUOTADONACIONPENDIENTEPAGO'];//CUOTADONACIONPENDIENTEPAGO es la cantidad que SE ORDENA COBRAR al banco
           //echo "<br><br>3-5-2 modeloTesorero:mActualizarCuotasCobradasEnRemesaTes:arrDatosActCuotaAnioSocio['IMPORTECUOTAANIOPAGADA']: ";print_r($arrDatosActCuotaAnioSocio['IMPORTECUOTAANIOPAGADA']);																																																																																																																																																									
											
											/*Donde ORDENES_COBRO.CUOTADONACIONPENDIENTEPAGO = CuotaDonacionPendienteCobro=(CUOTAANIOSOCIO.IMPORTECUOTAANIOSOCIO-CUOTAANIOSOCIO.IMPORTECUOTAANIOPAGADA)=cantidad que SE ORDENA COBRAR al banco
											 (en general CUOTAANIOSOCIO.IMPORTECUOTAANIOPAGADA=0, excepto en el caso de que CUOTAANIOSOCIO.ESTADOCUOTA='ABONADO_PARTE' pero no la cuota elegida completa)*/								       
											/*El siguiente if, lo pongo por si en el intervalo de tiempo hasta el cobro de la remesa, el socio o gestor hubiesen cambiado 
										 		el tipo de cuota del socio respecto a lo que teníamos en ORDENES_COBRO. Ejemplo	caso:  Joven y que pague 5 € en la orden de cobro de la remesa,
											 	pero en el intervalo se cambia a General 50 €, al abonarse la remesa y actualizar en ORDENES_COBRO se pondrá como 'ABONADA', pero en CUOTAANIOSOCIO se pondrá como 'ABONADA-PARTE'
											*/																																					
											if ($datosSocioActuales['datosFormCuotaSocio'][$anioCuota]['IMPORTECUOTAANIOEL']['valorCampo'] <= $arrDatosActCuotaAnioSocio['IMPORTECUOTAANIOPAGADA'])
											{												 											
											  $arrDatosActCuotaAnioSocio['ESTADOCUOTA'] = 'ABONADA';	//para la tabla 	CUOTAANIOSOCIO																			
											}
											else
											{ $arrDatosActCuotaAnioSocio['ESTADOCUOTA'] = 'ABONADA-PARTE';	//para la tabla 	CUOTAANIOSOCIO										
											}																																																				
	
											if (isset($arrDatosArchivoRemesa['IMPORTEGASTOSABONOCUOTA']) && $arrDatosArchivoRemesa['IMPORTEGASTOSABONOCUOTA']['valorCampo'] != 0.00)
											{
												$IMPORTEGASTOSABONOCUOTA = $arrDatosArchivoRemesa['IMPORTEGASTOSABONOCUOTA']['valorCampo']/$arrDatosArchivoRemesa['NUMRECIBOS']['valorCampo'];//CADA CUOTA			
												
												//Se sumarán a los gastos anteriores de ese año
												$arrDatosActCuotaAnioSocio['IMPORTEGASTOSABONOCUOTA'] = $datosSocioActuales['datosFormCuotaSocio'][$anioCuota]['IMPORTEGASTOSABONOCUOTA']['valorCampo']+ $IMPORTEGASTOSABONOCUOTA;						
											}
											
											$arrDatosActCuotaAnioSocio['MODOINGRESO'] = 'DOMICILIADA';
											$arrDatosActCuotaAnioSocio['CUENTAPAGO'] = $ordenesCobroRemesa[$fila]['CUENTAIBAN'];					      
											$arrDatosActCuotaAnioSocio['NOMARCHIVOSEPAXML'] = $nombreArchivoSEPAXML;

											$arrDatosActCuotaAnioSocio['OBSERVACIONES'] = addslashes($datosSocioActuales['datosFormCuotaSocio'][$anioCuota]['OBSERVACIONES']['valorCampo'].
											                                              ". ".$arrDatosArchivoRemesa['OBSERVACIONES']['valorCampo'].
																																																									". ".date('Y-m-d')." Efectuó un pago de ".$ordenesCobroRemesa[$fila]['CUOTADONACIONPENDIENTEPAGO'].
																																																									" euros  mediante la remesa ".$nombreArchivoSEPAXML.
																																																									"\nAnteriormente en el momento enviar la REMESA estaba como ".$ordenesCobroRemesa[$fila]['ESTADOCUOTA_ANTES_REMESA']. 
																																																									" habiendo pagado ".$ordenesCobroRemesa[$fila]['IMPORTECUOTAANIOPAGADA_ANTES_REMESA']. "euros".  
																																																									"\nAnteriormente en CUOTAANIOSOCIO estaba como ".$datosSocioActuales['datosFormCuotaSocio'][$anioCuota]['ESTADOCUOTA']['valorCampo'].																																																								
																																																									", ahora se ha anotado como ".$arrDatosActCuotaAnioSocio['ESTADOCUOTA']. //ok es en CUOTAANIOSOCIO
																																																									" en CUOTAANIOSOCIO. Cuenta pago: ".$ordenesCobroRemesa[$fila]['CUENTAIBAN'].
																																																									". Gestor CODUSER: ".$_SESSION['vs_CODUSER']."\n".$observacionesCambios
																																																									);																																																						
											//addslashes() escapa comillas como \"cuota especial\"		hay otros filtros para prohibir, o escapar caracteres especiales como htmlspecialchars(),filter_var(),etc.
	
											$actualizarCobroCuotaAnio = actualizarTabla('CUOTAANIOSOCIO',$arrCondicionesCuotaAnioSocio,$arrDatosActCuotaAnioSocio,$conexionDB['conexionLink']);//en modelos/BBDD/MySQL/modeloMySQL.php, error ok									
											//echo "<br><br>3-5-3 modeloTesorero:mActualizarCuotasCobradasEnRemesaTes:actualizarCobroCuotaAnio: ";print_r($actualizarCobroCuotaAnio);
											
											if ($actualizarCobroCuotaAnio['codError'] !== '00000')			
											{	$resActualizarCuotasCobradas['codError'] = $actualizarCobroCuotaAnio['codError'];
						       $resActualizarCuotasCobradas['errorMensaje'] = $actualizarCobroCuotaAnio['errorMensaje'];								
							  	   $textoErrores = "Error del sistema en modeloTesorero.php:mActualizarCuotasCobradasEnRemesaTes(), al actualizar la tabla CUOTAANIOSOCIO con la función actualizarTabla()";																
											}	
											elseif ($actualizarCobroCuotaAnio['numFilas'] <= 0)
											{ $resActualizarCuotasCobradas['codError'] ='80001'; 
													$resActualizarCuotasCobradas['errorMensaje'] = "Error lógico en modeloTesorero.php:mActualizarCuotasCobradasEnRemesaTes(): No se ha actualizado la fila correspondiente de la tabla CUOTAANIOSOCIO con la función actualizarTabla()";											  
							  	   $textoErrores = "Error en modeloTesorero.php:mActualizarCuotasCobradasEnRemesaTes(), no se ha actualizado la fila correspondiente en la tabla CUOTAANIOSOCIO con la función actualizarTabla()";
											}												
											else //$actualizarCobroCuotaAnio['codError']=='00000')			
											{ 
												//if ($actualizarCobroCuotaAnio['numFilas'] != 0)
												{ $contadorCuotasAnioSociosActualizados++;
												}									
												/*---------- Inicio actualizar tabla ORDENES_COBRO	------------------------------------*/												
												//1ª condición
												$arrayCondicionesOrdenesCobro['CODSOCIO']['valorCampo'] = $ordenesCobroRemesa[$fila]['CODSOCIO'];
												$arrayCondicionesOrdenesCobro['CODSOCIO']['operador'] = '=';
												$arrayCondicionesOrdenesCobro['CODSOCIO']['opUnir'] = 'AND'; 
												//2ª condición
												$arrayCondicionesOrdenesCobro['NOMARCHIVOSEPAXML']['valorCampo']  = $nombreArchivoSEPAXML; 
												$arrayCondicionesOrdenesCobro['NOMARCHIVOSEPAXML']['operador'] = '='; 
												$arrayCondicionesOrdenesCobro['NOMARCHIVOSEPAXML']['opUnir'] = ' ';
												//Datos a actualizar:
												
												$arrayDatosActOrdenesCobro['IMPORTECUOTAANIOPAGADA'] = $ordenesCobroRemesa[$fila]['CUOTADONACIONPENDIENTEPAGO'];//CUOTADONACIONPENDIENTEPAGO es la cantidad que SE ORDENA COBRAR al banco	
												//Donde ORDENES_COBRO.CUOTADONACIONPENDIENTEPAGO = CuotaDonacionPendienteCobro =(CUOTAANIOSOCIO.IMPORTECUOTAANIOSOCIO-CUOTAANIOSOCIO.IMPORTECUOTAANIOPAGADA)
											
												/*$arrayDatosActOrdenesCobro['IMPORTECUOTAANIOPAGADA'] = ORDENES_COBRO.IMPORTECUOTAANIOPAGADA es el importe pagado en la orden de cobro. 
						       ORDENES_COBRO.IMPORTECUOTAANIOPAGADA inicialmente tendrá el valor de 0€. Aquí en esta función después de “actualizarTabla('ORDENES_COBRO',...)” 
											  el campo pasará a tener el valor pagado en la remesa por el socio y que es ORDENES_COBRO.CUOTADONACIONPENDIENTEPAGO.
													En caso de posterior “Devolución” volverá a tener el valor 0€. ORDENES_COBRO.IMPORTECUOTAANIOPAGADA es el importe pagado en la orden de cobro, 
													en general coincidirá con CUOTAANIOSOCIO.IMPORTECUOTAANIOPAGADA, pero NO coincidirá si fuese el caso de ESTADOCUOTA=ABONADA-PARTE, 
													en ese caso ORDENES_COBRO.IMPORTECUOTAANIOPAGADA < CUOTAANIOSOCIO.IMPORTECUOTAANIOPAGADA. 		
												*/
											
												$arrayDatosActOrdenesCobro['ESTADOCUOTA'] = 'ABONADA';//antes PENDIENTE-COBRO, NOABONADA-DEVUELTA 
												$arrayDatosActOrdenesCobro['ANOTADO_EN_CUOTAANIOSOCIO'] = 'SI';									
												$arrayDatosActOrdenesCobro['FECHAPAGO'] = $fechaPagoBanco;	
												$arrayDatosActOrdenesCobro['FECHAANOTACION'] = date('Y-m-d');
												
												if (isset($arrDatosArchivoRemesa['IMPORTEGASTOSABONOCUOTA']['valorCampo']) && $arrDatosArchivoRemesa['IMPORTEGASTOSABONOCUOTA']['valorCampo'] != 0.00)
												{ 																		
													$arrayDatosActOrdenesCobro['IMPORTEGASTOSABONOCUOTA'] = $arrDatosArchivoRemesa['IMPORTEGASTOSABONOCUOTA']['valorCampo']/$arrDatosArchivoRemesa['NUMRECIBOS']['valorCampo'];//CADA CUOTA					
												}	
												$arrayDatosActOrdenesCobro['OBSERVACIONES'] = $ordenesCobroRemesa[$fila]['OBSERVACIONES'].". ".date('Y-m-d').$observacionesCambios. 
																																																										"<br />Actualizó las pagos de esta remesa en CUOTAANIOSOCIO. Gestor CODUSER: ".$_SESSION['vs_CODUSER'];																																																				

												$reActOrdenesCobro  = actualizarTabla('ORDENES_COBRO',$arrayCondicionesOrdenesCobro,$arrayDatosActOrdenesCobro,$conexionDB['conexionLink']);//en modelos/BBDD/MySQL/modeloMySQL.php, error ok		
												//echo "<br><br>3-6-1 modeloTesorero:mActualizarCuotasCobradasEnRemesaTes:reActOrdenesCobro : ";print_r($reActOrdenesCobro );

												/*---------- Fin actualizar tabla ORDENES_COBRO	------------------------------------------*/
												
												if ($reActOrdenesCobro['codError'] !== '00000')			
												{	$resActualizarCuotasCobradas['codError'] = $reActOrdenesCobro['codError'];
						        $resActualizarCuotasCobradas['errorMensaje'] = $reActOrdenesCobro['errorMensaje'];								
							  	    $textoErrores = "Error del sistema en modeloTesorero.php:mActualizarCuotasCobradasEnRemesaTes(), al actualizar la tabla ORDENES_COBRO con la función actualizarTabla()";														
												}
												elseif ($reActOrdenesCobro['numFilas'] <= 0)
												{ $resActualizarCuotasCobradas['codError'] = '80001';
						        $resActualizarCuotasCobradas['errorMensaje'] = "Error lógico en modeloTesorero.php:mActualizarCuotasCobradasEnRemesaTes(): No se ha actualizado la fila correspondiente de la tabla ORDENES_COBRO con la función actualizarTabla()";								
							  	    $textoErrores = "Error del sistema en modeloTesorero.php:mActualizarCuotasCobradasEnRemesaTes(),no se ha actualizado la fila correspondiente de la tabla ORDENES_COBRO con la función actualizarTabla()";																														
												}															
												else //$reActOrdenesCobro['codError']=='00000')					
												{
														$contadorOrdenesCobrosActualizados++;																						
												}									
											}//else $actualizarCobroCuotaAnio['codError']=='00000')											
									} //if ($resActualizarCuotasCobradas['codError'] =='00000')//2										
								}//if ($resActualizarCuotasCobradas['codError'] =='00000')//1
						}//else $reDatosSocioOrdenesCobro['codError']=='00000'
						
						$fila++;
			 	}//while ($fila < $ultimaF && $actualizarCobroCuotaAnio['codError'] =='00000')						
					/*--- Fin de bucle para actualizar CUOTAANIOSOCIO, SOCIO y ORDENES_COBRO ---------------------------------*/			
					
					if ($resActualizarCuotasCobradas['codError'] =='00000')			
					{									
							/*------ Inicio  actualizar tabla	REMESAS_SEPAXML  ------------------------------------------*/
														
							$arrayCondicionesOrdenesRemesa['NOMARCHIVOSEPAXML'] =  $nombreArchivoSEPAXML;								
							
  					$arrayDatosActRemesa['DIRECTORIOARCHIVOREMESA']['valorCampo'] = NULL;							
							$arrayDatosActRemesa['ANOTADO_EN_CUOTAANIOSOCIO']['valorCampo'] = 'SI';
							$arrayDatosActRemesa['FECHAPAGO']['valorCampo'] = $fechaPagoBanco;	
							$arrayDatosActRemesa['FECHAANOTACIONPAGO']['valorCampo'] = date('Y-m-d');
							$arrayDatosActRemesa['IMPORTEGASTOSREMESA']['valorCampo'] = $arrDatosArchivoRemesa['IMPORTEGASTOSABONOCUOTA']['valorCampo'];//EL TOTAL
							$arrayDatosActRemesa['OBSERVACIONES']['valorCampo'] = $arrDatosArchivoRemesa['OBSERVACIONES']['valorCampo'].". ".date('Y-m-d').
																																																							      ". Actualizó las pagos de esta remesa en CUOTAANIOSOCIO. Gestor CODUSER: ".$_SESSION['vs_CODUSER'].
																																																							      $listadoCambiosDatosSociosEnRemesaEnviadasTes;																																																							
       //echo "<br><br>4-1 modeloTesorero:mActualizarCuotasCobradasEnRemesaTes:arrayDatosActRemesa : ";print_r($arrayDatosActRemesa );		
							
							$reActRemesa = actualizarRemesa('REMESAS_SEPAXML',$arrayCondicionesOrdenesRemesa,$arrayDatosActRemesa,$conexionDB['conexionLink']);//en modeloTesorero.php, error ok
							//echo "<br><br>4-2 modeloTesorero:mActualizarCuotasCobradasEnRemesaTes:reActRemesa: ";print_r($reActRemesa );				

							/*------ Fin actualizar tabla	REMESAS_SEPAXML -----------------------------------------------*/
						
							if ($reActRemesa['codError']!=='00000')	//ya incluye caso de 	['numFilas'] <= 0;
							{	$resActualizarCuotasCobradas['codError'] = $reActRemesa['codError'];
						   $resActualizarCuotasCobradas['errorMensaje'] = $reActRemesa['errorMensaje'];								
							  $textoErrores = "Error del sistema en modeloTesorero.php:mActualizarCuotasCobradasEnRemesaTes(), al actualizar la tabla REMESAS_SEPAXML con la función actualizarRemesa()";				
							}									
							else //$reActRemesa['codError'] =='00000'
							{ 							
	       /*--------- Inicio eliminar archivo de orden de cobro del servidor ---------
								También hay que eliminar el	archivo de orden de cobro de esa remesa SEPA XML
								generado y subido al servidor	dispnible para descagar y llevar a web del 
								banco para su cobro, nombre similar SEPA_ISO20022CORE_2021_03_03H08_43_36.xml)	
								----------------------------------------------------------------------------*/
								$cadEliminarArchivo = '';
													
								$resEliminarArchivo = eliminarArchivo($dirSEPAXML,$nombreArchivoSEPAXML);//en modeloArchivos.php, no necesita acceder a la BBDD, probado error ok 

							 //echo "<br><br>5-1 modeloTesorero:mActualizarCuotasCobradasEnRemesaTes:resEliminarArchivo: ";print_r($resEliminarArchivo);
								
								/*-- Decido no controlar el error en la función "eliminarArchivo()", porque creo que es más importante y necesario
									poder ejecutar la actualización de las órdenes de cobro de esta remesa y que se actualicen las correspondientes tablas 
									con los datos de los pagos efectuados:  'CUOTAANIOSOCIO', 'SOCIO', 'ORDENES_COBRO' y 'REMESAS_SEPAXML'. 
									Pudiera ser que por error se hubiese borrado o modificado el nombre manualmente en el servidor el
									archivo "SEPA_ISO20022CORE-fcha.xml" correspondiente a esa remesa. Es más importante actualizar las tablas.
									Se mantiene y envía el mensaje de error de eliminarArchivo en "$cadEliminarArchivo" si le hubiese, pero no se hace rollback, 
									con lo cual se mantiene la actualización de las tablas.
							 */
        /* Nota: Lo dejo comentado por si decido activar el control de error más adelante				
								if ($resEliminarArchivo['codError'] !== '00000') //dejo las dos opciones por si quiero anteponer algún otro comentario
								{
									 $resActualizarCuotasCobradas  = $resEliminarArchivo;
										$cadEliminarArchivo = "<br /><br />".$resEliminarArchivo['arrMensaje']['textoComentarios'];												
										$textoErrores = $resEliminarArchivo['errorMensaje']." al actualizar eliminar el archivo ".$nombreArchivoSEPAXML.$resEliminarArchivo['codError'];										
								}
								else //$resEliminarArchivo['codError'] =='00000'
								*/						
								{ 
										$cadEliminarArchivo = "<br /><br />".$resEliminarArchivo['arrMensaje']['textoComentarios'];			
									
									//----------- Fin eliminar archivo firma socio del servidor ----------------								
					
									//----------------Inicio COMMIT ------------------------------------
									$resFinTrans = commitPDO($conexionDB['conexionLink']);
									
									if ($resFinTrans['codError'] !== '00000')//sera $resFinTrans['codError'] = '70502';		
									{$resFinTrans['errorMensaje'] = 'Error en el sistema, no se han podido finalizar transación del socio/a. ';
										$resFinTrans['numFilas'] = 0;	
									
										$resActualizarCuotasCobradas = $resFinTrans;	
											//echo "<br><br>6-1 modeloTesorero:mActualizarCuotasCobradasEnRemesaTes:resActualizarCuotasCobradas:";print_r($resActualizarCuotasCobradas);
									}												
									else //resFinTrans['codError'] == '00000'
									{
										$arrMensaje['textoComentarios']="Se ha efectuado el proceso de actualizar las cuotas cobradas por domiciliación correspondientes a la remesa con ".
										                                "<strong>fecha de orden cobro: ".$fechaOrdenCobroArchivoRemesa."</strong>, y nombre del archivo: <strong>".
																																											$nombreArchivoSEPAXML." que fue creada con fecha: ".$fechaCreacionArchivoRemesa. 
																																										"</strong><br /><br />Se han anotado ".$contadorCuotasAnioSociosActualizados." pagos de socios/as en la tabla CUOTAANIOSOCIO<br /><br />".
																																										" En la tabla ORDENES_COBRO se han actualizado ".$contadorOrdenesCobrosActualizados." filas <br /><br />".
																																										" En la tabla REMESAS_SEPAXML se han actualizado ".$reActRemesa['numFilas']." filas <br /><br />".
																																										" En la tabla SOCIO se han actualizado FRST->RCUR, (en el número se descuentan las bajas, y cambios de CUENTAIBAN): ".
																																										$contadorSociosCobrosActualizados." filas<br /><br />".$cadEliminarArchivo;
           //echo "<br><br>6-2 modeloTesorero:mActualizarCuotasCobradasEnRemesaTes:listadoCambiosDatosSociosEnRemesaEnviadasTes: ";print_r($listadoCambiosDatosSociosEnRemesaEnviadasTes); 
																																											
										//$arrMensaje['textoComentarios'] .= $listadoCambiosDatosSociosEnRemesaEnviadasTes;	
										if (isset($listadoCambiosDatosSociosEnRemesaEnviadasTes) && !empty($listadoCambiosDatosSociosEnRemesaEnviadasTes) )
										{ $arrMensaje['textoComentarios'] .= $listadoCambiosDatosSociosEnRemesaEnviadasTes;			
										}									
										$resActualizarCuotasCobradas['arrMensaje']['textoComentarios'] = $arrMensaje['textoComentarios'];
          //echo "<br><br>6-3 modeloTesorero:mActualizarCuotasCobradasEnRemesaTes:resActualizarCuotasCobradas: ";print_r($resActualizarCuotasCobradas); 	
										
									}//else resFinTrans['codError'] == '00000'
							 }//else $resEliminarArchivo['codError'] == '00000'
							}//else $reActRemesa['codError'] == '00000'
		   }//else $resActualizarCuotasCobradas['codError'] == '00000'				
				}//else $arrOrdenesCobroRemesa['codError'] == '00000'
    
				//echo "<br><br>7 modeloTesorero:mActualizarCuotasCobradasEnRemesaTes:resActualizarCuotasCobradas: ";print_r($resActualizarCuotasCobradas);
				
				/*----------------------- Inicio tratamiento de errores --------------------------------------*/
				if ($resActualizarCuotasCobradas['codError'] !== '00000')
				{				
					$resDeshacerTrans = rollbackPDO($conexionDB['conexionLink']);
							
					if ($resDeshacerTrans['codError'] !== '00000')//sera $resDeshacerTrans['codError'] = '70503';
					{ $resDeshacerTrans['errorMensaje'] = 'Error en el sistema, no se ha podido deshacer la transación. ';
							$resDeshacerTrans['numFilas'] = 0;	
							$resActualizarCuotasCobradas = $resDeshacerTrans;
					}	
					$arrMensaje['textoComentarios'] = "Información del error al actualizar las órdenes de cobro de cuotas domiciliadas correspondientes a esta remesa: <br /><br /> ".
																																							$textoErrores.". Gestor CODUSER: ".$_SESSION['vs_CODUSER']." Función: ".
																																							$resActualizarCuotasCobradas['nomScript'].": ".$resActualizarCuotasCobradas['nomFuncion'];
																																									
					$arrInsertarErrores = $resActualizarCuotasCobradas;		
					$arrInsertarErrores['textoComentarios'] =  $textoErrores." Función: ".$resActualizarCuotasCobradas['nomScript'].": ".$resActualizarCuotasCobradas['nomFuncion'].
					                                          ". Gestor CODUSER: ".$_SESSION['vs_CODUSER'];																																							
					
					$resActualizarCuotasCobradas['arrMensaje']['textoComentarios'] = $arrMensaje['textoComentarios'];						
				
					$resActualizarCuotasErrores = insertarError(	$arrInsertarErrores,$conexionDB['conexionLink']);//modeloErrores.php			
				
					if ($resActualizarCuotasErrores['codError'] !== '00000')
					{ $resActualizarCuotasCobradas['errorMensaje'] .= $resActualizarCuotasErrores['errorMensaje'];
							$arrMensaje['textoComentarios'] .= "Error del sistema al tratar ERRORES, vuelva a intentarlo pasado un tiempo ";
					}		
				}//if ($resActualizarCuotasCobradas['codError']!=='00000')	
				/*----------------------- Fin tratamiento de errores -----------------------------------------*/
		}//else $resIniTrans['codError']	
	}//else $conexionDB['codError']=="00000"

 //echo "<br>8 modeloTesorero:mActualizarCuotasCobradasEnRemesaTes:resActualizarCuotasCobradas: "; print_r($resActualizarCuotasCobradas); 
 
	return $resActualizarCuotasCobradas;
}
/*----------------------------- Fin mActualizarCuotasCobradasEnRemesaTes --------------------------*/

/*------------------- Inicio cambiosDatosSociosEnRemesaEnviadasTes() -------------------------------
En el intervalo de tiempo desde de la generación del archivo remesa y envíado al banco de las órdenes 
de cobro, puede suceder que algunos socios o gestores modifique datos, por ejemplo: se dan de baja, 
cambian IBAN, pagan por otros medios (PayPal), modificando su cuota, etc. Esto genera diferencias 
entre valores de algunos campos que contiene la remesa y los existentes en el momento en la BBDD.

Aquí se buscan la existencia de esas posibles diferencias y si se hubiesen producido, genera unos string, 
con el datos de las variaciones en ciertos campos que se han podido producir y enviará a la función
modeloTesorero.php:mActualizarCuotasCobradasEnRemesaTes(), para mostrar en pantalla y anotar en los campos
[OBSERVACIONES] en las tablas correspondientes de la BBDD, y para que Tesorería  resuelva las diferncias 
producidas respecto a los datos guardados en la tabla "ORDENES_COBRO", que se está utilizando ahora 
en el proceso de actualizar el pago en los datos de la tablas CUOTAANIOSOCIO, SOCIO, y REMESAS_SEPAXML.

Se llamará tantas veces como órdenes de cobro de socios existan en la remesa, dentro de un bucle
"while ($fila < $ultimaF && $resActualizarCuotasCobradas['codError'] =='00000')"
					
RECIBE: "$anioCuota" (el año de la cuota que se está cobrando en esa remesa), 
        "$datosSocioActuales" array con los datos actuales en la tablas "CUOTAANIOSOCIO, SOCIO, MIEMBRO"
								de cada socio 	existente en tabla "ORDENES_COBRO" en la remesa enviada y que ahora se quiere 
								anotar y actualizar,	
								"$ordenCobroRemesaSocio" array con los datos en la tabla "ORDENES_COBRO" de la orden de cobro
								de cada socio en esa remesa 
								
DEVUELVE: "$arrCambiosDatosSociosEnRemesa" array con los dos siguientes valores:
          "[listaCambiosDatosSociosEnRemesa]" es una cadena que acumula todas las diferencias producidas
          con respecto a los datos en las órdenes de cobro de esa Remesa. Esta organizada en forma de 
										filas numeradas conteniendo el nombre del socio, e información respecto a diferencias encontradas.		
										"[$observacionCambiosDatosSociosEnRemesa]": cadena que guarda las diferencias encontradas para un 
										solo socio, para anotar en el campos [OBSERVACIONES] de tablas "CUOTAANIOSOCIO" y "ORDENES_COBRO"
										
LLAMADA: modeloTesorero.php:mActualizarCuotasCobradasEnRemesaTes()										

OBSERVACIONES:	2021-03-14: Función probada con PHP 7.3.21
--------------------------------------------------------------------------------------------------*/
//Llamada = cambiosDatosSociosEnRemesaEnviadasTes($anioCuota,$datosSocioActuales,$ordenesCobroRemesa[$fila],$arrBuscarMiembroEliminado);
function cambiosDatosSociosEnRemesaEnviadasTes($anioCuota,$datosSocioActuales,$ordenCobroRemesaSocio,$arrBuscarMiembroEliminado=NULL)
{
	//echo "<br><br>0-1 modeloTesorero:cambiosDatosSociosEnRemesaEnviadasTes:datosSocioOrdenesCobro: ";print_r($datosSocioActuales); 
	//echo "<br><br>0-2 modeloTesorero:cambiosDatosSociosEnRemesaEnviadasTes:ordenCobroRemesaSocio: ";print_r($ordenCobroRemesaSocio); 
	//echo "<br><br>0-3 modeloTesorero:cambiosDatosSociosEnRemesaEnviadasTes:arrBuscarMiembroEliminado: ";print_r($arrBuscarMiembroEliminado); 

	$arrCambiosDatosSociosEnRemesa = array();

	static $contadorCambiosEnRemesa = 0;	

	static $textoListadoBajas = '';
	static $textoListadoAbonada = '';
	static $textoListadoCambiosIBAN = '';
	static $textoListadoCambiosCuota = '';
	static $textoListadoCambiosCodCuota = '';
	static $textoListadoCambiosAgrup = '';

	//	Nota: se podría hacer con arrays	static para ir acumulando	y unir al final	

	//-------- Inicio texto para cada socio de remesa que ha sido baja ------------------------	
	$observacionesSocioBajas	= "";

	if ($datosSocioActuales['datosFormUsuario']['ESTADO']['valorCampo'] == 'baja')
	{											
		if (isset($arrBuscarMiembroEliminado['APE2']) && !empty($arrBuscarMiembroEliminado['APE2']))
		{ $apesNomBaja = $arrBuscarMiembroEliminado['APE1']." ".$arrBuscarMiembroEliminado['APE2'].", ".$arrBuscarMiembroEliminado['NOM'];
		}
		else
		{ $apesNomBaja = $arrBuscarMiembroEliminado['APE1'].", ".$arrBuscarMiembroEliminado['NOM'];
		}
		$contadorCambiosEnRemesa++;
		
		$datosCambiosBajas =	", FECHA BAJA: ".$datosSocioActuales['datosFormSocio']['FECHABAJA']['valorCampo'].
																							". Cuota elegida en la remesa: ".$ordenCobroRemesaSocio['IMPORTECUOTAANIOSOCIO']." euros".	
																							". Cuota pagada en la remesa: ".$ordenCobroRemesaSocio['CUOTADONACIONPENDIENTEPAGO']." euros";
				
		$textoListadoBajas .= "<br />".$contadorCambiosEnRemesa."- Nombre: ".$apesNomBaja.$datosCambiosBajas;					

		//echo "<br><br>2-1 modeloTesorero:cambiosDatosSociosEnRemesaEnviadasTes:textoListadoBajas: ";print_r($textoListadoBajas);	
																							
		$observacionesSocioBajas	= ". AVISO: ".$apesNomBaja.$datosCambiosBajas.". Para ver más datos de este socio/a busca en el archivo EXCEL, generado el día de fecha de creación remesa ";																						

	}//if ($datosSocioActuales['datosFormUsuario']['ESTADO']['valorCampo'] == 'baja')													

	//-------- Fin texto para para cada socio de remesa que ha sido baja --------------------------	

	//---------- Inicio buscar si hay cuotas ABONADA  ABONADA-PARTE por otros medios --------------
	/* En la tabla ORDENES_COBRO el campo ESTADOCUOTA solo puede tener los valores PENDIENTE-COBRO o ABONADA-PARTE,	por lo que si ahora 
				en la tabla CUOTAANIOSOCIO tiene ESTADOCUOTA = ABONADA, siempre será un nuevo pago efectuado después de haber enviado la remesa. 			
				Si en la tabla CUOTAANIOSOCIO tiene ESTADOCUOTA = ABONADA-PARTE, "solo" será un nuevo pago efectuadao después de haber enviado la remesa 
				si en la tabla ORDENES_COBRO la cuota de ese socio ESTADOCUOTA !=='ABONADA-PARTE', (o sea ORDENES_COBRO,  ESTADOCUOTA=PENDIENTE-COBRO) 
				//OJO** Aquí ['ESTADOCUOTA'] !== 'ABONADA-PARTE' en "$datosSocioActuales" viene de la tabla "CUOTAANIOSOCIO" 
				y en "$ordenCobroRemesaSocio" viene de tabla "ORDENES_COBRO"
	*/
	$observacionesSocioAbonada	= "";
	
	if ($datosSocioActuales['datosFormCuotaSocio'][$anioCuota]['ESTADOCUOTA']['valorCampo'] == 'ABONADA' || 	
					($datosSocioActuales['datosFormCuotaSocio'][$anioCuota]['ESTADOCUOTA']['valorCampo'] == 'ABONADA-PARTE' && $ordenCobroRemesaSocio['ESTADOCUOTA_ANTES_REMESA'] !== 'ABONADA-PARTE')	
				)			
	{	if ($datosSocioActuales['datosFormUsuario']['ESTADO']['valorCampo'] == 'baja')
			{ if (isset($arrBuscarMiembroEliminado['APE2']) && !empty($arrBuscarMiembroEliminado['APE2']))
					{ $apesNomAbonada = $arrBuscarMiembroEliminado['APE1']." ".$arrBuscarMiembroEliminado['APE2'].", ".$arrBuscarMiembroEliminado['NOM'];
					}
					else
					{ $apesNomAbonada = $arrBuscarMiembroEliminado['APE1'].", ".$arrBuscarMiembroEliminado['NOM'];
					}												
			}										
			else
			{	if (isset($datosSocioActuales['datosFormMiembro']['APE2']['valorCampo']) && !empty($datosSocioActuales['datosFormMiembro']['APE2']['valorCampo']))
					{ $apesNomAbonada = $datosSocioActuales['datosFormMiembro']['APE1']['valorCampo']." ".$datosSocioActuales['datosFormMiembro']['APE2']['valorCampo'].", ".
							$datosSocioActuales['datosFormMiembro']['NOM']['valorCampo'];
					}
					else
					{ $apesNomAbonada = $datosSocioActuales['datosFormMiembro']['APE1']['valorCampo'].", ".$datosSocioActuales['datosFormMiembro']['NOM']['valorCampo'];
					}
			}
			$contadorCambiosEnRemesa++;

			$datosCambiosAbonada = ". Cuota elegida en la remesa: ".$ordenCobroRemesaSocio['IMPORTECUOTAANIOSOCIO']." euros".
																										". Cuota pagada en la remesa: ".$ordenCobroRemesaSocio['CUOTADONACIONPENDIENTEPAGO']." euros".
																										". Cuota ABONADA o ABONADA-PARTE en CUOTAANIOSOCIO: ".$datosSocioActuales['datosFormCuotaSocio'][$anioCuota]['IMPORTECUOTAANIOPAGADA']['valorCampo']." euros";

			$textoListadoAbonada .= "<br />".$contadorCambiosEnRemesa."- Nombre: ".$apesNomAbonada.$datosCambiosAbonada.
																											".  Agrupación actual: ".$datosSocioActuales['datosFormSocio']['NOMAGRUPACION']['valorCampo'];

			//echo "<br><br>2-2 modeloTesorero:cambiosDatosSociosEnRemesaEnviadasTes:textoListadoAbonada: ";print_r($textoListadoAbonada);
			
			$observacionesSocioAbonada	= ". AVISO: CONTACTAR CON SOCIO/A ".$apesNomAbonada.", ABONO DE CUOTA POR OTROS MEDIOS EN EL INTERVALO DE ESPERA DE COBRO DE LA REMESA. ".$datosCambiosAbonada;
	}
	//---------- Fin buscar si hay cuotas ABONADA o ABONADA-PARTE por otros medios ------------

	//---------- Inicio  LISTA CAMBIOS IBAN ---------------------------------------------------
	// Mostrará los casos en que haya cambiado o Eliminado el IBAN, incluidos los casos de cambio por baja de socio 

	$observacionesSocioCambiosIBAN	= "";					

	if ($ordenCobroRemesaSocio['CUENTAIBAN'] !== $datosSocioActuales['datosFormSocio']['CUENTAIBAN']['valorCampo'])						
	{ 		
			if ($datosSocioActuales['datosFormUsuario']['ESTADO']['valorCampo'] == 'baja')
			{ if (isset($arrBuscarMiembroEliminado['APE2']) && !empty($arrBuscarMiembroEliminado['APE2']))
					{ $apesNomIBAN = $arrBuscarMiembroEliminado['APE1']." ".$arrBuscarMiembroEliminado['APE2'].", ".$arrBuscarMiembroEliminado['NOM'];
					}
					else
					{ $apesNomIBAN = $arrBuscarMiembroEliminado['APE1'].", ".$arrBuscarMiembroEliminado['NOM'];
					}	
					$textoNuevoIBAN = ". IBAN NUEVA: ELIMINADA POR BAJA SOCIO/0";													
			}
			else //!==baja
			{	if (isset($datosSocioActuales['datosFormMiembro']['APE2']['valorCampo']) && !empty($datosSocioActuales['datosFormMiembro']['APE2']['valorCampo']))
					{ $apesNomIBAN = $datosSocioActuales['datosFormMiembro']['APE1']['valorCampo']." ".$datosSocioActuales['datosFormMiembro']['APE2']['valorCampo'].", ".
							$datosSocioActuales['datosFormMiembro']['NOM']['valorCampo'];
					}
					else
					{ $apesNomIBAN = $datosSocioActuales['datosFormMiembro']['APE1']['valorCampo'].", ".$datosSocioActuales['datosFormMiembro']['NOM']['valorCampo'];
					}																							  
					
					if (isset($datosSocioActuales['datosFormSocio']['CUENTAIBAN']['valorCampo']) && !empty($datosSocioActuales['datosFormSocio']['CUENTAIBAN']['valorCampo']))//Cambiado IBAN
					{	$textoNuevoIBAN = ". IBAN NUEVA: ".$datosSocioActuales['datosFormSocio']['CUENTAIBAN']['valorCampo'];	
					}
					else//if ($datosSocioActuales['datosFormUsuario']['ESTADO']['valorCampo'] !== 'baja')//=eliminado IBAN	
					{ 
							$textoNuevoIBAN = ". IBAN NUEVA: ELIMINADA POR SOCIO/A O GESTOR/A";	
					}
			}	
			$contadorCambiosEnRemesa++;		

			$datosCambiosIBAN  =	". IBAN en la remesa: ".$ordenCobroRemesaSocio['CUENTAIBAN'].$textoNuevoIBAN;		

			$textoListadoCambiosIBAN .= "<br />".$contadorCambiosEnRemesa."- Nombre: ".$apesNomIBAN.$datosCambiosIBAN.		
																															".  Agrupación actual: ".$datosSocioActuales['datosFormSocio']['NOMAGRUPACION']['valorCampo'];							

			//echo "<br><br>2-3 modeloTesorero:cambiosDatosSociosEnRemesaEnviadasTes:textoListadoCambiosIBAN: ";print_r($textoListadoCambiosIBAN);
		
			$observacionesSocioCambiosIBAN	= ". AVISO: CONTROLAR POSIBLE DEVOLUCIÓN ".$apesNomIBAN.", CAMBIO de cuenta IBAN SOCIO EN EL INTERVALO DE ESPERA DE COBRO DE LA REMESA. ".$datosCambiosIBAN;

	}//if ($ordenCobroRemesaSocio['CUENTAIBAN'] !== $datosSocioActuales['datosFormSocio']['CUENTAIBAN']['valorCampo'])		

	//---------- Fin  LISTA CAMBIOS IBAN ------------------------------------------------------	

	//---------- Inicio buscar si hay Cambios en IMPORTECUOTAANIOSOCIO ------------------------
	// Mostrará si ha disminuido o aumentado la cuota elegida por el socio	en el intervalo

	$observacionesSocioCambiosCuota = "";

	if ($datosSocioActuales['datosFormCuotaSocio'][$anioCuota]['IMPORTECUOTAANIOSOCIO']['valorCampo'] !== $ordenCobroRemesaSocio['IMPORTECUOTAANIOSOCIO'])			
	{											
			if (isset($datosSocioActuales['datosFormMiembro']['APE2']['valorCampo']) && !empty($datosSocioActuales['datosFormMiembro']['APE2']['valorCampo']))
			{ $apesNomCambioCuota = $datosSocioActuales['datosFormMiembro']['APE1']['valorCampo']." ".$datosSocioActuales['datosFormMiembro']['APE2']['valorCampo'].", ".
					$datosSocioActuales['datosFormMiembro']['NOM']['valorCampo'];
			}
			else
			{ $apesNomCambioCuota = $datosSocioActuales['datosFormMiembro']['APE1']['valorCampo'].", ".$datosSocioActuales['datosFormMiembro']['NOM']['valorCampo'];
			}

			if ($datosSocioActuales['datosFormCuotaSocio'][$anioCuota]['IMPORTECUOTAANIOSOCIO']['valorCampo'] < $ordenCobroRemesaSocio['IMPORTECUOTAANIOSOCIO'])//CAMBIO NOMBRE???		
			{	$textoCambiosCuota =	". AVISO: EL SOCIO/A O UN GESTOR/A HA DISMINUIDO LA CUOTA EN EL INTERVALO DE ESPERA DE COBRO DE LA REMESA. ";																											
			}
			elseif ($datosSocioActuales['datosFormCuotaSocio'][$anioCuota]['IMPORTECUOTAANIOSOCIO']['valorCampo'] > $ordenCobroRemesaSocio['IMPORTECUOTAANIOSOCIO'])//CAMBIO NOMBRE???		
			{										
					$textoCambiosCuota =	". AVISO: EL SOCIO/A O UN GESTOR/A HA AUMENTADO LA CUOTA EN EL INTERVALO DE ESPERA DE COBRO DE LA REMESA. ";																														
			}
			$contadorCambiosEnRemesa++;

			$datosCambiosCuota  =	". Cuota elegida en la remesa: ".$ordenCobroRemesaSocio['IMPORTECUOTAANIOSOCIO']." euros".
																									". Cuota elegida cambiada: ".$datosSocioActuales['datosFormCuotaSocio'][$anioCuota]['IMPORTECUOTAANIOSOCIO']['valorCampo']." euros ".
																									". Cuota pagada en la remesa: ".$ordenCobroRemesaSocio['CUOTADONACIONPENDIENTEPAGO']." euros";			

			$textoListadoCambiosCuota .= "<br />".$contadorCambiosEnRemesa."- Nombre: ".$apesNomCambioCuota.$datosCambiosCuota.		
																																". Agrupación actual: ".$datosSocioActuales['datosFormSocio']['NOMAGRUPACION']['valorCampo'];

			//echo "<br><br>2-4 modeloTesorero:cambiosDatosSociosEnRemesaEnviadasTes:textoListadoCambiosCuota: ";print_r($textoListadoCambiosCuota);
																							
			$observacionesSocioCambiosCuota	= $textoCambiosCuota.$apesNomCambioCuota.$datosCambiosCuota;		
	}   										
	//---------- Fin buscar si hayCambios en IMPORTECUOTAANIOSOCIO ----------------------------

	//---------- Inicio buscar si hay Cambios en 	CODCUOTA ------------------------------------
	// Mostrará si ha cambiado el tipo de cuota del socio	en el intervalo

	$observacionesSocioCambiosCodCuota = "";

	if ($datosSocioActuales['datosFormCuotaSocio'][$anioCuota]['CODCUOTA']['valorCampo'] !== $ordenCobroRemesaSocio['CODCUOTA'])
	{
			if (isset($datosSocioActuales['datosFormMiembro']['APE2']['valorCampo']) && !empty($datosSocioActuales['datosFormMiembro']['APE2']['valorCampo']))
			{ $apesNomCambioCodCuota = $datosSocioActuales['datosFormMiembro']['APE1']['valorCampo']." ".$datosSocioActuales['datosFormMiembro']['APE2']['valorCampo'].", ".
					$datosSocioActuales['datosFormMiembro']['NOM']['valorCampo'];
			}
			else
			{ $apesNomCambioCodCuota = $datosSocioActuales['datosFormMiembro']['APE1']['valorCampo'].", ".$datosSocioActuales['datosFormMiembro']['NOM']['valorCampo'];
			}
			$contadorCambiosEnRemesa++;
			
			$datosCambiosCodCuota  = ". Tipo de cuota elegida actual cambiada: ".$datosSocioActuales['datosFormSocio']['CODCUOTA']['valorCampo'].	
																												". Cuota nuevo tipo EL actual cambiada: ".$datosSocioActuales['datosFormSocio']['IMPORTECUOTAANIOEL']['valorCampo'].	
																												". Cuota nueva elegida en la remesa: ".$datosSocioActuales['datosFormCuotaSocio'][$anioCuota]['IMPORTECUOTAANIOSOCIO']['valorCampo'].
																												". Tipo de cuota elegida en la remesa: ".$ordenCobroRemesaSocio['CODCUOTA'].				
																												". Cuota tipo EL en la remesa: ".$ordenCobroRemesaSocio['IMPORTECUOTAANIOEL'].																															
																												". Cuota elegida en la remesa: ".$ordenCobroRemesaSocio['IMPORTECUOTAANIOSOCIO']." euros".																											
																												". Cuota pagada en la remesa: ".$ordenCobroRemesaSocio['CUOTADONACIONPENDIENTEPAGO']." euros";		

			$textoListadoCambiosCodCuota .= "<br />".$contadorCambiosEnRemesa."- Nombre: ".$apesNomCambioCodCuota.$datosCambiosCodCuota.		
																																			". Agrupación actual: ".$datosSocioActuales['datosFormSocio']['NOMAGRUPACION']['valorCampo'];			

			//echo "<br><br>2-5 modeloTesorero:cambiosDatosSociosEnRemesaEnviadasTes:textoListadoCambiosCodCuota: ";print_r($textoListadoCambiosCodCuota);

			$textoCambiosCodCuota =	". AVISO: EL SOCIO/A O UN GESTOR/A HA CAMBIADO DE TIPO DE CUOTA EN EL INTERVALO DE ESPERA DE COBRO DE LA REMESA. ";																																													
			$observacionesSocioCambiosCodCuota	= $textoCambiosCodCuota.$apesNomCambioCodCuota.$datosCambiosCodCuota;											
	}   										
	//---------- Fin buscar si hayCambios en 	CODACUOTA --------------------------------------

	//---------- Inicio buscar si hay Cambios en 	CODAGRUPACION-------------------------------
	// Mostrará si ha cambiado la agrupación del socio	en el intervalo

	$observacionesSocioCambiosAgrup = "";

	if ($datosSocioActuales['datosFormCuotaSocio'][$anioCuota]['CODAGRUPACION']['valorCampo'] !== $ordenCobroRemesaSocio['CODAGRUPACION'])	
	{ 
			if ($datosSocioActuales['datosFormUsuario']['ESTADO']['valorCampo'] !== 'baja')//si es baja da igual la agrupación y no se muestra
			{ 
					if (isset($datosSocioActuales['datosFormMiembro']['APE2']['valorCampo']) && !empty($datosSocioActuales['datosFormMiembro']['APE2']['valorCampo']))
					{ $apesNomCambioAgrup = $datosSocioActuales['datosFormMiembro']['APE1']['valorCampo']." ".$datosSocioActuales['datosFormMiembro']['APE2']['valorCampo'].", ".
							$datosSocioActuales['datosFormMiembro']['NOM']['valorCampo'];
					}
					else
					{ $apesNomCambioAgrup = $datosSocioActuales['datosFormMiembro']['APE1']['valorCampo'].", ".$datosSocioActuales['datosFormMiembro']['NOM']['valorCampo'];
					}
					$contadorCambiosEnRemesa++;

					$datosCambiosAgrup  =	". Agrupación en la remesa: ".$ordenCobroRemesaSocio['NOMAGRUPACION'].
																											". Agrupación actual cambiada: ".$datosSocioActuales['datosFormSocio']['NOMAGRUPACION']['valorCampo'];				

					$textoListadoCambiosAgrup .= "<br />".$contadorCambiosEnRemesa."- Nombre: ".$apesNomCambioAgrup.$datosCambiosAgrup;				

					//echo "<br><br>2-6 modeloTesorero:mActualizarCuotasCobradasEnRemesaTes:textoListadoCambiosAgrup: ";print_r($textoListadoCambiosAgrup);				
																									
					$textoCambiosAgrupación =	". AVISO: EL SOCIO/A O UN GESTOR/A HA CAMBIADO DE AGRUPACIÓN EN EL INTERVALO DE ESPERA DE COBRO DE LA REMESA. ";													
					$observacionesSocioCambiosAgrup	= $textoCambiosAgrupación.$apesNomCambioAgrup.$datosCambiosAgrup;		
			}   			
	}	
	//---------- Fin buscar si hay Cambios en 	CODAGRUPACION -------------------------------------
	
	//echo "<br><br>3 modeloTesorero:cambiosDatosSociosEnRemesaEnviadasTes:contadorCambiosEnRemesa: ";print_r($contadorCambiosEnRemesa); 	
		
	//--- $observacionCambiosDatosSociosEnRemesa: para guardar en 'CUOTAANIOSOCIO' y 'ORDENES_COBRO' en el campo ['OBSERVACIONES']
	$observacionCambiosDatosSociosEnRemesa = $observacionesSocioBajas.$observacionesSocioAbonada.$observacionesSocioCambiosIBAN.$observacionesSocioCambiosCuota.
																																										$observacionesSocioCambiosCodCuota.$observacionesSocioCambiosAgrup;  
	
	//--- $listaCambiosDatosSociosEnRemesa: para mostrar en pantalla, guardar en 'REMESAS_SEPAXML' en campo ['OBSERVACIONES'] y enviar por email a tesorero.	
	$listaCambiosDatosSociosEnRemesa = $textoListadoBajas.$textoListadoAbonada.$textoListadoCambiosIBAN.$textoListadoCambiosCuota.$textoListadoCambiosCodCuota.$textoListadoCambiosAgrup;
	
	$arrCambiosDatosSociosEnRemesa['observacionCambiosDatosSociosEnRemesa'] =  $observacionCambiosDatosSociosEnRemesa;
	$arrCambiosDatosSociosEnRemesa['listaCambiosDatosSociosEnRemesa'] =  $listaCambiosDatosSociosEnRemesa;

	//echo "<br><br>4 modeloTesorero:cambiosDatosSociosEnRemesaEnviadasTes:arrCambiosDatosSociosEnRemesa: ";print_r($arrCambiosDatosSociosEnRemesa); 				

	return $arrCambiosDatosSociosEnRemesa;
}
/*------------------- Fin cambiosDatosSociosEnRemesaEnviadasTes() --------------------------------*/

/*----------------------------- Inicio actualizarRemesa --------------------------------------------
En esta función se actualiza la tabla REMESAS_SEPAXML, a partir de los campos de condiciones
en nuestro caso en principio serán NOMARCHIVOSEPAXML 						  

RECIBE: un array con los campos de los datos a actualizar ya validados y el array condiciones
DEVUELVEe: un array con los controles de errores. 

LLAMADA: modeloTesorero.php:mActualizarCuotasCobradasEnRemesaTes()
LLAMA: modeloMySQL.php:actualizarTabla()

OBSERVACIONES: 
2020-11-11: Probada PHP 7.3.21 
Similar a modeloSocios:actualizSocio()          
-------------------------------------------------------------------------------------------------*/       
function actualizarRemesa($tablaAct,$arrayCondicionesOrdenesRemesa,$arrayDatosAct,$conexionLinkDB)	
{//echo '<br><br>1-1 modeloTesorero:actualizarRemesa:arrayCondicionesOrdenesRemesa: ';print_r($arrayCondicionesOrdenesRemesa);
 //echo '<br><br>1-2 modeloTesorero:actualizarRemesa:arrayCondicionesOrdenesRemesa: ';print_r($arrayDatosAct);

 $arrayCondiciones['NOMARCHIVOSEPAXML']['valorCampo'] = $arrayCondicionesOrdenesRemesa['NOMARCHIVOSEPAXML'];
 $arrayCondiciones['NOMARCHIVOSEPAXML']['operador'] = '=';	
 $arrayCondiciones['NOMARCHIVOSEPAXML']['opUnir'] = ' ';
	
	
	foreach ($arrayDatosAct as $indice => $contenido)                    
 {      
   $arrayDatos[$indice] = $contenido['valorCampo'];
 }
  
	$reActRemesa  = actualizarTabla($tablaAct,$arrayCondiciones,$arrayDatos,$conexionLinkDB);
 
	if ($reActRemesa['codError']!=='00000')			
	{	$reActRemesa['errorMensaje'] = $reActRemesa['errorMensaje']. " al actualizar tabla ".$tablaAct.". CODERROR: ".$reActRemesa['codError'];									
	}
	elseif ($reActRemesa['numFilas'] <= 0)
	{ $reActRemesa['codError'] ='80001'; 
		 $reActRemesa['errorMensaje'] = "No se han encontrado casos que cumplan las condiciones";			  
			
			$reActRemesa['errorMensaje'] = "No se han encontrado casos que cumplan las condiciones al actualizar la tabla ".$tablaAct.". CODERROR: ".$reActRemesa['codError'];		
	}	
	//echo '<br><br>2 modeloTesorero:actualizarRemesa:reActRemesa: ';print_r($reActRemesa);	
	
	return $reActRemesa;
 } 
/*----------------------------- Fin actualizarRemesa -------------------------------------------*/
	
/*== FIN: Funciones usadas en "Estado de las órdenes cobro domiciliadas en bancos =======================*/

/*== INICIO: FUNCIONES RELACIONADAS CON ARCHIVO ÓRDENES COBRO CUOTAS BANCOS:
					- exportarCuotasXMLBancos(): GENERAR ARCHIVO REMESA B.SANTANDER "SEPA-XML" (la que se usa ahora)
					- exportarCuotasExcelBancos(): ARCHIVOS CUOTAS EXCEL BANCOS (Antes Tríodos)
					- insertarOrdenesCobroRemesa(): ARCHIVOS CUOTAS EXCEL USO INTERNO (de tesorero)
					- exportarCuotasAEB19Bancos (antigua del B.Santander, ya no se usa)
========================================================================================================*/

/*---------------- Inicio buscarCodigosBIC ----------------------------------------------------------
Descripción: Busca los datos en las tabla  "CODIGOSBIC" para cobro cuotas SEPA
Llamada: modeloTesorero.php: SEPA_XML_SDD_CuotasTesoreroSantanderWrite(), ya no lo necesita,
Devuelve: un array con [resultadoFilas] organizado por según las condiciones de la consulta.
										También incluye datos de [codError] , [numFilas]
NOTA: 	Esta tabla se debiera actualizar al menos antes de cada cobro de cuotas, 
       pues se añaden y retiran bancos por el Banco de España. 

*** AUNQUE AHORA YA NO SE USA, dejo por si más adelante fuese útil para "BIC" PARA "ES"							
--------------------------------------------------------------------------------------------------*/
function buscarCodigosBIC($codEntidad, $codBIC, $nombreBanco)//ahora no se usua
{//echo '<br><br>1a modeloTesorero:buscarCodigosBIC:codEntidad :';print_r($codEntidad);
 //echo '<br><br>1b modeloTesorero:buscarCodigosBIC:codBIC :';print_r($codBIC);
 //echo '<br><br>1c modeloTesorero:buscarCodigosBIC:nombreBanco :';print_r($nombreBanco);

	$resCodigosBIC['codError']='00000';
	$resCodigosBIC['errorMensaje']='';
	$resCodigosBIC['nomFuncion']="buscarCodigosBIC";
 $resCodigosBIC['nomScript']="modeloTesorero.php";	

 require_once "BBDD/MySQL/modeloMySQL.php";
	
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	require_once "BBDD/MySQL/conexionMySQL.php";
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);

	if ($conexionDB['codError']!=='00000')	
	{ $resCodigosBIC=$conexionDB;
	}
	else	
	{	
	 if ( !isset($codEntidad) || empty($codEntidad) || $codEntidad =='%' )
		{ $condicionCodEntidadElegido = '';}
		else
		{	$condicionCodEntidadElegido = "  CODIGOSBIC.CODIGOENTIDAD = '$codEntidad'";}		
	 if ( !isset($codBIC) || empty($codBIC) || $codBIC =='%' )
		{ $condicionCodBICElegido = '';}
		else
		{	$condicionCodBICElegido = "  CODIGOSBIC.BIC = '$codBIC'";	}			
		
		if ( !isset($nombreBanco) || empty($nombreBanco) || $nombreBanco =='%' )
		{ $condicionNombreBancoElegido = '';}
		else
		{ $condicionNombreBancoElegido = "  CODIGOSBIC.NOMBREBANCO LIKE '%$nombreBanco%'";		}	
		
		if (!empty($condicionCodEntidadElegido) && !empty($condicionCodBICElegido) && !empty($condicionNombreBancoElegido))
		{ $cadCondicionesBuscar = " WHERE ".$condicionCodEntidadElegido." AND ".$condicionCodBICElegido." AND ".$condicionNombreBancoElegido;		}
		elseif (!empty($condicionCodEntidadElegido) && !empty($condicionCodBICElegido) )
		{ $cadCondicionesBuscar = " WHERE ".$condicionCodEntidadElegido." AND ".$condicionCodBICElegido;		}
		elseif (!empty($condicionCodEntidadElegido) && !empty($condicionNombreBancoElegido) )
		{ $cadCondicionesBuscar = " WHERE ".$condicionCodEntidadElegido." AND ".$condicionNombreBancoElegido;		}
		elseif (!empty($condicionCodBICElegido) && !empty($condicionNombreBancoElegido))
		{ $cadCondicionesBuscar = " WHERE ".$condicionCodBICElegido." AND ".$condicionNombreBancoElegido;		}
		elseif (!empty($condicionCodEntidadElegido) )
		{ $cadCondicionesBuscar = " WHERE ".$condicionCodEntidadElegido;		}
		elseif (!empty($condicionCodBICElegido) )
		{ $cadCondicionesBuscar = " WHERE ".$condicionCodBICElegido;		}
		elseif (!empty($condicionNombreBancoElegido))
		{ $cadCondicionesBuscar = " WHERE ".$condicionNombreBancoElegido;		}
		else
		{ $cadCondicionesBuscar = ' ';			
		}	
				
  //echo '<br><br>2a modeloTesorero:buscarCodigosBIC:nombreBanco :';print_r($nombreBanco);	
		
	 $tablasBusqueda = "CODIGOSBIC";
	 $camposBuscados = "CODIGOSBIC.*";		
		
	 $cadCondicionesBuscar = " $cadCondicionesBuscar ";		

		//$codigosBIC = buscarEnTablas($tablasBusqueda,$cadCondicionesBuscar,$camposBuscados,$conexionDB['conexionLink']);

		$cadSql = " SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar ";
		
		//echo '<br><br>2b modeloTesorero:buscarCodigosBIC:cadSql: ';print_r($cadSql);		
		
		$arrBind = array();
		$codigosBIC = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);	
																														
  //echo '<br><br>2c modeloTesorero:buscarCodigosBIC:$codigosBIC: ';print_r($codigosBIC);
																																								
		if ($codigosBIC['codError'] !== '00000')
		{$resCodigosBIC['codError'] = $codigosBIC['codError'];
		 $resCodigosBIC['errorMensaje'] = $codigosBIC['errorMensaje'];	
		 $resCodigosBIC['arrMensaje']['textoCabecera']='Buscar códigos BIC';
			$resCodigosBIC['arrMensaje']['textoComentarios'].=".Error del sistema, vuelva a intentarlo pasado un tiempo (modeloTesorero: buscarCodigosBIC)";
		 $resCodigosBIC['arrMensaje']['textoBoton']='Salir de la aplicación';
		 $resCodigosBIC['arrMensaje']['enlaceBoton']='./index.php?controlador=controladorLogin&amp;accion=logOut';
	
			require_once './modelos/modeloErrores.php';
			insertarError($resCodigosBIC);		
		}	
		/*elseif ($codigosBIC['numFilas']==0)
		{ $resCodigosBIC['codError']='80004'; 
			 $resCodigosBIC['errorMensaje']="No encontrado";
		}	
  */
  else 
  { $resCodigosBIC['numFilas'] = $codigosBIC['numFilas'];	
	   $resCodigosBIC['resultadoFilas'] = $codigosBIC['resultadoFilas'];
		}			
	}
		
 //echo '<br><br>3 modeloTesorero:buscarcodigosBIC:resCodigosBIC: ';print_r($resCodigosBIC);
	
	return $resCodigosBIC;
}
//------------------------------------------- Fin buscarcodigosBIC ----------------------------------

/*------ Inicio cadBuscarCuotasEmailSocios 	(de uso común en funciones relacionadas cuotas)----------
Forma la cadena select sql para buscar los datos de las cuotas de un año, (o de todos)
y otros datos de los socios, según los parámetros elegidos en el formulario de selección de campos, 
que dependerán de las funciones que llamen a esta.
Con esos parámetros se crearán las diversas condiciones WHERE para formar la cadena select SQL. 

Se necesitará Remesas cobro B.Santander, emails de aviso cobros cuotas, Excel Cuotas Bancos(antes Tríodos), 
generar Excel para uso interno, envíos email aviso Próximo Cobro Cuota, ect. Pero pudiera tener otros 
usos al ser muy completa y flexible.
La idea de hacer esta función compartida por varias funciones, algunas muy críticas, es que haya una 
coherencia en estos resultados, y que las modificaciones sean comunes incluyendo todos los casos posibles.
Se validan todos los parámetros recibidos, especialmente que no estén vacíos, para evitar errores 
al formar las condiciones WHERE. En caso de error se devuelve el mensaje correspondiente.
	
RECIBE:	
-$codAreaCoordinacion ='%', (de momento siempre por defecto) pero se deja por si 
		en su momento se crean tesoreros a nivel de  área de coordinación. 		
-$condicionEmail, este parámetro toma dos posibles valores, según desde donde se llame la función:
	a). llamada desde exportarCuotasXMLBancos(), exportarCuotasAEB19Bancos(),exportarCuotasExcelBancos(),
	    exportarExcelCuotasInternoTes():	$condicionEmail ='%'(ninguna condición para email)
	b). llamada desde datosEmailAvisoProximoCobro(),datosEmailAvisoCuotaNoCobradaSinCC(),
	    exportarEmailDomiciliadosPendientes(),exportarEmailSinCCSEPAPendientes(),
					$condicionEmailValido="AND MIEMBRO.EMAILRROR='NO'", para generar select  email de aviso cuotas	
					
-$arrayFormDatosElegidos: campos procedentes del formulario correspondiente, y en algún caso también 
																									 algún campo procede de alguna función de intermedia	de llamada a esta función	

DEVUELVE: $arrCadBuscarCuotasEmailSocios['cadSQLBuscarCuotasSocios'] con la cadena SQL select, 
          y campos de errores.              																									
										
LLAMADA: modeloTesorero.php: 
-exportarCuotasXMLBancos(): Para generar los archivos de las REMESAS de órdenes cobro SEPAXML B.Santander 
-exportarCuotasExcelBancos(): genera el Excel para B.Tríodos (ya no se usa para eso), ahora se usa como una copia de datos la Remesa SEPAXML B.Santander	
-exportarExcelCuotasInternoTes: genera Excel con datos personales y cuotas y bancarios para uso interno de Tesorería.								
-exportarCuotasAEB19Bancos(): ya no se usa, que genera archivo "AEB19.txt" (anteriomente eran las remesas del B.Santander),

-exportarEmailDomiciliadosPendientes():para función exportarEmailDomiciliadosPendWrite() que generar archivo de texto "emailAvisoCobro.txt"
                                       con los emails separados por (;) para enviar avisos de cuotas domiciliadas y aún no pagadas desde un programa de correo
-exportarEmailSinCCSEPAPendientes():para función exportarEmailDomiciliadosPendWrite() que generar archivo de texto con los emails 
                                    separados por (;) para enviar avisos de cuotas sin domiciliar y aún no pagadas desde un programa de correo 

-datosEmailAvisoProximoCobro(): para envíar emails de avisos próximo cobro cuotas domiciliadas desde Gestión Soci@s
-datosEmailAvisoCuotaNoCobradaSinCC(): para envíar emails de avisos cuotas aún no pagadas, sin domiciliar, desde Gestión Soci@s

OBSERVACIONES: ***Antes de modificar esta función tener en cuenta que es llamada desde varias funciones distintas 
								
Probada PHP 7.3.21. No hago adaptación para PDO7 con $arrBindValues, por complejidad condición "IN"
---------------------------------------------------------------------------------------------------*/
function cadBuscarCuotasEmailSocios($codAreaCoordinacion,$condicionEmail,$arrayFormDatosElegidos)
{
	//echo "<br /><br />0-1 modeloTesorero:cadBuscarCuotasEmailSocios:codAreaCoordinacion: ";print_r($codAreaCoordinacion);
 //echo "<br /><br />0-2 modeloTesorero:cadBuscarCuotasEmailSocios:condicionEmail: ";print_r($condicionEmail);
 //echo "<br /><br />0-3 modeloTesorero:cadBuscarCuotasEmailSocios:arrayFormDatosElegidos: ";print_r($arrayFormDatosElegidos);
	
	$arrCadBuscarCuotasEmailSocios['codError'] = '00000';
	$arrCadBuscarCuotasEmailSocios['errorMensaje'] = '';	
	$arrCadBuscarCuotasEmailSocios['cadSQLBuscarCuotasSocios'] = '';
	$textoGralError = ' ERROR: select en función cadBuscarCuotasEmailSocios(): ';
	
	//---------------- Inicio formación de condiciones WHERE --------------------------------------------

	/*--- $condicionAreaCoordinacion tabla AREAGESTIONAGRUPACIONESCOORD campo ODAREAGESTIONAGRUP -------
	 Área de coordinación, por defecto todas =%, por ahora lo gestiona el tesorero para todas las agrupaciones, 
	 lo pongo por si se cambiase esta forma de trabajar, y se hicisen tesoreros solo para una agrupación
	----------------------------------------------------------------------------------------------------*/
	if ( !isset($codAreaCoordinacion) || empty($codAreaCoordinacion) )//probado error 
	{	$arrCadBuscarCuotasEmailSocios['codError'] = '70601';
			$arrCadBuscarCuotasEmailSocios['errorMensaje'] = $textoGralError.' falta parámetro o está vacío $codAreaCoordinacion. ';							
			return $arrCadBuscarCuotasEmailSocios;
	} 		
	elseif ( $codAreaCoordinacion == '%' || $codAreaCoordinacion == 'TODOS' ) //que venga siempre con valor y si no será error
	{//$condicionAgrup = " AND AREACOORDINACIONINCLUYEAGRUPACION.CODAREACOORDINACION like '$codAreaCoordinacion' ";//más rápido la siguiente línea
			$condicionAreaCoordinacion = "";
	}
	else 
	{ $condicionAreaCoordinacion = " AND AREAGESTIONAGRUPACIONESCOORD.CODAREAGESTIONAGRUP='$codAreaCoordinacion' ";	
	}
	
	/*----------- $condicionEmail tabla MIEMBRO campo  EMAILERROR ------------------------------------------	
	 $condicionEmail	vendrá de funciones de llamada o formulario y puede tener al menos dos casos:
	 a)-$condicionEmail	= "%"; para seleccionar todos los casos posibles de situación de email (para las 
		   órdenes de cobro da igual que tengan email o no, o devuelto, siempre que tengan cuenta bancaria
  b)-$condicionEmail	= "AND MIEMBRO.EMAILERROR= 'NO' ", para generar listas email de aviso cobro cuotas	
 -------------------------------------------------------------------------------------------------------*/
	if ( !isset($condicionEmail) || empty($condicionEmail) )//probado error 
	{ $arrCadBuscarCuotasEmailSocios['codError'] = '70601';
			$arrCadBuscarCuotasEmailSocios['errorMensaje'] = $textoGralError.' falta parámetro o está vacío $condicionEmail';								
			return $arrCadBuscarCuotasEmailSocios;
	}
	elseif ( $condicionEmail == '%' || $condicionEmail == 'TODOS' )
	{  $condicionEmail = '';
	}
	elseif ($condicionEmail == 'NO' ) //para excluir a los que su email sea erróneo	= FALTA, DEVUELTO
	{	$condicionEmail = " AND MIEMBRO.EMAILERROR = 'NO' ";// o bien $condicionEmail = " AND MIEMBRO.EMAILERROR = '$condicionEmail' ";	 
	}	
	else
	{ $arrCadBuscarCuotasEmailSocios['codError'] = '70601';
			$arrCadBuscarCuotasEmailSocios['errorMensaje'] = $textoGralError.' parámetro no válido $condicionEmail';								
			return $arrCadBuscarCuotasEmailSocios;
	}
	//---------------------------------------------------------
 
	/*--------- $condicionAgrup en tabla SOCIO campo CODAGRUPACION -----------------------------------------
	 Se incluyen las claves primarias de las agrupaciones elegidas en el formulario,  viene de un box de 
		selección en el formulario correspondiente
	------------------------------------------------------------------------------------------------------*/
	if (!isset($arrayFormDatosElegidos['agrupaciones'])|| empty($arrayFormDatosElegidos['agrupaciones'])|| count($arrayFormDatosElegidos['agrupaciones']) === 0)//probado error	
 { $arrCadBuscarCuotasEmailSocios['codError'] = '70601';
			$arrCadBuscarCuotasEmailSocios['errorMensaje'] = $textoGralError.' falta parámetro o está vacío $arrayFormDatosElegidos[agrupaciones]';				
			return $arrCadBuscarCuotasEmailSocios;
 }	
	elseif (isset($arrayFormDatosElegidos['agrupaciones']['%'])  )//viene de formulario o funciones anteriores
	{ $condicionAgrup = "";
	}
	else 
 {$cadCodAgrupElegidas = '';			
	 $arrayCodAgrupElegidas = $arrayFormDatosElegidos['agrupaciones'];
		
  foreach ($arrayCodAgrupElegidas as $codAgrupacion) // Forma la cadena CODAGRUPACION IN (....) de la select
 	{
			 $cadCodAgrupElegidas .="'".$codAgrupacion."',";	
		}			
	 $cadCodAgrupElegidas = rtrim($cadCodAgrupElegidas, ",");//eliminar última coma de la cadena para evitar error SQL
				
  $condicionAgrup = " AND SOCIO.CODAGRUPACION IN (".$cadCodAgrupElegidas.") ";
	}
 //---------------------------------------------------------
	
	/*-----------$condicionAnioCuotas en tabla CUOTAANIOSOCIO campo ANIOCUOTA -----------------------------
 	Lo normal es que sea el año actual el elegido por defecto en el formulario 
		En exportar Excel uso interno vExcelCuotasInternoTesoreroInc.php se puede elegir años anteriores
	-----------------------------------------------------------------------------------------------------*/
	if ( !isset($arrayFormDatosElegidos['anioCuotasElegido']) || empty($arrayFormDatosElegidos['anioCuotasElegido']) )//probado error	
 { $arrCadBuscarCuotasEmailSocios['codError'] = '70601';
			$arrCadBuscarCuotasEmailSocios['errorMensaje'] = $textoGralError.' falta parámetro o está vacío $arrayFormDatosElegidos[anioCuotasElegido]';				
			return $arrCadBuscarCuotasEmailSocios;
 }			
	elseif ($arrayFormDatosElegidos['anioCuotasElegido'] == '%' || $arrayFormDatosElegidos['anioCuotasElegido'] == 'TODOS' )	
 { $condicionAnioCuotas = '';		
 }
	else 
	{ 	
   $condicionAnioCuotas = " AND CUOTAANIOSOCIO.ANIOCUOTA = '".$arrayFormDatosElegidos['anioCuotasElegido']."' ";	
 }	
	//---------------------------------------------------------

 /*---------- $condicionEstadoSocio en tabla USUARIO campo ESTADO ---------------------------------------
	Según los formularios, permitirá elegir  Estado socio alta, baja, TODOS
	Para lo relacionado con órdenes de cobro, ESTADO = 'alta,alta-sin-password-excel,alta-sin-password-gestor'
	Para exportar Excel uso interno vExcelCuotasInternoTesoreroInc.php además puede ser:  
 ESTADO ='%' o ='TODOS' que además incluiría ESTADO = 'baja', y ESTADO = 'ANULADA-SOCITUD-REGISTRO', pero 
	estos registros anulados, se ignoran en la práctica, ya que no cumplirán otras condiciones de la consulta
	ya que para esos casos no existirán datos en las tablas SOCIO, MIEMBRO, CUOTAANIOSOCIO... y no aparecerá 
	en la selección.
	-------------------------------------------------------------------------------------------------------*/
	if ( !isset($arrayFormDatosElegidos['ESTADO']) || empty($arrayFormDatosElegidos['ESTADO']) )//probado error	
	{ $arrCadBuscarCuotasEmailSocios['codError'] = '70601';
			$arrCadBuscarCuotasEmailSocios['errorMensaje'] = $textoGralError.' falta parámetro o está vacío $arrayFormDatosElegidos[ESTADO] ';			
			return $arrCadBuscarCuotasEmailSocios;
 }
	elseif ( $arrayFormDatosElegidos['ESTADO'] =='%' || $arrayFormDatosElegidos['ESTADO'] =='TODOS')
	{ $condicionEstadoSocio = '';		
 }
	elseif($arrayFormDatosElegidos['ESTADO'] == 'alta')
	{ 
	  $condicionEstadoSocio = " AND SUBSTRING(USUARIO.ESTADO,'1',4) = 'alta' ";//estaria bien //incluye: alta,alta-sin-password-excel,alta-sin-password-gestor	  
	}	
	elseif ($arrayFormDatosElegidos['ESTADO'] == 'baja')//por si se añadiesen otras búsquedas: baja, ANULADA-SOCITUD-REGISTRO,
	{ 
   $condicionEstadoSocio = " AND USUARIO.ESTADO = 'baja' ";
 }
 else
	{ $arrCadBuscarCuotasEmailSocios['codError'] = '70601';
			$arrCadBuscarCuotasEmailSocios['errorMensaje'] = $textoGralError.' parámetro no válido $arrayFormDatosElegidos[ESTADO] ';								
			return $arrCadBuscarCuotasEmailSocios;
	}	
	//---------------------------------------------------------

/*--- $condicionEstadosCuota en tabla CUOTAANIOSOCIO campo ESTADOCUOTA ----------------------------------------
 	Según los formularios, tendrá distintos valores en el array $arrayFormDatosElegidos['estadosCuotas'].
		-En los formularios en relación a órdenes de cobro serán: ESTADOCUOTA: 'PENDIENTE-COBRO' y 'ABONADA-PARTE'
		-En el formulario para exportar Excel uso interno vExcelCuotasInternoTesoreroInc.php, pueden ser: 
		 'PENDIENTE-COBRO,'ABONADA-PARTE','NOABONADA-DEVUELTA'='NOABONADA-ERROR-CUENTA','EXENTO','NOABONADA' o todos
---------------------------------------------------------------------------------------------------------------*/	
	if ( !isset($arrayFormDatosElegidos['estadosCuotas']) || empty($arrayFormDatosElegidos['estadosCuotas']) || count($arrayFormDatosElegidos['estadosCuotas']) === 0 )
	{ $arrCadBuscarCuotasEmailSocios['codError'] = '70601';
			$arrCadBuscarCuotasEmailSocios['errorMensaje'] = $textoGralError.' falta parámetro o está vacío $arrayFormDatosElegidos[estadosCuotas] ';				
			return $arrCadBuscarCuotasEmailSocios;
 }
	elseif (isset($arrayFormDatosElegidos['estadosCuotas']['%']) || isset($arrayFormDatosElegidos['estadosCuotas']['TODOS']) )
	{ $condicionEstadosCuota = "";
	}
	else 
 { $cadEstadosCuota  = '';		
   $arrayEstadosCuota = $arrayFormDatosElegidos['estadosCuotas'];
			
			foreach ($arrayEstadosCuota as $estadoCuota) 
			{
				$cadEstadosCuota .="'".$estadoCuota."',";	
			}			
			$cadEstadosCuota = rtrim($cadEstadosCuota, ",");//eliminar última coma de la cadena para evitar error SQL
					
			$condicionEstadosCuota = " AND CUOTAANIOSOCIO.ESTADOCUOTA IN (".$cadEstadosCuota.") ";
	}
 //---------------------------------------------------------	
	
 /*--- Campos cuentas bancos CUENTAIBAN y CUENTANOIBAN en la tabla "SOCIO" -----------------------------------
	 Según las funciones de llamada, en los distintos forms se permite elegir: 

		$paisCC ='ES'(solo CUENTAIBAN para España y CUENTAIBAN = 'ES12...'),B.Santander NO obliga a poner BIC 		
		$paisCC ='SEPA' (paises SEPA CUENTAIBAN distintos de España) en tabla "PAIS" campo SEPA ='SI', B.Santander SI obliga a poner BIC		
	 $paisCC ='EX'(con CUENTAIBAN de países NO SEPA (pudiera ser) o con CUENTANOIBAN(para años antiguos, ya no se admiten en Form alta)  		
	 $paisCC ='NO'(no tiene cuenta CUENTAIBAN ni CUENTANOIBAN)			
		$paisCC ='%' o bien 'TODOS' todos los casos: SIN cuentas o CON cuentas CUENTAIBAN(cualqier país), CUENTANOIBAN
	 
		Para generar las órdenes de cobro domiciliadas antiguo AEB19 sólo pemitía $paisCC ='ES'
	-------------------------------------------------------------------------------------------------------------*/
	if ( !isset($arrayFormDatosElegidos['paisCC']) || empty($arrayFormDatosElegidos['paisCC']) )
	{ $arrCadBuscarCuotasEmailSocios['codError'] = '70601';
			$arrCadBuscarCuotasEmailSocios['errorMensaje'] = $textoGralError.' falta parámetro o está vacío $arrayFormDatosElegidos[paisCC]';				
			return $arrCadBuscarCuotasEmailSocios;
 }
	elseif ($arrayFormDatosElegidos['paisCC'] == '%' || $arrayFormDatosElegidos['paisCC'] == 'TODOS')//todos los casos: SIN cuentas o CON cuentas CUENTAIBAN(cualqier país),CUENTANOIBAN
	{ 
	  $camposCC = " SOCIO.CUENTAIBAN, SOCIO.CUENTANOIBAN  ";
	  $condicionExisteCC = " ";	
	}
	elseif ($arrayFormDatosElegidos['paisCC'] == 'ES' )//solo las cuentas IBAN de España, B. Santander NO obliga a poner BIC 
	{
	  $camposCC = " SOCIO.CUENTAIBAN ";	
   $condicionExisteCC = " AND SOCIO.CUENTAIBAN != '' AND SOCIO.CUENTAIBAN IS NOT NULL 
		                        AND SUBSTRING(SOCIO.CUENTAIBAN,1,2) ='ES' ";
	}
	/*elseif ( $arrayFormDatosElegidos['paisCC'] == 'SEPA' )//sería cuenta de países SEPA incluida ES
 {$camposCC = " SOCIO.CUENTAIBAN ";	
	 $condicionExisteCC = " AND SOCIO.CUENTAIBAN != '' AND SOCIO.CUENTAIBAN IS NOT NULL 
		                       AND SUBSTRING(SOCIO.CUENTAIBAN,1,2) IN (SELECT CODPAIS1 FROM PAIS WHERE SEPA ='SI' )";//Sería para incluir ES y a la vez demás países SEPA
	}*/
	elseif ($arrayFormDatosElegidos['paisCC'] == 'SEPA' )//SEPA CUENTAIBAN distintos de España, B.Santander SI obliga a poner BIC 
 {
	 	$camposCC = " SOCIO.CUENTAIBAN ";	
 	 $condicionExisteCC = " AND SOCIO.CUENTAIBAN != '' AND SOCIO.CUENTAIBAN IS NOT NULL AND SUBSTRING(SOCIO.CUENTAIBAN,1,2) !='ES'
		                       AND SUBSTRING(SOCIO.CUENTAIBAN,1,2) IN (SELECT CODPAIS1 FROM PAIS WHERE SEPA ='SI' ) ";
	}
	elseif ($arrayFormDatosElegidos['paisCC'] == 'EX' )//con CUENTAIBAN de países NO SEPA (pudiera ser) o con CUENTANOIBAN(para años antiguos, ya no se admiten en Form alta) 
 {
	 	$camposCC = " SOCIO.CUENTAIBAN, SOCIO.CUENTANOIBAN  ";	
	  $condicionExisteCC = " AND (( SOCIO.CUENTANOIBAN != '' AND SOCIO.CUENTANOIBAN IS NOT NULL ) OR 
			                            ( SOCIO.CUENTAIBAN != '' AND SOCIO.CUENTAIBAN IS NOT NULL 
		                               AND SUBSTRING(SOCIO.CUENTAIBAN,1,2) IN (SELECT CODPAIS1 FROM PAIS WHERE  SEPA IS NULL OR SEPA !='SI' ) 
																															) 
																														)	";
	}	
	elseif ($arrayFormDatosElegidos['paisCC'] == 'NO')//(no tienen CC ni CUENTAIBAN ni CUENTANOIBAN )
	{
	 	$camposCC = " SOCIO.CUENTAIBAN, SOCIO.CUENTANOIBAN  ";//debieran estar vacíos esos campos																									
	  $condicionExisteCC = " AND (SOCIO.CUENTAIBAN = '' OR SOCIO.CUENTAIBAN IS NULL) 	
		                        AND (SOCIO.CUENTANOIBAN = '' OR SOCIO.CUENTANOIBAN IS NULL) ";																				
	}
	else
	{ $arrCadBuscarCuotasEmailSocios['codError'] = '70601';
			$arrCadBuscarCuotasEmailSocios['errorMensaje'] = $textoGralError.' parámetro no válido $arrayFormDatosElegidos[paisCC]';				
			return $arrCadBuscarCuotasEmailSocios;
	}		
	//---------------------------------------------------------
		
	/*-- $condicionFechaAltaExentosPago en la tabla SOCIO campo FECHAALTA -----------------------------
  a lo largo del año en segundas, terceras órdenes de cobro "de repesca" el tesorero puede elegir
		el valor "fechaAltaExentosPago"  para excluir el pago de la cuota a altas con fechas posteriores 
		ese valor que serán fechas de alta cerca de final año y no estaría bien cobrarles, en caso de 
		querer elegir que paguen todos, se pondría la fecha del día actual (lo normal en la 1ª remesa)
		Para "cTesorero.php:excelCuotasInternoTesorero()", siempre se incluye a todos viene con valores
		de día actual: anio=date('Y'),mes=date('m'),dia=date('d')
 -------------------------------------------------------------------------------------------------*/
	if (!isset($arrayFormDatosElegidos['fechaAltaExentosPago']['anio'])|| empty($arrayFormDatosElegidos['fechaAltaExentosPago']['anio'])	|| 
		   !isset($arrayFormDatosElegidos['fechaAltaExentosPago']['mes']) || empty($arrayFormDatosElegidos['fechaAltaExentosPago']['mes'])  ||
					!isset($arrayFormDatosElegidos['fechaAltaExentosPago']['dia']) || empty($arrayFormDatosElegidos['fechaAltaExentosPago']['dia'])	   ) 						
 { 
	  $arrCadBuscarCuotasEmailSocios['codError'] = '70601';
			$arrCadBuscarCuotasEmailSocios['errorMensaje'] = $textoGralError.' falta parámetro o está vacío $arrayFormDatosElegidos[fechaAltaExentosPago]';				
			return $arrCadBuscarCuotasEmailSocios;
	}
	else
	{
		 $fechaAltaExentosPago = $arrayFormDatosElegidos['fechaAltaExentosPago']['anio'].'-'.$arrayFormDatosElegidos['fechaAltaExentosPago']['mes'].'-'.
			                        $arrayFormDatosElegidos['fechaAltaExentosPago']['dia'];
			$condicionFechaAltaExentosPago = "AND SOCIO.FECHAALTA <= '$fechaAltaExentosPago'";	
	}		
	//---------------------------------------------------------
																							
	/*-- Condición ORDENARCOBROBANCO, es el campo de la tabla CUOTAANIOSOCIO -------------------------
 	Indica si se incluirá al socio en la siguiente órden de cobro al banco:
		Si no tiene cuota domicilida solo puede tener el valor=NO, si tiene domiciliada puede ser SI o NO		
		El tesorero en -Cuotas socios/as->Pago cuota, puede decidir poner a NO a un socio que tenga cuota
		domiciliada, por algún motivo, para no incluilo en la próxima orden cobro, o email de aviso cobro	
		Para la función "cTesorero.php:excelCuotasInternoTesorero()", en el 
		formulario "vExcelCuotasInternoTesoreroInc.php" se puede elegir 
		$arrayFormDatosElegidos['ORDENARCOBROBANCO']='TODOS' por defecto, o puede ser SI y NO
	-------------------------------------------------------------------------------------------------*/	
	if ( !isset($arrayFormDatosElegidos['ORDENARCOBROBANCO']) || empty($arrayFormDatosElegidos['ORDENARCOBROBANCO']) ) 
	{ $arrCadBuscarCuotasEmailSocios['codError'] = '70601';
			$arrCadBuscarCuotasEmailSocios['errorMensaje'] = $textoGralError.' falta parámetro o está vacío $arrayFormDatosElegidos[ORDENARCOBROBANCO] ';				
			return $arrCadBuscarCuotasEmailSocios;
 }
	elseif ($arrayFormDatosElegidos['ORDENARCOBROBANCO'] == '%' || $arrayFormDatosElegidos['ORDENARCOBROBANCO'] == 'TODOS' )//en formulario por defecto "TODOS" en Excel interno
 { $condicionOrdenarCobroBanco  = '';		
 }
	elseif ($arrayFormDatosElegidos['ORDENARCOBROBANCO'] == 'SI')
	{ $condicionOrdenarCobroBanco = " AND CUOTAANIOSOCIO.ORDENARCOBROBANCO = 'SI' ";	
	}
	elseif ($arrayFormDatosElegidos['ORDENARCOBROBANCO'] == 'NO')
	{ $condicionOrdenarCobroBanco = " AND CUOTAANIOSOCIO.ORDENARCOBROBANCO = 'NO' ";		
	}	
	else
	{ $arrCadBuscarCuotasEmailSocios['codError'] = '70601';
			$arrCadBuscarCuotasEmailSocios['errorMensaje'] = $textoGralError.' parámetro no válido $arrayFormDatosElegidos[ORDENARCOBROBANCO] ';				
			return $arrCadBuscarCuotasEmailSocios;
	}	
	//---------------------------------------------------------		
 //----------------------------- Fin condiciones WHERE ---------------------------------------------
	
	//----------------------------- Inicio formación consulta SELECT ----------------------------------
	
	$tablasBusqueda = "USUARIO,MIEMBRO,SOCIO,AGRUPACIONTERRITORIAL,CUOTAANIOSOCIO,AREAGESTIONAGRUPACIONESCOORD";	

	/*Nota: Los datos numéricos de las cuotas y gastos, ISO-SEPA-XML se deben poner con punto decimal 
   y tambian para UPDATE  (de lo contrario truncaría la parte decimal). 
	  Para Excel es mejor con coma decimal por lo que en exportarExcelTexto.php, se convertirá 
	  el punto decimal en coma decimal Excel.
			//-- Aquí está con punto decimal es se necesita para evitar problemas con B. Santander --
	*/
 
	$camposBuscados = 

	"DISTINCT SOCIO.CODSOCIO as Referencia_codSocio, USUARIO.CODUSER, SOCIO.FECHAALTA, SOCIO.CODAGRUPACION, SOCIO.CODCUOTA, SOCIO.SECUENCIAADEUDOSEPA, 
	DATE_FORMAT(SOCIO.FECHAACTUALIZACUENTA,'%Y-%m-%d') FECHAACTUALIZACUENTA, USUARIO.ESTADO, ".$camposCC." ,
	
	IF((CUOTAANIOSOCIO.IMPORTECUOTAANIOSOCIO-CUOTAANIOSOCIO.IMPORTECUOTAANIOPAGADA)<0,0.00,(CUOTAANIOSOCIO.IMPORTECUOTAANIOSOCIO-CUOTAANIOSOCIO.IMPORTECUOTAANIOPAGADA)) as CuotaDonacionPendienteCobro, 

	CUOTAANIOSOCIO.IMPORTECUOTAANIOSOCIO as IMPORTECUOTAANIOSOCIO, 

	CUOTAANIOSOCIO.IMPORTECUOTAANIOPAGADA as IMPORTECUOTAANIOPAGADA,

	CUOTAANIOSOCIO.IMPORTEGASTOSABONOCUOTA as IMPORTEGASTOSABONOCUOTA,	

	CUOTAANIOSOCIO.IMPORTECUOTAANIOEL as IMPORTECUOTAANIOEL,

	CUOTAANIOSOCIO.IMPORTECUOTAANIOPAGADA - CUOTAANIOSOCIO.IMPORTECUOTAANIOEL as saldo, 

	CUOTAANIOSOCIO.ESTADOCUOTA, CUOTAANIOSOCIO.ORDENARCOBROBANCO, CUOTAANIOSOCIO.ANIOCUOTA, 

	DATE_FORMAT(CUOTAANIOSOCIO.FECHAPAGO,'%Y-%m-%d') FECHAPAGO,
	DATE_FORMAT(CUOTAANIOSOCIO.FECHAANOTACION,'%Y-%m-%d') FECHAANOTACION,

	CUOTAANIOSOCIO.MODOINGRESO,
	
	UPPER(CONCAT(MIEMBRO.APE1,' ',IFNULL(MIEMBRO.APE2,''),', ',MIEMBRO.NOM)) as Apellidos_Nombre,
	SUBSTRING(MIEMBRO.DIRECCION,1,40) as DIRECCION,
	SUBSTRING(MIEMBRO.LOCALIDAD,1,40) as LOCALIDAD, MIEMBRO.CP as Codigo_postal,MIEMBRO.CODPAISDOM as Codigo_Pais,
	UPPER(MIEMBRO.NUMDOCUMENTOMIEMBRO) as NIF,
	MIEMBRO.EMAIL as EMAIL, MIEMBRO.EMAILERROR, MIEMBRO.INFORMACIONEMAIL, MIEMBRO.TELFIJOCASA, MIEMBRO.TELMOVIL,

	AGRUPACIONTERRITORIAL.NOMAGRUPACION as Agrupacion_Actual ";	

																		
 $cadCondicionesBuscar =" WHERE USUARIO.CODUSER = MIEMBRO.CODUSER 
																										AND USUARIO.CODUSER = SOCIO.CODUSER 
																										AND SOCIO.CODSOCIO = CUOTAANIOSOCIO.CODSOCIO																
																											
																										AND SOCIO.CODAGRUPACION = AREAGESTIONAGRUPACIONESCOORD.CODAGRUPACION

																										AND AREAGESTIONAGRUPACIONESCOORD.CODAGRUPACION = AGRUPACIONTERRITORIAL.CODAGRUPACION".
																											
																										$condicionAreaCoordinacion.$condicionEmail.
																										$condicionAgrup.$condicionAnioCuotas.$condicionEstadoSocio.$condicionEstadosCuota.
																										$condicionExisteCC.$condicionFechaAltaExentosPago.
																										$condicionOrdenarCobroBanco.
																																																			
																										" ORDER BY ANIOCUOTA DESC, Apellidos_Nombre";																																
							

	$arrCadBuscarCuotasEmailSocios['cadSQLBuscarCuotasSocios'] = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";
																																			
	//echo '<br><br>10 modeloTesorero:cadBuscarCuotasEmailSocios:arrCadBuscarCuotasEmailSocios: ';print_r($arrCadBuscarCuotasEmailSocios);
	
 //----------------------------- Fin formación consulta SELECT ----------------------------
	
	return $arrCadBuscarCuotasEmailSocios;
}
/*------ Fin cadBuscarCuotasEmailSocios	-----------------------------------------------------------*/


/*--------- Inicio exportarCuotasXMLBancos --------------------------------------------------------
Crea un archivo "SEPA_ISO20022CORE_fecha_orden_cobr.xml", Norma SEPA validado con "pain.008.001.02.xsd"
para la orden de cobro de cuotas en el B. Santander y lo guarda en un directorio de acceso restringido
para después descargarlo y subirlo a la web del B. Santander. 
Actualmente es el directorio "/upload/TESORERIA/SEPAXML_ISO20022" que viene de 
cTesorero:XMLCuotasTesoreroSantander()(y a su vez desde formulario y como opción podría venir 
de constantes o BBDD)  

A la vez se insertarán los datos de esa remesa	en las tablas "ORDENES_COBRO" y "REMESAS_SEPAXML".

DETALLE DE LA FUNCIÓN POR PASOS: 
- LLama a modeloTesorero:cadBuscarCuotasEmailSocios() para formar la cadena select para buscar los
datos de todas las cuotas de los socios según la selección (agrupaciones territorial, año, 
Cuenta banco España o SEPA,	ESTADOCUOTA), condicionFechaAltaExentosPago, ...), a partir de los datos
recibidos en cTesorero.php desde el formulario.

Y además siempre INCLUIRÁ:
- socios que estén de alta e incluye: alta,alta-sin-password-excel,alta-sin-password-gestor
- ORDENARCOBROBANCO ="SI" es condición necesaria para que se incluya en la lista	de cobros
- LOS ESTADOS DE CUOTAS: 'PENDIENTE-COBRO','ABONADA-PARTE' (las cuotas reducidas también se incluyen)
		
- Se ejecuta la SELECT y se obtiene un array "$arrDatosCobroCuotas" con los datos
  de cada socio para la orden de cobro de su cuota que tiene domiciliada.

- En el array "$arrDatosCobroCuotas" se comprueba si existe existe el BIC correspondiente a cada CUENTAIBAN y 
en caso exista, se tratará según sea cuenta ES u otro país SEPA distinto de ES, para evitar errores al
formar el archivo SEPA_XML en la ejecución de la función "SEPA_XML_SDD_CuotasTesoreroSantanderWrite()".
En caso de faltar algún BIC para ES se genera un BIC génerico (XXXXESXXXXX) según últimas normas del
B.Santander, y si falta BIC para otro país SEPA los mostrará en pantalla como un error en un listado 
con los datos de esas cuentas IBAN y nombres de los soci@s	para poder genrar la remesa manualmente en
la web del B.Santander.
NOTA: Actualmente no se incluyen los BICs de las cuentas bancarias, pero se deja preparado por si más adelante
se decidiese incluir junto con la 'CUENTAIBAN'	en la tabla 'SOCIO' y se evitarían cambios en esta función		

- Se pasa el array "$arrDatosCobroCuotas" a	la función "insertarOrdenesCobro()", que insertará las filas 
con los datos de esa remesa	en la tabla "ORDENES_COBRO" (que se utilizarán para actualizar la tabla	
"CUOTAANIOASOCIO"	después de que el banco efectue el cobro). También se insertan los datos genereles 
de información de esa remesa en la tabla "REMESAS_SEPAXML" (que está relacionada con tabla "ORDENES_COBRO")
"insertarOrdenesCobroRemesa()", controla e impide duplicidad de ORDENES_COBRO para los socios, 
si ya existiese una remesa previa que incluya a ese socio, y con el campo ANOTADO_EN_CUOTAANIOSOCIO= NO
En ese caso, impide la creación de un nuevo archivo de remesa, avisa del error, indica como corregirlo,
y escribe el nombre y otros datos de los socios con repetición de remesa.

- Si no hay errores se ejecuta la función "SEPA_XML_SDD_CuotasTesoreroSantanderWrite()" que utiliza 
la clase 'usuariosLibs/classes/SEPA_XML_SEPASDD/SEPASDD.php' para formar	el string con el contenido
archivo del XML Norma SEPA validado con "pain.008.001.02.xsd" en que se incluyen los datos necesarios 
de cada socio para la orden de cobro de su cuota y con los datos que pide B. Santander.

- Después se llama a la función "modeloTesoreo.php:crearEscribirArchivoSEPAXML()" que crea un archivo 
con el contenido del string el nombre "SEPA_ISO20022CORE_fecha.xml" y lo guarda en el directorio 
"/upload/TESORERIA/SEPAXML_ISO20022" para después descargarlo y subirlo a la web del B. Santander 

En caso de error en alguna función, rollback desahace las inserciones, y no se genera archivo SEPA-XML 
y se insertan los errores en modeloErrores.php:insertarError().

RECIBE: 
- $arrayFormDatosElegidos (datos bancarios de EL, año cuota, fecha cobro, fecha exención pago, 
  cuenta banco España o SEPA, agrupaciones elegidas, DIRECTORIOARCHIVOREMESA,nombreArchivoSEPA excepto fecha)
 	procedentes del formulario tesorero/formXMLCuotas.php
- $codAreaCoordinacion=%,$condicionEmail='%' que proceden del cTesorero.php ( o formulario)

DEVUELVE: array "$resExportarCuotasXMLBancos" con los mensajes correspondientes y códigos de error, 
además de crear el archivo SEPA y guardarlo en el servidor.
																								
LLAMADA: cTesorero:XMLCuotasTesoreroSantander()

LLAMA: usuariosConfig/BBDD/MySQL/configMySQL.php,BBDD/MySQL/conexionMySQL.php:conexionDB()
modeloMySQL.php:buscarCadSql()
modeloTesorero.php:cadBuscarCuotasEmailSocios(),insertarOrdenesCobroRemesa(),
SEPA_XML_SDD_CuotasTesoreroSantanderWrite(),crearEscribirArchivoSEPAXML() 
modeloErrores.php:insertarError()
									
OBSERVACIÓN: 
2020-12-12: Se rehace por completo esta función, para más seguridad y facilidad de uso
	
El campo "$nombreArchivoSEPAXML", que es clave primaria de "REMESAS_SEPAXML" (y forma parte de la 
clave primaria compuesta "ORDENES_COBRO"), lo creo en esta función tomando como dato variable la 
fecha de creación pero incluyendo date("TH:m:s"), que recoge el momento hasta los segundos,
y será muy improbable que coincidan dos órdenes con mismo hora+minutos+segundos aunque ejecutasen la 
orden dos personas con rol tesorero a la vez y el riesgo es mínimo en duplicidad en PK.
"$nombreArchivoSEPAXML"=$nombreArchivoSEPA."_".date_format(new DateTime($fechaOrdenCobroRemesa),'Y-m-d\HH-i-s').".xml";
que será algo como SEPA_ISO20022CORE_2017-12-19H09-57-04	 
Además esa improbable coincidencias sería tratada como un error repetición de valor de PK .

NOTA: 2020-11-19: SEGUN B.SANTANDER AHORA YA NO SERÍA NECESARIO BIC PARA "ES" PERO SI PARA OTROS 
PAISES SEPA	ADEMAS DICEN QUE MEJOR SIEMPRE "RCUR"	
---------------------------------------------------------------------------------------------------*/
function exportarCuotasXMLBancos($codAreaCoordinacion,$condicionEmail,$arrayFormDatosElegidos)//2020_11_19_BIC_ES_GENERICO_SEPA_LISTAERROR
{
 //echo "<br><br>0-1 modeloTesorero:exportarCuotasXMLBancos:codAreaCoordinacion: ";print_r($codAreaCoordinacion);
 //echo "<br><br>0-2 modeloTesorero:exportarCuotasXMLBancos:condicionEmail: ";print_r($condicionEmail);
	//echo "<br><br>0-3 modeloTesorero:exportarCuotasXMLBancos:arrayFormDatosElegidos: ";print_r($arrayFormDatosElegidos);

	$nombreArchivoSEPA = $arrayFormDatosElegidos['nombreArchivoSEPA']; 
	//será: $nombreArchivoSEPA = "SEPA_ISO20022CORE" y viene del formulario	y más adelante se añade fecha cobro al nombre 		
 
	$directorioArchivoRemesa = $arrayFormDatosElegidos['DIRECTORIOARCHIVOREMESA'];
	//$directorioArchivoRemesa = "/../upload/TESORERIA/SEPAXML_ISO20022"; es directorio protegido relativo a raiz $_SERVER['DOCUMENT_ROOT']		
	
	require_once './modelos/BBDD/MySQL/modeloMySQL.php';	
	require_once './modelos/modeloErrores.php'; 

 $resExportarCuotasXMLBancos['nomScript'] = 'modeloTesorero.php';		
	$resExportarCuotasXMLBancos['nomFuncion'] = 'exportarCuotasXMLBancos';
	$resExportarCuotasXMLBancos['codError'] = '00000';
	$resExportarCuotasXMLBancos['errorMensaje'] = '';	
	$resExportarCuotasXMLBancos['textoComentarios'] = '';
	$nomScriptFuncionError = ' modeloTesorero.php:exportarCuotasXMLBancos(). Error: '; 

	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	require_once "BBDD/MySQL/conexionMySQL.php";
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);

	if ($conexionDB['codError'] !== '00000')	
	{$resExportarCuotasXMLBancos = $conexionDB;
	}     
	else //if ($conexionDB['codError']=='00000')
	{
		/*---------- Inicio Buscar Datos de Cobro Cuotas socios para arcivo SEPA-XML --------------------------------------------*/

		/*if de control de existencia de "$codAreaCoordinacion, $arrayFormDatosElegidos" ya que son datos necesarios y críticos 
		  Nota: Ya no sería necesario este if, porque también se controla $arrayFormDatosElegidos en "cadBuscarCuotasEmailSocios()"  
		*/		
	 if ( (!isset($arrayFormDatosElegidos['nombreArchivoSEPA'] ) || empty($arrayFormDatosElegidos['nombreArchivoSEPA'] ) ) ||
							(!isset($arrayFormDatosElegidos['DIRECTORIOARCHIVOREMESA'] ) || empty($arrayFormDatosElegidos['DIRECTORIOARCHIVOREMESA']) ) ||
		     (!isset($arrayFormDatosElegidos['anioCuotasElegido']) || empty($arrayFormDatosElegidos['anioCuotasElegido']) ) || 
							(!isset($arrayFormDatosElegidos['fechaAltaExentosPago'] ) || empty(array_filter($arrayFormDatosElegidos['fechaAltaExentosPago'])) ) || 
						 (!isset($arrayFormDatosElegidos['paisCC'] ) || empty($arrayFormDatosElegidos['paisCC'])) ||
							(!isset($arrayFormDatosElegidos['agrupaciones'] ) || empty(array_filter($arrayFormDatosElegidos['agrupaciones'] )) ) ||
							(!isset($codAreaCoordinacion ) || empty($codAreaCoordinacion ))	|| (!isset($condicionEmail) || empty($condicionEmail))	)					
		{
			$resExportarCuotasXMLBancos['codError'] = '70601';//probado error sería un error de sistema pues no se podrá formar la select correctamente
			$resExportarCuotasXMLBancos['errorMensaje'] = ' ERROR: Remesa faltan datos necesarios para buscar cuotas pendientes cobro socios/as: algún campo de $arrayFormDatosElegidos';			 									
			$resExportarCuotasXMLBancos['textoComentarios'] = $resExportarCuotasXMLBancos['codError'].$resExportarCuotasXMLBancos['errorMensaje'];										
			$arrInsertarErrores = $resExportarCuotasXMLBancos;						
			//echo "<br><br>1-1 modeloTesorero:exportarCuotasXMLBancos:resExportarCuotasXMLBancos: ";print_r($resExportarCuotasXMLBancos);
		}	
		else //!	if ( (!isset($arrayFormDatosElegidos) || empty($arrayFormDatosElegidos)) ... || 
		{
			//podrían venir de cTesorero.php o desde formulario:
			$arrayFormDatosElegidos['ESTADO'] = 'alta';//solo se cobra a socios que estén de alta e incluye: alta,alta-sin-password-excel,alta-sin-password-gestor
			$arrayFormDatosElegidos['estadosCuotas'] = array('PENDIENTE-COBRO'=>'PENDIENTE-COBRO','ABONADA-PARTE'=>'ABONADA-PARTE');		
			$arrayFormDatosElegidos['ORDENARCOBROBANCO'] = 'SI'; //se excluyen los que tengan ['ORDENARCOBROBANCO']='NO' elegido por tesorero, aunque esté pendiente de cobro; 		  
			
	  //echo "<br><br>1-1 modeloTesorero:exportarCuotasXMLBancos:arrayFormDatosElegidos: ";print_r($arrayFormDatosElegidos);			
   
			$arrSQLBuscarCuotasSocios =	cadBuscarCuotasEmailSocios($codAreaCoordinacion,$condicionEmail,$arrayFormDatosElegidos);//en modeloTessorero.php probado error
				
   //echo "<br><br>1-2 modeloTesorero:exportarCuotasXMLBancos:arrSQLBuscarCuotasSocios: ";print_r($arrSQLBuscarCuotasSocios);

   if ($arrSQLBuscarCuotasSocios['codError'] !== '00000')		
			{ 
					$resExportarCuotasXMLBancos['codError'] = $arrSQLBuscarCuotasSocios['codError'];
					$resExportarCuotasXMLBancos['errorMensaje'] = $arrSQLBuscarCuotasSocios['errorMensaje'];
					$textoErrores = " ERROR: En cadBuscarCuotasEmailSocios() al buscar datos de cuotas pendientes cobro socios/as. CODERROR: ".$resExportarCuotasXMLBancos['codError'].$resExportarCuotasXMLBancos['errorMensaje'];						 
					$resExportarCuotasXMLBancos['textoComentarios'] = $textoErrores;							
					$arrInsertarErrores = $resExportarCuotasXMLBancos;
			}
   else //$arrSQLBuscarCuotasSocios['codError'] == '00000'
   {		  
				$arrDatosCobroCuotas = buscarCadSql($arrSQLBuscarCuotasSocios['cadSQLBuscarCuotasSocios'],$conexionDB['conexionLink']);//en modeloMySQL.php, probado error
			
				//echo "<br><br>1-3 modeloTesorero:exportarCuotasXMLBancos:arrDatosCobroCuotas: ";print_r($arrDatosCobroCuotas);
			
				/*------------- Fin Buscar Datos de Cobro Cuotas socios para archivo SEPA-XML --------------------------------------------*/
				if ($arrDatosCobroCuotas['codError'] !== '00000')//probado error sistema OK, con insertarError() y emailErrorWMaster() al final
				{ 
						$resExportarCuotasXMLBancos['codError'] = $arrDatosCobroCuotas['codError'];
						$resExportarCuotasXMLBancos['errorMensaje'] = $arrDatosCobroCuotas['errorMensaje'];
						$textoErrores = " ERROR: En buscarCadSql() al buscar datos de cuotas pendientes cobro socios/as. CODERROR: ".$resExportarCuotasXMLBancos['codError'].$resExportarCuotasXMLBancos['errorMensaje'];						 
						$resExportarCuotasXMLBancos['textoComentarios'] =  $textoErrores;							
						$arrInsertarErrores = $resExportarCuotasXMLBancos;
				}
				elseif ($arrDatosCobroCuotas['numFilas'] == 0)//probado error ok. No hay cuotas domiciliadas pendientes para condiciones búsqueda, NO ES ERROR, no inserta error, pero se muestra en pantalla 
				{					
						$resExportarCuotasXMLBancos['codError'] = '80001';//No sería verdaderemente un error, acaso se podría llevar a controlador?
						$resExportarCuotasXMLBancos['textoComentarios'] = " No se han encontrado datos que cumplan las condiciones de búsqueda elegidas para generar el al archivo SEPA XML las órdenes de cobro.";									
				}
				else	//arrDatosCobroCuotas['numFilas'] !==0 ) > 0
				{/*
							Inicio comprobación que en array $arrDatosCobroCuotas existe el BIC correspondiente a cada CUENTAIBAN y 
							en caso exista, se tratará según sea cuenta ES u otro país SEPA distinto de ES, para evitar errores al
							formar el archivo SEPA_XML en la ejecución de la función "SEPA_XML_SDD_CuotasTesoreroSantanderWrite()".
							En caso de faltar algún BIC para ES se genera un BIC génerico (XXXXESXXXXX), y si falta BIC para otro país SEPA 
							los mostrará en pantalla como un error un listado con los datos de esas cuentas IBAN y nombres de los soci@s						
							
							NOTA: Actualmente no se incluyen los BICs de las cuentas bancarias, pero se deja preparado por si más adelante
													se decidiese incluir junto con la 'CUENTAIBAN'	en la tabla 'SOCIO' y se evitarían cambios en esta función
					*/ 				
					//echo "<br><br>2-1 modeloTesorero:exportarCuotasXMLBancos:arrDatosCobroCuotas['numFilas']: ";print_r($arrDatosCobroCuotas['numFilas']);	

					$f = 0;	
					$contadorNoBICs = 0;
					$listaSociosSinBIC = 	'';	
				
					while ($f < $arrDatosCobroCuotas['numFilas'])
					{ 
							if (!isset($arrDatosCobroCuotas['resultadoFilas'][$f]['BIC']) || empty($arrDatosCobroCuotas['resultadoFilas'][$f]['BIC']))
							{	
									$cuentaIBAN_Pais = substr($arrDatosCobroCuotas['resultadoFilas'][$f]['CUENTAIBAN'], 0, 2);  
									
									if ($cuentaIBAN_Pais !== 'ES')//Habrá que generear la remesa manualmente o pago con PayPal
									{ 
											$cuentaIBAN = $arrDatosCobroCuotas['resultadoFilas'][$f]['CUENTAIBAN'];				
											$agrupacionSocioIBAN = $arrDatosCobroCuotas['resultadoFilas'][$f]['Agrupacion_Actual'];	
											$nomSocioIBAN = $arrDatosCobroCuotas['resultadoFilas'][$f]['Apellidos_Nombre'];
											$importeCuotaAdeudo = $arrDatosCobroCuotas['resultadoFilas'][$f]['CuotaDonacionPendienteCobro'];//en caso de ABONADA-PARTE (solo se incluye la parte que adeuda)
											$numDocumento = $arrDatosCobroCuotas['resultadoFilas'][$f]['NIF'];//aunque pone NIF, podría ser cualquier otro, el que figure en la tabla MIEMBRO											
											
											//$arrDatosCobroCuotas['resultadoFilas'][$f]['BIC'] = 'XXXX'.$cuentaIBAN_Pais.'XXXXX';//OJO NO se puede poner algo como	XXXXFRXXXXX, XXXXBEXXXXX, no admite B. Santander error al firmar.		
									
											$listaSociosSinBIC .= "<br />- PAIS CUENTA BANCARIA: ".$cuentaIBAN_Pais.", Nombre: ".$nomSocioIBAN.", Num. documento: ".$numDocumento.", IBAN: ".$cuentaIBAN.
																																	", Importe adeudo: ".$importeCuotaAdeudo." euros, AGRUPACIÓN: ".$agrupacionSocioIBAN;
											$contadorNoBICs++;															
									}
									else//$cuentaIBAN_Entidad == 'ES' 
									{ 			
											//$arrDatosCobroCuotas['resultadoFilas'][$f]['BIC'] = 'XXXX'.$cuentaIBAN_Pais.'XXXXX';//no se puede omitir hay que poner formato de 11 caracteres XXXXESXXXXX,		
											$arrDatosCobroCuotas['resultadoFilas'][$f]['BIC'] = 'XXXXESXXXXX';//Si NO se conoce no puede ser NULL o "", ya que es necesario validación urn:iso:std:iso:20022:tech:xsd:pain.008.001.02
											//$contadorNoBICs++;	// no hace falta aquí	
											
									}//else $cuentaIBAN_Entidad == 'ES'
									
							}//	if (!isset($arrDatosCobroCuotas['resultadoFilas'][$f]['BIC']) || empty($arrDatosCobroCuotas['resultadoFilas'][$f]['BIC']))	
								
							$arrDatosCobroCuotas['resultadoFilas'][$f]['SECUENCIAADEUDOSEPA'] = 'RCUR';//***Ahora B.Santander recomienda poner todos a RCUR aunque sea la primera vez (estos según norma SEPA era FRST)
							
							$f++;
					}//while ($f < $arrDatosCobroCuotas['numFilas'])
						
					//echo "<br><br>3-1 modeloTesorero:exportarCuotasXMLBancos:contadorNoBICs: ";print_r($contadorNoBICs); 
					//echo "<br><br>3-2 modeloTesorero:exportarCuotasXMLBancos:listaSociosSinBIC: ";print_r($listaSociosSinBIC); 	
					//echo "<br><br>3-3 modeloTesorero:exportarCuotasXMLBancos:arrDatosCobroCuotas: ";print_r($arrDatosCobroCuotas); 
				
					if ($contadorNoBICs >= 1)//no ha encontrado algún BIC 
					{ 					
							$resExportarCuotasXMLBancos['codError'] = '80001';//para que retorne ERROR LÓGICO 80001,	mejor no grabarlo, se enviará a pantalla, y creo conveniente email a tesorero.
							
							$textoErrores = "<strong>ERROR:</strong> No se han encontrado algunos códigos BICs correspondientes a las cuentas IBAN que algunas socias/os 
							tienen en bancos de otros países SEPA distintos de España. 
							<br /><br /><strong>OPCIONES:</strong> Debes anotar los datos de los socios/as que aparecen debajo (puedes copiar y pegar).
							<br /><br />A) Si es país SEPA, y la cuenta IBAN NO es 'ES', Tesorería podría generar manualmente la remesa de esos soci@s en la web del B. Santander con los datos que hay más abajo, una vez conseguido el BIC.
							<br /><br />B) Si quieres puedes excluir a esos socios/as de las órdenes de cobro, para ello tienes que ir a -Cuotas socios/as, y en la columna - Actualiza cuota -> poner el campo
																						*Incluir en la próxima lista de órdenes de cobro a los bancos del año actual = NO .
							<br /><br />C) Puedes enviarles un email para sugerirles que paguen mediante PayPal. 
							<br /><br /><br /><strong>DATOS SOCIAS/OS SIN BIC:</strong><br />";
																																							
							$resExportarCuotasXMLBancos['textoComentarios'] =	$textoErrores.$listaSociosSinBIC;	//la parte $listaSociosSinBIC muy útil .OK   
							
							//$arrInsertarErrores = $resExportarCuotasXMLBancos;// lo dejo comentado por ahora no inserto en tabla ERRORES, puesto que no es un error del sistema
							
							//echo "<br><br>3-4 modeloTesorero:exportarCuotasXMLBancos:resExportarCuotasXMLBancos: ";print_r($resExportarCuotasXMLBancos);
					}//elseif ($contadorNoBICs >= 1)			
					
					/*---Fin comprobación si en array $arrDatosCobroCuotas existe el BIC correspondientes a las cuentas IBAN de los socios	*/	
					
					if ($resExportarCuotasXMLBancos['codError']  == '00000' )	//Encontrado todos los BIC correspondientes a CUENTAIBANs se puede seguir
					{					
						/*------ Inicio insertarOrdenesCobroRemesa(): inserta datos en tablas "REMESAS_SEPAXML" y "ORDENES_COBRO" ---*/
						
						$anioCuota = $arrayFormDatosElegidos['anioCuotasElegido'];					
						$fechaAltasExentosPago = $arrayFormDatosElegidos['fechaAltaExentosPago']['anio']."-".$arrayFormDatosElegidos['fechaAltaExentosPago']['mes']."-".$arrayFormDatosElegidos['fechaAltaExentosPago']['dia'];
						$fechaCreacion = date("c");//será algo como "2015-02-12T15:19:21+00:00",Fecha ISO 8601 (añadido en PHP 5)							
						
						/*"$nombreArchivoSEPAXML", será la clave primaria en "REMESAS_SEPAXML" y también forma parte de la PK "ORDENES_COBRO", 
								lo creo en esta función tomando como dato variable la fecha del día de orden de cobro que viene del formulario, 
								pero con el añadido date("TH:m:s"), que recoge el momento de creación del archivo SEPAXML hasta los segundos, con lo cual 
								podría haber dos órdenes de cobro para el mismo día, pero será muy improbable que coincidan que se creasen con la
								misma hora+minutos+segundos y riesgo mínimo de duplicidad de clave primaria.
								Esta repetición esta tratada en los errores y aconseja repetir la operación pasado un tiempo
								Nota: insertarOrdenesCobroRemesa() controla parámetros recibidos, contiene "insertarError()" y retorna mensaje error			
						*/		
						$fechaOrdenCobroRemesa = $arrayFormDatosElegidos['fechacobro']['anio']."-".$arrayFormDatosElegidos['fechacobro']['mes']."-".$arrayFormDatosElegidos['fechacobro']['dia'].date("TH:i:s");
						
	     //$nombreArchivoSEPA = $arrayFormDatosElegidos['nombreArchivoSEPA']; //será: $nombreArchivoSEPA = "SEPA_ISO20022CORE" y viene del formulario					
												
						$nombreArchivoSEPAXML = $nombreArchivoSEPA."_".date_format(new DateTime($fechaOrdenCobroRemesa),'Y_m_d\HH_i_s').".xml";//será: SEPA_ISO20022CORE_2020_12_19H14_48_27 OK	

						//echo "<br><br>4-1 modeloTesorero:exportarCuotasXMLBancos:nombreArchivoSEPAXML: ";print_r($nombreArchivoSEPAXML);		
						
						require_once("BBDD/MySQL/transationPDO.php");
						$resIniTrans = beginTransationPDO($conexionDB['conexionLink']);					
											
						if ($resIniTrans['codError'] !== '00000') //será ['codError'] = '70501';
						{$resIniTrans['errorMensaje'] = 'Error en el sistema, no se ha podido iniciar la transación. ';
							$resIniTrans['numFilas'] = 0;											
							$resExportarCuotasXMLBancos = $resIniTrans;	
							$arrInsertarErrores = $resExportarCuotasXMLBancos;
							//echo "<br><br>4-2 modeloTesorero:exportarCuotasXMLBancos:resExportarCuotasXMLBancos: ";print_r($resExportarCuotasXMLBancos);			
						}			
						else //$resIniTrans['codError'] == '00000'		
						{/*insertarOrdenesCobroRemesa(), función que inserta las filas correspondientes en tablas "ORDENES_COBRO" y "REMESAS_SEPAXML" y controla e impide 
									duplicidad de ORDENES_COBRO para los socios, si ya existiese un remesa previa que incluya a ese socio, y que el campo ANOTADO_EN_CUOTAANIOSOCIO=NO.
									En ese caso, impide la creación de un nuevo archivo de remesa, avisa del error, indica como corregirlo, y escribe el nombre y 
									otros datos de los socios con repetición de remesa. 	
							*/						
       $resInsertarOrdenesCobroRemesa = insertarOrdenesCobroRemesa($arrDatosCobroCuotas,$anioCuota,$fechaOrdenCobroRemesa,$fechaAltasExentosPago,$fechaCreacion,
                                                                   $nombreArchivoSEPAXML,$directorioArchivoRemesa);//en modeloTesorero.php	probado error 									

							//echo "<br><br>4-3 modeloTesorero:exportarCuotasXMLBancos:resInsertarOrdenesCobroRemesa: ";print_r($resInsertarOrdenesCobroRemesa);//SEPA_ISO20022CORE_2017-12-19H09-07-04	 OK	
							/*--------- Fin insertarOrdenesCobroRemesa(): inserta datos en tablas "REMESAS_SEPAXML" y "ORDENES_COBRO" ---*/		

							if ($resInsertarOrdenesCobroRemesa['codError'] !== '00000')//Error OK, inserta y email. Ya vienen tratados los errores a final y también se pasan a cTesorero.php
							{ 
									$resExportarCuotasXMLBancos['codError'] = $resInsertarOrdenesCobroRemesa['codError'];
									$resExportarCuotasXMLBancos['errorMensaje'] = $resInsertarOrdenesCobroRemesa['errorMensaje'];									
	        $textoErrores = " ERROR: En insertarOrdenesCobroRemesa(). CODERROR: ".$resInsertarOrdenesCobroRemesa['codError'].$resInsertarOrdenesCobroRemesa['errorMensaje'];					 									
									$resExportarCuotasXMLBancos['textoComentarios'] = $textoErrores;
									$arrInsertarErrores = $resExportarCuotasXMLBancos;	
							}
							else// $resInsertarOrdenesCobroRemesa['codError'] == '00000'
							{ 
									/*La función "SEPA_XML_SDD_CuotasTesoreroSantanderWrite()" forma	el string con el contenido para 
											archivo XML Norma SEPA validado con "pain.008.001.02.xsd" y con los datos que pide B. Santander	
									*/								
									$resSEPA_XML_SDD_Cuotas = SEPA_XML_SDD_CuotasTesoreroSantanderWrite($arrDatosCobroCuotas,$arrayFormDatosElegidos,$fechaCreacion);//modeloTesorero, probado ERROR	
									
									//echo "<br><br>5-1 modeloTesorero:exportarCuotasXMLBancos:resSEPA_XML_SDD_Cuotas: Error: ";print_r($resSEPA_XML_SDD_Cuotas);

									if ($resSEPA_XML_SDD_Cuotas['codError'] !== '00000')
									{ 										
											$resExportarCuotasXMLBancos['codError'] = $resSEPA_XML_SDD_Cuotas['codError'];
											$resExportarCuotasXMLBancos['errorMensaje'] = $resSEPA_XML_SDD_Cuotas['errorMensaje'];
           $textoErrores = " ERROR: En SEPA_XML_SDD_CuotasTesoreroSantanderWrite(). CODERROR: ".$resSEPA_XML_SDD_Cuotas['codError'].$resSEPA_XML_SDD_Cuotas['errorMensaje'];													
											$resExportarCuotasXMLBancos['textoComentarios'] = $textoErrores;
											$arrInsertarErrores = $resExportarCuotasXMLBancos;	
									}
									else//if ( $resSEPA_XML_SDD_Cuotas['codError'] == '00000' )			
									{ 									  
          	/*La función "modeloTesorero.php:crearEscribirArchivoSEPAXML()" crea un archivo con el contenido del string "$resSEPA_XML_SDD_Cuotas['cadenaTextoXML']"
													y nombre "SEPA_ISO20022CORE_fecha.xml" y lo guarda en el directorio 	"/upload/TESORERIA/SEPAXML_ISO20022" para después descargarlo 
													y subirlo a la web del B.Santander 
													$directorioArchivoRemesa = "/../upload/TESORERIA/SEPAXML_ISO20022" es directorio protegido relativo a raiz $_SERVER['DOCUMENT_ROOT']
											*/
           $resGenerarArchivoSEPAXML = crearEscribirArchivoSEPAXML($resSEPA_XML_SDD_Cuotas['cadenaTextoXML'],$directorioArchivoRemesa,$nombreArchivoSEPAXML);//en modeloTesorero.php	probado error																	
											
											//echo "<br><br>5-2 modeloTesorero:exportarCuotasXMLBancos:resGenerarArchivoSEPAXML: ";print_r($resGenerarArchivoSEPAXML);
											
											if ($resGenerarArchivoSEPAXML['codError'] !== '00000')
											{ 
													$resExportarCuotasXMLBancos['codError'] = $resGenerarArchivoSEPAXML['codError'];
													$resExportarCuotasXMLBancos['errorMensaje'] =	$resGenerarArchivoSEPAXML['errorMensaje'];
             $textoErrores = " ERROR: En crearEscribirArchivoSEPAXML(). CODERROR: ".$resGenerarArchivoSEPAXML['codError'].$resGenerarArchivoSEPAXML['errorMensaje'];												
													$resExportarCuotasXMLBancos['textoComentarios'] = $textoErrores;																		
													$arrInsertarErrores = $resExportarCuotasXMLBancos;
											}									
											else//$resGenerarArchivoSEPAXML['codError'] == '00000'
											{							
												$resFinTrans = commitPDO($conexionDB['conexionLink']);
												
												//echo "<br><br>5-3 modeloTesorero:exportarCuotasXMLBancos:resFinTrans: ";var_dump($resFinTrans);
												
												if ($resFinTrans['codError'] !== '00000') //será ['codError'] = '70502';
												{$resFinTrans['errorMensaje'] = 'Error en el sistema, no se han podido anotar remesa y ordenes de cobro, ni generado archivo SEPA-XML correspondiente. ';
													$resFinTrans['numFilas'] = 0;	
											
													$resExportarCuotasXMLBancos = $resFinTrans;													
												}				
												else //$resFinTrans['codError'] == '00000'
												{
														$resExportarCuotasXMLBancos['textoComentarios'] = "<br /><br /><br />Se ha generado el archivo <strong>".$nombreArchivoSEPAXML. 
																																																"</strong> con las órdenes de cobro de cuotas domiciliadas para enviar la orden de cobro de esa remesa al B. Santander.
																																																<br /><br /><br />Además se ha efectuado el proceso de inserción en las tablas REMESAS_SEPAXML y ORDENES_COBRO 
																																																correspondientes a esa remesa con fecha de orden de cobro en el banco <strong>". $fechaOrdenCobroRemesa."</strong>";	
			
													//echo "<br><br>5-4 modeloTesorero:exportarCuotasXMLBancos:resExportarCuotasXMLBancos:";print_r($resExportarCuotasXMLBancos); 					
												}
											}//if ($reInsertarFilasOrdenCobro['codError'] =='00000')																		
									}
									//echo "<br><br>5-5 modeloTesorero:exportarCuotasXMLBancos:resSEPA_XML_SDD_Cuotas: Error: ";print_r($resSEPA_XML_SDD_Cuotas);
									
							}//else $resInsertarOrdenesCobroRemesa['codError'] == '00000'
						}//else $resIniTrans['codError'] == '00000'						
					}//if ($resExportarCuotasXMLBancos['codError']  == '00000' )
				}//else arrDatosCobroCuotas['numFilas'] !== 0)
			}
		}//else !	if( (!isset($arrayFormDatosElegidos) || empty($arrayFormDatosElegidos)) ... || 
		
		//--Fin Buscar cadBuscarCuotasEmailSocios, insertarOrdenesCobroRemesa(), SEPA_XML_SDD_CuotasTesoreroSantanderWrite()  ---
		
		//echo "<br><br>6-1 modeloTesorero:exportarCuotasXMLBancos:resExportarCuotasXMLBancos: Error: ";print_r($resExportarCuotasXMLBancos);
		//echo "<br><br>6-2 modeloTesorero:exportarCuotasXMLBancos:arrInsertarErrores: Error: ";print_r($arrInsertarErrores);
		
		//------ Inicio tratamiento de errores ----------------------------------- 
		if ($resExportarCuotasXMLBancos['codError'] !== '00000')//Ya vienen tratados comentarios los errores y se pasan a la siguiente funcion 
		{
			//echo "<br><br>7-1 modeloTesorero:exportarCuotasXMLBancos:resExportarCuotasXMLBancos: Error: ";print_r($resExportarCuotasXMLBancos);
			
			if (isset($resIniTrans['codError']) && $resIniTrans['codError'] == '00000')//ok, porque puede haber error que no implique rollback al no haber cambios en tablas   	
			{
				$resDeshacerTrans = rollbackPDO($conexionDB['conexionLink']);			

				if ($resDeshacerTrans['codError'] !== '00000')//será ['codError'] = '70503';
				{ $resDeshacerTrans['errorMensaje'] = 'Error en el sistema, no se ha podido deshacer la transación. ';
						$resDeshacerTrans['numFilas'] = 0;					
						$resExportarCuotasXMLBancos = $resDeshacerTrans;
						$arrInsertarErrores = $resExportarCuotasXMLBancos;
				  //echo "<br><br>7-2 modeloTesorero:resExportarCuotasXMLBancos:resExportarCuotasXMLBancos: ";print_r($resExportarCuotasXMLBancos);
				}						
			}				
			
			if ($resExportarCuotasXMLBancos['codError'] < '80000')//> si fuese >=80000 es error lógico y mejor no se graba, aunque se podría enviar por email.
			{	
     $arrInsertarErrores = $resExportarCuotasXMLBancos;	
					$arrInsertarErrores['textoComentarios'] =  "Función: ".$nomScriptFuncionError." Gestor CODUSER: ".$_SESSION['vs_CODUSER'].$arrInsertarErrores['textoComentarios'] ;						
				 $resInsertarErrores = insertarError($arrInsertarErrores);//en modeloErrores.php					
			}				
		}		
		//------ Fin tratamiento de errores  --------------------------------------			
	}//if ($conexionDB['codError']=='00000')

 //echo '<br><br>8 modeloTesorero:exportarCuotasXMLBancos:resExportarCuotasXMLBancos: ';print_r($resExportarCuotasXMLBancos);
	
	return $resExportarCuotasXMLBancos;	
}
/*----------- Fin exportarCuotasXMLBancos ---------------------------------------------------------*/

/*--- Inicio SEPA_XML_SDD_CuotasTesoreroSantanderWrite()(para exportarCuotasXMLBancos() B.Santander)-
La función "SEPA_XML_SDD_CuotasTesoreroSantanderWrite()" forma	el string con el contenido del 
archivo XML Norma SEPA validado con "pain.008.001.02.xsd" y con los datos que pide B. Santander	

- Se crea el array de configuración "$config" con los datos requeridos por la clase "SEPASDD.php".
con los datos del formulario "$arrayFormDatosElegidos" y otros para la cuenta de E.Laica del B. Santander
Si fuese para otro Banco habría que cambiar esos datos en el formulario que aporta los datos 
para "$arrayFormDatosElegidos" o directamente aquí.

- Con "$arrDatosCobroCuotas" que viene de consulta en modeloTesorero.php:exportarCuotasXMLBancos() se 
llama a la clase "usuariosLibs/classes/SEPA_XML_SEPASDD/SEPASDD.php", para formar	el string con el 
contenido archivo XML Norma SEPA validado con "pain.008.001.02.xsd" y con los datos que pide B. Santander.	
para posterioremnte crear archivo SEPAXML para subirlo a la web del B. Santander para el cobro domicialiado

Admite añadir información no obligatoria en "description": por ejemplo he puesto, que le llegue una 
especie de numeración de factura, y otros conceptos.		

- La función modelos/libs/eliminarAcentos.php:cambiarAcentosEspeciales() elimina acentos y ñ, etc. 
de los nombres de los socios (para evitar problemas según normas SEPA_ISO20022CORE (pain.008.001.02.xsd)

- Los datos de la cuenta del banco que podrían variar vienen del formulario para que sea fácil modificarlos: 
IVA, fechas, "creditor_id" identificación de la la entidad EL para el B. Santander que incluye CIF de EL
name($empresa_presentador), IBAN y BIC de EL, para el identificador "mandate_id" se usa:
fecha + ($reExportar['resultadoFilas'][$f]['NIF']), etc.							

- Controla	errores
													
RECIBE: 
- $arrDatosCobroCuotas: array con los datos del de la consulta, (importe cuota socio, CUENTAIBAN, BIC, NIF, etc)
  obtenida a partir de las agrupaciones del formulario  
- $arrayFormDatosElegidos: son los datos del formulario ya validados (agrupaciones, iva, cuentas EL, fechas ...)
- $fechaCreacion: Viene de modeloTesorero.php:exportarCuotasXMLBancos()

DEVUELVE: array "$SEPA_XML_SDD_CuotasTesoreroSantanderWrite" con string con el contenido archivo
XML Norma SEPA validado con los datos que pide B. Santander y datos de control de errores
																								
LLAMADA: modeloTesorero.php:exportarCuotasXMLBancos()
LLAMA: - clase "usuariosLibs/classes/SEPA_XML_SEPASDD/SEPASDD.php"
							- cambiarAcentosEspeciales()
							- validationXMLconXSDException.php (class)							
							- libxml_use_internal_errors(true);// para activar el error handling para XML					
	
OBSERVACIONES: Probada PHP 7.3.21
Los BICs que se necesitan para la validación iso:20022:tech:xsd:pain.008.001.02 ya están validados en 
la función previa "modeloTesorero.php:exportarCuotasXMLBancos() y no debieran lanzarse errores por ese
motivo dentro de esta función.

Al finalizar, esta función vuelve a modeloTesorero.php:exportarCuotasXMLBancos() donde 
con el string aquí formado con el contenido del archivo XML Norma SEPA, con la función 
modeloTesorero.php:crearEscribirArchivoSEPAXML() se genera el archivo "SEPA_XML" y se guarda en el 
servidor para posterior descarga.

NOTA: Hace algo similar a lo que se hacía unos años con norma AEB19, pero para las nuevas normas SEPA.
      modeloTesorero.php:AEB19CuotasTesoreroSantanderWrite($reExportar,$arrayFormDatosElegidos)	
						y en este caso es con con clase: usuariosLibs/classes/SEPA_XML_SEPASDD/SEPASDD.php		
--------------------------------------------------------------------------------------------------*/
function SEPA_XML_SDD_CuotasTesoreroSantanderWrite($arrDatosCobroCuotas,$arrayFormDatosElegidos,$fechaCreacion)//_2020_12_03_ArchivoEnServidor
{
	/**** ESTA ES LA QUE SE USA ACTUALMENTE *** /
	//echo '<br><br>0-1 modeloTesorero:SEPA_XML_SDD_CuotasTesoreroSantanderWrite:arrDatosCobroCuotas: ';print_r($arrDatosCobroCuotas);
	//echo '<br><br>0-2 modeloTesorero:SEPA_XML_SDD_CuotasTesoreroSantanderWrite:arrayFormDatosElegidos: ';print_r($arrayFormDatosElegidos);
 //echo '<br><br>0-3 modeloTesorero:SEPA_XML_SDD_CuotasTesoreroSantanderWrite:fechaCreacion: ';print_r($fechaCreacion);	
	
	/*-----------------  Inicio $config datos Bancarios Europa Laica, y SEPA-CORE -------------------------------*/
	//$Partiendo de $arrayFormDatosElegidos, formaríamos -$config- para los datos generales de cabecera y algún otro más
 	
	$config = array(	"name"             => $arrayFormDatosElegidos['empresa_presentador'],  //"ASOCIACION EUROPA LAICA", 																
																		"IBAN"             => $arrayFormDatosElegidos['ctaBanco']['IBAN'],//ok  mejor desde formulario : "ES4200490001522411813269",Europa Laica B. Santander 
																		"BIC"              => $arrayFormDatosElegidos['BIC'], //"BSCHESMMXXX",//ok mejor desde formulario 
																		"creditor_id"      => $arrayFormDatosElegidos['idEmpresa'],//Europa Laica B. Santander :"ES89001G45490414" 
																		"country"          => $arrayFormDatosElegidos['estado'],//ES 									
																		"currency"         => $arrayFormDatosElegidos['moneda'],//será EUR, MEJOR SERÍA DESDE FORMULARIO ojo solo admite EUR																	 
																		"fechaCreacion"    => substr($fechaCreacion,0,19),//date("c");//será algo como "2015-02-12T15:19:21+00:00",Fecha ISO 8601 (añadido en PHP 5)	
																		"batch"            => true,//ok  "false", daría Invalid config file: batch is empty.	
                 	//"version"          => "2"	//Esquema de validación que usa el B.Santander 2015-11-15		= pain.008.001.02.xsd		(mejor desde formulario?)	
		                "version"          => $arrayFormDatosElegidos['versionFormatoSEPA'],	//Esquema de validación que usa el B.Santander 2015-11-15		= pain.008.001.02.xsd		(mejor desde formulario?)																			
																		'MsgId'            => 'PRE-'.$arrayFormDatosElegidos['cif_ordenante']//Después genera PRE-G45490414-2015-12-09 12:53:43 (mejor por formulario) 
																		                                     // tag <MsgId>1.1 para  B. Santander, debe ser único  y distinto de los sucesivos ficheros que se envíen, por eso se añadirá fecha																																																							                 																																																						
                );
	
 //echo '<br><br>1 modeloTesorero:SEPA_XML_SDD_CuotasTesoreroSantanderWrite:config: ';print_r($config);	
 /*-----------------  Fin $config datos Bancarios Europa Laica, y SEPA-CORE ---------------------------------*/	

	require_once '../../usuariosLibs/classes/SEPA_XML_SEPASDD/SEPASDD.php';//se carga la clase 	SEPASDD	
	
 try{
					$SEPASDD = new SEPASDD($config);//crea objeto de clase SEPASDD (adeudo directo)	
					
					$f = 0; //$fUltima = 3;//para pruebas					
					$fUltima = $arrDatosCobroCuotas['numFilas'];
					//echo '<br><br>1 modeloTesorero:SEPA_XML_SDD_CuotasTesoreroSantanderWrite:arrayFormDatosElegidos: fUltima: '.$fUltima;

					$numFactura = 1; //numeración de recibos para cada fecha de cobro 

					/*-------- Los valores necesarios para cada órden de cobro, están contenidos en array "$arrDatosCobroCuotas" obtenido en ----
							función previa "modeloTesorero.php:exportarCuotasXMLBancos(). Entre otros datos se pasa la "CUENTAIBAN" que se 
							habrá validado al introducicirla en Gestión de socios, el "BIC" que para CUENTAIBAN de España, tendrá el valor 
							de la entidad bancaria o el valor genérico "XXXXESXXXXX". 
							Para los otros países SEPA, sería necesario el valor BIC de la entidad bancaria de esa CUENTAIBAN.
							En caso de tener el datos del BIC y para evitar el error está función ya se habrá filtrado y  tratado en la 
							función prevía "modeloTesorero.php:exportarCuotasXMLBancos()							
						----------------------------------------------------------------------------------------------------------------------------*/
						
					/*----------- Inicio bucle para añadir órdenes de pago al array "$payment" con el método "SEPASDD->addPayment($payment)" 
					  para cada socio pendiente de pago y con IBAN a partir del array $arrDatosCobroCuotas y otros datos de $config o $arrayFormDatosElegidos 
					-----------------------------------------------------------------------------------------------------------------------------*/
					while ($f < $fUltima)
					{		
						//echo '<br><br>2-1 modeloTesorero:SEPA_XML_SDD_CuotasTesoreroSantanderWrite:arrayFormDatosElegidos: f: '.$f;							
						//echo '<br><br>2-2 modeloTesorero:SEPA_XML_SDD_CuotasTesoreroSantanderWrite:arrDatosCobroCuotas['resultadoFilas'][$f]['SECUENCIAADEUDOSEPA']: ';print_r($arrDatosCobroCuotas['resultadoFilas'][$f]['SECUENCIAADEUDOSEPA']);			
      
						require_once "./modelos/libs/eliminarAcentos.php";	
						$Apellidos_Nombre  =  cambiarAcentosEspeciales($arrDatosCobroCuotas['resultadoFilas'][$f]['Apellidos_Nombre']);//en modelos/libs/eliminarAcentos.php";//normas SEPA sin acentos				
						
					 if(strlen($Apellidos_Nombre) > 70)// limitar tamaño a norma SEPA	70
						{	$Apellidos_Nombre  = substr($Apellidos_Nombre,0,70);
						  //echo '<br><br>2-3 modeloTesorero:SEPA_XML_SDD_CuotasTesoreroSantanderWrite:Apellidos_Nombre: ';print_r($Apellidos_Nombre);
						}
						
						$identificadorSocio = date("Y-m-d").'-'.$arrDatosCobroCuotas['resultadoFilas'][$f]['NIF']; //Fecha-NIF, NO SE REPETIRÁ EN LOS ARCHIVOS
						
					 if(strlen($identificadorSocio) > 35) // POR SI ALGUIEN METE PASAPORTE
						{	$identificadorSocio  = substr($identificadorSocio ,0,35);
						  //echo '<br><br>2-4 modeloTesorero:SEPA_XML_SDD_CuotasTesoreroSantanderWrite:identificadorSocio: ';print_r($identificadorSocio);
						}
						
						/* Este array "$payment" con los datos de cada orden de cobro de un socio, se llena a partir de $arrDatosCobroCuotas y $arrayFormDatosElegidos, 
						   donde CuotaDonacionPendienteCobro = (CUOTAANIOSOCIO.IMPORTECUOTAANIOSOCIO-CUOTAANIOSOCIO.IMPORTECUOTAANIOPAGADA)	para contemplar abonada parte		
      */						
						
						$payment = array(
																							"name" 											 => $Apellidos_Nombre,//Debiera limitar tamaño a norma SEPA	70
																							"IBAN" 											 => $arrDatosCobroCuotas['resultadoFilas'][$f]['CUENTAIBAN'],
																							"BIC"  										 	=> $arrDatosCobroCuotas['resultadoFilas'][$f]['BIC'],//Ya buscado en función previa																													
																							"amount" 					 				=> $arrDatosCobroCuotas['resultadoFilas'][$f]['CuotaDonacionPendienteCobro']*100,//para 40.00 euros * 100=4000 céntimos																							
																							"type" 							 				=> $arrDatosCobroCuotas['resultadoFilas'][$f]['SECUENCIAADEUDOSEPA'],//"type" será RCUR o FRST						
																							//"EndToEndId"       =>	date("Y-m-d").'-'.$arrDatosCobroCuotas['resultadoFilas'][$f]['NIF'],//date("Y-m-d").'-'."NIF", = 2015-11-03-24115481G  
																							"EndToEndId"       =>	$identificadorSocio, //limitado tamaño a 35,
																							"collection_date"  => $arrayFormDatosElegidos['fechacobro']['anio']."-".$arrayFormDatosElegidos['fechacobro']['mes']."-".$arrayFormDatosElegidos['fechacobro']['dia'],//formato("Y-m-d"),
																							"mandate_id" 			 		=> date("Y-m-d").'-'.$arrDatosCobroCuotas['resultadoFilas'][$f]['Referencia_codSocio'],//podria ser el codUSER, o NUMDOCUMENTOMIEMBRO 
																							"mandate_date" 	 		=> $arrDatosCobroCuotas['resultadoFilas'][$f]['FECHAACTUALIZACUENTA'],//la fecha de la firma o bien para los ya cobrados otras veces 2009-10-31
																							"country" 							  => substr($arrDatosCobroCuotas['resultadoFilas'][$f]['CUENTAIBAN'],0,2), //País: ES
																							"description" 		 		=> "Recibo".date("d-m-Y").": ".$numFactura++." EMITIDO POR: ASOCIACION EUROPA LAICA, POR PAGO CUOTA  ".date("Y").".  CIF: ES-G45490414 TOTAL: ".
																																													$arrDatosCobroCuotas['resultadoFilas'][$f]['CuotaDonacionPendienteCobro']." euros" // Hasta 140 caracteres según SEPA																																															
																				  );

						//echo '<br><br>2-5 modeloTesorero:SEPA_XML_SDD_CuotasTesoreroSantanderWrite:payment_'.$f.': ';print_r($payment);																			
				  
						$SEPASDD->addPayment($payment);//añade cada orden de cobro	una a una
						
						$f++;
					}//while ($f < $fUltima)-Fin bucle para datos de cada socio -----------------------	
					
	    /*--------------- Fin bucle para añadir órdenes de pago para array "$payment"-----------------------------------------------*/ 
					
					/*--------- Inicio Validar con el esquema XSD correspondiente --------------------------------------------------------------*/
					$xmlResult = $SEPASDD->save();//Devuelve un string con etiquetas XML y los datos
									
					libxml_use_internal_errors(true);// Activa error handling para XML	 
					
					$SEPA_XML_SDD_CuotasTesoreroSantanderWrite['codError'] = '00000';	
     $SEPA_XML_SDD_CuotasTesoreroSantanderWrite['errorMensaje'] = '';
     $SEPA_XML_SDD_CuotasTesoreroSantanderWrite['textoComentarios'] = '';					
					
	    $validationConEsquema = $SEPASDD->validate($xmlResult);//valida que los datos recibidos comple con el esquema ../../usuariosLibs/classes/SEPA_XML_SEPASDD/pain.008.001.02.xsd					
					
					//echo '<br><br>3 modeloTesorero:SEPA_XML_SDD_CuotasTesoreroSantanderWrite:validationConEsquema: ';print_r($validationConEsquema);		
					
			  require_once '../../usuariosLibs/classes/SEPA_XML_SEPASDD/validationXMLconXSDException.php';//se carga la clase 	para validar estos errores
					
					if ($validationConEsquema === true)	
					{				  			
      //echo '<br><br>4-1 modeloTesorero:SEPA_XML_SDD_CuotasTesoreroSantanderWrite:xmlResult: ';print_r($xmlResult);

						$SEPA_XML_SDD_CuotasTesoreroSantanderWrite['cadenaTextoXML'] = $xmlResult;

					}
					else	//$validationConEsquema !== true	)		
					{//echo '<br><br>4-2 modeloTesorero:SEPA_XML_SDD_CuotasTesoreroSantanderWrite:validationConEsquema: ';print_r($validationConEsquema);				   
					
					 throw new validationXMLconXSDException("Error al validar con el esquema XSD pain.008.001.0n.xsd: " );//se dispara la Exception de classes/SEPA_XML_SEPASDD/validationXMLconXSDException.php				
					}
					
				}//try fin
				catch(validationXMLconXSDException $e) //para errores de validación con el esquema XSD pain.008.001.0n.xsd 
				{									
						$validationXMLconXSDExceptionErrors = $e->libxml_display_all_errors();
						
						//echo '<br><br>4-3 modeloTesorero:SEPA_XML_SDD_CuotasTesoreroSantanderWrite:validationXMLconXSDException: ';print_r($validationXMLconXSDExceptionErrors);	
						
						$SEPA_XML_SDD_CuotasTesoreroSantanderWrite['codError'] = $validationXMLconXSDExceptionErrors['codError'];				
						
						//se guardan todos los errores también incluye el código de error		
	     $SEPA_XML_SDD_CuotasTesoreroSantanderWrite['codError'] = '60000'; //errores varios pero se pueden considerar no lógicos
						$SEPA_XML_SDD_CuotasTesoreroSantanderWrite['errorMensaje'] .= trim($validationXMLconXSDExceptionErrors['errorMensaje'].
						                                                                   "Nombre socia/o: ".$payment["name"]." Num.Documento: ".$arrDatosCobroCuotas['resultadoFilas'][$f]['NIF']);
						$SEPA_XML_SDD_CuotasTesoreroSantanderWrite['textoComentarios']  .= "Nombre socia/o: ".$payment["name"]." Num.Documento: ".$arrDatosCobroCuotas['resultadoFilas'][$f]['NIF'].
                                                                         "IBAN: ".$arrDatosCobroCuotas['resultadoFilas'][$f]['CUENTAIBAN']."BIC: ".$arrDatosCobroCuotas['resultadoFilas'][$f]['BIC'];						
																																																														
      //echo '<br><br>4-4 modeloTesorero:SEPA_XML_SDD_CuotasTesoreroSantanderWrite:catch(validationXMLconXSDException): ';print_r($SEPA_XML_SDD_CuotasTesoreroSantanderWrite);	
				}
				catch(Exception $e) //para ortros tipos de errores de validación no de esta clase
				{ 				
						$SEPA_XML_SDD_CuotasTesoreroSantanderWrite['codError'] = '60000'; //errores varios pero se pueden considerar no lógicos
						$SEPA_XML_SDD_CuotasTesoreroSantanderWrite['errorMensaje'] = $e->getMessage().", Linea: ".$e->getLine().", Fichero: ".$e->getFile().
						                                                                 "Nombre socio/a: ".$payment["name"]." Num.Documento: ".$arrDatosCobroCuotas['resultadoFilas'][$f]['NIF'];	
						$SEPA_XML_SDD_CuotasTesoreroSantanderWrite['textoComentarios'] .= "Nombre socio/a: ".$payment["name"]." Num.Documento: ".$arrDatosCobroCuotas['resultadoFilas'][$f]['NIF'].
                                                                        "IBAN: ".$arrDatosCobroCuotas['resultadoFilas'][$f]['CUENTAIBAN']."BIC: ".$arrDatosCobroCuotas['resultadoFilas'][$f]['BIC'];			
																																																																										
     //echo '<br><br>4-5 modeloTesorero:SEPA_XML_SDD_CuotasTesoreroSantanderWrite: SEPA_XML_SDD_CuotasTesoreroSantanderWrite: ';print_r($SEPA_XML_SDD_CuotasTesoreroSantanderWrite);																																																																							
    }	
				/*--------- Fin Validar con el esquema XSD correspondiente --------------------------------------------------------------*/
				
    //echo '<br><br>5 modeloTesorero:SEPA_XML_SDD_CuotasTesoreroSantanderWrite: SEPA_XML_SDD_CuotasTesoreroSantanderWrite: ';print_r($SEPA_XML_SDD_CuotasTesoreroSantanderWrite);

	return 	$SEPA_XML_SDD_CuotasTesoreroSantanderWrite;
}
/*--------------- Fin SEPA_XML_SDD_CuotasTesoreroSantanderWrite_2020_12_03_ArchivoEnServidor ------*/

/*--------- Inicio crearEscribirArchivoSEPAXML.php (para exportarCuotasXMLBancos() B.Santander)------
DESCRIPCION: Se crea un archivo y se escribe en el string recibido en el parámetro "$cadenaTexto" y 
se guarda en un directorio del servidor. Controla divesos tipos de posibles errores													

RECIBE: 
- $cadenaTexto string con el contenido del archivo Archivo SEPA XML.php 
  (se forma en modeloTesorero.php:exportarCuotasXMLBancos:SEPA_XML_SDD_CuotasTesoreroSantanderWrite), 

- $directorioArchivo (sin nomArchivo viene de modeloTesorero.php:exportarCuotasXMLBancos()) que será 
  un path relativo al directorio raíz: $directorioArchivo= "/../upload/TESORERIA/SEPAXML_ISO20022" 
		esta por encima de raíz	con acceso restringido  

- $nomArchivo: nombre archivo incluida extensión, viene de modeloTesorero.php:exportarCuotasXMLBancos()
  ejemplo: SEPA_ISO20022CORE_2017-12-19H09-57-04.xml

DEVUELVE: un archivo que se guarda en un directorio recibido, un array con los campos de errores					
													
LLAMADA: modeloTesorero.php:exportarCuotasXMLBancos()
LLAMA: nada

OBSERVACIONES:  PHP 7.3.21
Aunque esta funcion se podría incluir dentro del modeloArchivos.php, para mayor indenpendencia 
seguridad frente o posibles cambios, la dejo en modeloTesorero.php, para uso exclusivo ya que es 
muy crítica.

Se podría definir: define('APLICATION_ROOT', getcwd());echo "<br />aplicacion-root: ".APLICATION_ROOT;
----------------------------------------------------------------------------------------------------*/

function crearEscribirArchivoSEPAXML($cadenaTexto,$directorioArchivo,$nomArchivo)	
{	
	//echo "<br /><br />0-1 modeloTesorero.php:crearEscribirArchivoSEPAXML:cadenaTextoXML: ";print_r($cadenaTexto);	
	//echo "<br /><br />0-2 modeloTesorero.php:crearEscribirArchivoSEPAXML:directorioArchivo: ";print_r($directorioArchivo);	
	//echo "<br /><br />0-3 modeloTesorero.php:crearEscribirArchivoSEPAXML:nomArchivo: ";print_r($nomArchivo);
	//echo "<br /><br />0-4 modeloTesorero.php:crearEscribirArchivoSEPAXML:directorio actual: ";echo getcwd();

 //require_once './modelos/modeloArchivos.php';

	$arrCrearEscribirArchivoSEPAXML['codError'] = '00000';
	$arrCrearEscribirArchivoSEPAXML['errorMensaje'] = '';	
	
	if ( (!isset($cadenaTexto) || empty($cadenaTexto)) || (!isset($directorioArchivo) || empty($directorioArchivo)) || (!isset($nomArchivo) || empty($nomArchivo)) )
	{
			$arrCrearEscribirArchivoSEPAXML['codError'] = '70620';
			$arrCrearEscribirArchivoSEPAXML['errorMensaje'] = 'ERROR: faltan algunos de los siguientes datos: contenido escribir en el archivo, directorio o nombre archivo ';	
		//	echo '<br><br>1-1 modeloTesorero.php:crearEscribirArchivoSEPAXML: ' ;print_r($arrCrearEscribirArchivoSEPAXML);
	} 
	else //!if ( (!isset($nomArchivo) || empty($nomArchivo)) || ( !isset($cadenaTexto) || empty($cadenaTexto))  )
	{ 			
			//---  Inicio Preparar path absoluto ----------------------------------
					
			//Aclaraciones sobre directorios: directorios públicos (por debajo de europalaica.com/public_html) y privados o restringidos (por encima de europalaica.com/public_html) 
			
			$directorioRoot = $_SERVER['DOCUMENT_ROOT'];//será:   "/home/virtualmin/europalaica.com/public_html"
			//echo "<br /><br />2-1 modeloTesorero.php:crearEscribirArchivoSEPAXML:directorioRoot: ";print_r($directorioRoot);// devuelve /home/virtualmin/europalaica.com/public_html		
   
			//$directorioArchivo = "/../upload/_FILES/TESORERIA/SEPAXML_ISO20022";//ok el directorio "upload" NO es público, estó un nivel más arriba del root acceso restringido solo con PHP		
					
			$directorioAbsoluto = realpath($directorioRoot.$directorioArchivo);//será: /home/virtualmin/europalaica.com/public_html/../upload/TESORERIA/SEPAXML_ISO20022
			
			//echo "<br><br>2-2 modeloTesorero.php:crearEscribirArchivoSEPAXML:directorioAbsoluto: ".$directorioAbsoluto;			//home/virtualmin/europalaica.com/public_html/../upload/TESORERIA/SEPAXML_ISO20022
			
		 $directorioAbsolutoMasArchivo = $directorioAbsoluto."/".$nomArchivo;//será: /home/virtualmin/europalaica.com/upload/TESORERIA/SEPAXML_ISO20022/SEPA_ISO20022CORE_2020-12-21H10-38-02.xml					
		
			//echo "<br><br>2-3 modeloTesorero.php:crearEscribirArchivoSEPAXML:directorioAbsolutoMasArchivo: ".$directorioAbsolutoMasArchivo;//será: /home/virtualmin/europalaica.com/upload/TESORERIA/SEPAXML_ISO20022/SEPA_ISO20022CORE_2020-12-28H10-47-31.xml
   
			//---  Fin Preparar path absoluto -------------------------------------
			
			
			/*------------- Inicio validar datos archivo: directorio -------------------------------*/

			//$directorioAbsoluto ='hhhh'; para probar errores
			if (!is_dir($directorioAbsoluto) )//ok: comprueba si es un directorio y también si existe
			{			
					$arrCrearEscribirArchivoSEPAXML["codError"] = '50000';//es un errror del sistema, 	antes '82060' error lógico//"3"		
					$arrCrearEscribirArchivoSEPAXML['errorMensaje'] = "ERROR el directorio para subir el archivo no existe o no es un directorio: -".$directorioAbsoluto."-";		
			}
			elseif (!is_writable($directorioAbsoluto) )//ok 
			{			
					$arrCrearEscribirArchivoSEPAXML["codError"] = '50000';//es un errror del sistema, 	antes '82060' error lógico//"3"		
					$arrCrearEscribirArchivoSEPAXML['errorMensaje'] = "ERROR el directorio para subir el archivo no permite escritura: -".$directorioAbsoluto."-";		
			}		
			elseif (strlen($directorioAbsolutoMasArchivo) > PHP_MAXPATHLEN)//muy poco probable superar PHP_MAXPATHLEN, pero decidí ponerlo
			{ 
			  /*	PHP_MAXPATHLEN: Número entero.Longitud máxima de un nombre completo de fichero (incluyendo el árbol de directorios bajo el cual se encuentra). 
							 echo "<br /><br />PHP_MAXPATHLEN: ".PHP_MAXPATHLEN;//4096 
					*/					
					$arrCrearEscribirArchivoSEPAXML['codError'] = '50000';//es un errror del sistema, 	antes '82060' error lógico//"3"		
					$arrCrearEscribirArchivoSEPAXML['errorMensaje'] = "ERROR: longitud del path del directorio -".$directorioAbsolutoMasArchivo."- supera el valor permitido por el sistema, 
					                                            debe reducir la longitud del nombre del archivo o guardar en un directorio de path más corto";			
			}	
			elseif (file_exists($directorioAbsolutoMasArchivo))//ok si ya existe ese archivo
			{ 	  						
					$arrCrearEscribirArchivoSEPAXML["codError"] = '50000';//es un errror del sistema, antes '82060' error lógico//"3"		
					$arrCrearEscribirArchivoSEPAXML['errorMensaje'] = "ERROR: al subir el archivo ya existe un archivo con ese nombre: ".$directorioAbsolutoMasArchivo;	
			}
			
			//echo "<br /><br />3-6 modeloTesorero.php:crearEscribirArchivoSEPAXML:arrCrearEscribirArchivoSEPAXML: ";print_r($arrCrearEscribirArchivoSEPAXML);						
			//*------------- Fin validar datos archivo :directorio ----------------------------------*/
				

			if ($arrCrearEscribirArchivoSEPAXML['codError'] == '00000')
   {	
				//*------------- Inicio  crear, escribir, cerrar archivo  ----------------------------------*/

 			//if (!$recursoArchivo = fopen($directorioAbsolutoMasArchivo, 'x'))//Creación y apertura para sólo escritura. Si ya existe devuelve false y también un error tipo E_WARNING. Si no existe se intenta crear (Puntero al principio archivo)
				if (!$recursoArchivo = fopen($directorioAbsolutoMasArchivo, 'w'))//Apertura para sólo escritura y si ya existe borra su contenido. Si no existe se intenta crear. (Puntero al principio archivo)				
				{ 
						$arrCrearEscribirArchivoSEPAXML['codError'] = '50010';
						$arrCrearEscribirArchivoSEPAXML['errorMensaje'] = " Error al abrir archivo ";
						$arrCrearEscribirArchivoSEPAXML['textoComentarios'] = " Error al Error al abrir archivo ";
				}
				else//if ($recursoArchivo = fopen($directorioAbsolutoMasArchivo, 'w') )
				{	
			  if (fwrite($recursoArchivo, $cadenaTexto) === false)					
					{ 
				   $arrCrearEscribirArchivoSEPAXML['codError'] = '50020';
							$arrCrearEscribirArchivoSEPAXML['errorMensaje'] = " Error al escribir en un archivo ";
							$arrCrearEscribirArchivoSEPAXML['textoComentarios'] = " Error al escribir en un archivo ";
					}						
		
					if (!fclose($recursoArchivo))
					{
				 		$arrCrearEscribirArchivoSEPAXML['codError'] = '50030';
							$arrCrearEscribirArchivoSEPAXML['errorMensaje'] .= " Error al cerrar archivo ";
							$arrCrearEscribirArchivoSEPAXML['textoComentarios'] = " Error al cerrar archivo ";
					}
			 }//else if ($recursoArchivo = fopen($directorioAbsolutoMasArchivo, 'w') )	
					
				//echo '<br><br>3-7 modeloTesorero.php:crearEscribirArchivoSEPAXML:arrCrearEscribirArchivoSEPAXML: ';print_r($arrCrearEscribirArchivoSEPAXML);
		 } 
		 	//*------------- Fin  crear, escribir, cerrar archivo  -------------------------------------*/
			
 }//else !if ( (!isset($nomArchivo) || empty($nomArchivo)) || ( !isset($cadenaTexto) || empty($cadenaTexto))  )
	
	//echo '<br><br>4 3-5 modeloTesorero.php:crearEscribirArchivoSEPAXML:arrCrearEscribirArchivoSEPAXML: ';print_r($arrCrearEscribirArchivoSEPAXML);
	
 return $arrCrearEscribirArchivoSEPAXML;
}
/*---------------- FIN crearEscribirArchivoSEPAXML.php ---------------------------------------------*/

/*------------ Inicio insertarOrdenesCobroRemesa (para exportarCuotasXMLBancos() B.Santander) -------
Se insertan los datos generales de informacón de esa remesa SEPA-XML en la tabla "REMESAS_SEPAXML"
y después las órdenes de cobro de cuota de cada socio generadas para la remesa en "ORDENES_COBRO", 
y que posterioremente servirán para actualizar el ESTADOCUOTA = ABONADA la tabla "CUOTAANIOSOCIO", 
Y "SOCIOS" (campo RCUR), y también como histórico de órdenes de cobro.

Controla e impide duplicidad de ORDENES_COBRO para los socios, si ya existiese un remesa previa 
que incluya a ese socio, es decir el campo ANOTADO_EN_CUOTAANIOSOCIO= NO.
En ese caso, impide la creación de un nuevo archivo de remesa, avisa del error, indica como corregirlo, 
y escribe el nombre y otros datos de los socios con repetición de remesa. 
(Esta duplicidad para un socio podría suceder, si la remesas se generan separadas por agrupaciones 
territoriales, si en ese intervalo un socio se cambia de agrupación, podría dar lugar a una repetición)

$arrInserOrdenesCobro[$f]['IMPORTECUOTAANIOPAGADA'] = ORDENES_COBRO.IMPORTECUOTAANIOPAGADA es el 
"importe pagado en la orden de cobro". 
En general coincidirá con CUOTAANIOSOCIO.IMPORTECUOTAANIOPAGADA, pero NO coincidirá si fuese 
el caso de ESTADOCUOTA = ABONADA-PARTE, en ese caso:
ORDENES_COBRO.IMPORTECUOTAANIOPAGADA < CUOTAANIOSOCIO.IMPORTECUOTAANIOPAGADA. Ver 2019-01-16.

RECIBE: $arrDatosOrdenesCobro procede de los datos personales y cuotas domiciliadas pendientes
(select "cadBuscarCuotasEmailSocios($codAreaCoordinacion,$condicionEmail,$arrayFormDatosElegidos)") 
y otros ($fechaCobroRemesa,$fechaAltasExentosPago,$fechaCreacion) que vienen del formulario de XML
y de modeloTesorero:exportarCuotasXMLBancos(). 
y $nombreArchivoSEPAXML,$directorioArchivoRemesa: que viene de cTesorero:XMLCuotasTesoreroSantander()
(y a su vez como opción podría venir desde formulario constantes o BBDD)  

DEVUELVE: un array con los controles de errores, y mensajes de comentarios para mostrar.

LLAMADA: desde modeloTesorero:exportarCuotasXMLBancos(),
LLAMA: usuariosConfig/BBDD/MySQL/configMySQL.php,	BBDD/MySQL/conexionMySQL.php:conexionDB()
modeloMySQL.php:buscarCadSql(),insertarUnaFila(),actualizarTabla()
modeloErrores.php:insertarError()

OBSERVACIONES: 2020-12-16: Cambios para PDO. Probado PHP 7.3.21 
Aquí no se incluye control transaciones, pero en caso de error se anularían las inserciones en la 
función de llamada modeloTesorero:exportarCuotasXMLBancos(), que si tiene rollback, probado

2019-01-16: Cambios para mejorar tratamiento en la tabla "ORDENES_COBRO" al incluir en ella 
nueva columna CUOTADONACIONPENDIENTEPAGO para guardar el valor de: 
'CuotaDonacionPendienteCobro'=(CUOTAANIOSOCIO.IMPORTECUOTAANIOSOCIO - CUOTAANIOSOCIO.IMPORTECUOTAANIOPAGADA), 
mejoras en el caso de ABONADA-PARTE: He añadido columna = ESTADOCUOTA_ANTES_REMESA, que tendrá los 
valores que había en CUOTAANIOSOCIO en el momento antes de crear la remesa y que podrán 
ser: PENDIENTE_COBRO, o ABONADA-PARTE. También IMPORTECUOTAANIOPAGADA_ANTES_REMESA,
IMPORTEGASTOSABONOCUOTA_ANTES_REMESA,FECHAPAGO_ANTES_REMESA y otras mejoras 
--------------------------------------------------------------------------------------------------*/
function insertarOrdenesCobroRemesa($arrDatosOrdenesCobro,$anioCuota,$fechaOrdenCobroRemesa,$fechaAltasExentosPago,$fechaCreacion,$nombreArchivoSEPAXML,$directorioArchivoRemesa)
{
	//echo "<br><br>0-1-modeloTesorero:insertarOrdenesCobroRemesa:arrDatosOrdenesCobro:"; print_r($arrDatosOrdenesCobro);
 //echo "<br><br>0-2-modeloTesorero:insertarOrdenesCobroRemesa:fechaOrdenCobroRemesa: "; print_r($fechaOrdenCobroRemesa);
	//echo "<br><br>0-3-modeloTesorero:insertarOrdenesCobroRemesa:fechaCreacion: "; print_r($fechaCreacion);
 //echo "<br><br>0-4-modeloTesorero:insertarOrdenesCobroRemesa:directorioArchivoRemesa: "; print_r($directorioArchivoRemesa);
 								 								
	require_once "./modelos/BBDD/MySQL/modeloMySQL.php";
 require_once './modelos/modeloErrores.php'; 	
	
	$resInsertarOrdenesCobro['nomScript'] = 'modeloTesorero.php';	
	$resInsertarOrdenesCobro['nomFuncion'] = 'insertarOrdenesCobroRemesa';
 $resInsertarOrdenesCobro['codError'] = '00000';
 $resInsertarOrdenesCobro['errorMensaje'] = '';	 
	$nomScriptFuncionError = ' modeloTesorero.php:insertarOrdenesCobroRemesa:(). Error: ';	
 			
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	require_once "BBDD/MySQL/conexionMySQL.php";
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);
	
	if ($conexionDB['codError'] !== "00000")
	{ $resInsertarOrdenesCobro  = $conexionDB;
			$textoErrores = $conexionDB['errorMensaje']." al conectarse a la base de datos. CODERROR: ".$conexionDB['codError'];				
	}
	else //$conexionDB['codError']=="00000"
	{			
 	/* if para controlar que no puede faltar ningún dato, para evitar posibles inconsistencias en las tablas ORDENES_COBRO y REMESAS_SEPAXML
		   es una medida extraordinaria, que no se debiera producir nunca, porque los datos ya vienen controlados
		   arrays: $arrDatosOrdenesCobro, $fechaOrdenCobroRemesa, $fechaAltasExentosPago (uso empty(array_filter() mejor para validar elementos de arrays )
		   string: $anioCuota, $fechaCreacion, $nombreArchivoSEPAXML 
		*/
		if ( (!isset($arrDatosOrdenesCobro) || empty(array_filter($arrDatosOrdenesCobro)) )  || 
		     (!isset($anioCuota) || empty($anioCuota)) ||
							(!isset($fechaOrdenCobroRemesa) || empty($fechaOrdenCobroRemesa) ) || 
							(!isset($fechaAltasExentosPago) || empty($fechaAltasExentosPago) ) ||
							(!isset($fechaCreacion) || empty($fechaCreacion) ) || 
							(!isset($nombreArchivoSEPAXML) || empty($nombreArchivoSEPAXML) )|| 
							(!isset($directorioArchivoRemesa) || empty($directorioArchivoRemesa) )																	
					)
		{$resInsertarOrdenesCobro['codError'] = '70601';
			$resInsertarOrdenesCobro['errorMensaje'] = 'ERROR: Faltan datos necesarios de órdenes de cobro: $arrDatosOrdenesCobro, o anioCuota, o fechaOrdenCobroRemesa, o $fechaAltasExentosPago, o $fechaCreacion, o $nombreArchivoSEPAXML, o $directorioArchivoRemesa.';	
			$textoErrores = $resInsertarOrdenesCobro['errorMensaje']. " Para insertar las órdenes de cobro en las tablas: ORDENES_COBRO y REMESAS_SEPAXML. ".$resInsertarOrdenesCobro['codError'];
		}	
		else //!	if ( (!isset($arrDatosOrdenesCobro) || empty($arrDatosOrdenesCobro)) || 
		{		
			/*----- Inicio BUSCAR SOCIO DUPLICADO EN TABLA 'ORDENES_COBRO' Y CON CAMPO "ANOTADO_EN_CUOTAANIOSOCIO= NO" ----	
				if (encontrado and ESTADOCUOTA = PENDIENTE_COBRO(orden aún pendiente de cobro por el banco ) 
				Entonces 	prepara aquí un comentario para mostrar en pantalla los datos socio repetido.				
			---------------------------------------------------------------------------------------------------------------*/	

			$contadorSocioRepetidoEnOtraRemesaPendienteCobro = 0;
			$f = 0;	$totalFilas = $arrDatosOrdenesCobro['numFilas'];				
			$resInsertarOrdenesCobro['codError'] ='00000';	
			$textoListado	= '<br />';	
			$arrFilas = $arrDatosOrdenesCobro['resultadoFilas'];		
			
			while (($f < $totalFilas) && $resInsertarOrdenesCobro['codError'] =='00000')
			{									
				$cadSql = "SELECT *	FROM ORDENES_COBRO 
												 		WHERE CODSOCIO = :codSocio AND ANIOCUOTA = :anioCuota AND ESTADOCUOTA = 'PENDIENTE-COBRO' ";											
															
	   $arrBind = array(':codSocio' => $arrFilas[$f]['Referencia_codSocio'], ':anioCuota' => $anioCuota );	  															
		
				$reArrOrdenesCobroSocio = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);//en modeloMySQL.php, probado error sistema
	
				//echo "<br><br>1-1 modeloTesorero:insertarOrdenesCobroRemesa:reArrOrdenesCobroSocio : ";print_r($reArrOrdenesCobroSocio);					
				
				if ($reArrOrdenesCobroSocio['codError'] !== '00000')			
				{ $resInsertarOrdenesCobro = $reArrOrdenesCobroSocio;		
						$textoErrores = $reArrOrdenesCobroSocio['errorMensaje']. " al buscar posibles socios con remesa repetida en la tabla ORDENES_COBRO. CODERROR: ".$reArrOrdenesCobroSocio['codError'];			
				}				
				else //$reArrOrdenesCobroSocio =='00000'
				{					
					if ($reArrOrdenesCobroSocio['numFilas'] > 0)
					{ $arrOrdenesCobro = $reArrOrdenesCobroSocio['resultadoFilas'][0];
							//echo "<br><br>1-1b modeloTesorero:insertarOrdenesCobroRemesa:arrOrdenesCobro: ";print_r($arrOrdenesCobro);					
				
							$arrSociosRepetidosEnOtraRemesaPendienteCobro[$contadorSocioRepetidoEnOtraRemesaPendienteCobro]['Apellidos_Nombre'] = $arrFilas[$f]['Apellidos_Nombre'];
							$arrSociosRepetidosEnOtraRemesaPendienteCobro[$contadorSocioRepetidoEnOtraRemesaPendienteCobro]['CODSOCIO'] = $arrOrdenesCobro['CODSOCIO'];
							$arrSociosRepetidosEnOtraRemesaPendienteCobro[$contadorSocioRepetidoEnOtraRemesaPendienteCobro]['FECHAORDENCOBRO'] = $arrOrdenesCobro['FECHAORDENCOBRO'];		
							$arrSociosRepetidosEnOtraRemesaPendienteCobro[$contadorSocioRepetidoEnOtraRemesaPendienteCobro]['NOMARCHIVOSEPAXML'] = $arrOrdenesCobro['NOMARCHIVOSEPAXML'];			
							$arrSociosRepetidosEnOtraRemesaPendienteCobro[$contadorSocioRepetidoEnOtraRemesaPendienteCobro]['CODAGRUPACION'] = $arrOrdenesCobro['CODAGRUPACION'];		
							$arrSociosRepetidosEnOtraRemesaPendienteCobro[$contadorSocioRepetidoEnOtraRemesaPendienteCobro]['NOMAGRUPACION'] = $arrFilas[$f]['Agrupacion_Actual']; 							
							$arrSociosRepetidosEnOtraRemesaPendienteCobro[$contadorSocioRepetidoEnOtraRemesaPendienteCobro]['IMPORTECUOTAANIOSOCIO'] = $arrOrdenesCobro['IMPORTECUOTAANIOSOCIO'];
							$arrSociosRepetidosEnOtraRemesaPendienteCobro[$contadorSocioRepetidoEnOtraRemesaPendienteCobro]['CUOTADONACIONPENDIENTEPAGO'] = $arrOrdenesCobro['CUOTADONACIONPENDIENTEPAGO'];				
					
							//echo "<br><br>1-2 modeloTesorero:insertarOrdenesCobroRemesa:arrSociosRepetidosEnOtraRemesaPendienteCobro: ";print_r($arrSociosRepetidosEnOtraRemesaPendienteCobro);								
							
							//-- Formar texto para mostrar datos de órdenes de cobros previas aún no efectuadas y no actualizas en CUOTAANIOSCIO -----			
							$contadorSocioRepetidoEnOtraRemesaPendienteCobro++;
							
							$textoListado .= "<br />".$contadorSocioRepetidoEnOtraRemesaPendienteCobro."- Nombre: ".$arrFilas[$f]['Apellidos_Nombre'].",  Fecha orden cobro: ".$arrOrdenesCobro['FECHAORDENCOBRO'].
																																".  Nom. archivo SEPAXML: ".$arrOrdenesCobro['NOMARCHIVOSEPAXML'].
																																". Importe cuota soci/a: ".$arrOrdenesCobro['IMPORTECUOTAANIOSOCIO'].
																																". Importe cuota_donación pendiente pago: ".$arrOrdenesCobro['CUOTADONACIONPENDIENTEPAGO'].//añadido
																																" €.  Nom. Agrupación actual: ".$arrFilas[$f]['Agrupacion_Actual']; 
					}         
				}
				$f++;
			}//while (($f < $totalFilas) && $resInsertarOrdenesCobro['codError'] =='00000')
			//----- FIN BUSCAR SOCIO DUPLICADO 'ORDENES_COBRO' ----------------------------------------------------------------	
	
			if ($contadorSocioRepetidoEnOtraRemesaPendienteCobro !== 0 || $resInsertarOrdenesCobro['codError'] !== '00000')
			{	if ($contadorSocioRepetidoEnOtraRemesaPendienteCobro !== 0)//Hay órdenes de cobros previas aún no efectuadas y no actualizas en CUOTAANIOSCIO -----			
					{
						$textoErrores = "Error: Al intentar generar esta remesa, se han encontrado <strong>- $contadorSocioRepetidoEnOtraRemesaPendienteCobro -</strong> socios/as, 
																							que ya están anotados en otra remesa generada previamente y aún pendiente de cobro, y daría lugar a cobrar dos veces a los socios/as escritos más abajo.
																							<br /><br />Soluciones: <br />a)- Si la remesa anterior SÍ está enviada al banco, para generar la nueva remesa hay que excluir a los socios/as de las lista de abajo, 
																							poniendo ORDENARCOBROBANCO ='NO' en -Cuotas socios/as -> Pago Cuota.<br />b)- Si la remesa anterior NO está enviada al banco, se podría eliminar la anterior remesa, y generarlas de nuevo,
																							o también hacer la solución a)".$textoListado;
																								
						$resInsertarOrdenesCobro['codError'] = '80001';//error lógico
						$resInsertarOrdenesCobro['errorMensaje'] = $textoErrores;
					}
     //echo "<br><br>1-2 modeloTesorero:insertarOrdenesCobroRemesa:resInsertarOrdenesCobro: ";print_r($resInsertarOrdenesCobro);									
			}
			else//if ($contadorSocioRepetidoEnOtraRemesaPendienteCobro == 0 && $resInsertarOrdenesCobro['codError'] == '00000'):NO Hay órdenes de cobros previas aún no efectuadas y no actualizas en CUOTAANIOSCIO -----		
			{				
				//-- Inicio preparar-insertarUnaFila('REMESAS_SEPAXML) -----------
				$arrValoresInserRemesa['NOMARCHIVOSEPAXML'] = $nombreArchivoSEPAXML;			
    $arrValoresInserRemesa['DIRECTORIOARCHIVOREMESA'] = $directorioArchivoRemesa;				
				$arrValoresInserRemesa['FECHA_CREACION_ARCHIVO_SEPA'] = $fechaCreacion; 				
				$arrValoresInserRemesa['FECHAORDENCOBRO'] = $fechaOrdenCobroRemesa;
				$arrValoresInserRemesa['FECHAPAGO'] = NULL; //PARA ACTUALIZAR
				$arrValoresInserRemesa['FECHAANOTACIONPAGO'] = NULL; //PARA ACTUALIZAAR			
				$arrValoresInserRemesa['ANIOCUOTA'] = $anioCuota;
				$arrValoresInserRemesa['FECHAALTASEXENTOSPAGO'] = $fechaAltasExentosPago;				
				$arrValoresInserRemesa['ANOTADO_EN_CUOTAANIOSOCIO'] ='NO';	 
				$arrValoresInserRemesa['IMPORTEGASTOSREMESA'] = 0.00; //aún no se ha cobado
				//$arrValoresInserRemesa['NUMRECIBOS'] = $contadorNumRecibos;// al final
				//$arrValoresInserRemesa['IMPORTEREMESA'] = $acumuladorImporteRemesa;// al final					
				$arrValoresInserRemesa['CODUSER'] = $_SESSION['vs_CODUSER'];//del gestor que emite la remesa
				$arrValoresInserRemesa['OBSERVACIONES'] = "Nombre archivo XML: ".$nombreArchivoSEPAXML.". Gestor CODUSER: ".$_SESSION['vs_CODUSER'];		
			
		  //	echo "<br><br>2-2 modeloTesorero:insertarOrdenesCobroRemesa:nombreArchivoSEPAXML: ";print_r($nombreArchivoSEPAXML); 			
			
				$reInsertarRemesa = insertarUnaFila('REMESAS_SEPAXML',$arrValoresInserRemesa,$conexionDB['conexionLink']);		
				//echo "<br><br>2-3 modeloTesorero:insertarOrdenesCobroRemesa:reInsertarRemesa: ";print_r($reInsertarRemesa);				
				
				//-- Fin preparar-insertarUnaFila('REMESAS_SEPAXML) -----------
				
				if ($reInsertarRemesa['codError']!=='00000')			
				{ $resInsertarOrdenesCobro = $reInsertarRemesa;
						$textoErrores = $reInsertarRemesa['errorMensaje']. " al insertar una fila en la tabla REMESAS_SEPAXML. CODERROR: ".$reInsertarRemesa['codError'];						
				}	
				else //($reInsertarRemesa['codError']=='00000')	
				{			
					//----- Inicio insertar todas las Filas de 'ORDENES_COBRO' ----------------------------------						
					$acumuladorImporteRemesa = 0;
					$contadorNumRecibos = 0;
					$contadorCuotasAnioSociosActualizados =0;			
					
					//----- Inicio preparar datos para insertarVariasFilas('ORDENES_COBRO'..)
					$arrFilas = $arrDatosOrdenesCobro['resultadoFilas'];		
					
					$f = 0;
					$totalFilas = $arrDatosOrdenesCobro['numFilas'];				
					$resInsertarOrdenesCobro['codError'] ='00000';			
					
					while (($f < $totalFilas) && $resInsertarOrdenesCobro['codError'] =='00000')		
					{								
						$arrInserOrdenesCobro[$f]['NOMARCHIVOSEPAXML'] = $nombreArchivoSEPAXML;		
						$arrInserOrdenesCobro[$f]['CODSOCIO'] = $arrFilas[$f]['Referencia_codSocio'];								
						$arrInserOrdenesCobro[$f]['FECHAORDENCOBRO'] = $fechaOrdenCobroRemesa;
						$arrInserOrdenesCobro[$f]['ANIOCUOTA'] = $anioCuota;
						
						$arrInserOrdenesCobro[$f]['SECUENCIAADEUDOSEPA'] = $arrFilas[$f]['SECUENCIAADEUDOSEPA'];
						$arrInserOrdenesCobro[$f]['FECHAACTUALIZACUENTA'] = $arrFilas[$f]['FECHAACTUALIZACUENTA'];		
						$arrInserOrdenesCobro[$f]['CUENTAIBAN'] = $arrFilas[$f]['CUENTAIBAN'];								
			
						$arrInserOrdenesCobro[$f]['ANOTADO_EN_CUOTAANIOSOCIO'] ='NO';
						$arrInserOrdenesCobro[$f]['FECHAPAGO'] = NULL;
						$arrInserOrdenesCobro[$f]['FECHAANOTACION'] = NULL;
						$arrInserOrdenesCobro[$f]['IMPORTEGASTOSABONOCUOTA'] = 0.00;	
						$arrInserOrdenesCobro[$f]['IMPORTEGASTOSDEVOLUCION'] = 0.00;		
						$arrInserOrdenesCobro[$f]['FECHADEVOLUCION'] = '0000-00-00';	
						$arrInserOrdenesCobro[$f]['MOTIVODEVOLUCION'] = NULL;
						
						$arrInserOrdenesCobro[$f]['ESTADOCUOTA'] = 'PENDIENTE-COBRO';// ESTADO PAGO ORDEN			

						$arrInserOrdenesCobro[$f]['CUOTADONACIONPENDIENTEPAGO'] = $arrFilas[$f]['CuotaDonacionPendienteCobro'];//CuotaDonacionPendienteCobro=(CUOTAANIOSOCIO.IMPORTECUOTAANIOSOCIO-CUOTAANIOSOCIO.IMPORTECUOTAANIOPAGADA)	
						
						/*$arrInserOrdenesCobro[$f]['IMPORTECUOTAANIOPAGADA'] = ORDENES_COBRO.IMPORTECUOTAANIOPAGADA es el 
								"importe pagado en la orden de cobro". En general coincidirá con CUOTAANIOSOCIO.IMPORTECUOTAANIOPAGADA,
								pero NO coincidirá si fuese el caso de ESTADOCUOTA = ABONADA-PARTE, 
								en ese caso ORDENES_COBRO.IMPORTECUOTAANIOPAGADA < CUOTAANIOSOCIO.IMPORTECUOTAANIOPAGADA. */								

						$arrInserOrdenesCobro[$f]['IMPORTECUOTAANIOPAGADA'] = 0.00;//0€ antes actualizar cobro remesa, después al actualizar=CUOTADONACIONPENDIENTEPAGO €, al devolver=0€							
						
						/*En tabla "ORDENES_COBRO": solo puede estar ESTADOCUOTA=PENDIENTE-COBRO o NOABONADA-DEVUELTA, en tabla "CUOTAANIOSOCIO", también podría ser "ABONADA-PARTE",	EXENTO o NOABONADA*/
						
						$arrInserOrdenesCobro[$f]['ESTADOCUOTA_ANTES_REMESA'] = $arrFilas[$f]['ESTADOCUOTA'];//estará com PENDIENTE-COBRO o ABONADA-PARTE, se guardará sin posteriores modificaciones
						$arrInserOrdenesCobro[$f]['IMPORTECUOTAANIOSOCIO'] = $arrFilas[$f]['IMPORTECUOTAANIOSOCIO'];//está bien viene de CUOTAANIOSOCIO se guardará sin posteriores modificaciones 								
						$arrInserOrdenesCobro[$f]['IMPORTECUOTAANIOEL'] = $arrFilas[$f]['IMPORTECUOTAANIOEL'];	// no se modificaría después pago o devolución 
						$arrInserOrdenesCobro[$f]['IMPORTECUOTAANIOPAGADA_ANTES_REMESA'] = $arrFilas[$f]['IMPORTECUOTAANIOPAGADA'];//viene de CUOTAANIOSOCIO.IMPORTECUOTAANIOPAGADA será 0.00, expcepto en caso de "ABONADA-PARTE" 
						
						$arrInserOrdenesCobro[$f]['IMPORTEGASTOSABONOCUOTA_ANTES_REMESA'] = $arrFilas[$f]['IMPORTEGASTOSABONOCUOTA'];//se guardará sin posteriores modificaciones	
						
						$arrInserOrdenesCobro[$f]['FECHAPAGO_ANTES_REMESA'] = $arrFilas[$f]['FECHAPAGO'];//se guardará sin posteriores modificaciones		

						$arrInserOrdenesCobro[$f]['CODCUOTA'] = $arrFilas[$f]['CODCUOTA'];		
						$arrInserOrdenesCobro[$f]['CODAGRUPACION'] = $arrFilas[$f]['CODAGRUPACION'];						
						$arrInserOrdenesCobro[$f]['NOMAGRUPACION'] = $arrFilas[$f]['Agrupacion_Actual'];		
						
						$arrInserOrdenesCobro[$f]['OBSERVACIONES'] = "Nombre archivo XML: ".$nombreArchivoSEPAXML.". Gestor CODUSER: ".$_SESSION['vs_CODUSER'];		
																																																				
						//echo "<br><br>3-0 modeloTesorero:insertarOrdenesCobroRemesa:arrInserOrdenesCobro: ";print_r($arrInserOrdenesCobro);
						
						$acumuladorImporteRemesa += $arrFilas[$f]['CuotaDonacionPendienteCobro'];//CuotaDonacionPendienteCobro=(CUOTAANIOSOCIO.IMPORTECUOTAANIOSOCIO-CUOTAANIOSOCIO.IMPORTECUOTAANIOPAGADA)									
								
						
						//----- Inicio insertarFila 'ORDENES_COBRO' -------------------------------------------			
						//echo "<br><br>3-2a modeloTesorero:insertarOrdenesCobroRemesa:reInsertarFilaOrdenCobro: ";print_r($reInsertarFilaOrdenCobro);		
				
						$reInsertarFilaOrdenCobro = insertarUnaFila('ORDENES_COBRO',$arrInserOrdenesCobro[$f],$conexionDB['conexionLink']);//probado error y genrea rollback en anterior función: insertarUnaFila('REMESAS_SEPAXML'...)
						//echo "<br><br>3-2b modeloTesorero:insertarOrdenesCobroRemesa:reInsertarFilaOrdenCobro: ";print_r($reInsertarFilaOrdenCobro);
						
						if ($reInsertarFilaOrdenCobro['codError']!=='00000')			
						{ $resInsertarOrdenesCobro = $reInsertarFilaOrdenCobro;
					
								$textoErrores = $reInsertarFilaOrdenCobro['errorMensaje']. " al insertar filas en la tabla ORDENES_COBRO. CODERROR: ".$reInsertarFilaOrdenCobro['codError'];			
						}				
						else //($reActSocio['codError']!='00000')		
						{if ($reInsertarFilaOrdenCobro['numFilas'] != 0)
							{ $contadorNumRecibos++;
							}			
						} 				
						//-------------- Fin insertarFila'ORDENES_COBRO' ---------------------------------------							
						$f++;						
					}//fin while (($f < $totalFilas) && $resInsertarOrdenesCobro['codError'] =='00000')		
						
					//----- Fin insertar todas las Filas de 'ORDENES_COBRO' ---------------------------------------				

					//----------> Inicio actualizar acumuladores en tabla REMESAS_SEPAXML	-------------------	
					$arrayCondicionesActRemesa['NOMARCHIVOSEPAXML']['valorCampo']= $nombreArchivoSEPAXML;			
					$arrayCondicionesActRemesa['NOMARCHIVOSEPAXML']['operador']= '=';
					$arrayCondicionesActRemesa['NOMARCHIVOSEPAXML']['opUnir']= ' ';
					
					$arrayDatosActRemesa['IMPORTEREMESA'] = $acumuladorImporteRemesa;
					$arrayDatosActRemesa['NUMRECIBOS'] = $contadorNumRecibos;	
					
					//echo "<br><br>3-4a modeloTesorero:insertarOrdenesCobroRemesa:arrayDatosActRemesa: ";print_r($arrayDatosActRemesa);echo "<br><br>";									
						
					$reActRemesa = actualizarTabla('REMESAS_SEPAXML',$arrayCondicionesActRemesa,$arrayDatosActRemesa,$conexionDB['conexionLink']);
					
					//echo "<br><br>3-4b modeloTesorero:insertarOrdenesCobroRemesa:reActRemesa: ";print_r($reActRemesa);echo "<br><br>";										
									
					if ($reActRemesa['codError'] !=='00000')			
					{	$resInsertarOrdenesCobro = $reActRemesa;										
							$textoErrores = $reActRemesa['errorMensaje']." al actualizar tabla CUOTAANIOSOCIO. CODERROR: ".$reActRemesa['codError'];
					}	
					/*else //($reActSocio['codError']!='00000')---		creo que sobra ----
					{ if ($reActRemesa['numFilas'] != 0)//será error, no hay socios en la/s agrupaciones elegidas???
						{ $contadorRemesaActualizados++; //para qué sirve, creo que para nada
						}			
					} */
					//----------> Fin actualizar acumuladores en tabla REMESAS_SEPAXML	-------------------	

				}//else	($reInsertarRemesa['codError']=='00000')		
			}//else ($contadorSocioRepetidoEnOtraRemesaPendienteCobro == 0 && $resInsertarOrdenesCobro['codError'] == '00000')
		}//else !if ( (!isset($arrDatosOrdenesCobro) || empty($arrDatosOrdenesCobro)) || ...
		
	 //echo "<br><br>4-4 modeloTesorero:insertarOrdenesCobroRemesa:resInsertarOrdenesCobro: ";print_r($resInsertarOrdenesCobro);			
		//echo "<br><br>4-4-a modeloTesorero:insertarOrdenesCobroRemesa:arrSociosRepetidosEnOtraRemesaPendienteCobro: ";print_r($arrSociosRepetidosEnOtraRemesaPendienteCobro);	
	
		//----------------------- Inicio tratamiento de errores --------------------------------------
		if ($resInsertarOrdenesCobro['codError'] !== '00000')
		{//echo "<br><br>5-1 modeloTesorero:insertarOrdenesCobroRemesa:resInsertarOrdenesCobro: ";print_r($resInsertarOrdenesCobro);
			//echo "<br><br>5-2 modeloTesorero:insertarOrdenesCobroRemesa:textoErrores: ";print_r($textoErrores);	  
			
			if ( isset($contadorSocioRepetidoEnOtraRemesaPendienteCobro ) && $contadorSocioRepetidoEnOtraRemesaPendienteCobro !== 0)
			{ $arrMensaje['textoComentarios'] = $textoErrores."<br /><br />. Gestor CODUSER: ".$_SESSION['vs_CODUSER'].": ".$nomScriptFuncionError;			
			}
			else
			{	
					$arrMensaje['textoComentarios'] = " ERROR: No se han podido insertar las órdenes de cobro de cuotas domiciliadas correspondientes a esta remesa de cobros bancarios, 
																																							y no se ha generado el archivo SEPA-XML. Vuelva a intentarlo pasado un tiempo y en caso de siga el error, 
																																							avise al administrador de esta aplicación, e indique el mensaje de error.<br /><br/> ".
																																							$textoErrores."br /><br />. Gestor CODUSER: ".$_SESSION['vs_CODUSER'].": ".$nomScriptFuncionError;		
			}
			$arrInsertarErrores = $resInsertarOrdenesCobro;//si hay error en rollBack, solo incluiría los errores de $resDeshacerTrans; 	
			$arrInsertarErrores['textoComentarios'] = $textoErrores.": ".$nomScriptFuncionError.". Gestor CODUSER: ".$_SESSION['vs_CODUSER'];																								
			$resInsertarOrdenesCobro['arrMensaje']['textoComentarios'] = $arrMensaje['textoComentarios'];	
			
			$resInsertarErrores = insertarError($arrInsertarErrores);
		
			if ($resInsertarErrores['codError'] !== '00000')
			{ $resActualizarCuotasVigentesEL['errorMensaje'] .= $resInsertarErrores['errorMensaje'];
					$arrMensaje['textoComentarios'] .= "Error del sistema al tratar ERRORES, vuelva a intentarlo pasado un tiempo ";
			}		
		}//if (insertarOrdenesCobro['codError']!=='00000')

	}//else $conexionDB['codError']=="00000"
	//----------------------- Fin tratamiento de errores --------------------------------------		

 //echo "<br>6-modeloTesorero:insertarOrdenesCobroRemesa:resInsertarOrdenesCobro: "; print_r($resInsertarOrdenesCobro);
 
 return $resInsertarOrdenesCobro;
}
/*----------------------------- Fin insertarOrdenesCobroRemesa --------------------------------------*/


/*---------------------------- Inicio exportarCuotasExcelBancos --- (Antes para Tríodos) --------------
--anteriormente para Tríodos-- 
								
Genera y exporta a un archivo Excel, órdenes de pago de las cuotas de los socios, (se utilizaba para
las órdenes de cobro en B. Tríodos) y ahora también es útil para uso interno de tesorería cuando, 
se genera y descarga a continuación de generar el archivo XML SEPA para el B. Santander 
(con los mismos criterios de selección) y así el Excel puede servir para contrastar los totales y
otros datos y como un listado para anotar las devoluciones e incidencias en la remesa enviada.

- LLama a modeloTesorero:cadBuscarCuotasEmailSocios() para formar la cadena select para buscar los
datos de todas las cuotas de los socios según la selección (agrupaciones territorial, año, 
Cuenta banco España o SEPA,	ESTADOCUOTA), condicionFechaAltaExentosPago, ...), a partir de los datos
recibidos en cTesorero.php desde el formulario. 

Y además siempre INCLUIRÁ:
- socios que estén de alta e incluye: alta,alta-sin-password-excel,alta-sin-password-gestor
- ORDENARCOBROBANCO ="SI" es condición necesaria para que se incluya en la lista	de cobros
- LOS ESTADOS DE CUOTAS: 'PENDIENTE-COBRO','ABONADA-PARTE' (las cuotas reducidas también se incluyen)
		
Se ejecuta la SELECT y se obtiene un array "$arrDatosCobroCuotas" con los datos
de cada socio para la orden de cobro de su cuota que tiene domiciliada.

RECIBE: 
- $arrayFormDatosElegidos: (año cuota, fecha exención pago, cuenta banco España o SEPA,
  agrupaciones elegidas) procedente del formulario vistas/tesorero/exportarCuotasExcelBancos.php
- $codAreaCoordinacion=%,$condicionEmail='%' que proceden del cTesorero.php ( o formulario)

DEVUELVE: array "$resExportarCuotasXMLBancos" con los mensajes correspondientes y códigos de error, 
además de crear el archivo SEPA.
													
LLAMADA: cTesorero:excelCuotasTesoreroBancos()

LLAMA: modeloTesorero.php:cadBuscarCuotasEmailSocios()
       modeloMySQL.php:buscarCadSql()
       modelos/libs/exportarExcel.php:exportarExcelCuotasTes()
							modeloErrores.php:insertarError()

OBSERVACIONES: PHP 7.3.21, esta función no usa directamente PDO pero si algunas 
de las funciones llamadas.

OJO NO PONER NINGUNA SALIDA A PANTALLA (echo, etc.) No poner echo: daría error
al formar el buffer de salida a excel ya que utiliza "header()" y no puede 
haber ningúna salida delante.		Mejor que esté desactivado E-NOTICE

NOTA:
Esta función también se podría utilizar con /libs/exportarExcel.php:exportarExcelCuotasTesEnServidor(),
y en esa caso el arcivo Excel se guadaría en un directorio del servidor en lugar de descargarse	
--------------------------------------------------------------------------------------------------*/
function exportarCuotasExcelBancos($codAreaCoordinacion,$condicionEmail,$arrayFormDatosElegidos)	
{
	//echo "<br><br>0-1 modeloTesorero:exportarCuotasExcelBancos:arrayFormDatosElegidos: ";print_r($arrayFormDatosElegidos);

 require_once "./modelos/BBDD/MySQL/modeloMySQL.php";					
	require_once './modelos/modeloErrores.php'; 
	
	$resExportarCuotasBancos['nomScript'] = 'modeloTesorero.php';
	$resExportarCuotasBancos['nomFuncion'] = 'exportarCuotasExcelBancos';	
	$resExportarCuotasBancos['codError'] = '00000';
	$resExportarCuotasBancos['errorMensaje'] = '';
	$nomScriptFuncionError = "modeloTesorero.php:exportarCuotasExcelBancos(). Error: ";
	
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	require_once "./modelos/BBDD/MySQL/conexionMySQL.php";	
		
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);
	
	if ($conexionDB['codError'] !== '00000')	
	{$resExportarCuotasBancos = $conexionDB;
	}     
	else //if ($conexionDB['codError']=='00000')
	{		
		/*---------- Inicio Buscar Datos de Cobro Cuotas socios para archivo Excel --------------------------------------------*/
	
		/*if de control de existencia de "$codAreaCoordinacion, $arrayFormDatosElegidos" ya que son datos necesarios y críticos 
		  Nota: Ya no sería necesario este if, porque también se controla $arrayFormDatosElegidos en "cadBuscarCuotasEmailSocios()"  
		*/		
	 if ( (!isset($arrayFormDatosElegidos['anioCuotasElegido']) || empty($arrayFormDatosElegidos['anioCuotasElegido']) ) || 
							(!isset($arrayFormDatosElegidos['fechaAltaExentosPago'] ) || empty(array_filter($arrayFormDatosElegidos['fechaAltaExentosPago'])) ) || 
						 (!isset($arrayFormDatosElegidos['paisCC'] ) || empty($arrayFormDatosElegidos['paisCC'])) ||
							(!isset($arrayFormDatosElegidos['agrupaciones'] ) || empty(array_filter($arrayFormDatosElegidos['agrupaciones'] )) ) ||
							(!isset($codAreaCoordinacion ) || empty($codAreaCoordinacion ))	|| (!isset($condicionEmail) || empty($condicionEmail))	)						
		{
			$resExportarCuotasBancos['codError'] = '70601';//probado error sería un error de sistema pues no se podrá formar la select correctamente
			$resExportarCuotasBancos['errorMensaje'] = 'ERROR: Faltan datos necesarios para buscar datos de cuotas pendientes de cobro de socios/as: algún campo de $arrayFormDatosElegidos';			 									
			$resExportarCuotasBancos['textoComentarios'] = date('Y-m-d:H:i:s')."Función: ".$nomScriptFuncionError." :exportarCuotasExcelBancos(). Gestor CODUSER: ".$_SESSION['vs_CODUSER'];		
			$arrInsertarErrores = $resExportarCuotasBancos;		//seria para grabar errores al final				
			//echo "<br><br>1-1 modeloTesorero:exportarCuotasExcelBancos:resExportarCuotasBancos: ";print_r($resExportarCuotasBancos);
		}	
		else //!	if ( (!isset($arrayFormDatosElegidos) || empty($arrayFormDatosElegidos)) ... || 
		{
			//podrían venir de cTesorero.php	o desde formulario
			$arrayFormDatosElegidos['ESTADO'] = 'alta';//solo se cobra a socios que estén de alta e incluye: alta,alta-sin-password-excel,alta-sin-password-gestor
			$arrayFormDatosElegidos['estadosCuotas'] = array('PENDIENTE-COBRO'=>'PENDIENTE-COBRO','ABONADA-PARTE'=>'ABONADA-PARTE');		
			$arrayFormDatosElegidos['ORDENARCOBROBANCO'] = 'SI'; //se excluyen los que tengan ['ORDENARCOBROBANCO']='NO' elegido por tesorero, aunque esté pendiente de cobro; 		  
			
	  //echo "<br><br>1-2 modeloTesorero:exportarCuotasExcelBancos:arrayFormDatosElegidos: ";print_r($arrayFormDatosElegidos);			

			$arrSQLBuscarCuotasSocios =	cadBuscarCuotasEmailSocios($codAreaCoordinacion,$condicionEmail,$arrayFormDatosElegidos);//en modeloTessorero.php, probado error	
			
			//echo "<br><br>2-1 modeloTesorero:exportarCuotasExcelBancos:arrSQLBuscarCuotasSocios: ";print_r($arrSQLBuscarCuotasSocios);
			
			if ($arrSQLBuscarCuotasSocios['codError'] !== '00000')		
			{ 
					$resExportarCuotasBancos['codError'] = $arrSQLBuscarCuotasSocios['codError'];
					$resExportarCuotasBancos['errorMensaje'] = $arrSQLBuscarCuotasSocios['errorMensaje'];
					$textoErrores = "ERROR: Al buscar datos de cuotas pendientes de cobro de socios/as para Excel B.Tríodos. CODERROR: ".$resExportarCuotasBancos['codError'].$resExportarCuotasBancos['errorMensaje'];						 
					$resExportarCuotasBancos['textoComentarios'] = date('Y-m-d:H:i:s')." Función: ".$nomScriptFuncionError."cadBuscarCuotasEmailSocios(). Gestor CODUSER: ".$_SESSION['vs_CODUSER'].$textoErrores;							
     $arrInsertarErrores = $resExportarCuotasBancos;		//seria para grabar errores al final							
			}
			else //$arrSQLBuscarCuotasSocios['codError'] == '00000'
			{				
				$arrDatosCobroCuotas = buscarCadSql($arrSQLBuscarCuotasSocios['cadSQLBuscarCuotasSocios'],$conexionDB['conexionLink']);//en modeloMySQL.php, 

				//echo "<br><br>2-2 modeloTesorero:exportarCuotasExcelBancos:arrDatosCobroCuotas: ";print_r($arrDatosCobroCuotas);
				
				if ($arrDatosCobroCuotas['codError'] !== '00000')
				{
					$resExportarCuotasBancos['codError'] = $arrDatosCobroCuotas['codError'];
					$resExportarCuotasBancos['errorMensaje'] = $arrDatosCobroCuotas['errorMensaje'];
					$textoErrores = "ERROR: Al Exportar Cuotas a Excel para uso interno. CODERROR: ".$resExportarCuotasBancos['codError'].$resExportarCuotasBancos['errorMensaje'];						 
					$resExportarCuotasBancos['textoComentarios'] = date('Y-m-d:H:i:s')." Función: ".$nomScriptFuncionError."buscarCadSql(). Gestor CODUSER: ".$_SESSION['vs_CODUSER'].$textoErrores;
					$arrInsertarErrores = $resExportarCuotasBancos;		//seria para grabar errores al final		
				}
				elseif ($arrDatosCobroCuotas['numFilas'] <= 0)
				{ 
						$resExportarCuotasBancos['codError'] = '80001';	
						$resExportarCuotasBancos['errorMensaje'] = 'No se han encontrado datos que cumplan las condiciones de búsqueda elegidas';  
						$resExportarCuotasBancos['textoComentarios'] = $resExportarCuotasBancos['errorMensaje'];    	
				}
				else	//arrDatosCobroCuotas['numFilas']>0)
				{ 
						//------ Inicio bucle para adaptarlo a  Tríodos, se añaden estas dos columnas al final de cada filas orden de cobro
						$f = 0;
						$fUltima = $arrDatosCobroCuotas['numFilas'];
								
						while ($f < $fUltima) 
						{
							$arrDatosCobroCuotas['resultadoFilas'][$f]['Por_Cuenta_de'] = $arrayFormDatosElegidos['empresa_presentador']; 
							$arrDatosCobroCuotas['resultadoFilas'][$f]['concepto'] = $arrayFormDatosElegidos['concepto']." ".$arrayFormDatosElegidos['anioCuotasElegido'];                         
						
							$f++;
						}	
						//------ Fin bucle para adaptarlo a  Tríodos	----------------------------------------------------------		
						
						//echo "<br><br>3 modeloTesorero:exportarCuotasExcelBancos:arrDatosCobroCuotas: ";print_r($arrDatosCobroCuotas);
						
						$nomFile = 'Excel_Triodos';					

						require_once './modelos/libs/exportarExcel.php';					
						$resExportarCuotasExcel =	exportarExcelCuotasTes($arrDatosCobroCuotas,$nomFile);//Con formato decimal para Excel los números cuotas y gastos 
		
						/*--Está opción que está comentada, permite guardar el archivo en un directorio del servidor en lugar de descargarse directamente en el PC--*/
						//$directorioArchivo = '/usuarios_desarrollo';//podría ser otro directorio
						//$resExportarCuotasExcel =	exportarExcelCuotasTesEnServidor($arrDatosCobroCuotas,$directorioArchivo,$nomFile);//en modelos/libs/exportarExcel.php 
      /*-----------------------------------------------------------------------------------------------------------------------------------------*/
		
						//echo '<br><br>4 modeloTesorero:exportarCuotasExcelBancos:$resExportarCuotasExcel: ';print_r($resExportarCuotasExcel);
						
						if ($resExportarCuotasExcel['codError'] !== '00000')//No creo que entre aquí en caso error en exportarExcelCuotasTes()
						{ $resExportarCuotasBancos['codError'] = $resExportarCuotasExcel['codError'];
								$resExportarCuotasBancos['errorMensaje'] = $resExportarCuotasExcel['errorMensaje'];
								$textoErrores = "ERROR: Al Exportar Cuotas a Excel para Triodos y uso de tesorería. CODERROR: ".$resExportarCuotasBancos['codError'].$resExportarCuotasBancos['errorMensaje'];						 
								$resExportarCuotasBancos['textoComentarios'] = date('Y-m-d:H:i:s')." Función: ".$nomScriptFuncionError.": exportarExcel.php:exportarExcelCuotasTes(). Gestor CODUSER: ".$_SESSION['vs_CODUSER'].$textoErrores;		
        $arrInsertarErrores = $resExportarCuotasBancos;		//seria para grabar errores al final										
						}
				}//else	//arrDatosCobroCuotas['numFilas']>0)				
			}//else //$arrSQLBuscarCuotasSocios['codError'] == '00000'
	 }//else !	if ( (!isset($arrayFormDatosElegidos) || empty($arrayFormDatosElegidos)) ... || 
		
		//echo "<br><br>5 modeloTesorero:exportarCuotasExcelBancos:resExportarCuotasBancos: Error: ";print_r($resExportarCuotasBancos);
 
	 //--- Inicio tratamiento insertar errores (no necesario rollback, no se han modificado tablas) ---
		if ($resExportarCuotasBancos['codError'] !== '00000')//Ya vienen tratados comentarios los errores y se pasan a la siguiente funcion 
		{
			if ($resExportarCuotasBancos['codError'] < '80000')//> si fuese >=80000 es error lógico y mejor no se graba, aunque se podría enviar por email.
			{
				$resInsertarErrores = insertarError($arrInsertarErrores);//en modeloErrores.php					
			}				
		}		
		//------ Fin tratamiento insertar errores  --------------------------------------------------------			
	}//if ($conexionDB['codError']=='00000') 

 //echo '<br><br>6 modeloTesorero:exportarCuotasExcelBancos:$resExportarCuotasBancos: ';print_r($resExportarCuotasBancos);//se verá solo caso error, porque el buffer está cautivo
	
	return $resExportarCuotasBancos;	
}
/*---------------------------- Fin exportarCuotasExcelBancos ----------------------------------------*/

/*--------------------- Inicio exportarExcelCuotasInternoTes ----(Excel para uso interno )-------------
Genera un archivo Excel,para uso interno del tesorero, con datos de las cuotas de un año, 
y otros muchos datos de los socios, de los socios incluidos en la selección. 
LLama a modeloTesorero:cadBuscarCuotasEmailSocios() para formar la cadena select sql según 
los parámetros elegidos en el formulario de selección de campos:

El formulario permite elegir:
-Un determinado año (o de todos)			
-Estado de la cuota todos los posibles (PENDIENTE-COBRO,ABONADA-PARTE,NOABONADA-DEVUELTA,
                                        NOABONADA-ERROR-CUENTA,EXENTO,NOABONADA o todos)									
-Estado de los socios/as (alta, baja, todos) 
-ORDENARCOBROBANCO = SI/NO/TODOS 

-Cuenta bancaria de España
-Cuenta bancaria de paises SEPA (en Europa no incluye España)
-No tiene cuenta bancaria domiciliada
-Todas (Cuenta en España, en países SEPA, en países NO SEPA y sin cuenta domiciliada)

-Agrupaciones Territariales seleccionadas

Se descargará en un archivo Excel

LLAMADA: cTesorero:excelCuotasInternoTesorero()
LLAMA: modeloTesorero.php:cadBuscarCuotasEmailSocios()
       modeloMySQL.php:buscarCadSql()
       modelos/libs/exportarExcel.php:exportarExcelCuotasTes()
							modeloErrores.php:insertarError()

OBSERVACIONES: PHP 7.3.21, esta función no usa directamente PDO pero si algunas 
de las funciones llamadas.

OJO NO PONER NINGUNA SALIDA A PANTALLA (echo, etc.) No poner echo: daría error
al formar el buffer de salida a excel ya que utiliza "header()" y no puede 
haber ningúna salida delante.		
--------------------------------------------------------------------------------------------------*/
function exportarExcelCuotasInternoTes($codAreaCoordinacion,$condicionEmail,$arrayFormDatosElegidos)
{
	//echo "<br><br>0-1 modeloTesorero:exportarExcelCuotasInternoTes:arrayFormDatosElegidos: ";print_r($arrayFormDatosElegidos);
	
 require_once "./modelos/BBDD/MySQL/modeloMySQL.php";					
	require_once './modelos/modeloErrores.php'; 
	
	$resExportarCuotas['nomScript'] = 'modeloTesorero.php';
	$resExportarCuotas['nomFuncion'] = 'exportarExcelCuotasInternoTes';	
	$resExportarCuotas['codError'] = '00000';
	$resExportarCuotas['errorMensaje'] = '';
	$nomScriptFuncionError = "modeloTesorero.php:exportarExcelCuotasInternoTes(). Error: ";

	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	require_once "./modelos/BBDD/MySQL/conexionMySQL.php";	
			
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);
	
	if ($conexionDB['codError'] !== '00000')	
	{$resExportarCuotas = $conexionDB;
	}     
	else //if ($conexionDB['codError']=='00000')
	{
		//-- Para que fechaAltaExentosPago incluya a todas las altas hasta el día actual siempre --
		$arrayFormDatosElegidos['fechaAltaExentosPago']['anio'] = date('Y');
		$arrayFormDatosElegidos['fechaAltaExentosPago']['mes'] = date('m');
		$arrayFormDatosElegidos['fechaAltaExentosPago']['dia'] = date('d');
			
		//echo "<br><br>1 modeloTesorero:exportarExcelCuotasInternoTes:arrayFormDatosElegidos: ";print_r($arrayFormDatosElegidos);		
				
		$arrSQLBuscarCuotasSocios =	cadBuscarCuotasEmailSocios($codAreaCoordinacion,$condicionEmail,$arrayFormDatosElegidos);//en modeloTessorero.php, probado error
  
		//echo "<br><br>2 modeloTesorero:exportarExcelCuotasInternoTes:arrSQLBuscarCuotasSocios: ";print_r($arrSQLBuscarCuotasSocios);

		if ($arrSQLBuscarCuotasSocios['codError'] !== '00000')		
		{ 
			$resExportarCuotas['codError'] = $arrSQLBuscarCuotasSocios['codError'];
			$resExportarCuotas['errorMensaje'] = $arrSQLBuscarCuotasSocios['errorMensaje'];
			$textoErrores = "ERROR: Al Exportar Cuotas a Excel para uso interno. CODERROR: ".$resExportarCuotas['codError'].$resExportarCuotas['errorMensaje'];						 
			$resExportarCuotas['textoComentarios'] = " Función: ".$nomScriptFuncionError."cadBuscarCuotasEmailSocios(). Gestor CODUSER: ".$_SESSION['vs_CODUSER'].$textoErrores;							

			insertarError($resExportarCuotas);	
		}
		else //$arrSQLBuscarCuotasSocios['codError'] == '00000'
		{			
			$arrDatosCobroCuotas = buscarCadSql($arrSQLBuscarCuotasSocios['cadSQLBuscarCuotasSocios'],$conexionDB['conexionLink']);//en modeloMySQL.php, probado error 

			//echo "<br><br>3 modeloTesorero:exportarExcelCuotasInternoTes:arrDatosCobroCuotas: ";print_r($arrDatosCobroCuotas);
		
			if ($arrDatosCobroCuotas['codError'] !=='00000')
			{
	  	$resExportarCuotas['codError'] = $arrDatosCobroCuotas['codError'];
				$resExportarCuotas['errorMensaje'] = $arrDatosCobroCuotas['errorMensaje'];
		 	$textoErrores = "ERROR: Al Exportar Cuotas a Excel para uso interno. CODERROR: ".$resExportarCuotas['codError'].$resExportarCuotas['errorMensaje'];						 
		 	$resExportarCuotas['textoComentarios'] = " Función: ".$nomScriptFuncionError."buscarCadSql(). Gestor CODUSER: ".$_SESSION['vs_CODUSER'].$textoErrores;		

				insertarError($resExportarCuotas);	//en modeloErrores.php';				
			}
			elseif ($arrDatosCobroCuotas['numFilas'] <= 0)//aquí o en cTesorero.php
			{ 
			  $resExportarCuotas['codError'] = '80001';					
					$resExportarCuotas['errorMensaje'] = ' No se han encontrado datos que cumplan las condiciones de búsqueda elegidas'; 
     $resExportarCuotas['textoComentarios'] = $resExportarCuotas['errorMensaje'];					
			}
			else	//arrDatosCobroCuotas['numFilas']>0)
			{ //echo "<br><br>4-1 modeloTesorero:exportarExcelCuotasInternoTes:arrDatosCobroCuotas['numFilas']: ";print_r($arrDatosCobroCuotas['numFilas']);
				
					$nomFile = 'Excel_UsoInterno';
					
					require_once './modelos/libs/exportarExcel.php';					
					$resExportarCuotasInternoExcel =	exportarExcelCuotasTes($arrDatosCobroCuotas,$nomFile);//Con formato decimal para Excel los números cuotas y gastos 
					
					//echo '<br><br>4-2 modeloTesorero:exportarExcelCuotasInternoTes:$resExportarCuotas';print_r($resExportarCuotas);
					
					if ($resExportarCuotasInternoExcel['codError'] !== '00000')//No creo que entre aquí en caso error en caso error en exportarExcelTexto()
					{ $resExportarCuotas['codError'] = $resExportarCuotasInternoExcel['codError'];
			   	$resExportarCuotas['errorMensaje'] = $resExportarCuotasInternoExcel['errorMensaje'];
				   $textoErrores = "ERROR: Al Exportar Cuotas a Excel para uso interno. CODERROR: ".$resExportarCuotas['codError'].$resExportarCuotas['errorMensaje'];						 
							$resExportarCuotas['textoComentarios'] = date('Y-m-d:H:i:s')." Función: ".$nomScriptFuncionError."exportarExcel.php:exportarExcelCuotasTes(). Gestor CODUSER: ".$_SESSION['vs_CODUSER'].$textoErrores;		

							insertarError($resExportarCuotas);	//en modeloErrores.php';											
					}
			}//else	arrDatosCobroCuotas['numFilas']>0)
		}//else $arrSQLBuscarCuotasSocios['codError'] == '00000'	
	}//if ($conexionDB['codError']=='00000')
		
 //echo '<br><br>5 modeloTesorero:exportarExcelCuotasInternoTes:resExportarCuotas: ';print_r($resExportarCuotas);//no se verá porque el buffer está cautivo
		
	return $resExportarCuotas;	
}
/*---------------------------- Fin exportarExcelCuotasInternoTes ------------------------------------*/


/*---------------------------- Inicio exportarCuotasAEB19Bancos ---(Ya no se usa Para B.Santander)-----
/***YA NO SE USA, SUSTITUIDA POR exportarCuotasXMLBancos()***
// si se quisiera usar habría que adaptar a cambios en modeloTesorero
Descripción: --- B.Santander ---
             LLama a modeloTesorero: cadBuscarCuotasSociosAEB19()
             para formar la cadena select sql que busca los datos de todos 
             las cuotas de los socios según la selección (agrupaciones 
													territorial, y de un determinado años, CC Eapaña o extranjero,
													ESTADOCUOTA,  ejecuta la select y pasa el array con los datos a
													la función AEB19CuotasTesoreroSantanderWrite()que 
													formara	el archivo Norma AEB19 para exportación de cobros a bancos, 
             según las condiciones que pide B. Santander
													Los datos proceden del formulario vistas/tesorero/formAEB19Cuotas.php	
																								
Llamada: cTesorero:AEB19CuotasTesoreroSantander()
LLama a: modeloTesorero.php: cadBuscarCuotasEmailSocios() 
         modeloMySQL.php:buscarCadSql()
         modeloTesorero.php: AEB19CuotasTesoreroSantanderWrite() 
									
OBSERVACIÓN: 

OJO: Al utilizar "header()" en funciones que se llaman el proceso no se puede utilizar 
    echo u otras salidas de pantalla antes de esta función pues daría error al formar el buffer de salida a txt	
				Al finalizar, se descarga el archivo txt mediante el navegador, controla los posibles errores 
				en todas las funciones, excepto en  AEB19CuotasTesoreroSantanderWrite() que no controla si se ha producido un error
				 en la formación del archivo txt.				
--------------------------------------------------------------------------------------------------*/
function exportarCuotasAEB19Bancos($codAreaCoordinacion,$condicionEmail,$arrayFormDatosElegidos)	
{
	/***YA NO SE USA, SUSTITUIDA POR exportarCuotasXMLBancos()***/
	//echo "<br><br>0- modeloTesorero:exportarCuotasAEB19Bancos:arrayFormDatosElegidos:";print_r($arrayFormDatosElegidos);
	
	$arrDatosCobroCuotas['codError'] = '00000';
	$arrDatosCobroCuotas['errorMensaje'] = '';	
 
//---------------- Inicio ejecución de cadena sql SELECT --------------------------
	
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	require_once "./modelos/BBDD/MySQL/conexionMySQL.php";	
		
	$conexionUsuariosDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);
	
	if ($conexionUsuariosDB['codError']!=='00000')	
	{$arrDatosCobroCuotas = $conexionUsuariosDB;
	}     
	else //if ($conexionUsuariosDB['codError']=='00000')
	{
	 //Antes $cadBuscarCuotasSocios =	cadBuscarCuotasSociosAEB19($codAreaCoordinacion,$condicionEmail,$arrayFormDatosElegidos);	
	 $cadBuscarCuotasSocios =	cadBuscarCuotasEmailSocios($codAreaCoordinacion,$condicionEmail,$arrayFormDatosElegidos);	//esta función no tiene tratamiento de errores	
	
	 //echo "<br><br>1 modeloTesorero:exportarCuotasAEB19Bancos:cadBuscarCuotasSocios: ";print_r($cadBuscarCuotasSocios);
	 
	 require_once "./modelos/BBDD/MySQL/modeloMySQL.php";	
		$arrDatosCobroCuotas = buscarCadSql($cadBuscarCuotasSocios,$conexionUsuariosDB['conexionLink']);
  
		//echo "<br><br>2 modeloTesorero:exportarCuotasAEB19Bancos:arrDatosCobroCuotas: ";print_r($arrDatosCobroCuotas);
		
		if ($arrDatosCobroCuotas['codError'] !=='00000')
		{$arrDatosCobroCuotas['textoCabecera'] ='Exportar a AEB19Cuotas (celdas en formato texto)';
		 $arrDatosCobroCuotas['textoComentarios'] .= date('Y-m-d:H:i:s').":	exportarCuotasAEB19Bancos.Error del sistema, vuelva a intentarlo pasado un tiempo ";		
			$arrDatosCobroCuotas['nomFuncion'] .=", exportarCuotasAEB19Bancos()";
			
			require_once './modelos/modeloErrores.php';
			insertarError($arrDatosCobroCuotas);	
		}
		elseif ($arrDatosCobroCuotas['numFilas'] <= 0)
		{ $arrDatosCobroCuotas['codError'] = '80001';	
				$arrDatosCobroCuotas['errorMensaje']='No se han encontrado datos que cumplan las condiciones de búsqueda elegidas';  
		}
		else	//arrDatosCobroCuotas['numFilas']>0)
		{ //echo "<br><br>4 modeloTesorero:exportarCuotasAEB19Bancos:arrDatosCobroCuotas['numFilas']: ";print_r($arrDatosCobroCuotas['numFilas']);
	   
				$arrDatosCobroCuotas = AEB19CuotasTesoreroSantanderWrite($arrDatosCobroCuotas,$arrayFormDatosElegidos);//ESTA FUNCION ESTA EN modeloTesorero
 
	 //Se puede aquí, tratar mensajes de error??		
		}
	}//if ($conexionUsuariosDB['codError']=='00000')	

 //echo '<br><br>5 modeloTesorero:exportarCuotasAEB19Bancos:arrDatosCobroCuotas: ';print_r($arrDatosCobroCuotas);//no se verá porque el buffer está cautivo
	
	return $arrDatosCobroCuotas;	
}
/*------------------------------------- Fin exportarCuotasAEB19Bancos -------------------------------*/

// si se quisiera usar habría que adaptar a cambios en modeloTesorero
//------------------------------ Inicio AEB19CuotasTesoreroSantanderWrite ---(Para B.Santander)--------
/******YA NO SE USA, SUSTITUIDA POR  SEPA_XML_SDD_CuotasTesoreroSantanderWrite() *****

Forma el buffer de salida a archivo de texto AEB19.txt	para el Santander
Utiliza las cuentas IBAN de España y les quita los cuatro primeros caractes ESdd, para que se pueda utilizar 
con el progra de conversión de CC a SEPA del Santander.

(Referencia de esta clase en:http://www.php-hispano.net/foros/PHP/38067-crear-archivo-norma-19-para-remesas
Hay dos procedimientos para NORMA AEB19: el primero y el segundo. Este es una adaptación del 
procedimiento primero de la NORMA AEB19, que es el que aplica el b. Santander.)
 
Admite registros opcionaelas para la información no abligatoria: por ejemplo que te he puesto,
al cliente le llegaría el importe del IVA desglosado en el propio recibo que el banco le manda a casa, 
y otros conceptos

Llama: -AEB19/class.AEB19Writer.php:AEB19Writter();
       -cambiarAcentosEspeciales()-
							-modelos/libs/exportarBufferFicheroTXT.php:exportarBufferFicheroTXT()

RECIBE: $arrayFormDatosElegidos, campos del formulario (agrupaciones, iva, cuentas EL, fechas) y 
        $reExportar: los datos necesarios de los socios correspondientes (importe cuota socio, CC, NIF, etc) 

OBSERVACIÓN: -Sirve para SIN DESENCRIPTACIÓN Y CON DESENCRIPTACIÓN DE CC Y CEX		

             -Los datos que podrían variar vienen del formulario para que sea fácil modificarlos: 
													  IVA, fechas, codigo_presentador(cif_presentador),codigo_ordenante($cif_ordenante)
													  nombre_presentador($empresa_presentador),nombre_ordenante($empresa_ordenante),
															$cuenta,
															codigo_referencia_domiciliacion=codigo_devolucion_domiciliacion=CODUSER=$reExportar['resultadoFilas'][$f]['Referencia_codSocio']
															codigo_referencia_interna=($reExportar['resultadoFilas'][$f]['NIF'])															
													-Se deja un	if($iva!=='0.00')//Si el IVA es 0.00, se evita poner en la factura para simplificar, 
														los siguientes datos, pero se dejar este if por si más adelante se tuviera que incluir 
														el IVA distinto de 0.00 en Formulario 										
													-Se eliminan acentos y ñ, etc. (para evitar posibles problema )													
													-Después de ejecutar esta función, se queda estática la pantalla 
													 (habría que ver como recuperar el control del buffer).	

OJO: Al utilizar "header()" en funciones que se llaman el el proceso no se puede utilizar 
    echo u otras salidas de pantalla antes de esta función pues daría error al formar el buffer de salida a txt	
--------------------------------------------------------------------------------------------------*/
function AEB19CuotasTesoreroSantanderWrite($reExportar,$arrayFormDatosElegidos)
{//**YA NO SE USA, SUSTITUIDA POR SEPA_XML_SDD_CuotasTesoreroSantanderWrite()
	$empresa_presentador = $arrayFormDatosElegidos['empresa_presentador'];
	$cif_presentador =$arrayFormDatosElegidos['cif_presentador'];//EL
	$empresa_ordenante = $arrayFormDatosElegidos['empresa_ordenante'];//(tambien PUDIERA SER Andalucia Laica, o cada agrupación)
	$cif_ordenante =$arrayFormDatosElegidos['cif_ordenante'];//EL (también pudiera ser el de Andalucia Laica)
	// Se podría tener una cuenta para el presentador del banco Santader y otras cuentas del banco Santander
	// para los ordenantes que podrían ser las cuentas del Santander de cada agupación territorial		

 //$cuenta = array('0049', '0001', '52', '2411813269');//EL
	$cuenta = array($arrayFormDatosElegidos['ctaBanco']['CODENTIDAD'], 
	                $arrayFormDatosElegidos['ctaBanco']['CODSUCURSAL'],
																	$arrayFormDatosElegidos['ctaBanco']['DC'],
																	$arrayFormDatosElegidos['ctaBanco']['NUMCUENTA']);//cuenta de EL 	
																
	//$cuenta = substr($arrayFormDatosElegidos['CUENTAIBAN'],0,4);//cuenta tipo CCES o internacioanles sin los 4 caracteres del IBAN		
 
	if (!isset($arrayFormDatosElegidos['IVA']) || empty($arrayFormDatosElegidos['IVA']))//mejor en validar campos formulario
	{ $iva=0;
	}
	else
	{ $iva = $arrayFormDatosElegidos['IVA'];
	}
	
	$concepto = $arrayFormDatosElegidos['concepto'].$arrayFormDatosElegidos['anioCuotasElegido'];
	
	$dia  = $arrayFormDatosElegidos['fechacobro']['dia'];
	$mes  = $arrayFormDatosElegidos['fechacobro']['mes'];
	$anio = $arrayFormDatosElegidos['fechacobro']['anio'];
	
	$fechaCargo =  $dia.$mes.substr($anio, -2);//(ddmmaa)fechas llega desde formulario, para que sea más flexible cambiarlo
	$fechaEmision = $dia.'/'.$mes.'/'.$anio;// fecha por formulario	 
	
	$fechaConfeccionReg = date('dmy');//la del sistema	

 
	$reExportar['codError'] = '00000';
	$reExportar['errorMensaje'] = '';

	//------------------ INICIO CLASE AEB19Writter ------------------------------------
	//echo "<br><br>2- modeloTesorero:AEB19CuotasTesoreroSantanderWrite:reExportar['numFilas']:";print_r($reExportar['numFilas']);

	require_once '../../usuariosLibs/classes/AEB19/class.AEB19Writer.php';			
	//$aeb19 = new AEB19Writter('.');//Quitado por agustin
	$aeb19 = new AEB19Writter(' ');//agustin: el espacio en blanco, es el caracter con el que se va a rellenar algunos campos "libres" 
	                               //y que es obligatorio que sea así para el banco Santander

	//Asignamos los campos del presentador
	//El código presentador hay que indicarlo con ceros a la derecha?, así que lo hacemos en el formulario
	//NIF (9 digitos incluida letra)+Sufijo: 3 digitos (BS me indicaron que seria 001, pero aquí lo pone a ceros 000)
	//---$aeb19->insertarCampo('codigo_presentador', str_pad($cif_presentador, 12, '0', STR_PAD_RIGHT));
	$aeb19->insertarCampo('codigo_presentador', str_pad($cif_presentador, 11, '0', STR_PAD_RIGHT).'1');//B1(3)subfijo santander=001
	$aeb19->insertarCampo('fecha_fichero', $fechaConfeccionReg);//fecha sistema
	$aeb19->insertarCampo('nombre_presentador', $empresa_presentador);
	$aeb19->insertarCampo('entidad_receptora', $cuenta[0]);
	$aeb19->insertarCampo('oficina_presentador', $cuenta[1]);
	
	//Asignamos los campos del ordenante y guardamos el registro
	//El código ordenante hay que indicarlo con ceros a la derecha?, así que lo hacemos en el formulario
	//NIF (9 digitos incluida letra)+Sufijo:3 digitos (BS me indicaron que seria 001, pero aquí lo pone a ceros 000)
	//--$aeb19->insertarCampo('codigo_ordenante', str_pad($cif_ordenante, 12, '0', STR_PAD_RIGHT));
	$aeb19->insertarCampo('codigo_ordenante', str_pad($cif_ordenante, 11, '0', STR_PAD_RIGHT).'1');//B1(3)subfijo santander=001
	$aeb19->insertarCampo('fecha_cargo', $fechaCargo);
	$aeb19->insertarCampo('nombre_ordenante', $empresa_ordenante);
	$aeb19->insertarCampo('cuenta_abono_ordenante', implode('', $cuenta));
	$aeb19->guardarRegistro('ordenante');
	
	//Establecemos el código del ordenante para los registros obligatorios
	//--$aeb19->insertarCampo('ordenante_domiciliacion' , str_pad($cif_ordenante, 12, '0', STR_PAD_RIGHT));
	$aeb19->insertarCampo('ordenante_domiciliacion', str_pad($cif_ordenante, 11, '0', STR_PAD_RIGHT).'1');//B1(3)subfijo santander=001
	
	//Insertamos varias domiciliaciones en un bucle		
	//---------------------------------------------
	require_once "./modelos/libs/eliminarAcentos.php";
	
	$f = 0;
 $fUltima = $reExportar['numFilas'];  
	
	//-------------Inicio bucle para datos de cada socio -----------------------
 while ($f < $fUltima)
 {				
		$cuota_anio = $reExportar['resultadoFilas'][$f]['CuotaDonacionPendienteCobro'];//puede ser distinta para cada socio o cero, CuotaDonacionPendienteCobro=(CUOTAANIOSOCIO.IMPORTECUOTAANIOSOCIO-CUOTAANIOSOCIO.IMPORTECUOTAANIOPAGADA)				
		
		//$iva = 0.00; //EL  no ponemos iva hasta ahora, se podría pedir en el formulario
		$importeIva = round($cuota_anio * $iva/100, 2);
		$totalFactura = $cuota_anio + $importeIva;
		
  //Con el codigo_referencia_domiciliacion podremos referenciar la domiciliación CODSOCIO
  $aeb19->insertarCampo('codigo_referencia_domiciliacion', $reExportar['resultadoFilas'][$f]['Referencia_codSocio']);
		
		//$aeb19->insertarCampo('codigo_referencia_domiciliacion', "Soc-$f");
  //Cliente al que le domiciliamos:, APE1 APE2, NOM, 
		//cambiarAcentosEspeciales() cambia letras con acentos a sin acentos y Ñ,ñ,Ç,ç a N,n,C,c (según normas SEPA)
  //$aeb19->insertarCampo('nombre_cliente_domiciliacion', cambiarAcentosEspeciales($reExportar['resultadoFilas'][$f]['Nombre_Apellidos']));
  $aeb19->insertarCampo('nombre_cliente_domiciliacion', cambiarAcentosEspeciales($reExportar['resultadoFilas'][$f]['Apellidos_Nombre']));
		
		//$aeb19->insertarCampo('nombre_cliente_domiciliacion', "Nombre Titular-$f");
  //Cuenta del cliente en la que se domiciliará la factura, LOS 20 DÍGITOS 
		
		//$aeb19->insertarCampo('cuenta_adeudo_cliente', $reExportar['resultadoFilas'][$f]['CCES']);//de cada socio
	 
	 //echo "<br><br>2-2 modeloTesorero:AEB19CuotasTesoreroSantanderWrite:reExportar['resultadoFilas'][$f]['CUENTAIBAN']:";print_r($reExportar['resultadoFilas'][$f]['CUENTAIBAN']);
		
	 //ahora con las cuentas ES=IBAN hay que quitar los 4 caracteres del IBAN del IBAN, 
		//para utilizar la aplicación interna de conversión del Santader para las nuevas normas SEPA
		// a partir de antiguas cuentas CC
		$cuenta = substr($reExportar['resultadoFilas'][$f]['CUENTAIBAN'],4);
		//echo "<br><br>2-3 modeloTesorero:AEB19CuotasTesoreroSantanderWrite:cuenta :";print_r($cuenta);
		
		//$aeb19->insertarCampo('cuenta_adeudo_cliente', "2222222222222222222$f");//de cada socio
		$aeb19->insertarCampo('cuenta_adeudo_cliente', $cuenta );//de cada socio
		
   //El importe de la domiciliación (tiene que ser en céntimos de euro y con el IVA aplicado)
		
		//-----NO TENGO CLARO QUE HAYA QUE PONER EN CENTIMOS, ACASO SOLO EN EUROS--------
  $aeb19->insertarCampo('importe_domiciliacion', ($totalFactura * 100));
  //Código para asociar la devolución en caso de que ocurra //podria poner CODSOCIO max 6 digitos				
  //$aeb19->insertarCampo('codigo_devolucion_domiciliacion', $i);
		$aeb19->insertarCampo('codigo_devolucion_domiciliacion', $reExportar['resultadoFilas'][$f]['Referencia_codSocio']);
  		
		//$aeb19->insertarCampo('codigo_referencia_interna', "fra-$i");
  $aeb19->insertarCampo('codigo_referencia_interna', $reExportar['resultadoFilas'][$f]['NIF']);//maximo10 
	

  //Preparamos los conceptos opcionales de la domiciliación, en un array
		
  //Disponemos de 80 caracteres por línea (elemento del array). Más caracteres serán cortados
  //El índice 8 y 9 contendrían el sexto registro opcional, que es distinto a los demás
  $conceptosDom = array();
  
		//Los dos primeros índices serán el primer registro opcional
  //$conceptosDom[] = str_pad("Factura $i", 40, ' ', STR_PAD_RIGHT) . str_pad("emitida por: $empresa", 40, ' ', STR_PAD_RIGHT);
		$conceptosDom[] = str_pad("Factura $f", 40, ' ', STR_PAD_RIGHT) . str_pad("emitida por: $empresa_presentador", 40, ' ', STR_PAD_RIGHT);
  /********** mejorar desde un numero de la BBDD para que sea correlativo
		$numFactura = $f+1;
		$añoFactura = date(Y);		
		$cadNumFactura = $numFactura."-".$añoFactura;	//mejor desde un numero de la BBDD
		$conceptosDom[] = str_pad("Factura $cadNumFactura", 40, ' ', STR_PAD_RIGHT) . str_pad("emitida por: $empresa_presentador", 40, ' ', STR_PAD_RIGHT);
		*/
  //$conceptosDom[] = str_pad('emitida el ' . date('d/m/Y') . " para $empresa_ordenante ", 40, ' ', STR_PAD_RIGHT) . str_pad("CIF: ES-$cif_ordenante", 40, ' ', STR_PAD_RIGHT);
		//$conceptosDom[] = str_pad('emitida el ' .$dia.'/'.$mes.'/'.$anio . " para $empresa_ordenante ", 40, ' ', STR_PAD_RIGHT) . str_pad("CIF: ES-$cif_ordenante", 40, ' ', STR_PAD_RIGHT);
  $conceptosDom[] = str_pad('emitida el ' .$fechaEmision. " para $concepto ", 40, ' ', STR_PAD_RIGHT) . str_pad("CIF: ES-$cif_ordenante", 40, ' ', STR_PAD_RIGHT);

			
		//Los dos segundos índices serán el segundo registro opcional
  //$conceptosDom[] = str_pad(cambiarAcentosEspeciales($reExportar['resultadoFilas'][$f]['Nombre_Apellidos']), 40, ' ', STR_PAD_RIGHT);
  $conceptosDom[] = str_pad(cambiarAcentosEspeciales($reExportar['resultadoFilas'][$f]['Apellidos_Nombre']), 40, ' ', STR_PAD_RIGHT);
  
		//cambiarAcentosEspeciales() cambia letras con acentos a sin acentos y Ñ,ñ,Ç,ç a N,n,C,c (según normas SEPA)
		//$conceptosDom[] = str_pad("Nombre Titular-$f", 40, ' ', STR_PAD_RIGHT);
		
		//-- Inicio con IVA: dejar este if por si más adelante se tuviera que incluir el IVA distinto de 0.00 en Formulario ----
		if ($iva !== '0.00')//Si el IVA es 0.00, se evita poner en la factura para simplificar, los siguientes datos
		{
		 //number_format($i, 2, ',', '.') da numero $i con dos decimales y separados por coma (,)	los miles por puntos(.)		
   //$conceptosDom[] = str_pad('', 40, ' ', STR_PAD_RIGHT) . 'Base imponible:' . str_pad(number_format($i, 2, ',', '.') . ' EUR', 25, ' ', STR_PAD_LEFT);
   $conceptosDom[] = str_pad('', 40, ' ', STR_PAD_RIGHT) . 'Base imponible:' . str_pad(number_format($cuota_anio, 2, ',', '.') . ' EUR', 25, ' ', STR_PAD_LEFT);

		 //Los dos terceros índices serán el tercer registro opcional	
	
		 $conceptosDom[] = str_pad('', 40, ' ', STR_PAD_RIGHT).
      'IVA ' . str_pad(number_format($iva , 2, ',', '.'), 2, '0', STR_PAD_LEFT) . '%:'.
      str_pad(number_format($importeIva, 2, ',', '.') . ' EUR', 29, ' ', STR_PAD_LEFT);
		}	
		//--- Fin con IVA: se deja con este if por si más adelante se tuviera que incluir el IVA ---------------
		
  $conceptosDom[] = str_pad('', 40, ' ', STR_PAD_RIGHT).
      'Total:' . str_pad(number_format($totalFactura, 2, ',', '.') . ' EUR', 34, ' ', STR_PAD_LEFT);

  //Añadimos la domiciliación
  $aeb19->guardarRegistro('domiciliacion', $conceptosDom);	
		
		$f++;
	}//while ($f < $fUltima)------Fin bucle para datos de cada socio -----------------------	
 //--------------------------------------------------------	

 //Construimos el documento y lo mostramos por pantalla
 /*echo "<pre>{$aeb19->construirArchivo()}</pre>";*/
 $cadenaArchivo = $aeb19->construirArchivo();

 //------------------ FIN CLASE AEB19Writter ------------------------------------
	
	$nomFile = 'AEB19';
	
	require_once './modelos/libs/exportarBufferFicheroTXT.php';
	
	//exportarBufferFicheroTXT($cadenaArchivo,$nomFile);
 $reExportar = exportarBufferFicheroTXT($cadenaArchivo,$nomFile);
	
	return $reExportar;	
}
//------------------------------ Fin AEB19CuotasTesoreroSantanderWrite --------------------------------	

/*== FIN: FUNCIONES RELACIONADAS CON ARCHIVO ÓRDENES COBRO CUOTAS BANCOS ===============================*/


/*==== INICIO: FUNCIONES RELACIONADAS CON EMAILS COBRO CUOTAS ============================================
							- datosEmailAvisoProximoCobro(): Envío email próximo cobro cuota domiciliada 
							- datosEmailAvisoCuotaNoCobradaSinCC(): Envío email cuota no domiciliada aún NO pagada
							- exportarEmailDomiciliadosPendientes(): Exportar lista Emails socios cuota domciliada pendientes pagar
							- exportarEmailSinCCSEPAPendientes(): Exportar lista Emails socios cuota no domciliada pendientes pagar
========================================================================================================*/							
							

/*---------------------------- Inicio datosEmailAvisoProximoCobro -------------------------
Prepara el array de datos desde la consulta SQL para enviar, los emails a los socios 
que tienen domiciliada la cuenta bancaria.
LLama a modeloTesorero: cadBuscarCuotasEmailSocios() que forma la cadena select sql 
que busca Emails y otros datos de todos las cuotas de los socios según la selección 
(agrupaciones territorial, y de un determinado año, CC España , SEPA, o extranjero,
ESTADOCUOTA, proveniente de la función de que le llama, elegidos en el formulario
ejecuta la select y devuelve un array con los datos a la función que le llama.

Actualmente recibe siempre $codAreaCoordinacion ='%', pero se deja por si 
en su momento se crear tesorero a nivel de  área de coordinacion 

Estado cuotas: 'PENDIENTE-COBRO','ABONADA-PARTE', con cuenta en España o en país SEPA 
(por ahora no porque hay problemas con BIC), siempre que están de "alta" en el 
momento actual
- NO INCLUYE las cuotas "abonadas,exentas, y socios dados de baja",  
y ORDENARCOBROBANCO=SI y de sólo de las "AGRUPACIONES" elegidas en el formulario.

Para formar la lista de emails, desde formEmailAvisarDomiciliadosProximoCobro.php
se puede elegir:
- Cuenta bancaria en España
- Cuenta bancaria de países SEPA (distintos de España)

RECIBE: 
- $arrayFormDatosElegidos: (año cuota, fecha exención pago, cuenta banco España o SEPA,
  agrupaciones elegidas)
- $codAreaCoordinacion=%, que proceden del cTesorero.php ( o formulario)

DEVUELVE: array "$arrEmailsAvisoProximoCobro" con los emails y datos personales de los socios
          que cumplen los criterios de búsqueda.
													
LLAMADA: cTesorero:emailAvisarDomiciliadosProximoCobro()
LLAMA:
usuariosConfig/BBDD/MySQL/configMySQL.php,
modelos/BBDD/MySQL/conexionMySQL.php:conexionDB()
modeloTesorero.php: cadBuscarCuotasEmailSocios() 
modeloMySQL.php: buscarCadSql()
modelos/modeloErrores.php:insertarError()

OBSERVACIÓN:
2020-10-16: Aquí no utilizo parámetros arrBindVariables para PDO. Probada PHP 7.2.31
Parecida a modeloTesorero.php:exportarEmailDomiciliadosPendientes()
y datosEmailAvisoCuotaNoCobradaSinCC()
----------------------------------------------------------------------------------------*/
function datosEmailAvisoProximoCobro($codAreaCoordinacion,$arrayFormDatosElegidos)
{
	//echo "<br><br>0-1 modeloTesorero:datosEmailAvisoProximoCobro:codAreaCoordinacion: ";print_r($codAreaCoordinacion);
	//echo "<br><br>0-2 modeloTesorero:datosEmailAvisoProximoCobro:arrayFormDatosElegidos: ";print_r($arrayFormDatosElegidos);
	
	require_once 'BBDD/MySQL/modeloMySQL.php';
	require_once './modelos/modeloErrores.php';
	
	$arrEmailsAvisoProximoCobro['nomScript'] = "modeloTesorero.php";
	$arrEmailsAvisoProximoCobro['nomFuncion'] = "datosEmailAvisoProximoCobro";
	$arrEmailsAvisoProximoCobro['codError'] = '00000';
	$arrEmailsAvisoProximoCobro['errorMensaje'] = '';
 $nomScriptFuncionError = "modeloTesorero.php:datosEmailAvisoProximoCobro(): ERROR: ";
 
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	require_once "./modelos/BBDD/MySQL/conexionMySQL.php";		
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);
	
	if ($conexionDB['codError']!=='00000')	
	{$arrEmailsAvisoProximoCobro = $conexionDB;
	}     
	else //if ($conexionDB['codError']=='00000')
	{					
		/*if de control de existencia de "$codAreaCoordinacion, $arrayFormDatosElegidos" ya que son datos necesarios y críticos 
		  Nota: Ya no sería necesario este if, porque también se controla $arrayFormDatosElegidos en "cadBuscarCuotasEmailSocios()"  
		*/			 	
	 if ( (!isset($arrayFormDatosElegidos['anioCuotasElegido']) || empty($arrayFormDatosElegidos['anioCuotasElegido'])) || 
							(!isset($arrayFormDatosElegidos['fechaAltaExentosPago'] ) || empty($arrayFormDatosElegidos['fechaAltaExentosPago'])) || 
						 (!isset($arrayFormDatosElegidos['paisCC'] ) || empty($arrayFormDatosElegidos['paisCC'])) ||
							(!isset($arrayFormDatosElegidos['agrupaciones']  ) || empty($arrayFormDatosElegidos['agrupaciones'] )) ||
							(!isset($codAreaCoordinacion ) || empty($codAreaCoordinacion ))								
					)
		{
			$arrEmailsAvisoProximoCobro['codError'] = '70601';
			$arrEmailsAvisoProximoCobro['errorMensaje'] = 'ERROR: Faltan datos necesarios para envíar email de aviso de cuota no pagada. (de algún campo de $arrayFormDatosElegidos)';				
			$arrEmailsAvisoProximoCobro['textoComentarios'] = " Función: ".$nomScriptFuncionError."cadBuscarCuotasEmailSocios(). Gestor CODUSER: ".$_SESSION['vs_CODUSER'];		
  	//echo "<br><br>1-1 modeloTesorero:datosEmailAvisoCuotaNoCobradaSinCC:arrEmailsAvisoProximoCobro: ";print_r($arrEmailsAvisoProximoCobro);
		}	
		else //!	if ( (!isset($arrayFormDatosElegidos) || empty($arrayFormDatosElegidos)) ... || 
		{	
				//--- Los siguientes valores, no se incluyen en el formulario aun podrían venir de cTesorero.php o desde formulario:
		 	$arrayFormDatosElegidos['ESTADO'] = 'alta';//solo se cobra a socios que estén de alta e incluye: alta,alta-sin-password-excel,alta-sin-password-gestor
		 	$arrayFormDatosElegidos['estadosCuotas'] = array('PENDIENTE-COBRO'=>'PENDIENTE-COBRO','ABONADA-PARTE'=>'ABONADA-PARTE');
    //Cuenta bancaria = NOABONADA-DEVUELTA, NOABONADA-ERROR-CUENTA (en esos estados de cuota se borra la cuenta bancaria, por lo cual no se podrá domiciliar el cobro)	
				
			 $arrayFormDatosElegidos['ORDENARCOBROBANCO'] = 'SI'; //se excluyen los que tengan ['ORDENARCOBROBANCO']='NO' elegido por tesorero, aunque esté pendiente de cobro;    
			 /*En el caso de CUENTAIBAN de otros países SEPA distintos de ES, ORDENARCOBROBANCO por defecto es SI, 
			   entonces se podría envíar email con esta función "datosEmailAvisoProximoCobro()", pero tesorería puede poner a NO si no quiere 
				  hacer orden de cobro domiciliado y entonces se podría envíar email con la función "datosEmailAvisoCuotaNoCobradaSinCC()"*/		
			
				$condicionEmailError = 'NO';//solo se busca a los que EMAILERROR = 'NO'
				
				//echo "<br><br>1-2 modeloTesorero:datosEmailAvisoProximoCobro:arrayFormDatosElegidos: ";print_r($arrayFormDatosElegidos);
						
				$arrSQLBuscarCuotasSocios =	cadBuscarCuotasEmailSocios($codAreaCoordinacion,$condicionEmailError,$arrayFormDatosElegidos);//en modeloTessorero.php
				
				//echo "<br><br>2 modeloTesorero:datosEmailAvisoProximoCobro:arrSQLBuscarCuotasSocios: ";print_r($arrSQLBuscarCuotasSocios);

				if ($arrSQLBuscarCuotasSocios['codError'] !== '00000')		
				{ 
						$arrEmailsAvisoProximoCobro['codError'] = $arrSQLBuscarCuotasSocios['codError'];
						$arrEmailsAvisoProximoCobro['errorMensaje'] = $arrSQLBuscarCuotasSocios['errorMensaje'];
						$textoErrores = "ERROR: Al buscar datos de de socios/as con cuotas pendientes de cobro. CODERROR: ".$arrEmailsAvisoProximoCobro['codError'].$arrEmailsAvisoProximoCobro['errorMensaje'];						 
						$arrEmailsAvisoProximoCobro['textoComentarios'] = " Función: ".$nomScriptFuncionError."cadBuscarCuotasEmailSocios(). Gestor CODUSER: ".$_SESSION['vs_CODUSER'].$textoErrores;							
						insertarError($arrEmailsAvisoProximoCobro);	
				}
				else //$arrSQLBuscarCuotasSocios['codError'] == '00000'
				{	
					$arrDatosCobroCuotas = buscarCadSql($arrSQLBuscarCuotasSocios['cadSQLBuscarCuotasSocios'],$conexionDB['conexionLink']);//en modeloMySQL.php, probado error

				 //echo "<br><br>3 modeloTesorero:datosEmailAvisoProximoCobro:arrDatosCobroCuotas: ";print_r($arrDatosCobroCuotas);		
				
					/*------------- Fin Buscar Datos de Cobro Cuotas socios para archivo SEPA-XML --------------------------------------------*/
					if ($arrDatosCobroCuotas['codError'] !== '00000')//probado error sistema OK, con insertarError() y emailErrorWMaster() al final
					{ 
							$arrEmailsAvisoProximoCobro['codError'] = $arrDatosCobroCuotas['codError'];
							$arrEmailsAvisoProximoCobro['errorMensaje'] = $arrDatosCobroCuotas['errorMensaje'];
							$textoErrores = "ERROR: Al buscar datos de de socios/as con cuotas pendientes de cobro. CODERROR: ".$arrEmailsAvisoProximoCobro['codError'].$arrEmailsAvisoProximoCobro['errorMensaje'];						 
							$arrEmailsAvisoProximoCobro['textoComentarios'] =  " Función: ".	$nomScriptFuncionError.":buscarCadSql():cadBuscarCuotasEmailSocios(). Gestor CODUSER: ".$_SESSION['vs_CODUSER'].$textoErrores;							
							insertarError($arrEmailsAvisoProximoCobro);	
					}
					elseif ($arrDatosCobroCuotas['numFilas'] == 0)//probado error ok. No hay cuotas domiciliadas pendientes para condiciones búsqueda, NO ES ERROR, no inserta error, pero se muestra en pantalla 
					{					
							$arrEmailsAvisoProximoCobro['codError'] = '80001';//No sería verdaderemente un error, acaso se podría llevar a controlador?
							$arrEmailsAvisoProximoCobro['textoComentarios'] = " No se han encontrado datos que cumplan las condiciones de búsqueda elegidas.";									
					}	
     else
     { $arrEmailsAvisoProximoCobro = $arrDatosCobroCuotas;
     }	
		  }//else $arrSQLBuscarCuotasSocios['codError'] == '00000'
		}//else !if ( (!isset($arrayFormDatosElegidos) || empty($arrayFormDatosElegidos)) ... || 	
	}//if ($conexionDB['codError']=='00000')	

 //echo '<br><br>5 modeloTesorero:datosEmailAvisoProximoCobro:arrEmailsAvisoProximoCobro: ';print_r($arrEmailsAvisoProximoCobro);
	
	return $arrEmailsAvisoProximoCobro;	
}
/*---------------------------- Fin datosEmailAvisoProximoCobro  ---------------------------------------*/

/*---------------------------- Inicio datosEmailAvisoCuotaNoCobradaSinCC --------------------------------
Prepara el array de datos desde la consulta SQL para enviar los emails 
a los socios que NO tienen domiciliada la cuenta bancaria, O que son NO SEPA,
a que son de país SEPA, distintos de España, (en este caso sólo se envía email si tienen 
"Ordenar cobro banco = NO", por falta de cálculo BIC de otros países SEPA,
por eso se envía este email de aviso no pagado) 

LLama a modeloTesorero: cadBuscarCuotasEmailSocios() 
que forma la cadena select sql que busca Emails y otros datos de todos 
las cuotas de los socios según la selección (agrupaciones 
territorial, y de un determinado año, CC España , SEPA, o extranjero,
ESTADOCUOTA,  elegido en el formulario.
Ejecuta la select y devuelve un array con los datos a la función que le llama.
									
Actualmente recibe siempre $codAreaCoordinacion ='%', pero se deja por si 
en su momento se crear tesorero a nivel de  área de coordinacion 

INCLUYE:
-Estado cuotas de los socios/as:'PENDIENTE-COBRO','ABONADA-PARTE','NOABONADA-DEVUELTA',
'NOABONADA-ERROR-CUENTA', siempre que están de alta en el momento actual y 

-NO INCLUYE las cuotas "abonadas, exentas de los socios/as o que estén de baja". 

Para formar la lista de emails, desde formEmailAvisarCuotaNoCobradaSinCC.php se puede elegir:
-No tiene cuenta bancaria domiciliada
-Tiene cuenta bancaria de países NO SEPA (aún no es posible domiciliar las cuotas)
-Cuenta bancaria de países SEPA (NO ES). Por ahora solo se puede domiciliar para España, 
debido a que SEPA XML pide BIC y solo tengo hecho para ES el cálculo de BIC a partir de IBAN		

RECIBE: 
- $arrayFormDatosElegidos: (año cuota, fecha exención pago, cuenta banco España o SEPA,
  agrupaciones elegidas) 
- $codAreaCoordinacion=%, que proceden del cTesorero.php ( o formulario)

DEVUELVE: array "$arrEmailsAvisoCuotaNoPagada" con los emails y datos personales de los socios
          que cumplen los criterios de búsqueda.																				
									
LLAMADA: cTesorero:emailAvisarCuotaNoCobradaSinCC()
LLAMA: 
usuariosConfig/BBDD/MySQL/configMySQL.php,
modelos/BBDD/MySQL/conexionMySQL.php:conexionDB()
modeloTesorero.php: cadBuscarCuotasEmailSocios() 
modeloMySQL.php: buscarCadSql()
modelos/modeloErrores.php:insertarError()

OBSERVACIÓN: 
2020-10-17: Aquí no utilizo parámetros arrBindVariables para PDO. Probada PHP 7.2.31
Parecida a  modeloTesorero.php:exportarEmailSinCCSEPAPendientes()
Parecida a modeloTesorero.php:exportarEmailDomiciliadosPendientes()
y datosEmailAvisoProximoCobro()
--------------------------------------------------------------------------------------*/
function datosEmailAvisoCuotaNoCobradaSinCC($codAreaCoordinacion,$arrayFormDatosElegidos)	
{
	//echo "<br><br>0-1 modeloTesorero:datosEmailAvisoCuotaNoCobradaSinCC:codAreaCoordinacion: ";print_r($codAreaCoordinacion);
	//echo "<br><br>0-2 modeloTesorero:datosEmailAvisoCuotaNoCobradaSinCC:arrayFormDatosElegidos: ";print_r($arrayFormDatosElegidos);
	
	require_once 'BBDD/MySQL/modeloMySQL.php';
	require_once './modelos/modeloErrores.php';	
	
	$arrEmailsAvisoCuotaNoPagada['nomScript'] = "modeloTesorero.php";
	$arrEmailsAvisoCuotaNoPagada['nomFuncion'] = "datosEmailAvisoCuotaNoCobradaSinCC";
	$arrEmailsAvisoCuotaNoPagada['codError']     = '00000';
	$arrEmailsAvisoCuotaNoPagada['errorMensaje'] = '';
	$nomScriptFuncionError = "modeloTesorero.php:datosEmailAvisoCuotaNoCobradaSinCC(): ERROR: ";
 
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	require_once "./modelos/BBDD/MySQL/conexionMySQL.php";		
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);
	
	if ($conexionDB['codError']!=='00000')	
	{$arrEmailsAvisoCuotaNoPagada = $conexionDB;
	}     
	else //if ($conexionDB['codError']=='00000')
	{	 	
		/*if de control de existencia de "$codAreaCoordinacion, $arrayFormDatosElegidos" ya que son datos necesarios y críticos 
		  Nota: Ya no sería necesario este if, porque también se controla $arrayFormDatosElegidos en "cadBuscarCuotasEmailSocios()"  
		*/		 	
	 if ( (!isset($arrayFormDatosElegidos['anioCuotasElegido']) || empty($arrayFormDatosElegidos['anioCuotasElegido'])) || 
							(!isset($arrayFormDatosElegidos['fechaAltaExentosPago'] ) || empty($arrayFormDatosElegidos['fechaAltaExentosPago'])) || 
						 (!isset($arrayFormDatosElegidos['paisCC'] ) || empty($arrayFormDatosElegidos['paisCC'])) ||
							(!isset($arrayFormDatosElegidos['agrupaciones']  ) || empty($arrayFormDatosElegidos['agrupaciones'] )) || 
							(!isset($codAreaCoordinacion ) || empty($codAreaCoordinacion ))								
					)
		{
			$arrEmailsAvisoCuotaNoPagada['codError'] = '70601';
			$arrEmailsAvisoCuotaNoPagada['errorMensaje'] = 'ERROR: Faltan datos necesarios para envíar email de aviso de cuota no pagada. (de algún campo de $arrayFormDatosElegidos)';		
			$arrEmailsAvisoCuotaNoPagada['textoComentarios'] = " Función: ".$nomScriptFuncionError."cadBuscarCuotasEmailSocios(). Gestor CODUSER: ".$_SESSION['vs_CODUSER'];		
			//echo "<br><br>1-1 modeloTesorero:datosEmailAvisoCuotaNoCobradaSinCC:arrEmailsAvisoCuotaNoPagada: ";print_r($arrEmailsAvisoCuotaNoPagada);
		}	
		else //!	if ( (!isset($arrayFormDatosElegidos) || empty($arrayFormDatosElegidos)) || ....
		{						
			//--- Los siguientes valores, no se incluyen en el formulario aun podrían venir de cTesorero.php o desde formulario:
		 $arrayFormDatosElegidos['ESTADO'] = 'alta';//solo se cobra a socios que estén de alta e incluye: alta,alta-sin-password-excel,alta-sin-password-gestor			
			$arrayFormDatosElegidos['estadosCuotas'] = array('PENDIENTE-COBRO'=>'PENDIENTE-COBRO','ABONADA-PARTE'=>'ABONADA-PARTE','NOABONADA-DEVUELTA'=>'NOABONADA-DEVUELTA','NOABONADA-ERROR-CUENTA'=>'NOABONADA-ERROR-CUENTA');	

			$arrayFormDatosElegidos['ORDENARCOBROBANCO'] = 'NO';
		 /*En el caso de CUENTAIBAN de otros países SEPA distintos de ES, ORDENARCOBROBANCO por defecto es SI, 
		   entonces se podría envíar email con esta función "datosEmailAvisoProximoCobro()", pero tesorería puede poner a NO si no quiere 
			  hacer orden de cobro domiciliado y entonces se podría envíar email con la función "datosEmailAvisoCuotaNoCobradaSinCC()"*/	
					
			$condicionEmailError = 'NO'; //será MIEMBRO.EMAILERROR = 'NO', solo se busca a los que EMAILERROR = 'NO'
			
   //echo "<br><br>1-2 modeloTesorero:datosEmailAvisoCuotaNoCobradaSinCC:arrayFormDatosElegidos: ";print_r($arrayFormDatosElegidos);			
						
			$arrSQLBuscarCuotasSocios =	cadBuscarCuotasEmailSocios($codAreaCoordinacion,$condicionEmailError,$arrayFormDatosElegidos);//en modeloTessorero.php
			
			//echo "<br><br>2 modeloTesorero:datosEmailAvisoCuotaNoCobradaSinCC:cadSql: ";print_r($cadSql);
		
			if ($arrSQLBuscarCuotasSocios['codError'] !== '00000')		
			{ 
					$arrEmailsAvisoCuotaNoPagada['codError'] = $arrSQLBuscarCuotasSocios['codError'];
					$arrEmailsAvisoCuotaNoPagada['errorMensaje'] = $arrSQLBuscarCuotasSocios['errorMensaje'];
					$textoErrores = "ERROR: Al buscar datos de de socios/as con cuotas pendientes de pago. CODERROR: ".$arrEmailsAvisoCuotaNoPagada['codError'].$arrEmailsAvisoCuotaNoPagada['errorMensaje'];						 
					$arrEmailsAvisoCuotaNoPagada['textoComentarios'] = " Función: ".$nomScriptFuncionError."cadBuscarCuotasEmailSocios(). Gestor CODUSER: ".$_SESSION['vs_CODUSER'].$textoErrores;							
					insertarError($arrEmailsAvisoCuotaNoPagada);	
			}
			else //$arrSQLBuscarCuotasSocios['codError'] == '00000'
			{
				$arrDatosCobroCuotas = buscarCadSql($arrSQLBuscarCuotasSocios['cadSQLBuscarCuotasSocios'],$conexionDB['conexionLink']);//en modeloMySQL.php, probado error
				
				//echo "<br><br>3 modeloTesorero:datosEmailAvisoCuotaNoCobradaSinCC:arrDatosCobroCuotas: ";print_r($arrDatosCobroCuotas);					
				
					/*------------- Fin Buscar Datos de Cobro Cuotas socios para archivo SEPA-XML --------------------------------------------*/
					if ($arrDatosCobroCuotas['codError'] !== '00000')//probado error sistema OK, con insertarError() y emailErrorWMaster() al final
					{ 
							$arrEmailsAvisoCuotaNoPagada['codError'] = $arrDatosCobroCuotas['codError'];
							$arrEmailsAvisoCuotaNoPagada['errorMensaje'] = $arrDatosCobroCuotas['errorMensaje'];
							$textoErrores = "ERROR: Al buscar datos de de socios/as con cuotas pendientes de pago. CODERROR: ".$arrEmailsAvisoCuotaNoPagada['codError'].$arrEmailsAvisoCuotaNoPagada['errorMensaje'];						 
							$arrEmailsAvisoCuotaNoPagada['textoComentarios'] =  " Función: ".	$nomScriptFuncionError.":buscarCadSql():cadBuscarCuotasEmailSocios(). Gestor CODUSER: ".$_SESSION['vs_CODUSER'].$textoErrores;							
							insertarError($arrEmailsAvisoCuotaNoPagada);	
					}
					elseif ($arrDatosCobroCuotas['numFilas'] == 0)//probado error ok. No hay cuotas domiciliadas pendientes para condiciones búsqueda, NO ES ERROR, no inserta error, pero se muestra en pantalla 
					{					
							$arrEmailsAvisoCuotaNoPagada['codError'] = '80001';//No sería verdaderemente un error, acaso se podría llevar a controlador?
							$arrEmailsAvisoCuotaNoPagada['textoComentarios'] = " No se han encontrado datos que cumplan las condiciones de búsqueda elegidas.";									
					}	
     else
     { $arrEmailsAvisoCuotaNoPagada = $arrDatosCobroCuotas;
     }	
		 }//else $arrSQLBuscarCuotasSocios['codError'] == '00000'			
		}//else !if ( (!isset($arrayFormDatosElegidos) || empty($arrayFormDatosElegidos)) || ....
	}//if ($conexionDB['codError']=='00000')	

 //echo '<br><br>5 modeloTesorero:datosEmailAvisoCuotaNoCobradaSinCC:arrEmailsAvisoCuotaNoPagada: ';print_r($arrEmailsAvisoCuotaNoPagada);//no se verá porque el buffer está cautivo
	
	return $arrEmailsAvisoCuotaNoPagada;	
}
/*---------------------------- Fin datosEmailAvisoCuotaNoCobradaSinCC  --------------------------------*/

/*---------------------------- Inicio exportarEmailDomiciliadosPendientes -------------------------------
Genera y exporta a un archivo -txt los emails en forma de lista separados por (;) para copiar y pegar 
en el correo de NODO50 (tesoreria@europalica.org), y enviar un email a los socios de la lista, con texto
libre para avisar a los socios que se van enviar "las órdenes de cobro de las cuotas domiciliadas"  
para el cobro por el banco (actualmente B.Santander norma SEPA-XML), de las 
agrupaciones elegidas y que están de alta en el momento actual y según la 

LLama a modeloTesorero: "cadBuscarCuotasEmailSocios()", que forma la cadena select sql que busca Emails 
y otros datos de todos las cuotas de los socios, según la selección (agrup. territorial, año actual,etc.,
provenientes del formulario ejecuta la select y devuelve un array con los datos obtenidos.

Con el array obtenido de la Select, en la función "modeloTesorero.php:exportarEmailsWrite()" se genera 
la lista de emails sperados por (;) y dentro de esa función se llama a la función "exportarBufferFicheroTXT()"
para generar y descargar el archivo -txt con los emails.

Para formar la lista de emails, desde formEmailAvisarDomiciliadosProximoCobro.php
se puede elegir:
- Cuenta bancaria en España
- Cuenta bancaria países SEPA (distintos de España) y "Ordenar cobro banco = SI", 
  POR AHORA NO SE PUEDE GENERAR EL SEPA-XML CON ESTA APLICACIÓN por falta cálculo BIC 
  otros países SEPA, pero se podría hacer manualmente en la web del B.Santander si se 
  consiguiesen esos BICs.	

INCLUYE:		
- Socios que estén de alta e incluye: alta,alta-sin-password-excel,alta-sin-password-gestor
- Estado Cuotas de socios:'PENDIENTE-COBRO','ABONADA-PARTE'
- 'ORDENARCOBROBANCO' = 'SI' (está domiciliada y hay orden de cobro)
- $condicionEmailError = 'NO'
- Actualmente recibe siempre $codAreaCoordinacion ='%', pero se deja por si en su momento se crea tesorero 
  a nivel área de coordinacion 
EXCLUIDOS:
- Error email, o falta email.
- Cuotas "abonadas", "exentas (honorarios)"
- Socios que estén de "baja" en el momento actual
- Los que se dieron de alta después de la fecha elegida en formulario (en noviembre o diciembre)
		
RECIBE: 
- $arrayFormDatosElegidos: (año cuota, fecha exención pago, cuenta banco España o SEPA,
  agrupaciones elegidas) 
- $codAreaCoordinacion=%,  que proceden del cTesorero.php ( o formulario)

DEVUELVE: array "$arrExportarEmailCuotasDomiPend" con los emails y datos personales de los socios
          que cumplen los criterios de búsqueda.						
													
LLAMADA: cTesorero:exportarEmailDomiciliadosPend()
LLAMA: modeloTesorero.php: cadBuscarCuotasEmailSocios(), exportarEmailsWrite()
         modeloMySQL.php: buscarCadSql()
									modeloErrores.php:insertarError()

OBSERVACIONES: Probada PHP 7.3.21

OJO NO PONER NINGUNA SALIDA A PANTALLA (echo, etc.) No poner echo: daría error
al formar el buffer de salida a txt ya que utiliza "header()" y no puede haber ninguna salida delante
ni notice, warning, ...

NOTA: una vez ejecutada la función exportarEmailDomiciliadosPendSinCC(), si no ha habido erro 
la pantalla última permanece fija creo que por el buffer, habría que liberarla para que indique 
que se ha hecho bien				
------------------------------------------------------------------------------------------------------*/
function exportarEmailDomiciliadosPendientes($codAreaCoordinacion,$arrayFormDatosElegidos)	
{
	//echo "<br><br>0-1 modeloTesorero:exportarEmailDomiciliadosPendientes:arrayFormDatosElegidos: ";print_r($arrayFormDatosElegidos);

	require_once 'BBDD/MySQL/modeloMySQL.php';
	require_once './modelos/modeloErrores.php';	

	$arrExportarEmailCuotasDomiPend['nomScript'] = "modeloTesorero.php";
	$arrExportarEmailCuotasDomiPend['nomFuncion'] = "exportarEmailDomiciliadosPendientes";
	$arrExportarEmailCuotasDomiPend['codError'] = '00000';
	$arrExportarEmailCuotasDomiPend['errorMensaje'] = '';	 
	$nomScriptFuncionError = "modeloTesorero.php:exportarEmailDomiciliadosPendientes(): ERROR: ";
 
 //---------------- Inicio ejecución de cadena sql SELECT --------------------------	
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	require_once "./modelos/BBDD/MySQL/conexionMySQL.php";	
		
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);
	
	if ($conexionDB['codError']!=='00000')	
	{$arrDatosCobroCuotas = $conexionDB;
	}     
	else //if ($conexionDB['codError']=='00000')
	{	
		/*if de control de existencia de "$codAreaCoordinacion, $arrayFormDatosElegidos" ya que son datos necesarios y críticos 
		  Nota: Ya no sería necesario este if, porque también se controla $arrayFormDatosElegidos en "cadBuscarCuotasEmailSocios()"  
		*/		 	
	 if ( (!isset($arrayFormDatosElegidos['anioCuotasElegido']) || empty($arrayFormDatosElegidos['anioCuotasElegido'])) || 
							(!isset($arrayFormDatosElegidos['fechaAltaExentosPago'] ) || empty($arrayFormDatosElegidos['fechaAltaExentosPago'])) || 
						 (!isset($arrayFormDatosElegidos['paisCC'] ) || empty($arrayFormDatosElegidos['paisCC'])) ||
							(!isset($arrayFormDatosElegidos['agrupaciones']  ) || empty($arrayFormDatosElegidos['agrupaciones'] )) || 
							(!isset($codAreaCoordinacion ) || empty($codAreaCoordinacion ))								
					)
		{
			$arrExportarEmailCuotasDomiPend['codError'] = '70601';
			$arrExportarEmailCuotasDomiPend['errorMensaje'] = ' ERROR: Faltan datos necesarios para envíar email de aviso de cuota no pagada. (de algún campo de $arrayFormDatosElegidos)';		
			$arrExportarEmailCuotasDomiPend['textoComentarios'] = " Función: ".$nomScriptFuncionError."cadBuscarCuotasEmailSocios(). Gestor CODUSER: ".$_SESSION['vs_CODUSER'];
			//echo "<br><br>1-1 modeloTesorero:exportarEmailDomiciliadosPendientes:arrExportarEmailCuotasDomiPend: ";print_r($arrExportarEmailCuotasDomiPend);
		}	
		else //!	if ( (!isset($arrayFormDatosElegidos) || empty($arrayFormDatosElegidos)) || ....
		{		
			//--- Los siguientes valores, no se incluyen en el formulario aun podrían venir de cTesorero.php o desde formulario:
			$arrayFormDatosElegidos['ESTADO'] = 'alta';//solo se cobra a socios que estén de alta e incluye: alta,alta-sin-password-excel,alta-sin-password-gestor
			$arrayFormDatosElegidos['estadosCuotas'] = array('PENDIENTE-COBRO'=>'PENDIENTE-COBRO','ABONADA-PARTE'=>'ABONADA-PARTE');
			//Cuenta bancaria = NOABONADA-DEVUELTA, NOABONADA-ERROR-CUENTA (en esos estados de cuota se borra la cuenta bancaria, por lo cual no se podrá domiciliar el cobro)	
			
			$arrayFormDatosElegidos['ORDENARCOBROBANCO'] = 'SI'; //se excluyen los que tengan ['ORDENARCOBROBANCO']='NO' elegido por tesorero, aunque esté pendiente de cobro;    
			/*En el caso de CUENTAIBAN de otros países SEPA distintos de ES, ORDENARCOBROBANCO por defecto es SI, 
					entonces se podría envíar email con esta función "datosEmailAvisoProximoCobro()", pero tesorería puede poner a NO si no quiere 
					hacer orden de cobro domiciliado y entonces se podría envíar email con la función "datosEmailAvisoCuotaNoCobradaSinCC()"*/		
		
			$condicionEmailError = 'NO';//solo se busca a los que EMAILERROR = 'NO'		
			
   //echo "<br><br>1-2 modeloTesorero:exportarEmailDomiciliadosPendientes:arrayFormDatosElegidos: ";print_r($arrayFormDatosElegidos);			

			$arrSQLBuscarCuotasSocios =	cadBuscarCuotasEmailSocios($codAreaCoordinacion,$condicionEmailError,$arrayFormDatosElegidos);//en modeloTessorero.php
		
			//echo "<br><br>2 modeloTesorero:exportarEmailDomiciliadosPendientes:arrSQLBuscarCuotasSocios: ";print_r($arrSQLBuscarCuotasSocios);
			
			if ($arrSQLBuscarCuotasSocios['codError'] !== '00000')		
			{ 
					$arrExportarEmailCuotasDomiPend['codError'] = $arrSQLBuscarCuotasSocios['codError'];
					$arrExportarEmailCuotasDomiPend['errorMensaje'] = $arrSQLBuscarCuotasSocios['errorMensaje'];
					$textoErrores = "ERROR: Al exportar emails de socios/as para avisar de próxima orden de cobro de cuota domiciliada. CODERROR: ".$arrExportarEmailCuotasDomiPend['codError'].$arrExportarEmailCuotasDomiPend['errorMensaje'];						 
					$arrExportarEmailCuotasDomiPend['textoComentarios'] = " Función: ".$nomScriptFuncionError."cadBuscarCuotasEmailSocios(). Gestor CODUSER: ".$_SESSION['vs_CODUSER'].$textoErrores;							
					insertarError($arrExportarEmailCuotasDomiPend);	
			}
			else //$arrSQLBuscarCuotasSocios['codError'] == '00000'
			{		
			 $arrDatosCobroCuotas = buscarCadSql($arrSQLBuscarCuotasSocios['cadSQLBuscarCuotasSocios'],$conexionDB['conexionLink']);//en modeloMySQL.php, probado error			
			
		 	//echo "<br><br>3 modeloTesorero:exportarEmailDomiciliadosPendientes:arrDatosCobroCuotas: ";print_r($arrDatosCobroCuotas);

			 if ($arrDatosCobroCuotas['codError'] !== '00000')//probado error sistema OK, con insertarError() 
		 	{ 
					$arrExportarEmailCuotasDomiPend['codError'] = $arrDatosCobroCuotas['codError'];
					$arrExportarEmailCuotasDomiPend['errorMensaje'] = $arrDatosCobroCuotas['errorMensaje'];
					$textoErrores = "ERROR: Al exportar emails de socios/as para avisar de próxima orden de cobro de cuota domiciliada. CODERROR: ".$arrExportarEmailCuotasDomiPend['codError'].$arrExportarEmailCuotasDomiPend['errorMensaje'];						 
					$arrExportarEmailCuotasDomiPend['textoComentarios'] =  " Función: ".	$nomScriptFuncionError.":buscarCadSql():cadBuscarCuotasEmailSocios(). Gestor CODUSER: ".$_SESSION['vs_CODUSER'].$textoErrores;							
					insertarError($arrExportarEmailCuotasDomiPend);	
			 }
		 	elseif ($arrDatosCobroCuotas['numFilas'] === 0)
		 	{$arrExportarEmailCuotasDomiPend['codError'] = '80001';	
					$arrExportarEmailCuotasDomiPend['textoComentarios'] =  "No se han encontrado datos que cumplan las condiciones de búsqueda elegidas.";		
		 	}
		 	else	//arrDatosCobroCuotas['numFilas']>0)
			 {
					//echo "<br><br>4 modeloTesorero:exportarEmailDomiciliadosPendientes:arrDatosCobroCuotas: ";print_r($arrDatosCobroCuotas);
		
			 	$nomFile = 'emailsAvisoProximaOrdenCobro';
					
			 	$arrExportarEmailsWrite = exportarEmailsWrite($arrDatosCobroCuotas,$nomFile);//en modeloTesorero		
					
			 	if ($arrExportarEmailsWrite['codError'] !== '00000')//No creo que entre aquí en caso error en caso error en exportarExcelTexto()
			 	{$arrExportarEmailCuotasDomiPend['codError'] = $arrExportarEmailsWrite['codError'];
						$arrExportarEmailCuotasDomiPend['errorMensaje'] = $arrExportarEmailsWrite['errorMensaje'];
						$textoErrores = "ERROR: Al exportar emails de socios/as para avisar de próxima orden de cobro de cuota domiciliada. CODERROR: ".$arrExportarEmailsWrite['codError'].$arrExportarEmailsWrite['errorMensaje'];						 
						$arrExportarEmailCuotasDomiPend['textoComentarios'] = date('Y-m-d:H:i:s')." Función: ".$nomScriptFuncionError.":exportarEmailDomiciliadosPendWrite(). Gestor CODUSER: ".$_SESSION['vs_CODUSER'].$textoErrores;		
						insertarError($arrExportarEmailCuotasDomiPend);	//en modeloErrores.php';											
			 	}
			 }//else	arrDatosCobroCuotas['numFilas']>0
			}//else $arrSQLBuscarCuotasSocios['codError'] == '00000'	
 	}//else !	if ( (!isset($arrayFormDatosElegidos) || empty($arrayFormDatosElegidos)) || ....
	}//if ($conexionDB['codError']=='00000')	

 //echo '<br><br>5 modeloTesorero:exportarEmailDomiciliadosPendientes:aarrExportarEmailCuotasDomiPend: ';print_r($arrExportarEmailCuotasDomiPend);//no se verá porque el buffer está cautivo
	
	return $arrExportarEmailCuotasDomiPend;	
}
/*---------------------------- Fin exportarEmailDomiciliadosPendientes ---------------------------------*/

/*---------------------------- Inicio exportarEmailSinCCSEPAPendientes -----------------------------------
Genera y exporta a un archivo -txt los emails en forma de lista separados por (;) para copiar y pegar 
en el correo de NODO50 (tesoreria@europalica.org), y enviar un email a los socios de la lista, 
con texto libre para avisar a los socios que aún "no han abonado la cuota".

LLama a modeloTesorero: "cadBuscarCuotasEmailSocios()", que forma la cadena select sql que busca Emails 
y otros datos de todos las cuotas de los socios, según la selección (agrup. territorial, año actual,etc.,
provenientes del formulario ejecuta la select y devuelve un array con los datos obtenidos.

Con el array obtenido de la Select, en la función "modeloTesorero.php:exportarEmailsWrite()" se genera 
la lista de emails sperados por (;) y dentro de esa función se llama a la función "exportarBufferFicheroTXT()"
para generar y descargar el archivo -txt con los emails.

La select puede elegir: 
- No tiene cuenta bancaria domiciliada, - Tiene cuenta bancaria de países NO SEPA (o no es IBAN, ya 
  no se permite CUENTAS NO IBAN, este caso devolverá 0 socios), - Cuenta bancaria de países SEPA (distintos
 	de España), junto con "Ordenar cobro banco = NO" (por falta de BIC necesario para otros países SEPA, 
		por eso se envía este email de aviso no pagado)
- FechaAltaExentosPago		
- Agrupaciones seleccionadas

INCLUIRÁ:
- Socios que estén de alta e incluye: alta,alta-sin-password-excel,alta-sin-password-gestor
- Estado Cuotas de socios:'PENDIENTE-COBRO','ABONADA-PARTE','NOABONADA-DEVUELTA','NOABONADA-ERROR-CUENTA',
  'NOABONADA-ERROR-CUENTA' 
- 'ORDENARCOBROBANCO' = 'NO' (no está domiciliada o no hay orden de cobro)
- $condicionEmailError = 'NO'
- Actualmente recibe siempre $codAreaCoordinacion ='%', pero se deja por si en su momento se crea tesorero 
  a nivel área de coordinacion 

EXCLUIDOS:
- Error email, o falta email.
- Cuotas "abonadas, "exentas (honorarios"
- Socios que estén de "baja" en el momento actual
- Los que se dieron de alta después de la fecha elegida en formulario (en noviembre o diciembre)

RECIBE: 
- $arrayFormDatosElegidos: (año cuota, fecha exención pago, cuenta banco España o SEPA,
  agrupaciones elegidas) 
- $codAreaCoordinacion=%, que proceden del cTesorero.php ( o formulario)

DEVUELVE: array "$arrExportarEmailSinCCPend" con los mensajes correspondientes y códigos de error, 
además de crear el archivo -txt- y descargar en el directorio descargas por defecto

LLAMADA: cTesorero:exportarEmailDomiciliadosPendSinCC()
LLAMA: modeloTesorero.php:cadBuscarCuotasEmailSocios(),exportarEmailsWrite() 
       modeloMySQL.php:buscarCadSql()
									
OBSERVACIÓN: 	Probada PHP 7.3.21

Muy parecida a 	exportarEmailDomiciliadosPendientes(), la dejo separada para mas flexibilidad de cara a cambios	

OJO NO PONER NINGUNA SALIDA A PANTALLA (echo, notice, etc.) No poner echo: daría error
al formar el buffer de salida a txt ya que utiliza "header()" y no puede 
haber ningúna salida delante.		Mejor que esté desactivado E-NOTICE						
---------------------------------------------------------------------*/
function exportarEmailSinCCSEPAPendientes($codAreaCoordinacion,$arrayFormDatosElegidos)	
{
	//echo "<br><br>0-1 modeloTesorero:exportarEmailSinCCSEPAPendientes:arrayFormDatosElegidos: ";print_r($arrayFormDatosElegidos);
	
	require_once 'BBDD/MySQL/modeloMySQL.php';
	require_once './modelos/modeloErrores.php';	

	$arrExportarEmailSinCCPend['nomScript'] = "modeloTesorero.php";
	$arrExportarEmailSinCCPend['nomFuncion'] = "exportarEmailSinCCSEPAPendientes";
	$arrExportarEmailSinCCPend['codError'] = '00000';
	$arrExportarEmailSinCCPend['errorMensaje'] = '';	 
	$nomScriptFuncionError = "modeloTesorero.php:exportarEmailSinCCSEPAPendientes(): ERROR: ";
 
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	require_once "./modelos/BBDD/MySQL/conexionMySQL.php";	

	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);
	
	if ($conexionDB['codError'] !== '00000')	
	{$arrExportarEmailSinCCPend = $conexionDB;
	}     
	else //if ($conexionDB['codError']=='00000')
	{
		/*if de control de existencia de "$codAreaCoordinacion, $arrayFormDatosElegidos" ya que son datos necesarios y críticos 
		  Nota: Ya no sería necesario este if, porque también se controla $arrayFormDatosElegidos en "cadBuscarCuotasEmailSocios()"  
		*/		 	
	 if ( (!isset($arrayFormDatosElegidos['anioCuotasElegido']) || empty($arrayFormDatosElegidos['anioCuotasElegido'])) || 
							(!isset($arrayFormDatosElegidos['fechaAltaExentosPago'] ) || empty($arrayFormDatosElegidos['fechaAltaExentosPago'])) || 
						 (!isset($arrayFormDatosElegidos['paisCC'] ) || empty($arrayFormDatosElegidos['paisCC'])) ||
							(!isset($arrayFormDatosElegidos['agrupaciones']  ) || empty($arrayFormDatosElegidos['agrupaciones'] )) || 
							(!isset($codAreaCoordinacion ) || empty($codAreaCoordinacion ))								
					)
		{
			$arrExportarEmailSinCCPend['codError'] = '70601';
			$arrExportarEmailSinCCPend['errorMensaje'] = ' ERROR: Faltan datos necesarios para envíar email de aviso de cuota no pagada. (de algún campo de $arrayFormDatosElegidos)';		
			$arrExportarEmailSinCCPend['textoComentarios'] = " Función: ".$nomScriptFuncionError."cadBuscarCuotasEmailSocios(). Gestor CODUSER: ".$_SESSION['vs_CODUSER'];
			//echo "<br><br>1-1 modeloTesorero:datosEmailAvisoCuotaNoCobradaSinCC:arrExportarEmailSinCCPend: ";print_r($arrExportarEmailSinCCPend);
		}	
		else //!	if ( (!isset($arrayFormDatosElegidos) || empty($arrayFormDatosElegidos)) || ....
		{				
			//--- Los siguientes valores, no se incluyen en el formulario aun podrían venir de cTesorero.php o desde formulario:
			$arrayFormDatosElegidos['ESTADO'] = 'alta';//solo se cobra a socios que estén de alta e incluye: alta,alta-sin-password-excel,alta-sin-password-gestor			
			$arrayFormDatosElegidos['estadosCuotas'] = array('PENDIENTE-COBRO'=>'PENDIENTE-COBRO','ABONADA-PARTE'=>'ABONADA-PARTE','NOABONADA-DEVUELTA'=>'NOABONADA-DEVUELTA','NOABONADA-ERROR-CUENTA'=>'NOABONADA-ERROR-CUENTA');	

			$arrayFormDatosElegidos['ORDENARCOBROBANCO'] = 'NO';
			/*En el caso de CUENTAIBAN de otros países SEPA distintos de ES, ORDENARCOBROBANCO por defecto es SI, 
					entonces se podría envíar email con esta función "datosEmailAvisoProximoCobro()", pero tesorería puede poner a NO si no quiere 
					hacer orden de cobro domiciliado y entonces se podría envíar email con la función "datosEmailAvisoCuotaNoCobradaSinCC()"*/	
					
			$condicionEmailError = 'NO'; //será MIEMBRO.EMAILERROR = 'NO', solo se busca a los que el EMAILERROR = 'NO'
			
			//echo "<br><br>1-2 modeloTesorero:datosEmailAvisoCuotaNoCobradaSinCC:arrayFormDatosElegidos: ";print_r($arrayFormDatosElegidos);			

			$arrSQLBuscarCuotasSocios =	cadBuscarCuotasEmailSocios($codAreaCoordinacion,$condicionEmailError,$arrayFormDatosElegidos);//en modeloTessorero.php
		
			//echo "<br><br>2 modeloTesorero:exportarEmailSinCCSEPAPendientes:arrSQLBuscarCuotasSocios: ";print_r($arrSQLBuscarCuotasSocios);
			
			if ($arrSQLBuscarCuotasSocios['codError'] !== '00000')		
			{ 
					$arrExportarEmailSinCCPend['codError'] = $arrSQLBuscarCuotasSocios['codError'];
					$arrExportarEmailSinCCPend['errorMensaje'] = $arrSQLBuscarCuotasSocios['errorMensaje'];
					$textoErrores = "ERROR: Al buscar datos de de socios/as con cuota aún no pagada. CODERROR: ".$arrExportarEmailSinCCPend['codError'].$arrExportarEmailSinCCPend['errorMensaje'];						 
					$arrExportarEmailSinCCPend['textoComentarios'] = " Función: ".$nomScriptFuncionError."cadBuscarCuotasEmailSocios(). Gestor CODUSER: ".$_SESSION['vs_CODUSER'].$textoErrores;							
					insertarError($arrExportarEmailSinCCPend);	
			}
			else //$arrSQLBuscarCuotasSocios['codError'] == '00000'
			{	
				$arrDatosCobroCuotas = buscarCadSql($arrSQLBuscarCuotasSocios['cadSQLBuscarCuotasSocios'],$conexionDB['conexionLink']);//en modeloMySQL.php, probado error
				
				//echo "<br><br>3 modeloTesorero:exportarEmailSinCCSEPAPendientes:arrDatosCobroCuotas: ";print_r($arrDatosCobroCuotas);

				if ($arrDatosCobroCuotas['codError'] !== '00000')//probado error sistema OK, con insertarError() 
				{ 
						$arrExportarEmailSinCCPend['codError'] = $arrDatosCobroCuotas['codError'];
						$arrExportarEmailSinCCPend['errorMensaje'] = $arrDatosCobroCuotas['errorMensaje'];
						$textoErrores = "ERROR: Al exportar emails de socios/as para recordar cuota aún no pagada. CODERROR: ".$arrExportarEmailSinCCPend['codError'].$arrExportarEmailSinCCPend['errorMensaje'];						 
						$arrExportarEmailSinCCPend['textoComentarios'] =  " Función: ".	$nomScriptFuncionError.":buscarCadSql():cadBuscarCuotasEmailSocios(). Gestor CODUSER: ".$_SESSION['vs_CODUSER'].$textoErrores;							
						insertarError($arrExportarEmailSinCCPend);	
				}
				elseif ($arrDatosCobroCuotas['numFilas'] === 0)//probado error ok. No hay cuotas domiciliadas pendientes para condiciones búsqueda, NO ES ERROR, no inserta error, pero se muestra en pantalla 
				{			
						$arrExportarEmailSinCCPend['codError'] = '80001';//No sería verdaderemente un error, acaso se podría llevar a controlador?
						$arrExportarEmailSinCCPend['textoComentarios'] = "No se han encontrado datos que cumplan las condiciones de búsqueda elegidas.";									
				}
				else	//arrDatosCobroCuotas['numFilas']>0)
				{ //echo "<br><br>4 modeloTesorero:exportarEmailSinCCSEPAPendientes:arrDatosCobroCuotas: ";print_r($arrDatosCobroCuotas);
      
						$nomFile = 'emailSinCCPendientePago';
						
						$arrExportarEmailsWrite = exportarEmailsWrite($arrDatosCobroCuotas,$nomFile);//en modeloTesorero

						//echo '<br><br>4-2 modeloTesorero:exportarExcelCuotasInternoTes:$resExportarCuotas';print_r($resExportarCuotas);
					
					if ($arrExportarEmailsWrite['codError'] !== '00000')//No creo que entre aquí en caso error en caso error en exportarExcelTexto()
					{ $arrExportarEmailSinCCPend['codError'] = $arrExportarEmailsWrite['codError'];
			   	$arrExportarEmailSinCCPend['errorMensaje'] = $arrExportarEmailsWrite['errorMensaje'];
				   $textoErrores = "ERROR: Al exportar emails de socios/as para recordar cuota aún no pagada. CODERROR: ".$arrExportarEmailsWrite['codError'].$arrExportarEmailsWrite['errorMensaje'];						 
							$arrExportarEmailSinCCPend['textoComentarios'] = date('Y-m-d:H:i:s')." Función: ".$nomScriptFuncionError.":exportarEmailDomiciliadosPendWrite(). Gestor CODUSER: ".$_SESSION['vs_CODUSER'].$textoErrores;		
							insertarError($arrExportarEmailSinCCPend);	//en modeloErrores.php';											
					}
				}//else arrDatosCobroCuotas['numFilas']>0)
			}//else $arrSQLBuscarCuotasSocios['codError'] == '00000'			
 	}//else !	if ( (!isset($arrayFormDatosElegidos) || empty($arrayFormDatosElegidos)) || ....
	}//if ($conexionDB['codError']=='00000')	
 
//echo '<br><br>5 modeloTesorero:exportarEmailSinCCSEPAPendientes:arrExportarEmailSinCCPend: ';print_r($arrExportarEmailSinCCPend);//no se verá porque el buffer está cautivo

	return $arrExportarEmailSinCCPend;
}
/*---------------------------- Fin exportarEmailSinCCSEPAPendientes -------------------------------------*/

/*--------------------exportarEmailDomiciliadosPendWrite (para exportarEmail...) -------------------------          
En esta función se genera el fichero texto -txt- con emails de los socios sleccionados separados por (;) 
y se descarga en el PC, para cortar y pegar para enviar emails los de aviso a los	socios	antes de enviar 
la órden de cobro en los bancos, (B.Santander SEPA-XML), o para avisar a los que no tienen cuenta domicilida
o NO es SEPA y que deben ingresar la cuota. 

RECIBE:	Un array "$reExportar" procedente de de las funciones de modeloTesorero.php:"exportarEmailDomiciliadosPendientes" y 
        "exportarEmailSinCCSEPAPendientes()" con los EMAIL de los socios y otros datos, (aquí solo utiliza los emails)
								"$nomFile" string con nombre del fichero a generar.
								
DEVUELVE: El archivo descargado en el PC o array con mensajes de error.
								
LLAMADA: modeloTesorero.php: exportarEmailDomiciliadosPendientes() y exportarEmailSinCCSEPAPendientes()
LLAMA: modelos/libs/exportarBufferFicheroTXT.php:exportarBufferFicheroTXT()
										 
OBSERVACIONES:
OJO: exportarBufferFicheroTXT() usa la función header (no se puede utilizar echo u otras salidas de pantalla
     antes de esta función pues daría error al formar el buffer de salida a txt	)	
---------------------------------------------------------------------------------------------------------*/
function exportarEmailsWrite($reExportar,$nomFile)
{
	//echo "<br><br>0-1 modeloTesorero.php:exportarEmailsWrite:reExportar: ",print_r($reExportar); 
	
	$reExportar['codError'] = '00000';
	$reExportar['errorMensaje'] = '';	
	
	if (!isset($nomFile) || empty($nomFile) )
	{	
	  $nomFile = 'emailsAvisoPagoCuotas';
 }
	
	$cadenaTexto ='';
		
	$f = 0;
 $fUltima = $reExportar['numFilas'];
 
 while ($f < $fUltima)
 {					
		$cadenaTexto .= htmlspecialchars(utf8_decode($reExportar['resultadoFilas'][$f]['EMAIL'])).';';
		
		$f++;
	}	
	//echo "<br><br>1 exportarEmailDomiciliadosPendWrite:cadenaTexto: ",print_r($cadenaTexto); 
	
	require_once './modelos/libs/exportarBufferFicheroTXT.php';
	
	$reExportar = exportarBufferFicheroTXT($cadenaTexto,$nomFile);//devuele error

 //echo "<br><br>1 modeloTesorero.php:exportarEmailsWrite:reExportar: ",print_r($reExportar); 
	
	return 	$reExportar;//solo devuelve valores cuando hay error 
}
/*----------------------- Fin exportarEmailsWrite -------------------------------------------------------*/

/*==== FIN: FUNCIONES RELACIONADAS CON EMAILS COBRO CUOTAS ================================================		


/*==== FIN: FUNCIONES RELACIONADAS CON ÓRDENES COBRO CUOTAS BANCOS ==============================================
================================================================================================================*/	

?>