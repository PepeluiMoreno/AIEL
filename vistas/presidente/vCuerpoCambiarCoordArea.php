<?php
/******************* Inicio vCuerpoCambiarCoordArea.php ********************************************
FICHERO: vCuerpoCambiarCoordArea.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Es el formulario que muestra algunos datos personales, buscados previamente, de un socio que ya tiene
un área de coordinación asignada.

Mediante botón: "Cambiar área coordinación", se puede cambiarle la coordinación del área territorial
 actual por otra área, (o botón para "Cancelar" )


LLAMADA: vistas/presidente/vCambiarCoordAreaInc.php
y previamente cPresidente.php:cambiarCoordinacionArea() 


LLAMA: vistas/presidente/formCambiarCoordArea.php
e incluye plantillasGrales

OBSERVACIONES: El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario.
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
    CAMBIAR UN ÁREA DE COORDINACIÓN TERRITORIAl A COORDINADOR/A	  	
</h3>		

<?php require_once './vistas/presidente/formCambiarCoordArea.php'; ?>

</div>
<!--****************************** Fin Cuerpo central  ********************-->
</div>
<!--************************ Fin vCuerpoCoordArea.php ***************-->