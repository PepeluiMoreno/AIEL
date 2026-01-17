<?php
/*----------------------------------------------------------------------------------------
FICHERO: cAsminSociosApeNomPaginarInc.php
DESCRIPCIÓN:Incluye los if's para asignar los adecuados valores a 
            $_SESSION['vs_APE1'], $_SESSION['vs_APE2'] y 	$_SESSION['vs_CODAGRUPACION']  
												que se usan para $_SESSION['vs_pag_actual'] y que se utilizaran para guardar 
												los valores para la funciones "formarCadBuscarDatosSociosApes" "formarCadBuscarDatosSocios"
												que se pasarán despues a  "mPaginarLib"
OBSEVACIONES: Acaso se pudiera simplificar algo 
identica a controladores/libs/cPresidenteSociosApeNomPaginarInc.php												
-----------------------------------------------------------------------------------------*/
require_once './modelos/modeloTesorero.php';

//$estadoSocio='alta';
$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];

if ((isset($_POST['datosFormMiembro']['APE1']) && !empty($_POST['datosFormMiembro']['APE1'])) || 
			 (isset($_POST['datosFormMiembro']['APE2']) && !empty($_POST['datosFormMiembro']['APE2'])) ||
				(isset($_POST['datosFormSocio']['CODAGRUPACION']) && !empty($_POST['datosFormSocio']['CODAGRUPACION']))				
			)				
{ //echo "<br><br> 1";
  //------------------------ Incio control 'APE1'y 'APE2' ------------------------------------
		if ((isset($_POST['datosFormMiembro']['APE1']) && !empty($_POST['datosFormMiembro']['APE1'])) || 
			   (isset($_POST['datosFormMiembro']['APE2']) && !empty($_POST['datosFormMiembro']['APE2']))
					)			 
		{//echo "<br><br> 1-1"; 	
	
		 $cadApe1 = $_POST['datosFormMiembro']['APE1'];//Acaso haya que validar la entrada de datos APE1 y APE2	
			$cadApe2 = $_POST['datosFormMiembro']['APE2'];	
	  $datosFormMiembro['APE1']['valorCampo'] = $cadApe1;//para que se muestre en la cab del formulario
			$datosFormMiembro['APE2']['valorCampo'] = $cadApe2;		
			$_SESSION['vs_CODAGRUPACION'] = '%';			
			
		 if ($_SESSION['vs_APE1']!==$cadApe1 || $_SESSION['vs_APE2']!==$cadApe2)
			{ //echo "<br><br> 1-1-1"; 	
			  $_SESSION['vs_APE1'] = $cadApe1;//para que se pueda guardar para cabeceras
			  $_SESSION['vs_APE2'] = $cadApe2;
					unset($_SESSION['vs_pag_actual']); //al cambiar de nombre hay que iniciar siempre en la 1ª pág.						
			}
			else //$_SESSION['vs_APE1']==$cadApe1 || $_SESSION['vs_APE2']==$cadApe2)
	  { //echo "<br><br> 1-1-2"; 					 
			}	

   $_pag_propagar_opcion_buscar = 'APE';

		 //$_pagi_sql =	formarCadBuscarDatosSociosApes($cadApe1,$cadApe2);   			
		 $_pagi_sql =	cadBuscarCuotasSociosApeNom($codAreaCoordinacion,$cadApe1,$cadApe2,$_SESSION['vs_CODAGRUPACION'],$anioCuotas);			

		 //echo 	"<br><br>1-2-3-cPresidente:cPresidenteSociosApeNomPaginarInc:_pagi_sql:";print_r($_pagi_sql);
			
		}//fin if ((isset($_POST['datosFormMiembro']['APE1'])  || (isset($_POST['datosFormMiembro']['APE2']) 
  //------------------------ Fin control 'APE1'y 'APE2'-------------------------------- 
  //------------------------ Inicio control CODAGRUPACION ----------------		
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
					
    //$_pagi_sql = formarCadBuscarDatosSocios($_SESSION['vs_CODAGRUPACION']);
    $_pagi_sql = cadBuscarCuotasSocios($codAreaCoordinacion,$_SESSION['vs_CODAGRUPACION'],$anioCuotas,$estadoSocio,$estadoCuota); 	
				
    //echo 	"<br><br>1-2-3-cPresidente:cPresidenteSociosApeNomPaginarInc:_pagi_sql:";print_r($_pagi_sql);				
  }//viene desde otra función distinta de "cPresidente: mostrarSociosPres"
		//------------------------ Fin control CODAGRUPACION  ------------------				
}	//isset($_POST['datosFormMiembro']['APE1']) || ....
else//no isset($_POST['datosFormMiembro']['APE1'])|| ...||isset($_POST['datosFormSocio']['CODAGRUPACION']
{//echo "<br><br> 2";

 if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cPresidente&accion=mostrarSociosPres")	
	{ //echo "<br><br> 2-0";
	  
			if (isset($_GET['_opcion_buscar']))
			{ 
			 if ($_GET['_opcion_buscar']=='APE')
				{if (isset($_SESSION['vs_APE1']) ||isset($_SESSION['vs_APE12']))
					{//echo "<br><br> 2-0-1";
					 $datosFormMiembro['APE1']['valorCampo'] = $_SESSION['vs_APE1'];//para que se muestre en la cab del formulario
				 	$datosFormMiembro['APE2']['valorCampo'] = $_SESSION['vs_APE2'];
						
				  unset($_SESSION['vs_pag_actual']);
						
					 $_pag_propagar_opcion_buscar = 'APE';
						$_SESSION['vs_CODAGRUPACION'] = '%';
								
					 //$_pagi_sql =	formarCadBuscarDatosSociosApes($_SESSION['vs_APE1'],$_SESSION['vs_APE2']);
	     $_pagi_sql =	cadBuscarCuotasSociosApeNom($codAreaCoordinacion,$_SESSION['vs_APE1'],$_SESSION['vs_APE2'],
		                                             $_SESSION['vs_CODAGRUPACION'],$anioCuotas);			
						
						//echo 	"<br><br>2-0-1-1cPresidente:cPresidenteSociosApeNomPaginarInc:_pagi_sql:";print_r($_pagi_sql);
					}					
				}
				elseif ($_GET['_opcion_buscar']=='AGRUPACION')
				{	//echo "<br><br> 2-0-2";
				  unset($_SESSION['vs_APE1']);
						unset($_SESSION['vs_APE2']);
      unset($_SESSION['vs_pag_actual']);
						
						$_pag_propagar_opcion_buscar = 'AGRUPACION';
		    
			   //$_pagi_sql = formarCadBuscarDatosSocios($_SESSION['vs_CODAGRUPACION']);
      $_pagi_sql = cadBuscarCuotasSocios($codAreaCoordinacion,$_SESSION['vs_CODAGRUPACION'],$anioCuotas,$estadoSocio,$estadoCuota); 	
						
						//echo 	"<br><br>2-0-2-1cPresidente:cPresidenteSociosApeNomPaginarInc:_pagi_sql:";print_r($_pagi_sql);
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
				
				//$_pagi_sql = formarCadBuscarDatosSocios($_SESSION['vs_CODAGRUPACION']);
    $_pagi_sql = cadBuscarCuotasSocios($codAreaCoordinacion,$_SESSION['vs_CODAGRUPACION'],$anioCuotas,$estadoSocio,$estadoCuota); 	
				
				//echo 	"<br><br>2-0-4-1cPresidente:cPresidenteSociosApeNomPaginarInc:_pagi_sql:";print_r($_pagi_sql);
			}   
	}//no hay GET[_opcion_buscar]
	elseif ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']!==
									"index.php?controlador=cPresidente&accion=mostrarDatosSocioPres" &&
								  $_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']!==
										"index.php?controlador=cPresidente&accion=actualizarSocioPres" &&
								  $_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']!==
										"index.php?controlador=cPresidente&accion=eliminarSocioPres"
			      )	//procede de otros niveles inferiores o sin que no es de este hilo										
	{ //echo "<br><br> 2-1"; 		

		 unset($_SESSION['vs_APE1']);
		 unset($_SESSION['vs_APE2']);			
			unset($_SESSION['vs_pag_actual']);//se iniciará en la 1ª pág.	
			
			$_pag_propagar_opcion_buscar = 'AGRUPACION';
			$_SESSION['vs_CODAGRUPACION'] = '%';
			
			//$_pagi_sql = formarCadBuscarDatosSocios($_SESSION['vs_CODAGRUPACION']);
   $_pagi_sql = cadBuscarCuotasSocios($codAreaCoordinacion,$_SESSION['vs_CODAGRUPACION'],$anioCuotas,$estadoSocio,$estadoCuota); 	
			
			//echo 	"<br><br>2-1-1cPresidente:cPresidenteSociosApeNomPaginarInc:_pagi_sql:";print_r($_pagi_sql);			
	}
	else //volver desde otro nivel superior del mismo hilo
	{ //echo "<br><br> 2-2"; 
	  if (isset($_SESSION['vs_APE1']) || isset($_SESSION['vs_APE2']) )
	  { //echo "<br><br> 2-2-1"; 
					$datosFormMiembro['APE1']['valorCampo'] = $_SESSION['vs_APE1'];//para que se muestre en la cab del formulario
				 $datosFormMiembro['APE2']['valorCampo'] = $_SESSION['vs_APE2'];
					
					$_pag_propagar_opcion_buscar = 'APE';	    				
					$_SESSION['vs_CODAGRUPACION'] = '%';

					//$_pagi_sql =	formarCadBuscarDatosSociosApes($_SESSION['vs_APE1'],$_SESSION['vs_APE2']);
	    $_pagi_sql =	cadBuscarCuotasSociosApeNom($codAreaCoordinacion,$_SESSION['vs_APE1'],$_SESSION['vs_APE2'],
		                                            $_SESSION['vs_CODAGRUPACION'],$anioCuotas);			
					
			  //echo 	"<br><br>2-2-1-1cPresidente:cPresidenteSociosApeNomPaginarInc:_pagi_sql:";print_r($_pagi_sql);								
			}
			else //entra cuando vuelve de actualizar, ....
			{ //echo "<br><br> 2-2-2";
			  
			  $_pag_propagar_opcion_buscar = 'AGRUPACION';	
					
				 //$_pagi_sql = formarCadBuscarDatosSocios($_SESSION['vs_CODAGRUPACION']);
     //$_pagi_sql = cadBuscarCuotasSocios($_SESSION['vs_CODAGRUPACION'],$anioCuotas,'alta',$estadoCuota); 	
     $_pagi_sql = cadBuscarCuotasSocios($codAreaCoordinacion,$_SESSION['vs_CODAGRUPACION'],$anioCuotas,$estadoSocio,$estadoCuota); 	
					
			  //echo 	"<br><br>2-2-2-1cPresidente:cPresidenteSociosApeNomPaginarInc:_pagi_sql:";print_r($_pagi_sql);					
			}		
	}
///	 $datosFormMiembro['ANIOCUOTA']['valorCampo'] 
} //no (isset($_POST['datosFormMiembro']['APE1'])|| ... ||		(isset($_POST['datosFormSocio']['CODAGRUPACION'])
?>