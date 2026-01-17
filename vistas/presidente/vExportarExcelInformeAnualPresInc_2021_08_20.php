<?php
/*----------------------------------------------------------------------------------------------------
FICHERO: vExportarExcelInformeAnualPresInc.php
VERSION: PHP 7.3.21

DESCRIPCION: Formulario para exportar datos soci@s a Excel para informe anual de Secretaría, 
incluye las plantillas y formulario para la exportación de socios por presidencia

En el archivo Excel se incluye todos los socios/as que en el año correspondiente estuvieron de alta,
aunque después en ese mismo año se diesen de baja.

Este formulario permite elegir agrupación (aunque lo normal es que se incluyan todas) y el año desde 2009 
(lo normal es incluir el año que finalizó)

LLAMADA: cPresidente.php:cExportarExcelInformeAnualPres(), 
y previamente desde vistas/presidente/vMenuEstadisticasPres.php (página menú ESTADÍSTICAS Y 
DATOS DISPONIBLES PARA PRESIDENCIA)

LLAMA: vistas/presidente/vCuerpoExportarExcelInformeAnualPres.php
e incluye plantillasGrales

OBSERVACIONES: El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario.

-----------------------------------------------------------------------------------------------------*/
function vExportarExcelInformeAnualPresInc($tituloSeccion,$enlacesFuncionRolSeccId,$navegacion,$parValorComboAgrupaSocios)
{
		require_once './vistas/plantillasGrales/vCabeceraSalir.php';

  require_once './vistas/presidente/vCuerpoExportarExcelInformeAnualPres.php';
  
  require_once './vistas/plantillasGrales/vPieFinal.php';

}
?>