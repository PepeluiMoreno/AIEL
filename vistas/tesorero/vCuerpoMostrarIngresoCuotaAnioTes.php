<?php
/****************************** Inicio Inicio vCuerpoMostrarIngresoCuotaAnioTes.php ***********************
FICHERO: Inicio vCuerpoMostrarIngresoCuotaAnioTes.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Se muestran algunos datos personales del socio y los detalles en formato tabla del estado de 
las cuotas de ese socio en todos los años ( o se podría limitar por ejemplo a los últimos 5 años dependerá 
del límite que se ponga en el código "vistas/tesorero/formMostrarIngresoCuotaAnioTes.php" ) 

Muestra nombre archivo con la firma del socio en caso de que exista

LLAMADA: vistas/tesorero/vMostrarIngresoCuotaAnioTes.php
y previamente desde icono lupa en "vistas/tesorero/vMostrarIngresosCuotasInc.php"

LLAMA: vistas/tesorero/formMostrarIngresoCuotaAnioTes.php
e incluye plantillasGrales

OBSERVACIONES:
**********************************************************************************************************/
require_once './vistas/plantillasGrales/vContent.php';

/************************** Inicio Cuerpo central **************************/

if (isset($navegacion['cadNavegEnlaces'])) 
{
    echo $navegacion['cadNavegEnlaces'];
}
?>	

<br /><br />	
<h3 align="center">
    MOSTRAR CUOTAS Y OTROS DATOS DEL SOCIO/A  	
</h3>
<br />		 

<?php require_once './vistas/tesorero/formMostrarIngresoCuotaAnioTes.php'; ?>

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
<!--*********************** Fin  vCuerpoMostrarIngresoCuotaAnioTes ***********-->