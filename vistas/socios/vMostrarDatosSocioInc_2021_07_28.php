<?php
/*-----------------------------------------------------------------------------
FICHERO: vMostrarDatosSocioInc.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Se muestran los datos de un socio al propio socio (incluidos los propios gestores), 
sin permitir modificaciones.
La parte de navegación se añade, para que cuando un socio gestor CODROL >2 
(presidente, coordinador, secretaria, tesoreria, etc....) accede a sus propios datos
mantenga la navegación, si no es gestor no se muestra ninguna barra.

LLAMADA:  controladoSocios.php:mostrarDatosSocio()

Incluye las plantillas: vistas/plantillasGrales/vCabeceraSalir.php,
vistas/plantillasGrales/vPieFinal.php

OBSERVACIONES:
------------------------------------------------------------------------------*/
function vMostrarDatosSocioInc($tituloSeccion,$resDatosSocio,$navegacion)	
{ 
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';

	 $datosSocio = $resDatosSocio['valoresCampos'];
	
  require_once './vistas/socios/vCuerpoMostrarDatosSocio.php';
	
  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>