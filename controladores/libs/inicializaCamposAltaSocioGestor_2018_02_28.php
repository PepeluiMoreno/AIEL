<?php
/*-----------------------------------------------------------------------------
FICHERO: inicializaCamposAltaSocioGestor.php
VERSION: PHP 5.2.3
DESCRIPCION: Inicializa los campos necesarios para la función alta de Socio()
LLAMADO: desde "cPresidente.php: altaSocioPorGestorPres()						 
OBSERVACIONES: En realidad según esta desarrollado "validarCamposSocio.php" no 
               es imprescidible, pués funciona aunque no se inicialicen estos campos
------------------------------------------------------------------------------*/
 $datosInicio['codError']='00000';
 $datosInicio['errorMensaje']='';
	
	$datosInicio['datosFormMiembro']['INFORMACIONCARTAS']['valorCampo']='SI';
	$datosInicio['datosFormMiembro']['INFORMACIONCARTAS']['codError']='00000';
	$datosInicio['datosFormMiembro']['INFORMACIONEMAIL']['valorCampo']='SI';
	$datosInicio['datosFormMiembro']['INFORMACIONEMAIL']['codError']='00000';
 
	$datosInicio['datosFormSocio']['CODCUOTA']['valorCampo']='General';
	$datosInicio['datosFormSocio']['CODCUOTA']['codError']='00000';
		
	/* creo que los tres siguientes no hacen nada */
 $datosInicio['datosFormUsuario']['privacidad']['valorCampo']='NO';
	$datosInicio['datosFormUsuario']['privacidad']['codError']='00000';	
	$datosInicio['datosFormUsuario']['privacidad']['errorMensaje']='';
		
 /* Hay que eliminar lo siguiente para que se pueda pedir autemtificación y otras var de sesión
	 $_SESSION = array(); //Unset all of the session variables.
	if (isset($_COOKIE[session_name()]))
	{ setcookie(session_name(), '', time()-42000, '/');//borra cookies
	}
	session_destroy();
	*/	
?>