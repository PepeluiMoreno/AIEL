<?php
/*--------------------------------------------------------------------------------------------------
FICHERO: formAsignarCoordAreaBuscar.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION:  Este formulario permite:
- Con el botón "LISTA DE COORDINADORES/AS" mostrará una tabla con los datos de todos los coordinadores

- "BUSCAR" los datos de un socio por los siguientes campos del formulario: email o AP1,AP2,NOM
para ver si ya tiene rol de cooordinador.
Según la situación se podrá después se podrá asignar/modificar/eliminar un área de coordinación

Tiene tres botones para "Buscar por apellidos", "Buscar por email", y para "Cancelar"

LLAMADA: vistas/presidente/vCuerpoAsignarCoordAreaBuscarInc.php
y previamente desde  cPresidente.php: asignarCoordinacionAreaBuscar()
												
OBSERVACIONES:

--------------------------------------------------------------------------------------------------*/
?>

<div id="registro">	

<br />	
					<div align="center">
				 <form method="post" action="./index.php?controlador=cPresidente&accion=mostrarListaCoordinadores">
					      <input type="submit" name="mostrarListaCoordinadores" value="Lista de coordinadores/as">
	    </form>
    </div>
<br />				
	<span class="textoAzul8Left">
		El presidente/a, vice. y secretario/a, pueden asignar el rol de "Coordinación" sobre un Área de Coordinación.
		<br /><br />	<br />
		Podrá ser sobre:
		<br /><br />
 	- Un Área de Coordinación con ámbito de Comunidad Autónoma (CCAA), que incluye a todas las agrupaciones (provincias) de esa CCAA
			<br /><br />
 	- Un Área de Coordinación con ámbito de una provincia (o CCAA uniprovincial), que solo incluye a la agrupación correspondiente a esa provincia 		
 	<br /><br /><br />
	 .Primero hay que buscar al socio/a para después hacer la operación de asignar-modificar-anular el rol de "Coordinación"
						<br /><br />
		.Se puede buscar por apellidos y nombre, o por email del socio/a					
  </span>

 <!-- ********************* Inicio  mensaje error *************** -->
  <br /><br />
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
		 <form method="post" action="./index.php?controlador=cPresidente&amp;accion=asignarCoordinacionAreaBuscar">
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
		<!--			 <input type="submit" name="NoAsignar" value="No asignar coordinación" class="enviar"> -->
	 </fieldset>	
 </form>
	<!-- ****************** Fin form búsqueda por APE1,APE2  ************* -->		
	<br />
 <!-- *************** Inicio form búsqueda 'EMAIL' ******************** -->	
		<form method="post" action="./index.php?controlador=cPresidente&amp;accion=asignarCoordinacionAreaBuscar">
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
		<!--			 <input type="submit" name="NoAsignar" value="No asignar coordinación" class="enviar"> -->					
	 </fieldset>	
 </form>					
		<!-- *************** Fin form búsqueda 'EMAIL' ******************* -->	
			<br />
		<!-- ********************* Inicio no buscar *********************** -->			
		<form method="post" action="./index.php?controlador=cPresidente&amp;accion=asignarCoordinacionAreaBuscar">
			 <input type="submit" name="NoAsignar" value="No asignar coordinación" class="enviar">					
 </form>		
 	<!-- ********************* Fin no buscar ************************** -->		
					
	</div>

</div>		
