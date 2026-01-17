<?php
/*-----------------------------------------------------------------------------
FICHERO: formDescargarDocsRolSocio.php
PROYECTO: EL
VERSION: PHP 5.6.4
DESCRIPCION: Es el formulario que permite descargar del servidor los archivos 
             disponibles para el socio en "documentos/SOCIOS"
OBSERVACIONES: Es incluida desde "vCuerpoDescargarDocsRolSocio.php"  
2020-04-21: lo añado.            
-------------------------------------------------------------------------------*/
require_once './modelos/libs/comboLista.php';
?>

<?php /*if (isset($arrListaArchivos) && !empty($arrListaArchivos) )
										{  echo '1 formMostrarDocsRolSocio.php:arrListaArchivos:';print_r($arrListaArchivos);}*/?>

<div id="registro">
 <br />	
 <span class="textoAzu112Left2">
									
	 Desde esta página puedes descargar o ver el manual de ayuda y otros documentos que pueden ser de tu interés
	 <!-- A continuación te mostramos los datos personales que tenemos en Europa Laica -->
  </span>
  <br /><br />
		
      <!--  <form method="post" action="./index.php?controlador=cCoordinador&amp;accion=descargarDocsCoord">
         <input type="image" src="./vistas/images/lupa.gif"  value="mostrarDatosUsuario"
									  alt="[Ver]" name="Ver" title="Ver todos los datos del socio" align="middle" />							
         <input type='text' name="directorio" value='<?php // echo "documentos/UTILIDADES_PRUEBA/IMG"; ?>' />                                
	       </form>		-->
 <!-- <form method="post" class="linea"
      action="./index.php?controlador=controladorSocios&amp;accion=eliminarSocio">		
   <input type="submit" name="NoEliminar" value="No eliminar socio/a" class="enviar">
  </form>	-->								
		<!-- ******************** Inicio Archivos disponibles ********************************* -->
	 <fieldset>	 
	  <legend><strong>Archivos disponibles
			<?php	if (isset($arrListaArchivos['directorio']) && !empty($arrListaArchivos['directorio'])) 				
			 { //$arrListaArchivos['directorio'] = '/'.$arrListaArchivos['directorio'].'/';
					 echo " en \"". $arrListaArchivos['directorio']."\"";
			 }	
			?>
		</strong></legend>	
		<p>
	   <label><strong>Clic sobre el archivo</strong></label><br />
							
				<ul>				
					<?php				
					$array = $arrListaArchivos['listaArchivos'];
     //$arrListaArchivos['directorio'] ='documentos/SOCIOS';
				
					foreach ($array as $i => $value) 
					{
						if ($array[$i]['Tamaño'] == 0)
						{ //echo '<strong>'.$array[$i]['Nombre'].'</strong><br />';
					   //$arrListaArchivos['directorio'] = $arrListaArchivos['directorio'].'/'.$array[$i]['Nombre'];
        //echo "<br />antes ";print_r($arrListaArchivos['directorio']	);
								//echo "<br />antes [DirPath]: ";print_r($array[$i]['DirPath']	);
								//echo "<br />antes [DirPath]: ";print_r(str_replace(realpath($_SERVER['DOCUMENT_ROOT']),"",$array[$i]['DirPath']).$array[$i]['Nombre']);echo "<br />";
								?>
								<!--
								<br /><strong>
        <form method="post" action="./index.php?controlador=cCoordinador&amp;accion=descargarDocsCoord">
         <input type="text" readonly name="directorio" value="<?php echo (str_replace(realpath($_SERVER['DOCUMENT_ROOT']),"",$array[$i]['DirPath']).$array[$i]['Nombre']); ?>"
	               size="50" maxlength = "100"									
         <input type="hidden"  name="directorio" value="<?php echo (str_replace(realpath($_SERVER['DOCUMENT_ROOT']),"",$array[$i]['DirPath']).$array[$i]['Nombre']); ?>"	/>	
									<input type='hidden' name="dirAnterior" value='<?php echo (str_replace(realpath($_SERVER['DOCUMENT_ROOT']),"",$array[$i]['DirPath'])); ?>' />
									<input type="image" src="./vistas/images/lupa.gif"  value="mostrarDatosUsuario"
									  alt="[Ver]" name="Ver" title="Ver todos los datos del socio" align="middle" />
	       </form>	
        </strong>		-->							
								<?php							
						}//if ($array[$i]['Tamaño'] == 0)
						else// if ($array[$i]['Tamaño'] !== 0)
						{
					  ?>
					  <!-- 
							 link: https://www.europalaica.com/documentos/SOCIOS/Carta-Programatica-Europa-Laica-2016.pdf 
							 texto: Carta-Programatica-Europa-Laica-2016.pdf, (123 Kb)
							-->	
							<!-- <li> 	-->
							  <?php	/*echo "<br />Dentro ";print_r($arrListaArchivos['directorio']);	echo "<br />";
										echo "<br />Dentro [DirPath]: ";print_r(str_replace(realpath($_SERVER['DOCUMENT_ROOT']),"",$array[$i]['DirPath']));echo "<br />";
				      $dirAux = $arrListaArchivos['directorio'];
										echo "<br />Dentro: dirAux: ";print_r($dirAux);echo "<br />";*/
										
									if (isset($arrListaArchivos['directorio']) && $arrListaArchivos['directorio'] !== (str_replace(realpath($_SERVER['DOCUMENT_ROOT']),"",$array[$i]['DirPath'])) ) 
									{ /*echo " en \"". $arrListaArchivos['directorio']."\"<br />";*/
									}
							 	else
								 {											
									 ?>	
           <li>										
											<a href="<?php	echo (str_replace(realpath($_SERVER['DOCUMENT_ROOT']),"",$array[$i]['DirPath'])).$array[$i]['Nombre']?>"							        
																				target='ventana1'
																				title = "<?php echo $array[$i]['NombreArch']; ?>"	
																				onclick="window.open('','ventana1','width=800,height=600,scrollbars=yes')"																		
												>	<?php echo $array[$i]['Nombre'].', ('.round($array[$i]['Tamaño']/1024).' Kb)'.'<br /><br />'; ?>	             
											</a>   				
							   </li> 
									<?php
								 }
									?>
							<!-- </li>	-->
							<?php
						}//else// if ($array[$i]['Tamaño'] !== 0)	
					}//foreach ($array as $i => $value) 
					
//---
					foreach ($array as $i => $value) 
					{
						if ($array[$i]['Tamaño'] == 0)
						{ //echo '<strong>'.$array[$i]['Nombre'].'</strong><br />';
					   //$arrListaArchivos['directorio'] = $arrListaArchivos['directorio'].'/'.$array[$i]['Nombre'];
        //echo "<br />antes ";print_r($arrListaArchivos['directorio']	);
								//echo "<br />antes [DirPath]: ";print_r($array[$i]['DirPath']	);
								//echo "<br />antes [DirPath]: ";print_r(str_replace(realpath($_SERVER['DOCUMENT_ROOT']),"",$array[$i]['DirPath']).$array[$i]['Nombre']);echo "<br />";
								?>
								<br /><strong>
        <form method="post" action="./index.php?controlador=cCoordinador&amp;accion=descargarDocsCoord">
							
         <input type="text" readonly name="directorio" value="<?php echo (str_replace(realpath($_SERVER['DOCUMENT_ROOT']),"",$array[$i]['DirPath']).$array[$i]['Nombre']); ?>"
	               size="50" maxlength = "100"									/>  
									<input type='hidden' name="dirAnterior" value='<?php echo (str_replace(realpath($_SERVER['DOCUMENT_ROOT']),"",$array[$i]['DirPath'])); ?>' /><!-- para anterior -->   
																	
									<input type="image" src="./vistas/images/lupa.gif"  value="mostrarArchivosDirectorio"
									  alt="[Ver]" name="Ver" title="Ver todos archivos de este directorio" align="middle" /><!--para todos browsers-->			
									
        	
	       </form>	
        </strong>									
								<?php							
						}
					}
					
//---						
					?>
    </ul>
		</p>
	 </fieldset>
	 <br />	
	 <!-- ********************** Fin Archivos disponibles *************** --> 	 

</div>


<!-- ******************* Inicio Form botón submit ******************** -->		
<!--  <div align="center">
		<form method="post" action="./index.php?controlador=cGestion&amp;accion=mostrarSocios">     
				<input type="submit" name="ConfirmarSalir" value="Volver">
	</form>
	</div>
	-->			

<!-- ********************  Fin Form botón submit  ******************** --> 
	