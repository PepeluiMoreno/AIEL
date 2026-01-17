<?php
/******************* Inicio vCuerpoAsignarMantenimientoRol.php *******************************************
FICHERO: vCuerpoAsignarMantenimientoRol.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Es el formulario que muestra algunos datos personales de un socio, buscado previamente para asignarle 
rol de Mantenimiento 

Tiene unos botones para "Asignación Rol Mantenimiento ", y para "Cancelar asignar rol Mantenimiento "

LLAMADA: vistas/admin/vAsignarMantenimientoRolInc.php 
y previamente desde cAdmin.php:asignarMantenimientoRolBuscar()

LLAMA: vistas/admin/formAsignarMantenimientoRol.php
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
    ASIGNAR ROL DE MANTENIMIENTO
</h3>		

<?php require_once './vistas/admin/formAsignarMantenimientoRol.php'; ?>

</div>
<!--****************************** Fin Cuerpo central  ********************-->
</div>
<!--************************ Fin vCuerpoAsignarMantenimientoRol.php ***************-->