<?php

/*---------------------------------------------------------------------------------
FICHERO: vExcelMenuPresInc.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: 
Formulario para exportar a un fichero Excel por un gestor con rol de Presidencia, 
Vice. y Secretaría, algunos datos de los socios de las agrupaciones elegidas
en este formulario 

Se descarga en el PC en carpeta "Descargas" el archivo Excel mediante el navegador
Al abrir el archivo Excel, puede dar un aviso sobre seguridad.

LLAMADA: cPresidente.php: excelSociosPres()

LLAMA: vistas/presidente/vCuerpoExcelMenuPres.php y plantillas generales													

OBSERVACIONES: 
----------------------------------------------------------------------------------*/
function vExcelMenuPresInc($tituloSeccion,$enlacesFuncionRolSeccId,$navegacion,$parValorComboAgrupaSocio)
{
		require_once './vistas/plantillasGrales/vCabeceraSalir.php';

  require_once './vistas/presidente/vCuerpoExcelMenuPres.php';
  
  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>