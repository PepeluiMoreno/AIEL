<?php
/******************* Inicio vCuerpoAsignarCoordArea.php *******************************************
FICHERO: vCuerpoAsignarCoordArea.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Es el formulario que muestra algunos datos personales de un socio, buscado previamente para asignarle 
un área de coordinación y que permite asignarle un área de coordinación y el rol de coordinador a la vez.

Tiene unos botones para "Asignación coordinación", y para "Cancelar"

LLAMADA: vistas/presidente/vAsignarCoordAreInc.php 
y previamente desde cPresidente.php:asignarCoordinacionArea()

LLAMA: vistas/presidente/formAsignarCoordArea.php
e incluye plantillasGrales

OBSERVACIONES: El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario.
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
    ASIGNAR UN ÁREA DE COORDINACIÓN TERRITORIAL A UN SOCIO/A COORDINADOR/A	  	
</h3>		

<?php require_once './vistas/presidente/formAsignarCoordArea.php'; ?>

</div>
<!--****************************** Fin Cuerpo central  ********************-->
</div>
<!--************************ Fin vCuerpoAsignarCoordArea.php ***************-->