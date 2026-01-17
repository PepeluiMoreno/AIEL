<?php
/*-----------------------------------------------------------------------------
FICHERO: formTipoUsuario.php
VERSION: PHP 5.2.3
DESCRIPCION: Es el formulario para el registro de tipo de usuario
OBSERVACIONES:????Es incluida desde "vCuerpoRegistrarUsuario.php"
              mediante require_once './vistas/usuarios/formRegistrarUsuario.php'
-------------------------------------------------------------------------------*/
?>
<div id="registro">
 <form method="post"
    action="./index.php?controlador=controladorUsuarios&accion=registrarTipoUsuario">

   <p>
    <!--********************** Inicio Datos de identificación MIEMBRO ***************-->
		<span class="textoAzul9Left">
		<br />Para participar en la asociación "Europa Laica", tiene dos alternativas: como socio o como simpatizante.
		<br /><br />
		Como <b>SOCIO</b>
		<br /> > Tendrás derecho a participar en las asambleas de Europa Laica con voz y voto
		<br /> > Podrás ser miembro de las juntas directivas (transcurridos 6 meses desde la inscripción)
		<br /> > Deberás abonar una cuota anual de 30 euros (año 2010)
		<br /> > Podrás inscribirte en alguna de las agrupaciones territoriales existentes en Europa Laica
		<br /> > Recibirás información de Europa Laica por correo electrónico (opcional), con las actividades de Europa Laica,
		        convocatorias de asambleas e información específica para los socios
		<br /><br />
		Para hacerte socio de "Europa Laica" debes abonar tu cuota (30 euros año 2010) mediante ingreso
	  directo, o transferencia, en Caja de Madrid, cuenta <b>2038 5511 63 6000115837</b> 
		<br />Señala como concepto: cuota asociación, tu NIF y el nombre y apellidos.
	<br /><br />
	También puedes abonar tu cuota de alta como socio mediante pago con tarjeta 
	<br /><br />
	Si quieres realizar puedes realizar una donación a la vez que abonas la cuota (entonces la cantidad ingresa deberá ser superior a 30
	 euros)
	<br /><br />
	Una vez realizado tu ingreso, nos envías un correo electrónico a <b>presidente@europalaica.com</b> con la fecha del
	 ingreso (la que figure en el documento de ingreso, o transferencia) y los datos anotados en concepto (NIF y el nombre y
	  apellidos).
	<br /><br />
	
	Para domiciliar el pago de la cuota anual en años sucesivos y rellenar tus datos como socio, debes rellenar el formulario
	 siguiente. Además en este formulario te podrás inscribir en una de las agrupaciones territoriales de Europa Laica. 
		</span>
		<span class="textoAzul9Left">
		Como <b>SIMPATIZANTE</b> 
		<br /> > No tienes que pagar cuota
		<br /> > Recibirás información de Europa Laica por correo electrónico, con las actividades de Europa Laica 
		<br /><br />
		
		Para registrarte com simpatizante, debes rellenar el formulario siguiente. 
		<br /><br />
		Una vez registrado como socio o simpatizante, desde el menú podrás modificar tus datos y eliminarlos de la base de datos
		(según la ley de protección de datos)
		</span>
	</p>
  <br />	
	 <fieldset>	
	 <p> 
	  <legend>*Tipo usuario: Socio o Simpatizante<br /></legend>		
		 <label>
		   <span class="textoRojo8Left">
			  <?php //echo "<br><br> error:" ; print_r($error);
			  if (isset($error['datosFormMiembro']['tipoMiembro']['errorMensaje']))
			  {echo $error['datosFormMiembro']['tipoMiembro']['errorMensaje'];}
			  ?>				
				</span>
		 </label>	
		  <br /><br />
	    <input type="radio"
	           name="datosFormMiembro[tipoMiembro]"
	           value='socio'
						 <?php if ($error['datosFormMiembro']['tipoMiembro']['valorCampo']=='socio')
	           {  echo "checked='checked'";}
	           ?>
	    />
			<label>Socio</label>
			<br /><br />	 
	    <input type="radio"
	           name="datosFormMiembro[tipoMiembro]"
	           value='simpatizante'
						 <?php if ($error['datosFormMiembro']['tipoMiembro']['valorCampo']=='simpatizante')
	           {  echo "checked='checked'";}
	           ?>						 
	    /><label>Simpatizante</label>  
   	<br />
	 </p>	 		 	
	 </fieldset>
	 
 
  <div align="center">
    <input type="submit" value="Aceptar" class="enviar"/>
  </div>

 </form> 
</div>
  
<div align="center">
	<form method="post"
	      action="./index.php?controlador=controladorLogin&accion=logOut">
	   <input type="submit" value="Salir de registrar usuario" class="enviar">
	</form>
</div>