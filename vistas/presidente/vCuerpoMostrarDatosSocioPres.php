<?php
/**************** Inicio vCuerpoMostrarDatosSocioPres  *******************************************************
FICHERO: vCuerpoMostrarDatosSocioPres.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Contiene menú idz de "Secciones" y el formulario para mostrar los datos de un socio al rol Presidente
													
LLAMADA: vistas/presidente/vMostrarDatosSocioPresInc.php y a su vez desde cPresidente.php:mostrarDatosSocioPres(),
en lista de socios desde el icono Ver = Lupa
LLAMA: vistas/presidente/formMostrarDatosSocioPres.php
											
OBSERVACIONES: 
*************************************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';

/************************* Inicio Cuerpo central ************************/

if (isset($navegacion['cadNavegEnlaces'])) 
{
    echo $navegacion['cadNavegEnlaces'];
}
?>	
<br /><br />		
<h3 align="center">
    DATOS SOCIO/A	  	
</h3>	
<!--*** Inicio formMostrarDatosSocioPres (se podría pasar com parámetro)*******-->
<?php require_once './vistas/presidente/formMostrarDatosSocioPres.php'; ?>
<!--********************  Fin formMostrarDatosSocioPres ***********************-->
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

<!--******************************* Fin Cuerpo vCuerpoMostrarDatosSocioPres ***************-->