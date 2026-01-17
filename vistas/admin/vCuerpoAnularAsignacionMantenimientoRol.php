<?php
/******************* Inicio vCuerpovAnularAsignacionMantenimientoRol.php ****************************************
FICHERO: vCuerpoAnularAsignacionMantenimientoRol.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Es el formulario que muestra algunos datos personales, buscados previamente, de un socio que tiene
el rol de Mantenimiento asignado.

Mediante dos botones:  "Eliminar asignación rol Mantenimiento", y para "Cancelar" 
se puede retirarle el rol de Mantenimiento asignado.

LLAMADA: vistas/admin/vAnularAsignacionMantenimientoRolInc.php
y previamente desde cAdmin.php:asignarMantenimientoRolBuscar()

LLAMA: vistas/admin/formAnularAsignacionMantenimientoRol.php
e incluye plantillasGrales

OBSERVACIONES: El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario.
*****************************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';

/************************* Inicio Cuerpo central **************************/

if (isset($navegacion['cadNavegEnlaces'])) 
{
    echo $navegacion['cadNavegEnlaces'];
}
?>	

<br /><br />		

<h3 align="center">
    ELIMINAR ASIGNACIÓN DEL ROL DE MANTENIMIENTO	DE UN SOCIO/A 
</h3>		

<?php require_once './vistas/admin/formAnularAsignacionMantenimientoRol.php'; ?> 

</div>
<!--****************************** Fin Cuerpo central  ********************-->
</div>
<!--************************ Fin vCuerpoAnularAsignacionMantenimientoRol.php ***************-->