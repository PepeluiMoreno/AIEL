<?php
/* -----------------------------------------------------------------------------
FICHERO:vCuerpoFuncionRol.php
PROYECTO: EL
VERSION: php 7.3.21

DESCRIPCION: Contiene menú idz. de funciones según tipo de rol de usuario, 
y la parte central del cuerpo de la página. 
También incluye descargar archivos con manuales o documentos para ese rol, 
si previamente se incluyen en: controladorSocios.php:menuGralSocio(),
cTesorero.php:menuGralTesorero(), etc. como archivos de alta socio u otros

LLAMADA: /vistas/login/vCuerpoFuncionRol.php que a su vez es llamda desde
controladorSocios.php:menuGralSocio(),cCoordinador.php:menuGralCoord(),
cPresidente.php:menuGralPres(),cTesorero.php:menuGralTesorero()
cAdmin.php:menuGralAdmin(), etc

LLAMA: vistas/plantillasGrales/vContent.php

OBSERVACIONES: Se le llama desde vFuncionRolInc.php
2017-04-23 : Añado $enlacesArchivos, para manuales de gestores			
------------------------------------------------------------------------------- */
?>

<?php
if ($_SESSION['vs_autentificado'] !== 'SI') 
{ 
			header('Location:./index.php?controlador=controladorLogin&accion=validarLogin');
}
else //if ($_SESSION['vs_autentificado'] == 'SI') 
{ //echo '<br /><br />0-1 vCuerpoRolFuncion.php._SESSION: '; print_r($_SESSION);
		
		//$botonSubmit = $navegacion;//no es necesario si no hay form en vCuerpoFuncionRol.php
		//$botonAnterior = $navegacion; 		
		?>
		
	<!-- ************************* Inicio Cuerpo  ****************************** -->
		
	<?php
		require_once './vistas/plantillasGrales/vContent.php';//incluye escribirLinksSeccionIzda($tituloSeccion,$enlacesSeccIzda);
	?>
	<!-- Se abre <div class="content0"> dentro de vContent.php y se cierra al final de este vCuerpoFuncionRol.php -->
		
			<!-- *********************** Inicio Cuerpo central ************************ -->
			
			<!-- **** Inicio Linea superior Links navegación si tiene más de un rol *** -->
			<?php
				if (isset($navegacion['cadNavegEnlaces']) && !empty($navegacion['cadNavegEnlaces'])) 
				{			echo $navegacion['cadNavegEnlaces'];
				}
			?>
			<!-- **** Fin Linea superior Links navegación si tiene más de un rol ****** -->
			<br />	<br />	
			
			<!-- **** Inicio título de la página concreta ***************************** -->
			<h3 align="center">         
				<?php	
				if (isset($cabeceraCuerpo) && !empty($cabeceraCuerpo)) 
				{			echo $cabeceraCuerpo;
				}				
				?>
			</h3>
			<!-- **** Fin título de la página concreta ********************************* -->
			<br /><br /><br />
			
			<!-- **** Inicio texto para el cuerpo de esa página concreta *************** -->
			<span class="textoNegro9Left">		
				<?php
				if (isset($textoCuerpo) && !empty($textoCuerpo)) 
				{			echo $textoCuerpo;
				}		
				?>
			</span>
				<!-- **** Fin texto para el cuerpo de esa página concreta ****************** -->
			<br /><br />	
			
			<!-- *****************  Inicio Formulario formRolFuncion ******************** -->
							<!-- En este caso no es necesario -->
							<?php // require_once './vistas/login/formFuncionRol.php ';	 ?>
							
			<!-- ******************  Fin Formulario formRolFuncion  ********************* -->

			<div align="center">
						<!-- ************************ Inicio imagen ****************************** -->
						<p align="center">
										<img src="./vistas/images/EscuelaPublicaT5small.jpg"  align="middle" alt="Escuela Publica y Laica">
						</p>
						<!-- ***************************  Fin imagen ***************************** -->							

						<!-- **** Inicio Si vienen enlaces de archivos de manuales los sacamos *** -->				
						
						<?php
						/* "$enlacesArchivos" se incluyen manualmente en las funciones que llaman a 
						vFuncionRol.php:	controladorSocios.php:menuGralSocio(),cTesorero.php:menuGralTesorero(), etc.*/
						
						if (isset($enlacesArchivos) && !empty($enlacesArchivos) ) 																
						{		//echo "<br><br>1 vCuerpoFuncionRol.php:enlacesArchivos: ";print_r($enlacesArchivos);
								?>
								<ul>
									<?php													
									foreach ($enlacesArchivos as $fila => $enlace) 
									{								
											?>
													<br />																																					
																<li>
																		<a href="<?php	echo $enlacesArchivos[$fila]['link'];	?>"
																					target='ventana1'
																					title="<?php echo $enlacesArchivos[$fila]['title']; ?>"
																					onclick="window.open('','ventana1','width=800,height=600,scrollbars=yes')" >	<?php echo $enlacesArchivos[$fila]['textoMenu']; ?>	             
																		</a>																									
																</li>																
											<?php
									}
									?>
								</ul>
								<?php          													
						}													
						?>	        										
						<!-- **** Inicio Si vienen enlaces de archivos de manuales los sacamos ****** -->				  
						<br />
						
					<!-- ******************** Inicio form botón anterior ************************* --> 	
					<?php				
						if (isset($navegacion['anterior']) && !empty($navegacion['anterior'])) 
						{ echo $navegacion['anterior'];
						}
					?>	
						<!-- ********************  Fin Form botón anterior ************************* --> 
					<br />	<br />
			
			</div><!--cierra <div align="center"> -->	
			<!-- ************************** Fin Cuerpo central  ************************ -->
		
	</div><!-- cierra <div class="content0"> abierto al principio en require_once './vistas/plantillasGrales/vContent.php';
	
	<!-- ******************************** Fin Cuerpo  ****************************** -->
	<?php
}//else $_SESSION['vs_autentificado'] == 'SI'
?>

