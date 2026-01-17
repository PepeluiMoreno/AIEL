<?php
/*-------------------------------------------------------------------------------------------------------
FICHERO: formComprobarDonantePrevio_Socio.php

VERSION: PHP 7.3.21

DESCRIPCION: Formulario que sirve para introducir el "NIF" o el "email", para buscar 
los datos personales de un donante que puede ser socio, o un donante ya registrado previamente
por haber realizado alguna donación anteriormente:

-	Buscar por "Nº Documento" NIF, NIE, pasaporte, otros, por ser socio o donante no socio 
         ya anotado (buscará en las tablas "MIEMBROS" o "DONACION")									
-	Buscar por "Email" por ser socio o donante no socio ya anotado 
 (buscará en las tablas "MIEMBROS" o "DONACION")
									
Después de introducir los datos pedidos en el formulario llevará de 
cTesorero.php:comprobarDonantePrevio_Socio(), que (previa validación) llama a la función buscarDonante(),  

LLAMADA: vistas/tesorero/vCuerpoComprobarDonantePrevio_Socio.php
LLAMA: modelos/libs/comboLista.php
       
OBSERVACIONES:
--------------------------------------------------------------------------------------------------------*/
require_once './modelos/libs/comboLista.php';
?>

<div id="registro">
		<span class="textoAzu112Left2">
		<ul>		 
			<li>Elige <b>Buscar por Número de documento</b> o <b>Buscar por email</b> si ya estuviese registrado como socio/a o 
			    ha realizado donaciones previas. Podrás recuperar sus datos personales y no tendrás que escribirlos</li>					
		</ul>		
			<br />
	</span>
	
 <!-- ********************** Inicio buscar datos por NUMDOCUMENTOMIEMBRO ******************** -->	 	 		
						
 <form name="registrarDonacion" method="post" action="./index.php?controlador=cTesorero&amp;accion=comprobarDonantePrevio_Socio">		
	
	 <fieldset>	 
			<legend><b>Buscar donante por NIF, NIE, pasaporte (en el caso de ser socio/a o donante con donaciones previas)</b></legend>
			<p>	
				<label>Tipo documento</label>
				<?php	  	
				$parValorTipoDoc = array("NIF"=>"NIF","NIE"=>"NIE","Pasaporte"=>"Pasaporte","OTROS"=>"Otros");			
				
				if (!isset($datosAnotarDonacion['datosFormDonacion']['TIPODOCUMENTOMIEMBRO']['valorCampo']) || empty($datosAnotarDonacion['datosFormDonacion']['TIPODOCUMENTOMIEMBRO']['valorCampo']))
				{ $datosAnotarDonacion['datosFormDonacion']['TIPODOCUMENTOMIEMBRO']['valorCampo'] = 'NIF'; }//evita Notice		
			
				echo comboLista($parValorTipoDoc,"datosFormDonacion[TIPODOCUMENTOMIEMBRO]",
																				$datosAnotarDonacion['datosFormDonacion']['TIPODOCUMENTOMIEMBRO']['valorCampo'],
																				$parValorTipoDoc[$datosAnotarDonacion['datosFormDonacion']['TIPODOCUMENTOMIEMBRO']['valorCampo']],"NIF","NIF");																				
				?>
					<label>Nº documento</label> 
						<input type="text"
													name="datosFormDonacion[NUMDOCUMENTOMIEMBRO]"
													value='<?php if (isset($datosAnotarDonacion['datosFormDonacion']['NUMDOCUMENTOMIEMBRO']['valorCampo']))
													{  echo $datosAnotarDonacion['datosFormDonacion']['NUMDOCUMENTOMIEMBRO']['valorCampo'];}
													?>'
													size="12"
													maxlength="20"
						/>	 
				<span class="error">
				<?php
				if (isset($datosAnotarDonacion['datosFormDonacion']['NUMDOCUMENTOMIEMBRO']['errorMensaje']))
				{echo $datosAnotarDonacion['datosFormDonacion']['NUMDOCUMENTOMIEMBRO']['errorMensaje'];}
				?>
				</span>		
				<br />
				<label>País documento</label>
						<?php							
						if (!isset($datosAnotarDonacion['datosFormDonacion']['CODPAISDOC']['valorCampo']) || empty($datosAnotarDonacion['datosFormDonacion']['CODPAISDOC']['valorCampo']))
						{ $datosAnotarDonacion['datosFormDonacion']['CODPAISDOC']['valorCampo'] = 'ES'; /*$parValorComboPaisMiembro['descDefecto']="España";*/}//evita Notice	
					
							echo comboLista($parValorComboPaisMiembro['lista'], "datosFormDonacion[CODPAISDOC]",
																							$datosAnotarDonacion['datosFormDonacion']['CODPAISDOC']['valorCampo'],
																							$parValorComboPaisMiembro['lista'][$datosAnotarDonacion['datosFormDonacion']['CODPAISDOC']['valorCampo']],
																							"ES","España")
								?> 
						<span class="error">
						<?php
						if (isset($datosAnotarDonacion['datosFormDonacion']['CODPAISDOC']['errorMensaje']))
						{echo $datosAnotarDonacion['datosFormDonacion']['CODPAISDOC']['errorMensaje'];}
						?>
						</span>
						<br />
						
						<div align="left">
							<input type="submit" name="buscarPorNumDoc" value="Buscar por nº documento" class="enviar" />		    	
						</div>
						
			</p>
	 </fieldset>
	</form>
 <!-- *********************** Fin Inicio buscar datos por NUMDOCUMENTOMIEMBRO *************** --> 	
		
	<!-- ********************** Inicio buscar datos por email ********************************** -->	 	 	 
					
	<form name="registrarDonacion" method="post" action="./index.php?controlador=cTesorero&amp;accion=comprobarDonantePrevio_Socio">
	 <fieldset>	 
	  <legend><b>Buscar donante por email (en el caso de ser socio/a o donante con donaciones previas)</b></legend>
		 <p>	
	   <label>email</label> 
	    <input type="text"
	           name="datosFormDonacion[EMAIL]"
	           value='<?php if (isset($datosAnotarDonacion['datosFormDonacion']['EMAIL']['valorCampo']))
	           {  echo $datosAnotarDonacion['datosFormDonacion']['EMAIL']['valorCampo'];}
	           ?>'
	           size="30"
	           maxlength="70"
	    />	 
		  <span class="error">
			 <?php
			 if (isset($datosAnotarDonacion['datosFormDonacion']['EMAIL']['errorMensaje']))
		 	{echo $datosAnotarDonacion['datosFormDonacion']['EMAIL']['errorMensaje'];}
		 	?>
		  </span>		
				
		  <div align="left">
      <input type="submit" name="buscarPorEmail" value="Buscar por email" class="enviar" />		    	
    </div>
				
		 </p>
	 </fieldset>
	</form>
	<!-- *********************** Fin Inicio buscar datos por email ***************************** -->

 <form name="registrarDonacion" method="post" action="./index.php?controlador=cTesorero&amp;accion=comprobarDonantePrevio_Socio">			
	
		<div align="center">
			<input type="submit" name="salirDonacion" 
				onClick="return confirm('¿Salir de donación?')"
				value='Salir sin anotar donación' />
	 </div>
		
 </form> 
	
</div>