<?php
/***********************************************************************************************
FICHERO: vCuerpoMenuPermisosRolesAdmin.php
PROYECTO: EL
VERSION: PHP 7.3.21
								
DESCRIPCION:													

Se forma el menú de asignación-eliminación de permisos de "Roles" a socios/as que se pueden 
hacer desde el rol de "Administración" y que permite llamar a las páginas: 

- ASIGNAR-ANULAR ROL DE ADMINISTRACIÓN
- ASIGNAR-ANULAR ROL DE MANTENIMIENTO

También incluye mostrar las listas de socios/as con estos roles.

Uso exclusivo desde el rol "Administración"													
									
LLAMADA: vistas/admin/vMenuPermisosRolesAdminInc.php y previamente desde cAdmin.php:menuPermisosRolesAdmin()	

LLAMA: vistas/admin/formMenuPermisosRolesAdmin.php e incluye plantillasGrales

Desde ese menú con "href" se podrá llamar a: 
cAdmin.php:asignarAdministracionRolBuscar(),asignarMantenimientoRolBuscar()


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

				MENÚ PARA ASIGNAR-ANULAR-MOSTRAR ROLES DE SOCIOS/AS (ADMINISTRACIÓN y MANTENIMIENTO)
    
</h3>
<br /><br /><br />
<!-- *****************  Inicio Formulario  ************ -->
<?php require_once './vistas/admin/formMenuPermisosRolesAdmin.php';
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

