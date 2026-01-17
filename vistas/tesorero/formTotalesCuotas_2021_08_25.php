<?php
/*--------------------------------------------------------------------------------------------------
FICHERO: formTotalesCuotas.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: 
Se forma y muestra una tabla "TOTALES CUOTAS SOCIOS" con el resumen de los totales de las cuotas 
pagadas y pendientes de los socios, y deglosadas en otros detalles, hasta la fecha actual.
 
Orden decreciente por años. 
Desde la última columna de la tabla "lupa", se podrá llamar a un función, para ver para cada año 
los totales pagos cuotas por cada agrupación 

LLAMADA: vistas/presidente/vCuerpoTotalesCuotas.php
y previamente desde vistas/tesorero/vMostrarIngresosCuotas.php con botón "Totales pagos cuotas por años" 

OBSERVACIONES: 					
--------------------------------------------------------------------------------------------------*/
?>
<div id="registro">
	
		<a class='textoAzul9Left' href="./index.php?controlador=cTesorero&amp;accion=infTotalesCuotas" 
				   target="ventana1" title="Información sobre el significado de las columnas" 
							onclick="window.open('','ventana1','width=800,height=600,scrollbars=yes')">
							>>Ver aclaraciones sobre el significado de las columnas         
 </a> 
<br />
		<div align="left">
					<!-- ******************** Inicio informa num. pag y líneas ***************** --> 
					<!--<span class='textoAzulClaro8L'><?php echo $resCuotasSocios['_pag_info']; ?></span>	-->
					<!-- ******************** Fin informa. num. pag y líneas ***************** -->	
					
				<!-- ************************ Inicio tabla totales cuotas ************************* -->		
					<!-- ******************** Inicio fila cabecera tabla ********************************* --> 	
    <table  width="100%" border="1" cellspacing="0" cellpadding="0" bordercolor="#99CCFF">
     <tr bgcolor="#CCCCCC">			
						<th rowspan='2' class="textoAzul7L" valign="top">1<br /><br />Año</th>
      <th rowspan='2' class="textoAzul7L" valign="top">2<br /><br />Soci@s Num. Total</th> 
						<th rowspan='2' class="textoAzul7L" valign="top">3<br /><br />General Num.</th>
						<th rowspan='2' class="textoAzul7L" valign="top">4<br /><br />Parado Num.</th> 	
						<th rowspan='2' class="textoAzul7L" valign="top">5<br /><br />Joven Num.</th> 	
						<th rowspan='2' class="textoAzul7L" valign="top">6<br /><br />Honorario Num.</th> 	 						
				
      <th rowspan='2' class="textoAzul7L" valign="top">7<br /><br />Cuotas EL<br/><br/><b> &nbsp;&nbsp;€</b></th>						
      <th rowspan='2' class="textoAzul7L" valign="top">8<br /><br />Abonan Num.</th>
						<th rowspan='2' class="textoAzul7L" valign="top">9<br /><br />Cuotas Abonadas<br/><br/><b> &nbsp;&nbsp;€</b></th>	
						<th rowspan='2' class="textoAzul7L" valign="top">10<br /><br />Saldo cuotas&nbsp;&nbsp;&nbsp;&nbsp;<br/><br/><b> &nbsp;&nbsp;€</b></th>
      <th rowspan='2' class="textoAzul7L" valign="top">11<br /><br />NO Abon. +<br/>Pend. Cobro Num.</th>
						<th rowspan='2' class="textoAzul7L" valign="top">12<br /><br />NO Abon. + Pend. Cobro<br/><br/><b>&nbsp;&nbsp;€</b></th>	
						<!-- <th rowspan='2' class="textoAzul7L" valign="top">13<br /><br />Error CC Num.</th>					
						<th rowspan='2' class="textoAzul7L" valign="top">14<br /><br />Total Error CC<br/><br/><b> &nbsp;&nbsp;€</b></th>		
						<th rowspan='2' class="textoAzul7L" valign="top">15<br /><br />De- vuelta Num.</th>					
						<th rowspan='2' class="textoAzul7L" valign="top">16<br /><br />De- vuelta<br/><br/><b> &nbsp;&nbsp;€</b></th> -->
												
						<th rowspan='2' class="textoAzul7L" valign="top">13<br /><br />Devuelta y <br />Error CC Num.</th>					
						<th rowspan='2' class="textoAzul7L" valign="top">14<br /><br />Devuelta y <br />Error CC<br/><b> &nbsp;&nbsp;€</b></th>
						
						<th rowspan='2' class="textoAzul7L" valign="top">15<br /><br />Abona Parte Num.</th>					
						<th rowspan='2' class="textoAzul7L" valign="top">16<br /><br />Abona Parte<br/><br/><b> &nbsp;&nbsp;€</b></th>	
	     
						<th rowspan='2' class="textoAzul7L" valign="top">17<br /><br />Exent Num.</th>	
						<th rowspan='2' class="textoAzul7L" valign="top">18<br /><br />Exento paga<br/><br/><b> &nbsp;&nbsp;€</b></th>						

      <th rowspan='2' class="textoAzul7L" valign="top">19<br /><br />Dona en <br />la cuota Num.</th> 
      <th rowspan='2' class="textoAzul7L" valign="top">20<br /><br />Dona en la cuota<br/><br/><b> &nbsp;&nbsp;€</b></th>  
	    	<th rowspan='2' class="textoAzul7L" valign="top">21<br /><br />Domiciliada Num.<br/><br/><br/></th>
	    	<th rowspan='2' class="textoAzul7L" valign="top">22<br /><br />Pay Pal Num.<br/><br/><br/></th>
	    	<th rowspan='2' class="textoAzul7L" valign="top">23<br /><br />Transferencia Num.</th>
	    	<th rowspan='2' class="textoAzul7L" valign="top">24<br /><br />Metálico Num.</th>
	    	<th rowspan='2' class="textoAzul7L" valign="top">25<br /><br />Sin datos Num.</th>				
					 <th rowspan='2' class="textoAzul7L" valign="top">26<br /><br />Gastos cobro cuotas<br/><br/><b> &nbsp;&nbsp;€</b></th>						
	     <th rowspan='2' class="textoAzul7L" valign="top"><b>27<br /><br />TOTALES CUOTAS PAGADAS</b> <br/><br/><b>&nbsp;&nbsp;&nbsp;€</b></th>  						
      <th class="textoAzul7L">Ver</th>  
     </tr>
     <tr bgcolor="#CCCCCC">
      <th class="textoAzul7L">Por agrup</th>
     </tr>  
					<!-- ******************** Fin fila cabecera tabla ********************************* --> 	
      
     <?php 
					$items = $totalesAniosPagosCuota['resultadoFilas'];
     //echo "<br><br>items:";print_r($items);
					//-- ******************** Inicio foreach ***************** 
     foreach ($items as $anio => $fila)
	  	 { 
					 echo ("<tr height='40'>");          

						echo ("<td class='textoAzul7L' bgcolor='#F2F3F4'>".$anio."</td>");
						echo ("<td class='textoAzul7R' bgcolor='#fcfee9'>".$items[$anio]['numSociosAnio']."</td>"); 
						echo ("<td class='textoAzul7R'>".$items[$anio]['numGeneral']."</td>"); 
						echo ("<td class='textoAzul7R'>".$items[$anio]['numParado']."</td>"); 
						echo ("<td class='textoAzul7R'>".$items[$anio]['numJoven']."</td>"); 
						echo ("<td class='textoAzul7R'>".$items[$anio]['numHonorario']."</td>"); 
						
	     //echo ("<td class='textoAzul7R'>".$items[$anio]['numSociosAnioExentos']."</td>");
				  //echo ("<td class='textoAzul7R'>"."+".number_format($items[$anio]['totalSociosAnioExentos'],0,",",".")."</td>");														
      echo ("<td class='textoAzul7R' bgcolor='#F2F3F4'>".number_format($items[$anio]['totalAnioCuotasEL'],0,",",".")."</td>");       						 
						echo ("<td class='textoAzul7R' bgcolor='#fcfee9'>".$items[$anio]['numSociosAnioAbonada']."</td>"); 
      echo ("<td class='textoAzul7R' bgcolor='#ccffcc'>"."+".number_format($items[$anio]['totalAnioAbonada'],0,",",".")."</td>"); 										
						
  				if ($items[$anio]['totalAnioAbonada']-$items[$anio]['totalAnioCuotasEL']< 0)		
						{echo ("<td class='textoRojo7Right'>".number_format(($items[$anio]['totalAnioAbonada']-$items[$anio]['totalAnioCuotasEL']),0,",",".")."</td>");}
						else 
						{echo ("<td class='textoAzul7R'>".number_format(($items[$anio]['totalAnioAbonada']-$items[$anio]['totalAnioCuotasEL']),0,",",".")."</td>");}

						echo ("<td class='textoRojo7Right' bgcolor='#fcfee9'>".$items[$anio]['numSociosNoAbonadaAnio_y_PendientesCobro']."</td>");     
      echo ("<td class='textoRojo7Right' bgcolor='#ffcccc'>"."-".number_format($items[$anio]['totalAnioNoAbonada_y_PendienteCobro'],0,",",".")."</td>"); 
						
      //echo ("<td class='textoRojo7Right' bgcolor='#fcfee9'>".$items[$anio]['numSociosErrorCuenta']."</td>");
						//echo ("<td class='textoRojo7Right' bgcolor='#ffcccc'>"."-".number_format($items[$anio]['totalAnioErrorCuenta'],0,",",".")."</td>");
	     //echo ("<td class='textoRojo7Right' bgcolor='#fcfee9'>".$items[$anio]['numSociosDevuelta']."</td>");
						//echo ("<td class='textoRojo7Right' bgcolor='#ffcccc'>"."-".number_format($items[$anio]['totalAnioDevuelta'],0,",",".")."</td>");	

	    	echo ("<td class='textoRojo7Right' bgcolor='#fcfee9'>".number_format($items[$anio]['numSociosErrorCuenta'] + $items[$anio]['numSociosDevuelta'],0,",",".")."</td>");
						echo ("<td class='textoRojo7Right' bgcolor='#ffcccc'>".number_format($items[$anio]['totalAnioDevuelta']+$items[$anio]['totalAnioErrorCuenta'],0,",",".")."</td>");						
						
	     echo ("<td class='textoAzul7R' bgcolor='#fcfee9'>".$items[$anio]['numSociosAbonaParte']."</td>");
						echo ("<td class='textoAzul7R' bgcolor='#ccffcc'>"."+".number_format($items[$anio]['totalAnioAbonaParte'],0,",",".")."</td>");		

	     echo ("<td class='textoAzul7R' bgcolor='#fcfee9'>".$items[$anio]['numSociosAnioExentos']."</td>");
						echo ("<td class='textoAzul7R' bgcolor='#ccffcc'>"."+".number_format($items[$anio]['totalSociosAnioExentos'],0,",",".")."</td>");									
	 
						echo ("<td class='textoAzul7R' bgcolor='#fcfee9'>".$items[$anio]['numSociosAnioCuotaDonacion']."</td>");     
      echo ("<td class='textoAzul7R' bgcolor='#ccffcc'>"."+".number_format($items[$anio]['totalAnioCuotaDonacion'],0,",",".")."</td>"); 
				//echo ("<td class='textoGris7Right'>".number_format($items[$anio]['totalAnioDefictGastosPayPal'],0,",",".")."</td>");
				  
				 //---
						echo ("<td class='textoRojo7Right' bgcolor='#fcfee9'>".$items[$anio]['numDOMICILIADA']."</td>");     
      //echo ("<td class='textoRojo7Right' bgcolor='#ffcccc'>"."-".number_format($items[$anio]['totalDOMICILIADA'],0,",",".")."</td>");					
						echo ("<td class='textoRojo7Right' bgcolor='#fcfee9'>".$items[$anio]['numPAYPAL']."</td>");     
      //echo ("<td class='textoRojo7Right' bgcolor='#ffcccc'>"."-".number_format($items[$anio]['totalPAYPAL'],0,",",".")."</td>");						
						echo ("<td class='textoRojo7Right' bgcolor='#fcfee9'>".$items[$anio]['numTRANSFERENCIA']."</td>");     
      //echo ("<td class='textoRojo7Right' bgcolor='#ffcccc'>"."-".number_format($items[$anio]['totalTRANSFERENCIA'],0,",",".")."</td>");						
						echo ("<td class='textoRojo7Right' bgcolor='#fcfee9'>".$items[$anio]['numMETALICO']."</td>");     
      //echo ("<td class='textoRojo7Right' bgcolor='#ffcccc'>"."-".number_format($items[$anio]['totalMETALICO'],0,",",".")."</td>");						
						echo ("<td class='textoRojo7Right' bgcolor='#fcfee9'>".$items[$anio]['numSIN-DATOS']."</td>");     
      //echo ("<td class='textoRojo7Right' bgcolor='#ffcccc'>"."-".number_format($items[$anio]['totalSIN-DATOS'],0,",",".")."</td>");				
		   //---
echo ("<td class='textoGris7Right' bgcolor='#ffcccc'>".number_format($items[$anio]['totalGastosPagosAnio'],2,",",".")."</td>");					
					 echo ("<td class='textoAzul8Right' bgcolor='#ccffcc' ><b>".number_format($items[$anio]['IMPORTECUOTAANIOPAGADA'],2,",",".")."</b></td>");
						
      echo ("<td  valign='center'>");
      ?>
       <form method="post" 
          action="./index.php?controlador=cTesorero&accion=mostrarTotalesCuotasAnioAgrup">
        <input type="image" src="./vistas/images/lupa.gif"  value="mostrarTotalCuotasAgrup"
								  alt="Mostrar totales de la cuotas de cada año por agrupaciones" name="Ver"
										title="Mostrar totales de la cuotas de cada año por agrupaciones" align="middle" valign='center'  />
        <input type='hidden' name="datosFormTotalCuotas[ANIOCUOTA]"
         value='<?php echo $anio;?>' /> 									
         
       </form>
      <?php           
       echo ("</td>");
      echo ("</tr>");
      }
						//-- ******************** Fin foreach ***************** 
      ?>
    </table> 
				<!-- ************************ Fin tabla totales cuotas ************************* -->	
	 </div>

			<!-- ************************ Inicio paginación  ************************ -->					
    <?php  
			//		echo "<span class='textoAzul9C'>".$resCuotasSocios['_pag_navegacion']."</span>";
    ?>			
		<!-- ************************ Fin paginación  *************************** -->


</div>		
