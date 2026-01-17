<?php
/*-----------------------------------------------------------------------------
FICHERO: vMensajeCabSalirInc.php 
PROYECTO: Europa Laica
VERSION: PHP 7.3.21

DESCRIPCION: En la vista con una cabecera incluye sólo el links para "Salir", 
además de los textos que se reciban en $arrayParamMensaje y $enlacesSeccIzda para 
el menú izdo.
Se puede utilizar para casos en que la opción es terminar y salir.

vCuerpoMensaje.php no muestra nada navegación ya que sólo sería para rol socio.

LAMADA: Desde controladores

OBSERVACIONES: 
2016-03-31: cambio $tituloSecc, por $tituloSeccion, si no se pierde en 
vistas/plantillasGrales/vContent.php
------------------------------------------------------------------------------*/
function vMensajeCabSalirInc($tituloSeccion,$arrayParamMensaje,$enlacesSeccIzda)
{ 
 //echo "<br><br>0-1 vistas/mensajes/vMensajeCabSalirInc.php:arrayParamMensaje: ";print_r($arrayParamMensaje);
 //echo "<br><br>0-2 vistas/mensajes/vMensajeCabSalirInc.php:enlacesSeccIzda: ";print_r($enlacesSeccIzda);	
 
	require_once './vistas/plantillasGrales/vCabeceraSalir.php';

	require_once './vistas/mensajes/vCuerpoMensaje.php';//contiene vContent.php
	
 require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>