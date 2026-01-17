<?php
/******************* Inicio vCuerpoAsignarMantenimientoRolBuscar.php *************************************
FICHERO: vCuerpoAsignarMantenimientoRolBuscar.php
VERSION: PHP 7.3.21

DESCRIPCION:  Este formulario permite:
- Con el botón "LISTA DE SOCIOS/AS CON ROL Mantenimiento" mostrará una tabla con los datos de todos 
  los socios con rol de Mantenimiento

- "BUSCAR" los datos de un socio por los siguientes campos del formulario: email o AP1,AP2,NOM
para ver si ya tiene rol de Mantenimiento.
Según la situación se podrá después se podrá asignar/eliminar rol de Mantenimiento a un socio

Tiene tres botones para "Buscar por apellidos", "Buscar por email", y para "Cancelar"

LLAMADA: vistas/admin/vAsignarMantenimientoRolBuscarInc.php 
y previamente desde cAdmin.php:asignarMantenimientoRolBuscar()

LLAMA: vistas/admin/formAsignarMantenimientoRolBuscar.php
e incluye plantillasGrales

OBSERVACIONES: El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario.
*****************************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';
		
/************************** Inicio Cuerpo central **************************/

echo $navegacion['cadNavegEnlaces'];

?>
<br /><br />
<h3 align="center">
    ASIGNAR-ANULAR  ROL DE MANTENIMIENTO
</h3>		 

<?php
require_once './vistas/admin/formAsignarMantenimientoRolBuscar.php';

?>
<!-- ********************  Inicio form botón anterior******************** --> 		

<div align="center">	
    <br />		
    <?php
    if (isset($navegacion['anterior'])) 
				{
        echo $navegacion['anterior'];
    }
    ?>	
    <br />			
</div 
<!-- ********************  Fin Form botón anterior *********************** --> 

</div>
<!-- ****************************** Fin Cuerpo central  ******************** -->
</div>
<!-- ******************************* Fin vCuerpoAsignarMantenimientoRolBuscarInc.php ************* -->