<?php
/*---------------------- vMostrarCuotasVigentesELTesInc.php -------------------------------------
FICHERO:  vMostrarCuotasVigentesELTesInc.php
VERSION: PHP PHP 7.3.21

DESCRIPCION:
Se muestran en unas tablas los datos de las cuotas vigentes para EL del año actual y el año siguiente,
a partir de la tabla "IMPORTEDESCUOTAANIO" y desde este formulario se puede ir a la función de 
cambiar los importes de las cuotas anuales vigentes en EL para el año siguiente. 

Con llamada a función: cTeserero.php:actualizarCuotasVigentesELTes()
	
LLAMADA: cTesorero.php:cuotasVigentesELTes() y previamente desde meú izdo "-Cuotas Vigentes en EL"
LLAMA: vistas/tesorero/vCuerpoMostrarCuotasVigentesELTes.php
e incluye plantillasGrales

OBSERVACIONES:
-------------------------------------------------------------------------------------------------*/
function vMostrarCuotasVigentesELTesInc($tituloSeccion,$cuotasAnioActualEL,$cuotasAnioSiguienteEL,$navegacion)
{ 
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';	
	
  require_once './vistas/tesorero/vCuerpoMostrarCuotasVigentesELTes.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>