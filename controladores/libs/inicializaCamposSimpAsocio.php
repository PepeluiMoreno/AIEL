<?php
/*-----------------------------------------------------------------------------
FICHERO: inicializaCamposSimpAsocio.php
VERSION: PHP 5.2.3
DESCRIPCION: Inicializa los campos necesarios para la función simpatizanteAsocio()
LLAMADO: desde "controladorSimpatizantes.php						 
OBSERVACIONES: 
------------------------------------------------------------------------------*/
 $resDatosAltaSocioSimp['valoresCampos']['codError']='00000';
 $resDatosAltaSocioSimp['valoresCampos']['errorMensaje']='';
	
	$resDatosAltaSocioSimp['valoresCampos']['datosFormMiembro']['INFORMACIONCARTAS']['valorCampo']='SI';
	$resDatosAltaSocioSimp['valoresCampos']['datosFormMiembro']['INFORMACIONCARTAS']['codError']='00000';
	$resDatosAltaSocioSimp['valoresCampos']['datosFormMiembro']['INFORMACIONEMAIL']['valorCampo']='SI';
	$resDatosAltaSocioSimp['valoresCampos']['datosFormMiembro']['INFORMACIONEMAIL']['codError']='00000';
 
	$resDatosAltaSocioSimp['valoresCampos']['datosFormSocio']['CODCUOTA']['valorCampo']='General';
	$resDatosAltaSocioSimp['valoresCampos']['datosFormSocio']['CODCUOTA']['codError']='00000';	
	
	
	//$resDatosAltaSocioSimp['valoresCampos']= $resDatosAltaSocioSimp;
	/* creo que los tres siguientes no hacen nada 
 $resulValidar['datosFormUsuario']['privacidad']['valorCampo']='NO';
	$resulValidar['datosFormUsuario']['privacidad']['codError']='00000';	
	$resulValidar['datosFormUsuario']['privacidad']['errorMensaje']='';
	*/
	
	
	//$resDatosAltaSocioSimp['datosFormCuotaSocio']['ANIOCUOTA']=

?>