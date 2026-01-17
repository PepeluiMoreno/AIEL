<?php
/*--------------------------------------------------------------------------------------------------
FICHERO: formAsignarAdminRol.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: 
Es el formulario que muestra algunos datos personales de un socio, buscado previamente para asignarle 
rol de Administración 

Tiene unos botones para "Asignación Rol Administración", y para "Cancelar asignar rol Administración"

LLAMADA: vistas/admin/vCuerpoAsignarAdminRol.php
y previamente desde  cAdmin.php:asignarAdministracionRolBuscar(), asignarAdministracionRol()
												
OBSERVACIONES:

--------------------------------------------------------------------------------------------------*/
require_once './modelos/libs/comboLista.php';
?>

<div id="registro">

 <br /><br />

 <div id="formLinea">
	
  <form method="post" class="linea" action="./index.php?controlador=cAdmin&amp;accion=asignarAdministracionRol"	
			     onSubmit="return confirm('¿Asignar rol de Administración?')">			
			
						
	 <!-- ******************* Inicio Datos de SOCIO ******************************** -->
   <input type="hidden"
         	id="codUser"
          name="datosFormSocio[CODUSER]"
          value='<?php if (isset($datSocio['datosFormSocio']['CODUSER']['valorCampo']))
                       {  echo $datSocio['datosFormSocio']['CODUSER']['valorCampo'];}
                 ?>'
   />
 	
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
					<br />		<br />							
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
										size="50"
										maxlength="100"																	
    />				
				
	 	</p>
	 </fieldset>
 	<br /><br />
			<!--************ Inicio Datos de datosFormSocio[OBSERVACIONES]***********-->
			<fieldset>
							<legend><b>Observaciones</b></legend>
							<p>
											<textarea  id='OBSERVACIONES' onKeyPress="limitarTextoArea(250, 'OBSERVACIONES');"	
																						class="textoAzul8Left" name="datosFormSocio[OBSERVACIONES]" rows="3" cols="80"><?php
if (isset($datSocio['datosFormSocio']['OBSERVACIONES']['valorCampo'])) {
echo htmlspecialchars(stripslashes($datSocio['datosFormSocio']['OBSERVACIONES']['valorCampo']));
}
?></textarea> 			 
							</p>
			</fieldset>
	 <br />	
	 <!-- ******************* Fin Datos de identificación Socio ************** --> 	 		
 
	  <input type="submit" name="SiAsignar" value="Asignar rol de Administración" class="enviar">	
  </form>

  <form method="post" class="linea" action="./index.php?controlador=cAdmin&amp;accion=asignarAdministracionRol">		
   <input type="submit" name="NoAsignar" value="Cancelar asignar rol de Administración" class="enviar">
  </form>					
 </div><!--<div id="formLinea">-->
</div><!-- <div id="registro">-->




