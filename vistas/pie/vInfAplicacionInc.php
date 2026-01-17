<?php
/*-----------------------------------------------------------------------------
FICHERO: vInfAplicacionInc.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: 
Contiene los includes necesarios para formar la página de información de la 
aplicación.
														
Muestra un ventana pop-up y blank con información sobre esta aplicación de 
Gestión de Soci@s. Dentro del texto hay algunos links y un "mailto"
No hay tratamiento de errores.

LLAMADA: cEnlacesPie.php:infAplicacion()
         desde el "pie", abajo la pantalla, barra horizontal "Sobre esta aplicación"
         
LLAMA: vistas/pie/vCuerpoInfAplicacion.php
------------------------------------------------------------------------------*/

function  vInfAplicacionInc()
{
		require_once './vistas/plantillasGrales/vCabeceraBlank.php';

		require_once './vistas/pie/vCuerpoInfAplicacion.php';

		require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>