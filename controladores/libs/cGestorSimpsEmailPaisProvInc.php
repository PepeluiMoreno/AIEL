<?php
/*----------------------------------------------------------------------------------------
FICHERO: cGestorSimpsEmailPaisProvInc.php
DESCRIPCIÓN:Incluye los if's para asignar los adecuados valores a 
            $_SESSION['vs_codPaisDomBuscarSimps'], $_SESSION['vs_codProvBuscarSimps'] y 
												$_SESSION['vs_emailBuscarSimps'] que se usan para $_SESSION['vs_pag_actual']
												que se utilizaran para guardar los valores en las búsquedas y en la páginación
												para la funciones "formarCadBuscarDatosSimps" y mPaginarLib
OBSEVACIONES: Acaso se pudiera simplificar algo 												
-----------------------------------------------------------------------------------------*/
$pagActual = $_SESSION['vs_HISTORIA']['pagActual'];

if (isset($_POST['datosFormSimp']['CODPAISDOM']) || 
    isset($_POST['datosFormSimp']['CODPROV']) ||  
    isset($_POST['datosFormSimp']['EMAIL']))
{ //echo "<br><br> 1";
  //------------------------ Incio controlEmail ---------------------------------
		if (isset($_POST['datosFormSimp']['EMAIL']))			 
		{//echo "<br><br> 1-1"; 	
		
			$_SESSION['vs_codPaisDomBuscarSimps'] = '%'; //Para que en los resultados de buscar por email
			$_SESSION['vs_codProvBuscarSimps'] = '%';    // se mueste "Todos" en los combox país, prov
			
		 require_once './modelos/libs/validarCampos.php';	
		 $resulValidarEmail=validarEmail($_POST['datosFormSimp']['EMAIL'],"");//la validación incluye trim
			//echo "<br><br> 1-1-resulValidarEmail:";print_r($resulValidarEmail);
			if ($resulValidarEmail['codError']!=='00000')
			{	//echo "<br><br> 1-1-1";
			  $_SESSION['vs_emailBuscarSimps'] = $resulValidarEmail['valorCampo'];				
					unset($_SESSION['vs_pag_actual']);

					$_pagi_sql = cadBuscarDatosSimpsEmail($_SESSION['vs_emailBuscarSimps']);  		

					//$_pagi_sql=cadBuscarDatosSimps($_SESSION['vs_codPaisDomBuscarSimps'],$_SESSION['vs_codProvBuscarSimps']); 
			}
			else //$cadEmail[]=='00000'
			{ //echo "<br><br> 1-1-2";
			  //if ($_SESSION['vs_emailBuscarSimps'] !== $resulValidarEmail['valorCampo'])//el tratamiento es el mismo sea = o !=
			  //{ 
					  $_SESSION['vs_emailBuscarSimps'] = $resulValidarEmail['valorCampo'];
						 unset($_SESSION['vs_pag_actual']); //al cambiar de email hay que iniciar siempre en la 1ª pág.
					//}
					$_pagi_sql = cadBuscarDatosSimpsEmail($_SESSION['vs_emailBuscarSimps']);  					
			} 
		}//fin isset($_POST['datosFormSimp']['EMAIL']))	
  //------------------------ Fin controlEmail ------------------------------------- 
  //------------------------ Incio control cGestorSimpsDomPaisProv ----------------		
  else // if (isset($_POST['datosFormSimp']['CODPAISDOM']) || isset($_POST['datosFormSimp']['CODPROV'])	          
		{	//echo "<br><br> 1-2"; 	
		  
		  unset($_SESSION['vs_emailBuscarSimps']);
			 if ($_SESSION['vs_codPaisDomBuscarSimps'] !== $_POST['datosFormSimp']['CODPAISDOM'])//ha cambiado de país
				{ 
				  $_SESSION['vs_codPaisDomBuscarSimps'] = $_POST['datosFormSimp']['CODPAISDOM'];
						//echo "<br><br> 1-2-1"; 
						
						if ($_SESSION['vs_codPaisDomBuscarSimps'] == 'ES')
						{ //echo "<br><br> 1-2-1-1"; 
						  if ($_POST['datosFormSimp']['CODPROV'] == 'ninguna')
						  {//echo "<br><br> 1-2-1-1-1"; 
								 $_SESSION['vs_codProvBuscarSimps'] = '%';
							 }
								else
								{ //echo "<br><br> 1-2-1-1-2"; 
									 $_SESSION['vs_codProvBuscarSimps'] = $_POST['datosFormSimp']['CODPROV'];
								}							
						}
						elseif($_SESSION['vs_codPaisDomBuscarSimps'] =='%')
						{ //echo "<br><br> 1-2-1-2"; 
							 $_SESSION['vs_codProvBuscarSimps'] = '%';
						}
						else //un país extranjero
						{ //echo "<br><br> 1-2-1-3"; 
							 $_SESSION['vs_codProvBuscarSimps'] = 'ninguna';
						}
      unset($_SESSION['vs_pag_actual']); //al cambiar de país hay que iniciar siempre en la 1ª pág.
				}//fin --------//ha cambiado de país
		  //no cambia de país 
				elseif ($_SESSION['vs_codPaisDomBuscarSimps'] == 'ES') //sigue en ES
				{ //echo "<br><br> 1-2-2"; 
				  if ($_SESSION['vs_codProvBuscarSimps'] !== $_POST['datosFormSimp']['CODPROV'])//ha cambiado de provincia
					 { //echo "<br><br> 1-2-2-1"; 
						  $_SESSION['vs_codProvBuscarSimps'] = $_POST['datosFormSimp']['CODPROV'];
						  unset($_SESSION['vs_pag_actual']);		
						}
				} 
				elseif ($_SESSION['vs_codPaisDomBuscarSimps'] == '%') //sigue en % y obliga a todas las provincias
				{ //echo "<br><br> 1-2-3"; 
				  $_SESSION['vs_codProvBuscarSimps'] = '%';
				}
				else //otros paises extranjeros
				{ //echo "<br><br> 1-2-4"; 
				  $_SESSION['vs_codProvBuscarSimps'] = 'ninguna';
				}
			 //fin de if (isset($_POST['datosFormSimp']['CODPAISDOM']) && ...
		 	/*elseif(!isset($_SESSION['vs_codPaisDomBuscarSimps']))//acaso no entre nunca,
	   { $_SESSION['vs_codPaisDomBuscarSimps'] = 'ES';//España
					$_SESSION['vs_codProvBuscarSimps'] = '%';
					unset($_SESSION['vs_pag_actual']); //??necesario	
					echo "<br><br> -----------------1-2"; 		
			 }
			 */
	   //else { /* no se hace nada, será un cambio de pág echo "<br><br> 1-2-2"; */}
				$_pagi_sql=cadBuscarDatosSimps($_SESSION['vs_codPaisDomBuscarSimps'],$_SESSION['vs_codProvBuscarSimps']); 
				
  }//viene desde otra función distinta de "cGestorSimps&accion=mostrarSimps"
		//------------------------ Fin control cGestorSimpsDomPaisProv ------------------				
}	//isset($_POST['datosFormSimp']['CODPAISDOM']) || ....
else //no isset($_POST['datosFormSimp']['CODPAISDOM']) || ....
{
 //echo "<br><br> 2";
	
	if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cGestorSimps&accion=mostrarSimps")
	{ //echo "<br><br> 2-0";
	
		 unset($_SESSION['vs_pag_actual']);//se iniciará en la 1ª pág.	de la paginación
			$_pagi_sql=cadBuscarDatosSimps($_SESSION['vs_codPaisDomBuscarSimps'],$_SESSION['vs_codProvBuscarSimps']); 
	}
	elseif ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']!==
									"index.php?controlador=cGestorSimps&accion=mostrarDatosSimpGestor" &&
								  $_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']!==
										"index.php?controlador=cGestorSimps&accion=actualizarSimpGestor" &&
								  $_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']!==
										"index.php?controlador=cGestorSimps&accion=eliminarSimpGestor"
			      )	//procede de otros niveles inferiores o sin que no es de este hilo										
	{ //echo "<br><br> 2-1"; 
			$_SESSION['vs_codPaisDomBuscarSimps'] = 'ES';    
			$_SESSION['vs_codProvBuscarSimps'] = '%';
			
			if (isset($_SESSION['vs_emailBuscarSimps']))
			{unset($_SESSION['vs_emailBuscarSimps']);}
			
			unset($_SESSION['vs_pag_actual']);//se iniciará en la 1ª pág.	
			
			$_pagi_sql=cadBuscarDatosSimps($_SESSION['vs_codPaisDomBuscarSimps'],$_SESSION['vs_codProvBuscarSimps']); 
	}
	else //volver desde otro nivel superior del mismo hilo
	{ //echo "<br><br> 2-2"; 
	  if (isset($_SESSION['vs_emailBuscarSimps'])) //??Seguro
	  { //echo "<br><br> 2-2-1"; 
				 $_pagi_sql = cadBuscarDatosSimpsEmail($_SESSION['vs_emailBuscarSimps']);  
			}
			else //??Seguro
			{ //echo "<br><br> 2-2-2";
				 $_pagi_sql=cadBuscarDatosSimps($_SESSION['vs_codPaisDomBuscarSimps'],$_SESSION['vs_codProvBuscarSimps']); 
			}		
	} 
} //no isset($_POST['datosFormSimp']['CODPAISDOM']) || ....
?>