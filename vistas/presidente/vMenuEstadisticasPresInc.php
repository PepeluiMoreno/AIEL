<?php
/*-----------------------------------------------------------------------------------------
FICHERO: vMenuEstadisticasPresInc.php
VERSION: PHP 7.3.21

DESCRIPCION:													
Se forma el menú estadísticas que llamar a las páginas para mostrar: Datos de socios fallecidos,
Exportar los nombres de socias/os y otros datos a Excel para el informe anual de secretaría, 
Exportar a Excel estadísticas de totales de socias/os y altas y bajas anuales organizado por:
AGRUPACIONES, PROVINCIAS o CCAA 

Uso exclusivo de rol presidencia.
													
LLAMADA: cPresidente.php:cMenuEstadisticasPres()						

LLAMA: Desde ese menú se podrá llamar a: cPresidente.php:mostrarSociosFallecidosPres(),
cExportarExcelInformeAnualPres(),cExportarExcelEstadisticasAltasBajasAgrupPres(),
cExportarExcelEstadisticasAltasBajasProvPres(),cExportarExcelEstadisticasAltasBajasCCAAPres() 
e incluye plantillasGrales

OBSERVACIONES:

------------------------------------------------------------------------------*/
function vMenuEstadisticasPresInc($tituloSeccion,$enlacesFuncionRolSeccId,$navegacion)
{ 		
		require_once './vistas/plantillasGrales/vCabeceraSalir.php';
 
  require_once './vistas/presidente/vCuerpoMenuEstadisticasPres.php';
  
  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>