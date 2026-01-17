<?php
/*-----------------------------------------------------------------------------------------------------
FICHERO: vExcelDonacionesTesoreroInc.php
VERSION: PHP 7.3.21	

DESCRIPCION: Contiene el formulario que permite elegir el año de las donaciones y los tipos 
de donates: Todos,Socios,Simpatizantes (no socios),Anónimos, para a exportar a Excel las donaciones 
con información individual de cada donación.

LLAMADA: vistas/tesorero/vMostrarDonacionesInc.php (botón superior: Exportar las donaciones a archivo Excel)
y después cTesorero.php:excelDonacionesTesorero()

LLAMA: vistas/tesorero/vCuerpoExcelDonacionesTesorero.php
y contiene plantillasGrales/vCabeceraSalir.php

OBSERVACIONES:
-----------------------------------------------------------------------------------------------------*/
function vExcelDonacionesTesoreroInc($tituloSeccion,$enlacesFuncionRolSeccId,$navegacion)
{
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';

  require_once './vistas/tesorero/vCuerpoExcelDonacionesTesorero.php';
  
  require_once './vistas/plantillasGrales/vPieFinal.php';

}
?>