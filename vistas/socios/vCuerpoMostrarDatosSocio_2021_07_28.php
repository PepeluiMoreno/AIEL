<?php
/****************************** Inicio vCuerpoMostrarDatosSocio ***************
FICHERO: vCuerpoMostrarDatosSocio.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Contiene menú idz de "Secciones" y el formulario de 
             Para mostrar los datos de de un socio
													
LLAMADA: vistas/sociosd/vMostrarDatosSocioInc.php y a su vez desde controladoSocios.php:mostrarDatosSocio() 
											
OBSERVACIONES:  incluye /vistas/socios/formMostrarDatosSocio.php
*********************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';

/************************ Inicio Cuerpo central ************************/

if (isset($navegacion['cadNavegEnlaces'])) 
{
    echo $navegacion['cadNavegEnlaces'];
}
?>	
<br />	
<h3 align="center">
    MOSTRAR DATOS SOCIO / A	  	
</h3>	

<!--*** Inicio formMostrarDatosSocio **************************************-->
<?php require_once './vistas/socios/formMostrarDatosSocio.php'; ?>
<!--********************  Fin formMostrarDatosSocio ***********************-->

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

<!-- ********************  Fin Form botón anterior *********************** -->

</div>

<!--****************************** Fin Cuerpo central  ********************-->
</div>

<!--******************************* Fin Cuerpo vCuerpoMostrarDatosSocio **********-->