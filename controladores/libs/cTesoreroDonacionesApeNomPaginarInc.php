<?php
/*------------------------------------------------------------------------------------
FICHERO:cTesoreroDonacionesApeNomPaginarInc.php

Este include incluye el tratamiento de los posibles variables de búsqueda en la 
lista de donaciones, con el objeto de preparar los parámentros que 
son necesarios en cada caso para que funciones "cadBuscarDonacionesApeNom()" y
"cadBuscarDonaciones()" formen la cadena select correspondiente que se pasará
después a "mPaginarLib()". 

Incluye los if's para asignar los adecuados valores a $_SESSION['vs_APE1'],
$_SESSION['vs_APE2'] y 	$_SESSION['vs_ANIODONACIONSELEGIDO'] que se usan para 
$_SESSION['vs_pag_actual'] y que se utilizaran para guardar los valores para las
funciones "cadBuscarDonacionesApeNom()" y "cadBuscarDonaciones()" 

INCLUIDA en: cTesorero.php:mostrarDonaciones()

LLAMA: modelos/modeloTesorero.php:cadBuscarDonacionesApeNom() o
       cadBuscarDonaciones()
											
RECIBE: datos de $_POST, $_SESSION, cTesorero.php:mostrarDonaciones()
DEVUELVE: en la llamada a las funciones cadBuscarDonacionesApeNom() y 
cadBuscarDonaciones() devuelven en un array la cadena select ['cadSQL'], 
y el correspondiente ['arrBindValues'] para ejecutar 		
la consulta. También datos en $datosFormMiembro y $_SESSION

OBSERVACIONES: 	
2020-09-15: Adaptada para PDO y PHP 7.3.21

Creo que sería posible hacerlo un poco más simple o transformarlo en función para
ello habría que sustituir la variables tipo $_SESSION por parámetros y variables
------------------------------------------------------------------------------------*/
require_once './modelos/modeloTesorero.php';

$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
$datosFormMiembro = array();

//echo "<br><br>pagActual:",$pagActual;

//----------- Inicio datos de búsqueda para APE1 o APE2 o ANIO en POST--------------------
if ( isset($_POST['datosFormMiembro']['APE1']) || isset($_POST['datosFormMiembro']['APE2']) || isset($_POST['resDonaciones']['anioDonacionesElegido']) )	
{ //echo "<br><br> 1";
  //------------------------ Incio control 'APE1'y 'APE2' ----------------------------

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
			{ //si el APE1 y APE2 están vacios " ", debiera indicar que faltan un valor para APE1 o APE2
		   //echo "<br><br> 1-1-1";
					
		   $datosFormMiembro['APE1']['errorMensaje'] = 'Al menos uno de los apellidos no puede estar vacío'; 								
					$cadApe1 = '---******---';//esto caracteres no están permitidos para los apellidos por lo que la select devolverá 0 filas
			  $cadApe2 = '---******---';
   } 
	  else //!if ( empty($_POST['datosFormMiembro']['APE1'])  && empty($_POST['datosFormMiembro']['APE2']) )//Si campo APE1 + APE2 no están empty		
			{	//echo "<br><br> 1-1-2"; 
	
					$cadApe1 = $_POST['datosFormMiembro']['APE1'];//Acaso haya que validar la entrada de datos APE1 y APE2	
					$cadApe2 = $_POST['datosFormMiembro']['APE2'];	
					$datosFormMiembro['APE1']['valorCampo'] = $cadApe1;//para que se muestre en la cab del formulario
					$datosFormMiembro['APE2']['valorCampo'] = $cadApe2;		
					$_SESSION['vs_ANIODONACIONSELEGIDO'] = '%';		
	 	}			
   if ((!isset($_SESSION['vs_APE1']) || $_SESSION['vs_APE1'] !== $cadApe1) || (!isset($_SESSION['vs_APE2']) || $_SESSION['vs_APE2'] !== $cadApe2))//acaso sobre este if							
			{ //echo "<br><br> 1-1-1"; 	
			  $_SESSION['vs_APE1'] = $cadApe1; //para que se pueda guardar para cabeceras
			  $_SESSION['vs_APE2'] = $cadApe2;
					
					unset($_SESSION['vs_pag_actual']); //al cambiar de nombre hay que iniciar siempre en la 1ª pág.					
			}
			else //$_SESSION['vs_APE1']==$cadApe1 || $_SESSION['vs_APE2']==$cadApe2)
	  { //echo "<br><br> 1-1-2"; 					 
			}	

   $_pag_propagar_opcion_buscar = 'APE';			
   
			$arrSelectDonaciones = cadBuscarDonacionesApeNom($cadApe1,$cadApe2);	 //en modeloTesorero.php
		 //echo 	"<br><br>1-1-3 cTesorero:libs:cTesoreroDonacionesApeNomPaginarInc:arrSelectDonaciones: ";print_r($arrSelectDonaciones);
			
		}//fin if ((isset($_POST['datosFormMiembro']['APE1'])  || (isset($_POST['datosFormMiembro']['APE2']) 
  //------------------------ Fin control 'APE1'y 'APE2'------------------------------- 
	
  //------------------------ Incio control anioDonacionesElegido -------------------------
  else // if ($_POST['resDonaciones']['anioDonacionesElegido') && !empty($_POST['resDonaciones']['anioDonacionesElegido'))         
		{	//echo "<br><br> 1-2";		  
		  
				unset($_SESSION['vs_APE1']);
				unset($_SESSION['vs_APE2']);
				unset($_SESSION['vs_pag_actual']); //al cambiar de anioDonacionesElegido hay que iniciar siempre en la 1ª pág.			
	
			 if ($_SESSION['vs_ANIODONACIONSELEGIDO'] !== $_POST['resDonaciones']['anioDonacionesElegido'])//ha cambiado anioDonacionesElegido
				{ //echo "<br><br> 1-2-1";
				  $_SESSION['vs_ANIODONACIONSELEGIDO'] = $_POST['resDonaciones']['anioDonacionesElegido'];			
						$_SESSION['vs_ANIODONACIONSELEGIDO'] = $_POST['resDonaciones']['anioDonacionesElegido'];			
				}	
				else //son iguales
		  { //echo "<br><br> 1-2-2";				  
				}	
				
				$_pag_propagar_opcion_buscar = 'ANIO';
    $codAgrup = "%";//el tesorero áctua sobre todas las agrupaciones, se deja por si hubiese tesoreros de una agrupación			
				$tipoDonante = '%';   
				
				$arrSelectDonaciones = cadBuscarDonaciones($codAgrup,$_SESSION['vs_ANIODONACIONSELEGIDO'],$tipoDonante);	//en modeloTesorero.php
    //echo 	"<br><br>1-2-3- cTesorero:libs:cTesoreroDonacionesApeNomPaginarInc:arrSelectDonaciones: ";print_r($arrSelectDonaciones);				
  }
		//------------------------ Fin control anioDonacionesElegido ---------------------------		
}	//isset($_POST['datosFormMiembro']['APE1']) || ....
//----------- Fin  datos de búsqueda para APE1 o APE2 o ANIO en POST----------------------

//--- Incio NO hay datos de búsqueda para APE1 o APE2 o ANIO en POST----------------------
else//no isset($_POST['datosFormMiembro']['APE1'])|| ...||isset($_POST['resDonaciones']['anioDonacionesElegido'])
{
  //echo "<br><br> 2";//1ª vez entra por aquí, y 	también cuando en la lista pulso un núm. pág o siguiente, y cuando vuelve de columnas Acciones (Ver,Modificacion,Eliminar)????
	
 if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] == "index.php?controlador=cTesorero&accion=mostrarDonaciones")	
	{ 
   //--Inicio viene de cTesorero=mostrarDonaciones-----------------------------------
			//Entra cuando en la lista pulso un núm. pág, siguiente, última,... 
   //y cuando vuelve de columnas Acciones(Ver,Modificacion,Eliminar) es decir "cTesorero:mostrarIngresoDonacion(),modificarIngresoDonacionTes(),anularDonacionErroneaTes"  
   //echo "<br><br> 2-0";
	  
			if (isset($_GET['_opcion_buscar']))//entra cuando en la lista pulso un núm. pág, siguiente, última,...
			{ 
			 if ($_GET['_opcion_buscar'] == 'APE')//cuando en la lista de busqueda por APE1, APE2 pulso un núm. página, siguiente, última,...
				{
					if (isset($_SESSION['vs_APE1']) || isset($_SESSION['vs_APE2']))//cuando en la lista de busqueda por APE1, APE2 pulso un núm. página, siguiente, última,...
					{//echo "<br><br> 2-0-1";
					 
						$datosFormMiembro['APE1']['valorCampo'] = $_SESSION['vs_APE1'];//para que se muestre en la cab del formulario
				 	$datosFormMiembro['APE2']['valorCampo'] = $_SESSION['vs_APE2'];
						
				  unset($_SESSION['vs_pag_actual']);
						
					 $_pag_propagar_opcion_buscar = 'APE';
						$_SESSION['vs_ANIODONACIONSELEGIDO'] = '%';
											
						$arrSelectDonaciones = cadBuscarDonacionesApeNom($_SESSION['vs_APE1'],$_SESSION['vs_APE2']);	//en modeloTesorero.php
						//echo 	"<br><br>2-0-1-1 cTesorero:libs:cTesoreroDonacionesApeNomPaginarInc:arrSelectDonaciones: ";print_r($arrSelectDonaciones);
					}				
				}//if ($_GET['_opcion_buscar'] == 'APE')
					
				elseif ($_GET['_opcion_buscar'] == 'ANIO')//cuando en la lista de búsqueda por ANIO pulso un núm. página, siguiente, última,...
				{	//echo "<br><br> 2-0-2";
				  unset($_SESSION['vs_APE1']);
						unset($_SESSION['vs_APE2']);
		    unset($_SESSION['vs_pag_actual']);//se iniciará en la 1ª pág.	de la paginación
						
      $_pag_propagar_opcion_buscar = 'ANIO';
		    $codAgrup = "%";
						$tipoDonante = '%';
						
		    $arrSelectDonaciones = cadBuscarDonaciones($codAgrup,$_SESSION['vs_ANIODONACIONSELEGIDO'],$tipoDonante);	//en modeloTesorero.php				
		    //echo 	"<br><br>2-0-2-1 cTesorero:libs:cTesoreroDonacionesApeNomPaginarInc:arrSelectDonaciones: ";print_r($arrSelectDonaciones);
				}
				else  //no hay GET[_opcion_buscar]: //no hay GET[_opcion_buscar]== 'ANIO') y tampoco hay($_GET['_opcion_buscar'] == 'APE' ***creo que sobra***************
				{	//echo "<br><br> 2-0-3";				
				}						
			}//if (isset($_GET['_opcion_buscar']))//entra cuando en la lista pulso un núm. pág, siguiente, última,...
			
			else //(!isset($_GET['_opcion_buscar']))//sólo entrará cuando estando en una lista de búsqueda por APE o por ANIO ,... hago clic "- Mostrar donaciones" 
			{//echo "<br><br> 2-0-4";
	
				unset($_SESSION['vs_APE1']); 
				unset($_SESSION['vs_APE2']);			
			 unset($_SESSION['vs_pag_actual']);			
							
				$_pag_propagar_opcion_buscar = 'ANIO';				
				$_SESSION['vs_ANIODONACIONSELEGIDO'] = '%';
				$codAgrup = "%";
				$tipoDonante = '%';
				
 			$arrSelectDonaciones = cadBuscarDonaciones($codAgrup,$_SESSION['vs_ANIODONACIONSELEGIDO'],$tipoDonante);	//en modeloTesorero.php
    //echo 	"<br><br>2-0-4-1 cTesorero:libs:cTesoreroDonacionesApeNomPaginarInc:arrSelectDonaciones: ";print_r($arrSelectDonaciones);			
			}   
			//--Fin viene de cTesorero=mostrarDonaciones---------------------------------------
			
	}//no hay GET[_opcion_buscar]

	elseif ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] !== "index.php?controlador=cTesorero&accion=mostrarIngresoDonacion" &&
								 $_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] !== "index.php?controlador=cTesorero&accion=modificarIngresoDonacionTes" &&
									$_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] !== "index.php?controlador=cTesorero&accion=anularDonacionErroneaTes"	
			      )							
	{ //-- Inicio viene de otros niveles inferiores de menú , ... ------
	  
			//echo "<br><br> 2-1"; 		//1ª vez entra por aquí y mostrará la busqueda por ANIO, y % como valores por defecto para $codAgrup,$_SESSION['vs_ANIODONACIONSELEGIDO'],$tipoDonante

		 unset($_SESSION['vs_APE1']);
		 unset($_SESSION['vs_APE2']);			
			unset($_SESSION['vs_pag_actual']);
			
			$_pag_propagar_opcion_buscar = 'ANIO';
			$_SESSION['vs_ANIODONACIONSELEGIDO'] = '%';			
			$codAgrup = "%";
			$tipoDonante = '%';
			
			$arrSelectDonaciones = cadBuscarDonaciones($codAgrup,$_SESSION['vs_ANIODONACIONSELEGIDO'],$tipoDonante);	//en modeloTesorero.php
   //echo 	"<br><br>2-1-1 cTesorero:libs:cTesoreroDonacionesApeNomPaginarInc:arrSelectDonaciones: ";print_r($arrSelectDonaciones);
			
			//-- Fin viene de otros niveles inferiores, menú, ... --------		
			
	}//elseif ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] !== "index.php?controlador=cTesorero&accion=mostrarIngresoDonacion" && ... 
	
	else //elseif($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']!= "index.php?controlador=cTesorero&accion=mostrarIngresoDonacion" && ...volver desde otro nivel superior del mismo hilo 
	{ 
	  //-- Inicio cuando vuelve de Acciones: Ver, Modificar, Eliminar ---			
	  //echo "<br><br> 2-2"; 
			
	  if (isset($_SESSION['vs_APE1']) || isset($_SESSION['vs_APE2']))//cuando vuelve de Acciones: Ver, Modificar, Eliminar y procedía lista de búsqueda por APE1 o APE2
	  { //echo "<br><br> 2-2-1"; 
			
					$datosFormMiembro['APE1']['valorCampo'] = $_SESSION['vs_APE1'];//para que se muestre en la cab del formulario
				 $datosFormMiembro['APE2']['valorCampo'] = $_SESSION['vs_APE2'];
					
					$_pag_propagar_opcion_buscar = 'APE';		    				
					$_SESSION['vs_ANIODONACIONSELEGIDO'] = '%';

					$arrSelectDonaciones = cadBuscarDonacionesApeNom($_SESSION['vs_APE1'],$_SESSION['vs_APE2']);	//en modeloTesorero.php
					//echo 	"<br><br>2-2-1-1 cTesorero:libs:cTesoreroDonacionesApeNomPaginarInc:arrSelectDonaciones: ";print_r($arrSelectDonaciones);
			}// if (isset($_SESSION['vs_APE1']) || isset($_SESSION['vs_APE2']))
				
			else //!if (isset($_SESSION['vs_APE1']) || isset($_SESSION['vs_APE2']) )//cuando vuelve de Acciones: Ver, Modificar, Eliminar y procedía lista de búsqueda por ANIO
			{ //echo "<br><br> 2-2-2";
			 
			  $_pag_propagar_opcion_buscar = 'ANIO';
					$codAgrup = "%";
					$tipoDonante = '%';	
					
					$arrSelectDonaciones = cadBuscarDonaciones($codAgrup,$_SESSION['vs_ANIODONACIONSELEGIDO'],$tipoDonante);	//en modeloTesorero.php
		   //echo 	"<br><br>2-2-2-1 cTesorero:libs:cTesoreroDonacionesApeNomPaginarInc:arrSelectDonaciones: ";print_r($arrSelectDonaciones);	
			}		
	}
	//-------- Fin NO hay datos de búsqueda para APE1 o APE2 o ANIO en POST---------------------
	
} //no (isset($_POST['datosFormMiembro']['APE1'])|| ... ||		(isset($_POST['resDonaciones']['anioDonacionesElegido'])

//echo 	"<br><br>3 cTesorero:libs:cTesoreroDonacionesApeNomPaginarInc:arrSelectDonaciones: ";print_r($arrSelectDonaciones);	

$_pagi_sql = $arrSelectDonaciones['cadSQL'];		

$arrBindValues = $arrSelectDonaciones['arrBindValues'];

?>