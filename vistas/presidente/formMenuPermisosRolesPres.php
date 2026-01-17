<?php
/*--------------------------------------------------------------------------------------------------
FICHERO: formMenuPermisosRolesPres.php
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
													
LLAMADA: vistas/presidente/vCuerpoMenuPermisosRolesPresInc.php y previamente desde cPresidente.php:menuPermisosRolesPres()						

LLAMA: vistas/presidente/formMenuPermisosRolesPres.php e incluye plantillasGrales

Desde ese menú con "href" se podrá llamar a: 
cPresidente.php:asignarCoordinacionAreaBuscar(),asignarPresidenciaRolBuscar(),asignarTesoreriaRolBuscar()

OBSERVACIONES:

--------------------------------------------------------------------------------------------------*/
?>
<div id="registro">
<!--
  <a href="./index.php?controlador=cPresidente&amp;accion=mostrar....." 
      title="mostrar....">
						 <strong>>> Mostrar lista de socia/os .....</strong></a>
							<br /><br />
							En esta lista se muestran las socias y socios .....

	 	<br /><br /><br /><br />	
-->
  <a href="./index.php?controlador=cPresidente&amp;accion=asignarCoordinacionAreaBuscar" 
      title="Asignar-Modificar-Anular a un socio/a el rol de Cordinador/a sobre agrupaciones territoriales y Lista de Coordinadores/as ">
								<strong>>> ROL DE COORDINACIÓN: Asignar-Anular el rol de Coordinación a un socio/a y mostrar lista de socios/as con rol de Coordinación </strong>
							</a>
 <br /><br /><br /><br />	
		
  <a href="./index.php?controlador=cPresidente&amp;accion=asignarPresidenciaRolBuscar" 
      title="Asignar-Anular a un socio/a el rol de Presidencia y Lista de socios/as con rol de Presidencia">
							<strong>>> ROL DE PRESIDENCIA (Presidencia, Vice, Secretaría):Asignar-Anular el rol de Presidencia a un socio/a y mostrar lista de socios/as con rol de Presidencia </strong>
							</a>

	 <br /><br /><br /><br />	

  <a href="./index.php?controlador=cPresidente&amp;accion=asignarTesoreriaRolBuscar" 
      title="Asignar-Anular a un socio/a el rol de Tesorería y Lista de socios/as con rol de Tesorería">
							<strong>>> ROL DE TESORERÍA: Asignar-Anular el rol de Tesorería a un socio/a y mostrar lista de socios/as con rol de Tesorería </strong>							
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