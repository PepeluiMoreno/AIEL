<?php

/*------------------------------------------------------------------------------
FICHERO: cTesoreroCuotasSociosApeNomPaginarInc.php

Este include incluye el tratamiento de los posibles variables de búsqueda en la 
lista de socios toda la Asociación, con el objeto de preparar los parámentros que 
son necesarios en cada caso para que funciones "cadBuscarCuotasSociosApeNom()" y
"cadBuscarCuotasSocios()" formen la cadena select correspondiente que se pasará
después a "mPaginarLib()", para formar la lista de trabajo para Tesorería 

Incluye los if's para asignar los adecuados valores a $_SESSION['vs_APE1'],
$_SESSION['vs_APE2'],$_SESSION['vs_ANIOCUOTASELEGIDO'],$_SESSION['vs_CODAGRUPACION'], 
$_SESSION['vs_ESTADOCUOTA'] que se usan para $_SESSION['vs_pag_actual'] y que se
utilizarán para guardar los valores para las funciones "cadBuscarCuotasSociosApeNom",
"cadBuscarCuotasSocios" 

INCLUIDA en:cTesorero:mostrarIngresosCuotas()	
LLAMA: modelos/modeloTesorero.php:cadBuscarCuotasSociosApeNom() o
      cadBuscarCuotasSocios()
											
RECIBE: datos de $_POST, $_SESSION, codAreaCoordinacion,$anioCuotas desde 
cTesorero:mostrarIngresosCuotas()	y formulario vMostrarIngresosCuotasInc()
DEVUELVE: en la llamada a las funciones cadBuscarCuotasSociosApeNom() o
cadBuscarCuotasSocios(). Estas funciones devuelven en un array la cadena
select ['cadSQL'], y el correspondiente ['arrBindValues'] para ejecutar 		
la consulta. También datos en $datosFormMiembro y variables $_SESSION 

OBSERVACIONES: 	
2020-08-27: Amplío para incluir también búsqueda por agrupaciones
Adaptada para PDO y PHP7

Creo que sería posible hacerlo un poco más simple o transformarlo en función para
ello habría que sustituir las variables tipo $_SESSION por parámetros y variables
Muy parecida a controladores/libs/cPresCoordSociosApeNomPaginarInc
------------------------------------------------------------------------------*/
//echo 	"<br><br>1-0-1 cTesorero:libs:cTesoreroCuotasSociosApeNomPaginarInc:_SESSION: ";print_r($_SESSION);	
//echo 	"<br><br>1-0-2 cTesorero:libs:cTesoreroCuotasSociosApeNomPaginarInc:_POST: ";print_r($_POST);	

require_once './modelos/modeloTesorero.php';

$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
$estadoSocio = '%'; 
$datosFormMiembro = array();

//echo 	"<br><br>1-0-3 cTesorero:libs:cTesoreroCuotasSociosApeNomPaginarInc:pagActual: ";print_r($pagActual);

//-------- Incio búsqueda para APE1 o APE2 o ANIOCUOTASELEGIDO, CODAGRUPACION, ESTADOCUOTA -----	

if ( isset($_POST['datosFormMiembro']['APE1']) || isset($_POST['datosFormMiembro']['APE2']) ||
				 isset($_POST['resCuotasSocios']['anioCuotasElegido']) || isset($_POST['datosFormSocio']['CODAGRUPACION']) || 
				 isset($_POST['resCuotasSocios']['ESTADOCUOTA']) 
			)	//Si exite algún campo de entrada aunque esté empty		
{ 
  //echo "<br><br> 1";
  //------------------------ Incio control 'APE1'y 'APE2' ---------------------
	
		if (isset($_POST['datosFormMiembro']['APE1']) || isset($_POST['datosFormMiembro']['APE2']) )//Si exite campo APE1 o APE2 aunque esté empty				
		{//echo "<br><br> 1-1"; //entrará cuando se busca en el formula por cualquier valor de los campos APE1 o APE2 aunque esté empty					
	  
			/*---------- Inicio APE1 y AP2 -------------------------------------------
			En el caso de que sean a la vez: empty($cadApe1) y empty($cadApe2) 
			cambio valores: $cadApe1='---******---' y $cadApe2='---******---' estos caracteres no están
			permitidos para los apellidos por lo que la select devolverá 0 filas en el caso de que los
			dos estén empty	y envío "errorMensaje"="Al menos uno de los apellidos no puede estar vacío"
			Pero se permite buscar con APE1 o APE2 empty, pero los dos vacíos a la vez
			-------------------------------------------------------------------------*/
	  if ( empty($_POST['datosFormMiembro']['APE1']) && empty($_POST['datosFormMiembro']['APE2']) )//Si campo APE1 o APE2 están empty				
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
			{ //echo "<br><br> 1-1-3"; 	
			 
 				$_SESSION['vs_APE1'] = $cadApe1;//para que se pueda guardar para cabeceras
			  $_SESSION['vs_APE2'] = $cadApe2;					
					unset($_SESSION['vs_pag_actual']); //al cambiar de nombre hay que iniciar siempre en la 1ª pág.					
			}

   $_pag_propagar_opcion_buscar = 'APE'; //buscará por apellidos
			
			$_SESSION['vs_ANIOCUOTASELEGIDO'] = '%';	
   $_SESSION['vs_ESTADOCUOTA'] = '%';				
   $_SESSION['vs_CODAGRUPACION'] = '%';

   $arrSelectCuotasSocios = cadBuscarCuotasSociosApeNom($codAreaCoordinacion,$cadApe1,$cadApe2,$_SESSION['vs_CODAGRUPACION'],$_SESSION['vs_ANIOCUOTASELEGIDO']);//en modeloTesorero.php	
		 //echo 	"<br><br>1-1-4 cTesorero:libs:cTesoreroCuotasSociosApeNomPaginarInc:arrSelectCuotasSocios: ";print_r($arrSelectCuotasSocios);
			
		}//fin if ((isset($_POST['datosFormMiembro']['APE1'])  || (isset($_POST['datosFormMiembro']['APE2']) 
  //------------------------ Fin control 'APE1'y 'APE2'------------------------

  //--- Inicio control ANIOCUOTASELEGIDO, CODAGRUPACION, ESTADOCUOTA ----------
  else //if($_POST['resCuotasSocios']['anioCuotasElegido']) || isset($_POST['datosFormSocio']['CODAGRUPACION']) || isset($_POST['resCuotasSocios']['ESTADOCUOTA']))		        
		{	//echo "<br><br> 1-2";//entrará cuando se busca en el formulario por cualquier valor de los campos ANIOCUOTASELEGIDO, CODAGRUPACION, ESTADOCUOTA (o por defecto)
		  
				unset($_SESSION['vs_APE1']);
				unset($_SESSION['vs_APE2']);
	   unset($_SESSION['vs_pag_actual']); //al cambiar de anioCuotasElegido hay que iniciar siempre en la 1ª pág.				
				
			 if ($_SESSION['vs_ANIOCUOTASELEGIDO'] !== $_POST['resCuotasSocios']['anioCuotasElegido'])//ha cambiado anioCuotasElegido
				{ //echo "<br><br> 1-2-1";
				  $_SESSION['vs_ANIOCUOTASELEGIDO'] = $_POST['resCuotasSocios']['anioCuotasElegido'];
				}	

			 if ($_SESSION['vs_CODAGRUPACION'] !== $_POST['datosFormSocio']['CODAGRUPACION'])//ha cambiado de agrupación
				{ //echo "<br><br> 1-2-2";
				  $_SESSION['vs_CODAGRUPACION'] = $_POST['datosFormSocio']['CODAGRUPACION'];								
				}	

			 if ($_SESSION['vs_ESTADOCUOTA'] !== $_POST['resCuotasSocios']['ESTADOCUOTA'])//ha cambiado anioCuotasElegido
				{ //echo "<br><br> 1-2-3";
				  $_SESSION['vs_ESTADOCUOTA'] = $_POST['resCuotasSocios']['ESTADOCUOTA'];
				}
					 
				$_pag_propagar_opcion_buscar = 'ANIO';//En relidad buscará por ANIOCUOTASELEGIDO, CODAGRUPACION, ESTADOCUOTA

    $arrSelectCuotasSocios = cadBuscarCuotasSocios($codAreaCoordinacion,$_SESSION['vs_CODAGRUPACION'],$_SESSION['vs_ANIOCUOTASELEGIDO'],$estadoSocio,$_SESSION['vs_ESTADOCUOTA']);//en modeloTesorero.php 
    //echo 	"<br><br>1-2-4 cTesorero:libs:cTesoreroCuotasSociosApeNomPaginarInc:arrSelectCuotasSocios: ";print_r($arrSelectCuotasSocios);				
  }
		//--- Fin control ANIOCUOTASELEGIDO, CODAGRUPACION, ESTADOCUOTA -------------			
		
}	//isset($_POST['datosFormMiembro']['APE1']) || ...isset($_POST['resCuotasSocios']['anioCuotasElegido'])... || ...
//-------- Fin búsqueda para APE1 o APE2 o ANIOCUOTASELEGIDO, CODAGRUPACION, ESTADOCUOTA -----------------------	

//-------- Incio NO hay datos de búsqueda para APE1 o APE2 o ANIOCUOTASELEGIDO, CODAGRUPACION, ESTADOCUOTA -----	
else//NO isset($_POST['datosFormMiembro']['APE1'])||...isset($_POST['resCuotasSocios']['anioCuotasElegido'])||...isset($_POST['datosFormSocio']['CODAGRUPACION'])||...isset($_POST['resCuotasSocios']['ESTADOCUOTA']) 
{
	//echo "<br><br> 2"; //1ª vez entra por aquí, y 	también cuando en la lista pulso un núm. pág o siguiente, y cuando vuelve de (Ver,Pagocuota,Actualiza cuota,Baja)

 if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cTesorero&accion=mostrarIngresosCuotas")	
	{ 
   //Entra cuando en la lista pulso un núm. pág, siguiente, última,... 
   //y cuando vuelve de columnas Acciones (Ver,Pagocuota,Actualiza cuota,Baja) es decir desde misma "mostrarIngresosCuotas"
   //echo "<br><br> 2-0";
			
			if (isset($_GET['_opcion_buscar']))//entra cuando en la lista pulso un núm. pág, siguiente, última,... 
			{
				//echo 	"<br><br>2-0-0 cTesorero:libs:cTesoreroCuotasSociosApeNomPaginarInc:_GET: ";print_r($_GET); 
		
			 if ($_GET['_opcion_buscar'] == 'APE')//cuando en la lista de busqueda por APE1, APE2 pulso un núm. página, siguiente, última,...
				{
					if (isset($_SESSION['vs_APE1']) || isset($_SESSION['vs_APE2']))//cuando en la lista de busqueda por APE1, APE2 pulso un núm. página, siguiente, última,...					
					{//echo "<br><br> 2-0-1";
						
					 $datosFormMiembro['APE1']['valorCampo'] = $_SESSION['vs_APE1'];//para que se muestre en la cab del formulario
				 	$datosFormMiembro['APE2']['valorCampo'] = $_SESSION['vs_APE2'];
						
				  unset($_SESSION['vs_pag_actual']);
						
					 $_pag_propagar_opcion_buscar = 'APE';
						
						$_SESSION['vs_ANIOCUOTASELEGIDO'] = '%';
      $_SESSION['vs_ESTADOCUOTA'] = '%';
      $_SESSION['vs_CODAGRUPACION'] = '%';						

	     $arrSelectCuotasSocios = cadBuscarCuotasSociosApeNom($codAreaCoordinacion,$_SESSION['vs_APE1'],$_SESSION['vs_APE2'],$_SESSION['vs_CODAGRUPACION'],$_SESSION['vs_ANIOCUOTASELEGIDO']);					
						//echo 	"<br><br>2-0-1-1 cTesorero:libs:cTesoreroCuotasSociosApeNomPaginarInc:arrSelectCuotasSocios: ";print_r($arrSelectCuotasSocios);
					}				
				}//if ($_GET['_opcion_buscar'] == 'APE')

				elseif ($_GET['_opcion_buscar'] == 'ANIO')//cuando en la lista de búsqueda por ANIOCUOTASELEGIDO,CODAGRUPACION,ESTADOCUOTA pulso un núm. página, siguiente, última,...
				{//echo "<br><br> 2-0-2";				
			  unset($_SESSION['vs_APE1']);
					unset($_SESSION['vs_APE2']);
	    unset($_SESSION['vs_pag_actual']);//se iniciará en la 1ª pág.	de la paginación
					
     $_pag_propagar_opcion_buscar = 'ANIO';

	    $arrSelectCuotasSocios = cadBuscarCuotasSocios($codAreaCoordinacion,$_SESSION['vs_CODAGRUPACION'],$_SESSION['vs_ANIOCUOTASELEGIDO'],$estadoSocio,$_SESSION['vs_ESTADOCUOTA']);
	    //echo 	"<br><br>2-0-2-1 cTesorero:libs:cTesoreroCuotasSociosApeNomPaginarInc:arrSelectCuotasSocios: ";print_r($arrSelectCuotasSocios);
				}//elseif ($_GET['_opcion_buscar'] == 'ANIO')
				
				else //no hay GET[_opcion_buscar]== 'ANIO') y tampoco hay($_GET['_opcion_buscar'] == 'ANIO' ***creo que sobra*********************
				{	//echo "<br><br> 2-0-3";				
				}	
				
			}//if (isset($_GET['_opcion_buscar'])) 
			
			else //(!isset($_GET['_opcion_buscar']))//sólo entrará cuando estando en una lista de búsqueda por APE o por ANIOCUOTASELEGIDO,... hago clic en menú izdo. "-Cuotas socios/as"
			{
				//echo "<br><br> 2-0-4";
				unset($_SESSION['vs_APE1']);
				unset($_SESSION['vs_APE2']);
	   unset($_SESSION['vs_pag_actual']);//se iniciará en la 1ª pág.	de la paginación
							
  	 $_pag_propagar_opcion_buscar = 'ANIO';
			
				$_SESSION['vs_ANIOCUOTASELEGIDO'] = date('Y');// se mostrará por defecto el año actual, $_SESSION['vs_ANIOCUOTASELEGIDO'] = '%'; saldrían todos				
    $_SESSION['vs_ESTADOCUOTA'] = '%';		
    $_SESSION['vs_CODAGRUPACION'] = '%';
				
    $arrSelectCuotasSocios = cadBuscarCuotasSocios($codAreaCoordinacion,$_SESSION['vs_CODAGRUPACION'],$_SESSION['vs_ANIOCUOTASELEGIDO'],$estadoSocio,$_SESSION['vs_ESTADOCUOTA']);			
    //echo 	"<br><br>2-0-4-1 cTesorero:libs:cTesoreroCuotasSociosApeNomPaginarInc:arrSelectCuotasSocios: ";print_r($arrSelectCuotasSocios);			
			}//no hay GET[_opcion_buscar]   
			
	}//if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cTesorero&accion=mostrarIngresosCuotas")	
		
	elseif ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] !=="index.php?controlador=cTesorero&accion=mostrarIngresoCuotaAnio" &&
								 $_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] !=="index.php?controlador=cTesorero&accion=actualizarIngresoCuota" &&
								 $_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] !=="index.php?controlador=cTesorero&accion=actualizarDatosCuotaSocioTes" &&
								 $_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] !=="index.php?controlador=cTesorero&accion=eliminarSocioTes"																				
			     )	//procede de otros niveles 					
	{ 
	  //-- Inicio viene de otros niveles inferiores, menú o desde Email a socios, ... ------
	
	  //echo "<br><br> 2-1"; 	//1ª vez entra por aquí y mostrará la busqueda por ANIOCUOTASELEGIDO, CODAGRUPACION, ESTADOCUOTA por defecto
		 unset($_SESSION['vs_APE1']); 
		 unset($_SESSION['vs_APE2']);			
			unset($_SESSION['vs_pag_actual']);
			
			$_pag_propagar_opcion_buscar = 'ANIO'; //buscará por ANIOCUOTASELEGIDO, CODAGRUPACION, ESTADOCUOTA
		
			$_SESSION['vs_ANIOCUOTASELEGIDO'] = date('Y');//se mostrará por defecto el año actual, $_SESSION['vs_ANIOCUOTASELEGIDO'] = '%'; saldrían todos
   $_SESSION['vs_ESTADOCUOTA'] = '%';
   $_SESSION['vs_CODAGRUPACION'] = '%';	
   
			$arrSelectCuotasSocios = cadBuscarCuotasSocios($codAreaCoordinacion,$_SESSION['vs_CODAGRUPACION'],$_SESSION['vs_ANIOCUOTASELEGIDO'],$estadoSocio,$_SESSION['vs_ESTADOCUOTA']);
   //echo 	"<br><br>2-1-1 cTesorero:libs:cTesoreroCuotasSociosApeNomPaginarInc:arrSelectCuotasSocios: ";print_r($arrSelectCuotasSocios);
			
			//-- Fin viene de otros niveles inferiores, ej. desde Email a socios, ... --------				
			
	}//elseif ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] !=="index.php?controlador=cTesorero&accion=mostrarIngresoCuotaAnio" && ...
	
	else //($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] =="index.php?controlador=cTesorero&accion=mostrarIngresoCuotaAnio" && ...								 
	{
	  //--- Entra cuando vuelve de Acciones: Ver,Pago cuota,Actualiza cuota,Baja y vuelve a la página de la que salió 			
			//echo "<br><br> 2-2";
				
			if (isset($_SESSION['vs_APE1']) || isset($_SESSION['vs_APE2']))//cuando vuelve de Acciones: Ver,Pago cuota,Actualiza cuota,Baja y procedía lista de búsqueda por APE1 o APE2
	  {//echo "<br><br> 2-2-1"; 
				$datosFormMiembro['APE1']['valorCampo'] = $_SESSION['vs_APE1'];//para que se muestren en la cab del formulario
			 $datosFormMiembro['APE2']['valorCampo'] = $_SESSION['vs_APE2'];
				
				$_pag_propagar_opcion_buscar = 'APE';	
				
				$_SESSION['vs_ANIOCUOTASELEGIDO'] = '%';
    $_SESSION['vs_ESTADOCUOTA'] = '%';							
    $_SESSION['vs_CODAGRUPACION'] = '%'; 
				
    $arrSelectCuotasSocios = cadBuscarCuotasSociosApeNom($codAreaCoordinacion,$_SESSION['vs_APE1'],$_SESSION['vs_APE2'],$_SESSION['vs_CODAGRUPACION'],$_SESSION['vs_ANIOCUOTASELEGIDO']);			
	
				//echo 	"<br><br>2-2-1-1 cTesorero:libs:cTesoreroCuotasSociosApeNomPaginarInc:arrSelectCuotasSocios: ";print_r($arrSelectCuotasSocios);
			}//if (isset($_SESSION['vs_APE1']) || isset($_SESSION['vs_APE2']))
			
			else // !if (isset($_SESSION['vs_APE1']) || isset($_SESSION['vs_APE2']))//cuando vuelve de Ver, Pago, cuando procedía lista de búsqueda por ANIOCUOTASELEGIDO, CODAGRUPACION, ESTADOCUOTA 
			{
				//echo "<br><br> 2-2-2";//cuando vuelve de Acciones: Ver,Pago cuota,Actualiza cuota,Baja cuando procedía lista de búsqueda por ANIOCUOTASELEGIDO, CODAGRUPACION, ESTADOCUOTA 
			 
		  $_pag_propagar_opcion_buscar = 'ANIO';//buscará por ANIOCUOTASELEGIDO, CODAGRUPACION, ESTADOCUOTA

		  $arrSelectCuotasSocios = cadBuscarCuotasSocios($codAreaCoordinacion,$_SESSION['vs_CODAGRUPACION'],$_SESSION['vs_ANIOCUOTASELEGIDO'],$estadoSocio,$_SESSION['vs_ESTADOCUOTA']); 	
		  //echo 	"<br><br>2-2-2-1 cTesorero:libs:cTesoreroCuotasSociosApeNomPaginarInc:arrSelectCuotasSocios: ";print_r($arrSelectCuotasSocios);	
			}		
	}//-- Fin cuando vuelve de Acciones: Ver,Pago cuota,Actualiza cuota,Baja y vuelve a la página de la que salió 
	
 //-------- Fin NO hay datos de búsqueda para APE1 o APE2 o ANIOCUOTASELEGIDO, CODAGRUPACION, ESTADOCUOTA -----	
	
} //no (isset($_POST['datosFormMiembro']['APE1'])|| ... ||		(isset($_POST['datosFormSocio']['CODAGRUPACION'])

	//echo 	"<br><br>2-3-1 cTesorero:libs:cTesoreroCuotasSociosApeNomPaginarInc:datosFormMiembro: ";print_r($datosFormMiembro);	
	//echo 	"<br><br>2-3-2 cTesorero:libs:cTesoreroCuotasSociosApeNomPaginarInc:arrSelectCuotasSocios: ";print_r($arrSelectCuotasSocios);
	//echo 	"<br><br>2-3-3 cTesorero:libs:cTesoreroCuotasSociosApeNomPaginarInc:pagActual: ";print_r($pagActual);

$_pagi_sql = $arrSelectCuotasSocios['cadSQL'];//contiene la cadena de la select asignada		

$arrBindValues = $arrSelectCuotasSocios['arrBindValues']; //contiene el array correspondiente la select asignada	
	
?>