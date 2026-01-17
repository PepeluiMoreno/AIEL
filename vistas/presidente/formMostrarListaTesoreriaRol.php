<?php
/*--------------------------------------------------------------------------------------------------
FICHERO: formMostrarListaTesoreriaRol.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Se forma y muestra una tabla con la lista actual de socios con el rol 
de Tesorería  y sus datos personales 

LLAMADA: vistas/presidente/vCuerpoMostrarListaTesoreriaRol.php
y previamente desde  cPresidente.php:mostrarListaTesoreriaRol(),
												
OBSERVACIONES:

--------------------------------------------------------------------------------------------------*/
?>

<div id="registro">

 <br />
	<span class='textoNegro8Left'>

	- En la lista está incluidas socias/os con  el rol de Tesorería
	</span>
	<br /> 	<br /> 
	 <table width="100%" border="0px" bordercolor="#FFFFFF" cellspacing="0" cellpadding="0">
   <tr>
			 <td>
				<div align="left">
					<!-- ******************** Inicio informa num. pag y líneas ***************** --> 
					<!-- <span class='textoAzulClaro8L'><?php echo $resCuotasSocios['_pag_info']; ?></span>	 -->
					<!-- ******************** Inicio informa. num. pag y líneas ***************** -->			
    <table width="100%" border="1" cellspacing="0" cellpadding="0" bordercolor="#99CCFF">
     <tr bgcolor="#CCCCCC">			

						<th valign="top" class="textoAzul8L"><strong>Nombre</strong></th> 						
      <th valign="top" class="textoAzul8L"><strong>Email personal</strong></th> 
      <th valign="top" class="textoAzul8L"><strong>Tel. fijo</strong><br /></th>  

						<th valign="top" class="textoAzul8L"><strong>Tel. móvil</strong></th> 
						<th valign="top" class="textoAzul8L"><strong>Provincia</strong></th> 
																	
						<th valign="top" class="textoAzul8L"><strong>Localidad</strong></th> 
						<th valign="top" class="textoAzul8L"><strong>CP</strong></th> 
						<th valign="top" class="textoAzul8L"><strong>Dirección</strong></th> 

     </tr>	
     
     <?php 				
	
					$items = $datosSociosTesoreriaRol['resultadoFilas'];
					
     //echo "<br><br>items:";print_r($items);
					
     foreach ($items as $fila => $contenidoFila)
	  	 { 
					 echo ("<tr height='30'>"); 

					 echo ("<td class='textoAzul8L'><strong>&nbsp;".$items[$fila]['APE1']." &nbsp;".$items[$fila]['APE2'].", &nbsp;".$items[$fila]['NOM']."</strong></td>");
						
					 echo ("<td class='textoAzul8L' bgcolor='#dcecfc'>".$items[$fila]['EMAIL']."</td>");				
					 echo ("<td class='textoAzul8L'>&nbsp;".$items[$fila]['TELFIJOCASA']."&nbsp;</td>");						
					 echo ("<td class='textoAzul8L'>&nbsp;".$items[$fila]['TELMOVIL']."&nbsp;</td>");  						
 						
					 echo ("<td class='textoAzul8L' bgcolor='#dcecfc'>&nbsp;<strong>".$items[$fila]['NOMPROVINCIA']."</strong></td>"); 
					 echo ("<td class='textoAzul8L'>".$items[$fila]['LOCALIDAD']."</td>");
					 echo ("<td class='textoAzul8L'>".$items[$fila]['CP']."</td>"); 
						echo ("<td class='textoAzul8L'>".$items[$fila]['DIRECCION']."</td>");			
 
      echo ("</tr>");
      }
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
		<!-- ************************ Fin filas  *************************** -->
  </table>   
</div>		
