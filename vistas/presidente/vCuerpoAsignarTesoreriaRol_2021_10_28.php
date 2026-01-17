<?php
/******************* Inicio vCuerpoAsignarTesoreriaRol.php *******************************************
FICHERO: vCuerpoAsignarTesoreriaRol.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Es el formulario que muestra algunos datos personales de un socio, buscado previamente para asignarle 
rol de Tesorería

Tiene unos botones para "Asignación Rol Tesoreria", y para "Cancelar asignar rol Tesoreria"

LLAMADA: vistas/presidente/vAsignarTesoreriaRolInc.php 
y previamente desde cPresidente.php:asignarTesoreriaRolBuscar()

LLAMA: vistas/presidente/formAsignarTesoreriaRol.php
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
    ASIGNAR EL ROL DE TESORERÍA A UN SOSCIO/A
</h3>		

<?php require_once './vistas/presidente/formAsignarTesoreriaRol.php'; ?>

</div>
<!--****************************** Fin Cuerpo central  ********************-->
</div>
<!--************************ Fin vCuerpoAsignarTesoreriaRol.php ***************-->