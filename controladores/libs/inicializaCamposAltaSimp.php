<?php
/*-----------------------------------------------------------------------------
FICHERO: inicializaCamposAltaSimp.php (idéntico que en socios)
VERSION: PHP 5.2.3
DESCRIPCION: Inicializa los campos necesarios para la función altaSimp()
LLAMADO: desde "controladorSocios.php						 
OBSERVACIONES: En realidad según esta desarrollado "validarCamposSimp.php" no 
               es imprescidible, pués funciona aunque no se inicialicen estos campos
------------------------------------------------------------------------------*/
 $resInsertar['codError']='00000';
 $resInsertar['errorMensaje']='';
	$resInsertar['datosFormMiembro']['INFORMACIONCARTAS']['valorCampo']="NO";
	$resInsertar['datosFormMiembro']['INFORMACIONCARTAS']['codError']='00000';
 $resInsertar['datosFormMiembro']['INFORMACIONEMAIL']['valorCampo']="SI";
	$resInsertar['datosFormMiembro']['INFORMACIONEMAIL']['codError']='00000';
	
 $resulValidar['datosFormUsuario']['privacidad']['valorCampo']='NO';
	$resulValidar['datosFormUsuario']['privacidad']['codError']='00000';	
	$resulValidar['datosFormUsuario']['privacidad']['errorMensaje']='';
	
 $_SESSION = array(); //Unset all of the session variables.

	if (isset($_COOKIE[session_name()]))
	{
    setcookie(session_name(), '', time()-42000, '/');//borra cookies
	}
	session_destroy(); 	
?>
