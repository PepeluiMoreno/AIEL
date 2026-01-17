<?php
/*-----------------------------------------------------------------------------------------------------
FICHERO: vTotalesDonacionesInc.php
VERSION: PHP 7.3.21

DESCRIPCION: Contiene el formulario que muesta la tabla "TOTALES DONACIONES". Decreciente por años. 
Entre otros campos incluye: 
Nº total donantes, 	Tipo de donante (socios, donantes identificados, anónimos) , Modo de ingreso, 
Gastos donación, Total donaciones €,  

LLAMADA: cTesorero.php:mostrarTotalesDonaciones() y previamente desde el formulario:
vMostrarDonacionesInc.php ( Botón "Totales donaciones" )

LLAMA: vistas/tesorero/vCuerpoTotalesDonaciones.php
e incluye plantillasGrales

OBSERVACIONES:
													
------------------------------------------------------------------------------------------------------*/
function vTotalesDonacionesInc($tituloSeccion,$enlacesFuncionRolSeccId,$totalesAniosPagosDonaciones)
{
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';

  require_once './vistas/tesorero/vCuerpoTotalesDonaciones.php';
  
  require_once './vistas/plantillasGrales/vPieFinal.php';

}
?>