<?php
/*-----------------------------------------------------------------------------
FICHERO: vCambiarExplotacion_MantenimientoAdminInc.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Es el formulario para elegir cambiar el modo de trabajo de la aplicación de 
Gestión de soci@s "MANTENIMIENTO<->EXPLOTACIÓN".
Mostrará el estado actual de modo de trabajo de la aplicación y permitirá al modo alternativo
														
Cambiar el modo de trabajo de la aplicación de Gestión de soci@s "MANTENIMIENTO<->EXPLOTACIÓN"

RECIBE: variables $cabeceraCuerpo y $textoCuerpo (con texto de información sobre 
        los modos de trabajo MANTENIMIENTO<->EXPLOTACIÓN), además de las habituales:
								$tituloSeccion,$enlacesSeccIzda,$navegacion,							
														
LLAMADA: cAdmin.php:cambiarExplotacion_MantenimientoAdmin()		
LLAMA: vCuerpoCambiarExplotacion_MantenimientoAdmin.php 			

OBSERVACIONES: utilizará la la variable $_SESSION['vs_MODOTRABAJO'] que se asignó a 
partir de la a tabla 'CONTROLMODOAPLICACION' 
------------------------------------------------------------------------------*/
function vCambiarExplotacion_MantenimientoAdminInc($tituloSeccion,$enlacesSeccIzda,$navegacion,$cabeceraCuerpo,$textoCuerpo)
{ 
  require_once './vistas/plantillasGrales/vCabeceraInicial.php';
  
  
		require_once './vistas/admin/vCuerpoCambiarExplotacion_MantenimientoAdmin.php';


  require_once './vistas/plantillasGrales/vPieFinal.php';

}
?>