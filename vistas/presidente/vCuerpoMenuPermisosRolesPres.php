<?php
/***********************************************************************************************
FICHERO: vCuerpoMenuPermisosRolesPres.php
PROYECTO: EL
VERSION: PHP 7.3.21
								
DESCRIPCION:													
Se forma el menú de asignación-eliminación de permisos de "Roles" a socios/as que se pueden 
hacer desde el rol de "Presidencia" y que permite llamar a las páginas: 

- ASIGNAR-MODIFICAR-ANULAR ROL DE COORDINADOR/A A AGRUPACIONES TERRITORIALES 
- ASIGNAR-ANULAR ROL DE PRESIDENCIA (Presidencia, Vice, Secretaría) 
- ASIGNAR-ANULAR ROL DE TESORERÍA

También incluye mostra las listas de socios/as con estos roles.

Uso exclusivo desde el rol "Presidencia"
													
LLAMADA: vistas/presidente/vMenuPermisosRolesPresInc.php y previamente desde cPresidente.php:menuPermisosRolesPres()						

LLAMA: vistas/presidente/formMenuPermisosRolesPres.php e incluye plantillasGrales

Desde ese menú con "href" se podrá llamar a: 
cPresidente.php:asignarCoordinacionAreaBuscar(),asignarPresidenciaRolBuscar(),asignarTesoreriaRolBuscar()


OBSERVACIONES: 
**********************************************************************************************/

/************************** Inicio Cuerpo  *******************************/

require_once './vistas/plantillasGrales/vContent.php';

/*********************** Inicio Cuerpo central **************************/

if (isset($navegacion['cadNavegEnlaces'])) 
{
    echo $navegacion['cadNavegEnlaces'];
}
?>	
<br /><br />
<h3 align="center">
<br />

				MENÚ PARA ASIGNAR-MODIFICAR-ANULAR-MOSTRAR ROLES DE SOCIOS/AS  (COORDINACIÓN, PRESIDENCIA, TESORERÍA)
    
</h3>
<br /><br /><br />
<!-- *****************  Inicio Formulario  ************ -->
<?php require_once './vistas/presidente/formMenuPermisosRolesPres.php';
?>
<!-- ******************  Fin Formulario   ************* -->
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
<br /><br />
<!-- ************************** Fin Cuerpo central  ************************ -->	
</div>
<!-- ******************************** Fin Cuerpo  ****************************** -->

