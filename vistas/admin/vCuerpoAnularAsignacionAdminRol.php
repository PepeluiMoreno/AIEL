<?php
/******************* Inicio vCuerpovAnularAsignacionAdminRol.php ****************************************
FICHERO: vCuerpoAnularAsignacionAdminRol.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Es el formulario que muestra algunos datos personales, buscados previamente, de un socio que tiene
el rol de Administración asignado.

Mediante dos botones:  "Eliminar asignación rol Administración", y para "Cancelar" 
se puede retirarle el rol de Administración asignado.

LLAMADA: vistas/admin/vAnularAsignacionAdminRolInc.php
y previamente desde cAdmin.php:asignarAdministracionRolBuscar()

LLAMA: vistas/admin/formAnularAsignacionAdminRol.php
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
    ELIMINAR ASIGNACIÓN DEL ROL DE ADMINISTRACIÓN	DE UN SOCIO/A 
</h3>		

<?php require_once './vistas/admin/formAnularAsignacionAdminRol.php'; ?> 

</div>
<!--****************************** Fin Cuerpo central  ********************-->
</div>
<!--************************ Fin vCuerpoAnularAsignacionAdminRol.php ***************-->