<?php
/*-----------------------------------------------------------------------------
FICHERO: vPrivacidadInc.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: 
Contiene los includes necesarios para formar la página de mensaje privacidad.
														
Muestra un ventana pop-up y blank con información sobre privacidad y protección
de datos. Dentro del texto hay algunos links y un "mailto"
No hay tratamiento de errores.

LLAMADA: cEnlacesPie.php:privacidad()
         desde el "pie", abajo la pantalla, la barra horizontal "Protección de Datos"
         
LLAMA: vistas/pie/vCuerpoPrivacidad.php
------------------------------------------------------------------------------*/

function  vPrivacidadInc()
{
		require_once './vistas/plantillasGrales/vCabeceraBlank.php';

		require_once './vistas/pie/vCuerpoPrivacidad.php';

		require_once './vistas/plantillasGrales/vPieFinal.php';
	}
?>