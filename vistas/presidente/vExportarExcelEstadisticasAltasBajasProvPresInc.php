<?php
/*----------------------------------------------------------------------------------------------------
FICHERO: vExportarExcelEstadisticasAltasBajasProvPresInc.php
VERSION: PHP 7.3.21

DESCRIPCION: Formulario para exportar a Excel informes estadísticos por "provincias" y "años" 
a fecha Y-12-31 con los datos siguientes: 

Total de Alta,	ALTAS_ANIO(Total	H	%H	M	%M),BAJAS_ANIO(Total	H	%H	M	%M),	Netas	de Alta(por año)

Permite elegir provincia (lo normal es que se incluyan todas) y "rangos de año inferior-superior" 
a fecha Y-12-31 desde el año 2009 al actual.

LLAMADA: cPresidente.php:cExportarExcelEstadisticasAltasBajasProvPres(),
y previamente desde vistas/presidente/vMenuEstadisticasPres.php (página menú ESTADÍSTICAS Y 
DATOS DISPONIBLES PARA PRESIDENCIA)

LLAMA: vistas/presidente/vCuerpoExportarExcelEstadisticasAltasBajasProvPres.php
e incluye plantillasGrales

OBSERVACIONES: El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario.

-----------------------------------------------------------------------------------------------------*/
function vExportarExcelEstadisticasAltasBajasProvPresInc($tituloSeccion,$enlacesFuncionRolSeccId,$navegacion,$parValorComboProvSocios)
{
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';

  require_once './vistas/presidente/vCuerpoExportarExcelEstadisticasAltasBajasProvPres.php';
  
  require_once './vistas/plantillasGrales/vPieFinal.php';

}
?>