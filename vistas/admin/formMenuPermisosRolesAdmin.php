<?php
/*--------------------------------------------------------------------------------------------------
FICHERO: formMenuPermisosRolesAdmin.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION:													
	
Se forma el menú de asignación-eliminación de permisos de "Roles" a socios/as que se pueden 
hacer desde el rol de "Administración" y que permite llamar a las páginas: 

- ASIGNAR-ANULAR ROL DE ADMINISTRACIÓN
- ASIGNAR-ANULAR ROL DE MANTENIMIENTO

También incluye mostrar las listas de socios/as con estos roles.

Uso exclusivo desde el rol "Administración"													
									
LLAMADA: vistas/admin/vCuerpoMenuPermisosRolesAdmin.php y previamente desde cAdmin.php:menuPermisosRolesAdmin()	

Desde ese menú con "href" se podrá llamar a: 
cAdmin.php:asignarAdministracionRolBuscar(),asignarMantenimientoRolBuscar()

OBSERVACIONES:

--------------------------------------------------------------------------------------------------*/
?>
<div id="registro">

  <a href="./index.php?controlador=cAdmin&amp;accion=asignarAdministracionRolBuscar" 
      title="Asignar-Anular a un socio/a el rol de  Administración y Lista de socios/as con rol de  Administración">
							<strong>>> ROL DE ADMINISTRACIÓN: Asignar-Anular el rol de Administración a un socio/a y mostrar lista de socios/as con rol de Administración</strong>
							</a>

	 <br /><br /><br /><br />	

  <a href="./index.php?controlador=cAdmin&amp;accion=asignarMantenimientoRolBuscar" 
      title="Asignar-Anular a un socio/a el rol de Mantenimiento y Lista de socios/as con rol de Mantenimiento">
							<strong>>> ROL DE MANTENIMIENTO: Asignar-Anular el rol de Mantenimiento a un socio/a y mostrar lista de socios/as con rol de Mantenimiento</strong>							
							</a>

	 <br /><br /><br /><br />			
	
							
			<?php 
			if (isset($botonSubmit['enlaceBoton'])&&($botonSubmit['enlaceBoton']!=='')&&
			($botonSubmit['enlaceBoton']!==NULL))
   {
	    echo	"<form method='post' action=./".$botonSubmit['enlaceBoton'].">".
				      " <input type='submit' value=".$botonSubmit['textoBoton'].">";
		   echo " </form>";
			}	
			?>
			 <br /><br />			
	</div>  				