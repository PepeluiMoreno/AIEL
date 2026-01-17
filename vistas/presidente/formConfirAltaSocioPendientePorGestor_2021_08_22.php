<?php
/*----------------------------------------------------------------------------------------------------
FICHERO: formConfirAltaSocioPendientePorGestor.php
VERSION: PHP 7.3.21

DESCRIPCION: 
En esta función se confirma el alta de un socio (aún "PENDIENTE-CONFIRMACION" su alta por el mismo),
por un gestor autorizado (presidencia,vice,secretaría,teorería), normalmente después de contacto 
con el socio y este le solicita a un gestor que le confirme el alta (email, teléfono, etc.) 
y entonces el gestor le confirma el alta ocn esta función.

Al confirmar el alta, se insertarán en todas las tablas que correspondan los datos del socio, 
se eliminan físicamente de SOCIOSCONFIRMAR para que no salga también duplicado en mostrar pendientes,
y otras posibles búsquedas y USUARIO.ESTADO ='alta'
 
Se enviará un email al socio y también a secretaria, tesoreria, coordinador y presidencia para comunicar el alta.

LLAMADA: vistas/presidente/vCuerpoConfirAltaSocioPendientePorGestor.php
y previamente desde cPresidente.php:confirmarAltaSocioPendientePorGestor()

OBSERVACIONES: 
-----------------------------------------------------------------------------------------------------*/
?>

<div id="registro">
 <br />	
	
 <span class="textoAzu112Left2">
	 El socio/a no ha completado el segundo paso del proceso de alta: no ha confirmado su alta mediante el email enviado para ello.
		<br /><br />
		Es posible que no haya podido ver el email debido al filtro antiespam de su programa de correo, más habitual con hotmail.
		<br /><br /> 
		Ahora al confirmar un gestor/a su alta en EL, ya figurará como socia/o confirmada/o y a la vez volverá a recibir otro email con su "usuario/o" con un enlace para elegir contraseña.
		<br />
  Mientras no haga clic en ese enlace y confirme su email y elija su contraseña, no podrá entrar en la aplicación de "Gestión de Soci@s"	ni podrá 
		recibir información en "Recuperar usuario/a y contraseña".
		<br /><br />
		Es aconsejable ponerse en contacto con la socia/o por teléfono para confirmar que sí ha leído ese email de confirmación de alta.
		<br /><br />
	 A continuación se visualizan algunos datos del socio/a antes de que decidas confirmar su alta.
	</span>
  <br /><br />
		
<!-- <div id="formLinea">-->
	
  <form method="post" class="linea" 
      action="./index.php?controlador=cPresidente&amp;accion=confirmarAltaSocioPendientePorGestor"
			onSubmit="return confirm('¿Confirmar alta socio/a pendiente de confirmar?')">			
						
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
	  <legend><b>Datos informativos </b></legend>	
		<p>
	   <label>Fecha inicio por el socio/a del registro en Europa Laica</label> 
	    <input type="text" readonly
						      class="mostrar"		
	           name="datosFormSocioPendienteConfirmar[FECHAREGISTRO]"
	           value='<?php if (isset($datSocioPendienteConfirmar['FECHAREGISTRO']))
	           {  echo $datSocioPendienteConfirmar['FECHAREGISTRO'];}
	           ?>'
	           size="10"
	           maxlength="10"
	    />	
			<br />		
			<label>Número emails enviados para petición confirmación alta</label> 
	    <input type="text" readonly
						      class="mostrar"		
	           name="datosFormSocioPendienteConfirmar[NUMENVIOS]"
	           value='<?php if (isset($datSocioPendienteConfirmar['NUMENVIOS']))
	           {  echo $datSocioPendienteConfirmar['NUMENVIOS'];}
	           ?>'
	           size="3"
	           maxlength="10"
	    />	
			<label>Fecha envío de último email</label> 
	    <input type="text" readonly
						      class="mostrar"		
	           name="datosFormSocioPendienteConfirmar[FECHAENVIOEMAILULTIMO]"
	           value='<?php if (isset($datSocioPendienteConfirmar['FECHAENVIOEMAILULTIMO']))
	           {  echo $datSocioPendienteConfirmar['FECHAENVIOEMAILULTIMO'];}
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
		<br />
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
	  <br /><br />
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
				<label>Importe cuota</label>
	    <input type="text" readonly
						 class="mostrar"		
	           name="datosFormSocioPendienteConfirmar[IMPORTECUOTAANIOSOCIO]"
	           value='<?php if (isset($datSocioPendienteConfirmar['IMPORTECUOTAANIOSOCIO']))
	           {  echo $datSocioPendienteConfirmar['IMPORTECUOTAANIOSOCIO'];}
	           ?>'
	           size="20"
	           maxlength="60"
	    />	  
	  <br />
					<label>Modo pago de la cuota</label>
	    <input type="text" readonly
						 class="mostrar"		
	           name="datosFormSocioPendienteConfirmar[MODOINGRESO]"
	           value='<?php if (isset($datSocioPendienteConfirmar['MODOINGRESO']))
	           {  echo $datSocioPendienteConfirmar['MODOINGRESO'];}
	           ?>'
	           size="20"
	           maxlength="60"
	    />	
					<label>IBAN</label>
	    <input type="text" readonly
						 class="mostrar"		
	           name="datosFormSocioPendienteConfirmar[CUENTAIBAN]"
	           value='<?php if (isset($datSocioPendienteConfirmar['CUENTAIBAN']))
	           {  echo $datSocioPendienteConfirmar['CUENTAIBAN'];}
	           ?>'
	           size="30"
	           maxlength="60"
	    />	
			<br />		
			 
			<!--------------------------------------------------------------------------------------->						
					
		</p>
	 </fieldset>
	 <br />	
	 <!-- ******************* Fin Datos de identificación MIEMBRO ************** --> 

		
		<!--************ Inicio Datos de datSocioPendienteConfirmar[OBSERVACIONES] ***********-->		
	 <fieldset> 
	  <legend><b>Comentario del socia/o</b></legend>
	 <p>						
		<textarea type="text" readonly class="mostrar" wrap="hard" name="datosFormSocioPendienteConfirmar[COMENTARIOSOCIO]" 
		          rows="3" cols="80"><?php 
		  if (isset($datSocioPendienteConfirmar['COMENTARIOSOCIO']))                    
			{echo htmlspecialchars(stripslashes($datSocioPendienteConfirmar['COMENTARIOSOCIO']));}
		?></textarea> 			 
		</p>
	 </fieldset>		 
		<br />			

	 <!-- ******************* Fin Datos de SOCIO ******************************** -->		
	  <input type="submit" name="SiConfirmarAltaSocio" value="Si confirmar alta socio/a pendiente" class="enviar">	
  </form>

  <form method="post" class="linea" action="./index.php?controlador=cPresidente&amp;accion=confirmarAltaSocioPendientePorGestor">
   <input type="submit" name="NoConfirmarAltaSocio" value="No confirmar alta socio/a" class="enviar">
  </form>
 <!--</div>  <div id="formLinea">-->
	
</div><!-- <div id="registro">-->




