<?php
/*--------------------------------------------------------------------------------------------------
FICHERO: formConfirmarSocio.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Formulario que pide confirmar o anular el alta de un 
socio (pendiente de confirmar), a petición del mismo, desde el link que recibió al registrase 
como nuevo socio. 
Desde el formulario según la elección se llamará a:
-controladorSocios:confirmarAltaSocio() 
-controladorSocios:anularAltaSocioPendienteConfirmar()
														
LLAMADA: vistas/socios/vCuerpoConfirmarSocio.php y a su vez de controladorSocios.php:confirmarAnularAltaSocio()
													
OBSERVACIONES: Probado PHP 7.3.21
--------------------------------------------------------------------------------------------------*/
require_once './modelos/libs/comboLista.php';
?>
<div id="registro">
 <br />	
 <span class="textoAzu112Left2">
	  Estimado/a<b>
			<?php echo $datosSocioConfirmar['NOM'].' '.$datosSocioConfirmar['APE1'].' '.$datosSocioConfirmar['APE2']?></b>
	  <br /><br /><br />Para completar el proceso de hacerte socio/a de Europa Laica, por seguridad, es necesario que confirmes ahora tu deseo de 
			asociarte a Europa Laica.
			<br /><br /><br /><br />
			- Si quieres confirmar tu deseo de hacerte socia/o de de Europa Laica, haz clic en el botón "Confirmar alta socio/a"
	  <br /><br /><br />
			- Si no quieres hacerte socio/a de Europa Laica, haz clic en el botón "Anular alta socio/a" 
			y borraremos todos tus datos 
  </span>
  <br /><br />
 <div id="formLinea">

  <form method="post" class="linea"
      action="./index.php?controlador=controladorSocios&amp;accion=confirmarAltaSocio">		
	  <input type="submit" name="confirmarAltaSocio" value="Confirmar alta socio/a" class="enviar">	
			<input type="hidden"	name="codUserEncrip"
	           value='<?php echo $datosSocioConfirmar['codUserEncrip'];?>'
	     />			
  </form>				
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<form method="post" class="linea" 
      action="./index.php?controlador=controladorSocios&amp;accion=anularAltaSocioPendienteConfirmar"	
			   onSubmit="return confirm('¿Anular alta socio/a?')">	
   <input type="submit" name="anularAltaSocio" value="Anular alta socio/a" class="enviar">
			<input type="hidden"	name="codUserEncrip"
	           value='<?php echo $datosSocioConfirmar['codUserEncrip'];?>'
	     />					
  </form>
 </div><!--<div id="formLinea">-->
</div><!-- <div id="registro">-->



