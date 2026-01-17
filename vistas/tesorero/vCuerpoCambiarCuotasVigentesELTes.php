<?php
/********************* vCuerpoCambiarCuotasVigentesELTes.php  ************************************
FICHERO: vCuerpoCambiarCuotasVigentesELTes.php
VERSIÓN: PHP PHP 7.3.21

DESCRIPCION: Es el formulario donde se muestra el importe y datos actuales de las cuotas vigentes 
para EL para el "tipo de cuota y año elegida", y con un campo para para introducir el nuevo importe 
para ese tipo de cuota y año = (Y+1)   

Se muestra el resultado con número de cambios de cuotas de socios afectados y actualizadas para 
el año siguiente, o mensaje de error.                 
	
LLAMADA: vistas/tesorero/vCambiarCuotasVigentesELTesInc.php y previmanente al hacer clic en 
icono "Modificar" (pluma) del formulario: vistas/tesorero/vCuerpoMostrarCuotasVigentesELTes.php


LLAMA: vistas/tesorero/formCambiarCuotasVigentesELTes.php

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
    ACTUALIZAR LAS CUOTAS VIGENTES DE EUROPA LAICA PARA EL PRÓXIMO AÑO	  	
</h3>		

<?php require_once './vistas/tesorero/formCambiarCuotasVigentesELTes.php'; ?>

</div>
<!--****************************** Fin Cuerpo central  ********************-->
</div>
<!--************************ Fin vCuerpoCambiarCuotasVigentesELTes.php ***************-->