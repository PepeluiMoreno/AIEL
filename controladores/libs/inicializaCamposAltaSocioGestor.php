<?php
/*------------------ inicializaCamposAltaSocioGestor.php ----------------------
FICHERO: inicializaCamposAltaSocioGestor.php
VERSION: PHP 7.3.21

DESCRIPCION: Inicializa los campos necesarios para la función alta de Socio(), 
y otros para evitar Notices

LLAMADO: controladores/libs/altaSocioPorGestor.php
que a su vez es incluida desde cCoordinador.php:altaSocioPorGestorCoord(),
cPresidente.php:altaSocioPorGestorPres(), cTesorero.php:altaSocioPorGestorTes()
		 
OBSERVACIONES: Creo este script para aligerar texto en la función 
"controladores/libs/altaSocioPorGestor.php"
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

 /* Para Gestores hay que eliminar lo siguiente para que se pueda pedir autentificación y otras var de sesión
	$_SESSION = array(); //Unset all of the session variables.
	if (isset($_COOKIE[session_name()]))
	{ setcookie(session_name(), '', time()-42000, '/');//borra cookies
	}
	session_destroy();
	*/	
	/*------------------ Fin inicializaCamposAltaSocioGestor.php -------------------*/
?>