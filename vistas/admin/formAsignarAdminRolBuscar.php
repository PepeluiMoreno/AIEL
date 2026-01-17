<?php
/*--------------------------------------------------------------------------------------------------
FICHERO: formAsignarAdminRolBuscar.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION:  Este formulario permite:
- Con el botón "LISTA DE SOCIOS/AS CON ROL Administración" mostrará una tabla con los datos de todos 
  los socios con rol de Administración

- "BUSCAR" los datos de un socio por los siguientes campos del formulario: email o AP1,AP2,NOM
para ver si ya tiene rol de Administración.
Según la situación se podrá después se podrá asignar/eliminar rol de Administración

Tiene tres botones para "Buscar por apellidos", "Buscar por email", y para "Cancelar"

LLAMADA: vistas/admin/vCuerpoAsignarAdminRolBuscar.php
y previamente desde  cAdmin.php:asignarAdministracionRolBuscar()
												
OBSERVACIONES:

--------------------------------------------------------------------------------------------------*/
?>

<div id="registro">	

<br />	
					<div align="center">

				 <form method="post" action="./index.php?controlador=cAdmin&accion=mostrarListaAdministracionRol">
					      <input type="submit" name="mostrarListaAdministracionRol" value="Lista de socios/as con rol Administración">
	    </form>					
			
    </div>
<br />				
	<span class="textoAzul8Left">
 El Administrador/a, pueden asignar o anular el rol de Administración a un socios/a de EL				
<br /><br />	
Primero hay que buscar al socio/a para después hacer la operación de asignar-anular el rol de Administración
	 			<br /><br />
 Se puede buscar por apellidos y nombre, o por email personal del socio/a					
  </span>

 <!-- ********************* Inicio  mensaje error *************** -->
  <br /><br /><br />
			<span class="error">
			 <strong>
			  <?php if (isset($datosFormSocio['errorMensaje'])){echo $datosFormSocio['errorMensaje'];}
					?>
			 </strong>
			</span>
		<br />
	<!-- ********************** Fin mensaje error ******************* -->	
			
	 <div align="left">
		
	<!-- *************** Inicio form búsqueda por APE1, APE2 ************* -->	
		<form method="post" action="./index.php?controlador=cAdmin&amp;accion=asignarAdministracionRolBuscar">
	  <fieldset>
			<legend>Buscar socio/a por apellidos</legend>
			 <p>
					<label>Apellido1</label> 
	    <input type="text"
	           name="datosFormSocio[APE1]"
	           value='<?php if (isset($datosFormSocio['APE1']['valorCampo']))
	           {  echo $datosFormSocio['APE1']['valorCampo'];}
	           ?>'
	           size="60"
	           maxlength="200"
	    />	
					<span class="error">
						<?php
						if (isset($datosFormSocio['APE1']['errorMensaje']))
						{echo $datosFormSocio['APE1']['errorMensaje'];}
						?>
					</span>
			  <br />
					<label>Apellido2</label> 				
	    <input type="text"
	           name="datosFormSocio[APE2]"
	           value='<?php if(isset($datosFormSocio['APE2']['valorCampo']))
	           {  echo $datosFormSocio['APE2']['valorCampo'];}
	           ?>'
	           size="60"
	           maxlength="200"
	    />	
					<span class="error">
						<?php
						if (isset($datosFormSocio['APE2']['errorMensaje']))
						{echo $datosFormSocio['APE2']['errorMensaje'];}
						?>
					</span>
					<br />
					<label>Nombre</label> 				
	    <input type="text"
	           name="datosFormSocio[NOM]"
	           value='<?php if(isset($datosFormSocio['NOM']['valorCampo']))
	           {  echo $datosFormSocio['NOM']['valorCampo'];}
	           ?>'
	           size="60"
	           maxlength="200"
	    />	
					<span class="error">
						<?php
						if (isset($datosFormSocio['NOM']['errorMensaje']))
						{echo $datosFormSocio['NOM']['errorMensaje'];}
						?>
					</span>				
				
   </p>	
			<br /><br />
					<input type="submit" name="BuscarApes" value="Buscar por apellidos">
		<!--			 <input type="submit" name="NoAsignar" value="No asignar Administración" class="enviar"> -->
	 </fieldset>	
 </form>
	<!-- ****************** Fin form búsqueda por APE1,APE2  ************* -->		
	
 <!-- *************** Inicio form búsqueda 'EMAIL' ******************** -->	
		<form method="post" action="./index.php?controlador=cAdmin&amp;accion=asignarAdministracionRolBuscar">
	  <fieldset>
			<legend>Buscar socia/o por email</legend>
			 <p>
					<label>Email</label> 
	    <input type="text"
	           name="datosFormSocio[EMAIL]"
	           value='<?php if (isset($datosFormSocio['EMAIL']['valorCampo']))
	           {  echo $datosFormSocio['EMAIL']['valorCampo'];}
	           ?>'
	           size="100"
	           maxlength="200"
	    />	
					<span class="error">
						<?php
						if (isset($datosFormSocio['EMAIL']['errorMensaje']))
						{echo $datosFormSocio['EMAIL']['errorMensaje'];}
						?>
					</span>
					<br />	
				</p>	
	
				<input type="submit" name="BuscarEmail" value="Buscar por email">
		<!--			 <input type="submit" name="NoAsignar" value="No asignar rol Administración" class="enviar"> -->					
	 </fieldset>	
 </form>					
		<!-- *************** Fin form búsqueda 'EMAIL' ******************* -->	
			<br />
		<!-- ********************* Inicio no buscar *********************** -->			
		<form method="post" action="./index.php?controlador=cAdmin&amp;accion=asignarAdministracionRolBuscar">
			 <input type="submit" name="NoAsignar" value="Cancelar operación rol" class="enviar">					
 </form>		
 	<!-- ********************* Fin no buscar ************************** -->		
					
	</div>

</div>		
