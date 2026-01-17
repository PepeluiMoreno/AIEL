<?php
/******************* Inicio vCuerpovActualizarDatosCuotaSocioTes.php ********************************
FICHERO: vCuerpoActualizarDatosCuotaSocioTes.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Es el formulario para la actualización de la cantidad y tipo de cuota elegida por el socio, 
los datos bancarios, ORDENARCOBROBANCO y OBSERVACIONES y otros datos personales

LLAMADA: vistas/tesorero/vActualizarDatosCuotaSocioTesInc.php
y previamente desde cTesorero.php:actualizarDatosCuotaSocioTes()

LLAMA: vistas/tesorero/formActualizarDatosCuotaSocioTes.php
incluye plantillasGrales
							
OBSERVACIONES: 
****************************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';

/************************** Inicio Cuerpo central **************************/

if (isset($navegacion['cadNavegEnlaces'])) 
{
    echo $navegacion['cadNavegEnlaces'];
}
?>	
<br /><br />	

<h3 align="center">
    ACTUALIZAR LA CUOTA Y OTROS DATOS DEL SOCIO/A
</h3>
<br />		 

<?php require_once './vistas/tesorero/formActualizarDatosCuotaSocioTes.php'; ?>

</div>
<!--****************************** Fin Cuerpo central  ********************-->
</div>
<!--******************************* Fin vCuerpovActualizarDatosCuotaSocioTes **************-->