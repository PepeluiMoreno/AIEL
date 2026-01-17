<?php
/******************************* vCuerpoAnularSocioPendienteConfirPres.php *****************************
FICHERO: vCuerpoAnularSocioPendienteConfirPres.php
VERSION: PHP 7.3.21

En este formulario se algunos datos personales de un "casi" socio que inició el alta por él mismo 
y aún está "PENDIENTE-CONFIRMACION" su alta por él mismo. 
En el formulario en un botón se pide confirmación para anular el intento de alta del socio. 
También botón "No eliminar", pide segunda confimación 

LLAMADA: vistas/presidente/vAnularSocioPendienteConfirPres.php 
y previamente desde cPresidente.php:anularSocioPendienteConfirmarPres()

LLAMA: vistas/presidente/formAnularSocioPendienteConfirmPres.php e incluye plantillasGrales. 

OBSERVACIONES: 
********************************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';

/************************** Inicio Cuerpo central **************************/

if (isset($navegacion['cadNavegEnlaces'])) 
{
    echo $navegacion['cadNavegEnlaces'];
}
?>	

<br /><br />		
<h3 align="center">
    ELIMNAR LOS DATOS DEL SOCIO/A PENDIENTE DE CONFIRMAR SU ALTA (ACCIÓN IRREVERSIBLE)	  	
</h3>		

<?php require_once './vistas/presidente/formAnularSocioPendienteConfirmPres.php'; ?>

</div>
<!--****************************** Fin Cuerpo central  ********************-->
</div>
<!--******************************* Fin vCuerpoAnularSocioPendienteConfirPres.php ***************-->