<?php
/*--------------- vAniadirDonacionConceptoInc.php  -----------------------------------------------
FICHERO: vAniadirDonacionConceptoInc.php

VERSIÓN: PHP PHP 7.3.21

DESCRIPCION: Fomulario para añadir un nuevo Concepto de Donación a la tabla "DONACIONCONCEPTOS"             	

Tiene unos botones para "Crear nuevo Concepto Donación", y para "NO Crear nuevo Concepto Donación"

Antes de aceptar la inserción hace pregunta de confirmación 
	
LLAMADA: cTesorero.php:aniadirDonacionConceptoTes() y al hacer clic en icono "Añadir nuevo Concepto de Donación" 
en el formulario: vistas/tesorero/vDonacionConceptosInc.php (DE cTesorero.php:mostrarDonaciones())

LLAMA: vistas/tesorero/vCuerpoAniadirDonacionConcepto.php e incluye plantillasGrales

OBSERVACIONES:
----------------------------------------------------------------------------------------------------*/
function vAniadirDonacionConceptoInc($tituloSeccion,$datosDonacionConcepto,$navegacion)
{ 
  require_once './vistas/plantillasGrales/vCabeceraSalir.php'; 
		
  require_once './vistas/tesorero/vCuerpoAniadirDonacionConcepto.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';		
}
?>