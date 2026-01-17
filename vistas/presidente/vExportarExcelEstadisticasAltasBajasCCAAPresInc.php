<?php
/*----------------------------------------------------------------------------------------------------
FICHERO: vExportarExcelEstadisticasAltasBajasCCAAPresInc.php
VERSION: PHP 7.3.21

DESCRIPCION: Formulario para exportar a Excel informes estadísticos por "CCAA" (comunundades autónomas)
y "años" a fecha Y-12-31 con los datos siguientes: 

Total de Alta,	ALTAS_ANIO(Total	H	%H	M	%M),BAJAS_ANIO(Total	H	%H	M	%M),	Netas	de Alta(por año)

Permite elegir CCAA (comunundades autónomas), (lo normal es que se incluyan todas) y "rangos de año
inferior-superior" a fecha Y-12-31 desde el año 2009 al actual.

LLAMADA: cPresidente.php:cExportarExcelEstadisticasAltasBajasCCAAPres(),
y previamente desde vistas/presidente/vMenuEstadisticasPres.php (página menú ESTADÍSTICAS Y 
DATOS DISPONIBLES PARA PRESIDENCIA)

LLAMA: vistas/presidente/vCuerpoExportarExcelEstadisticasAltasBajasCCAAPres.php
e incluye plantillasGrales

OBSERVACIONES: El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario.

-----------------------------------------------------------------------------------------------------*/
function vExportarExcelEstadisticasAltasBajasCCAAPresInc($tituloSeccion,$enlacesFuncionRolSeccId,$navegacion,$parValorComboCCAASocios)
{
		require_once './vistas/plantillasGrales/vCabeceraSalir.php';

  require_once './vistas/presidente/vCuerpoExportarExcelEstadisticasAltasBajasCCAAPres.php';
  
  require_once './vistas/plantillasGrales/vPieFinal.php';

}
?>