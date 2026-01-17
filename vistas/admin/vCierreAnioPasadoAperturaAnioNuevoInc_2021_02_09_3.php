<?php
/*-----------------------------------------------------------------------------
FICHERO: vCierreAnioPasadoAperturaAnioNuevoInc.php

Es el formulario para ejecutar el "Cierre de año pasado y apertura de año nuevo" 
En los botones al final de formulario, se eligen en las opciones disponibles 
- $anioActual = date('Y') para ejecutar en año actual.
-	$anioPruebaYmas1 = date('Y')+1) para simular para año siguiente antes de enero

A partir del directorio "$dirHome", se muestra en que versión de Gestión de Soci@s, 
que ese momento se está ejecutando: 
- La REAL "europalaica.com/usuarios" 
- Versiones para PRUEBA: "europalaica.com/usuarios_copia", o "europalaica.com/usuarios_desarrollo"

E informa tmabién de la SECUENCIA DE EJECUCIÓN 

RECIBE: 	'$estadoActualizacion['actualizadoAnioY']' que contiene el valor del campo de la tabla controles par 
el año actual date('Y'), $datosMensaje para cabecera y cuerpo 

LLAMADO: cAdmin.php:cierreAnioPasadoAperturaAnioNuevoAdmin()	
LLAMA: vistas/admin/vCuerpoCierreAnioPasadoAperturaAnioNuevo.php									
									
OBSERVACIONES:
------------------------------------------------------------------------------*/
function vCierreAnioPasadoAperturaAnioNuevoInc($tituloSeccion,$estadoActualizacion,$datosMensaje,$enlacesSeccIzda,$navegacion)
{ 

  require_once './vistas/plantillasGrales/vCabeceraInicial.php';  
  
		require_once './vistas/admin/vCuerpoCierreAnioPasadoAperturaAnioNuevo.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';

}
?>