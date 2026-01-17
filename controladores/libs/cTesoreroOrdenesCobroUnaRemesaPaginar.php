<?php

/*------------------------------------------------------------------------------
FICHERO: cTesoreroOrdenesCobroUnaRemesaPaginar.php, en controladores/libs/

Este función incluye el tratamiento de las variables para la búsqueda de órdenes
de cobro de una remesa concreta, en ella se preparan los parámentros necesarios 
en cada caso para llamar a la función "cadBuscarOrdenesCobroRemesaApeNom()" 
que forma la cadena select correspondiente para que posteriomente en 
"cTesorero:mostrarOrdenesCobroUnaRemesaTes()" se pase a la función
"modelos/libs/mPaginarLib.php", para ejecutar la select y mostrar la tabla
en pantalla paginada.

Incluye los if's para asignar los adecuados valores a $_SESSION['vs_APE1'],
$_SESSION['vs_APE2'],$_SESSION['vs_CODAGRUPACION'],$_SESSION['vs_ESTADOCUOTA'] 
y $_SESSION['vs_pag_actual'] y que se utilizarán para guardar los valores 
para las funciones 

RECIBE: $post (que viene de los formularios), y codAreaCoordinacion 
desde cTesorero:mostrarOrdenesCobroUnaRemesaTes() y 
form: vMostrarOrdenesCobroUnaRemesaInc.php, utilizará $_SESSION variados.

DEVUELVE: array $arrDatosCadBuscarOrdenesCobro, formado en la llamada a las función 
"cadBuscarOrdenesCobroRemesaApeNom() con los campos: select ['cadSQL'], 
y el correspondiente ['arrBindValues'] para ejecutar la consulta. 
También datos en['$datosFormMiembro'] y ['datosRemesa'] y variables $_SESSION 

LLAMADA:cTesorero:mostrarOrdenesCobroUnaRemesaTes()	
LLAMA: modelos/modeloTesorero.php:cadBuscarOrdenesCobroRemesaApeNom()										


OBSERVACIONES: 	
2021-06-09: Probada PDO y PHP 7.3.21

NOTA: Creo que sería posible hacerlo sustituyendo las variables tipo $_SESSION 
por parámetros y variables enviados a los formularios tipo hydden.
Muy parecida a controladores/libs/: cTesoreroCuotasSociosApeNomPaginarInc.php y
cPresCoordSociosApeNomPaginarInc
------------------------------------------------------------------------------*/
function cTesoreroOrdenesCobroUnaRemesaPaginar($post,$codAreaCoordinacion)
{
	//echo 	"<br><br>0-1 cTesoreroOrdenesCobroUnaRemesaPaginar.php:_SESSION: ";print_r($_SESSION); 
	//echo 	"<br><br>0-2 cTesoreroOrdenesCobroUnaRemesaPaginar: ";print_r($post);	

	$arrDatosCadBuscarOrdenesCobro['codError'] = '00000';
	$arrDatosCadBuscarOrdenesCobro['errorMensaje'] = '';
	
	/*Solo la primera vez que se entra en esta función el parámetro "$post" contiene los datos 
	  generales de esa remesa procedentes de form. "tesorero/vEstadoOrdenesCobroRemesasTesInc.php"
	  $post['datosFormOrdenCobroRemesa']=['NOMARCHIVOSEPAXML'],['ANIOCUOTA'],['FECHAORDENCOBRO'],['FECHAPAGO'],['FECHAANOTACIONPAGO']
	  Después los valores de "$post" tendrán los datos procedentes de form. "vMostrarOrdenesCobroUnaRemesaInc.php"
	  Se guadarán como variables de sesión para tenerlos disponibles durante las funciones de "mostrarOrdenesCobroUnaRemesaTes"
	*/	
	
 if (isset($post['datosFormOrdenCobroRemesa']) && !empty($post['datosFormOrdenCobroRemesa']) )//solo la primera vez que entra tendrá valor
	{ 
   $_SESSION['vs_datosFormOrdenCobroRemesa'] = $post['datosFormOrdenCobroRemesa'];	   			
   //echo "<br><br>0-3 cTesoreroOrdenesCobroUnaRemesaPaginar:_SESSION['vs_datosFormOrdenCobroRemesa']: " ; print_r($_SESSION['vs_datosFormOrdenCobroRemesa']);	
	}

	$nomAchivoRemesaSEPAXML = $_SESSION['vs_datosFormOrdenCobroRemesa']['NOMARCHIVOSEPAXML'];//El valor de ['NOMARCHIVOSEPAXML'] es necesario como PK, para la select 
	
	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];//solo para HISTORIA
	$datosFormMiembro = array();

	//echo 	"<br><br>0-4 cTesoreroOrdenesCobroUnaRemesaPaginar:pagActual: ";print_r($pagActual);

	//-------- Inicio búsqueda para APE1 o APE2 o CODAGRUPACION, ESTADOCUOTA ----------	

	if ( isset($post['datosFormMiembro']['APE1']) || isset($post['datosFormMiembro']['APE2']) ||
						isset($post['datosFormSocio']['CODAGRUPACION']) || isset($post['resCuotasSocios']['ESTADOCUOTA']) 
				)	//Si existe algún campo que venga desde formulario aunque esté empty		
	{ 
			//echo "<br><br> 1";
			//------------------------ Inicio control 'APE1'y 'APE2' --------------------
		
			if (isset($post['datosFormMiembro']['APE1']) || isset($post['datosFormMiembro']['APE2']) )//Si existe APE1 o APE2 aunque esté empty, ha pulsado botón buscar por apellidos			
			{ //echo "<br><br> 1-1"; 	
				
				$cadApe1 = $post['datosFormMiembro']['APE1'];
				$cadApe2 = $post['datosFormMiembro']['APE2'];					

				if ( empty($post['datosFormMiembro']['APE1']) && empty($post['datosFormMiembro']['APE2']) )//Si campo APE1 o APE2 están empty				
				{ 
						//echo "<br><br> 1-1-1"; //Sí entro								
						/*Si se busca por APE1 y APE2 y están vacíos " ", debiera tratarse como error lógico, y pedir introducir Ape1 o Ape2 
				    (además si busca en antiguas bajas de más de 5 años con esos dos campos empty no deben seleccionarse ya estarían borrados nombres)	
						  $datosFormMiembro['APE1']['errorMensaje']= 'Al menos uno de los apellidos no puede estar vacío';							
						  $cadApe1='-***-';$cadApe2='-***-';//sería otra opción: caracteres no permitidos para los apellidos, la select devolverá 0 filas
      */
						$arrDatosCadBuscarOrdenesCobro['codError'] = '80001';//error lógico
						$arrDatosCadBuscarOrdenesCobro['errorMensaje'] = 'Error campos apellidos vacíos, al menos debes poner un apellido';
						$arrDatosCadBuscarOrdenesCobro['datosFormMiembro']['APE1']['errorMensaje'] = $arrDatosCadBuscarOrdenesCobro['errorMensaje'];
				}
				elseif ( (!isset($_SESSION['vs_APE1']) || $_SESSION['vs_APE1'] !== $cadApe1) || (!isset($_SESSION['vs_APE2']) || $_SESSION['vs_APE2'] !== $cadApe2))//acaso sobre este if
				{ //echo "<br><br> 1-1-2";//Sí entro cuando por primera vez que se pone APE  para buscar, y cuando cambia valores APE1 o APE2
				
						unset($_SESSION['vs_pag_actual']); //al cambiar de Ape hay que iniciar siempre en la 1ª pág.				
						$_SESSION['vs_APE1'] = $cadApe1;//para que se pueda guardar para cabeceras en cambios de niveles 
						$_SESSION['vs_APE2'] = $cadApe2;	
    }
				$_pag_propagar_opcion_buscar = 'APE'; //buscará por apellidos

				$_SESSION['vs_ESTADOCUOTA'] = '%';				
				$_SESSION['vs_CODAGRUPACION'] = '%';	
				$estadoOrdenCobro = $_SESSION['vs_ESTADOCUOTA'];
				$codAgrupacion = $_SESSION['vs_CODAGRUPACION'];

			}//fin if ((isset($post['datosFormMiembro']['APE1']) || (isset($post['datosFormMiembro']['APE2']) 
			//------------------------ Fin control 'APE1'y 'APE2'------------------------

			//--- Inicio control  CODAGRUPACION, ESTADOCUOTA ----------------------------
			else //if(isset($post['datosFormSocio']['CODAGRUPACION']) || isset($post['resCuotasSocios']['ESTADOCUOTA']))		        
			{	//echo "<br><br> 1-2";//Sí entro cuando se busca en el formulario por cualquier valor de los campos CODAGRUPACION, ESTADOCUOTA 
			  
					unset($_SESSION['vs_pag_actual']); //al cambiar de CODAGRUPACION o ESTADOCUOTA hay que iniciar siempre en la 1ª pág.								
					unset($_SESSION['vs_APE1']);
					unset($_SESSION['vs_APE2']);
					
 				$cadApe1 = NULL; $cadApe2 = NULL;
					
					if ($_SESSION['vs_CODAGRUPACION'] !== $post['datosFormSocio']['CODAGRUPACION'])//ha cambiado de agrupación
					{ //echo "<br><br> 1-2-2";
							$_SESSION['vs_CODAGRUPACION'] = $post['datosFormSocio']['CODAGRUPACION'];								
					}
					if ($_SESSION['vs_ESTADOCUOTA'] !== $post['resCuotasSocios']['ESTADOCUOTA'])//ha cambiado 
					{ //echo "<br><br> 1-2-3";
							$_SESSION['vs_ESTADOCUOTA'] = $post['resCuotasSocios']['ESTADOCUOTA'];
					}					 
					$_pag_propagar_opcion_buscar = 'AGRUPACION';//En relidad buscará por CODAGRUPACION, ESTADOCUOTA				
					
					$estadoOrdenCobro = $_SESSION['vs_ESTADOCUOTA'];
					$codAgrupacion = $_SESSION['vs_CODAGRUPACION'];								
			}
			//--- Fin control  CODAGRUPACION, ESTADOCUOTA -------------------------------		
			
	}	//isset($post['datosFormMiembro']['APE1'])||isset($post['datosFormSocio']['CODAGRUPACION'])||isset($post['resCuotasSocios']['ESTADOCUOTA']) 
	//-------- Fin búsqueda para APE1 o APE2 o CODAGRUPACION, ESTADOCUOTA --------------	

	//-------- Inicio NO hay datos de búsqueda para APE1 o APE2 o CODAGRUPACION, ESTADOCUOTA -----	
	else//NO isset($post['datosFormMiembro']['APE1'])||isset($post['datosFormSocio']['CODAGRUPACION'])||isset($post['resCuotasSocios']['ESTADOCUOTA']) 
	{
		//echo "<br><br> 2"; //1ª vez entra por aquí, también cuando en la lista inicial, agrup, o lista garcia, pulso un núm. pág o siguiente, y cuando vuelve de (Ver,...)  

  if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cTesorero&accion=mostrarOrdenesCobroUnaRemesaTes")			
		{			
				//Entra cuando en la lista agrupación pulso un núm. pág, siguiente, última, y buscar por APE, o CODAGRUPACION, ESTADOCUOTA
				//echo "<br><br> 2-0"; //Sí entro
				
			 if (!isset($_GET['_opcion_buscar'])) //sólo entrará 1ª en menú 
				{			
					//echo "<br><br> 2-0-1";//Primera Vez cuando viene desde menú Estado órdenes cobro remesas y operaciones
		   
					unset($_SESSION['vs_pag_actual']);//se iniciará en la 1ª pág.	de la paginación				
					unset($_SESSION['vs_APE1']);
					unset($_SESSION['vs_APE2']);
					$cadApe1 = NULL; $cadApe2 = NULL;					
					$_pag_propagar_opcion_buscar = 'AGRUPACION';
					
					$_SESSION['vs_ESTADOCUOTA'] = '%';		
					$_SESSION['vs_CODAGRUPACION'] = '%';				
					$estadoOrdenCobro = $_SESSION['vs_ESTADOCUOTA'];
					$codAgrupacion = $_SESSION['vs_CODAGRUPACION'];	
				}//no hay GET[_opcion_buscar]
				
				elseif (isset($_GET['_opcion_buscar']))//entra cuando en la lista pulso un núm. pág, siguiente, última,... (bien en lista agrup o en lista APE repetidos)
				{
					//echo 	"<br><br>2-1-0 cTesorero:libs:cTesoreroCuotasSociosApeNomPaginarInc:_GET: ";print_r($_GET); 
			
					if ($_GET['_opcion_buscar'] == 'APE')//cuando en la lista de busqueda por APE1,APE2 pulso un núm. página, siguiente, última,...
					{
						if (isset($_SESSION['vs_APE1']) || isset($_SESSION['vs_APE2']))//cuando en la lista de busqueda por APE1, APE2 pulso un núm. página, siguiente, última,...					
						{//echo "<br><br> 2-1-1"; //Sí entró
													
							unset($_SESSION['vs_pag_actual']);
				
							$cadApe1 = $_SESSION['vs_APE1'];
							$cadApe2 = $_SESSION['vs_APE2'];	
							
							$_pag_propagar_opcion_buscar = 'APE';

							$_SESSION['vs_ESTADOCUOTA'] = '%';
							$_SESSION['vs_CODAGRUPACION'] = '%';	
							$estadoOrdenCobro = $_SESSION['vs_ESTADOCUOTA'];
							$codAgrupacion = $_SESSION['vs_CODAGRUPACION'];	
					
						}				
					}//if ($_GET['_opcion_buscar'] == 'APE')

					elseif ($_GET['_opcion_buscar'] == 'AGRUPACION')//cuando en la lista de búsqueda por CODAGRUPACION,ESTADOCUOTA pulso un núm. página, siguiente, última,...
					{//echo "<br><br> 2-1-2";	//Sí entró	
						
						unset($_SESSION['vs_pag_actual']);//se iniciará en la 1ª pág.	de la paginación				
						unset($_SESSION['vs_APE1']);
						unset($_SESSION['vs_APE2']);

						$cadApe1 = NULL; $cadApe2 = NULL;

						$_pag_propagar_opcion_buscar = 'AGRUPACION';
						
						$estadoOrdenCobro = $_SESSION['vs_ESTADOCUOTA'];
						$codAgrupacion = $_SESSION['vs_CODAGRUPACION'];
					
					}//elseif ($_GET['_opcion_buscar'] == 'AGRUPACION')
					
					else //No hay GET[_opcion_buscar]== 'AGRUPACION') y tampoco hay($_GET['_opcion_buscar'] == 'AGRUPACION' ***sobra***
					{	//echo "<br><br> 2-1-3";//no entro nunca				
					}	
					
				}//if (isset($_GET['_opcion_buscar']) )	
			
		}//if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cTesorero&accion=mostrarOrdenesCobroUnaRemesaTes")
	
		elseif($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] =="index.php?controlador=cTesorero&accion=mostrarIngresoCuotaAnio")		
		{ 
				//--- Inicio cuando vuelve de Acciones: Ver,... y vuelve a la página de la que salió 
				
				//echo "<br><br> 2-2"; //si entró al volver de mostrar una orden de cobro de un socio
					
				if (isset($_SESSION['vs_APE1']) || isset($_SESSION['vs_APE2']))//cuando vuelve de Acciones: Ver,... y procedía lista de búsqueda por APE1 o APE2
				{
					//echo "<br><br> 2-2-1"; //Sí entró	al volver de mostrar una orden de cobro de un socio buscada por APE

					$cadApe1 = $_SESSION['vs_APE1'];
					$cadApe2 = $_SESSION['vs_APE2'];
					
					$_pag_propagar_opcion_buscar = 'APE';	
					
					$_SESSION['vs_ESTADOCUOTA'] = '%';							
					$_SESSION['vs_CODAGRUPACION'] = '%';				
					$estadoOrdenCobro = $_SESSION['vs_ESTADOCUOTA'];
					$codAgrupacion = $_SESSION['vs_CODAGRUPACION'];	
					
				}//if (isset($_SESSION['vs_APE1']) || isset($_SESSION['vs_APE2']))
				
				else // !if (isset($_SESSION['vs_APE1']) || isset($_SESSION['vs_APE2']))//cuando vuelve de Ver, cuando procedía lista de búsqueda por CODAGRUPACION, ESTADOCUOTA 
				{
					//echo "<br><br> 2-2-2";//Sí entró	al volver de mostrar una orden de cobro de un socio buscada por CODAGRUPACION, ESTADOCUOTA
					
					$cadApe1 = NULL; $cadApe2 = NULL;	
					
					$_pag_propagar_opcion_buscar = 'AGRUPACION';//buscará por CODAGRUPACION, ESTADOCUOTA

					$estadoOrdenCobro = $_SESSION['vs_ESTADOCUOTA'];
					$codAgrupacion = $_SESSION['vs_CODAGRUPACION'];	// aqui no seria %		
				}	
    //--- Fin cuando vuelve de Acciones: Ver,... y vuelve a la página de la que salió 
				
		}//elseif ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] =="index.php?controlador=cTesorero&accion=mostrarIngresoCuotaAnio ..."		
		
	} //no (isset($post['datosFormMiembro']['APE1'])|| ... ||		(isset($post['datosFormSocio']['CODAGRUPACION'] ...
 //-------- Fin NO hay datos de búsqueda para APE1 o APE2 o CODAGRUPACION, ESTADOCUOTA ------	

 
	if ($arrDatosCadBuscarOrdenesCobro['codError'] !=='00000' && $arrDatosCadBuscarOrdenesCobro['codError'] == '80001') //Error campos apellidos vacíos, al menos debes poner un apellido';
	{
	  $arrDatosCadBuscarOrdenesCobro['datosFormMiembro']['APE1']['errorMensaje'] = $arrDatosCadBuscarOrdenesCobro['errorMensaje'];			
	}
	else// ($arrDatosCadBuscarOrdenesCobro['codError'] == '00000') 
	{
		require_once "./modelos/modeloTesorero.php";			
		$arrDatosCadBuscarOrdenesCobro =	cadBuscarOrdenesCobroRemesaApeNom($codAreaCoordinacion,$codAgrupacion,$estadoOrdenCobro,$cadApe1,$cadApe2,$nomAchivoRemesaSEPAXML);//en modeloTesorero.php
		
	 //echo 	"<br><br>3-1 cTesoreroOrdenesCobroUnaRemesaPaginar:arrDatosCadBuscarOrdenesCobro: ";print_r($arrDatosCadBuscarOrdenesCobro);

		$arrDatosCadBuscarOrdenesCobro['pag_propagar_opcion_buscar'] = $_pag_propagar_opcion_buscar;	// contendrá: APE o AGRUPACION

	}
	// Lo siguiente para que se muestre en la cabecera del formulario
	$arrDatosCadBuscarOrdenesCobro['datosFormMiembro']['APE1']['valorCampo'] = $cadApe1;//para que se muestre en la cabecera del formulario
	$arrDatosCadBuscarOrdenesCobro['datosFormMiembro']['APE2']['valorCampo'] = $cadApe2;//para que se muestre en la cabecera del formulario
	$arrDatosCadBuscarOrdenesCobro['estadoCuota'] = $_SESSION['vs_ESTADOCUOTA'];//para que se muestre en la cabecera del formulario
	$arrDatosCadBuscarOrdenesCobro['codAgrupacion'] = $_SESSION['vs_CODAGRUPACION'];//para que se muestre en la cabecera del formulario
	$arrDatosCadBuscarOrdenesCobro['datosRemesa'] = $_SESSION['vs_datosFormOrdenCobroRemesa'];//para que se muestre en la cabecera del formulario

	//echo 	"<br><br>4-1 cTesoreroOrdenesCobroUnaRemesaPaginar:pagActual: ";print_r($pagActual);
	//echo 	"<br><br>4-2 cTesoreroOrdenesCobroUnaRemesaPaginar:nomAchivoRemesaSEPAXML: ";print_r($nomAchivoRemesaSEPAXML);	
	//echo 	"<br><br>4-3 cTesoreroOrdenesCobroUnaRemesaPaginar:arrDatosCadBuscarOrdenesCobro: ";print_r($arrDatosCadBuscarOrdenesCobro);
	
 return 	$arrDatosCadBuscarOrdenesCobro;
}		
?>