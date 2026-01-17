<?php
/*----------------------------------------------------------------------------------------------------
FICHERO: formMostrarEstadoConfirSocios.php
VERSION: PHP 7.3.21

Se forma y muestra una tabla con el estado de confirmación del alta, datos de contacto y otros
de los socios para las diferentes situaciones en 

"Pendientes confirmar alta por socio/a":
"alta-sin-password-gestor"=>"Altas por gestor sin confirmar email por socio/a",
"alta-sin-password-excel"=>"Altas antiguos socio/as aún sin confirmar email",
"pendiente_confirmar_algo"=>"Todos los pendientes de alguna confirmación",
"alta_por_socio_confirmada"=>"Altas ya confirmadas por socio/a",
"alta_por_gestor_confirmada"=>"Altas por gestor ya confirmado email por socio/a

Al final de la tabla según el estado de confirmación permite  las siguientes "Acciones":
- Reenviar email	
- Confirmar soci@	
- Borrar pendiente confirmar


LLAMADA: vistas/presidente/vCuerpoMostrarEstadoConfirSocios.php
y previamente desde cPresidente.php:mostrarEstadoConfirmacionSocios()

OBSERVACIONES: 
-----------------------------------------------------------------------------------------------------*/
?>

<div id="registro">	

	 <table width="100%" border="0" bordercolor="#FFFFFF" cellspacing="0">
		 <tr>
			 <td>
				
					<!-- *************** Inicio form búsqueda por APE1, APE2 ************* -->	
				 <form method="post" 
           action="./index.php?controlador=cPresidente&amp;accion=mostrarEstadoConfirmacionSocios">
							<span class='textoAzul9C'>Apellido1</span>					
			    <input type="text"
			           name="datosFormElegirApeEstadoConf[APE1]"
			           value='<?php if (isset($datosFormElegirApeEstadoConf['APE1']['valorCampo']))
			           {  echo $datosFormElegirApeEstadoConf['APE1']['valorCampo'];}
			           ?>'
			           size="30"
			           maxlength="200"
			    />	
							<span class="error">
								<?php
								if (isset($datosFormElegirApeEstadoConf['APE1']['errorMensaje']))
								{echo $datosFormElegirApeEstadoConf['APE1']['errorMensaje'];}
								?>
							</span>
							<span class='textoAzul9C'>Apellido2</span>					
			    <input type="text"
			           name="datosFormElegirApeEstadoConf[APE2]"
			           value='<?php if (isset($datosFormElegirApeEstadoConf['APE2']['valorCampo']))
			           {  echo $datosFormElegirApeEstadoConf['APE2']['valorCampo'];}
			           ?>'
			           size="30"
			           maxlength="200"
			    />	
							<span class="error">
								<?php
								if (isset($datosFormElegirApeEstadoConf['APE2']['errorMensaje']))
								{echo $datosFormElegirApeEstadoConf['APE2']['errorMensaje'];}
								?>
							</span>	
							
							<input type="submit" name="BuscarApeNom" value="Buscar por apellidos"> 											
	    </form>
					<!-- ****************** Fin form búsqueda por APE1,APE2  ************* -->				
					
    </td> 
			</tr>  

   <tr>
			 <td>
				<div align="left"> 	
				
					<!-- ********************* Inicio Estado confirmación email socio *************** -->
				 <form method="post" 
           action="./index.php?controlador=cPresidente&amp;accion=mostrarEstadoConfirmacionSocios">
										
					 <span class='textoAzul9C'>Elegir estado confirmación alta</span>					
					 <?php 
					  require_once './modelos/libs/comboLista.php';
       /*
			    $parValorConfirmarEmail=array("%"=>"Todos los que no sean bajas","alta"=>"Alta confirmada",
       "alta-sin-password-gestor"=>"Alta gestor pendiente contraseña","alta-sin-password-excel"=>"Alta excel pendiente contraseña",
							"PENDIENTE-CONFIRMAR"=>"Alta pendiente confirmar por socio/a");										 
       */

			    $parValorConfirmarEmail = array("PENDIENTE-CONFIRMAR"=>"Pendientes confirmar alta por socio/a",
							                                "alta-sin-password-gestor"=>"Altas por gestor sin confirmar email por socio/a",
																																							"alta-sin-password-excel"=>"Altas antiguos socio/as aún sin confirmar email",
																																							"pendiente_confirmar_algo"=>"Todos los pendientes de alguna confirmación",
																																							"alta_por_socio_confirmada"=>"Altas ya confirmadas por socio/a",
																																							"alta_por_gestor_confirmada"=>"Altas por gestor ya confirmado email por socio/a"
																																							);		
       //añado if para evitar notices																																							
							if (!isset($datosFormElegirApeEstadoConf['CONFIRMACIONEMAIL']['valorCampo']) || 
							    empty($datosFormElegirApeEstadoConf['CONFIRMACIONEMAIL']['valorCampo']))
							{	$datosFormElegirApeEstadoConf['CONFIRMACIONEMAIL']['valorCampo'] = 'pendiente_confirmar_algo';}	
																																																		
		     echo comboLista($parValorConfirmarEmail,"datosFormElegirApeEstadoConf[CONFIRMACIONEMAIL]",
			                $datosFormElegirApeEstadoConf['CONFIRMACIONEMAIL']['valorCampo'],
										         $parValorConfirmarEmail[$datosFormElegirApeEstadoConf['CONFIRMACIONEMAIL']['valorCampo']],"pendiefnte_confirmar_algo","Todos los pendientes de alguna confirmación");									  
		    ?>
						
      <input type="submit" name="estadoConfirmacion" value="Buscar por Estado confirmación alta"> 												
	    </form>
				 <!-- ************************* Fin Estado confirmación email socio ************* -->		
					
						<!-- ******************** Inicio informa. num. pag y líneas ***************** -->	

					<span class='textoAzulClaro8L'><?php if (isset($resDatosSocios['_pag_info'])) {	echo $resDatosSocios['_pag_info'];}	?></span>							
						<!-- ******************** Inicio informa. num. pag y líneas ***************** -->							

    <!-- ******************** Inicio tabla datos socios ************************** -->				
    <table width="100%" border="1" cellspacing="0" bordercolor="#99CCFF">
     <tr bgcolor="#CCCCCC">
      <th rowspan='2' class="textoAzul8L">Socios/as</th>
      <th rowspan='2' class="textoAzul8L">Estado</th>	
						<th rowspan='2' class="textoAzul8L">País</th>	
      <!--<th rowspan='2' class="textoAzul8L">Provincia</th>				-->				
						<th rowspan='2' class="textoAzul8L">Localidad</th> 
      <th rowspan='2' class="textoAzul8L">Dirección</th> 
      <th rowspan='2' class="textoAzul8L">Teléfonos</th>  
						<th rowspan='2' class="textoAzul8L">Email</th>  
						<th rowspan='2' class="textoAzul8L">Fecha incio registro por soci@</th>		
      <th rowspan='2' class="textoAzul8L">Fecha último email a soci@</th>											
      <th rowspan='2' class="textoAzul8L">Nº envío</th> 
      <!--<th rowspan='2' class="textoAzul8L">Cod</th>-->
      <th colspan =3 class="textoAzul8C">Acciones</th>  
					</tr>
					<tr bgcolor="#CCCCCC">	 
						<th class='textoAzul7L'>Reenviar email</th>  
						<th class='textoAzul7L'>Confirmar soci@</th>  
						<th class='textoAzul7L'>Borra pendiente confirmar</th>  
     </tr> 

					<!-- ******* Fin Nombres cabeceras de las columnas ************************* -->
					
     <?php      
					if (isset($resDatosSocios['numFilas']) && $resDatosSocios['numFilas'] === 0)
				 {?> 
			 	 <br />
				  <span class="error"><strong>No se han datos para las condiciones de búsqueda</strong></span>
			   <br />
					<?php		
     }	
					if (isset ($resDatosSocios['resultadoFilas']))
				 {	
				
				 	$items=$resDatosSocios['resultadoFilas'];
      //echo "<br><br>items:";print_r($items);
      foreach ($items as $ordinal => $fila)     //tabla de datos                          
   	  { echo ("<tr style='height:30px;'>"); 
	      echo ("<td class='textoAzul7L'>".$items[$ordinal]['apeNom']."</td>");
							//echo ("<td class='textoAzul7L'>".$items[$ordinal]['APE1']." ".$items[$ordinal]['APE2'].", ".$items[$ordinal]['NOM']."</td>");
       // echo ("<td class='textoAzul7L'>".$items[$ordinal]['CONFIRMACIONEMAIL']."</td>");				
					 	echo ("<td class='textoAzul7L'>".$items[$ordinal]['ESTADO']."</td>");																																									
       echo ("<td class='textoAzul7L'>".$items[$ordinal]['CODPAISDOM']."</td>");	
						
							//echo ("<td class='textoAzul7L'>".$items[$ordinal]['LOCALIDAD']."</td>");

       if (isset($items[$ordinal]['LOCALIDAD']) && $items[$ordinal]['LOCALIDAD']!=="")
       {echo ("<td class='textoAzul7L'>".$items[$ordinal]['LOCALIDAD']."</td>");}
	      else 
	      {echo ("<td class='textoAzul7L'>"."&nbsp;"."</td>");}  							
																																								
       if (isset($items[$ordinal]['DIRECCION'])&& $items[$ordinal]['DIRECCION']!=="")
       {echo ("<td class='textoAzul7L'>".$items[$ordinal]['DIRECCION']."</td>");}
	      else 
	      {echo ("<td class='textoAzul7L'>"."&nbsp;"."</td>");}
       						 
       echo ("<td class='textoAzul7L'>".$items[$ordinal]['TELFIJOCASA']."<br />".
	                                       $items[$ordinal]['TELMOVIL']."</td>");
																																								
       if (isset($items[$ordinal]['EMAIL'])&& $items[$ordinal]['EMAIL']!=="" && 
							    substr($items[$ordinal]['EMAIL'], -10) !== "@falta.com")   
							{echo ("<td class='textoAzul7L'>".$items[$ordinal]['EMAIL']."</td>");}
	      else 
	      {echo ("<td class='textoAzul7L'>"."FALTA"."</td>");}	

       if (isset($items[$ordinal]['FECHAREGISTRO'])&& $items[$ordinal]['FECHAREGISTRO']!=="")
       {echo ("<td class='textoAzul7L'>".$items[$ordinal]['FECHAREGISTRO']."</td>");}
	      else 
	      {echo ("<td class='textoAzul7L'>"."--"."</td>");}       			

							echo ("<td class='textoAzul7L'>".$items[$ordinal]['FECHAENVIOEMAILULTIMO']."</td>");
							echo ("<td class='textoAzul7L'>".$items[$ordinal]['NUMENVIOS']."</td>");	
					 	//	echo ("<td class='textoAzul7L'>".$items[$ordinal]['CODUSER']."</td>");	
							
							/*--------------- INICIO ACCIONES  -----------------------------------------*/
							echo ("<td  valign='center' width='10'>");
							
						 if (isset($items[$ordinal]['EMAIL'])&& $items[$ordinal]['EMAIL']!=="" && 
							    substr($items[$ordinal]['EMAIL'], -10) !== "@falta.com" && 
											isset($items[$ordinal]['ESTADO']) && $items[$ordinal]['ESTADO']!== "alta")
							{			
								?>									
								<!-- El siguiente form sirve para los socios ESTADO =(PENDIENTE-CONFIRMAR, o alta-sin-password-excel	o alta-sin-password-gestor)	-->  			
								<form method="post" action="./index.php?controlador=cPresidente&amp;accion=reenviarEmailConfirmarSocioAltaGestor">										
									<!--  action="./index.php?controlador=controladorSocios&accion=mostrarDatosSocio" target="_blank"> -->		           								
																				
										<input type="image" src="./vistas/images/email.gif"  value="emailRecordatoriorAlta"
												alt="Reenviar email recordatorio de confirmación de alta a socio/a" 
												name="Reenviar" title="Reenviar email recordatorio de confirmación de alta a socio/a" align="middle" /><!--para todos browsers-->
										<input type='hidden' name="datosFormMostrarEstadoConfSocio[CODUSER]"
											value='<?php echo $items[$ordinal]['CODUSER'];?>' />  		
										<input type='hidden' name="datosFormMostrarEstadoConfSocio[ESTADO]"
											value='<?php echo $items[$ordinal]['ESTADO'];?>' /> 
											
								</form>								
								<?php	
							}
							else 
							{echo ("&nbsp;");
							 //echo ("<span class='textoAzul7L'>No tiene</span>");											
							}							 
							echo ("</td>");
							//---
							echo ("<td  valign='center' width='10'>");
							if (isset($items[$ordinal]['ESTADO'])&& $items[$ordinal]['ESTADO']=="PENDIENTE-CONFIRMAR")
       {
						  ?>							
					  	<!-- El siguiente form sirve para los socios ESTADO =(PENDIENTE-CONFIRMAR), que lo confirme el gestor presidente	-->  			
        <form method="post" action="./index.php?controlador=cPresidente&amp;accion=confirmarAltaSocioPendientePorGestor">										
   																	
         <input type="image" src="./vistas/images/Lambda.jpg"  value="confirmarAltaSocioPendientePorGestor"
									  alt="Confirmación de alta a socio/a pendiente por gestor" 
											name="Confirmar" title="Confirmación de alta a socio/a pendiente por gestor" align="middle" /><!--para todos browsers-->
         <input type='hidden' name="datosFormMostrarEstadoConfSocio[CODUSER]"
          value='<?php echo $items[$ordinal]['CODUSER'];?>' />  		
									<input type='hidden' name="datosFormMostrarEstadoConfSocio[ESTADO]"
          value='<?php echo $items[$ordinal]['ESTADO'];?>' /> 
          
	       </form> 
							
							 <?php	
							}
							else 
							{echo ("&nbsp;");
							 //echo ("<span class='textoAzul7L'>No tiene</span>");											
							}							 
							echo ("</td>");							
							//---
							echo ("<td  valign='center' width='10'>");
							if (isset($items[$ordinal]['ESTADO'])&& $items[$ordinal]['ESTADO']=="PENDIENTE-CONFIRMAR")
       {
				  		?>			
					 	 <!-- El siguiente form sirve para los socios ESTADO =(PENDIENTE-CONFIRMAR), las bajas de socios se hace desde el menú lista de socios		--> 		
        <form method="post" action="./index.php?controlador=cPresidente&amp;accion=anularSocioPendienteConfirmarPres">										
   																
         <input type="image" src="./vistas/images/papelera.gif"  value="anularSocioPendienteConfirmar"
									  alt="Eliminar datos socio/a pendiente de confirmar alta" 
											name="Eliminar" title="Eliminar datos socio/a pendiente de confirmar alta" align="middle" /><!--para todos browsers-->
         <input type='hidden' name="datosFormMostrarEstadoConfSocio[CODUSER]"
          value='<?php echo $items[$ordinal]['CODUSER'];?>' />  		
									<input type='hidden' name="datosFormMostrarEstadoConfSocio[ESTADO]"
          value='<?php echo $items[$ordinal]['ESTADO'];?>' /> 
          
	       </form> 
							 <?php	
							}
							else 
							{echo ("&nbsp;");
							 //echo ("<span class='textoAzul7L'>No permitido</span>");											
							}
							echo ("</td>");	
       /*--------------- FIN ACCIONES  -------------------------------------------*/							
       echo ("</tr>");
      }//foreach ($items as $ordinal => $fila) 
					}// if (isset ($resDatosSocios['resultadoFilas']))	
      ?>
    </table> 
			 </div> 
    <!-- ************************ Fin tabla datos socios ************************* -->						
				</td>  		
	  </tr>	
    <br />
				
			<!-- ************************ Inicio paginación  ************************ -->					
			<tr>  
			 <td align="center">
   <?php  
									/*echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    	<span class='textoAzul9C'>
					".$resDatosSocios['_pag_navegacion'].					"<br />
										
					</span>";*/
				
				 if (isset($resDatosSocios['_pag_navegacion']))
					{echo "<span class='textoAzul9C'>".$resDatosSocios['_pag_navegacion']."</span>";}
     ?>								

    </td>
   </tr>	
	 	<!-- ************************ Fin paginación  *************************** -->	

  </table>   
</div>		
