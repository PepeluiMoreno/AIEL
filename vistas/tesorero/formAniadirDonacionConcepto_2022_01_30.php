<?php
/*--------------- formAniadirDonacionConcepto.php  --------------------------------------------
FICHERO: formAniadirDonacionConcepto.php

VERSION: PHP PHP 7.3.21

DESCRIPCION: Fomulario para añadir un nuevo Concepto de Donación a la tabla "DONACIONCONCEPTOS"             	

Tiene unos botones para "Crear nuevo Concepto Donación", y para "NO Crear nuevo Concepto Donación"

Antes de aceptar la inserción hace pregunta de confirmación 
	
LLAMADA: vistas/tesorero/vCuerpoAniadirDonacionConcepto.php 

OBSERVACIONES:
----------------------------------------------------------------------------------------------*/
?>

<script type="text/javascript">
function limitarTextoArea(max, id)
{if(max < document.getElementById(id).value.length)
	{ document.getElementById(id).value = document.getElementById(id).value.substr(0, max);
   alert("Llegó al máximo de caracteres permitidos");
	}			
}
</script> 


<div id="registro">
 
		<span class="error">
				<?php
				if (isset($datosDonacionConcepto['codError']) && $datosDonacionConcepto['codError'] !== '00000') 
				{
							echo "<b>ERROR AL INTRODUCIR LOS DATOS: revisa los datos con comentarios de error en color rojo</b>";
				}
				?>
		</span>
		<br /><br />

		<span class="textoAzu112Left2">
		 Un nuevo concepto de donación, se creará para una petición especial y concreta que se va a realizar 
			a socios/as, simpatizantes y ciudadanía en general.
		 <br /><br />				
   La petición se realizará mediante emails, webs, etc. después de aprobarse en Asamblea o en junta directiva de Europa Laica.
		 <br /><br />				
			Ejemplo: "COSTAS-MEDALLA-VIRGEN-ROSARIO-CADIZ", o algún evento especial que requiera las aportación de fondos extras,
   <br /><br />				
		 Todos los "Conceptos de Donación", aparecerán o estarán disponibles en el campo "Concepto de la donación" 
			en los procedimientos de gestionar "Donaciones" en que aparezca ese campo.
 	</span> 
		<br /><br />
		
	 <span class='error'><strong>AVISO: </strong></span>
 	<span class='textoAzu112Left2'>&nbsp; Una vez añadido un nuevo "Concepto de Donación", por consistencia de la base de datos 
	                	               ya no se podrá eliminar. Antes de crear un nuevo concepto revisa bien los datos	
		</span>
  <br /><br /> 	<br /> 	
		
	 <span class="textoAzu112Left2">Los campos con asterisco (<b>*</b>) son obligatorios </span> 	
		<br /><br /> 	
  <div id="formLinea">
	
	 <!--********************** Inicio formAniadirDonacionConcepto *******************-->  
		
  <form method="post" class="linea" action="./index.php?controlador=cTesorero&amp;accion=aniadirDonacionConceptoTes"	
			     onSubmit="return confirm('¿Añadir nuevo Concepto de Donación?')">		
 	
	  <fieldset>	 
				<legend><b>Nuevo Concepto Donación</b></legend>	
				<p>
				
					<label>*Concepto Donación (Escueto y significativo en mayúsculas)</label> 
						<input type="text" 	
													name="CONCEPTO"
													value='<?php if (isset($datosDonacionConcepto['CONCEPTO']['valorCampo']))
													{  echo $datosDonacionConcepto['CONCEPTO']['valorCampo'];}
													?>'
													size="35"
													maxlength="100"
						/>
					<span class="error">	
     <strong>						
					<?php
							if (isset($datosDonacionConcepto['CONCEPTO']['errorMensaje']))
							{echo $datosDonacionConcepto['CONCEPTO']['errorMensaje'];}
						?>
					</strong>	
     </span>	 					
						
						<br /><br />
					<label>*Nombre Concepto (Corto y significativo)</label> 
						<input type="text" 	
													name="NOMBRECONCEPTO"
													value='<?php if (isset($datosDonacionConcepto['NOMBRECONCEPTO']['valorCampo']))
													{  echo $datosDonacionConcepto['NOMBRECONCEPTO']['valorCampo'];}
													?>'
													size="50"
													maxlength="100"
						/>
					<span class="error">	
     <strong>						
					<?php
							if (isset($datosDonacionConcepto['NOMBRECONCEPTO']['errorMensaje']))
							{echo $datosDonacionConcepto['NOMBRECONCEPTO']['errorMensaje'];}
						?>
					</strong>	
     </span>	 					
						<br />						
					<!--	
					<label>Fecha</label> 
						<input type="text" 
													name="formDonacionConcepto[FECHACREACIONCONCEPTO]"
													value='<?php if (isset($datosDonacionConcepto['FECHACREACIONCONCEPTO']['valorCampo']))
													{  echo $datosDonacionConcepto['FECHACREACIONCONCEPTO']['valorCampo'];}
													?>'
													size="35"
													maxlength="100"
						/>-->
					<br /><br />				

    <!-- ************ Inicio Datos de OBSERVACIONES  ************************************** -->		
					
	    <label><b>Observaciones</b></label>

					<textarea type="text" wrap="hard" name="OBSERVACIONES"
													rows="3" cols="80"><?php 
					if (isset($datosDonacionConcepto['OBSERVACIONES']['valorCampo']))       
					{echo htmlspecialchars(stripslashes($datosDonacionConcepto['OBSERVACIONES']['valorCampo']));}
					?></textarea>
						<span class="error">	
						<strong>						
						<?php
								if (isset($datosDonacionConcepto['OBSERVACIONES']['errorMensaje']))
								{echo $datosDonacionConcepto['OBSERVACIONES']['errorMensaje'];}
							?>
							</strong>
						</span>		
					<br /><br />	
				
				</p>
			</fieldset>
			<br />	
			
			<span class="error">
					<?php
					if (isset($datosDonacionConcepto['codError']) && $datosDonacionConcepto['codError'] !== '00000') 
					{
								echo "<b>ERROR AL INTRODUCIR LOS DATOS: revisa los datos con comentarios de error en color rojo</b>";
					}
					?>
			</span>
			<br />			
			
	  <!-- ************* Fin Datos de OBSERVACIONES  ***************************************** -->	
		
 
	  <input type="submit" name="SICrearConceptoDonacion" value="Crear nuevo Concepto Donación" title = "Crear nuevo Concepto Donación"  class="enviar">	
  </form>
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  <form method="post" class="linea" action="./index.php?controlador=cTesorero&amp;accion=aniadirDonacionConceptoTes">		
		
   <input type="submit" name="NOCrearConceptoDonacion" value="No Crear nuevo Concepto Donación" title = "Salir sin Crear nuevo Concepto Donación" class="enviar">			
  </form>	
		
		<!--********************** Fin formAniadirDonacionConcepto **********************-->  
		
 </div><!--<div id="formLinea">-->
</div><!-- <div id="registro">-->




