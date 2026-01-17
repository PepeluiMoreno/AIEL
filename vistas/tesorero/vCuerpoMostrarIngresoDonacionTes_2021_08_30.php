<?php
/***************** Inicio vCuerpoMostrarIngresoDonacionTes *******************************************
FICHERO: vCuerpoMostrarIngresoDonacionTes.php
VERSION:  PHP 7.3.21	

DESCRIPCION: 
Formulario para mostrar todos los datos de una donación concreta a partir de la tabla DONACION

LLAMADA: vistas/tesorero/vMostrarIngresoDonacionTes.php
LLAMA: vistas/tesorero/formMostrarIngresoDonacionTes.php.php
e incluye plantillasGrales

OBSERVACIONES: 
*******************************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';

/************************** Inicio Cuerpo central ***************************/

if (isset($navegacion['cadNavegEnlaces'])) 
{
    echo $navegacion['cadNavegEnlaces'];
}
?>
	
<br /><br />	
<h3 align="center">
    MOSTRAR DATOS DE UNA DONACIÓN	  	
</h3>
<br />	
	 

<?php require_once './vistas/tesorero/formMostrarIngresoDonacionTes.php'; ?>


<!-- ********************  Inicio form botón anterior******************** --> 		
<div align="center">
    <br />	
    <?php
    if (isset($navegacion['anterior'])) 
				{
        echo $navegacion['anterior'];
    }
    ?>	
    <br />
</div>		
</div>
<!--****************************** Fin Cuerpo central  ********************-->
</div>
<!--*********************** Fin  vCuerpoMostrarIngresoDonacionTes ***********-->