<?php
/*-----------------------------------------------------------------------------------------
FICHERO: formCierreAnioPasadoAperturaAnioNuevo.php
VERSION: PHP 7.3.21

Es el formulario para ejecutar el "Cierre de año pasado y apertura de año nuevo" 
En los botones al final de formulario, se eligen en las opciones disponibles 
- $anioActual = date('Y') para ejecutar en año actual.
-	$anioPruebaYmas1 = date('Y')+1) para simular para año siguiente antes de enero, 
  pero este  segundo botón solo estará disponible para versiones para 
		PRUEBA: "europalaica.com/usuarios_copia", o "europalaica.com/usuarios_desarrollo"

A partir del directorio "$dirHome", se muestra en que versión de Gestión de Soci@s, 
que ese momento se está ejecutando: 
- La REAL "europalaica.com/usuarios" 
- Versiones para PRUEBA: "europalaica.com/usuarios_copia", o "europalaica.com/usuarios_desarrollo"

E informa tmabién de la SECUENCIA DE EJECUCIÓN 

RECIBE: 	'$estadoActualizacion['actualizadoAnioY']' que contiene el valor del campo de la tabla controles par 
el año actual date('Y'),

LLAMADO: vistas/admin/vCuerpoCierreAnioPasadoAperturaAnioNuevo.php
         previamente desde cAdmin.php:cierreAnioPasadoAperturaAnioNuevoAdmin()	
									
OBSERVACIONES:
Pasa los valores  POST[anioActual] y	POST[anioActualUnoMas] a la función:
 cAdmin.php:cierreAnioPasadoAperturaAnioNuevoAdmin()	
-----------------------------------------------------------------------------------------*/
?>
<div id="registro">
 
<?php /* Para pruebas
	if(isset($_SERVER['HTTP_REFERER'])) 
	{ echo "<BR><BR>_SERVER['DOCUMENT_ROOT']: ".$_SERVER['DOCUMENT_ROOT']; 				
			$dirHome = getcwd();		 echo "<BR><BR>dirHome: ".$dirHome;
			echo "<BR><BR>_SERVER['HTTP_REFERER']: ".$_SERVER['HTTP_REFERER'];
			$cadOriginal = $_SERVER['HTTP_REFERER'];
			//$cadEliminar = "https://europalaica.com/usuarios/";
			$cadEliminar = "https://europalaica.com/usuarios_desarrollo/";
			$cadAction = str_replace($cadEliminar, "",$cadOriginal); 				echo "<BR><BR>cadAction: ".$cadAction;	echo "<BR><BR>";
			}*/	
	?>	
	<!-- ********************** Inicio INFORMACIÓN **************************************** -->	
		<span class="textoNegro9Left">		
		<strong>INFORMACIÓN</strong>
		<br /><br />El "Cierre de año pasado y apertura de año nuevo", implica bastantes modificaciones en las siguientes tablas de la BBDD:
		<br /><br />"CONTROLES, USUARIO, CUOTAANIOSOCIO, SOCIO, IMPORTEDESCUOTAANIO, MIEMBROELIMINADO5ANIOS, SOCIOSCONFIRMAR, ORDENES_COBRO".
 	<br /><br />Este proceso es <b>IRREVERSIBLE</b>, por lo que previamente hay que poner la aplicación en modo MANTENIMIENTO y hacer un BACKUP de la BBDD.
		<br /><br />En este proceso, se copian datos de las cuotas de soci@s, para el nuevo año, por protección de datos personales se eliminan datos de pendientes de confirmar su alta, 
		datos de socios que fueron baja, etc.
		</span> 

		<!-- ********************** Fin INFORMACIÓN ******************************************* -->	
		<br /><br /><br />
		
  <!-- ********************** Inicio 	SECUENCIA DE EJECUCIÓN ******************************* -->	
		<!--<span class="textoRojo9Right"><strong>OJO: Acción irreversible!!!</strong></span>-->		
		<span class="textoNegro9Left">		
		<strong>SECUENCIA DE EJECUCIÓN: </strong></span>		
		<br /><br />
		
	<ul>
		<li class="textoNegro9Left">El día <strong>31 de diciembre</strong> poner la aplicación en modo <strong>MANTENIMIENTO</strong> hasta que se termine el proceso y haz un <strong>BACKUP</strong></li>
		<br />
	 <li class="textoNegro9Left">Ejecutar esta función el día <strong>1 de enero</strong></li>
		<br />
		<li class="textoNegro9Left">Ten paciencia, podría tardar cierto tiempo, no salgas del navegador hasta ver el mensaje indicando que el proceso ha finalizado, o un aviso de error</li>
		<br />
		<li class="textoNegro9Left"><strong>Comprobar el funcionamiento general</strong> de la aplicación, con un gestor (usuario) que tenga todos los roles y <strong>autorizado para modo mantenimiento</strong></li>
		<br />
		<li class="textoNegro9Left">Después de comprobar que todo está <strong>CORRECTO</strong>, haz un BACKUP y al final pon la aplicación en modo <strong>EXPLOTACIÓN</strong></li>
		<br />
		<li class="textoNegro9Left"> NOTA: <strong>PARA HACER PRUEBAS</strong>, ir a las versiones de pruebas. URLs: "www.europalaica.org/usuarios_copia", o en "www.europalaica.org/usuarios_desarrollo".
		<br />Incluso puedes probar antes del 1 de enero cuando se muestre botón: "Prueba Cierre Año <?php echo $estadoActualizacion['anioActual']; ?> 
		      y Apertura Año <?php echo $estadoActualizacion['anioActual']+1; ?> (simula estar en <?php echo $estadoActualizacion['anioActual']+1; ?> )". 
		<br />De este modo sólo se pueden modificar las correspondientes BBDD de prueba: "europalaica_com_copia" o "europalaica_com_desarrollo"</li>
	</ul>		
	
	<!-- ********************** Fin 	SECUENCIA DE EJECUCIÓN ********************************** -->	
 <br />

	<!-- ********************** Inicio datos para form ************************************* -->	
	<?php 
	 //$directorioRoot = $_SERVER['DOCUMENT_ROOT'];//será:   "/home/virtualmin/europalaica.com/public_html"	
	 $dirHome = getcwd();//será: /home/virtualmin/europalaica.com/public_html/usuarios, o /home/virtualmin/europalaica.com/public_html/usuarios_desarrollo, o /home/virtualmin/europalaica.com/public_html/usuarios_copia
	 $arrURL = explode("/",$dirHome);//devuelve un array de string a partir la URL $dirHome obtenidos por la separación por "/"
 	$i = count($arrURL) - 1; //se toma el último del $arrURL 			
				
 	$versionGestionSocios = $arrURL[$i]; 
		
  //echo "<br><br>1-3 formCierreAnioPasadoAperturaAnioNuevo.php:versionGestionSocios: "; print_r($versionGestionSocios);
				
  if ($versionGestionSocios !== 'usuarios')
		{ $textoVersion = " VERSIÓN PRUEBA: www.europalaica.org/".$versionGestionSocios;	   
  }
  else
  { $textoVersion = " VERSIÓN REAL:  www.europalaica.org/".$versionGestionSocios;
		}
		?>
  <span class="textoRojo9Right"><strong>OJO Acción irreversible!!! </strong></span>		 
		<span class="textoNegro9Left"><strong>	<?php echo $textoVersion;  ?>. AÑO ACTUAL: <?php echo date ('Y'); ?></strong>		
		</span>
  <br /><br />
		<!-- ********************** Fin datos para form ***************************************** -->	

 <!-- ********************** Inicio 	form  ************************************************ -->
	<form name="actualizarSocio" method="post" class="linea" action="./index.php?controlador=cAdmin&amp;accion=cierreAnioPasadoAperturaAnioNuevoAdmin">
	
		<div align="center">
			<?php			
   $anioActual = $estadoActualizacion['anioActual'];//$anioActual=date('Y')		
			if ( $estadoActualizacion['actualizadoAnioY'] == 'NO' )//$actualizadoElAnio = $anioNuevo; Para hacer el día 1 de enenero
   {	?>
			 <input type="hidden"	name="anioActual" value="<?php echo $anioActual; ?>"	/>
				
				<input type="submit" name="cierreAnio" value="Cierre Año <?php echo $anioActual-1; ?> y Apertura Año <?php echo $anioActual; ?>"
											onClick="return confirm('¿Ejecutar Cierre <?php echo $anioActual-1; ?> y Apertura <?php echo $anioActual ?> ?')">	
		 <?php			   
			}
			else//$estadoActualizacion['actualizadoAnioY'] == 'SI'//para probar antes de el 1 de enero para el año siguiente
			{	
		 ?>							
					<span class='textoRojo9Right'><strong>YA ESTÁ REALIZADO EL CIERRE  DE <?php echo date('Y')-1; ?> y APERTURA DE AÑO NUEVO <?php echo date('Y'); ?> :</strong></span>
				 <?php
					/*------------ Este botón y opción solo estará disponible las URLs de PRUEBA ---------------*/
					if ( $dirHome =='/home/virtualmin/europalaica.com/public_html/usuarios_copia' || $dirHome =='/home/virtualmin/europalaica.com/public_html/usuarios_desarrollo' )	
					{	?>						
						<input type="hidden"	name="anioActualUnoMas" value="<?php echo $anioActual+1; ?>"		/>				
						&nbsp;	&nbsp;	
						<input type="submit" name="cierreAnioPruebaYmas1" value="Prueba Cierre Año <?php echo $anioActual;?> y Apertura Año <?php echo $anioActual+1;?>(simula estar en <?php echo $anioActual+1;?>"	
													onClick="return confirm('¿Ejecutar simular Cierre <?php echo $anioActual; ?> y Apertura <?php echo $anioActual+1; ?> ?')">				
					<?php
					}	
			  /*------------- Fin de botón de PRUEBA -------------------------------------------------------*/	
			}
   ?>		
			
   <br /><br />						
			<input type='submit' name="salirSinCierreAnio" value="Salir sin realizar Cierre y Apertura Año"
										onClick="return confirm('¿Salir sin realizar Cierre y Apertura Año?')">	
		</div>							
			
 </form>
	<!-- ********************** Fin 	form  ************************************************** -->

 	 <br />
	</div>  			
	
	