<?php
/******************* Inicio vCuerpoAsignarCoordAreaBuscarInc.php *************************************
FICHERO: vCuerpoAsignarCoordAreaBuscarInc.php
VERSION: PHP 7.3.21

DESCRIPCION:  Este formulario permite:
- Con el botón "LISTA DE COORDINADORES/AS" mostrará una tabla con los datos de todos los coordinadores

- "BUSCAR" los datos de un socio por los siguientes campos del formulario: email o AP1,AP2,NOM
para ver si ya tiene rol de cooordinador.
Según la situación se podrá después se podrá asignar/modificar/eliminar un área de coordinación

Tiene tres botones para "Buscar por apellidos", "Buscar por email", y para "Cancelar"

LLAMADA: vistas/presidente/vAsignarCoordAreaBuscar.php 
y previamente desde cPresidente.php: asignarCoordinacionAreaBuscar()

LLAMA: vistas/presidente/formAsignarCoordAreaBuscar.php
e incluye plantillasGrales

OBSERVACIONES: El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario.
*****************************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';
		
/************************** Inicio Cuerpo central **************************/

echo $navegacion['cadNavegEnlaces'];

?>
<br /><br />
<h3 align="center">
    ASIGNAR-MODIFICAR-ANULAR  ROL DE COORDINADOR/A  A  AGRUPACIONES  TERRITORIALES
</h3>		 

<?php
require_once './vistas/presidente/formAsignarCoordAreaBuscar.php';

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
<!-- ******************************* Fin vCuerpoAsignarCoordAreaBuscarInc.php ************* -->