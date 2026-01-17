<?php
/*----------------------------------------------------------------------------------------------------
FICHERO: cPresidenteMostrarEstadoConfSocios.php

DESCRIPCIÓN: Incluye los if's para asignar los adecuados valores a $_SESSION['vs_APE1'], $_SESSION['vs_APE2']
y 	$_SESSION['vs_estadoConfirmacion'] que se usan para $_SESSION['vs_pag_actual'] y que se utilizaran 
para guardar los valores para la función que genera una compleja cadena SELECT:
"modeloPresCood:cadBuscarEstadoConfirmacionAltasGestor()" que se pasará después a "mPaginarLib" 
para el sistema de paginación

INCLUIDO DESDE: cPresidente.php: mostrarEstadoConfirmacionSocios()

LLAMA:	modeloPresCoord.php:cadBuscarEstadoConfirmacionAltasGestor() y en esa funcion no devuelve $arrBindParametr,
sería mas compleja con las UNION select que incluye. 
Usará la consulta PDO tal como está aquí, y probada sin problemas.

OBSERVACIONES: PHP 7.3.21
añado comentarios. Subida a usuarios. Acaso se pudiera simplificar algo 								
----------------------------------------------------------------------------------------------------*/

$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
$datosFormElegirApeEstadoConf = array();//añadido para evitar notice
$datosFormElegirApeEstadoConf['codError'] ='00000';

if ( isset($_POST['datosFormElegirApeEstadoConf']['APE1']) || isset($_POST['datosFormElegirApeEstadoConf']['APE2']) ||
			  isset($_POST['datosFormElegirApeEstadoConf']['CONFIRMACIONEMAIL'])	)				
{ echo "<br><br> 1";

  //------------------------ Inicio control  'APE1'y 'APE2' ------------------------------------

		if (isset($_POST['datosFormElegirApeEstadoConf']['APE1']) || isset($_POST['datosFormElegirApeEstadoConf']['APE2']) )//Si existe APE1 o APE2 aunque esté empty, ha pulsado botón buscar por apellidos			
		{ echo "<br><br> 1-1"; 	
	
			if ( empty($_POST['datosFormElegirApeEstadoConf']['APE1']) && empty($_POST['datosFormElegirApeEstadoConf']['APE2']) )//Si campo APE1 o APE2 están empty				
			{ 
						echo "<br><br> 1-1-1"; //Sí entro								
						/*Si se busca por APE1 y APE2 y están vacíos " ", debiera tratarse como error lógico, y pedir introducir Ape1 o Ape2 
								(además si busca en antiguas bajas de más de 5 años con esos dos campos empty no deben seleccionarse ya estarían borrados nombres)	
								$datosFormElegirApeEstadoConf['APE1']['errorMensaje']= 'Al menos uno de los apellidos no puede estar vacío';							
								$cadApe1='-***-';$cadApe2='-***-';//sería otra opción: caracteres no permitidos para los apellidos, la select devolverá 0 filas
						*/
						$datosFormElegirApeEstadoConf['codError'] = '80001';//error lógico
						$datosFormElegirApeEstadoConf['errorMensaje'] = 'Error campos apellidos vacíos, al menos debes poner un apellido';
						$datosFormElegirApeEstadoConf['APE1']['errorMensaje'] = $datosFormElegirApeEstadoConf['errorMensaje'];
						
						$_pag_propagar_opcion_buscar = 'APE';		
			}
			else	// !if ( empty($_POST['datosFormElegirApeEstadoConf']['APE1']) && empty($_POST['datosFormElegirApeEstadoConf']['APE2']) )/						
			{ echo "<br><br> 1-1-2"; 	//se selecciona ape1 y ape2

					$datosFormElegirApeEstadoConf['APE1']['valorCampo'] = $_POST['datosFormElegirApeEstadoConf']['APE1'];//No se valida APE1 y APE2, al ser pres no hay riesgo	
					$datosFormElegirApeEstadoConf['APE2']['valorCampo'] = $_POST['datosFormElegirApeEstadoConf']['APE2'];	
					
     //unset($_SESSION['vs_pag_actual']); //al buscar por un nombre hay que iniciar siempre en la 1ª pág.	
					
     $_pag_propagar_opcion_buscar = 'APE';					
							
					$_SESSION['vs_estadoConfirmacion'] = 'pendiente_confirmar_algo';
					$datosFormElegirApeEstadoConf['CONFIRMACIONEMAIL']['valorCampo'] =		$_SESSION['vs_estadoConfirmacion'];
					
					if ( (!isset($_SESSION['vs_APE1']) || $_SESSION['vs_APE1'] !== $cadApe1) || (!isset($_SESSION['vs_APE2']) || $_SESSION['vs_APE2'] !== $cadApe2))//acaso sobre este if
					{ echo "<br><br> 1-1-2-1";//Sí entro cuando por primera vez que se pone APE  para buscar, y cuando cambia valores APE1 o APE2
							
							$_SESSION['vs_APE1'] = $_POST['datosFormElegirApeEstadoConf']['APE1'];//No se valida APE1 y APE2, al ser pres no hay riesgo	
							$_SESSION['vs_APE2'] = $_POST['datosFormElegirApeEstadoConf']['APE2'];		
							
       unset($_SESSION['vs_pag_actual']); //al cambiar de Ape hay que iniciar siempre en la 1ª pág.											
					}
			}//else	 !if ( empty($_POST['datosFormElegirApeEstadoConf']['APE1']) && empty($_POST['datosFormElegirApeEstadoConf']['APE2']) )	 
				//------------------------ Fin control buscar por 'APE1'y 'APE2'-------------------------------- 
		}//fin if ((isset($_POST['datosFormElegirApeEstadoCon']['APE1']) || (isset($_POST['datosFormElegirApeEstadoCon']['APE2']) 
		//------------------------ Fin control 'APE1'y 'APE2'--------------------------------- 
		
  //------------------------ Inicio control buscar por CONFIRMACIONEMAIL ----------------		
  else //si hay $_POST['datosFormElegirApeEstadoConf']['CONFIRMACIONEMAIL']
		{echo "<br><br> 1-2";
			
			$_SESSION['vs_estadoConfirmacion'] = $_POST['datosFormElegirApeEstadoConf']['CONFIRMACIONEMAIL'];
			$datosFormElegirApeEstadoConf['CONFIRMACIONEMAIL']['valorCampo'] = $_SESSION['vs_estadoConfirmacion'];
			
			$_pag_propagar_opcion_buscar = 'ESTADOCONFIRMACION';			
						
		 unset($_SESSION['vs_APE1']);
			unset($_SESSION['vs_APE2']);
	  unset($_SESSION['vs_pag_actual']);//al buscar se ponga la principio de todas las pág. de la lista
			
  }
		//------------------------ Fin control buscar por CONFIRMACIONEMAIL ------------------				
}	//isset($_POST['datosFormMiembro']['APE1']) || ....
else //--Inicio control no hay: ($_POST['datosFormElegirApeEstadoConf']['APE1']) ni $_POST['ddatosFormElegirApeEstadoConf']['CONFIRMACIONEMAIL']-		
     //1ª vez no hay ($_POST['datosFormElegirApeEstadoConf']['APE1']) ni $_POST['ddatosFormElegirApeEstadoConf']['CONFIRMACIONEMAIL']
{echo "<br><br> 2";
 //---------- Inicio procede de dentro de la función mostrarEstadoConfirmacionSocios -----------------
	
	//-----------está moviendóse entre la lista de las distintas pág. de paginación 1,2,3.
 if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cPresidente&accion=mostrarEstadoConfirmacionSocios")	
	{ echo "<br><br> 2-0"; //está moviendóse entre la lista de las distintas pág. de paginación 1,2,3..
	  
			if (isset($_GET['_opcion_buscar']))//cuando se buscan por APE, o ESTADOCONFIRMACION 
			{ 
			 if ($_GET['_opcion_buscar'] == 'APE')//(para un busqueda de APE1,APE2, puede haber pág. 1, 2, 3...siguiente,última que se pueden elegir)
				{
					echo "<br><br> 2-0-1";
					$datosFormElegirApeEstadoConf['APE1']['valorCampo'] = $_SESSION['vs_APE1'];//para que se muestre en la cab del formulario
				 $datosFormElegirApeEstadoConf['APE2']['valorCampo'] = $_SESSION['vs_APE2'];
					
					$_SESSION['vs_estadoConfirmacion'] = 'pendiente_confirmar_algo';
											
					$_pag_propagar_opcion_buscar = 'APE';
					
				 unset($_SESSION['vs_pag_actual']);
				}
				elseif ($_GET['_opcion_buscar'] == 'ESTADOCONFIRMACION')//(para un busqueda de un ESTADOCONFIRMACION haber pág. 1, 2, 3...siguiente,última que se pueden elegir).
				{echo "<br><br> 2-0-2";//si
   
					$datosFormElegirApeEstadoConf['CONFIRMACIONEMAIL']['valorCampo'] =	$_SESSION['vs_estadoConfirmacion'];
											
					$_pag_propagar_opcion_buscar = 'ESTADOCONFIRMACION';				
			  
					unset($_SESSION['vs_APE1']);
					unset($_SESSION['vs_APE2']);
     unset($_SESSION['vs_pag_actual']);
				}
			}
			else //(!isset($_GET['_opcion_buscar']))//entra cuando estando en mostrar se pulsa el menú izdo Confirmar socios
			{echo "<br><br> 2-0-3";//no hay $_GET['_opcion_buscar' se busca por AP1, APE2 y no se han introducido valores: se buscan todos
	
				$_SESSION['vs_estadoConfirmacion'] = 'pendiente_confirmar_algo';
				$datosFormElegirApeEstadoConf['CONFIRMACIONEMAIL']['valorCampo'] =	$_SESSION['vs_estadoConfirmacion'];
				
				$_pag_propagar_opcion_buscar = 'ESTADOCONFIRMACION';				
				
				unset($_SESSION['vs_APE1']); 
				unset($_SESSION['vs_APE2']);			
			 unset($_SESSION['vs_pag_actual']);
				
			}   
	}//no hay GET[_opcion_buscar]
	//-- Fin if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cPresidente&accion=mostrarEstadoConfirmacionSocios")	 
	elseif ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']!=="index.php?controlador=cPresidente&accion=reenviarEmailConfirmarSocioAltaGestor" &&
								  $_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']!=="index.php?controlador=cPresidente&accion=anularSocioPendienteConfirmarPres" &&
							   $_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']!=="index.php?controlador=cPresidente&accion=confirmarAltaSocioPendientePorGestor"
										
			      )	//procede de otros niveles inferiores o sin que tenga relacion con este hilo										
	{ echo "<br><br> 2-1"; 		

		 unset($_SESSION['vs_APE1']);
		 unset($_SESSION['vs_APE2']);			
			unset($_SESSION['vs_pag_actual']);//se iniciará en la 1ª pág.	
			
			$_pag_propagar_opcion_buscar = 'ESTADOCONFIRMACION';			
			$_SESSION['vs_estadoConfirmacion'] = 'pendiente_confirmar_algo';
   //antes			$datosFormElegirApeEstadoConf['CONFIRMACIONEMAIL']['valorCampo'];
			$datosFormElegirApeEstadoConf['CONFIRMACIONEMAIL']['valorCampo'] = "";//nuevo
	}		
//-------------		
	else // Inicio No if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cPresidente&accion=mostrarEstadoConfirmacionSocios")	
	{ //viene desde otro nivel (como reenviar,eliminar) y no tendrán valores $datosFormElegirApeEstadoConf[APE1], APE2, form CONFIRMACIONEMAIL, 
	  //por lo que se utiliza lo guardado en sesiones para que vuelva a las pág. previas 
	  echo "<br><br> 2-2";

	  if (isset($_SESSION['vs_APE1']) || isset($_SESSION['vs_APE2']) )//vuelve de un nivel al que fue buscando por nombre
	  { echo "<br><br> 2-2-1"; 
					$datosFormElegirApeEstadoConf['APE1']['valorCampo'] = $_SESSION['vs_APE1'];//para muestrar en la seccion de buscar del formulario
			  $datosFormElegirApeEstadoConf['APE2']['valorCampo'] = $_SESSION['vs_APE2'];
					
			  $_SESSION['vs_estadoConfirmacion'] = 'pendiente_confirmar_algo';
     $datosFormElegirApeEstadoConf['CONFIRMACIONEMAIL']['valorCampo'] =		$_SESSION['vs_estadoConfirmacion'];
					$_pag_propagar_opcion_buscar = 'APE';					

			  //unset($_SESSION['vs_pag_actual']); //??al buscar por un nombre hay que iniciar siempre en la 1ª pág.

	    //echo 	"<br><br>2-2-2";					
			}
			elseif (isset($_SESSION['vs_estadoConfirmacion']))//entra cuando vuelve de otro nivel que fue por buscando por 'ESTADOCONFIRMACION'....
			{ echo "<br><br> 2-2-3";
	
					$datosFormElegirApeEstadoConf['CONFIRMACIONEMAIL']['valorCampo'] =	$_SESSION['vs_estadoConfirmacion'];
											
					$_pag_propagar_opcion_buscar = 'ESTADOCONFIRMACION';			
	    //echo 	"<br><br>2-2-4";	
			}
			else //no tiene variable de sesion, no vuelve de nigun nivel superior
			{//acaso no entre nunca aqui
				echo "<br><br> 2-2-5";
			 /* $_SESSION['vs_estadoConfirmacion'] = 'pendiente_confirmar_algo';
     $datosFormElegirApeEstadoConf['CONFIRMACIONEMAIL']['valorCampo'] =		$_SESSION['vs_estadoConfirmacion'];	
			 */				
			}					
	}//---Fin  o No if ($_SESSION['vs_HISTORIA']['enlaces'] ..... 
	
}//---Fin no hay ($_POST['datosFormElegirApeEstadoConf']['APE1']) ni $_POST['datosFormElegirApeEstadoConf']['CONFIRMACIONEMAIL']
echo "<br><br>3-0-1 cPresidenteMostrarEstadoConfSocios.php:SESSION: ";print_r($_SESSION); 
echo "<br><br>3-0-2 cPresidenteMostrarEstadoConfSocios.php:datosFormElegirApeEstadoConf: ";print_r($datosFormElegirApeEstadoConf); 

if (!isset($_SESSION['vs_APE1'])) //para evitar notices
{$_SESSION['vs_APE1'] = "";	
}
if (!isset($_SESSION['vs_APE2']))
{$_SESSION['vs_APE2'] = "";	
}
//--
	if ($datosFormElegirApeEstadoConf['codError'] !=='00000' && $datosFormElegirApeEstadoConf['codError'] == '80001') //Error campos apellidos vacíos, al menos debes poner un apellido';
	{
	  //$arrDatosCadBuscarOrdenesCobro['datosFormMiembro']['APE1']['errorMensaje'] = $arrDatosCadBuscarOrdenesCobro['errorMensaje'];			
			$datosFormElegirApeEstadoConf['APE1']['errorMensaje'] = $datosFormElegirApeEstadoConf['errorMensaje'];//ya tiene asignado eso
	}
	else// ($arrDatosCadBuscarOrdenesCobro['codError'] == '00000') 
	{
			//require_once './modelos/modeloPresCoord.php';
			//$_pagi_sql = cadBuscarEstadoConfirmacionAltasGestor($_SESSION['vs_APE1'],$_SESSION['vs_APE2'],$_SESSION['vs_estadoConfirmacion']);	
	 //echo 	"<br><br>3-1 cTesoreroOrdenesCobroUnaRemesaPaginar:arrDatosCadBuscarOrdenesCobro: ";print_r($arrDatosCadBuscarOrdenesCobro);

		//$arrDatosCadBuscarOrdenesCobro['pag_propagar_opcion_buscar'] = $_pag_propagar_opcion_buscar;	// contendrá: APE o AGRUPACION
		
		require_once './modelos/modeloPresCoord.php';
  $_pagi_sql = cadBuscarEstadoConfirmacionAltasGestor($_SESSION['vs_APE1'],$_SESSION['vs_APE2'],$_SESSION['vs_estadoConfirmacion']);	

	}
	// Lo siguiente para que se muestre en la cabecera del formulario
	/*$arrDatosCadBuscarOrdenesCobro['datosFormMiembro']['APE1']['valorCampo'] = $cadApe1;//para que se muestre en la cabecera del formulario
	$arrDatosCadBuscarOrdenesCobro['datosFormMiembro']['APE2']['valorCampo'] = $cadApe2;//para que se muestre en la cabecera del formulario
	$arrDatosCadBuscarOrdenesCobro['estadoCuota'] = $_SESSION['vs_ESTADOCUOTA'];//para que se muestre en la cabecera del formulario
	$arrDatosCadBuscarOrdenesCobro['codAgrupacion'] = $_SESSION['vs_CODAGRUPACION'];//para que se muestre en la cabecera del formulario
	$arrDatosCadBuscarOrdenesCobro['datosRemesa'] = $_SESSION['vs_datosFormOrdenCobroRemesa'];//para que se muestre en la cabecera del formulario
*/
//---

//require_once './modelos/modeloPresCoord.php';
//$_pagi_sql = cadBuscarEstadoConfirmacionAltasGestor($_SESSION['vs_APE1'],$_SESSION['vs_APE2'],$_SESSION['vs_estadoConfirmacion']);	

//echo "<br><br>3 cPresidenteMostrarEstadoConfSocios.php:_pagi_sql: ";print_r($_pagi_sql); 			
?>