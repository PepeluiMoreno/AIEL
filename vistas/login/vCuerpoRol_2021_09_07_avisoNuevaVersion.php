<?php
/* -----------------------------------------------------------------------------------------------
FICHERO: vCuerpoRol.php
PROYECTO: EL		
VERSION: PHP 7.3.21

DESCRIPCION: Contiene menú idz de roles según tipo de usuario y y la parte central del cuerpo
 de la página incluido descargar archivo de documento alta socio

LLAMADA: vRolInc.php

OBSERVACIONES: 
2019-02-01: Añado descargar archivo de alta para firma de socio:ALTA_NUEVO_SOCIO_DOCUMENTO_FIRMA.pdf
-------------------------------------------------------------------------------------------------*/
?>

	<?php
  //echo '<br /><br />0-1 vCuerpoRol.php:_SESSION: ';print_r($_SESSION);
		
		if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_autentificadoGestor'] !== 'SI')
		{ 
						header('Location:./index.php?controlador=controladorLogin&accion=validarLogin');
		} 
		else//if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_autentificadoGestor'] !== 'SI')
		{ 
				?>		
    <!-- ************************* Inicio Cuerpo  ****************************** -->
						
				<?php
				 require_once './vistas/plantillasGrales/vContent.php';//incluye escribirLinksSeccionIzda($tituloSeccion,$enlacesSeccIzda);
				?>
				<!-- Se abre <div class="content0"> dentro de vContent.php y se cierra al final de este vCuerpoRol.php -->
				
				<!-- ************************ Inicio Cuerpo central ************************ -->
					
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
				   <!-- Este texto podría sustituirse por una variable $cabeceraCuerpo que viniese de controladorLogin.php:menuRolesUsuario() -->							
							GESTIÓN DE SOCIOS/AS DE EUROPA LAICA         
				</h3>
			 <!-- **** Fin título de la página concreta ********************************* -->		
   	<br /><br />
				
    <!-- **** Inicio texto para el cuerpo de esa página concreta *************** -->
			
			<!-- INICIO AVISO NUEVA VERSIÓN (para diciembre de 2021 se debiera quitar) -->
			<?php
			if (isset($_SESSION['vs_autentificadoGestor']) && $_SESSION['vs_autentificadoGestor'] == 'SI') 
			{	
			?>
			<span class= "textoRojo9Left">
		  <strong>N U E V A &nbsp;&nbsp; V E R S I Ó N &nbsp;&nbsp;DE &nbsp;&nbsp;"G E S T I Ó N &nbsp;DE&nbsp; S O C I @ S"</strong>
			</span>	
			<br /><br />
			<!--<span class="textoNegro9Left">-->
			<span 	class="textoAzu112Left2">		
			Ha sido necesario adaptar el código de esta aplicación a la nueva versión del lenguaje de programación.
			<br /><br />
			Externamente la presentación ha sufrido pocos cambios, en algunos casos para mejoras de presentación o de funcionalidad,
			pero internamente se han realizado muchos cambios en el código de programación. 
			<br /><br />
			Si encuentras alguna anomalía, te agradecería que me lo comuniques enviando un email a <strong>adminusers@europalaica.org</strong> (Gestión de Soci@s, Agustín Villacorta)
			</span>
			
			<?php
				}		
			?>			
   <br /><br /><br />
			<!-- FIN AVISO NUEVA VERSIÓN (para diciembre de 2021 se debiera quitar)    -->
			
				<span class="textoNegro9Left">
						<!-- Este texto podría sustituirse por una variable textoCuerpo que viniese de controladorLogin.php:menuRolesUsuario() -->					
						Desde el menú izquierdo puedes acceder a las funciones para la gestión de socias/os, 
						que dependerán de los roles (presidencia, vicepresidencia, secretaría, tesorería, gestión de socios/as, ... ) que tengas asignadas en Europa Laica.
						<br /><br />
						La opción "Socio/a" te permitirá, ver, modificar, o eliminar tus propios datos personales
						<br /><br /><br /><br />	
						
						<strong>SEGÚN LAS LEYES DE PROTECCIÓN DE DATOS, LOS DATOS PERSONALES DE LOS SOCIOS/AS NO 
						SERÁN UTILIZADOS CON FINES AJENOS A Europa Laica.</strong> 
						<br /><br />
						Te recordamos que según el Reglamento de Régimen Interior de Europa Laica, 
						has firmado un documento aceptando el conocimiento de la ley de Protección de Datos Personales 
						en lo que se refiere al adecuado uso de dichos datos de las socias/os y que tienes acceso 
						según tus funciones de gestor de Europa Laica (presidencia, vicepresidencia, secretaría, tesorería, gestión de socios/as, ... ). 
						<br />	<br />
						Para evitar que otras personas puedan acceder a ellos elige una contraseña segura.
			 </span>
				
				<!-- **** Fin texto para el cuerpo de esa página concreta ****************** -->
							<br /><br />	
							
				<!-- **** Inicio descargar archivo de formulario alta socio pdf  *********** -->				
									 
				<span class= "textoRojo9Left"><strong>N U E V O </strong>
				
				<a class="textoAzu112Left2" href='../documentos/ASOCIACION/ALTA_NUEVO_SOCIO_DOCUMENTO_FIRMA.pdf'
									target='ventana1' title='Descargar el formulario de alta del socio/a para firmar con reglamento de Protección de Datos Personales'
									onclick="window.open('','ventana1','width=800,height=600,scrollbars=yes')">
									>> Descargar el Formulario de alta de un/a Nuevo/a Socio/a para firmar, con reglamento de Protección de Datos Personales.</a>				
									
				<!-- **** Fin descargar archivo de formulario alta socio pdf  **************** -->	
				<br /><br />						
						
				<!-- ********************  Inicio Formulario salir aplicación **************** -->
				 <?php // require_once './vistas/login/formularioCuerpoLogin.php';	 ?>
				<!-- ********************  Fin Formulario   ********************************** -->
				
				<div align="center">	

						<!-- ************************ Inicio imagen  ****************************** -->
						<p align="center">
										<img src="./vistas/images/EscuelaPublicaT5small.jpg"  align="middle" alt="Escuela Publica y Laica">
										
										<br />											
						</p>
						<!-- ***************************  Fin imagen   **************************** -->	
					<br />	<br />			
					
					<!-- ********************  Inicio form botón anterior*********************** --> 								
					<?php				
						//if (isset($navegacion['anterior']) && !empty($navegacion['anterior'])) 
						//{ echo $navegacion['anterior'];
						//}
					?>						
				<!-- ********************  Fin Form botón anterior ************************** --> 		
				
				</div><!--cierra <div align="center"> -->	
				
				<!-- ****************************** Fin Cuerpo central  ********************* -->

				</div><!-- cierra <div class="content0"> abierto al principio en require_once './vistas/plantillasGrales/vContent.php';
				<!-- ******************************* Fin CuerpoRol ************************** -->
				<?php
				
		}//else !if ($_SESSION['vs_autentificado'] !== 'SI' || $_SESSION['vs_autentificadoGestor'] !== 'SI')
		?>
