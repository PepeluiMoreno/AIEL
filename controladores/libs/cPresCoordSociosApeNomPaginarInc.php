<?php
/*------------------------------------------------------------------------------
FICHERO: cPresCoordSociosApeNomPaginarInc.php

Este include incluye el tratamiento de los posibles variables de búsqueda en la 
lista de socios toda la Asociación, con el objeto de preparar los parámentros que 
son necesarios en cada caso para que funciones "cadBuscarCuotasSociosApeNom()" y
"cadBuscarCuotasSocios()" formen la cadena select correspondiente que se pasará
después a "mPaginarLib()". 

Incluye los if's para asignar los adecuados valores a $_SESSION['vs_APE1'],
$_SESSION['vs_APE2'] y 	$_SESSION['vs_CODAGRUPACION'] que se usan para 
$_SESSION['vs_pag_actual'] y que se utilizaran para guardar los valores para las
funciones "cadBuscarCuotasSociosApeNom", "cadBuscarCuotasSocios" 

INCLUIDA en: cPresidente.php:mostrarSociosPres() y desde 
cCoordinador.php:mostrarSociosCoord()
LLAMA: modelos/modeloTesorero.php:cadBuscarCuotasSociosApeNom() o
      cadBuscarCuotasSocios()
											
RECIBE: datos de $_POST, $_SESSION, codAreaCoordinacion,$anioCuotas desde 
cPresidente.php:mostrarSociosPres() o desde cCoordinador.php:mostrarSociosCoord()
DEVUELVE: en la llamada a las funciones cadBuscarCuotasSociosApeNom() o
cadBuscarCuotasSocios(). Estas funciones devuelven en un array la cadena
select ['cadSQL'], y el correspondiente ['arrBindValues'] para ejecutar 		
la consulta. También datos en $datosFormMiembro y $_SESSION

OBSERVACIONES: 	
2020-05-27: Sustituye a cPresidenteSociosApeNomPaginarInc y a 
cCoordinadorSociosApeNomPaginarInc.php, al unificarse en uno único.
Adaptada para PDO y PHP7

Creo que sería posible hacerlo un poco más simple o transformarlo en función para
ello habría que sustituir la variables tipo $_SESSION por parámetros y variables
Muy parecida a controladores/libs/cTesoreroCuotasSociosApeNomPaginarInc.php
------------------------------------------------------------------------------*/
//echo 	"<br><br>1-0-1 cPresCoordSociosApeNomPaginarInc:arrSelectCuotasSocios:_SESSION: ";print_r($_SESSION);	
//echo 	"<br><br>1-0-2 cPresCoordSociosApeNomPaginarInc:arrSelectCuotasSocios:_POST: ";print_r($_POST);	

require_once './modelos/modeloTesorero.php';

$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
$anioCuotas	= date('Y'); 
$estadoSocio = 'alta';
$estadoCuota = '%';	
$datosFormMiembro = array();

//echo 	"<br><br>1-0-3 cPresCoordSociosApeNomPaginarInc:pagActual: ";print_r($pagActual);
	
//----------- Inicio datos de búsqueda para APE1 o APE2 o CODAGRUPACION --------------------------

if (isset($_POST['datosFormMiembro']['APE1']) || isset($_POST['datosFormMiembro']['APE2']) || isset($_POST['datosFormSocio']['CODAGRUPACION']) )	
{ 
  //echo "<br><br> 1";
  //------------------------ Incio control 'APE1'y 'APE2' ----------------------

 	if (isset($_POST['datosFormMiembro']['APE1']) || isset($_POST['datosFormMiembro']['APE2']) )//Si exite campo APE1 o APE2 aunque esté empty				
		{//echo "<br><br> 1-1"; //entrará cuando se busca en el formula por cualquier valor de los campos APE1 o APE2 aunque esté empty		
	  
			/*---------- Inicio APE1 y AP2 -------------------------------------------
			En el caso de que sean a la vez: empty($cadApe1) y empty($cadApe2) 
			cambio valores: $cadApe1='---******---' y $cadApe2='---******---' estos caracteres no están
			permitidos para los apellidos por lo que la select devolverá 0 filas en el caso de que los
			dos estén empty	y envío "errorMensaje"="Al menos uno de los apellidos no puede estar vacío"
			Pero se permite buscar con APE1 o APE2 empty, pero los dos vacíos a la vez
			-------------------------------------------------------------------------*/
		 if ( empty($_POST['datosFormMiembro']['APE1'])  && empty($_POST['datosFormMiembro']['APE2']) )//Si campo APE1 o APE2 están empty				
			{ //si el APE1 y APE2 están vacios " ", pueden ser antiguas bajas más de 5 años con esos dos campos empty y no deben seleccionarse		
		   //echo "<br><br> 1-1-1";
					
		   $datosFormMiembro['APE1']['errorMensaje'] = 'Al menos uno de los apellidos no puede estar vacío'; 								
					$cadApe1 = '---******---';//esto caracteres no están permitidos para los apellidos por lo que la select devolverá 0 filas
			  $cadApe2 = '---******---';
   }  
	  else //!if ( empty($_POST['datosFormMiembro']['APE1'])  && empty($_POST['datosFormMiembro']['APE2']) )//Si campo APE1 + APE2 no están empty		
			{	//echo "<br><br> 1-1-2"; 
		
					$cadApe1 = $_POST['datosFormMiembro']['APE1'];
					$cadApe2 = $_POST['datosFormMiembro']['APE2'];	
					$datosFormMiembro['APE1']['valorCampo'] = $cadApe1;//para que se muestre en la cabecera del formulario
					$datosFormMiembro['APE2']['valorCampo'] = $cadApe2;				
			}
			
   if ((!isset($_SESSION['vs_APE1']) || $_SESSION['vs_APE1'] !== $cadApe1) || (!isset($_SESSION['vs_APE2']) || $_SESSION['vs_APE2'] !== $cadApe2))//acaso sobre este if						
			{ //echo "<br><br> 1-1-1"; 	
			
			  $_SESSION['vs_APE1'] = $cadApe1;//para que se pueda guardar para cabeceras
			  $_SESSION['vs_APE2'] = $cadApe2;
					unset($_SESSION['vs_pag_actual']);//al cambiar de nombre hay que iniciar siempre en la 1ª pág.						
			}

   $_pag_propagar_opcion_buscar = 'APE';
			
			$_SESSION['vs_CODAGRUPACION'] = '%';
		 
   $arrSelectCuotasSocios =	cadBuscarCuotasSociosApeNom($codAreaCoordinacion,$cadApe1,$cadApe2,$_SESSION['vs_CODAGRUPACION'],$anioCuotas);//en modeloTesorero.php 
		 //echo 	"<br><br>1-2-3 cPresCoordSociosApeNomPaginarInc:arrSelectCuotasSocios: ";print_r($arrSelectCuotasSocios);
			
		}//fin if ((isset($_POST['datosFormMiembro']['APE1'])  || (isset($_POST['datosFormMiembro']['APE2']) 
  //------------------------ Fin control 'APE1'y 'APE2'-------------------------
	
  //------------------------ Inicio control CODAGRUPACION ----------------------
  else // if (isset($_POST['datosFormSocio']['CODAGRUPACION']))         
		{	//echo "<br><br> 1-2";echo "<br><br> 1-2";//entrará cuando se busca en el formulario por cualquier valor del campo CODAGRUPACION
		  
				unset($_SESSION['vs_APE1']);
				unset($_SESSION['vs_APE2']);
				unset($_SESSION['vs_pag_actual']); //al cambiar de agrupación hay que iniciar siempre en la 1ª pág.				
	
			 if ($_SESSION['vs_CODAGRUPACION'] !== $_POST['datosFormSocio']['CODAGRUPACION'])//ha cambiado de agrupación
				{ //echo "<br><br> 1-2-1";
				  $_SESSION['vs_CODAGRUPACION'] = $_POST['datosFormSocio']['CODAGRUPACION'];								
				}	
	
				$_pag_propagar_opcion_buscar = 'AGRUPACION';
			   
    $arrSelectCuotasSocios = cadBuscarCuotasSocios($codAreaCoordinacion,$_SESSION['vs_CODAGRUPACION'],$anioCuotas,$estadoSocio,$estadoCuota);//en modeloTesorero.php
    //echo 	"<br><br>1-2-3 cPresCoordSociosApeNomPaginarInc:arrSelectCuotasSocios: ";print_r($arrSelectCuotasSocios);				
  }
		//------------------------ Fin control CODAGRUPACION  ------------------------	
		
}	//isset($_POST['datosFormMiembro']['APE1']) || ...isset($_POST['datosFormSocio']['CODAGRUPACION']
//----------- Fin datos de búsqueda para APE1 o APE2 o CODAGRUPACION ------------------------------------	

//--- Incio NO hay datos de búsqueda para APE1 o APE2 o CODAGRUPACION -----------------------------------
else//no isset($_POST['datosFormMiembro']['APE1'])|| ...||isset($_POST['datosFormSocio']['CODAGRUPACION']
{		
	//echo "<br><br> 2";//1ª vez entra por aquí, y 	también cuando en la lista pulso un núm. pág o siguiente, y cuando vuelve de columnas Acciones (Ver,Modificacion,Baja)
  
	//if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] == "index.php?controlador=cCoordinadore&accion=mostrarSociosCoord")	Antes para Coord	
 //if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] == "index.php?controlador=cPresidente&accion=mostrarSociosPres")	Antes para Pres
	if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] == "index.php?controlador=".$NomFuncionActualMostrarSocios)
	{ 
   //--Inicio viene de cPresidente&acción =mostrarSociosPres o cCoordinadorCoord==mostrarSociosCoord -------
			//Entra cuando en la lista pulso un núm. pág, siguiente, última,... 
   //y cuando vuelve de columnas Acciones (Ver,Modificacion,Baja) es decir desde "cPresidente&acción =mostrarSociosPres o cCoordinadorCoord==mostrarSociosCoord"  
   //echo "<br><br> 2-0";
	  
			if (isset($_GET['_opcion_buscar']))//entra cuando en la lista pulso un núm. pág, siguiente, última,... 
			{ 
		 	//echo 	"<br><br>2-0-0 cTesorero:libs:cTesoreroCuotasSociosApeNomPaginarInc:_GET: ";print_r($_GET); 
				
			 if ($_GET['_opcion_buscar'] == 'APE')//cuando en la lista de busqueda por APE1, APE2 pulso un núm. página, siguiente, última,...
				{
					if (isset($_SESSION['vs_APE1']) || isset($_SESSION['vs_APE12']))//cuando en la lista de busqueda por APE1, APE2 pulso un núm. página, siguiente, última,...
					{//echo "<br><br> 2-0-1";
					
					 $datosFormMiembro['APE1']['valorCampo'] = $_SESSION['vs_APE1'];//para que se muestre en la cab del formulario
				 	$datosFormMiembro['APE2']['valorCampo'] = $_SESSION['vs_APE2'];
						
				  unset($_SESSION['vs_pag_actual']);
						
					 $_pag_propagar_opcion_buscar = 'APE';
						$_SESSION['vs_CODAGRUPACION'] = '%';
	     
	     $arrSelectCuotasSocios =	cadBuscarCuotasSociosApeNom($codAreaCoordinacion,$_SESSION['vs_APE1'],$_SESSION['vs_APE2'],$_SESSION['vs_CODAGRUPACION'],$anioCuotas);//en modeloTesorero.php=						
						//echo 	"<br><br>2-0-1-1 cPresCoordSociosApeNomPaginarInc:arrSelectCuotasSocios: ";print_r($arrSelectCuotasSocios);
					}					
				}//if ($_GET['_opcion_buscar'] == 'APE')
					
				elseif ($_GET['_opcion_buscar'] == 'AGRUPACION')//cuando en la lista de búsqueda por CODAGRUPACION pulso un núm. página, siguiente, última,...
				{	//echo "<br><br> 2-0-2";
				  unset($_SESSION['vs_APE1']);
						unset($_SESSION['vs_APE2']);
      unset($_SESSION['vs_pag_actual']);
						
						$_pag_propagar_opcion_buscar = 'AGRUPACION';
      
      $arrSelectCuotasSocios = cadBuscarCuotasSocios($codAreaCoordinacion,$_SESSION['vs_CODAGRUPACION'],$anioCuotas,$estadoSocio,$estadoCuota);//en modeloTesorero.php
						//echo 	"<br><br>2-0-2-1 cPresCoordSociosApeNomPaginarInc:arrSelectCuotasSocios: ";print_r($arrSelectCuotasSocios);
				}//elseif ($_GET['_opcion_buscar'] == 'AGRUPACION')
				
				else  //no hay GET[_opcion_buscar]: //no hay GET[_opcion_buscar]== 'ANIO') y tampoco hay($_GET['_opcion_buscar'] == 'AGRUPACION' ***creo que sobra*********************
				{	//echo "<br><br> 2-0-3";				
				}						
				
			}//if (isset($_GET['_opcion_buscar']))
				
			else //(!isset($_GET['_opcion_buscar']))//sólo entrará cuando estando en una lista de búsqueda por APE o por CODAGRUPACION ,... hago clic en menú izdo. "-Cuotas socios/as" 
			{
				//echo "<br><br> 2-0-4";
				unset($_SESSION['vs_APE1']); 
				unset($_SESSION['vs_APE2']);			
			 unset($_SESSION['vs_pag_actual']);				
				
				$_pag_propagar_opcion_buscar = 'AGRUPACION';				
				$_SESSION['vs_CODAGRUPACION'] = '%';								
    
    $arrSelectCuotasSocios = cadBuscarCuotasSocios($codAreaCoordinacion,$_SESSION['vs_CODAGRUPACION'],$anioCuotas,$estadoSocio,$estadoCuota);//en modeloTesorero.php
				//echo 	"<br><br>2-0-4-1 cPresCoordSociosApeNomPaginarInc:arrSelectCuotasSocios: ";print_r($arrSelectCuotasSocios);
			}//no hay GET[_opcion_buscar]  
			
   //--- Fin viene de acción cPresidente&accion=mostrarSociosPres o Coord ---
			
	}//if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] ....	
	
	elseif ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] !== "index.php?controlador=".$NomFuncionMostrarDatosSocio &&
								 $_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] !== "index.php?controlador=".$NomFuncionActualizarSocio &&
								 $_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] !==	"index.php?controlador=".$NomFuncionEliminarSocio														
			     )	//procede de otros niveles inferiores 										
	{ 
	  //-- Incio viene de otros niveles inferiores, menú o desde Email a socios, ... ------
			
			//echo "<br><br> 2-1"; 	//1ª vez entra por aquí y mostrará la busqueda por vs_CODAGRUPACION, y valores por defecto para $codAreaCoordinacion,$anioCuotas,$estadoSocio,$estadoCuota	
		 unset($_SESSION['vs_APE1']);
		 unset($_SESSION['vs_APE2']);			
			unset($_SESSION['vs_pag_actual']);//se iniciará en la 1ª pág.	
			
			$_pag_propagar_opcion_buscar = 'AGRUPACION';
			$_SESSION['vs_CODAGRUPACION'] = '%';
			
   $arrSelectCuotasSocios = cadBuscarCuotasSocios($codAreaCoordinacion,$_SESSION['vs_CODAGRUPACION'],$anioCuotas,$estadoSocio,$estadoCuota);//en modeloTesorero.php
			//echo 	"<br><br>2-1-1 cPresCoordSociosApeNomPaginarInc:arrSelectCuotasSocios: ";print_r($arrSelectCuotasSocios);
			
   //-- Fin viene de otros niveles inferiores, ej. desde Email a socios, ... --------				
			
	}//elseif ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] !== "index.php?controlador=".$NomFuncionMostrarDatosSocio &&...
	
	else //($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] =="index.php?controlador=".$NomFuncionMostrarDatosSocio &&" && ...		volver desde otro nivel superior del mismo hilo
	{ 
	  //-- Inicio cuando vuelve de Acciones: Ver, Modificar, Baja: &accion=mostrarDatosSocioPres o actualizarSocioPres o eliminarSocioPres o Coord ...---			
	  //echo "<br><br> 2-2"; 
			
	  if (isset($_SESSION['vs_APE1']) || isset($_SESSION['vs_APE2']) )//cuando vuelve de Acciones: Ver, Modificar, Baja y procedía lista de búsqueda por APE1 o APE2
	  { //echo "<br><br> 2-2-1"; 
					$datosFormMiembro['APE1']['valorCampo'] = $_SESSION['vs_APE1'];//para que se muestre en la cab del formulario
				 $datosFormMiembro['APE2']['valorCampo'] = $_SESSION['vs_APE2'];
					
					$_pag_propagar_opcion_buscar = 'APE';	    				
					$_SESSION['vs_CODAGRUPACION'] = '%';
	    
     $arrSelectCuotasSocios =	cadBuscarCuotasSociosApeNom($codAreaCoordinacion,$_SESSION['vs_APE1'],$_SESSION['vs_APE2'],$_SESSION['vs_CODAGRUPACION'],$anioCuotas);//en modeloTesorero.php=	
					
			  //echo 	"<br><br>2-2-1-1 cPresCoordSociosApeNomPaginarInc:arrSelectCuotasSocios: ";print_r($arrSelectCuotasSocios);								
			}//if (isset($_SESSION['vs_APE1']) || isset($_SESSION['vs_APE2']) )
				
			else //!if (isset($_SESSION['vs_APE1']) || isset($_SESSION['vs_APE2']) )//cuando vuelve de Acciones: Ver, Modificar, Baja cuando procedía lista de búsqueda por busqueda por vs_CODAGRUPACION
			{ //echo "<br><br> 2-2-2";
			  
			  $_pag_propagar_opcion_buscar = 'AGRUPACION';
     
     $arrSelectCuotasSocios = cadBuscarCuotasSocios($codAreaCoordinacion,$_SESSION['vs_CODAGRUPACION'],$anioCuotas,$estadoSocio,$estadoCuota);//en modeloTesorero.php=	
					
			  //echo 	"<br><br>2-2-2-1 cPresCoordSociosApeNomPaginarInc:arrSelectCuotasSocios: ";print_r($arrSelectCuotasSocios);					
			}			
   //-- Fin cuando vuelve de Acciones: Ver, Modificar, Baja: &accion=mostrarDatosSocioPres o actualizarSocioPres o eliminarSocioPres o Coord ...---			
			
	}//else volver desde otro nivel superior del mismo hilo
	
 //-------- Fin NO hay datos de búsqueda para APE1 o APE2 o CODAGRUPACION ----------------------------------	
	
}//else no  (isset($_POST['datosFormMiembro']['APE1'])|| ... ||		(isset($_POST['datosFormSocio']['CODAGRUPACION'])

	//echo 	"<br><br>2-3-1 cPresCoordSociosApeNomPaginarInc:datosFormMiembro: ";print_r($datosFormMiembro);	
	//echo 	"<br><br>2-3-2 cPresCoordSociosApeNomPaginarInc:arrSelectCuotasSocios: ";print_r($arrSelectCuotasSocios);
	//echo 	"<br><br>2-3-3 cPresCoordSociosApeNomPaginarInc:pagActual: ";print_r($pagActual);

$_pagi_sql = $arrSelectCuotasSocios['cadSQL'];		

$arrBindValues = $arrSelectCuotasSocios['arrBindValues'];

/* ----- Fin cPresCoordSociosApeNomPaginarInc.php ----------------------------*/
?>