<?php
/*-----------------------------------------------------------------------------
FICHERO: formRegistrarUsuario.php
VERSION: PHP 5.2.3
DESCRIPCION: Es el formulario para el registro de un usuario
OBSERVACIONES:Es incluida desde "vCuerpoRegistrarUsuario.php"
              mediante require_once './vistas/usuarios/formRegistrarUsuario.php'
-------------------------------------------------------------------------------*/
require_once './modelos/libs/comboLista.php';
?>
<div id="registro">
 <form method="post"
    action="./index.php?controlador=controladorUsuarios&accion=registrarUsuario">
	<!--	<input type="checkbox" name="option2" value="Butter" checked> Butter<br>
		<input type="checkbox" name="option1" value="Milk"> Milk<br>
-->
   <p>
    <!--********************** Inicio Datos de identificación MIEMBRO ***************-->
<!--		<br />Para registrarse como usuario en la bases de datos de Europa Laica, tiene dos alternativas: como socio de pleno derecho o como simpatizante
		<br />
Socio: como socio tendrá el derecho a participar en las asambleas de Europa Laica con voz y voto y ser miembro de las juntas directivas. Se inscribirá dentro de alguna de las agrupaciones territoriales existentes en Europa Laica. Deberá abonar una couta anual de 30 euros (año 2010). Recibirá información por correo electrónico de Europa Laica (opcional)	
			<br />
Simpatizante: recibirá información, por correo electrónico, de los comunicados y actividades de Europa Laica. No tiene que pagar cuotas
-->
<br />		
		 <label>*Tipo (Socio o Simpatizante) 
		   <span class="textoRojo8Left">
			  <?php
			  if (isset($error['datosFormMiembro']['tipoMiembro']['errorMensaje']))
			  {echo $error['datosFormMiembro']['tipoMiembro']['errorMensaje'];}
			  ?>
				
				</span>
		 </label>	
		  <br />
	    <input type="radio"
	           name="datosFormMiembro[tipoMiembro]"
	           value='socio'
						 <?php if ($error['datosFormMiembro']['tipoMiembro']['valorCampo']=='socio')
	           {  echo "checked='checked'";}
	           ?>
	    />
			<br />
			<label>Socio</label>
			<br />	 
	    <input type="radio"
	           name="datosFormMiembro[tipoMiembro]"
	           value='simpatizante'
						 <?php if ($error['datosFormMiembro']['tipoMiembro']['valorCampo']=='simpatizante')
	           {  echo "checked='checked'";}
	           ?>						 
	    /><label>Simpatizante</label>  
   	<br />
	 </p>		 
	 <fieldset>	 
	  <legend>Datos personales<br /></legend>	
		<p>	
		<label>*Documento</label>		
		<?php	  	
		 //$parValorMes=array(1=>"DNI",2=>"NIE",3=>"Pasaporte");	//se se crea una tabla se usará este
		 //echo comboLista($parValorMes,"datosFormMiembro[tipoDocumentoMiembro]",
		 //                $error['datosFormMiembro']['tipoDocumentoMiembro']['valorCampo'],
		 //							 $parValorMes[$error['datosFormMiembro']['tipoDocumentoMiembro']['valorCampo']],"1","DNI");			 
		 $parValorMes=array("DNI"=>"DNI","NIE"=>"NIE","Pasaporte"=>"Pasaporte");										 
		 echo comboLista($parValorMes,"datosFormMiembro[tipoDocumentoMiembro]",
		                 $error['datosFormMiembro']['tipoDocumentoMiembro']['valorCampo'],
										 $parValorMes[$error['datosFormMiembro']['tipoDocumentoMiembro']['valorCampo']],"DNI","DNI");									  
		 ?>
	   <label>*Nº documento</label> <!--obligatorio y se valida para NIF y NIE pero no para pasaporte-->
	    <input type="text"
	           name="datosFormMiembro[numDocumentoMiembro]"
	           value='<?php if (isset($error['datosFormMiembro']['numDocumentoMiembro']['valorCampo']))
	           {  echo $error['datosFormMiembro']['numDocumentoMiembro']['valorCampo'];}
	           ?>'
	           size="12"
	           maxlength="20"
	    />	 
		<span class="textoRojo8Left">
			<?php
			if (isset($error['datosFormMiembro']['numDocumentoMiembro']['errorMensaje']))
			{echo $error['datosFormMiembro']['numDocumentoMiembro']['errorMensaje'];}
			?>
		</span>		
	  <br />	

	  <label>*País documento</label>
	     <?php
			 //$parValorComboPais=arrayParValor('PAIS','',"CODPAIS1","NOMBREPAIS",$_POST['datosFormMiembro']['CODPAIS1']);			 
	     //echo '<br>dentro form:';print_r($parValorDescrip);
			 //function comboLista($parValor,$identificadorCampo,$valorPrevio,$descPrevio,$valorInicial,$descInicial)
	      echo utf8_encode(comboLista($parValorComboPaisMiembro['lista'], "datosFormMiembro[CODPAIS1]",
	        	                       $parValorComboPaisMiembro['valorDefecto'],$parValorComboPaisMiembro['descDefecto'],"",""));						
	      ?> 	
		 <br /><br />
		
		 <label>*Sexo
		   <span class="textoRojo8Left">
			  <?php
			  if (isset($error['datosFormMiembro']['sexo']['errorMensaje']))
			  {echo $error['datosFormMiembro']['sexo']['errorMensaje'];}
			  ?>
				</span>
		 </label>	
		  <br />
	    <input type="radio"
	           name="datosFormMiembro[sexo]"
	           value='H' 
						 <?php if ($error['datosFormMiembro']['sexo']['valorCampo']=='H')
	           {  echo " checked";}
	           ?>
	    /><label>Hombre</label>
	    <input type="radio"
	           name="datosFormMiembro[sexo]"
	           value='M'
						 <?php if ($error['datosFormMiembro']['sexo']['valorCampo']=='M')
	           {  echo " checked";}
	           ?>						 
	    /><label>Mujer</label>		
		  <br />    
	   <label>*Nombre</label> <!--obligatorio y se valida si existe-->
	    <input type="text"
	           name="datosFormMiembro[nom]"
	           value='<?php if (isset($error['datosFormMiembro']['nom']['valorCampo']))
	           {  echo $error['datosFormMiembro']['nom']['valorCampo'];}
	           ?>'
	           size="25"
	           maxlength="100"
	    />	 
		<span class="textoRojo8Left">
			<?php
			if (isset($error['datosFormMiembro']['nom']['errorMensaje']))
			{echo $error['datosFormMiembro']['nom']['errorMensaje'];}
			?>
		</span>		
	  <br />
		<label>*Apellido primero</label> <!--obligatorio y se valida si existe-->
	    <input type="text"
	           name="datosFormMiembro[ape1]"
	           value='<?php if (isset($error['datosFormMiembro']['ape1']['valorCampo']))
	           {  echo $error['datosFormMiembro']['ape1']['valorCampo'];}
	           ?>'
	           size="25"
	           maxlength="100"
	    />	 
		<span class="textoRojo8Left">
			<?php
			if (isset($error['datosFormMiembro']['ape1']['errorMensaje']))
			{echo $error['datosFormMiembro']['ape1']['errorMensaje'];}
			?>
		</span>	
		 <br />	
	   <label>Apellido segundo</label> <!--no obligatorio pero se valida si existe-->
	    <input type="text"
	           name="datosFormMiembro[ape2]"
	           value='<?php if (isset($error['datosFormMiembro']['ape2']['valorCampo']))
	                 {  echo $error['datosFormMiembro']['ape2']['valorCampo'];}
	                  ?>'
	           size="25"
	           maxlength="100"
	    />	 
		<span class="textoRojo8Left">
			<?php
			if (isset($error['datosFormMiembro']['ape2']['errorMensaje']))
			{echo $error['datosFormMiembro']['ape2']['errorMensaje'];}
			?>
		</span>	
		<br /><br /> 
		<label>Fecha de nacimiento (dd/mm/aaaa)</label> <!--no obligatorio pero se valida si existe-->		
		<?php
   	 //lo referente a fecha podría ser un requiere_once
		 //function comboLista($parValor,$identificadorCampo,$valorPrevio,$descPrevio,$valorInicial,$descInicial)
     for ($d=1;$d<=31;$d++){$parValorDia[$d]=$d;}	   
		 echo comboLista($parValorDia, "datosFormMiembro[fechanac][dia]",$error['datosFormMiembro']['fechanac']['dia']['valorCampo'],
			                 $parValorDia[$error['datosFormMiembro']['fechanac']['dia']['valorCampo']],"dd","");					
											//$parValorDia[$error['datosFormMiembro']['fechanac']['dia']['valorCampo']],"","");Problemas 								 
	  	
		 //for ($m=1;$m<=12;$m++){$parValorMes[$m]=$m;} 
		 $parValorMes=array(1=>"Enero",2=>"Febrero",3=>"Marzo",4=>"Abril",5=>"Mayo",6=>"Junio" ,7=>"Julio" ,
		                    8=>"Agosto",9=>"Septiembre",10=>"Octubre",11=>"Noviembre",12=>"Diciembre");
										 
		 echo comboLista($parValorMes,"datosFormMiembro[fechanac][mes]",$error['datosFormMiembro']['fechanac']['mes']['valorCampo'],
		 $parValorMes[$error['datosFormMiembro']['fechanac']['mes']['valorCampo']],"mes","");		 
		 //$parValorMes[$error['datosFormMiembro']['fechanac']['mes']['valorCampo']],"","");	Problemas 	 
			 
		 for ($a=date("Y")-100; $a<=date("Y"); $a++){$parValorAnio[$a]=$a;} 
		 echo comboLista($parValorAnio, "datosFormMiembro[fechanac][anio]",$error['datosFormMiembro']['fechanac']['anio']['valorCampo'],
			                 $parValorAnio[$error['datosFormMiembro']['fechanac']['anio']['valorCampo']],"aaaa","");			
		 //$parValorAnio[$error['datosFormMiembro']['fechanac']['anio']['valorCampo']],"","");		Problemas 	
		 ?>	
  	<span class="textoRojo8Left">
			<?php
			if (isset($error['datosFormMiembro']['fechanac']['errorMensaje']))
			{echo $error['datosFormMiembro']['fechanac']['errorMensaje'];}
			?>
		</span>	
		  <br /><br />    
	   <label>Teléfono casa</label> <!--no obligatorio pero se valida si existe-->
	    <input type="text"
	           name="datosFormMiembro[telFijoCasa]"
	           value='<?php if (isset($error['datosFormMiembro']['telFijoCasa']['valorCampo']))
	           {  echo $error['datosFormMiembro']['telFijoCasa']['valorCampo'];}
	           ?>'
	           size="11"
	           maxlength="11"
	    />	 
		<span class="textoRojo8Left">
			<?php
			if (isset($error['datosFormMiembro']['telFijoCasa']['errorMensaje']))
			{echo $error['datosFormMiembro']['telFijoCasa']['errorMensaje'];}
			?>
		</span>
	  <br />		
		<label>Teléfono trabajo</label> <!--no obligatorio pero se valida si existe-->
	    <input type="text"
	           name="datosFormMiembro[telFijoTrabajo]"
	           value='<?php if (isset($error['datosFormMiembro']['telFijoTrabajo']['valorCampo']))
	           {  echo $error['datosFormMiembro']['telFijoTrabajo']['valorCampo'];}
	           ?>'
	           size="11"
	           maxlength="11"
	    />	 
		<span class="textoRojo8Left">
			<?php
			if (isset($error['datosFormMiembro']['telFijoTrabajo']['errorMensaje']))
			{echo $error['datosFormMiembro']['telFijoTrabajo']['errorMensaje'];}
			?>
		</span>
	  <br />		
		<label>Teléfono móvil</label> <!--no obligatorio pero se valida si existe-->	
	    <input type="text"
	           name="datosFormMiembro[telMovil]"
	           value='<?php if (isset($error['datosFormMiembro']['telMovil']['valorCampo']))
	           {  echo $error['datosFormMiembro']['telMovil']['valorCampo'];}
	           ?>'
	           size="11"
	           maxlength="11"
	    />	 
		<span class="textoRojo8Left">
			<?php
			if (isset($error['datosFormMiembro']['telMovil']['errorMensaje']))
			{echo $error['datosFormMiembro']['telMovil']['errorMensaje'];}
			?>
		</span>
	  <br /><br />		
		<label>*email</label>
	    <input type="text"
	           name="datosFormMiembro[email]"
	           value='<?php if (isset($error['datosFormMiembro']['email']['valorCampo']))
	           {  echo $error['datosFormMiembro']['email']['valorCampo'];}
	           ?>'
	           size="30"
	           maxlength="200"
	    />	 
		<span class="textoRojo8Left">
			<?php
			if (isset($error['datosFormMiembro']['email']['errorMensaje']))
			{echo $error['datosFormMiembro']['email']['errorMensaje'];}
			?>
		</span>	
	  <br />		
		<label>*Repetir email</label>
	    <input type="text"
	           name="datosFormMiembro[Remail]"
	           value='<?php if (isset($error['datosFormMiembro']['Remail']['valorCampo']))
	           {  echo $error['datosFormMiembro']['Remail']['valorCampo'];}
	           ?>'
	           size="30"
	           maxlength="200"
	    />	 
		<span class="textoRojo8Left">
			<?php
			if (isset($error['datosFormMiembro']['Remail']['errorMensaje']))
			{echo $error['datosFormMiembro']['Remail']['errorMensaje'];}
			?>
		</span>				
	  <br />	
		<label>Recibir email con comunicados y noticias de Europa Laica</label>
	    <input type="checkbox"
	           name="datosFormMiembro[informacionEmail]"
	           value="SI"
						 <?php if ($error['datosFormMiembro']['informacionEmail']['valorCampo']=='SI')
						 {	echo " checked='checked'"; }
	           ?>
	    />	 
	  <br />		
		<label>Recibir cartas con comunicados de Europa Laica</label>
	    <input type="checkbox" 
	           name="datosFormMiembro[informacionCartas]"
						 value="SI"
	           <?php if ($error['datosFormMiembro']['informacionCartas']['valorCampo']=='SI')
	           {  echo " checked='checked'";}
	           ?>
	    />	 
	  <br />	

		</p>
	 </fieldset>
	 <br />	
	 <!--********************** Fin Datos de identificación MIEMBRO ***************--> 	
	 
	 <!--********************** Inicio Validar datosFormDomicilio ***************--> 	
	 <fieldset>
	  <legend>Domicilio<br /></legend>
		<p>
		
		 <label>*País domicilio</label>
	     <?php
			 //$parValorComboPais=arrayParValor('PAIS','',"CODPAIS1","NOMBREPAIS",$_POST['datosFormMiembro']['CODPAIS1']);			 
	     //echo '<br>dentro form:';print_r($parValorDescrip);
			 //function comboLista($parValor,$identificadorCampo,$valorPrevio,$descPrevio,$valorInicial,$descInicial)
	      echo utf8_encode(comboLista($parValorComboPaisDomicilio['lista'], "datosFormDomicilio[CODPAIS1]",
	        	                       $parValorComboPaisDomicilio['valorDefecto'],$parValorComboPaisDomicilio['descDefecto'],"",""));						
	      ?> 	
		 <br /><br />		
		 
<!--*****	Tipo vía mejor que lo escriban, se evita problemas con otros países *****-->	
	   <label>*Tipo vía (calle, plaza, etc ...)</label> <!-- no se valida tipos datos-->	
	    <input type="text"
		         id="via"
	           name="datosFormDomicilio[via]"
	           value='<?php if (isset($error['datosFormDomicilio']['via']['valorCampo']))
	                        {  echo $error['datosFormDomicilio']['via']['valorCampo'];}
	                  ?>'
	           size="30"
	           maxlength="100"
	     />		
		<span class="textoRojo8Left">
			<?php
			  if (isset($error['datosFormDomicilio']['via']['errorMensaje']))
		    {echo $error['datosFormDomicilio']['via']['errorMensaje'];}
			?>
		</span>			 
		<br />  
		<label>*Nombre vía</label> <!-- no se valida tipos datos-->	
		    <input type="text"			
		           name="datosFormDomicilio[direccion]"
		           value='<?php if (isset($error['datosFormDomicilio']['direccion']['valorCampo']))
		                        {  echo $error['datosFormDomicilio']['direccion']['valorCampo'];}
		                  ?>'
		           size="60"
		           maxlength="255"
		    />		
		<span class="textoRojo8Left">
			<?php
			  if (isset($error['datosFormDomicilio']['direccion']['errorMensaje']))
		    {echo $error['datosFormDomicilio']['direccion']['errorMensaje'];}
			?>
		</span>		
	  <br />
		<label>*Número</label>	<!--no se valida y puede no existir-->
		    <input type="text"			
		           name="datosFormDomicilio[numero]"
		           value='<?php if (isset($error['datosFormDomicilio']['numero']['valorCampo']))
		                        {  echo $error['datosFormDomicilio']['numero']['valorCampo'];}
		                  ?>'
		           size="10"
		           maxlength="100"
		    />		
		<span class="textoRojo8Left">
			<?php
			  if (isset($error['datosFormDomicilio']['numero']['errorMensaje']))
		    {echo $error['datosFormDomicilio']['numero']['errorMensaje'];}
			?>
		</span>
		<br />				
		<label>&nbsp;&nbsp;Bloque</label>	<!--no se valida y puede no existir-->
		    <input type="text"			
		           name="datosFormDomicilio[bloque]"
		           value='<?php if (isset($error['datosFormDomicilio']['bloque']['valorCampo']))
		                        {  echo $error['datosFormDomicilio']['bloque']['valorCampo'];}
		                  ?>'
		           size="10"
		           maxlength="100"
		    />	
		<label>&nbsp;&nbsp;Escalera</label>	<!--no se valida y puede no existir-->
		    <input type="text"			
		           name="datosFormDomicilio[escalera]"
		           value='<?php if (isset($error['datosFormDomicilio']['escalera']['valorCampo']))
		                        {  echo $error['datosFormDomicilio']['escalera']['valorCampo'];}
		                  ?>'
		           size="10"
		           maxlength="30"
		    />
		<label>&nbsp;&nbsp;Piso</label>	<!--no se valida y puede no existir-->
		    <input type="text"			
		           name="datosFormDomicilio[piso]"
		           value='<?php if (isset($error['datosFormDomicilio']['piso']['valorCampo']))
		                        {  echo $error['datosFormDomicilio']['piso']['valorCampo'];}
		                  ?>'
		           size="10"
		           maxlength="30"
		    />				
		<label>&nbsp;&nbsp;Puerta/letra</label>	<!--no se valida y puede no existir-->
		    <input type="text"			
		           name="datosFormDomicilio[puertaletra]"
		           value='<?php if (isset($error['datosFormDomicilio']['puertaletra']['valorCampo']))
		                        {  echo $error['datosFormDomicilio']['puertaletra']['valorCampo'];}
		                  ?>'
		           size="10"
		           maxlength="30"
		    />		
	  <br /><br />	
		<label>*Código postal</label>	
		    <input type="text"			
		           name="datosFormDomicilio[cp]"
		           value='<?php if (isset($error['datosFormDomicilio']['cp']['valorCampo']))
		                        {  echo $error['datosFormDomicilio']['cp']['valorCampo'];}
		                  ?>'
		           size="5"
		           maxlength="10"
		    />		
		<span class="textoRojo8Left">
			<?php
			  if (isset($error['datosFormDomicilio']['cp']['errorMensaje']))
		    {echo $error['datosFormDomicilio']['cp']['errorMensaje'];}
			?>
		</span>
	  <br />		
		<label>*Localidad</label>	
		    <input type="text"			
		           name="datosFormDomicilio[localidad]"
		           value='<?php if (isset($error['datosFormDomicilio']['localidad']['valorCampo']))
		                        {  echo $error['datosFormDomicilio']['localidad']['valorCampo'];}
		                  ?>'
		           size="50"
		           maxlength="255"
		    />		
		<span class="textoRojo8Left">
			<?php
			  if (isset($error['datosFormDomicilio']['localidad']['errorMensaje']))
		    {echo $error['datosFormDomicilio']['localidad']['errorMensaje'];}
			?>
		</span>		
	  <br />		
		<label>*Provincia</label>
	     <?php
			 //$parValorComboPais=arrayParValor('PAIS','',"CODPAIS1","NOMBREPAIS",$_POST['datosFormMiembro']['CODPAIS1']);			 

			 //function comboLista($parValor,$identificadorCampo,$valorPrevio,$descPrevio,$valorInicial,$descInicial)
	      echo utf8_encode(comboLista($parValorComboProvDomicilio['lista'], "datosFormDomicilio[CODPROV]",
				$parValorComboProvDomicilio['valorDefecto'],$parValorComboProvDomicilio['descDefecto'],"",""));
	      ?> 	
		 <br />
		</p>
	 </fieldset>
	 <br />	 	 
	
	 <!--********************** Fin Validar datosFormDomicilio ******************--> 
	 
	 <!--****************** Inicio Datos de identificación USUARIO ************-->
	 <fieldset>
	  <legend>Datos de identificación<br /></legend>
		<p>
		
	   <label for="user">*Usuario</label> <!--obligatorio y se valida-->
	    <input type="text"
		         id="user"
	           name="datosFormUsuario[USUARIO]"
	           value='<?php if (isset($error['datosFormUsuario']['USUARIO']['valorCampo']))
	                        {  echo $error['datosFormUsuario']['USUARIO']['valorCampo'];}
	                  ?>'
	           size="12"
	           maxlength="30"
	     />
		<span class="textoRojo8Left">
			<?php
			  if (isset($error['datosFormUsuario']['USUARIO']['errorMensaje']))
		    {echo $error['datosFormUsuario']['USUARIO']['errorMensaje'];}
			?>
	  </span>	
		
		 <br /><br />	 
		<label>*Contraseña</label> <!--obligatorio y se valida-->	
		    <input type="password"			
		           name="datosFormUsuario[PASSUSUARIO]"
		           value='<?php if (isset($error['datosFormUsuario']['PASSUSUARIO']['valorCampo']))
		                        {  echo $error['datosFormUsuario']['PASSUSUARIO']['valorCampo'];}
		                  ?>'
		           size="10"
		           maxlength="16"
		    />		
		<span class="textoRojo8Left">
			<?php
			  if (isset($error['datosFormUsuario']['PASSUSUARIO']['errorMensaje']))
		    {echo $error['datosFormUsuario']['PASSUSUARIO']['errorMensaje'];}
			?>
		</span>
	  <br />
	  <label>*Repetir contraseña</label> <!--obligatorio y se valida-->	 
	    <input type="password"
	           name="datosFormUsuario[RPASSUSUARIO]"
	           value='<?php if (isset($error['datosFormUsuario']['RPASSUSUARIO']['valorCampo']))
	                        {  echo $error['datosFormUsuario']['RPASSUSUARIO']['valorCampo'];}
	                  ?>'
	           size="10"
	           maxlength="16"
	    /> 			
		<span class="textoRojo8Left">
			<?php
			  if (isset($error['datosFormUsuario']['RPASSUSUARIO']['errorMensaje']))
		    {echo $error['datosFormUsuario']['RPASSUSUARIO']['errorMensaje'];}
			?>
		</span>
		<br />
		</p>
	 </fieldset>
	 <br />	 
	 <!--********************** Fin Datos de identificación USUARIO ***************-->  
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