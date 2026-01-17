<?php
/*----------------------------------------------------------------------------------------------------
FICHERO: formMostrarIngresosCuotas.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION:
Se forma y muestra una tabla-lista páginada "ESTADO CUOTAS SOCIOS" con el resumen de las cuotas pagadas
y pendientes de los socios. 
Decreciente por años (5 últimos), y creciente		por nombre en la selección por años.  
Incluye un botón para elegir por AÑO (por defecto el actual), ESTADO CUOTA (por defecto el Todos) 
y AGRUPACION, y otro botón para elegir por APE1, APE2.

En la parte inferior se muestran número de páginas para poder ir directamente a un página, 
anterior, siguiente, primera, última.
Además de de mostrar en cada fila datos sobre las cuotas de los socios:	Ingreso, Gasto cobro cuota, 
Fecha ingreso, 	Estado cuota, 	etc. al final para cada fila, hay iconos con links para acciones sobre
el socio:Ver,	Pago cuota, Actualiza cuota, Baja socio 	

En el formulario-tabla, en la parte superior también está el botón  "Totales pagos cuotas por años" 
que dirige a cTesorero.php:mostrarTotalesCuotas()

LLAMADA: vistas/tesorero/vCuerpoMostrarIngresosCuotasInc.php, a su vez desde "cTesorero.php:mostrarIngresosCuotas()"
y menú izdo del rol tesorería "Cuotas socios/as"

OBSERVACIONES
------------------------------------------------------------------------------------------------------*/
?>
<div id="registro">

	 <table width="100%" border="0px" bordercolor="#FFFFFF" cellspacing="0" cellpadding="0">		
			
			<!-- Inicio selecciones por "Totales pagos cuotas","Anio","ESTADOCUOTA","AGRUPACION" ******** -->	
		 <tr>
			 <td>
					<br />
				 <div align="center">
				 <form method="post" action="./index.php?controlador=cTesorero&accion=mostrarTotalesCuotas">
					      <input type="submit" name="mostrarTotalesCuotas" value="Totales pagos cuotas por años">
	    </form>
     </div>  
			 	<br />
			 	<!-- *************** Inicio form búsqueda por APE1, APE2 ******************** -->	
				
				 <form method="post" action="./index.php?controlador=cTesorero&amp;accion=mostrarIngresosCuotas">
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
	    </form>
			 	<!-- ****************** Fin form búsqueda por APE1,APE2  ********************* -->						
				
			 	<!-- Inicio selección por anioCuotasElegido, ESTADOCUOTA, AGRUPACION *********** -->				
				
				 <!-- ******* Inicio selección  anioCuotasElegido ********************** -->		
				 <form method="post" action="./index.php?controlador=cTesorero&accion=mostrarIngresosCuotas">
						<span class='textoAzul9C'>Año</span>		
						 <?php 
						  require_once './modelos/libs/comboLista.php';
				
				    for ($a=date("Y")-5; $a<=date("Y")+1; $a++){$parValorAnio[$a]=$a;}
						 	$parValorAnio["%"]="Todos"; 						 																	
																					
				    echo comboLista($parValorAnio,"resCuotasSocios[anioCuotasElegido]",$resCuotasSocios['anioCuotasElegido'],
					                  $parValorAnio[$resCuotasSocios['anioCuotasElegido']],"2013",2013);																																																
				   ?>	

						<span class='textoAzul9C'>&nbsp;&nbsp;Estado cuota</span>		
							<?php 			
						 $parValorEstadoCuota=array("ABONADA"=>"Abonada",
																																	 	"ABONADA-PARTE"=>"Abonada en parte",
																																 		"PENDIENTE-COBRO"=>"Pendiente de cobro por EL",																														
																																	 	"NOABONADA"=>"No abonada causa desconocido",
																																	 	"NOABONADA-DEVUELTA"=>"Devuelta",
																																			"NOABONADA-ERROR-CUENTA"=>"Error cuenta banco",
																																	 	"EXENTO"=>"Exento"
																																		);																																		
																																		
				   $parValorEstadoCuota["%"]="Todos"; 																															
																		
				  	 echo comboLista($parValorEstadoCuota,"resCuotasSocios[ESTADOCUOTA]",
								                $resCuotasSocios['ESTADOCUOTA'],
						                  $parValorEstadoCuota[$resCuotasSocios['ESTADOCUOTA']],"","");																					
						 ?>
			   <!-- ********* Fin selección  anioCuotasElegido ********************** -->		

					 <!-- ********************* Inicio selección AGRUPACION *************** -->						
										
					 <span class='textoAzul9C'>&nbsp;&nbsp;Agrupación territorial</span>					
					 <?php 
					  require_once './modelos/libs/comboLista.php';

							//$parValorComboAgrupaSocio['lista']["%"]= "Todas"; 
					  //echo "<br><br>parValorComboAgrupaSocio:";print_r($parValorComboAgrupaSocio);
						 //function comboLista($parValor,$identificadorCampo,$valorPrevio,$descPrevio,$valorInicial,$descInicial)
							
      unset($parValorComboAgrupaSocio['lista']['%']);//elimina el elemento correspondiente Todos Para reordenar 
						unset($parValorComboAgrupaSocio['lista']['00000000']);//elimina el elemento correspondiente a Europa Laica Estatal e Internacional Para reordenar 
						$parValorComboAgrupaSocio['lista']['00000000']='Europa Laica Estatal e Internacional'; //añade al final del array el elemento correspondiente a Europa Laica Estatal e Internacional
						$parValorComboAgrupaSocio['lista']["%"]= "Todas"; //Añade Todos Para reordenar y que quede última

							echo comboLista($parValorComboAgrupaSocio['lista'], "datosFormSocio[CODAGRUPACION]",
						        	        $parValorComboAgrupaSocio['valorDefecto'],$parValorComboAgrupaSocio['lista'][$parValorComboAgrupaSocio['valorDefecto']],"","");
						 																	
       ?> 	
												
  
       <input type="submit" name="anio" value="Buscar por año, agrupación y estado cuota">						
	    </form>
				 <!-- ************************* Fin selección AGRUPACION ************* -->	
					
			 	<!-- Fin selección por anioCuotasElegido, ESTADOCUOTA, AGRUPACION *********** -->		
    </td> 						
			</tr>	
			<!-- Fin selecciones por "Totales pagos cuotas","Anio","ESTADOCUOTA","AGRUPACION" *********** -->	

   <tr>
			 <td>
				<br />
				<div align="left">
					<!-- ******************** Inicio informa núm. pag y líneas ***************** --> 

					<span class='textoAzulClaro8L'><?php echo $resCuotasSocios['_pag_info']; ?></span>	
					<!-- ******************** Fin informa. num. pag y líneas ******************* -->			
   			
					<!-- ************************ Inicio Tabla datos socios********************** -->	
			 <table width="100%" border="1" cellspacing="0" cellpadding="0" bordercolor="#99CCFF">
			
			  <!-- ******* Inicio Nombres cabeceras de las columnas *********************** -->
     <tr bgcolor="#CCCCCC">			
						<th rowspan='2' class="textoAzul8L">Año</th>
      <th rowspan='2' class="textoAzul8L">Socio/a</th> 
						<th rowspan='2' class="textoAzul8L">Estado</th>
      <th rowspan='2' class="textoAzul8L">Cuota EL</th>
      <th rowspan='2' class="textoAzul8L">Cuota elegida</th>  
      <th rowspan='2' class="textoAzul8L">Ingreso</th>  
      <th rowspan='2' class="textoAzul8L">Gasto cobro cuota</th>  						
      <th rowspan='2' class="textoAzul8L">Fecha ingreso</th> 
						<th rowspan='2' class="textoAzul8L">Fecha anotación</th>
      <th rowspan='2' class="textoAzul8L">&nbsp;Saldo&nbsp;</th>
      <!-- <th rowspan='2' class="textoAzul8L" style='width:23px; word-break: break-all'>Estado cuota</th>-->
						<th rowspan='2' class="textoAzul8L">Estado cuota</th> 
						<th rowspan='2' class="textoAzul8L">Modo ingreso/<br />Incluir órden a banco</th>			
      <th colspan='4' class="textoAzul8C">Acciones</th>  
     </tr>
     <tr bgcolor="#CCCCCC">
      <th class="textoAzul7L">Ver</th>
      <th class="textoAzul7L">Pago<br />cuota</th>
      <th class="textoAzul7L">Actualiza<br />cuota</th>
						<th class="textoAzul7L">Baja</th>
     </tr>    
					
					<!-- ******* Fin Nombres cabeceras de las columnas ************************* -->
					
     <?php 
					$items=$resCuotasSocios['resultadoFilas'];
     //echo "<br><br>formMostrarIngresosCuotas.php:items:";print_r($items);
					
					/********** Inicio rellenar filas de la tabla *********************************/
     foreach ($items as $ordinal => $fila)
	  	 { echo ("<tr height='10'>");          

	      echo ("<td class='textoAzul7L'>".$items[$ordinal]['ANIOCUOTA']."</td>");
							//echo ("<td class='textoAzul7L'>".$items[$ordinal]['apeNom']."</td>"); 
							if (isset($items[$ordinal]['apeNom']) && !empty($items[$ordinal]['apeNom']))
							{ echo ("<td class='textoAzul7L'>".$items[$ordinal]['apeNom']."</td>");}
							else
							{ echo ("<td class='textoAzul7L' style='word-wrap: break-word'>"."&nbsp;"."</td>");}
							echo ("<td class='textoAzul7L' style='word-break: break-all'>".$items[$ordinal]['ESTADO']."</td>");     
       echo ("<td class='textoAzul7R'>".$items[$ordinal]['IMPORTECUOTAANIOEL']."</td>");  
						 //echo ("<td class='textoAzul7R'>".number_format($items[$ordinal]['IMPORTECUOTAANIOEL'],2,".",".")."</td>");		
							//echo ("<td class='textoAzul7R'>".number_format($items[$ordinal]['IMPORTECUOTAANIOEL'],1,".",".")."</td>");									
							//echo ("<td class='textoAzul7R'>".number_format($items[$ordinal]['IMPORTECUOTAANIOEL'],0,".",".")."</td>");			     						 
       echo ("<td class='textoAzul7R'>".$items[$ordinal]['IMPORTECUOTAANIOSOCIO']."</td>");							
       echo ("<td class='textoAzul7R'>".$items[$ordinal]['IMPORTECUOTAANIOPAGADA']."</td>");		
							echo ("<td class='textoAzul7R'>".$items[$ordinal]['IMPORTEGASTOSABONOCUOTA']."</td>");								
						 echo ("<td class='textoAzul7L'>".$items[$ordinal]['FECHAPAGO']."</td>");     
       echo ("<td class='textoAzul7L'>".$items[$ordinal]['FECHAANOTACION']."</td>");
							if ($items[$ordinal]['saldo']< 0)							
						 {echo ("<td class='textoRojo8Right'>".$items[$ordinal]['saldo']."</td>");
							}
						 else 
						 {echo ("<td class='textoAzul7R'>".$items[$ordinal]['saldo']."</td>");
							}
							
							if ($items[$ordinal]['ESTADOCUOTA'] !== 'ABONADA' && $items[$ordinal]['ESTADOCUOTA'] !== 'EXENTO')
						 { echo ("<td class='textoRojo7Left'>".$items[$ordinal]['ESTADOCUOTA']."</td>");	
							}						
						 else 
						 { echo ("<td class='textoAzul7L' style='word-wrap: break-word'>".$items[$ordinal]['ESTADOCUOTA']."</td>");	
							}
							
				   if (isset($items[$ordinal]['MODOINGRESO']) && !empty($items[$ordinal]['MODOINGRESO']))
       {echo ("<td class='textoAzul7L' style='word-wrap: break-word'>".$items[$ordinal]['MODOINGRESO'].'/<br />'.
							            $items[$ordinal]['ORDENARCOBROBANCO'].
													 "</td>");
							}
						 else
							{ echo ("<td class='textoAzul7L' style='word-wrap: break-word'>"."&nbsp;"."</td>");
							}
							
       echo ("<td  valign='center' >");							
		
      ?>
						<!-- Inicio links Acciones: Ver, 	Pago cuota, 	Actualiza cuota, 	Baja -->
						
        <form method="post" 
           action="./index.php?controlador=cTesorero&accion=mostrarIngresoCuotaAnio">
         <input type="image" src="./vistas/images/lupa.gif"  value="mostrarIngresoCuotaAnio"
									  alt="Mostrar Ingresos Cuotas" name="Ver"
											title="Ver todos los datos del socio" align="middle" /><!--para todos browsers-->
         <input type='hidden' name="datosFormUsuario[CODUSER]"
          value='<?php echo $items[$ordinal]['CODUSER'];?>' />  
																				
         <input type="hidden"	name="datosFormCuotaSocio[ANIOCUOTA]"
                value='<?php echo $items[$ordinal]['ANIOCUOTA'];?>' />																					
          
	       </form>
      <?php           
       echo ("</td>");      
       echo ("<td valign='center'>");						
       ?> 
							 
        <form method="post" 
           action="./index.php?controlador=cTesorero&accion=actualizarIngresoCuota">
											
         <input type="image" src="./vistas/images/euro.gif" value="actualizarIngresoCuota"
									 alt="Actualizar Ingreso Cuota" name="Modificar" title="Anotar ingreso de la cuota del socio" />
										
         <input type='hidden' name="datosFormUsuario[CODUSER]"
          value='<?php echo $items[$ordinal]['CODUSER'];?>' /> 										
					
         <input type="hidden"	name="datosFormCuotaSocio[ANIOCUOTA]"
                value='<?php echo $items[$ordinal]['ANIOCUOTA'];?>' />	
	       </form>						                    
      <?php 
       echo ("</td>");      
       echo ("<td>");
						 //if (isset($items[$ordinal]['ESTADO']) && $items[$ordinal]['ESTADO']!== "baja" )
							if ((isset($items[$ordinal]['ESTADO']) && $items[$ordinal]['ESTADO']!== "baja" ) 
								//&&  $items[$ordinal]['ESTADOCUOTA'] == 'ABONADA' && $items[$ordinal]['ESTADOCUOTA'] == '	ABONADA-PARTE' && $items[$ordinal]['ESTADOCUOTA'] == '	PENDIENTE-COBRO' 	
											&& $items[$ordinal]['ANIOCUOTA'] >= date('Y')
										)								
							{										
       ?>  
	       <form method="post" 
           action="./index.php?controlador=cTesorero&accion=actualizarDatosCuotaSocioTes">
         <input type="image" src="./vistas/images/pluma.gif" value="actualizarDatosCuotaSocioTes"
									alt="Actualizar datos bancario y cuota elegida" name="Actualizar"  
									title="Actualizar datos bancarios, cuota elegida y agrupación" />
         <input type='hidden' name="datosFormUsuario[CODUSER]"
          value='<?php echo $items[$ordinal]['CODUSER'];?>' />  
										
									<input type="hidden"	name="datosFormCuotaSocio[ANIOCUOTA]"
                value='<?php echo $items[$ordinal]['ANIOCUOTA'];?>' />	
	       </form> 
								
								<!-- Fin links Acciones: Ver, 	Pago cuota, 	Actualiza cuota, 	Baja -->
								
       <?php 
							}
							else 
							{echo ("&nbsp;");
							 //echo ("<span class='textoAzul7L'>No tiene</span>");											
							}								
       echo ("</td>"); 

       echo ("<td>");
						 if (isset($items[$ordinal]['ESTADO']) && $items[$ordinal]['ESTADO']!== "baja")
							{										
       ?>  
	       <form method="post" 
           action="./index.php?controlador=cTesorero&accion=eliminarSocioTes">
         <input type="image" src="./vistas/images/papelera.gif"  value="eliminarDatosSocioTes"
									alt="Baja y eliminación de los datos de un socio/a" name="BajaSocio"  
									title="Baja de un socio/a" />
         <input type='hidden' name="datosFormUsuario[CODUSER]"
          value='<?php echo $items[$ordinal]['CODUSER'];?>' />
										
									<input type="hidden"	name="datosFormCuotaSocio[ANIOCUOTA]"
                value='<?php echo $items[$ordinal]['ANIOCUOTA'];?>' />										
	       </form>
         								
       <?php 
							}
							else 
							{echo ("&nbsp;");
							 //echo ("<span class='textoAzul7L'>No tiene</span>");											
							}									
       echo ("</td>"); 
							
       echo ("</tr>");
							/********** Fin rellenar filas de la tabla ************************************/
      }
      ?>
    </table>   
				<!-- ************************ Fin Tabla datos socios*************************** -->		
			 </div> 
  				
				</td>  		
	  </tr>
   <!--	</td>
		 </tr>-->
			<!-- ************************ Inicio paginación  ************************ -->					
			<tr>  
			 <td align="center">
    <?php  
					echo "<span class='textoAzul9C'>".$resCuotasSocios['_pag_navegacion']."</span>";
     ?>				
    </td>
   </tr>	
		<!-- ************************ Fin paginación  *************************** -->
  </table>
		
</div>		
