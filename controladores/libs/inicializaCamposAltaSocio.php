<?php
/*---------------------- inicializaCamposAltaSocioGestor.php -------------------
FICHERO: inicializaCamposAltaSocio.php
VERSION: PHP 7.3.21
DESCRIPCION: Inicializa los campos necesarios para la función altaSocio()
LLAMADO:  "controladorSocios.php:altaSocio()					 
OBSERVACIONES: Creo este script para aligerar texto en la función altaSocio()	
------------------------------------------------------------------------------*/
 $datosInicio['codError'] = '00000';
 $datosInicio['errorMensaje'] = '';
	
	//---- Para inicio  parValoresRegistrarUsuario() -----------------
	$valorDefectoPaisDoc = "ES";
	$valorDefectoPaisDom = "ES";
	$valorDefectoAgrup = '';//no ponemos ninguna agrupación por defecto 	
	
	//---- Algunos de estos son para evitar notices ------------------
	$datosInicio['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']['valorCampo'] ="NIF";
	$datosInicio['datosFormMiembro']['FECHANAC']['dia']['valorCampo'] ="00";
	$datosInicio['datosFormMiembro']['FECHANAC']['mes']['valorCampo'] ="00";
	$datosInicio['datosFormMiembro']['FECHANAC']['anio']['valorCampo'] ="0000";
	$datosInicio['datosFormMiembro']['ESTUDIOS']['valorCampo'] ="";
	$datosInicio['datosFormMiembro']['COLABORA']['valorCampo'] ="";	
	
	$datosInicio['datosFormMiembro']['INFORMACIONCARTAS']['valorCampo'] = 'SI';
	$datosInicio['datosFormMiembro']['INFORMACIONCARTAS']['codError'] = '00000';
	$datosInicio['datosFormMiembro']['INFORMACIONEMAIL']['valorCampo'] = 'SI';
	$datosInicio['datosFormMiembro']['INFORMACIONEMAIL']['codError'] = '00000';
	$datosInicio['datosFormMiembro']['EMAILERROR']['valorCampo'] = 'NO';
	$datosInicio['datosFormMiembro']['EMAILERROR']['codError'] = '00000';
	$datosInicio['datosFormMiembro']['EMAILERROR']['valorCampo'] = 'NO';
	$datosInicio['datosFormMiembro']['EMAILERROR']['errorMensaje'] = '';
 
	$datosInicio['datosFormSocio']['CODCUOTA']['valorCampo'] = 'General';
	$datosInicio['datosFormSocio']['CODCUOTA']['codError'] = '00000';
		
	$datosInicio['datosFormUsuario']['privacidad']['valorCampo'] = 'NO';
	$datosInicio['datosFormUsuario']['privacidad']['codError'] = '00000';	
	$datosInicio['datosFormUsuario']['privacidad']['errorMensaje'] = '';

	//------ Destruir variables de sesion y cookies ------------------	
 $_SESSION = array(); //Unset all of the session variables.

	if (isset($_COOKIE[session_name()]))
	{
    setcookie(session_name(), '', time()-42000, '/');//borra cookies
	}
	session_destroy();	
	
		/*------------------ Fin inicializaCamposAltaSocioGestor.php -------------------*/
?>