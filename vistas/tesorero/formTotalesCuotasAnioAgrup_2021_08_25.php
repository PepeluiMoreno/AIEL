<?php
/*---------------------------------------------------------------------------------------------------------
FICHERO: formTotalesCuotasAnioAgrup.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Formulario que muestra una tabla "TOTALES CUOTAS POR AGRUPACIONES" de un año concreto, hasta la fecha actual
Ordenadas de modo creciente por nombre de agrupación. 
Detalles en las columnas, incluido las sumas las cuotas pagadas y pendientes de los socios, y otros.  

En la última fila de la tabla, se mostrarán los totales del año correspondiente 

LLAMADA: vistas/tesorero/vCuerpoTotalesCuotasAnioAgrup.php 
y previamente desde cTesorero.php:mostrarTotalesCuotasAnioAgrup()

OBSERVACIONES:
---------------------------------------------------------------------------------------------------------*/
?>
<div id="registro">

<div align="center">
 <!-- <span class="textoAzul8Center"><b><?php echo "AÑO: ".$totalesPagosCuotaAgrupAnio['ANIOCUOTA'];?></b></span> -->
</div>	
    <span class='textoAzulClaro8L'><?php 
				echo "Fecha de obtención: ";echo date("d-m-Y"); ?> 
				</span>
<h3 align="center">
		<?php 
				echo "Datos del año: ".$totalesPagosCuotaAgrupAnio['anioCuotas'];
		?> 
</h3>
		
		<a class='textoAzul9Left' href="./index.php?controlador=cTesorero&amp;accion=infTotalesCuotasAnioAgrup" 
				   target="ventana1" title="Información sobre el significado de las columnas" 
							onclick="window.open('','ventana1','width=800,height=600,scrollbars=yes')">
							>>Ver aclaraciones sobre el significado de las columnas
 </a> 
<br />
	 <table width="100%" border="0px" bordercolor="#FFFFFF" cellspacing="0" cellpadding="0">		
		                
   <tr>
			 <td>
				<div align="left">
					<!-- ******************** Inicio informa num. pag y líneas ***************** --> 
					<!-- <span class='textoAzulClaro8L'><?php echo $resCuotasSocios['_pag_info']; ?></span>	-->
					<!-- ******************** Inicio informa. num. pag y líneas ***************** -->			
    <table width="100%" border="1" cellspacing="0" cellpadding="0" bordercolor="#99CCFF">
					<?php $n= 1;
					?>
     <tr bgcolor="#CCCCCC">			
						<th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Agrupación</th>
      <th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Soci@s Num. Total</th> 
						<th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />General Num.</th>
						<th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Parado Num.</th> 	
						<th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Joven Num.</th> 	
						<th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Honorario Num.</th> 
						
      <th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Cuotas EL<br/><br/><b> &nbsp;&nbsp;€</b></th>						
      <th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Abonan Num.</th>
						<th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Cuotas Abonadas<br/><br/><b> &nbsp;&nbsp;€</b></th>	
						<th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Saldo cuotas&nbsp;&nbsp;&nbsp;&nbsp;<br/><br/><b> &nbsp;&nbsp;€</b></th>
      <th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />NO Abon. +<br/>Pend. Cobro Num.</th>
						<th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />NO Abon. + Pend. Cobro<br/><b><br/>&nbsp;&nbsp;€</b></th>	
						
						<th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Devuelta y <br />Error CC Num.</th>					
						<th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Devuelta y <br />Error CC<br/><b> &nbsp;&nbsp;€</b></th>	
						<!-- <th class="textoAzul7L" valign="top"><br /><br />Error CC Num.</th>					
						<th class="textoAzul7L" valign="top"><br /><br />Total Error CC<br/><br/><b> &nbsp;&nbsp;€</b></th>		
						<th class="textoAzul7L" valign="top"><br /><br />De- vuelta Num.</th>					
						<th class="textoAzul7L" valign="top"><br /><br />De- vuelta<br/><br/><b> &nbsp;&nbsp;€</b></th>	-->	
						
						<th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Abona Parte Num.</th>					
						<th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Abona Parte<br/><br/><b> &nbsp;&nbsp;€</b></th>		
						<th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Exent Num.</th>	
						<th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Exento paga<br/><br/><b> &nbsp;&nbsp;€</b></th>										
      <th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Dona en <br/>la cuota Num.</th> 
      <th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Dona en la cuota<br/><br/><b> &nbsp;&nbsp;€</b></th>  
						
	    	<th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Domiciliada Num.<br/><br/><br/></th>
	    	<th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Pay Pal Num.<br/><br/><br/></th>
	    	<th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Transferencia Num.</th>
	    	<th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Metálico Num.</th>
	    	<th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Sin datos Num.</th>									
						
		<!--<th class="textoAzul7L" valign="top">19<br /><br />Pay Pal <br/>&nbsp;%<br/><br/><b> &nbsp;&nbsp;€</b></th>-->							
					 <th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Gastos cobro cuotas<br/><br/><b> &nbsp;&nbsp;€</b></th>
						
						
	     <th class="textoAzul7L" valign="top"><b><?php echo $n++;?><br /><br />TOTALES CUOTAS PAGADAS</b> <br/><br/><b>&nbsp;&nbsp;&nbsp;€</b></th>  						
					</tr>					

     <?php 
					//echo "<br><br>totalesPagosCuotaAgrupAnio:";print_r($totalesPagosCuotaAgrupAnio);
					$items = $totalesPagosCuotaAgrupAnio['resultadoFilas'];
     //echo "<br><br>items:";print_r($items);
     foreach ($items as $agrup => $fila)
	  	 {echo ("<tr height='30'>");          

						echo ("<td class='textoAzul7L' bgcolor='#F2F3F4'>".$agrup."</td>");
						echo ("<td class='textoAzul7R' bgcolor='#fcfee9'>".$items[$agrup]['numSociosAnio']."</td>");						
						
						echo ("<td class='textoAzul7R'>".$items[$agrup]['numGeneral']."</td>"); 
						echo ("<td class='textoAzul7R'>".$items[$agrup]['numParado']."</td>"); 
						echo ("<td class='textoAzul7R'>".$items[$agrup]['numJoven']."</td>"); 
						echo ("<td class='textoAzul7R'>".$items[$agrup]['numHonorario']."</td>"); 						
						
      echo ("<td class='textoAzul7R' bgcolor='#F2F3F4'>".number_format($items[$agrup]['totalAnioCuotasEL'],0,",",".")."</td>");       						 
						echo ("<td class='textoAzul7R' bgcolor='#fcfee9'>".$items[$agrup]['numSociosAnioAbonada']."</td>"); 
      echo ("<td class='textoAzul7R' bgcolor='#ccffcc'>"."+".number_format($items[$agrup]['totalAnioAbonada'],0,",",".")."</td>"); 										
						
  				if ($items[$agrup]['totalAnioAbonada']-$items[$agrup]['totalAnioCuotasEL']< 0)		
						{echo ("<td class='textoRojo7Right'>".number_format(($items[$agrup]['totalAnioAbonada']-$items[$agrup]['totalAnioCuotasEL']),0,",",".")."</td>");}
						else 
						{echo ("<td class='textoAzul7R'>".number_format(($items[$agrup]['totalAnioAbonada']-$items[$agrup]['totalAnioCuotasEL']),0,",",".")."</td>");}

						echo ("<td class='textoRojo7Right' bgcolor='#fcfee9'>".$items[$agrup]['numSociosNoAbonadaAnio_y_PendientesCobro']."</td>");     
      echo ("<td class='textoRojo7Right' bgcolor='#ffcccc'>"."-".number_format($items[$agrup]['totalAnioNoAbonada_y_PendienteCobro'],0,",",".")."</td>"); 
						
	    	echo ("<td class='textoRojo7Right' bgcolor='#fcfee9'>".number_format($items[$agrup]['numSociosErrorCuenta'] + $items[$agrup]['numSociosDevuelta'],0,",",".")."</td>");
						echo ("<td class='textoRojo7Right' bgcolor='#ffcccc'>".number_format($items[$agrup]['totalAnioDevuelta']+$items[$agrup]['totalAnioErrorCuenta'],0,",",".")."</td>");
      //echo ("<td class='textoRojo7Right' bgcolor='#fcfee9'>".$items[$agrup]['numSociosErrorCuenta']."</td>");
						//echo ("<td class='textoRojo7Right' bgcolor='#ffcccc'>"."-".number_format($items[$agrup]['totalAnioErrorCuenta'],0,",",".")."</td>");
	     //echo ("<td class='textoRojo7Right' bgcolor='#fcfee9'>".$items[$agrup]['numSociosDevuelta']."</td>");
						//echo ("<td class='textoRojo7Right' bgcolor='#ffcccc'>"."-".number_format($items[$agrup]['totalAnioDevuelta'],0,",",".")."</td>"); 
						
	     echo ("<td class='textoAzul7R' bgcolor='#fcfee9'>".$items[$agrup]['numSociosAbonaParte']."</td>");
						echo ("<td class='textoAzul7R' bgcolor='#ccffcc'>"."+".number_format($items[$agrup]['totalAnioAbonaParte'],0,",",".")."</td>");
	     echo ("<td class='textoAzul7R' bgcolor='#fcfee9'>".$items[$agrup]['numSociosAnioExentos']."</td>");
						echo ("<td class='textoAzul7R' bgcolor='#ccffcc'>"."+".number_format($items[$agrup]['totalSociosAnioExentos'],0,",",".")."</td>");								
						echo ("<td class='textoAzul7R' bgcolor='#fcfee9'>".$items[$agrup]['numSociosAnioCuotaDonacion']."</td>");     
      echo ("<td class='textoAzul7R' bgcolor='#ccffcc'>"."+".number_format($items[$agrup]['totalAnioCuotaDonacion'],0,",",".")."</td>");

						echo ("<td class='textoRojo7Right' bgcolor='#fcfee9'>".$items[$agrup]['numDOMICILIADA']."</td>");     
      //echo ("<td class='textoRojo7Right' bgcolor='#ffcccc'>"."-".number_format($items[$anio]['totalDOMICILIADA'],0,",",".")."</td>");					
						echo ("<td class='textoRojo7Right' bgcolor='#fcfee9'>".$items[$agrup]['numPAYPAL']."</td>");     
      //echo ("<td class='textoRojo7Right' bgcolor='#ffcccc'>"."-".number_format($items[$anio]['totalPAYPAL'],0,",",".")."</td>");						
						echo ("<td class='textoRojo7Right' bgcolor='#fcfee9'>".$items[$agrup]['numTRANSFERENCIA']."</td>");     
      //echo ("<td class='textoRojo7Right' bgcolor='#ffcccc'>"."-".number_format($items[$anio]['totalTRANSFERENCIA'],0,",",".")."</td>");						
						echo ("<td class='textoRojo7Right' bgcolor='#fcfee9'>".$items[$agrup]['numMETALICO']."</td>");     
      //echo ("<td class='textoRojo7Right' bgcolor='#ffcccc'>"."-".number_format($items[$anio]['totalMETALICO'],0,",",".")."</td>");						
						echo ("<td class='textoRojo7Right' bgcolor='#fcfee9'>".$items[$agrup]['numSIN-DATOS']."</td>");     
      //echo ("<td class='textoRojo7Right' bgcolor='#ffcccc'>"."-".number_format($items[$anio]['totalSIN-DATOS'],0,",",".")."</td>");

				  //echo ("<td class='textoGris7Right'>".number_format($items[$agrup]['totalAnioDefictGastosPayPal'],0,",",".")."</td>");
					 echo ("<td class='textoGris7Right' bgcolor='#ffcccc'>".number_format($items[$agrup]['totalGastosPagosAnio'],2,",",".")."</td>");
						
					 echo ("<td class='textoAzul8Right' bgcolor='#ccffcc' ><b>".number_format($items[$agrup]['IMPORTECUOTAANIOPAGADA'],2,",",".")."</b></td>");
						
      echo ("</tr>");
     }	
					?>
  					<?php $n= 1;
					?>
    <tr bgcolor="#CCCCCC">			
						<th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Agrupación</th>
      <th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Soci@s Num. Total</th> 
						<th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />General Num.</th>
						<th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Parado Num.</th> 	
						<th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Joven Num.</th> 	
						<th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Honorario Num.</th> 
						
      <th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Cuotas EL<br/><br/><b> &nbsp;&nbsp;€</b></th>						
      <th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Abonan Num.</th>
						<th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Cuotas Abonadas<br/><br/><b> &nbsp;&nbsp;€</b></th>	
						<th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Saldo cuotas&nbsp;&nbsp;&nbsp;&nbsp;<br/><br/><b> &nbsp;&nbsp;€</b></th>
      <th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />NO Abon. +<br/>Pend. Cobro Num.</th>
						<th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />NO Abon. + Pend. Cobro<br/><b><br/>&nbsp;&nbsp;€</b></th>	
						
						<th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Devuelta y <br />Error CC Num.</th>					
						<th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Devuelta y <br />Error CC<br/><b> &nbsp;&nbsp;€</b></th>	
						<!--<th class="textoAzul7L" valign="top"><br /><br />Error CC Num.</th>					
						<th class="textoAzul7L" valign="top"><br /><br />Total Error CC<br/><br/><b> &nbsp;&nbsp;€</b></th>		
						<th class="textoAzul7L" valign="top"><br /><br />De- vuelta Num.</th>					
						<th class="textoAzul7L" valign="top"><br /><br />De- vuelta<br/><br/><b> &nbsp;&nbsp;€</b></th>	-->	
						
						<th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Abona Parte Num.</th>					
						<th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Abona Parte<br/><br/><b> &nbsp;&nbsp;€</b></th>		
						<th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Exent Num.</th>	
						<th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Exento paga<br/><br/><b> &nbsp;&nbsp;€</b></th>										
      <th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Dona en <br/>la cuota Num.</th> 
      <th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Dona en la cuota<br/><br/><b> &nbsp;&nbsp;€</b></th>  
						
	    	<th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Domiciliada Num.<br/><br/><br/></th>
	    	<th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Pay Pal Num.<br/><br/><br/></th>
	    	<th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Transferencia Num.</th>
	    	<th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Metálico Num.</th>
	    	<th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Sin datos Num.</th>				
				 <!-- <th class="textoAzul7L" valign="top">19<br /><br />Pay Pal <br/>&nbsp;%<br/><br/><b> &nbsp;&nbsp;€</b></th>		-->
					<th class="textoAzul7L" valign="top"><?php echo $n++;?><br /><br />Gastos cobro cuotas<br/><br/><b> &nbsp;&nbsp;€</b></th>
	     <th class="textoAzul7L" valign="top"><b><?php echo $n++;?><br /><br />TOTALES INGRESOS</b> <br/><br/><b>&nbsp;&nbsp;&nbsp;€</b></th>  						
					</tr>						
					
					<?php	
							
     $totales = $totalesPagosCuotaAgrupAnio['totales'];
     //echo "<br><br>items:";print_r($items);
			
					 echo ("	<tr height='30' bgcolor='#dcecfc'>");
	     echo ("<td class='textoAzul7L'><b>TOTALES</b></td>");
				  echo ("<td class='textoAzul8Right'><b>".$totales['numSociosAnio']."</b></td>"); 

				  echo ("<td class='textoAzul8Right'><b>".$totales['numGeneral']."</b></td>"); 	
				  echo ("<td class='textoAzul8Right'><b>".$totales['numParado']."</b></td>"); 	
				  echo ("<td class='textoAzul8Right'><b>".$totales['numJoven']."</b></td>"); 	
				  echo ("<td class='textoAzul8Right'><b>".$totales['numHonorario']."</b></td>"); 							


						
						echo ("<td class='textoAzul8Right'><b>".number_format($totales['totalAnioCuotasEL'],0,",",".")."</b></td>");						
						echo ("<td class='textoAzul8Right'><b>".$totales['numSociosAnioAbonada']."</b></td>"); 
      echo ("<td class='textoAzul8Right'><b>".number_format($totales['totalAnioAbonada'],0,",",".")."</b></td>");						
						      						 
  				if ($totales['totalAnioAbonada']-$totales['totalAnioCuotasEL']< 0)		
						{echo ("<td class='textoRojo8Right'><b>".number_format(($totales['totalAnioAbonada']-$totales['totalAnioCuotasEL']),0,",",".")."</b></td>");}
						else 
						{echo ("<td class='textoAzul8Right'><b>".number_format(($totales['totalAnioAbonada']-$totales['totalAnioCuotasEL']),0,",",".")."</b></td>");}

						echo ("<td class='textoAzul8Right'><b>".$totales['numSociosNoAbonadaAnio_y_PendientesCobro']."</b></td>");     
      echo ("<td class='textoRojo8Right'><b>".number_format($totales['totalAnioNoAbonada_y_PendienteCobro'],0,",",".")."</b></td>");

 					echo ("<td class='textoRojo8Right'><b>".number_format($totales['numSociosErrorCuenta'] + $totales['numSociosDevuelta'],0,",",".")."</b></td>");
						echo ("<td class='textoRojo8Right'><b>".number_format($totales['totalAnioErrorCuenta'] + $totales['totalAnioDevuelta'],0,",",".")."</b></td>");

      //echo ("<td class='textoRojo8Right'><b>".$totales['numSociosErrorCuenta']."</b></td>");
						//echo ("<td class='textoAzul8Right'><b>".number_format($totales['totalAnioErrorCuenta'],0,",",".")."</b></td>");				 	     
      //echo ("<td class='textoRojo8Right'><b>".$totales['numSociosDevuelta']."</b></td>");
						//echo ("<td class='textoAzul8Right'><b>".number_format($totales['totalAnioDevuelta'],0,",",".")."</b></td>"); 
					      
      echo ("<td class='textoAzul8Right'><b>".$totales['numSociosAbonaParte']."</b></td>");
						echo ("<td class='textoAzul8Right'><b>".number_format($totales['totalAnioAbonaParte'],0,",",".")."</b></td>");
						
      echo ("<td class='textoAzul8Right'><b>".$totales['numSociosAnioExentos']."</b></td>");
						echo ("<td class='textoAzul8Right'><b>".number_format($totales['totalSociosAnioExentos'],0,",",".")."</b></td>");														
						
					 echo ("<td class='textoAzul8Right'><b>".$totales['numSociosAnioCuotaDonacion']."</b></td>");     
      echo ("<td class='textoAzul8Right'><b>".number_format($totales['totalAnioCuotaDonacion'],0,",",".")."</b></td>"); 
						
					 //echo ("<td class='textoGris7Right'>".number_format($totales['totalAnioDefictGastosPayPal'],0,",",".")."</td>");
					
						
						//---
					 echo ("<td class='textoAzul8Right'><b>".$totales['numDOMICILIADA']."</b></td>");
					 echo ("<td class='textoAzul8Right'><b>".$totales['numPAYPAL']."</b></td>"); 
					 echo ("<td class='textoAzul8Right'><b>".$totales['numTRANSFERENCIA']."</b></td>");
					 echo ("<td class='textoAzul8Right'><b>".$totales['numMETALICO']."</b></td>");
					 echo ("<td class='textoAzul8Right'><b>".$totales['numSIN-DATOS']."</b></td>"); 
		    //---				
						      
      echo ("<td class='textoRojo8Right'><strong>".number_format($totales['totalGastosPagosAnio'],2,",",".")."</strong></td>");						
      echo ("<td class='textoAzul8Right'><strong>".number_format($totales['IMPORTECUOTAANIOPAGADA'],2,",",".")."</strong></td>"); 
     echo ("</tr>");		
    ?>
    </table> 
			 </div> 
    <!-- ************************ Fin datos socios ************************* -->						
				</td>  		
	  </tr>
			<!-- ************************ Inicio paginación  ************************ -->					
			<tr>  
			 <td align="center">
    <?php  
					//echo "<span class='textoAzul9C'>".$resCuotasSocios['_pag_navegacion']."</span>";
     ?>				
    </td>
   </tr>	
		<!-- ************************ Fin paginación  *************************** -->
  </table>   
		
</div>		
