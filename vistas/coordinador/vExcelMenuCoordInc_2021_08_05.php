<?php

/*---------------------------------------------------------------------------------
FICHERO: vExcelMenuCoordInc.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: 
Formulario para exportar a un fichero Excel, los socios de todas las agrupaciones 
correspondientes a un área de gestión o bien de una agrupación concreta elegida 
dentro de ese área territorial que gestiona un coordinador. 
En el caso del que el área de gestión incluya varias posibles agrupaciones, 
por ejemplo Andalucía, en este formulario se permite elegir una Agrupación territorial concreta

Se descarga en el PC en carpeta "Descargas" el archivo Excel mediante el navegador
Al abrir el archivo Excel, puede dar un aviso sobre seguridad.

LLAMADA: cCoordinador.php: excelSociosCoord()

LLAMA: vistas/coordinador/vCuerpoExcelMenuCoord.php y plantillas generales													

OBSERVACIONES: 
----------------------------------------------------------------------------------*/
function vExcelMenuCoordInc($tituloSeccion,$enlacesFuncionRolSeccId,$navegacion,$parValorComboAgrupaSocio,$areaGestionNom)
{
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';
 
  require_once './vistas/coordinador/vCuerpoExcelMenuCoord.php';
  
  require_once './vistas/plantillasGrales/vPieFinal.php';
 
}
?>