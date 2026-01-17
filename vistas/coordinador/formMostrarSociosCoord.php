<!-- ************************************************************************
FICHERO: formMostrarSociosCoord.php
PROYECTO: EL
VERSION: PHP 5.2.3
DESCRIPCION: Muesta la lista de socios, que gestiona el coordinador
OBSERVACIONES:Es incluida desde './vistas/admin/vCuerpoMostrarSociosCoord.php'              
************************************************************************ -->
<div id="registro">		
 <!-- ********************* Inicio  agrupación nombre *************** -->
		 <span class='textoAzul9C'>Área de gestión territorial:	</span>			
			<span class='mostrar1'><?php  echo "<b>&nbsp;$nomAgrupCoord</b>"; ?></span>
	<!-- ********************* Fin agrupación ******************* -->	
			<br />	
	 <div align="left">
	<!-- *************** Inicio form búsqueda por APE1, APE2 ************* -->	
		 <form method="post" action="./index.php?controlador=cCoordinador&amp;accion=mostrarSociosCoord">
	  <fieldset>
			 <p>
					<label>Apellido1</label> 
	    <input type="text"
	           name="datosFormMiembro[APE1]"
	           value='<?php if (isset($datosFormMiembro['APE1']['valorCampo']))
	           {  echo $datosFormMiembro['APE1']['valorCampo'];}
	           ?>'
	           size="25"
	           maxlength="200"
	    />	
					<span class="error">
						<?php
						if (isset($datosFormMiembro['APE1']['errorMensaje']))
						{echo $datosFormMiembro['APE1']['errorMensaje'];}
						?>
					</span>				
					<label>Apellido2</label> 				
	    <input type="text"
	           name="datosFormMiembro[APE2]"
	           value='<?php if(isset($datosFormMiembro['APE2']['valorCampo']))
	           {  echo $datosFormMiembro['APE2']['valorCampo'];}
	           ?>'
	           size="25"
	           maxlength="200"
	    />	
					<span class="error">
						<?php
						if (isset($datosFormMiembro['APE2']['errorMensaje']))
						{echo $datosFormMiembro['APE2']['errorMensaje'];}
						?>
					</span>							
   </p>	
					<input type="submit" name="BuscarApeNom" value="Buscar por apellidos">
	 </fieldset>	
 </form>
	<!-- ****************** Fin form búsqueda por APE1,APE2  ************* -->		
	
 <!-- *************** Inicio form búsqueda Todos ************* -->	
	<!--
	<form method="post" 
         action="./index.php?controlador=cCoordinador&amp;accion=mostrarSociosCoord">
					<input type="submit" name="BuscarTodos" value="Buscar todos los socios"> 
 </form>
	-->
		<!-- *************** Fin form búsqueda Todos *************** -->				
	<!-- ********************* Inicio selección agrupación *************** -->						
<!--	<div align="center"> -->
 <form method="post" 
       action="./index.php?controlador=cCoordinador&amp;accion=mostrarSociosCoord">
						
	 <span class='textoAzul9C'>Agrupación territorial</span>					
	 <?php 
	  require_once './modelos/libs/comboLista.php';

			//$parValorComboAgrupaSocio['lista']["%"]= "Todas"; 
	  //echo "<br><br>1parValorComboAgrupaSocio:";print_r($parValorComboAgrupaSocio);
		 //function comboLista($parValor,$identificadorCampo,$valorPrevio,$descPrevio,$valorInicial,$descInicial)
	  //echo utf8_encode(comboLista($parValorComboAgrupaSocio['lista'], "datosFormSocio[CODAGRUPACION]",
			
			echo comboLista($parValorComboAgrupaSocio['lista'], "datosFormSocio[CODAGRUPACION]",
		        	                       $parValorComboAgrupaSocio['valorDefecto'],
																																		$parValorComboAgrupaSocio['lista'][$parValorComboAgrupaSocio['valorDefecto']],"","");
   ?> 	
		&nbsp;&nbsp;&nbsp;&nbsp;															
  <input type="submit" name="organizacionElegida" value="Buscar por agrupación"> 												
 </form>
 <!-- ************************* Fin selección agrupación ************* -->				
									
		
		 <br />
			<!-- ******************** Inicio infoma. num. pag y líneas ***************** -->	
				<span class='textoAzulClaro8L'><?php echo $resDatosSocios['_pag_info']; ?></span>	
			<!-- ******************** Inicio infoma. num. pag y líneas ***************** -->									
	</div>

			<!-- ******************** Inicio tabla datos socios ************************** -->	
    <table width="100%" border="1px" cellspacing="0" bordercolor="#99CCFF">
     <tr bgcolor="#CCCCCC">
      <th rowspan='2' class="textoAzul8L">Socios/as</th>
      <th rowspan='2' class="textoAzul8L">Agrupación</th>	
<!--						<th rowspan='2' class="textoAzul8L">País</th>-->
      <th rowspan='2' class="textoAzul8L">Provincia</th>								
						<th rowspan='2' class="textoAzul8L">Localidad</th> 
      <th rowspan='2' class="textoAzul8L">Emails</th> 
      <th rowspan='2' class="textoAzul8L">Teléfonos</th>  
      <th rowspan='2' class="textoAzul8L">Cuota <?php echo date('Y');?></th>   
     <th colspan =3 class="textoAzul8C">Acciones</th>  
     </tr>
     <tr bgcolor="#CCCCCC">
      <th class="textoAzul7L">Ver</th>
      <th class="textoAzul7L">Modifica</th>
      <th class="textoAzul7L">Baja</th>
     </tr>        
     <?php 
					$items=$resDatosSocios['resultadoFilas'];
//echo "<br><br>items:";print_r($items);
     foreach ($items as $ordinal => $fila)     //tabla de datos                          
   	 { echo ("<tr height='10'>");          
       
	      echo ("<td class='textoAzul7L'>".$items[$ordinal]['apeNom']."</td>");
	/* 						echo ("<td class='textoAzul7L'>".$items[$ordinal]['APE1']." ".$items[$ordinal]['APE2'].", ".
							                                 $items[$ordinal]['NOM']."</td>");
      echo ("<td class='textoAzul7L'>".$items[$ordinal]['NOMAGRUPACION']."</td>");	
*/
	      echo ("<td class='textoAzul7L' width='8'>".$items[$ordinal]['Agrupacion_Actual']."</td>");							
 /*      echo ("<td class='textoAzul7L'>".$items[$ordinal]['CODPAISDOM']."</td>");		
*/									
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
											 //&& substr($items[$ordinal]['EMAIL'], -10) !== "@falta.com")
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
											
       //echo ("<td class='textoAzul7L'>".$items[$ordinal]['FECHAALTA']."</td>");
       //echo ("<td  valign='center'>");						
       ?> 
        <form method="post" 
 		          	action="./index.php?controlador=cCoordinador&amp;accion=mostrarDatosSocioCoord">										
     <!--  action="./index.php?controlador=controladorSocios&accion=mostrarDatosSocio" target="_blank"> -->		           								
																			
         <input type="image" src="./vistas/images/lupa.gif"  value="mostrarDatosUsuario"
									  alt="[Ver]" name="Ver" title="Ver todos los datos del socio" align="middle" v /><!--para todos browsers-->
         <input type='hidden' name="datosFormUsuario[CODUSER]"
          value='<?php echo $items[$ordinal]['CODUSER'];?>' />  										
          
	       </form>
      <?php
       echo ("</td>");      
       echo ("<td valign='center'>");	
							if (isset($items[$ordinal]['ESTADO']) && $items[$ordinal]['ESTADO']!== "baja")
							{							
       ?> 

        <form method="post" 
           action="./index.php?controlador=cCoordinador&amp;accion=actualizarSocioCoord">
         <input type="image" src="./vistas/images/pluma.gif" value="actualizarDatosUsuario"
									 alt="[Modificar]" name="Modificar" title="Modificar datos del socio" />
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
       echo ("<td>");
						 if (isset($items[$ordinal]['ESTADO']) && $items[$ordinal]['ESTADO']!== "baja")
							{	
							?>  
	       <form method="post" 
           action="./index.php?controlador=cCoordinador&amp;accion=eliminarSocioCoord">
         <input type="image" src="./vistas/images/papelera.gif" value="eliminarDatosSocio"
									alt="Baja y eliminación de los datos de un socio/a" name="BajaSocio"  title="Baja de un socio/a" />
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
				
    <!-- ************************ Fin datos tabla socios ************************* -->						

			<!-- ************************ Inicio paginación  ************************ -->					
	 
			 <div align="center">
    <?php  
					echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    	<span class='textoAzul9C'>".$resDatosSocios['_pag_navegacion'].
					"<br /></span>";
     ?>				
    </div>
		<!-- ************************ Fin paginación  *************************** -->	

</div>		
