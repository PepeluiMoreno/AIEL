<?php
/*------------------------------------------------------------------------------
FICHERO: cPresidenteSociosFellecApeNomPaginarInc.php

Incluye los if's para asignar los adecuados valores a $_SESSION['vs_APE1'], 
$_SESSION['vs_APE2'] y 	$_SESSION['vs_CODAGRUPACION'] que se usan para 
$_SESSION['vs_pag_actual'] y que se utilizaran para guardar los valores para la 
funciones "cadBuscarSociosFallecidosApeNom" "cadBuscarSociosFallecidos"
que se pasarán despues a  "mPaginarLib"
												
LLAMADA: cPresidente.php:mostrarSociosFallecidosPres()		
LLAMA: /modelos/modeloPresCoord.php:cadBuscarSociosFallecidosApeNom(),
cadBuscarSociosFallecidos()

OBSERVACIONES: 	
2020-04-17: Adaptada para PDO
2017-03-29: se añade.
Es parecida a "cPresCoordSociosApeNomPaginarInc.php" 
Creo que sería posible hacerlo un poco más simple o transformarlo en función para
ello habría que sustituir la variables tipo $_SESSION, por parámetros y variables			

Nota: los nombres de $datosFormMiembro y $datosFormSocio, no son muy adecuados 
porque en realidad se refiere  a campos para buscar en la tabla SOCIOSFALLECIDOS 								
------------------------------------------------------------------------------*/
require_once './modelos/modeloPresCoord.php';

$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
$datosFormMiembro = array();

if ((isset($_POST['datosFormMiembro']['APE1']) && !empty($_POST['datosFormMiembro']['APE1'])) || 
			 (isset($_POST['datosFormMiembro']['APE2']) && !empty($_POST['datosFormMiembro']['APE2'])) ||
				(isset($_POST['datosFormSocio']['CODAGRUPACION']) && !empty($_POST['datosFormSocio']['CODAGRUPACION']))				
			)				
{ //echo "<br><br> 1";
  //------------------------ Incio control 'APE1'y 'APE2' ----------------------
		if ((isset($_POST['datosFormMiembro']['APE1']) && !empty($_POST['datosFormMiembro']['APE1'])) || 
			   (isset($_POST['datosFormMiembro']['APE2']) && !empty($_POST['datosFormMiembro']['APE2']))
					)			 
		{//echo "<br><br> 1-1"; 	
	
		 $cadApe1 = $_POST['datosFormMiembro']['APE1'];//Acaso haya que validar la entrada de datos APE1 y APE2	
			$cadApe2 = $_POST['datosFormMiembro']['APE2'];	
	  $datosFormMiembro['APE1']['valorCampo'] = $cadApe1;//para que se muestre en la cab del formulario
			$datosFormMiembro['APE2']['valorCampo'] = $cadApe2;		
			$_SESSION['vs_CODAGRUPACION'] = '%';			
			
		 //if ($_SESSION['vs_APE1'] !== $cadApe1 || $_SESSION['vs_APE2'] !== $cadApe2)
		 if ((!isset($_SESSION['vs_APE1']) || $_SESSION['vs_APE1'] !== $cadApe1) || 
			    (!isset($_SESSION['vs_APE2']) || $_SESSION['vs_APE2'] !== $cadApe2)
						)							
			{ //echo "<br><br> 1-1-1"; 	
			  $_SESSION['vs_APE1'] = $cadApe1;//para que se pueda guardar para cabeceras
			  $_SESSION['vs_APE2'] = $cadApe2;
					unset($_SESSION['vs_pag_actual']); //al cambiar de nombre hay que iniciar siempre en la 1ª pág.						
			}
			else //$_SESSION['vs_APE1']==$cadApe1 || $_SESSION['vs_APE2']==$cadApe2)
	  { //echo "<br><br> 1-1-2"; 					 
			}
   $_pag_propagar_opcion_buscar = 'APE';

   $arrSelectSociosFallecidos	= cadBuscarSociosFallecidosApeNom($cadApe1,$cadApe2,$_SESSION['vs_CODAGRUPACION'],$anioBaja);
		 //echo 	"<br><br>1-2-3-cPresidente:cPresidenteSociosFellecApeNomPaginarInc:arrSelectSociosFallecidos: ";print_r($arrSelectSociosFallecidos);
			
		}//fin if ((isset($_POST['datosFormMiembro']['APE1'])  || (isset($_POST['datosFormMiembro']['APE2']) 
  //------------------------ Fin control 'APE1'y 'APE2'-------------------------
	
  //------------------------ Inicio control CODAGRUPACION ----------------------		
  else // if (isset(isset($_POST['datosFormSocio']['CODAGRUPACION']) && !empty($_POST['datosFormSocio']['CODAGRUPACION']))         
		{	//echo "<br><br> 1-2";		  
		  unset($_SESSION['vs_APE1']);
				unset($_SESSION['vs_APE2']);
	
			 if ($_SESSION['vs_CODAGRUPACION'] !== $_POST['datosFormSocio']['CODAGRUPACION'])//ha cambiado de agrupación
				{ //echo "<br><br> 1-2-1";
				  $_SESSION['vs_CODAGRUPACION'] = $_POST['datosFormSocio']['CODAGRUPACION'];
						unset($_SESSION['vs_pag_actual']); //al cambiar de agrupación hay que iniciar siempre en la 1ª pág.						
				}	
				else //son iguales
		  { //echo "<br><br> 1-2-2";				  
						unset($_SESSION['vs_pag_actual']);//al buscar por agrupación nueva se ponga al principio  	
				}
				$_pag_propagar_opcion_buscar = 'AGRUPACION';

	   $arrSelectSociosFallecidos	= cadBuscarSociosFallecidos($_SESSION['vs_CODAGRUPACION'],$anioBaja); 	
				
    //echo 	"<br><br>1-2-3-cPresidente:cPresidenteSociosFellecApeNomPaginarInc.php:arrSelectSociosFallecidos: ";print_r($arrSelectSociosFallecidos);				
  }//viene desde otra función distinta de "cPresidente: mostrarSociosFallecidosPres"
		//------------------------ Fin control CODAGRUPACION  ------------------------	
		
}	//isset($_POST['datosFormMiembro']['APE1']) || ....
else//no isset($_POST['datosFormMiembro']['APE1'])|| ...||isset($_POST['datosFormSocio']['CODAGRUPACION']
{//echo "<br><br> 2";

 if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cPresidente&accion=mostrarSociosFallecidosPres")	
	{ //echo "<br><br> 2-0";
	  
			if (isset($_GET['_opcion_buscar']))
			{ 
			 if ($_GET['_opcion_buscar'] == 'APE')
				{if (isset($_SESSION['vs_APE1']) ||isset($_SESSION['vs_APE12']))
					{//echo "<br><br> 2-0-1";
					 $datosFormMiembro['APE1']['valorCampo'] = $_SESSION['vs_APE1'];//para que se muestre en la cab del formulario
				 	$datosFormMiembro['APE2']['valorCampo'] = $_SESSION['vs_APE2'];
						
				  unset($_SESSION['vs_pag_actual']);
						
					 $_pag_propagar_opcion_buscar = 'APE';
						$_SESSION['vs_CODAGRUPACION'] = '%';

      $arrSelectSociosFallecidos	=	cadBuscarSociosFallecidosApeNom($_SESSION['vs_APE1'],$_SESSION['vs_APE2'],$_SESSION['vs_CODAGRUPACION'],$anioBaja);				
						//echo 	"<br><br>2-0-1-1cPresidente:cPresidenteSociosFellecApeNomPaginarInc.php:arrSelectSociosFallecidos: ";print_r($arrSelectSociosFallecidos);
					}					
				}
				elseif ($_GET['_opcion_buscar'] == 'AGRUPACION')
				{	//echo "<br><br> 2-0-2";
				  unset($_SESSION['vs_APE1']);
						unset($_SESSION['vs_APE2']);
      unset($_SESSION['vs_pag_actual']);
						
						$_pag_propagar_opcion_buscar = 'AGRUPACION';

      $arrSelectSociosFallecidos	=	cadBuscarSociosFallecidos($_SESSION['vs_CODAGRUPACION'],$anioBaja); 							
						//echo 	"<br><br>2-0-2-1cPresidente:cPresidenteSociosFellecApeNomPaginarInc:arrSelectSociosFallecidos: ";print_r($arrSelectSociosFallecidos);
				}
				else  //creo que este else sobra hay GET[_opcion_buscar]: perque no hay mas opciones que las dos anteriores
				{	//echo "<br><br> 2-0-3";				
				}						
			}
			else //(!isset($_GET['_opcion_buscar'])) 
			{ //echo "<br><br> 2-0-4";

				unset($_SESSION['vs_APE1']); 
				unset($_SESSION['vs_APE2']);			
			 unset($_SESSION['vs_pag_actual']);				
				
				$_pag_propagar_opcion_buscar = 'AGRUPACION';				
				$_SESSION['vs_CODAGRUPACION'] = '%';

    $arrSelectSociosFallecidos	= cadBuscarSociosFallecidos($_SESSION['vs_CODAGRUPACION'],$anioBaja); 			
				//echo 	"<br><br>2-0-4-1cPresidente:cPresidenteSociosFellecApeNomPaginarInc:arrSelectSociosFallecidos: ";print_r($arrSelectSociosFallecidos);
			}   
	}//no hay GET[_opcion_buscar]
	elseif ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link'] !== "index.php?controlador=cPresidente&accion=mostrarDatosSocioPres mostrarDatosSocioFallecidoPres" 
			      )	//procede de otros niveles 										
	{ //echo "<br><br> 2-1"; 		

		 unset($_SESSION['vs_APE1']);
		 unset($_SESSION['vs_APE2']);			
			unset($_SESSION['vs_pag_actual']);//-->se iniciará en la 1ª pág.	
			
			$_pag_propagar_opcion_buscar = 'AGRUPACION';
			$_SESSION['vs_CODAGRUPACION'] = '%';			

   $arrSelectSociosFallecidos	=	cadBuscarSociosFallecidos($_SESSION['vs_CODAGRUPACION'],$anioBaja); 		
			//echo 	"<br><br>2-1-1cPresidente:cPresidenteSociosFellecApeNomPaginarInc:arrSelectSociosFallecidos:";print_r($arrSelectSociosFallecidos);			
	}
	else //volver desde otro nivel superior del mismo hilo
	{ //echo "<br><br> 2-2"; 
	  if (isset($_SESSION['vs_APE1']) || isset($_SESSION['vs_APE2']) )
	  { //echo "<br><br> 2-2-1"; 
					$datosFormMiembro['APE1']['valorCampo'] = $_SESSION['vs_APE1'];//para que se muestre en la cab del formulario
				 $datosFormMiembro['APE2']['valorCampo'] = $_SESSION['vs_APE2'];
					
					$_pag_propagar_opcion_buscar = 'APE';	    				
					$_SESSION['vs_CODAGRUPACION'] = '%';
				
	    $arrSelectSociosFallecidos	=	cadBuscarSociosFallecidosApeNom($_SESSION['vs_APE1'],$_SESSION['vs_APE2'],$_SESSION['vs_CODAGRUPACION'],$anioBaja);		
			  //echo 	"<br><br>2-2-1-1cPresidente:cPresidenteSociosFellecApeNomPaginarInc:arrSelectSociosFallecidos:";print_r($arrSelectSociosFallecidos);								
			}
			else //entra cuando vuelve de actualizar, ....
			{ //echo "<br><br> 2-2-2";
			  
			  $_pag_propagar_opcion_buscar = 'AGRUPACION';	

     $arrSelectSociosFallecidos	=	cadBuscarSociosFallecidos($_SESSION['vs_CODAGRUPACION'],$anioBaja);			
					
			  //echo 	"<br><br>2-2-2-1cPresidente:cPresidenteSociosFellecApeNomPaginarInc:arrSelectSociosFallecidos:";print_r($arrSelectSociosFallecidos);					
			}		
	}
} //else no (isset($_POST['datosFormMiembro']['APE1'])|| ... ||		(isset($_POST['datosFormSocio']['CODAGRUPACION'])
	
	//echo 	"<br><br>2-3-1 cPresidente:cPresidenteSociosFellecApeNomPaginarInc: ";print_r($datosFormMiembro);	
	//echo 	"<br><br>2-3-2 cPresidente:cPresidenteSociosFellecApeNomPaginarInc:arrSelectSociosFallecidos: ";print_r($arrSelectSociosFallecidos);
 //echo 	"<br><br>2-3-2 cPresidente:cPresidenteSociosFellecApeNomPaginarInc:arrSelectSociosFallecidos: ";print_r($arrSelectSociosFallecidos);
 //echo 	"<br><br>2-3-3 cPresidente:cPresidenteSociosFellecApeNomPaginarInc:pagActual: ";print_r($pagActual);

$_pagi_sql = $arrSelectSociosFallecidos['cadSQL'];	
$arrBindValues = $arrSelectSociosFallecidos['arrBindValues'];

/* ----- Fin cPresidenteSociosFellecApeNomPaginarInc.php ---------------------*/ 
?>