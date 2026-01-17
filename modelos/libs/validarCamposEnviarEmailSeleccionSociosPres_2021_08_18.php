<?php
/*---------------- Inicio validarCamposEnviarEmailSeleccionSociosPres -----------------------------
DESCRIPCION:
Valida los campos de selección de email de socios y otros datos, según pertenencia a:
"agrupación, país domicilio, CCAA domicilio (para ES), y provincia domicilio (para ES)".

Tratará como error lógico NINGUNA selección, o más de una selección. 
Por ejemplo si se selecciona por agrupación se excluyen las demás opciones, y lo mismo con las otras
     
LLAMADA: validarCamposEmailAdjuntosSociosPres() y a su vez desde cPresidente:enviarEmailSociosPres
LLAMA:  Ninguna función

OBSERVACIONES: Probada PHP 7.3.21 
----------------------------------------------------------------------------------------------*/
function validarCamposEnviarEmailSeleccionSociosPres($datosEmailFormSelecionSocios)//recibe $datosEmailFormSelecionSocios=$datosCamposEmailForm['datosSelecionEmailSocios']   
{	
 //echo "<br><br>0-1 validarCamposEnviarEmailSeleccionSociosPres:datosEmailFormSelecionSocios: ";print_r($datosEmailFormSelecionSocios);	

	$contNumSelecciones = 0;//sería error
	
	foreach ($datosEmailFormSelecionSocios as $indiceCampo => $valCampo)
	{		
		//echo "<br><br>1 validarCamposEnviarEmailSeleccionSociosPres:resulValidar:[$indiceCampo]=: ";print_r($valCampo);
		if ($valCampo == '-' || $valCampo == 'NINGUNA' ) //separador en formulario
		{ 
			 $datosEmailFormSelecionSocios[$indiceCampo] = 'NINGUNA';	
		}
		else
		{ $contNumSelecciones++;
		}			
	}
	
	//echo "<br><br>2 validarCamposEnviarEmailSeleccionSociosPres:contNumSelecciones = ";print_r($contNumSelecciones);
	
	$resulValidar = $datosEmailFormSelecionSocios;
	
	if ($contNumSelecciones === 0 )
	{ 
   $resulValidar['codError'] = '80200';//	80200:	No son válidos los campos
			$resulValidar['errorMensaje'] = "Error: Debes elegir una opción y no más de una para selección de socios/as";	
	} 	
	elseif ($contNumSelecciones >= 2 )
	{ $resulValidar['codError'] = '80200';//	80200:	No son válidos los campos	
			$resulValidar['errorMensaje'] = "Error: Debes elegir una opción y no más de una para selección de socios/as";	
	} 
	else
	{	$resulValidar['codError'] = '00000';	
			$resulValidar['errorMensaje'] = "";	
	}	
	
	//echo "<br><br>3 validarCamposEnviarEmailSeleccionSociosPres:resulValidar: ";print_r($resulValidar);
	
 return $resulValidar;
}
/*----------------------------- Fin validarCamposEnviarEmailSeleccionSociosPres -----------------------------*/
?>
