<?php
/*----------------------------------------------------------------------------------------------------
FICHERO: formAnularSocioPendienteConfirmPres.php
VERSION: PHP 7.3.21

En este formulario se algunos datos personales de un "casi" socio que inició el alta por él mismo 
y aún está "PENDIENTE-CONFIRMACION" su alta por él mismo. 
En el formulario en un botón se pide confirmación para anular el intento de alta del socio. 
También botón "No eliminar", pide segunda confirmación 

LLAMADA: vistas/presidente/vCuerpoAnularSocioPendienteConfirPres.php 
y previamente desde cPresidente.php:anularSocioPendienteConfirmarPres()

OBSERVACIONES: El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario.
-----------------------------------------------------------------------------------------------------*/
?>

<div id="registro">

 <br />	
 <span class="textoAzu112Left2">
	 A continuación se visualizan algunos datos personales del socia/a que vas a eliminar
  </span>
  <br /><br />
 <!-- <div id="formLinea"> -->
	
  <form method="post" class="linea" 
      action="./index.php?controlador=cPresidente&amp;accion=anularSocioPendienteConfirmarPres"
			onSubmit="return confirm('¿Borrar los datos del socio/a pendiente de confirmar alta?')">			
						
	 <!-- ******************* Inicio Datos de SOCIO ******************************** -->
    <input type="hidden"
         	id="codUser"
          name="datosFormSocioPendienteConfirmar[CODUSER]"
          value='<?php if (isset($datSocioPendienteConfirmar['CODUSER']))
                       {  echo $datSocioPendienteConfirmar['CODUSER'];}
                 ?>'
   />
 	 <!-- ******************* Inicio Datos de identificación MIEMBRO ************** --> 
	 <fieldset>	 
	  <legend><b>Datos personales</b></legend>	
		<p>
	   <label>Fecha inicio del registro en Europa Laica</label> 
	    <input type="text" readonly
						      class="mostrar"		
	           name="datosFormSocioPendienteConfirmar[FECHAREGISTRO]"
	           value='<?php if (isset($datSocioPendienteConfirmar['FECHAREGISTRO']))
	           {  echo $datSocioPendienteConfirmar['FECHAREGISTRO'];}
	           ?>'
	           size="10"
	           maxlength="10"
	    />	
	  <br />		<br />									
	   <label>Nombre</label> 
	    <input type="text" readonly
						      class="mostrar"		
	           name="datosFormSocioPendienteConfirmar[NOM]"
	           value='<?php if (isset($datSocioPendienteConfirmar['NOM']))
	           {  echo $datSocioPendienteConfirmar['NOM'];}
	           ?>'
	           size="35"
	           maxlength="100"
	    />	
	  <br />
		<label>Apellido primero</label> 
	    <input type="text" readonly
						 class="mostrar"		
	           name="datosFormSocioPendienteConfirmar[APE1]"
	           value='<?php if (isset($datSocioPendienteConfirmar['APE1']))
	           {  echo $datSocioPendienteConfirmar['APE1'];}
	           ?>'
	           size="35"
	           maxlength="100"
	    />
	
	   <label>Apellido segundo</label> 
	    <input type="text" readonly
						 class="mostrar"		
	           name="datosFormSocioPendienteConfirmar[APE2]"
	           value='<?php if (isset($datSocioPendienteConfirmar['APE2']))
	                 {  echo $datSocioPendienteConfirmar['APE2'];}
	                  ?>'
	           size="35"
	           maxlength="100"
	    />	 
		<br /><br />
	 <label>Fecha de nacimiento</label> 
	    <input type="text" readonly
						 class="mostrar"		
	           name="datosFormSocioPendienteConfirmar[FECHANAC]"
	           value='<?php if (isset($datSocioPendienteConfirmar['FECHANAC']) && $datSocioPendienteConfirmar['FECHANAC'] !== "0000-00-00")
	                 {  echo $datSocioPendienteConfirmar['FECHANAC'];}
	                  ?>'
	           size="10"
	           maxlength="10"
	    />	 
		<br /><br />
			<!-- Los tres siguientes para insertar nº documento en tabla	"MIEMBROELIMINADO5ANIOS" -->

		 <label for="user">Tipo documento</label> 
   <input type="text" readonly
						    class="mostrar"		
         	id="codUser"
          name="datosFormSocioPendienteConfirmar[TIPODOCUMENTOMIEMBRO]"
          value='<?php if (isset($datSocioPendienteConfirmar['TIPODOCUMENTOMIEMBRO']))
                       {  echo $datSocioPendienteConfirmar['TIPODOCUMENTOMIEMBRO'];}
                 ?>'
	         size="10"
	         maxlength="20"							
   />	
			<label for="user">Documento</label> 
   <input type="text" readonly
						    class="mostrar"		
         	id="codUser"
          name="datosFormSocioPendienteConfirmar[NUMDOCUMENTOMIEMBRO]"
          value='<?php if (isset($datSocioPendienteConfirmar['NUMDOCUMENTOMIEMBRO']))
                       {  echo $datSocioPendienteConfirmar['NUMDOCUMENTOMIEMBRO'];}
                 ?>'
          size="12"
          maxlength="20"																	
   />		
  <label for="user">Código País</label> 
  <input type="text" readonly
						   class="mostrar"		
         	id="codUser"
          name="datosFormSocioPendienteConfirmar[CODPAISDOC]"
          value='<?php if (isset($datSocioPendienteConfirmar['CODPAISDOC']))
                       {  echo $datSocioPendienteConfirmar['CODPAISDOC'];}
                 ?>'
         size="3"
         maxlength="4"																	
   />
			<br />	
		<label>Correo electrónico</label>
	    <input type="text" readonly
						 class="mostrar"		
	           name="datosFormSocioPendienteConfirmar[EMAIL]"
	           value='<?php if (isset($datSocioPendienteConfirmar['EMAIL']))
	           {  echo $datSocioPendienteConfirmar['EMAIL'];}
	           ?>'
	           size="60"
	           maxlength="200"
	    />	  
	  <br />			
			
		<label>Teléfono móvil</label>
	    <input type="text" readonly
						 class="mostrar"		
	           name="datosFormSocioPendienteConfirmar[TELMOVIL]"
	           value='<?php if (isset($datSocioPendienteConfirmar['TELMOVIL']))
	           {  echo $datSocioPendienteConfirmar['TELMOVIL'];}
	           ?>'
	           size="20"
	           maxlength="60"
	    />	  
	  <br />
		<label>Teléfono casa</label>
	    <input type="text" readonly
						 class="mostrar"		
	           name="datosFormSocioPendienteConfirmar[TELFIJOCASA]"
	           value='<?php if (isset($datSocioPendienteConfirmar['TELFIJOCASA']))
	           {  echo $datSocioPendienteConfirmar['TELFIJOCASA'];}
	           ?>'
	           size="20"
	           maxlength="60"
	    />	  
	  <br />			
									
		<label>Localidad</label>
	    <input type="text" readonly
						 class="mostrar"		
	           name="datosFormSocioPendienteConfirmar[LOCALIDAD]"
	           value='<?php if (isset($datSocioPendienteConfirmar['LOCALIDAD']))
	           {  echo $datSocioPendienteConfirmar['LOCALIDAD'];}
	           ?>'
	           size="40"
	           maxlength="200"
	    />	  
	  <br />
					<label>Dirección</label>
	    <input type="text" readonly
						 class="mostrar"		
	           name="datosFormSocioPendienteConfirmar[DIRECCION]"
	           value='<?php if (isset($datSocioPendienteConfirmar['DIRECCION']))
	           {  echo $datSocioPendienteConfirmar['DIRECCION'];}
	           ?>'
	           size="60"
	           maxlength="200"
	    />	  
	  <br />			
			<label>Tipo cuota</label>
	    <input type="text" readonly
						 class="mostrar"		
	           name="datosFormSocioPendienteConfirmar[CODCUOTA]"
	           value='<?php if (isset($datSocioPendienteConfirmar['CODCUOTA']))
	           {  echo $datSocioPendienteConfirmar['CODCUOTA'];}
	           ?>'
	           size="20"
	           maxlength="60"
	    />	 
					
		</p>
	 </fieldset>
	 <br />	
		
	 <fieldset> 
	  <legend><b>Comentario del socia/o</b></legend>
	 <p>						 			
		<textarea  
		class="mostrar" readonly name="datosFormSocioPendienteConfirmar[COMENTARIOSOCIO]" rows="4" cols="80"><?php 
		  if (isset($datSocioPendienteConfirmar['COMENTARIOSOCIO']))                    
			{echo htmlspecialchars(stripslashes($datSocioPendienteConfirmar['COMENTARIOSOCIO']));}
		?></textarea> 
	  <br />			
									
			<!--------------------------------------------------------------------------------------->						
					
		</p>
	 </fieldset>
	 <br />	
	 <!-- ******************* Fin Datos de identificación MIEMBRO ************** --> 	 
					

	 <!-- ******************* Fin Datos de SOCIO ******************************** -->		
	  <input type="submit" name="SiEliminar" value="Eliminar datos socio/a pendiente confirmar" class="enviar">	
  </form>

  <form method="post" class="linea"
      action="./index.php?controlador=cPresidente&amp;accion=anularSocioPendienteConfirmarPres">		
   <input type="submit" name="NoEliminar" value="No eliminar datos socio/a pendiente confirmar" class="enviar">
  </form>					
 <!-- </div><!--<div id="formLinea">-->
</div><!-- <div id="registro">-->




