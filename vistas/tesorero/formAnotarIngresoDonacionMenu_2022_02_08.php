<?php
/*--------------------------------------------------------------------------------------------------------
FICHERO: formAnotarIngresoDonacionMenu.php
VERSION: PHP 7.3.21

DESCRIPCION: Formulario que sirve como menú para elegir entre los posibles casos 
que se pueden dar al anotar una donación:

- Donante nuevo e identificado (no socio), pero no registrado previamente como donante 
- Anónimo. 

-	Buscar por "Nº Documento NIF, NIE, pasaporte, otros" o por "email" por ser socio, 
  o donante no socio ya anotado (buscará en las tablas "MIEMBROS" o "DONACION")				
									

LLAMADA: vistas/tesorero/vCuerpoAnotarIngresoDonacionMenu.php
LLAMA: modelos/libs/comboLista.php
       
OBSERVACIONES:
--------------------------------------------------------------------------------------------------------*/
require_once './modelos/libs/comboLista.php';
?>

<!-- ********************** Inicio  formAnotarIngresoDonacionMenu.php *************** -->

<br />

<div id="registro">
		<span class="textoAzu112Left2">
		<ul>
		 <li>Elige <b>Donante nuevo no anónimo/a, no socio/a</b> si dona por primera vez y tienes datos de identificación (Nombre,NIF,etc.)</li>
		 <li>Elige <b>Donante anónimo/a</b> si la persona que dona quiere permanecer anónimo/a</li>
			<li>Elige <b>Buscar por nº documento</b> o <b>Buscar por email</b> si ya estuviese registrado como socio/a o 
			    ha realizado donaciones previas, podrás recuperar sus datos personales y no tendrás que escribirlos</li>					
		</ul>		
	</span>

	<br />
 <!-- ********************** Inicio buscar por DONANTE-NUEVO-NO-ANONIMO ************* -->	 	 	 

	<form name="registrarDonacion" method="post" action="./index.php?controlador=cTesorero&amp;accion=anotarIngresoDonacionMenu">
	
 <fieldset>	 
  <legend><b>Donante nuevo no anónimo/a y no socio/a (con datos personales: Nombre y Apellido al menos)</b></legend>

	 	<input type="hidden" name="datosFormDonacion[TIPODONANTE]" value='IDENTIFICADO-NO-SOCIO' />	 	

   <input type="submit" name="buscarPorDonanteNuevoNoAnonimo" value="Donante nuevo (no anónimo/a, no socio/a)" class="enviar" />
			
   <br /><br />
			
	 </fieldset>	
	</form>
	<!-- ********************** Fin buscar por DONANTE-NUEVO-NO-ANONIMO **************** -->	
 <br />
 <!-- ********************** Inicio buscar por anónimo ****************************** -->	 	

 <form name="registrarDonacion" method="post" action="./index.php?controlador=cTesorero&amp;accion=anotarIngresoDonacionMenu">	
	
 <fieldset>	 
  <legend><b>Donante anónimo/a (sin datos personales)</b></legend>

		 <input type="hidden" name="datosFormDonacion[TIPODONANTE]" value='ANONIMO' />	 	

   <input type="submit" name="buscarPorAnonimo" value="Donante anónimo/a" class="enviar" />
	  
			<br /><br />

	 </fieldset>	
	</form>
	<!-- ********************** Fin buscar por anónimo ********************************* -->			
 <br />
		
	 <!-- ********************** Inicio buscar datos por nº Documento o por email ****** -->	 	 	 

	<form name="registrarDonacion" method="post" action="./index.php?controlador=cTesorero&amp;accion=comprobarDonantePrevio_Socio">		
	  <fieldset>	 
	   <legend><b>Buscar donante por NIF, NIE, pasaporte o por Email, para donates que son socios/as o donantes con donaciones previas</b></legend>
      <br />
      <input type="submit" name="buscarPorNumDocEmail" value="Buscar Buscar por nº documento o por email" class="enviar" />		    	
      <br /><br />
						
	  </fieldset>
		
	</form>
	 <!-- ********************** Fin buscar datos por nº Documento o por email ********* -->	 
		
 <br />
 <!-- Mejor volver con botón "Anterior" en cueropo
	<form name="registrarDonacion" method="post" action="./index.php?controlador=cTesorero&amp;accion=anotarIngresoDonacionMenu">		
		
		<input type="submit" name="salirDonacion" onClick="return confirm('¿Salir de donación?')"
		       value='Salir sin anotar donación' />
			
 </form> -->
	
</div>
<!-- ********************** Fin formAnotarIngresoDonacionMenu.php ******************* -->


