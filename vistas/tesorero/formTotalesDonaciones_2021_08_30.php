<?php
/*----------------------------------------------------------------------------------------------------
FICHERO: formTotalesDonaciones.php
VERSION: PHP 7.3.21

DESCRIPCION: Contiene el formulario que muesta la tabla "TOTALES DONACIONES". Decreciente por años. 
Entre otros campos incluye: 
Nº total donantes, 	Tipo de donante (socios, donantes identificados, anónimos) , Modo de ingreso, 
Gastos donación, Total donaciones €,      

LLAMADA: vistas/tesorero/vCuerpoTotalesDonaciones.php

OBSERVACIONES:
--------------------------------------------------------------------------------------------------*/
?>

<div id="registro">

	<span class='textoNegro8Left'>Donantes pueden ser: socios/as, donantes anónimos/as y 
	  donantes identificados/as (donantes con nombre pero no socios/as)
	</span>
	<br /> 	<br /> 
	
				<div align="left">
					<!-- ******************** Inicio informa num. pag y líneas ***************** --> 					
					<!-- <span class='textoAzulClaro8L'><?php //echo $resCuotasSocios['_pag_info']; ?></span>	 -->
					<!-- ******************** Inicio informa. num. pag y líneas ***************** -->	
					
    <table width="100%" border="1" cellspacing="0" cellpadding="0" bordercolor="#99CCFF">
    
				<!-- ******************** Inicio fila cabecera ********************** -->	
				<tr bgcolor="#CCCCCC">			
						<th valign="top" class="textoAzul8L">&nbsp;&nbsp;&nbsp;<strong>Año</strong>&nbsp;&nbsp;&nbsp;</th>
						<th valign="top" class="textoAzul8L"><b>&nbsp;&nbsp;&nbsp;Nº total donantes</b></th> 

						<th valign="top" class="textoAzul8L">Nº donantes socios/as</th>
						<th valign="top" class="textoAzul8L">Total donantes socios/as<br /><br />&nbsp;&nbsp;&nbsp;&nbsp; €</th>	
						<th valign="top" class="textoAzul8L">Nº donantes identificados/as<br />(sin asociar)</th>					
						<th valign="top" class="textoAzul8L">Total donantes identificados/as<br />(sin asociar)<br />&nbsp;&nbsp;&nbsp;&nbsp; €</th>
      <th valign="top" class="textoAzul8L">Nº donantes anónimos/as</th> 
      <th valign="top" class="textoAzul8L">Total donaciones anónimos/as<br /><br />&nbsp;&nbsp;&nbsp;&nbsp; €</th>  

						<th valign="top" class="textoAzul8L">Transferencia&nbsp;&nbsp;&nbsp;</th> 
						<th valign="top" class="textoAzul8L">PayPal&nbsp;&nbsp;&nbsp;</th> 
						<!--<th valign="top" class="textoAzul8L">Total PayPal&nbsp;&nbsp;&nbsp;</th>-->
											
						<th valign="top" class="textoAzul8L">Metálico&nbsp;&nbsp;&nbsp;</th> 
						<th valign="top" class="textoAzul8L">Cheque&nbsp;&nbsp;&nbsp;</th> 
						<th valign="top" class="textoAzul8L">Tarjeta&nbsp;&nbsp;&nbsp;</th> 
						<th valign="top" class="textoAzul8L">Domiciliada&nbsp;&nbsp;&nbsp;</th> 
						<th valign="top" class="textoAzul8L">Sin datos&nbsp;&nbsp;&nbsp;</th> 
      <th valign="top" class="textoAzul8L">Gastos PayPal y otros&nbsp;&nbsp;&nbsp;</th> 	
						<th valign="top" class="textoAzul8L"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total donaciones&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br /><br />&nbsp;&nbsp;&nbsp;&nbsp; €</b></th>							
     </tr>
	    <!-- ******************** Fin fila cabecera ************************* -->	
     
					<!-- ******************** Inicio filas datos ************************ -->	
     <?php 
					//echo "<br><br>totalesAniosPagosDonaciones:";print_r($totalesAniosPagosDonaciones);
					$items = $totalesAniosPagosDonaciones['resultadoFilas'];
     //echo "<br><br>items:";print_r($items);
					
     foreach ($items as $anio => $fila)
	  	 { 
					 echo ("<tr height='30'>");          

						echo ("<td class='textoAzul7L' bgcolor='#F2F3F4'><strong>".$anio."</strong></td>");
						
						echo ("<td class='textoAzul8R'><b>".$items[$anio]['numDonantesAnio']."</b></td>");						
																
      echo ("<td class='textoAzul8R'>".$items[$anio]['numDonantesSociosAnio']."</td>");
      echo ("<td class='textoAzul8R' bgcolor='#ccffcc'>".number_format($items[$anio]['totalDonacionesSociosAnio'],2,",",".")."</td>");
													
					 echo ("<td class='textoAzul8R'>".$items[$anio]['numDonantesIdentificados']."</td>");     
      echo ("<td class='textoAzul8R' bgcolor='#ccffcc'>".number_format($items[$anio]['totalDonacionesIdentificados'],2,",",".")."</td>"); 
						
					 echo ("<td class='textoAzul8R'>".$items[$anio]['numDonantesAnonimos']."</td>");     
      echo ("<td class='textoAzul8R' bgcolor='#ccffcc'>".number_format($items[$anio]['totalDonacionesAnonimos'],2,",",".")."</td>");
						
					 echo ("<td class='textoAzul8R' bgcolor='#dcecfc'>".$items[$anio]['numTRANSFERENCIA']."</td>");
					 // echo ("<td class='textoAzul8R'>".$items[$anio]['totalTRANSFERENCIA']."</td>");  						
					 echo ("<td class='textoAzul8R'>".$items[$anio]['numPAYPAL']."</td>");  						
					 //echo ("<td class='textoAzul8R'>".$items[$anio]['totalPAYPAL']."</td>"); 
						
					 echo ("<td class='textoAzul8R' bgcolor='#dcecfc'>".$items[$anio]['numMETALICO']."</td>");  						
					 //echo ("<td class='textoAzul8R'>".$items[$anio]['totalMETALICO']."</td>");  						
					 echo ("<td class='textoAzul8R'>".$items[$anio]['numCHEQUE']."</td>");  						
					 //echo ("<td class='textoAzul8R'>".$items[$anio]['totalCHEQUE']."</td>");  						
					 echo ("<td class='textoAzul8R' bgcolor='#dcecfc'>".$items[$anio]['numTARJETA']."</td>");  						
					 //echo ("<td class='textoAzul8R'>".$items[$anio]['totalTARJETA']."</td>");  						
					 echo ("<td class='textoAzul8R'>".$items[$anio]['numDOMICILIADA']."</td>");  						
					 //echo ("<td class='textoAzul8R'>".$items[$anio]['totalDOMICILIADA']."</td>");
					 echo ("<td class='textoAzul8R' bgcolor='#dcecfc'>".$items[$anio]['numSIN-DATOS']."</td>");  						
					 //echo ("<td class='textoAzul8R'>".$items[$anio]['totalSIN-DATOS']."</td>"); 		
      echo ("<td class='textoAzul8R' bgcolor='#ffcccc'>".number_format($items[$anio]['totalGASTOSDONACION'],2,",",".")."</td>");						

      echo ("<td class='textoAzul8R' bgcolor='#ccffcc'><b>".number_format($items[$anio]['totalAnioDonaciones'],2,",",".")."</b></td>"); 
						

      echo ("</tr>");
      }
      ?>
						<!-- ******************** Fin filas datos **************************** -->	
    </table> 
			</div> 
    			
				</td>  		
	  </tr>
</div>		
