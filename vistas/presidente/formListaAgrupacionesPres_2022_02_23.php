<?php
/*-----------------------------------------------------------------------------------------------------------
FICHERO: formListaAgrupacionesPres.php
VERSION: PHP 7.3.21

Con datos de tabla " AGRUPACIONTERRITORIAL" se forma una tabla-lista páginada "LISTADO DE AGRUPACIONES", 
y se  muestran algunos datos de cada agrupación territorial. Al final de cada fila dos enlaces: 
icono lupa (ver toda información de esa agrupación), icono pluma (modificar algunos datos de esa agrupación)

RECIBE: un array "$arrDatosAgrupaciones" con los datos de las agrupaciones

LLAMADA: vistas/presidente/vCuerpoListaAgrupacionesPres.php y a su vez desde cPresidente.php:listaAgrupacionesPres()

OBSERVACIONES:    
-----------------------------------------------------------------------------------------------------------*/
?>    		
<div id="registro"> <!-- <div id="registro"> -->

	<table width="100%" border="0px" bordercolor="#FFFFFF" cellspacing="0" cellpadding="0">	<!-- table 1-->	
													
		<tr><!-- tr table 1-->	
			<td><!-- td table 1-->	
			
				<!-- <div align="left">  --> 
				
					<!-- ******************** Inicio informa núm. pag y líneas ***************** --> 
					<br />
					
					<span class='textoAzulClaro8L'>
					<?php if (isset($arrDatosAgrupaciones['_pag_info'])) 
					{	echo $arrDatosAgrupaciones['_pag_info'];}
					?></span>	
					<!-- ******************** Inicio informa. num. pag y líneas ***************** -->			
						
					<!-- ************************ Inicio Tabla datos AGRUPACIONES *************** -->	
					<table width="100%" border="1" cellspacing="0" cellpadding="0" bordercolor="#99CCFF"><!-- table 2-->
				
						<!-- ******* Inicio Nombres cabeceras de las columnas *********************** -->
						<tr bgcolor="#CCCCCC">			
							
							<!--<th rowspan='2' class="textoAzul8L">CODAGRUPACION</th>-->
							<th rowspan='2' class="textoAzul8C">Agrupación</th> 										
							<th rowspan='2' class="textoAzul8C">CIF</th>
							<th rowspan='2' class="textoAzul8C">Banco 1</th>
							<th rowspan='2' class="textoAzul8C">IBAN Banco 1</th>
							<th rowspan='2' class="textoAzul8C">Banco 2</th>
							<th rowspan='2' class="textoAzul8C">IBAN Banco 2</th>						
							<!--<th rowspan='2' class="textoAzul8C">TELMOV</th>-->
							<th rowspan='2' class="textoAzul8C">WEB</th>							
							<!--<th rowspan='2' class="textoAzul8C">EMAIL</th>	-->				
							<th rowspan='2' class="textoAzul8C">Email Coordinación</th> 
							<!--<th rowspan='2' class="textoAzul8C">Email Tesorería</th>  	-->
							<!--<th rowspan='2' class="textoAzul8C">Email Secretaría</th>			-->	
							<!-- <th rowspan='2' class="textoAzul8C">Ámbito</th>
							<th rowspan='2' class="textoAzul8C">Estado</th> -->
							<th rowspan='2' class="textoAzul8C">Observaciones</th> 
							<th colspan='2' class="textoAzul8C">Acciones</th>  
						</tr>
						
						<tr bgcolor="#CCCCCC">
							<th class="textoAzul7L">Ver</th>
							<th class="textoAzul7L">Actualizar</th>
						</tr>    
						
						<!-- ******* Fin Nombres cabeceras de las columnas ************************* -->						
						<br />
						
						<?php      
						if (isset($arrDatosAgrupaciones['numFilas']) && $arrDatosAgrupaciones['numFilas'] === 0)
						{?> 
							<br />
							<!--<span class="error"><strong>No se han encontrado agrupaciones para las condiciones de búsqueda</strong></span>-->
							<br />
						<?php		
						}	
						
						/********** Inicio rellenar filas de la tabla AGRUPACIONES **************************/
						
						if (isset ($arrDatosAgrupaciones['resultadoFilas']))
						{	
							$items = $arrDatosAgrupaciones['resultadoFilas'];			
							
							foreach ($items as $ordinal => $fila)
							{ 									
								?>
								<tr height='10'> <!-- Inicio fila datos agrupacion -->
								
										<?php	
										//echo ("<td class='textoAzul7L'>".$items[$ordinal]['CODAGRUPACION']."</td>");									
										echo ("<td class='textoAzul7L'>".$items[$ordinal]['NOMAGRUPACION']."</td>"); 
										echo ("<td class='textoAzul7L'>".$items[$ordinal]['CIF']."</td>"); 		
										echo ("<td class='textoAzul7L'>".$items[$ordinal]['NOMBREIBAN1']."</td>"); 										
										echo ("<td class='textoAzul7L'>".$items[$ordinal]['CUENTAAGRUPIBAN1']."</td>");								
										echo ("<td class='textoAzul7L'>".$items[$ordinal]['NOMBREIBAN2']."</td>");								
										echo ("<td class='textoAzul7L'>".$items[$ordinal]['CUENTAAGRUPIBAN2']."</td>"); 
										//echo ("<td class='textoAzul7R'>".$items[$ordinal]['TELMOV']."</td>");
										echo ("<td class='textoAzul7L'>"."&nbsp;".$items[$ordinal]['WEB']."</td>");
										//echo ("<td class='textoAzul7L'>".$items[$ordinal]['EMAIL']."</td>");   
										echo ("<td class='textoAzul7L'>"."&nbsp;".$items[$ordinal]['EMAILCOORD']."</td>");
										//echo ("<td class='textoAzul7L'>".$items[$ordinal]['EMAILTESORERO']."</td>");													
										//echo ("<td class='textoAzul7L'>".$items[$ordinal]['EMAILSECRETARIO']."</td>"); 					
										//echo ("<td class='textoAzul7L'>".$items[$ordinal]['AMBITO']."</td>");   
										//echo ("<td class='textoAzul7L'>".$items[$ordinal]['ESTADO']."</td>");
										echo ("<td class='textoAzul7L'>".$items[$ordinal]['OBSERVACIONES']."</td>"); 
					
									 ?>
										<!-- Inicio links Acciones: Ver, 	Actualizar -->		
										<td  valign='center' >															

											<form method="post" action="./index.php?controlador=cPresidente&accion=mostrarDatosAgrupacionPres">
														
														<input type="image" src="./vistas/images/lupa.gif"  value="mostrarDatosAgrupacionPres"
																alt="Mostrar los datos de una agrupación territorial" name="Ver"
																title="Ver todos los datos de una agrupación territorial" align="middle" /><!--para todos browsers-->

															<input type='hidden' name="datosFormAgrupacion[CODAGRUPACION]"
																						value='<?php echo $items[$ordinal]['CODAGRUPACION'];?>' />				
															
											</form>
										</td>   
									
										<td valign='center'>																																							
			
											<form method="post" action="./index.php?controlador=cPresidente&accion=actualizarDatosAgrupacionPres">
														
														<input type="image" src="./vistas/images/pluma.gif"  value="actualizarDatosAgrupacionPres"
																alt="Actualizar datos de la Agrupación territorial" name="Ver"
																title="Actualizar datos de la Agrupación territorial" align="middle" /><!--para todos browsers-->
				
															<input type='hidden' name="datosFormAgrupacion[CODAGRUPACION]"
																						value='<?php echo $items[$ordinal]['CODAGRUPACION'];?>' />																						
															
											</form>												
										</td>
										
          <!-- Fin links Acciones: Ver, 	Actualizar ----->		
										
								</tr>	<!-- Fin fila datos agrupacion -->						

								<?php 		
										/********** Fin rellenar filas de la tabla ************************************/
							}//cierra foreach
						}	
							?>
					</table><!-- table 2-->   
					<!-- ************************ Fin Tabla datos AGRUPACIONES ********************** -->		
					
				<!-- <div align="left">  -->
					
			</td> <!-- td table 1-->	 		
		</tr><!-- tr table 1-->	

		<!-- ************************ Inicio paginación  ************************ -->					
		<tr> <!-- tr table 1-->	 
		
			<td align="center"><!-- td table 1-->	
				<?php  
					if (isset($arrDatosAgrupaciones['_pag_navegacion']))
					{echo "<span class='textoAzul9C'>".$arrDatosAgrupaciones['_pag_navegacion']."</span>";}
					?>				
			</td><!-- td table 1-->	
			
		</tr>	<!-- tr table 1-->	
	 <!-- ************************ Fin paginación  *************************** -->
	
	</table> <!-- table 1-->  
		
</div>		<!-- <div id="registro"> -->
