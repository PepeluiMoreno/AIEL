<?php
/*-----------------------------------------------------------------------------
FICHERO: formMostrarOrdenesCobroUnaRemesa.php
VERSION: PHP 7.3.19

Se forma y muestra una tabla-lista páginada "MOSTRAR ÓRDENES COBRO DE UNA REMESA" 
con los detalles de las órdenes de cobro individuales correspondientes a una remesa
concreta (pendiente de enviar, enviada o actualizada). Y un Link "Ver" para ver los 
datos del socio corresnpondiente a la orden de cobro.
Se mostrarán y ordenadas por APELLIDOS.  

Incluye un botón para elegir, ESTADO CUOTA (por defecto el Todos) y AGRUPACION,
y otro botón para elegir por APE1, APE2.

LLAMADA: vistas/tesorero/vCuerpoMostrarOrdenesCobroUnaRemesa.php 
y a su vez cTesorero:mostrarOrdenesCobroUnaRemesaTes()

OBSERVACIONES:         
------------------------------------------------------------------------------*/
?>    		
<div id="registro">

			<span class="comentario12">Achivo Remesa SEPAXML:</span><span class="mostrar"><?php echo $datosRemesa['NOMARCHIVOSEPAXML'];?></span>
			<span class="comentario12">Año cuota:</span><span class="mostrar"><?php echo $datosRemesa['ANIOCUOTA'];?></span>		
			<span class="comentario12">Fecha orden cobro:</span>	<span class="mostrar"><?php echo $datosRemesa['FECHAORDENCOBRO'];?></span>		
			<span class="comentario12">Fecha pago:</span><span class="mostrar">
			<?php if (isset($datosRemesa['FECHAPAGO']) && !empty($datosRemesa['FECHAPAGO'])) 
			{echo $datosRemesa['FECHAPAGO'];} else echo "-";?>
		 </span>	
			<span class="comentario12">Fecha anotación:</span>	<span class="mostrar">
			<?php if (isset($datosRemesa['FECHAANOTACIONPAGO']) && !empty($datosRemesa['FECHAANOTACIONPAGO'])) 
			{echo $datosRemesa['FECHAANOTACIONPAGO'];} else echo "-";?>
		 </span>						
   <br /><br />	
			<span class="comentario12">Directorio Archivo Remesa:</span><span class="mostrar">
			<?php if (isset($datosRemesa['DIRECTORIOARCHIVOREMESA']) && !empty($datosRemesa['DIRECTORIOARCHIVOREMESA'])) 
			{echo $datosRemesa['DIRECTORIOARCHIVOREMESA'];} else echo "-";?>
		 </span>
	
	 <table width="100%" border="0px" bordercolor="#FFFFFF" cellspacing="0" cellpadding="0">		
		 <tr>
			 <td>
				
    <br />
				<!-- *************** Inicio form búsqueda por APE1, APE2 ******************** -->	

					<form method="post" action="./index.php?controlador=cTesorero&amp;accion=mostrarOrdenesCobroUnaRemesaTes">					

							<span class='textoAzul9C'>Apellido1</span>					
			    <input type="text"
			           name="datosFormMiembro[APE1]"
			           value='<?php if (isset($datosFormMiembro['APE1']['valorCampo']))
			           {  echo $datosFormMiembro['APE1']['valorCampo'];}
			           ?>'
			           size="30"
			           maxlength="200"
			    />	
							<span class="error"><strong>
								<?php
								if (isset($datosFormMiembro['APE1']['errorMensaje']))
								{echo $datosFormMiembro['APE1']['errorMensaje'];}
								?>
							</strong></span>
							<span class='textoAzul9C'>Apellido2</span>				
			    <input type="text"
			           name="datosFormMiembro[APE2]"
			           value='<?php if (isset($datosFormMiembro['APE2']['valorCampo']))
			           {  echo $datosFormMiembro['APE2']['valorCampo'];}
			           ?>'
			           size="30"
			           maxlength="200"
			    />	
							<span class="error"><strong>
								<?php
								if (isset($datosFormMiembro['APE2']['errorMensaje']))
								{echo $datosFormMiembro['APE2']['errorMensaje'];}
								?>
							</strong></span>
							<input type="submit" name="BuscarApeNom" value="Buscar por apellidos"> 											
	    </form>
				<!-- ****************** Fin form búsqueda por APE1,APE2  ********************* -->						
				
				<!-- Inicio selección por  ESTADOCUOTA, AGRUPACION *************************** -->				
				
				 <!-- ******* Inicio selección  ESTADOCUOTA **************************** -->		
					<form method="post" action="./index.php?controlador=cTesorero&amp;accion=mostrarOrdenesCobroUnaRemesaTes">
		
						<span class='textoAzul9C'>&nbsp;&nbsp;Estado cuota</span>		
							<?php 			
								require_once './modelos/libs/comboLista.php';
								$parValorEstadoCuota=array("ABONADA"=>"Abonada",
																																				"ABONADA-PARTE"=>"Abonada en parte",
																																				"PENDIENTE-COBRO"=>"Pendiente de cobro por EL",																														
																																				"NOABONADA"=>"No abonada causa desconocido",
																																				"NOABONADA-DEVUELTA"=>"Devuelta",
																																				"NOABONADA-ERROR-CUENTA"=>"Error cuenta banco",
																																				"EXENTO"=>"Exento"
																																			);																																		
																																			
								$parValorEstadoCuota["%"]="Todos";
								
								if (!isset($resCuotasSocios['ESTADOCUOTA']) || empty($resCuotasSocios['ESTADOCUOTA']))
								{	$resCuotasSocios['ESTADOCUOTA'] = '%';		}								
																		
				  	 echo comboLista($parValorEstadoCuota,"resCuotasSocios[ESTADOCUOTA]",
								                $resCuotasSocios['ESTADOCUOTA'],
						                  $parValorEstadoCuota[$resCuotasSocios['ESTADOCUOTA']],"","");																					
						 ?>											
      
			   <!-- ********* Fin selección  ESTADOCUOTA **************************** -->		

					 <!-- ********************* Inicio selección AGRUPACION *************** -->						
										
					 <span class='textoAzul9C'>&nbsp;&nbsp;Agrupación territorial</span>					
					 <?php 
					  require_once './modelos/libs/comboLista.php';
	
						 //function comboLista($parValor,$identificadorCampo,$valorPrevio,$descPrevio,$valorInicial,$descInicial)
							
							unset($parValorComboAgrupaSocio['lista']['%']);//elimina el elemento correspondiente Todos Para reordenar 
							unset($parValorComboAgrupaSocio['lista']['00000000']);//elimina el elemento correspondiente a Europa Laica Estatal e Internacional Para reordenar 
							$parValorComboAgrupaSocio['lista']['00000000']='Europa Laica Estatal e Internacional'; //añade al final del array el elemento correspondiente a Europa Laica Estatal e Internacional
							$parValorComboAgrupaSocio['lista']["%"]= "Todas"; //Añade Todos Para reordenar y que quede última

							if (!isset($parValorComboAgrupaSocio['valorDefecto']) || empty($parValorComboAgrupaSocio['valorDefecto']))
							{	$parValorComboAgrupaSocio['valorDefecto'] = '%';	}
						
							echo comboLista($parValorComboAgrupaSocio['lista'], "datosFormSocio[CODAGRUPACION]",
																								$parValorComboAgrupaSocio['valorDefecto'],$parValorComboAgrupaSocio['lista'][$parValorComboAgrupaSocio['valorDefecto']],"","");
																									
       ?> 												
  
       <input type="submit" name="agrupacion" value="Buscar por agrupación y estado cuota">						
							
	    </form>
				 <!-- ************************* Fin selección AGRUPACION ************* -->			
					
				<!-- Fin selección por ESTADOCUOTA, AGRUPACION ****************************** -->	
				
    </td> 
			</tr>		                
   <tr>
			 <td>
				<div align="left">
					<!-- ******************** Inicio informa núm. pag y líneas ***************** --> 
					<br />
					<span class='textoAzulClaro8L'>
					<?php if (isset($resCuotasSocios['_pag_info'])) 
					{	echo $resCuotasSocios['_pag_info'];}
 				?></span>	
					<!-- ******************** Inicio informa. num. pag y líneas ***************** -->			
   			
					<!-- ************************ Inicio Tabla datos socios********************** -->	
			 <table width="100%" border="1" cellspacing="0" cellpadding="0" bordercolor="#99CCFF">
			
			  <!-- ******* Inicio Nombres cabeceras de las columnas *********************** -->
     <tr bgcolor="#CCCCCC">			
						<!--<th rowspan='2' class="textoAzul8L">Año</th>-->
      <!--<th rowspan='2' class="textoAzul8L">Socio/aAPE1</th>-->
      <th rowspan='2' class="textoAzul8L">Socio/aApenom</th>
						<th rowspan='2' class="textoAzul8L">Cuenta IBAN</th>
      <th rowspan='2' class="textoAzul8L">Agrupación</th> 							
						<!--<th rowspan='2' class="textoAzul8L">Estado</th>-->
						
      <th rowspan='2' class="textoAzul8L">Cuota EL</th>
      <th rowspan='2' class="textoAzul8L">Cuota elegida</th>						
      <th rowspan='2' class="textoAzul8L">A cobrar por banco</th>
						<th rowspan='2' class="textoAzul8L">Pagada por banco</th> 
      <th rowspan='2' class="textoAzul8L">Gasto cobro cuota</th>  	
						<th rowspan='2' class="textoAzul8L">Gasto devolución</th>  	
      <th rowspan='2' class="textoAzul8L">Fecha ingreso</th><!--Es la fecha real en la que el banco abona a EL las cuotas domiciliadas (igual o posterior a FECHAORDENCOBRO)-->
						<th rowspan='2' class="textoAzul8L">Fecha anotación</th><!--Es el día en que el tesorero Actualizar los pagos de esta remesa en tabla CUOTAANIOSOCIO -->
						<th rowspan='2' class="textoAzul8L">Fecha devolución</th>						
      <!--<th rowspan='2' class="textoAzul8L">&nbsp;Saldo&nbsp;</th>-->
      <!-- <th rowspan='2' class="textoAzul8L" style='width:23px; word-break: break-all'>Estado cuota</th>-->
						<th rowspan='2' class="textoAzul8L">Estado cuota</th> 
						<th rowspan='2' class="textoAzul8L">Observaciones</th> 
						<!--<th rowspan='2' class="textoAzul8L">Modo ingreso/<br />Incluir órden a banco</th>		-->	
      <!--    <th colspan='4' class="textoAzul8C">Acciones</th>  -->
						<th colspan='1' class="textoAzul8C">Acciones</th>  
     </tr>
     <tr bgcolor="#CCCCCC">
      <th class="textoAzul7L">Ver</th>
     <!-- <th class="textoAzul7L">Pago<br />cuota</th>
      <th class="textoAzul7L">Actualiza<br />cuota</th>
						<th class="textoAzul7L">Baja</th>-->
     </tr>    
					
					<!-- ******* Fin Nombres cabeceras de las columnas ************************* -->
					
     <?php      
					if (isset($resCuotasSocios['numFilas']) && $resCuotasSocios['numFilas'] === 0)
				 {?> 
			 	 <br />
				  <span class="error"><strong>No se han encontrado órdenes de cobro para las condiciones de búsqueda</strong></span>
			   <br />
					<?php		
     }	
					
					if (isset ($resCuotasSocios['resultadoFilas']))
				 {	
				   $items = $resCuotasSocios['resultadoFilas'];
							//echo "<br><br>formMostrarIngresosCuotas.php:items:";print_r($items);
							
							/********** Inicio rellenar filas de la tabla *********************************/
							foreach ($items as $ordinal => $fila)
							{ echo ("<tr height='10'>");          

									//echo ("<td class='textoAzul7L'>".$items[$ordinal]['ANIOCUOTA']."</td>");

									/*if (isset($items[$ordinal]['APE1']) && !empty($items[$ordinal]['APE1']))
									{ echo ("<td class='textoAzul7L'>".$items[$ordinal]['APE1']."</td>");}
									else
									{ echo ("<td class='textoAzul7L' style='word-wrap: break-word'>"."&nbsp;"."</td>");}*/
									
									if (isset($items[$ordinal]['apeNom']) && !empty($items[$ordinal]['apeNom']))
									{ echo ("<td class='textoAzul7L'>".$items[$ordinal]['apeNom']."</td>");}
									else
									{ echo ("<td class='textoAzul7L' style='word-wrap: break-word'>"."&nbsp;"."</td>");}
									
									if (isset($items[$ordinal]['CUENTAIBAN']) && !empty($items[$ordinal]['CUENTAIBAN']))//cuando se da de baja a los 5 años debiera desaparecer con cierre año
									{ echo ("<td class='textoAzul7L'>".$items[$ordinal]['CUENTAIBAN']."</td>");}
									else
									{ echo ("<td class='textoAzul7L' style='word-wrap: break-word'>"."&nbsp;"."</td>");}
											
									
									if (isset($items[$ordinal]['NOMAGRUPACION']) && !empty($items[$ordinal]['NOMAGRUPACION']))
									{ echo ("<td class='textoAzul7L'>".$items[$ordinal]['NOMAGRUPACION']."</td>");}						
								
									/*echo ("<td class='textoAzul7L' style='word-break: break-all'>".$items[$ordinal]['ESTADO']."</td>");*/ 
									/*echo ("<td class='textoAzul7R'>".$items[$ordinal]['IMPORTECUOTAANIOEL']."</td>"); */ 
									//echo ("<td class='textoAzul7R'>".number_format($items[$ordinal]['IMPORTECUOTAANIOEL'],2,".",".")."</td>");		
									//echo ("<td class='textoAzul7R'>".number_format($items[$ordinal]['IMPORTECUOTAANIOEL'],1,".",".")."</td>");									
									echo ("<td class='textoAzul7R'>".number_format($items[$ordinal]['IMPORTECUOTAANIOEL'],0,".",".")."</td>");							
									echo ("<td class='textoAzul7R'>".$items[$ordinal]['IMPORTECUOTAANIOSOCIO']."</td>");								
									echo ("<td class='textoAzul7R'>".$items[$ordinal]['CUOTADONACIONPENDIENTEPAGO']."</td>");//CUOTADONACIONPENDIENTEPAGO = cantidad que se solicita cobrar por banco												
									echo ("<td class='textoAzul7R'>".$items[$ordinal]['IMPORTECUOTAANIOPAGADA']."</td>");//importe pagado por banco en orden cobro antes de fecha cobro será = 0, después = CUOTADONACIONPENDIENTEPAGO, si devuelta = 0
									echo ("<td class='textoAzul7R'>".$items[$ordinal]['IMPORTEGASTOSABONOCUOTA']."</td>");					
									echo ("<td class='textoAzul7R'>".$items[$ordinal]['IMPORTEGASTOSDEVOLUCION']."</td>");

									echo ("<td class='textoAzul7L'>".$items[$ordinal]['FECHAPAGO']."</td>"); /*Es la fecha real en la que el banco abona a EL las cuotas domiciliadas (igual o posterior a FECHAORDENCOBRO).*/    
									echo ("<td class='textoAzul7L'>".$items[$ordinal]['FECHAANOTACION']."</td>");/*Es el día en que el tesorero Actualizar los pagos de esta remesa en tabla CUOTAANIOSOCIO */
									echo ("<td class='textoAzul7L'>".$items[$ordinal]['FECHADEVOLUCION']."</td>"); /*Es la fecha que el tesorero escribe como fecha devolución*/	
		       /*	if ($items[$ordinal]['ESTADOCUOTA'] !== 'ABONADA' && $items[$ordinal]['ESTADOCUOTA'] !== 'EXENTO')
									{ echo ("<td class='textoRojo7Left'>".$items[$ordinal]['ESTADOCUOTA']."</td>");	
									}						
									else*/ 
									{ echo ("<td class='textoAzul7L' style='word-wrap: break-word'>".$items[$ordinal]['ESTADOCUOTA']."</td>");	
									}
									echo ("<td class='textoAzul7L'>".$items[$ordinal]['OBSERVACIONES']."</td>"); 											
								
									echo ("<td  valign='center' >");							
				
								?>
								<!-- Inicio links Acciones: Ver, 	... -->
								
										<form method="post" action="./index.php?controlador=cTesorero&accion=mostrarIngresoCuotaAnio">
											
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
									
									echo ("</td>"); 
									
									echo ("</tr>");
									/********** Fin rellenar filas de la tabla ************************************/
							}//cierra foreach
						}	
      ?>
    </table>   
				<!-- ************************ Fin Tabla datos ordenes cobro ********************** -->		
			 </div> 
  				
				</td>  		
	  </tr>

			<!-- ************************ Inicio paginación  ************************ -->					
			<tr>  
			 <td align="center">
    <?php  
				 if (isset($resCuotasSocios['_pag_navegacion']))
					{echo "<span class='textoAzul9C'>".$resCuotasSocios['_pag_navegacion']."</span>";}
     ?>				
    </td>
   </tr>	
		<!-- ************************ Fin paginación  *************************** -->
  </table>   
</div>		
