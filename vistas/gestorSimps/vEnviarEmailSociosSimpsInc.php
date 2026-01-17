<?php
/*----------------------------------------------------------------------------------------------------
FICHERO: vEnviarEmailSociosSimpsInc.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION:													
Formulario de selección para formar los emails personalizado a enviar a los socios desde el 
rol de Gestor de Simpatizantes, que permite seleccionar los emails de los socios destinatarios por:	CODAGRUPACION,
CODPAISDOM, CCAA, CODPROV.

Además del texto de subject y body, puede anexar hasta dos ficheros con un límite de 4MB cada y sólo 
determinados tipos archivos.

Permite elegir entre los siguientes emails de envío FROM: "info@europalaica.org, 

Es obligatorio incluir un BCC que recibirá una copia del email.

Además el formulario tiene tres botones de selección que permiten elegir:
-1. Enviar emails PERSONALIZADOS a socios/as: se enviarán uno a uno y personlizados con nombres socios (lento)
-2. Enviar emails NO PERSONALIZADOS a socios/as: se enviarán todos los emails a la vez y sin personalizar (más rápido)
-3. Enviar email de prueba solo a BCC: al final mostrará en pantalla a cuántos socios/as se habría enviado el email
- Cancelar enviar emails: Salir sin enviar email

RECIBE: array "$datosEmail" y otros
													
LLAMADA: cGestorSimps.php:enviarEmailSimpsGes()											
													
OBSERVACIONES: 
Es un clon de la función  vEnviarEmailSociosPresInc.php
---------------------------------------------------------------------------------------------------*/	
function vEnviarEmailSociosSimpsInc($tituloSeccion,$enlacesFuncionRolSeccId,$navegacion,$datosEmail,$parValorCombo) 
{
		$parValorComboAgrupaSocio   = $parValorCombo['agrupaSocio'];
		$parValorComboPaisDomicilio = $parValorCombo['paisDomicilio'];
		$parValorComboCCAADomicilio = $parValorCombo['CCAADomicilio'];
		$parValorComboProvDomicilio = $parValorCombo['provDomicilio'];

		require_once './vistas/plantillasGrales/vCabeceraSalir.php';

		require_once './vistas/gestorSimps/vCuerpoEnviarEmailSociosSimps.php';

		require_once './vistas/plantillasGrales/vPieFinal.php';
}

?>