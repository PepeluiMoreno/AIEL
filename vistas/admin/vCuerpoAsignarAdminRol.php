<?php
/******************* Inicio vCuerpoAsignarAdminRol.php *******************************************
FICHERO: vCuerpoAsignarAdminRol.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Es el formulario que muestra algunos datos personales de un socio, buscado previamente para asignarle 
rol de Administración 

Tiene unos botones para "Asignación Rol Administración ", y para "Cancelar asignar rol Administración "

LLAMADA: vistas/admin/vAsignarAdminRolInc.php 
y previamente desde cAdmin.php:asignarAdministracionRolBuscar()

LLAMA: vistas/admin/formAsignarAdminRol.php
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
    ASIGNAR ROL DE ADMINISTRACIÓN
</h3>		

<?php require_once './vistas/admin/formAsignarAdminRol.php'; ?>

</div>
<!--****************************** Fin Cuerpo central  ********************-->
</div>
<!--************************ Fin vCuerpoAsignarAdminRol.php ***************-->