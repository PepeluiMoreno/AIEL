<?php
/**************** Inicio vCuerpoMostrarDatosSocioCoord  *******************************************************
FICHERO: vCuerpoMostrarDatosSocioCoord.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Contiene menú idz de "Secciones" y el formulario para mostrar los datos de de un socio al coordinador
													
LLAMADA: vistas/coordinador/vMostrarDatosSocioCoordInc.php y a su vez desde cCoordinador.php:mostrarDatosSocioCoord(),
en lista de socios desde el icono Ver = Lupa
LLAMA: vistas/coordinador/formMostrarDatosSocioCoord.php
											
OBSERVACIONES: 
*************************************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';

/************************ Inicio Cuerpo central ******************************/

if (isset($navegacion['cadNavegEnlaces'])) 
{
    echo $navegacion['cadNavegEnlaces'];
}
?>	
<br /><br />		
<h3 align="center">
    MOSTRAR DATOS DEL SOCIO/A  	
</h3>	
<!--*** Inicio formMostrarDatosSocioCoord (se podría pasar com parámetro)*******-->
<?php require_once './vistas/coordinador/formMostrarDatosSocioCoord.php'; ?>
<!--********************  Fin formMostrarDatosSocioCoord ***********************-->
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

<!--******************************* Fin vCuerpoMostrarDatosSocioCoord **********-->