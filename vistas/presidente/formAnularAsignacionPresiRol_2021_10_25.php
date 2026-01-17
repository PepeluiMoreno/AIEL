<?php
/*----------------------------------------------------------------------------------------------------														
FICHERO: formAnularAsignacionPresiRol.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Es el formulario que muestra algunos datos personales, buscados previamente, de un socio que tiene
el rol de Presidencia (Presidencia, Vice, Secretaría) asignado.

Mediante dos botones:  "Eliminar asignación rol Presidencia ", y para "Cancelar" 
se puede retirarle el rol de Presidencia asignado.

LLAMADA: vistas/presidente/vCuerpovAnularAsignacionPresiRol.php
y previamente desde cPresidente.php:asignarPresidenciaRolBuscar()

LLAMA: cPresidente.php:eliminarAsignacionPresidenciaRol()

OBSERVACIONES:											
-----------------------------------------------------------------------------------------------------*/
?>
<div id="registro">

 <br />	
 <span class="textoAzu112Left2">
	  Este socio/a tiene asignado el rol de Presidencia (presidente/a, vice. y secretario/a)
  </span>
  <br /> <br />
		
 <div id="formLinea">
		
		<!-- ******************* Inicio Form para eliminar rol de Presidencia ************* -->

  <form method="post" class="linea" action="./index.php?controlador=cPresidente&amp;accion=eliminarAsignacionPresidenciaRol"
	       onSubmit="return confirm('¿Eliminar asignación del rol Presidencia?')">							
						
	    <input type="hidden"
         	id="codUser"
          name="datosFormSocio[CODUSER]"
          value='<?php if (isset($datSocio['datosFormSocio']['CODUSER']['valorCampo']))
                       {  echo $datSocio['datosFormSocio']['CODUSER']['valorCampo'];}
                 ?>'
    />
						
 	 <!-- ******************* Inicio Datos de identificación Socio ************** --> 
	 <fieldset>	 
	  <legend><b>Datos personales</b></legend>	
		<p>
	   <label>Estado socio/a (alta, baja, ...)</label> 
	    <input type="text" readonly
											 class="mostrar"		
	           name="datosFormSocio[ESTADO]"
	           value='<?php if (isset($datSocio['datosFormSocio']['ESTADO']['valorCampo']))
	           {  echo $datSocio['datosFormSocio']['ESTADO']['valorCampo'];}
	           ?>'
	           size="35"
	           maxlength="100"
	    />
					<br />		
				
	   <label>Nombre</label> 
	    <input type="text" readonly
						      class="mostrar"		
	           name="datosFormSocio[NOM]"
	           value='<?php if (isset($datSocio['datosFormSocio']['NOM']['valorCampo']))
	           {  echo $datSocio['datosFormSocio']['NOM']['valorCampo'];}
	           ?>'
	           size="35"
	           maxlength="100"
	    />	
	  <br />
		 <label>Apellido primero</label> 
	    <input type="text" readonly
						      class="mostrar"		
	           name="datosFormSocio[APE1]"
	           value='<?php if (isset($datSocio['datosFormSocio']['APE1']['valorCampo']))
	           {  echo $datSocio['datosFormSocio']['APE1']['valorCampo'];}
	           ?>'
	           size="35"
	           maxlength="100"
	    />
	
	   <label>Apellido segundo</label> 
	    <input type="text" readonly
						      class="mostrar"		
	           name="datosFormSocio[APE2]"
	           value='<?php if (isset($datSocio['datosFormSocio']['APE2']['valorCampo']))
	                 {  echo $datSocio['datosFormSocio']['APE2']['valorCampo'];}
	                  ?>'
	           size="35"
	           maxlength="100"
	    />	 
		<br /><br />
		<label>Correo electrónico</label>
	    <input type="text" readonly
						      class="mostrar"		
	           name="datosFormSocio[EMAIL]"
	           value='<?php if (isset($datSocio['datosFormSocio']['EMAIL']['valorCampo']))
	           {  echo $datSocio['datosFormSocio']['EMAIL']['valorCampo'];}
	           ?>'
	           size="60"
	           maxlength="200"
	    />	  
	  <br />	
		 <label for="user">Tipo documento</label> 
   <input type="text" readonly
						    class="mostrar"		
         	id="codUser"
          name="datosFormSocio[TIPODOCUMENTOMIEMBRO]"
          value='<?php if (isset($datSocio['datosFormSocio']['TIPODOCUMENTOMIEMBRO']['valorCampo']))
                       {  echo $datSocio['datosFormSocio']['TIPODOCUMENTOMIEMBRO']['valorCampo'];}
                 ?>'
	         size="10"
	         maxlength="20"							
   />	
			<label for="user">Documento</label> 
   <input type="text" readonly
						    class="mostrar"		
         	id="codUser"
          name="datosFormSocio[NUMDOCUMENTOMIEMBRO]"
          value='<?php if (isset($datSocio['datosFormSocio']['NUMDOCUMENTOMIEMBRO']['valorCampo']))
                       {  echo $datSocio['datosFormSocio']['NUMDOCUMENTOMIEMBRO']['valorCampo'];}
                 ?>'
          size="12"
          maxlength="20"																	
   />		
  <label for="user">Código País</label> 
  <input type="text" readonly
						   class="mostrar"		
									id="codUser"
									name="datosFormSocio[CODPAISDOC]"
									value='<?php if (isset($datSocio['datosFormSocio']['CODPAISDOC']['valorCampo']))
																						{  echo $datSocio['datosFormSocio']['CODPAISDOC']['valorCampo'];}
																?>'
								size="3"
								maxlength="4"																	
   />
			<br />
			<label for="user">Localidad</label> 
   <input type="text" readonly
						    class="mostrar"		
         	id="codUser"
          name="datosFormSocio[LOCALIDAD]"
          value='<?php if (isset($datSocio['datosFormSocio']['LOCALIDAD']['valorCampo']))
                       {  echo $datSocio['datosFormSocio']['LOCALIDAD']['valorCampo'];}
                 ?>'
          size="60"
          maxlength="120"																	
   />		
			<br />
					<label>Observaciones</label> 				
	    <input type="text" readonly
						      class="mostrar"
	           name="datosFormSocio[OBSERVACIONES]"
	           value='<?php if(isset($datSocio['datosFormSocio']['OBSERVACIONES']))
	           {  echo $datSocio['datosFormSocio']['OBSERVACIONES'];}
	           ?>'
	           size="60"
	           maxlength="250"
	    />			
						
		</p>
	 </fieldset>
	 <br />	
	 <!-- ******************* Fin Datos de identificación Socio ************** --> 	 

  <input type="submit" name="SiEliminar" value="Eliminar asignación de rol Presidencia" class="enviar">				

 </form>
	<!-- ******************* Fin Form para eliminar rol de Presidencia ****************** -->


<!-- ******************* Inicio Form botón para cancelar ************************** -->	
	
		<form method="post" class="linea"  action="./index.php?controlador=cPresidente&amp;accion=eliminarAsignacionPresidenciaRol">		
   
			<input type="submit" name="Cancelar" value="Cancelar operación eliminar rol" class="enviar">
  </form>	
<!-- ******************* Fin Form botón para cancelar ******************************* -->					
 </div><!--<div id="formLinea">-->
</div><!-- <div id="registro">-->




