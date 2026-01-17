<?php
/**************** Inicio vCuerpoMostrarDatosAgrupacionPres *****************************************
FICHERO: vCuerpoMostrarDatosAgrupacionPres.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: 
En este formulario se muestran los datos de una AGRUPACION TERRITORIAL procedentes de la 
tabla "AGRUPACIONTERRITORIAL". Contiene menú idz de "Secciones"

RECIBE: array "$arrDatosAgrupacion" con los datos de la agrupación

LLAMADA: vistas/presidente/vMostrarDatosAgrupacionPresInc.php y previamente desde 
cPresidente.php:mostrarDatosAgrupacionPres()

LLAMA: vistas/presidente/formMostrarDatosAgrupacionPres.php
										
OBSERVACIONES: 
*****************************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';

/************************* Inicio Cuerpo central ************************/

if (isset($navegacion['cadNavegEnlaces'])) 
{
    echo $navegacion['cadNavegEnlaces'];
}
?>	
<br /><br />		
<h3 align="center">
    DATOS DE UNA AGRUPACIÓN TERRITORIAL	
</h3>	

<!--*** Inicio formMostrarDatosAgrupacionPres (se podría pasar com parámetro)*******-->

<?php require_once './vistas/presidente/formMostrarDatosAgrupacionPres.php'; ?>

<!--********************  Fin formMostrarDatosAgrupacionPres ***********************-->

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

</div 
<!-- ********************  Fin Form botón anterior *********************** -->
</div>

<!--****************************** Fin Cuerpo central  ********************-->
</div>

<!--******************************* Fin Cuerpo vCuerpoMostrarDatosAgrupacionPres ***************-->