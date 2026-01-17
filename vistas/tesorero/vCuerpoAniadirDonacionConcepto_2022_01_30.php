<?php
/********************* vCuerpoAniadirDonacionConcepto.php  ************************************
FICHERO: vCuerpoAniadirDonacionConcepto.php
VERSIÓN: PHP PHP 7.3.21

DESCRIPCION: Fomulario para añadir un nuevo Concepto de Donación a la tabla "DONACIONCONCEPTOS"             	

Tiene unos botones para "Crear nuevo Concepto Donación", y para "NO Crear nuevo Concepto Donación"

Antes de aceptar la inserción hace pregunta de confirmación 
	
LLAMADA: vAniadirDonacionConceptoInc.php y antes al hacer clic en icono "Añadir nuevo Concepto de Donación" 
en el formulario: vistas/tesorero/vDonacionConceptosInc.php

LLAMA: vistas/tesorero/formAniadirDonacionConcepto.php 

OBSERVACIONES:
*****************************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';

/************************* Inicio Cuerpo central ***************************/

if (isset($navegacion['cadNavegEnlaces'])) 
{
    echo $navegacion['cadNavegEnlaces'];
}
?>	
<br /><br />		

<h3 align="center">
    AÑADIR NUEVO CONCEPTO DONACIÓN	  	
</h3>		

<?php require_once './vistas/tesorero/formAniadirDonacionConcepto.php'; ?>

</div>
<!--****************************** Fin Cuerpo central  ********************-->
</div>
<!--************************ Fin vCuerpoAniadirDonacionConcepto.php ***************-->