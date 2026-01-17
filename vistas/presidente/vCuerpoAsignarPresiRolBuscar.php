<?php
/******************* Inicio vCuerpoAsignarPresiRolBuscar.php *************************************
FICHERO: vCuerpoAsignarPresiRolBuscar.php
VERSION: PHP 7.3.21

DESCRIPCION:  Este formulario permite:
- Con el botón "LISTA DE SOCIOS/AS CON ROL PRESIDENCIA" mostrará una tabla con los datos de todos 
  los socios con rol de Presidencia

- "BUSCAR" los datos de un socio por los siguientes campos del formulario: email o AP1,AP2,NOM
para ver si ya tiene rol de Presidencia.
Según la situación se podrá después se podrá asignar/eliminar rol de Presidencia a un socio

Tiene tres botones para "Buscar por apellidos", "Buscar por email", y para "Cancelar"

LLAMADA: vistas/presidente/vAsignarPresiRolBuscarInc.php 
y previamente desde cPresidente.php:asignarPresidenciaRolBuscar()

LLAMA: vistas/presidente/formAsignarPresiRolBuscar.php
e incluye plantillasGrales

OBSERVACIONES: El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario.
*****************************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';
		
/************************** Inicio Cuerpo central **************************/

echo $navegacion['cadNavegEnlaces'];

?>
<br /><br />
<h3 align="center">
    ASIGNAR-ANULAR  ROL DE PRESIDENCIA (Presidencia, Vice, Secretaría) 
</h3>		 

<?php
require_once './vistas/presidente/formAsignarPresiRolBuscar.php';

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
<!-- ******************************* Fin vCuerpoAsignarPresiRolBuscarInc.php ************* -->