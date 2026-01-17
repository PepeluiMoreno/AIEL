<?php
/******************* Inicio vCuerpoAsignarPresiRol.php *******************************************
FICHERO: vCuerpoAsignarPresi.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Es el formulario que muestra algunos datos personales de un socio, buscado previamente para asignarle 
rol de Presidencia que lo comparten (presidente/a, vice. y el secretario/a)

Tiene unos botones para "Asignación Rol Presidencia", y para "Cancelar asignar rol Presidencia"

LLAMADA: vistas/presidente/vAsignarPresiRolInc.php 
y previamente desde cPresidente.php:asignarPresidenciaRolBuscar()

LLAMA: vistas/presidente/formAsignarPresiRol.php
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
    ASIGNAR ROL DE PRESIDENCIA (Presidencia, Vice, Secretaría) 
</h3>		

<?php require_once './vistas/presidente/formAsignarPresiRol.php'; ?>

</div>
<!--****************************** Fin Cuerpo central  ********************-->
</div>
<!--************************ Fin vCuerpoAsignarPresiRol.php ***************-->