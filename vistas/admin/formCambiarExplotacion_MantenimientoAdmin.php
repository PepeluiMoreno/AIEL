<?php
/*-----------------------------------------------------------------------------
FICHERO: formCambiarExplotacion_MantenimientoAdmin.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Es el formulario para elegir cambiar el modo de trabajo de la aplicación de 
Gestión de soci@s "MANTENIMIENTO<->EXPLOTACIÓN".
Mostrará el estado actual de modo de trabajo de la aplicación y permitirá al modo alternativo
														
Cambiar el modo de trabajo de la aplicación de Gestión de soci@s "MANTENIMIENTO<->EXPLOTACIÓN"

RECIBE: variable $_SESSION['vs_MODOTRABAJO'], de cAdmin:cambiarExplotacion_MantenimientoAdmin()	
        que utilizará para ofrecer cambiar al modo alternativo que corresponda
DEVUELVE: en $_POST el nuevo modo a poner la aplicación: "$nuevoModoAplicacion"
														
LLAMADA: vCuerpoCambiarExplotacion_MantenimientoAdmin.php 
         previamente cAdmin:cambiarExplotacion_MantenimientoAdmin()					

OBSERVACIONES: 
-------------------------------------------------------------------------------*/
?>
<div id="registro">
 
	 <br /><br />	
	<?php 
		if (isset($_SESSION['vs_MODOTRABAJO']) && $_SESSION['vs_MODOTRABAJO'] == 'MANTENIMIENTO')
		{	
	   $nuevoModoAplicacion = 'EXPLOTACION';//para mostrar en botón de cambiar a nuevo modo
	?>
				<span class="textoNegro9Left"><strong>Para poner la aplicación en modo "EXPLOTACIÓN" clic en el siguiente botón</strong>			
						 </span>
			 <br /><br />
	<?php
		}	
  elseif (!isset($_SESSION['vs_MODOTRABAJO']) || $_SESSION['vs_MODOTRABAJO'] !== 'MANTENIMIENTO')
		{	
		  $nuevoModoAplicacion = 'MANTENIMIENTO';//para mostrar en botón de cambiar a nuevo modo
	?>
		  <span class="textoNegro9Left"><strong>Para poner la aplicación en modo "MANTENIMIENTO" clic en el siguiente botón</strong> 
					</span>
			 <br /><br />
	<?php
		}	
	?>

 <!-- ********************** Inicio 	form  ************************************************ -->
	<form name="cambiarModoAplicacion" method="post" class="linea" action="./index.php?controlador=cAdmin&amp;accion=cambiarExplotacion_MantenimientoAdmin">
  
		
		<input type="hidden"	name="nuevoModoAplicacion" value="<?php echo $nuevoModoAplicacion; ?>"		/>		
		
		<div align="center">

			<input type="submit" name="SiCambiarModoAp" value="Poner aplicación en modo <?php echo $nuevoModoAplicacion ?> "
			       onClick="return confirm('¿Poner aplicación en modo <?php echo $nuevoModoAplicacion?> ?')">	

   <br /><br />						
			<input type='submit' name="salirSinCambiarModoAp" value="Salir sin cambiar el modo de la aplicación "
											onClick="return confirm('¿Salir sin cambiar el modo de la aplicación?')">	
		</div>							
			
 </form>
	<!-- ********************** Fin 	form  ************************************************** -->
		
	 <?php 
		if (isset($botonSubmit['enlaceBoton'])&&($botonSubmit['enlaceBoton']!=='')&&
		($botonSubmit['enlaceBoton']!==NULL))
		{
				echo	"<form method='post' action=./".$botonSubmit['enlaceBoton'].">".
									" <input type='submit' value=".$botonSubmit['textoBoton'].">";
				echo " </form>";
		}	
		?>
	 <br /><br />	
			
	</div>  			
	
	