<?php
/******************* Inicio vCuerpovAnularAsignacionTesoreriaRol.php ****************************************
FICHERO: vCuerpoAnularAsignacionTesoreriaRol.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Es el formulario que muestra algunos datos personales, buscados previamente, de un socio que tiene
el rol de Tesorería asignado.

Mediante dos botones:  "Eliminar asignación rol Tesoreria", y para "Cancelar" 
se puede retirarle el rol de Tesoreria asignado.

LLAMADA: vistas/presidente/vAnularAsignacionTesoreriaRolInc.php
y previamente desde cPresidente.php:asignarTesoreriaRolBuscar()

LLAMA: vistas/presidente/formAnularAsignacionTesoreriaRol.php
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
    ELIMINAR ASIGNACIÓN DEL ROL DE TESORERÍA	DE UN SOCIO/A
</h3>		

<?php require_once './vistas/presidente/formAnularAsignacionTesoreriaRol.php'; ?> 

</div>
<!--****************************** Fin Cuerpo central  ********************-->
</div>
<!--************************ Fin vCuerpoAnularAsignacionTesoreriaRol.php ***************-->