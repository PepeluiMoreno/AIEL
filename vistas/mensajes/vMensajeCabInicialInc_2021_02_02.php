<?php
/*------------------------------------------------------------------------------
FICHERO: vMensajeCabInicialInc.php

VERSION: PHP 7.3.21
DESCRIPCION: 
En la vista con la cabecera inicial de entrada en la aplicación, y en la barra 
superior aparecen links de Entrar, Nuevo socio, Recordar contraseña y Salir.
Además de los textos que se reciban en $arrayParamMensaje y $enlacesSeccIzda para 
el menú izdo.
vCuerpoMensaje.php no muestra nada navegación ya que sólo sería para rol socio.

OBSERVACIONES: Llamado desde controladores.php

Agustín: añado más comentarios
2019-02-01: Corrección $tituloSeccion
------------------------------------------------------------------------------*/
function vMensajeCabInicialInc($tituloSeccion,$arrayParamMensaje,$enlacesSeccIzda)
{ 
 //echo "<br><br>0-1 vistas/mensajes/vMensajeCabInicialInc.php:arrayParamMensaje: ";print_r($arrayParamMensaje);
	//echo "<br><br>0-2 vistas/mensajes/vMensajeCabSalirInc.php:enlacesSeccIzda: ";print_r($enlacesSeccIzda);	
	
  require_once './vistas/plantillasGrales/vCabeceraInicial.php';

	 require_once './vistas/mensajes/vCuerpoMensaje.php';//contiene vContent.php
	

  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>