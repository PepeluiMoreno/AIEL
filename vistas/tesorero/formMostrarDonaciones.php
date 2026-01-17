<?php
/*-------------------------------------------------------------------------------------------------------
FICHERO: formMostrarDonaciones.php
VERSION: PHP 7.3.21

DESCRIPCION: Muestra una tabla "LISTADO DE LAS DONACIONES " con la lista de las donaciones ordenadas
según LAST-IN ->FIRST-OUT.
Al final de cada fila de una donación, hay iconos con links para acciones sobre
la correspondiente donación con Acciones: Ver,	Modificar, Eliminar

Se forma y muestra una tabla-lista páginada "LISTADO DE LAS DONACIONES " con la lista de las donaciones
ordenadas según LAST-IN ->FIRST-OUT.

Incluye un campo para elegir por AÑO, y otro campo para elegir por APE1, APE2.

Aquí se incluye la paginación de la lista donaciones. En la parte inferior se muestran número de páginas 
para poder ir directamente a un página, anterior, siguiente, primera, última.
La vista correspondiente en forma de tabla, además de de mostrar en cada fila datos sobre las 
donaciones:	Año,	Apellidos, Nombre, etc. al final para cada fila, hay iconos con links para acciones sobre
 la correspondiente donación con Acciones: Ver,	Modificar, Eliminar

En el formulario-tabla, en la parte superior también están los botones: "Anotar donación", "Total donaciones",
"Exportar las donaciones a Excel" y "Mostrar y Añadir Conceptos de Donación" que dirigen a las funciones 
correspondientes dentro de cTesorero.php

LLAMADA: vistas/tesorero/vCuerpoMostrarDonaciones.php
a su vez desde cTesorero.php: mostrarDonaciones() a su vez desde el menú izdo."-	Donaciones"
		
OBSERVACIONES: Se puede cambiar fácilmente de punto decimal a coma decimal para los importes y gastos donación
---------------------------------------------------------------------------------------------------------*/
?>

<div id="registro">	

	 <table width="100%" border="0px" bordercolor="#FFFFFF" cellspacing="0">
		 <tr>
			 <td>
					<div align="center">
					
						<!-- *************** Inicio botones form búsqueda para dirigir a funciones Donación ************* -->	
					 
						<form method="post" class="linea" action="./index.php?controlador=cTesorero&accion=anotarIngresoDonacionMenu">
														<input type="submit" value="Anotar donaciones">
						</form>
						&nbsp;&nbsp;&nbsp;&nbsp;
						<form method="post" class="linea" action="./index.php?controlador=cTesorero&accion=mostrarTotalesDonaciones">
												<input type="submit" name="mostrarTotalesDonaciones" value="Totales donaciones">
						</form>
						&nbsp;&nbsp;&nbsp;&nbsp;
						<form method="post" class="linea" action="./index.php?controlador=cTesorero&amp;accion=excelDonacionesTesorero">
												<input type="submit" name="exportarExcelDonacionesTesorero" value="Exportar las donaciones a archivo Excel">
						</form>
						&nbsp;&nbsp;&nbsp;&nbsp;
						
						<form method="post" class="linea" action="./index.php?controlador=cTesorero&amp;accion=donacionConceptos">
												<input type="submit" name="mostrarAniadirDonacionConceptos" value="Mostrar y Añadir Conceptos de Donación">
						</form>
						
					</div>  
					<!-- *************** Fin botones form búsqueda para dirigir a funciones Donación **************** -->	
					
					<br />
				
					<!-- *************** Inicio form búsqueda por APE1, APE2 **************************************** -->	
				 <form method="post" action="./index.php?controlador=cTesorero&amp;accion=mostrarDonaciones">
					
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
					<!-- ****************** Fin form búsqueda por APE1,APE2  **************************************** -->
    </td> 
			</tr> 
		                
   <tr>
			 <td>
						
				<div align="left">
				
				 <!-- ********************* Inicio selección año ************************************************* -->	
				 <form method="post" action="./index.php?controlador=cTesorero&accion=mostrarDonaciones">
										
					 <span class='textoAzul9C'>Año</span>					
					 <?php 
					  require_once './modelos/libs/comboLista.php';
			
			    //for ($a=date("Y")-5; $a<=date("Y"); $a++){$parValorAnio[$a]=$a;}
							for ($a=2012; $a<=date("Y"); $a++){$parValorAnio[$a]=$a;}
					 	$parValorAnio["%"]="Todos"; 	
																			
			    echo comboLista($parValorAnio,"resDonaciones[anioDonacionesElegido]",$resDonaciones['anioDonacionesElegido'],
				                   $parValorAnio[$resDonaciones['anioDonacionesElegido']],"","");																								
			   ?>	
						&nbsp;&nbsp;&nbsp;&nbsp;															
      <input type="submit" name="anio" value="Buscar por año"> 												
	    </form>
			  <!-- ************************* Fin selección año ************************************************ -->
				
				 <!-- ******************** Inicio informa num. pag y líneas ************************************** --> 
					<span class='textoAzulClaro8L'><?php echo "<br />".$resDonaciones['_pag_info']; ?></span>	
				 <!-- ******************** Fin informa. num. pag y líneas **************************************** -->		
					
     <!-- ******************** Inicio tabla datos donaciones ***************************************** --> 
					<table width="100%" border="1" cellspacing="0" bordercolor="#99CCFF">
						<tr bgcolor="#CCCCCC">
		
							<th rowspan='2' class="textoAzul8L">Año</th>
							<th rowspan='2' class="textoAzul8L">Apellidos, Nombre</th> 
							<th rowspan='2' class="textoAzul8L">Donación (€)</th>
							<th rowspan='2' class="textoAzul8L">Gastos Donación (€)</th>
							<th rowspan='2' class="textoAzul8L">Modo ingreso</th>  
							<th rowspan='2' class="textoAzul8L">Tipo donante</th>  
							<th rowspan='2' class="textoAzul8L">Concepto donación</th>  
							<th rowspan='2' class="textoAzul8L">Email</th> 
							<th rowspan='2' class="textoAzul8L">Fecha ingreso</th>
							
							<!--<th rowspan='2' class="textoAzul8L">Antigüedad</th>  -->
							<th colspan =3 class="textoAzul8C">Acciones</th>  
						</tr>
						<tr bgcolor="#CCCCCC">
							<th class="textoAzul7L">Ver</th>
							<th class="textoAzul7L">Modifica</th>
							<th class="textoAzul7L" width='10'>Eliminar</th>						
						</tr>        
						<?php 
						$items=$resDonaciones['resultadoFilas'];
						//echo "<br><br>items:";print_r($items);
						foreach ($items as $ordinal => $fila)
						//tabla de datos en este caso será solo una fila y se podría suprimir el foreach                          
						{ echo ("<tr height='10'>");          
					
								echo ("<td class='textoAzul7L'>".substr($items[$ordinal]['FECHAINGRESO'],0,4)."</td>");
								//echo ("<td class='textoAzul7L'>".$items[$ordinal]['anioDonacion']."</td>");
								echo ("<td class='textoAzul7L'>".$items[$ordinal]['apeNom']."</td>"); 
		
								//Ya está tratado en la select SQL con REPLACE, o en la función de modelo correspondiente y serviría:
								//echo ("<td class='textoAzul7R'>".$items[$ordinal]['IMPORTEDONACION_COMA_DECIMAL']."</td>"); 
								//Pero me parece más flexible y general lo siguiente:
								
								$importeComaDecimal = number_format($items[$ordinal]['IMPORTEDONACION'], 2, '.','');   //Pone punto y dos decimales en todos los números incluso enteros				
								//$importeComaDecimal = number_format($items[$ordinal]['IMPORTEDONACION'], 2, ',',''); //Pone coma y dos decimales en todos los números incluso enteros								
								//$importeComaDecimal = str_replace(".", ",", $items[$ordinal]['IMPORTEDONACION']);    //Cambia el punto decimal por una coma 
								echo ("<td class='textoAzul7R'>".$importeComaDecimal."</td>");	

        $gastosDonacionComaDecimal = number_format($items[$ordinal]['GASTOSDONACION'], 2, '.','');		 //Pone punto y dos decimales en todos los números incluso enteros			
								//$gastosDonacionComaDecimal = number_format($items[$ordinal]['GASTOSDONACION'], 2, ',','');	//Pone coma y dos decimales en todos los números incluso enteros											
								//$gastosDonacionComaDecimal = str_replace(".", ",", $items[$ordinal]['GASTOSDONACION']);    //Cambia el punto decimal por una coma 
 	
	       echo ("<td class='textoAzul7R'>".$gastosDonacionComaDecimal."</td>"); 
        //echo ("<td class='textoAzul7R'>"."item:-->".$items[$ordinal]['GASTOSDONACION']."</td>"); 								
								
								//echo ("<td class='textoAzul7R'>".$items[$ordinal]['GASTOSDONACION_COMA_DECIMAL']."</td>");  	
								echo ("<td class='textoAzul7L'>".$items[$ordinal]['MODOINGRESO']."</td>");					
								echo ("<td class='textoAzul7L'>".$items[$ordinal]['TIPODONANTE']."</td>");	
								echo ("<td class='textoAzul7L'>".$items[$ordinal]['CONCEPTO']."</td>");	
								if (isset($items[$ordinal]['EMAIL']) && $items[$ordinal]['EMAIL']!==NULL && $items[$ordinal]['EMAIL']!=='')
								{echo ("<td class='textoAzul7L'>".$items[$ordinal]['EMAIL']."</td>");}
								else
								{ echo ("<td class='textoAzul7L'>"."&nbsp;"."</td>");}
								echo ("<td class='textoAzul7L'>".$items[$ordinal]['FECHAINGRESO']."</td>");							
								
								echo ("<td  valign='center'>");			
							?>
									<form method="post" 
												action="./index.php?controlador=cTesorero&accion=mostrarIngresoDonacion">
										<input type="image" src="./vistas/images/lupa.gif"  value="mostrarIngresoDonacion"
												alt="[Ver]" name="Ver" title="Ver todos los datos de esta donación" align="middle" /><!--para todos browsers-->
										<input type='hidden' name="datosFormDonacion[CODDONACION]"
											value='<?php echo $items[$ordinal]['CODDONACION'];?>' />	          
									</form>				                    
							<?php 
								echo ("</td>");      
								echo ("<td>");
								?>  
									<form method="post" 
												action="./index.php?controlador=cTesorero&accion=modificarIngresoDonacionTes">
										<input type="image" src="./vistas/images/pluma.gif" value="modificarIngresoDonacionTes"
										alt="[Modificar]" name="Modificar"  title="Modificar algún campo de esta donación" />
										<input type='hidden' name="datosFormDonacion[CODDONACION]"
											value='<?php echo $items[$ordinal]['CODDONACION'];?>' />  
									</form> 
								<?php							
							
								echo ("</td>");
								echo ("<td valign='center' width='10'>");
								?>  
									<form method="post" 
												action="./index.php?controlador=cTesorero&accion=anularDonacionErroneaTes">
										<input type="image" src="./vistas/images/papelera.gif" value="anularDonacionErroneaTes"
										alt="[Eliminar donación]" name="Eliminar"  title="Eliminar donación errónea" />
										<input type='hidden' name="datosFormDonacion[CODDONACION]"
											value='<?php echo $items[$ordinal]['CODDONACION'];?>' />                                
									</form> 
								<?php 
									
								echo ("</td>");         
								echo ("</tr>");
							}
							?>					
					</table> 
					<!-- ******************** Fin tabla datos donaciones ******************************************** -->
					
			 </div> <!-- <div align="left"> -->
 				
				</td>  		
	  </tr>	       
							
			<br />

			<!-- ************************ Inicio paginación  ************************************************* -->					
			<tr>  
			 <td align="center">
    <?php  
					echo "<span class='textoAzul9C'>".$resDonaciones['_pag_navegacion']."</span>";
     ?>				
    </td>
   </tr>	
		 <!-- ************************ Fin paginación  *************************************************** -->	

  </table>   
</div>		
