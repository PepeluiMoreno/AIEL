<!-- ************************************************************************
FICHERO: formMostrarSociosPres.php
PROYECTO: EL
VERSION: PHP 5.2.3
DESCRIPCION: Muesta la lista de socios, que gestiona el presidente, y desde la que 
             se enlaza con las acciones: Ver, Modificar, Baja para cada socio
OBSERVACIONES:Es incluida desde './vistas/presidente/vCuerpoMostrarSociosPres.php'              
************************************************************************ -->
<?php
/*--------------------------------------------------------------------------------------------------
FICHERO: formMostrarSociosPres.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: 
En el formulario se forma y muestra una tabla-lista paginada "LISTA DE SOCI@S" de los socios de con 
algunos campos de información de los socios de todas las agrupaciones de EL, pero sólo de los que e
stán dados de alta 
Incluye un campo para elegir una agrupación concreta. También permite buscar por apellidos de socios.

En la parte inferior se muestran número de páginas para poder ir directamente a un página, anterior,
siguiente, primera, última.
Además al final para cada fila, hay iconos links para: ver detalles de ese socio, modificar datos,
borrar datos socio 	

LLAMADA: cPresidente.php: vCuerpoMostrarSociosPres.php 
y a su vez desde cPresidente.php: mostrarSociosPres() y el menú izdo del rol presidencia "Lista soci@s"



OBSERVACIONES: El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario.
----------------------------------------------------------------------------------------------------*/
?>

<div id="registro">	

	 <table width="100%" border="0" bordercolor="#FFFFFF" cellspacing="0">
		 <tr>
			 <td>
					<!-- *************** Inicio form búsqueda por APE1, APE2 ************* -->	
				 <form method="post" action="./index.php?controlador=cPresidente&amp;accion=mostrarSociosPres">
					
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
					<!-- ****************** Fin form búsqueda por APE1,APE2  ************* -->										
    </td> 
			</tr>  

   <tr>
			 <td>
     <!--				<div align="left"> 		-->
					<!-- ********************* Inicio selección agrupación *************** -->						
				 <form method="post" action="./index.php?controlador=cPresidente&amp;accion=mostrarSociosPres">
										
					 <span class='textoAzul9C'>Agrupación territorial</span>					
					 <?php 
					  require_once './modelos/libs/comboLista.php';
							
					  //echo "<br><br>1parValorComboAgrupaSocio:";print_r($parValorComboAgrupaSocio);
						 //function comboLista($parValor,$identificadorCampo,$valorPrevio,$descPrevio,$valorInicial,$descInicial)
							
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

					
    <!-- ******************** Inicio tabla datos socios ************************** -->				
    <table width="100%" border="1" cellspacing="0" bordercolor="#99CCFF">
     <tr bgcolor="#CCCCCC">
      <th rowspan='2' class="textoAzul8L" width='190'>Socios/as</th>
      <th rowspan='2' class="textoAzul8L" width='10'>Agrupación</th>	
						<th rowspan='2' class="textoAzul8L" width='8'>País</th>	
      <th rowspan='2' class="textoAzul8L">Provincia</th>								
						<th rowspan='2' class="textoAzul8L">Localidad</th> 
      <th rowspan='2' class="textoAzul8L">Emails</th> 
      <th rowspan='2' class="textoAzul8L">Teléfonos</th> 						
      <th rowspan='2' class="textoAzul8L" width='10'>Cuota</th>  
      <!--<th rowspan='2' class="textoAzul8L">Colabora</th>											
      <th rowspan='2' class="textoAzul8L">Antigüedad</th>  -->
      <th colspan =3 class="textoAzul8C">Acciones</th>  
     </tr>
     <tr bgcolor="#CCCCCC">
      <th class="textoAzul7L" width='10'>Ver</th>
      <th class="textoAzul7L" width='10'>Modif</th>
      <th class="textoAzul7L" width='10'>Baja</th>
     </tr>    
					   
     <?php 
					$items=$resDatosSocios['resultadoFilas'];
     //echo "<br><br>items:";print_r($items);
     foreach ($items as $ordinal => $fila)     //tabla de datos                          
   	 { echo ("<tr style='height:30px;'>");         
       
	      echo ("<td class='textoAzul7L' width='190'>".$items[$ordinal]['apeNom']."</td>");
       //echo ("<td class='textoAzul7L'>".$items[$ordinal]['APE1']." ".$items[$ordinal]['APE2'].", ".$items[$ordinal]['NOM']."</td>");																																							
       //echo ("<td class='textoAzul7L'>".$items[$ordinal]['NOMAGRUPACION']."</td>");	
	
	      echo ("<td class='textoAzul7L' width='8'>".$items[$ordinal]['Agrupacion_Actual']."</td>");	
       echo ("<td class='textoAzul7L'>".$items[$ordinal]['CODPAISDOM']."</td>");		
							
       if (isset($items[$ordinal]['NOMPROVINCIA']) && $items[$ordinal]['NOMPROVINCIA']!=="")
       {echo ("<td class='textoAzul7L'>".$items[$ordinal]['NOMPROVINCIA']."</td>");}
	      else 
	      {echo ("<td class='textoAzul7L'>"."&nbsp;"."</td>");}	

       if (isset($items[$ordinal]['LOCALIDAD']) && $items[$ordinal]['LOCALIDAD']!=="")
       {echo ("<td class='textoAzul7L'>".$items[$ordinal]['LOCALIDAD']."</td>");}
	      else 
	      {echo ("<td class='textoAzul7L'>"."&nbsp;"."</td>");}  							
																																								
       if ((isset($items[$ordinal]['INFORMACIONEMAIL'])&& $items[$ordinal]['INFORMACIONEMAIL']=="SI") && 
							    (isset($items[$ordinal]['EMAILERROR'])&& $items[$ordinal]['EMAILERROR']=="NO") && 
							    (isset($items[$ordinal]['EMAIL'])&& !empty($items[$ordinal]['EMAIL']))
											 //&& 	 substr($items[$ordinal]['EMAIL'], -10) !== "@falta.com")
										)		 
       {echo ("<td class='textoAzul7L'>".$items[$ordinal]['EMAIL']."</td>");}
	      else 
	      {echo ("<td class='textoRojo7Left'>"."falta, error o no recibir email"."</td>");}	
        
							echo ("<td class='textoAzul7L'>".$items[$ordinal]['TELFIJOCASA']."<br />".
	                                       $items[$ordinal]['TELMOVIL']."</td>");													
       						 
       if ($items[$ordinal]['ESTADOCUOTA']=="ABONADA" || $items[$ordinal]['ESTADOCUOTA']=="EXENTO")											
       {echo ("<td class='textoAzul7L' width='10'>".$items[$ordinal]['ESTADOCUOTA']."</td>");}
	      else 
							{echo ("<td class='textoRojo7Left' width='10'>".$items[$ordinal]['ESTADOCUOTA']."</td>");}
       echo ("<td  valign='center' width='10'>");
      ?>
      <?php 
       // echo ("<span class='textoAzul7L' >".$items[$ordinal]['ESTADO']."</span>");            
        ?> 

        <form method="post" action="./index.php?controlador=cPresidente&amp;accion=mostrarDatosSocioPres">										
        <!--  action="./index.php?controlador=controladorSocios&accion=mostrarDatosSocio" target="_blank"> -->		           								
																			
         <input type="image" src="./vistas/images/lupa.gif"  value="mostrarDatosUsuario"
									  alt="Mostrar datos socio" name="Ver" title="Ver todos los datos del socio" align="middle" /><!-- para todos browsers -->
											
         <input type='hidden' name="datosFormUsuario[CODUSER]"
          value='<?php echo $items[$ordinal]['CODUSER'];?>' />  										
          
	       </form>
      <?php           
       echo ("</td>");      
       echo ("<td valign='center' width='10'>");
						
						 if (isset($items[$ordinal]['ESTADO']) && $items[$ordinal]['ESTADO']!== "baja")
							{							
       ?> 
        <form method="post" action="./index.php?controlador=cPresidente&amp;accion=actualizarSocioPres">
								
         <input type="image" src="./vistas/images/pluma.gif" value="actualizarDatosUsuario"
									 alt="Modificar datos socio" name="Modificar" title="Modificar datos del socio" />
										
         <input type='hidden' name="datosFormUsuario[CODUSER]"
          value='<?php echo $items[$ordinal]['CODUSER'];?>' />   
										
	       </form>
							<?php	
							}
							else 
							{echo ("&nbsp;");
							 //echo ("<span class='textoAzul7L'>No tiene</span>");											
							}							 
							echo ("</td>");										                   

       echo ("<td valign='center' width='10'>");
						 if (isset($items[$ordinal]['ESTADO']) && $items[$ordinal]['ESTADO']!== "baja")
							{	
							?>  
	       <form method="post" action="./index.php?controlador=cPresidente&amp;accion=eliminarSocioPres">
								
         <input type="image" src="./vistas/images/papelera.gif" value="eliminarSocio"
									alt="Baja y eliminación de datos socio/a" name="BajaSocio"  title="Baja de un socio/a" />
									
         <input type='hidden' name="datosFormUsuario[CODUSER]"
          value='<?php echo $items[$ordinal]['CODUSER'];?>' />  
										
	       </form> 
       <?php 
							}
							else 
							{echo ("&nbsp;");
							 //echo ("<span class='textoAzul7L'>No tiene</span>");											
							}									
       echo ("</td>");         
       echo ("</tr>");
      }
      ?>
    </table> 
	   <!--		 </div> -->
    <!-- ************************ Fin tabla datos socios ************************* -->						
				</td>  		
	  </tr>	
			
    <br />
			<!-- ************************ Inicio paginación  ************************ -->		
			
			<tr>  
			 <td align="center">
    <?php  
					echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    	<span class='textoAzul9C'>".$resDatosSocios['_pag_navegacion'].
					"<br /></span>";
     ?>				
    </td>
   </tr>	
		<!-- ************************ Fin paginación  *************************** -->	

  </table>   
</div>		
