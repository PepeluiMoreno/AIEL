<?php
/******************* Inicio vCuerpovAnularAsignacionPresiRol.php ****************************************
FICHERO: vCuerpoAnularAsignacionPresiRol.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Es el formulario que muestra algunos datos personales, buscados previamente, de un socio que tiene
el rol de Presidencia (Presidencia, Vice, Secretaría) asignado.

Mediante dos botones:  "Eliminar asignación rol Presidencia", y para "Cancelar" 
se puede retirarle el rol de Presidencia asignado.

LLAMADA: vistas/presidente/vAnularAsignacionPresiRolInc.php
y previamente desde cPresidente.php:asignarPresidenciaRolBuscar()

LLAMA: vistas/presidente/formAnularAsignacionPresiRol.php
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
    ELIMINAR ASIGNACIÓN DEL ROL DE PRESIDENCIA	DE UN SOCIO/A (Presidencia, Vice, Secretaría) 
</h3>		

<?php require_once './vistas/presidente/formAnularAsignacionPresiRol.php'; ?> 

</div>
<!--****************************** Fin Cuerpo central  ********************-->
</div>
<!--************************ Fin vCuerpoAnularAsignacionPresiRol.php ***************-->