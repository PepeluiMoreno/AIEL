<?php
/*---------------------------------------------------------------------------------------------------------
FICHERO: vMostrarIngresoCuotaAnioTesInc.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Se muestran algunos datos personales del socio y los detalles en formato tabla del estado de 
las cuotas de ese socio en todos los años ( o se podría limitar por ejemplo a los últimos 5 años dependerá 
del límite que se ponga en el código "vistas/tesorero/formMostrarIngresoCuotaAnioTes.php" ) 

Muestra nombre archivo con la firma del socio en caso de que exista

LLAMADA: cTesorero.php: mostrarIngresoCuotaAnio()
y previamente desde icono lupa en "vistas/tesorero/vMostrarIngresosCuotasInc.php"

LLAMA: vistas/tesorero/vCuerpoMostrarIngresoCuotaAnioTes.php
e incluye plantillasGrales

OBSERVACIONES:El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario
---------------------------------------------------------------------------------------------------------*/
function vMostrarIngresoCuotaAnioTesInc($tituloSeccion,$datSocio,$navegacion)
{ 
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';	
	
  require_once './vistas/tesorero/vCuerpoMostrarIngresoCuotaAnioTes.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>