<?php
/*--------------------------------------------------------------------------------------------------
FICHERO: formMostrarSociosFallecidosPres.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Muestra una tabla de los socios fallecidos dados de baja por un gestor

A partir de la tabla SOCIOSFALLECIDOS, en la que se inserta una fila cuando un gestor da de baja a 
un socio anotándolo como fallecido, se ontiene el listado que se muestra en formato tabla como
"LISTA DE SOCI@S FALLECIDOS" paginadas, con algunos campos de información de los socios fallecidos
de todas las agrupaciones de EL ordenados alfabéticamente. 

Incluye un campo para elegir una agrupación concreta. También permite buscar por apellidos de socios

Aquí se incluye la páginación de la lista de socios fallecidos. En la parte inferior se muestran
número de páginas para poder ir directamente a un página, anterior, siguiente, priemera, última.
													
LLAMADA: vistas/presidente/vCuerpoMostrarSociosFallecidosPres.php
y previamente desde cPresidente.php:mostrarSociosFallecidosPres() 

OBSERVACIONES:

--------------------------------------------------------------------------------------------------*/
?>
<div id="registro">	

	 <table width="100%" border="0" bordercolor="#FFFFFF" cellspacing="0">
		 <tr>
			 <td>
					<!-- *************** Inicio form búsqueda por APE1, APE2 ************* -->	
					
				 <form method="post" action="./index.php?controlador=cPresidente&amp;accion=mostrarSociosFallecidosPres">
					
							<span class='textoAzul9C'>Apellido1</span>					
			    <input type="text"
			           name="datosFormMiembro[APE1]"
			           value='<?php if (isset($datosFormMiembro['APE1']['valorCampo']))
			           {  echo $datosFormMiembro['APE1']['valorCampo'];}
			           ?>'
			           size="30"
			           maxlength="200"
			    />	
							<span class="error">
								<?php
								if (isset($datosFormMiembro['APE1']['errorMensaje']))
								{echo $datosFormMiembro['APE1']['errorMensaje'];}
								?>
							</span>
							<span class='textoAzul9C'>Apellido2</span>					
			    <input type="text"
			           name="datosFormMiembro[APE2]"
			           value='<?php if (isset($datosFormMiembro['APE2']['valorCampo']))
			           {  echo $datosFormMiembro['APE2']['valorCampo'];}
			           ?>'
			           size="30"
			           maxlength="200"
			    />	
							<span class="error">
								<?php
								if (isset($datosFormMiembro['APE2']['errorMensaje']))
								{echo $datosFormMiembro['APE2']['errorMensaje'];}
								?>
							</span>	
							
							<input type="submit" name="BuscarApeNom" value="Buscar por apellidos">
							
      <br /><br />						
	    </form>
					<!-- ****************** Fin form búsqueda por APE1,APE2  ************* -->										
    </td> 
			</tr> 

   <tr>
			 <td>

					 <!-- ********************* Inicio selección agrupación *************** -->						
						<form method="post" action="./index.php?controlador=cPresidente&amp;accion=mostrarSociosFallecidosPres">
											
							<span class='textoAzul9C'>Agrupación territorial</span>					
							<?php 
								require_once './modelos/libs/comboLista.php';

								//echo "<br><br>1parValorComboAgrupaSocio:";print_r($parValorComboAgrupaSocio);
								//function comboLista($parValor,$identificadorCampo,$valorPrevio,$descPrevio,$valorInicial,$descInicial)
								//echo utf8_encode(comboLista($parValorComboAgrupaSocio['lista'], "datosFormSocio[CODAGRUPACION]",
								
								//---------- Inicio reordenar listado agrupaciones --------------------------------------------
								
								unset($parValorComboAgrupaSocio['lista']['00000000']);//elimina el elemento correspondiente a Europa Laica Estatal e Internacional
								unset($parValorComboAgrupaSocio['lista']["%"]);       //elimina para después añadir 						
								
								$parValorComboAgrupaSocio['lista']['00000000']='Europa Laica Estatal e Internacional'; //añade al final del array el elemento correspondiente a Europa Laica Estatal e Internacional
								$parValorComboAgrupaSocio['lista']["%"]= "Todas"; //añade al final del array el elemento correspondiente a "Todas"
								//---------- Fin reordenar listado agrupaciones -----------------------------------------------												
											
								echo comboLista($parValorComboAgrupaSocio['lista'], "datosFormSocio[CODAGRUPACION]",
																																							$parValorComboAgrupaSocio['valorDefecto'],
																																							$parValorComboAgrupaSocio['lista'][$parValorComboAgrupaSocio['valorDefecto']],"","");
								?> 	
								
							&nbsp;&nbsp;&nbsp;&nbsp;															
							<input type="submit" name="organizacionElegida" value="Buscar por agrupación"> 		
							
						</form>
							<!-- ************************* Fin selección agrupación ************* -->			
							
								<!-- ******************** Inicio informa. num. pag y líneas ***************** -->	
							<span class='textoAzulClaro8L'><?php echo "<br />".$resDatosSocios['_pag_info']; ?></span>	
								<!-- ******************** Inicio informa. num. pag y líneas ***************** -->							

				 </td>
    </tr>
				
    <tr>	
						<!-- ******************** Inicio tabla datos socios fallecidos ************************** -->				
						<table width="100%" border="1" cellspacing="0" bordercolor="#99CCFF">
							<tr bgcolor="#CCCCCC">				
								<th rowspan='2' class="textoAzul8L" width='15%'>Nombre</th>
								<!-- <th rowspan='2' class="textoAzul8L" width='3%'>Núm. soci@</th> -->
								<th rowspan='2' class="textoAzul8L" width='10%'>Agrupación</th>	
								<th rowspan='2' class="textoAzul8L" width='8%'>País</th>	
								<th rowspan='2' class="textoAzul8L" width='8%'>Provincia</th>								
								<th rowspan='2' class="textoAzul8L" width='10%'>Localidad</th> 
								<th rowspan='2' class="textoAzul8L" width='5%'>Fecha nacimiento</th> 						
								<th rowspan='2' class="textoAzul8L" width='5%'>Fecha alta</th> 
								<th rowspan='2' class="textoAzul8L" width='5%'>Fecha baja</th> 						
								<th rowspan='2' class="textoAzul8L" width='31%'>Observaciones</th>						
								<!--   <th colspan =3 class="textoAzul8C">Acciones</th>  -->
							</tr>
					
							<tr>
								<?php 
								$items=$resDatosSocios['resultadoFilas'];
								//echo "<br><br>items:";print_r($items);
								foreach ($items as $ordinal => $fila)     //tabla de datos                          
								{ echo ("<tr style='height:30px;'>");         
										
											echo ("<td class='textoAzul7L' width='15%'>".$items[$ordinal]['apeNom']."</td>");
											//echo ("<td class='textoAzul7L'>".$items[$ordinal]['APE1']." ".$items[$ordinal]['APE2'].", ".$items[$ordinal]['NOM']."</td>");
											//echo ("<td class='textoAzul7L' width='3%'>".$items[$ordinal]['CODSOCIO']."</td>");																																									
																																													
											echo ("<td class='textoAzul7L' width='10%'>".$items[$ordinal]['NOMAGRUPACION']."</td>");			
												
											echo ("<td class='textoAzul7L' width='8%'>".$items[$ordinal]['NOMBREPAISDOM']."</td>");		
											
											if (isset($items[$ordinal]['NOMPROVINCIA']) && $items[$ordinal]['NOMPROVINCIA']!=="")
											{echo ("<td class='textoAzul7L' width='8%'>".$items[$ordinal]['NOMPROVINCIA']."</td>");}
											else 
											{echo ("<td class='textoAzul7L' width='8%'>"."&nbsp;"."</td>");}	

											if (isset($items[$ordinal]['LOCALIDAD']) && $items[$ordinal]['LOCALIDAD']!=="")
											{echo ("<td class='textoAzul7L' width='10%'>".$items[$ordinal]['LOCALIDAD']."</td>");}
											else 
											{echo ("<td class='textoAzul7L' width='10%'>"."&nbsp;"."</td>");} 
										
											if (isset($items[$ordinal]['FECHANAC']) && $items[$ordinal]['FECHANAC']!=="")
											{echo ("<td class='textoAzul7L' width='5%'>".$items[$ordinal]['FECHANAC']."</td>");}
											else 
											{echo ("<td class='textoAzul7L' width='5%'>"."&nbsp;"."</td>");}	
																																												
											echo ("<td class='textoAzul7L' width='5%'>".$items[$ordinal]['FECHAALTA']."</td>");
											echo ("<td class='textoAzul7L' width='5%'>".$items[$ordinal]['FECHABAJA']."</td>");
												
											echo ("<td class='textoAzul7L' width='31%'>"."&nbsp;".$items[$ordinal]['OBSERVACIONES']."</td>");													
																	
										echo "</tr>";
									}
									?>
			    </tr>
							
						</table> 
     <!-- ************************ Fin tabla datos socios fallecidos ************************* -->						
	   </tr>	
    <br />
			<!-- ************************ Inicio paginación  ************************ -->					
			<tr> 
    <td>			
				<div align="center"> 
					<?php  
						echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<span class='textoAzul9C'>".$resDatosSocios['_pag_navegacion'].
						"<br /></span>";
						?>	
     </div>					
    </td>
   </tr>	
		 <!-- ************************ Fin paginación  *************************** -->	

  </table>   
		
</div>		
