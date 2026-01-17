<?php
/***************************** Inicio vCuerpoMostrarCuotasVigentesELTes.php **************************
FICHERO:  vCuerpoMostrarCuotasVigentesELTes.php
VERSION: PHP PHP 7.3.21

DESCRIPCION:
Se muestran en unas tablas los datos de las cuotas vigentes para EL del año actual y el año siguiente,
a partir de la tabla "IMPORTEDESCUOTAANIO" y desde este formulario se puede ir a la función de 
cambiar los importes de las cuotas anuales vigentes en EL para el año siguiente. 

Con llamada a función: cTeserero.php:actualizarCuotasVigentesELTes()
	
LLAMADA: vistas/tesorero/vMostrarCuotasVigentesELTesInc.php
LLAMA: vistas/tesorero/formMostrarCuotasVigentesELTes.php
e incluye plantillasGrales

OBSERVACIONES:
*****************************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';

/************************** Inicio Cuerpo central **************************/

if (isset($navegacion['cadNavegEnlaces'])) 
{
  echo $navegacion['cadNavegEnlaces'];
}
?>	

<br /><br />	
<h3 align="center">
    CUOTAS VIGENTES PARA EL AÑO ACTUAL Y ACTUALIZAR LAS CUOTAS DEL AÑO PRÓXIMO	  
</h3>
<br />		 

<?php require_once './vistas/tesorero/formMostrarCuotasVigentesELTes.php'; ?>

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
<!--********************** Fin  vCuerpoMostrarCuotasVigentesELTes.php ***********-->