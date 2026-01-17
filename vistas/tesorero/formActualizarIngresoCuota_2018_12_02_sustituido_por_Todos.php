<?php
/*-----------------------------------------------------------------------------
FICHERO: formActualizarIngresoCuota.php
VERSION: PHP 5.2.3
Agustin
2016_03_20 mejoras en presentación DE ORDENARCOBROBANCO y RECUR FRST
2017_01_11, CUENTAPAGO, correciones FRST y RCUR, y otros
2017_04_15, aumento tamaño OBSERVACIONES a 499
DESCRIPCION: Es el formulario para la actualización de datos cuotas de un socio
OBSERVACIONES:Es incluida desde "vCuerpoActualizarIngresoCuota.php"
              mediante require_once './vistas/tesorero/formActualizarIngresoCuota.php'
-------------------------------------------------------------------------------*/
require_once './modelos/libs/comboLista.php';
?>
<script languaje='JavaScript'>
<!-- 
function limitarTextoArea(max, id)
{if(max < document.getElementById(id).value.length)
	{ document.getElementById(id).value = document.getElementById(id).value.substr(0, max);
   alert("Llegó al máximo de caracteres permitidos");
	}			
}
-->
</script> 

<div id="registro">
	<span class="textoAzu112Left2">
	<strong>Formulario para anotar el pago de la cuota de socio/a</strong>
	 </span> 
	<br />
			<span class="error">
									<?php //echo "datSocio:"; print_r($datSocio);  echo "<br />"; 
									if (isset($datSocio['codError']) && $datSocio['codError'] !== '00000') 
									{
													echo "<b>ERROR AL INTRODUCIR LOS DATOS: revisa los datos con comentarios de error en color rojo</b>";
									}
									?>
		</span> 	
		<br /> 	<br /> 
 <form name="actualizarSocio" method="post" class="linea"
       action="./index.php?controlador=cTesorero&amp;accion=actualizarIngresoCuota">
				
		<!-- ****************** Inicio hidden formActualizarIngresoCuota ***************** -->		
   <input type="hidden"	name="datSocio[formIngresoCuotaAnterior][anteriorIMPORTECUOTAANIOPAGADA]"
          value='<?php if (isset($datSocio['formIngresoCuotaAnterior']['anteriorIMPORTECUOTAANIOPAGADA']['valorCampo']))
                   {  echo $datSocio['formIngresoCuotaAnterior']['anteriorIMPORTECUOTAANIOPAGADA']['valorCampo'];}?>'
    />			
   <input type="hidden"	name="datSocio[formIngresoCuota][ANIOCUOTA]"
			       value='<?php if (isset($datSocio['formIngresoCuota']['ANIOCUOTA']['valorCampo']))
										{  echo $datSocio['formIngresoCuota']['ANIOCUOTA']['valorCampo'];}?>'
			/>																			
			<input type="hidden"	name="datSocio[formIngresoCuota][CODSOCIO]" 
			       value='<?php if (isset($datSocio['formIngresoCuota']['CODSOCIO']['valorCampo'])) 
										             {  echo $datSocio['formIngresoCuota']['CODSOCIO']['valorCampo'];}?>'			 
			/>
			<input type="hidden"	name="datSocio[formIngresoCuota][CODCUOTA]"
          value='<?php if (isset($datSocio['formIngresoCuota']['CODCUOTA']['valorCampo']))
                       {  echo $datSocio['formIngresoCuota']['CODCUOTA']['valorCampo'];}?>'
    />				
   <input type="hidden"	name="datSocio[formIngresoCuota][NOMBRECUOTA]"
          value='<?php if (isset($datSocio['formIngresoCuota']['NOMBRECUOTA']['valorCampo']))
                       {  echo $datSocio['formIngresoCuota']['NOMBRECUOTA']['valorCampo'];}?>'
    />		
   <input type="hidden"	name="datSocio[formIngresoCuota][IMPORTECUOTAANIOEL]"
          value='<?php if (isset($datSocio['formIngresoCuota']['IMPORTECUOTAANIOEL']['valorCampo']))
                       {  echo $datSocio['formIngresoCuota']['IMPORTECUOTAANIOEL']['valorCampo'];}?>'
    />		
   <input type="hidden"	name="datSocio[formIngresoCuota][IMPORTECUOTAANIOSOCIO]"
          value='<?php if (isset($datSocio['formIngresoCuota']['IMPORTECUOTAANIOSOCIO']['valorCampo']))
                       {  echo $datSocio['formIngresoCuota']['IMPORTECUOTAANIOSOCIO']['valorCampo'];}?>'
    />			
			<input type="hidden"	name="datSocio[datosFormSocio][CODAGRUPACION]"
          value='<?php if (isset($datSocio['datosFormSocio']['CODAGRUPACION']['valorCampo']))
                       {  echo $datSocio['datosFormSocio']['CODAGRUPACION']['valorCampo'];}?>'
    />		
			<input type="hidden"	name="datSocio[datosFormSocio][NOMAGRUPACION]"
			       value='<?php if (isset($datSocio['datosFormSocio']['NOMAGRUPACION']['valorCampo']))
                       {  echo $datSocio['datosFormSocio']['NOMAGRUPACION']['valorCampo'];}?>'
    />						
   <input type="hidden" name="datSocio[datosFormMiembro][APE1]"
          value='<?php if (isset($datSocio['datosFormMiembro']['APE1']['valorCampo']))
                       {  echo " ".$datSocio['datosFormMiembro']['APE1']['valorCampo'];}?>'
   />	
   <input type="hidden" name="datSocio[datosFormMiembro][APE2]"
          value='<?php if (isset($datSocio['datosFormMiembro']['APE2']['valorCampo']))
                       {  echo " ".$datSocio['datosFormMiembro']['APE2']['valorCampo'];}?>'
   />	
   <input type="hidden" name="datSocio[datosFormMiembro][NOM]"
       			value='<?php if (isset($datSocio['datosFormMiembro']['NOM']['valorCampo']))
                       {  echo " ".$datSocio['datosFormMiembro']['NOM']['valorCampo'];}?>'
			/>																			
			<!-- Este campo IMPORTEGASTOSABONOCUOTA viene de la tabla ORDENES_COBRO y son los gastos asociados a una remesa (emisión y devolución si la hubiese) 
								y podrá ser distinto a lo que se guarda en CUOTAANIOSOCIO  que podría llegar a guardar los gastos de mas 
								de una remesa en el año si hubiese habido devoluciones --> 
			<input type="hidden" name="datSocio[formOrdenesCobro][IMPORTEGASTOSABONOCUOTA]"
									value='<?php if (isset($datSocio['formOrdenesCobro']['IMPORTEGASTOSABONOCUOTA']['valorCampo']))
									             {  echo " ".$datSocio['formOrdenesCobro']['IMPORTEGASTOSABONOCUOTA']['valorCampo'];}?>'																						
				/>	
			<!-- DEVOLUCIONES para campo observaciones poner la cuanta que desparecerá de CUAOTAANISOCIO-->
			<input type="hidden" name="datSocio[formOrdenesCobro][CUENTAIBAN]"
									value='<?php if (isset($datSocio['formOrdenesCobro']['CUENTAIBAN']['valorCampo']))
																						{  echo " ".$datSocio['formOrdenesCobro']['CUENTAIBAN']['valorCampo'];}?>'																						
				/>
			<input type="hidden" name="datSocio[formOrdenesCobro][FECHAPAGO]"
									value='<?php if (isset($datSocio['formOrdenesCobro']['FECHAPAGO']['valorCampo']))
																						{  echo " ".$datSocio['formOrdenesCobro']['FECHAPAGO']['valorCampo'];}?>'																						
				/>								

		<!-- ******************** Fin hidden formActualizarIngresoCuota ********************** -->			
	
		<!-- ****************** Inicio Nombre del socio *************************** -->
	 <fieldset>
	  <legend><strong>Nombre socio/a</strong></legend>
		 <p>
					<span class="mostrar">
      <?php 
						echo $datSocio['datosFormMiembro']['APE1']['valorCampo'];   
							      if (isset($datSocio['datosFormMiembro']['APE2']['valorCampo']))
             {  echo " ".$datSocio['datosFormMiembro']['APE2']['valorCampo'];}
													echo ", ".$datSocio['datosFormMiembro']['NOM']['valorCampo'];
       ?>					
					</span>
					<br />
			<label>Estado socio/a (alta, baja, ...)</label> 
	    <input type="text" readonly
											 class="mostrar"		
	           name="datSocio[datosFormUsuario][ESTADO]"
	           value='<?php if (isset($datSocio['datosFormUsuario']['ESTADO']['valorCampo']))
	           {  echo $datSocio['datosFormUsuario']['ESTADO']['valorCampo'];}
											?>'
         size="24"
         maxlength="24"												
	    />
					<br />
		</p>	
 </fieldset>
	<!-- ********************** Fin Nombre del socio ****************************** --> 
		<br />		
	 <!-- ************ Inicio Datos de IngresoCuota ******************************* -->
	 <fieldset>
		<legend><strong>Cuota del año <?php echo $datSocio['formIngresoCuota']['ANIOCUOTA']['valorCampo'] ?> </strong></legend>
	 <p>			 
			<?php
	   echo "<span class='comentario11'>Agrupación socio/a en ".$datSocio['formIngresoCuota']['ANIOCUOTA']['valorCampo']."</span>";
			 			
				echo "<span class='mostrar'>".$datSocio['datosFormSocio']['NOMAGRUPACION']['valorCampo']."</span><br />";		
							
				echo "<span class='comentario11'>Cuota tipo </span>";
				echo "<span class='mostrar'>".$datSocio['formIngresoCuota']['CODCUOTA']['valorCampo']."</span>";		
				echo "<span class='comentario11'>elegida por el socio/a para el año ".$datSocio['formIngresoCuota']['ANIOCUOTA']['valorCampo'].
				     " es </span>";
			 echo "<span class='mostrar'>".$datSocio['formIngresoCuota']['IMPORTECUOTAANIOSOCIO']['valorCampo']."</span>";
				echo "<span class='comentario11'> euros</span><br />";	
   ?> 
			
  <label>Estado de la cuota <?php echo $datSocio['formIngresoCuota']['ANIOCUOTA']['valorCampo'] ?></label>  
  <input type="text" readonly 
		       class='mostrar'
         name="datSocio[formIngresoCuota][ESTADOCUOTA]"
         value='<?php if (isset($datSocio['formIngresoCuota']['ESTADOCUOTA']['valorCampo']))
                      {  echo $datSocio['formIngresoCuota']['ESTADOCUOTA']['valorCampo'];}
																					?>'
         size="30"
         maxlength="30"
  />	
 		<span class="error">
			<?php
			if (isset($datSocio['formIngresoCuota']['ESTADOCUOTA']['errorMensaje']))
			{echo $datSocio['formIngresoCuota']['ESTADOCUOTA']['errorMensaje'];}
			?>
		</span>	
		<br />	
		<label>Cobrada en la cuenta del socio/a</label>   
  <input type="text" readonly 
		       class='mostrar'
         name="datSocio[formIngresoCuota][CUENTAPAGO]"
         value='<?php if (isset($datSocio['formIngresoCuota']['CUENTAPAGO']['valorCampo']))
                      {  echo $datSocio['formIngresoCuota']['CUENTAPAGO']['valorCampo'];}
																					?>'
         size="40"
         maxlength="40"
  />	
 		<span class="error">
			<?php
			if (isset($datSocio['formIngresoCuota']['CUENTAPAGO']['errorMensaje']))
			{echo $datSocio['formIngresoCuota']['CUENTAPAGO']['errorMensaje'];}
			?>
		</span>
		<br />	

		<label>Nombre del archivo de cobro domiciliado SEPA-XML </label>   
  <input type="text" readonly 
		       class='mostrar'
         name="datSocio[formIngresoCuota][NOMARCHIVOSEPAXML]"
         value='<?php if (isset($datSocio['formIngresoCuota']['NOMARCHIVOSEPAXML']['valorCampo']))
                      {  echo $datSocio['formIngresoCuota']['NOMARCHIVOSEPAXML']['valorCampo'];}
																					?>'
         size="50"
         maxlength="60"
  />	

 		<span class="error">
			<?php
			if (isset($datSocio['formIngresoCuota']['NOMARCHIVOSEPAXML']['errorMensaje']))
			{echo $datSocio['formIngresoCuota']['NOMARCHIVOSEPAXML']['errorMensaje'];}
			?>
		</span>
		<br />
		
	 <span class="error">
					<?php //echo "datSocio:"; print_r($datSocio);  echo "<br />"; 
					//if (isset($datSocio['formIngresoCuota']['NOMARCHIVOSEPAXML']['valorCampo']) && !empty($datSocio['formIngresoCuota']['NOMARCHIVOSEPAXML']['valorCampo']) && 
					//    $datSocio['formIngresoCuota']['ESTADOCUOTA']['valorCampo'] == 'PENDIENTE-COBRO') 				
					if (isset($datSocio['formOrdenesCobro']['NOMARCHIVOSEPAXML']['valorCampo']) && !empty($datSocio['formOrdenesCobro']['NOMARCHIVOSEPAXML']['valorCampo']) && 
					    $datSocio['formOrdenesCobro']['ESTADOCUOTA']['valorCampo'] == 'PENDIENTE-COBRO') 	
					{
									echo "<br /><br /><strong>*** PENDIENTE PROXIMO COBRO POR BANCO DE LA REMESA \"".$datSocio['formOrdenesCobro']['NOMARCHIVOSEPAXML']['valorCampo']. 
									"\" EL DIA ".$datSocio['formOrdenesCobro']['FECHAORDENCOBRO']['valorCampo']." *** ".
									"</strong><br /><br />";
					}
					?>
		</span> 	
		<br />
<!-- -->

		<!-- inicio gastos cuota ----------------------------->
			<?php 
    echo "<label>Gastos al abonar o devolver la cuota  si los hubiese (cobrados por PayPal o la entidad bancaria)</label>";
				?>				
			    <input type="text" readonly 
							     class='mostrar'
	           name="datSocio[formIngresoCuota][IMPORTEGASTOSABONOCUOTA]"
	           value='<?php if (isset($datSocio['formIngresoCuota']['IMPORTEGASTOSABONOCUOTA']['valorCampo']))
	           {  echo $datSocio['formIngresoCuota']['IMPORTEGASTOSABONOCUOTA']['valorCampo'];}
											?>'
	           size="12"
	           maxlength="20"
	    />
		<span class='comentario11'> euros</span>
		<span class="error">
			<?php
			if (isset($datSocio['formIngresoCuota']['IMPORTEGASTOSABONOCUOTA']['errorMensaje']))
			{echo $datSocio['formIngresoCuota']['IMPORTEGASTOSABONOCUOTA']['errorMensaje'];}
			
			?>		
		</span>		
		<br />		
					<?php 
    echo "<label>Añadir más <strong>GASTOS</strong> si los hubiese, a los ya existentes al abonar la cuota o por devolución (PayPal o bancos)</label>";
				?>				
			    <input type="text"
	           name="datSocio[formIngresoCuota][ADD_IMPORTEGASTOSABONOCUOTA]"
	           value='<?php 
												if (isset($datSocio['formIngresoCuota']['ADD_IMPORTEGASTOSABONOCUOTA']['valorCampo']))
	           {  echo $datSocio['formIngresoCuota']['ADD_IMPORTEGASTOSABONOCUOTA']['valorCampo'];
											 }
												else
												{  echo "0.00";													
												}	
	           ?>'
	           size="12"
	           maxlength="20"
	    />
		<span class='comentario11'> euros</span>
		<span class="error">
			<?php
			if (isset($datSocio['formIngresoCuota']['ADD_IMPORTEGASTOSABONOCUOTA']['errorMensaje']))
			{echo $datSocio['formIngresoCuota']['ADD_IMPORTEGASTOSABONOCUOTA']['errorMensaje'];}
			?>		
		</span>		
					<?php 
    echo "<label>Nota: Si se necesitase disminuir la cantidad anotada en gastos, se pueden escribir cantidades con con el signo menos delante (-) para que reste</label>";
				?>				

	<!--	fin gastos cuota--->
		
		<br /><br />	
			<?php 
    echo "<label>Cuota <strong>".$datSocio['formIngresoCuota']['ANIOCUOTA']['valorCampo'].
				" PAGADA</strong> por el socio/a (total ingresado sin descontar gastos de cobro por PayPal o bancos)</label>";

				?>					
	    <input type="text"
	           name="datSocio[formIngresoCuota][IMPORTECUOTAANIOPAGADA]"
	           value='<?php if (isset($datSocio['formIngresoCuota']['IMPORTECUOTAANIOPAGADA']['valorCampo']))
	           {  echo $datSocio['formIngresoCuota']['IMPORTECUOTAANIOPAGADA']['valorCampo'];}
	           ?>'
	           size="12"
	           maxlength="20"
	    />
		<span class='comentario11'> euros</span>
		<span class="error">
			<?php
			if (isset($datSocio['formIngresoCuota']['IMPORTECUOTAANIOPAGADA']['errorMensaje']))
			{echo $datSocio['formIngresoCuota']['IMPORTECUOTAANIOPAGADA']['errorMensaje'];}
			
			?>		
		</span>	
<br />
		
			<?php
			
				// echo "<span class='error'> 1 datosSocio['formIngresoCuota']['ORDENARCOBROBANCO']['valorCampo']:";
				//	echo $datSocio['formIngresoCuota']['ORDENARCOBROBANCO']['valorCampo'];echo "</span>";		
	//-- Inicio radio button para poder incluir excluir de la próxima lista de cobro por banco de la siguiente cuota ---  

		  if (
									//incluye cuentas españolas y extranjeras
								 (
										(isset($datSocio['datosFormSocio']['CUENTAIBAN']['valorCampo']) && 
			        !empty($datSocio['datosFormSocio']['CUENTAIBAN']['valorCampo'])
									 ) 
										|| //descomentar si se quiere incluir a bancos extranjeros
									 (isset($datSocio['datosFormSocio']['CUENTANOIBAN']['valorCampo']) && 
			        !empty($datSocio['datosFormSocio']['CUENTANOIBAN']['valorCampo'])
									 )										
									) &&									
								 ($datSocio['formIngresoCuota']['ANIOCUOTA']['valorCampo'] == date('Y')//Para que no se mueste en años anteriores        
								 )											
							)							
				{
					?>
					<br />

					<label>*Incluir en la próxima lista de órdenes de cobro a los bancos (aún no se puede con c. extranjeras) 
					 <strong>del año <?php echo $datSocio['formIngresoCuota']['ANIOCUOTA']['valorCampo'] ?> </strong>: 
					</label>
		
		   <span class="error">
			   <?php
			   if (isset($datSocio['formIngresoCuota']['ORDENARCOBROBANCO']['errorMensaje']))
			   {echo $datSocio['formIngresoCuota']['ORDENARCOBROBANCO']['errorMensaje'];}
			   ?>
				 </span>				
					 
	    <input type="radio"
	           name="datSocio[formIngresoCuota][ORDENARCOBROBANCO]"
	           value='SI' 
						      <?php if ($datSocio['formIngresoCuota']['ORDENARCOBROBANCO']['valorCampo']=='SI')
	           {  echo " checked";}
											?>
	    />
					<label>SI&nbsp;&nbsp;&nbsp;&nbsp;</label>
	    <input type="radio"
	           name="datSocio[formIngresoCuota][ORDENARCOBROBANCO]"
	           value='NO'
						 <?php if ($datSocio['formIngresoCuota']['ORDENARCOBROBANCO']['valorCampo']=='NO')
	           {  echo " checked";}
	           ?>						 
	    />
					<label>NO</label>		
		  <br />    


			 	<?php								
				}				
				//-- Fin radio button para poder incluir excluir de la próxima lista de cobro por banco de la siguiente cuota ---
				
			 echo "<label>Cambiar estado actual de pago de la cuota a</label>"; 
				
   $parValorEstadoCuota = array("ABONADA"=>"Abonada",
																												   	"ABONADA-PARTE"=>"Abonada en parte",
																												 	  "NOABONADA-DEVUELTA"=>"Devuelta, no abonada",
																														  "NOABONADA-ERROR-CUENTA"=>"Error cuenta. O J O:se borrarán datos bancarios"
																													   );	
																																
																						
				//if ($datSocio['formIngresoCuota']['CODCUOTA']['valorCampo']!=='General')//Para salga EXENTO
    if ($datSocio['formIngresoCuota']['IMPORTECUOTAANIOEL']['valorCampo'] == 0)				
				{	
				 $parValorEstadoCuota["EXENTO"]= 'Exento de pago';//(Honorario, ...)
					
					unset ($parValorEstadoCuota["ABONADA"]);
					unset ($parValorEstadoCuota["ABONADA-PARTE"]);		
				}
				elseif ($datSocio['formIngresoCuota']['ANIOCUOTA']['valorCampo'] == date('Y')//Para que no se mueste en años anteriores        
							     )					
				{ $parValorEstadoCuota["PENDIENTE-COBRO"]= 'Pendiente de cobro por EL';
				}
				else 
				{ $parValorEstadoCuota["NOABONADA"]= 'No abonada causa desconocida';			 
				}																																																																	
	
 	  echo comboLista($parValorEstadoCuota,"datSocio[formIngresoCuota][ESTADOCUOTA]",
			                $datSocio['formIngresoCuota']['ESTADOCUOTA']['valorCampo'],
	                  $parValorEstadoCuota[$datSocio['formIngresoCuota']['ESTADOCUOTA']['valorCampo']],"","");
		?>

		<br />
			<label>Modo de pago</label> 
			<?php 			
		 $parValorModoIngreso=array("SIN-DATOS"=>"Sin datos","DOMICILIADA"=>"Domiciliada","TRANSFERENCIA"=>"Transferencia",
			                           "TARJETA"=>"Tarjeta","PAYPAL"=>"PayPal","CHEQUE"=>"Cheque","METALICO"=>"Metálico");
				 //function comboLista($parValor,$identificadorCampo,$valorPrevio,$descPrevio,$valorInicial,$descInicial)	 
  	echo comboLista($parValorModoIngreso,"datSocio[formIngresoCuota][MODOINGRESO]",
			                $datSocio['formIngresoCuota']['MODOINGRESO']['valorCampo'],
		                 $parValorModoIngreso[$datSocio['formIngresoCuota']['MODOINGRESO']['valorCampo']],"","");	
		?>
		<br />
		
		
		
	 <!-- ********** Inicio datSocio[FECHAPAGO] ********* -->
		<label>Fecha del ingreso realizado por el socio/a (dd/mm/aaaa)</label> 
		<?php 
   	 //lo referente a fecha podría ser un requiere_once parValorFechas
 		$parValorDia["00"]="día"; 
		 for ($d=1;$d<=31;$d++) 
		 { if ($d<10) {$valor="0"."$d";}//para que los días tengan el formato 01, 02,...10,...31
			 else {$valor="$d";}
			 $parValorDia[$valor]=$valor;
		 }
		 //function comboLista($parValor,$identificadorCampo,$valorPrevio,$descPrevio,$valorInicial,$descInicial)
		 echo comboLista($parValorDia,"datSocio[formIngresoCuota][FECHAPAGO][dia]",
			                $datSocio['formIngresoCuota']['FECHAPAGO']['dia']['valorCampo'],
			                $parValorDia[$datSocio['formIngresoCuota']['FECHAPAGO']['dia']['valorCampo']],"","");					
										 	
		 $parValorMes=array("00"=>"mes","01"=>"Enero","02"=>"Febrero","03"=>"Marzo","04"=>"Abril","05"=>"Mayo","06"=>"Junio",
		 "07"=>"Julio","08"=>"Agosto","09"=>"Septiembre","10"=>"Octubre","11"=>"Noviembre","12"=>"Diciembre");
					 
  	echo comboLista($parValorMes,"datSocio[formIngresoCuota][FECHAPAGO][mes]",
			                $datSocio['formIngresoCuota']['FECHAPAGO']['mes']['valorCampo'],
		                 $parValorMes[$datSocio['formIngresoCuota']['FECHAPAGO']['mes']['valorCampo']],"","");	 		 

		 $parValorAnio["0000"]="año"; 		 
		 for ($a=date("Y")-5; $a<=date("Y"); $a++){$parValorAnio[$a]=$a;} 
		 echo comboLista($parValorAnio,"datSocio[formIngresoCuota][FECHAPAGO][anio]",
			                $datSocio['formIngresoCuota']['FECHAPAGO']['anio']['valorCampo'],
			                $parValorAnio[$datSocio['formIngresoCuota']['FECHAPAGO']['anio']['valorCampo']],"","");			
		 ?>	
 		<span class="error">
			<?php
			if (isset($datSocio['formIngresoCuota']['FECHAPAGO']['errorMensaje']))
			{echo $datSocio['formIngresoCuota']['FECHAPAGO']['errorMensaje'];}
			?>
		</span>	
		<!-- *********** Fin datSocio[FECHAPAGO] *********** -->
		  <br />
		<label>Fecha de la anotación por tesorería <b><?php echo date("d/m/Y") ?> </b></label> 				

		</p>
	 </fieldset>
		
	 <br />
		<!-- **** Inicio actualizar tabla ORDENES_COBRO ORDENES_COBRO: fecha y motivo DEVOLUCIONES ***** -->
		<?php 
		if(isset($datSocio['formIngresoCuota']['NOMARCHIVOSEPAXML']['valorCampo']) && !empty($datSocio['formIngresoCuota']['NOMARCHIVOSEPAXML']['valorCampo']))
		{
			?>		
				<fieldset>
					<legend><strong>Rellenar SÓLO en caso DEVOLUCIÓN POR EL BANCO de pago domiciliado (aquí no se incluyen otro tipo de devoluciones, ni PayPal)</strong></legend>
					<p>				
      <!-- *********** Inicio datSocio[FECHADEVOLUCION] *********** -->		
						
						<label>Fecha devolución banco (debe ser superior a fecha de abono e inferior o igual a día de hoy):</label>  
						<?php 

							$parValorDia["00"]="día"; 
							for ($d=1;$d<=31;$d++) 
							{ if ($d<10) {$valor="0"."$d";}//para que los días tengan el formato 01, 02,...10,...31
								else {$valor="$d";}
								$parValorDia[$valor]=$valor;
							}
							//function comboLista($parValor,$identificadorCampo,$valorPrevio,$descPrevio,$valorInicial,$descInicial)
							echo comboLista($parValorDia,"datSocio[formOrdenesCobro][FECHADEVOLUCION][dia]",
																							$datSocio['formOrdenesCobro']['FECHADEVOLUCION']['dia']['valorCampo'],
																							$parValorDia[$datSocio['formOrdenesCobro']['FECHADEVOLUCION']['dia']['valorCampo']],"","");					
																
							$parValorMes=array("00"=>"mes","01"=>"Enero","02"=>"Febrero","03"=>"Marzo","04"=>"Abril","05"=>"Mayo","06"=>"Junio",
							"07"=>"Julio","08"=>"Agosto","09"=>"Septiembre","10"=>"Octubre","11"=>"Noviembre","12"=>"Diciembre");
										
							echo comboLista($parValorMes,"datSocio[formOrdenesCobro][FECHADEVOLUCION][mes]",
																							$datSocio['formOrdenesCobro']['FECHADEVOLUCION']['mes']['valorCampo'],
																							$parValorMes[$datSocio['formOrdenesCobro']['FECHADEVOLUCION']['mes']['valorCampo']],"","");	 		 

							$parValorAnio["0000"]="año"; 		 
							for ($a=date("Y")-5; $a<=date("Y"); $a++){$parValorAnio[$a]=$a;} 
							echo comboLista($parValorAnio,"datSocio[formOrdenesCobro][FECHADEVOLUCION][anio]",
																							$datSocio['formOrdenesCobro']['FECHADEVOLUCION']['anio']['valorCampo'],
																							$parValorAnio[$datSocio['formOrdenesCobro']['FECHADEVOLUCION']['anio']['valorCampo']],"","");			
							?>	
							<span class="error">
							<?php
							if (isset($datSocio['formOrdenesCobro']['FECHADEVOLUCION']['errorMensaje']))
							{echo $datSocio['formOrdenesCobro']['FECHADEVOLUCION']['errorMensaje'];}
							?>
						</span>	
						<!-- *********** Fin datSocio[FECHADEVOLUCION] *********** -->		
						<br />
							<label>Motivo devolución</label>  
							<input type="text" 
														name="datSocio[formOrdenesCobro][MOTIVODEVOLUCION]"
														value='<?php if (isset($datSocio['formOrdenesCobro']['MOTIVODEVOLUCION']['valorCampo']))
																											{  echo $datSocio['formOrdenesCobro']['MOTIVODEVOLUCION']['valorCampo'];}
 																					?>'
														size="80"
														maxlength="120"
														/>		
							<span class="error">
								<?php 
									//if (isset($datSocio['formOrdenesCobro']['MOTIVODEVOLUCION']['errorMensaje']))
									//{ echo $datSocio['formOrdenesCobro']['MOTIVODEVOLUCION']['errorMensaje'];
									//}
								?>
						</span>					
					</p>
				</fieldset>
				<?php
		}
		?>	
  <!-- **** Fin actualizar tabla ORDENES_COBRO: fecha y motivo DEVOLUCIONES ***** -->
		
	 <br />
		<!-- ********** Inicio de datSocio[OBSERVACIONES] ******* -->
	 <fieldset>	 
	  <legend><b>Observaciones cuota</b>	
	  <?php if (isset($datSocio['formIngresoCuota']['ANIOCUOTA']['valorCampo']) 
				          && !empty($datSocio['formIngresoCuota']['ESTADOCUOTA']['valorCampo'])
				        )
            { echo " ( se anotará en los datos de la cuota de ".$datSocio['formIngresoCuota']['ANIOCUOTA']['valorCampo']." )";} 
				?> 																					
			
			</legend>  <!--No obligatorio -->
			<p>
		<textarea  id='OBSERVACIONES' onKeyPress="limitarTextoArea(1999,'OBSERVACIONES');"	
		class="textoAzul8Left" name="datSocio[formIngresoCuota][OBSERVACIONES]" rows="12" cols="80"><?php 
		  if (isset($datSocio['formIngresoCuota']['OBSERVACIONES']['valorCampo']))                    
			{echo htmlspecialchars(stripslashes($datSocio['formIngresoCuota']['OBSERVACIONES']['valorCampo']));}
		?></textarea> 			 
		</p>
	 </fieldset>
		 <!-- ************ Fin de datSocio[OBSERVACIONES] ********* -->
	 <br />
	 <!-- ********************** Fin Datos de IngresoCuota ************************** -->	

		<!-- ************** Inicio datSocio tabla SOCIO datos bancarios (no se modifican aquí) ***** -->
	 <fieldset>
	  <legend><strong>Domiciliación del pago de la cuota (datos bancarios)</strong></legend>
	 	<p>		
   <!-- **** Inicio datSocio datos bancario (no se modifican aquí) ***** -->
	  <?php 
		 if(isset($datSocio['datosFormSocio']['CUENTAIBAN']['valorCampo']) && 
		               $datSocio['datosFormSocio']['CUENTAIBAN']['valorCampo'] !== NULL && 
		               $datSocio['datosFormSocio']['CUENTAIBAN']['valorCampo'] !=='')
		 {
	 	 ?>		
    <label>Actualmente <?php echo date('Y-m-d');?> domicialiado en cuenta bancaria IBAN:</label>  
    <input type="text" readonly 
				       class='mostrar'
           name="datSocio[datosFormSocio][CUENTAIBAN]"
           value='<?php if (isset($datSocio['datosFormSocio']['CUENTAIBAN']['valorCampo']))
                        {  echo $datSocio['datosFormSocio']['CUENTAIBAN']['valorCampo'];}
																		?>'
           size="50"
           maxlength="50"
    /> 		
    <label>Tipo secuencia de pago</label>  
    <input type="text" readonly 
				       class='mostrar'
           name="datSocio[datosFormSocio][SECUENCIAADEUDOSEPA]"
           value='<?php if (isset($datSocio['datosFormSocio']['SECUENCIAADEUDOSEPA']['valorCampo']))
                        {  echo $datSocio['datosFormSocio']['SECUENCIAADEUDOSEPA']['valorCampo'];}
                  ?>'
           size="4"
           maxlength="10"
    /> 				
	
	  	<?php //echo "<span class='comentario11'>Tipo secuencia de pago </span>"; echo "<span class='mostrar'>".
		      //      $datSocio['datosFormSocio']['SECUENCIAADEUDOSEPA']['valorCampo']."</span>";	
								echo "<span class='comentario11'>(RCUR: ya cobrado otras veces, FRST: no cobrado antes)</span>";						
		 } 
		 elseif (isset($datSocio['datosFormSocio']['CUENTANOIBAN']['valorCampo']) && 
		              $datSocio['datosFormSocio']['CUENTANOIBAN']['valorCampo'] !==NULL && 
		              $datSocio['datosFormSocio']['CUENTANOIBAN']['valorCampo'] !=='')
	 	{
		  ?>	
	   <label>Núm. cuenta bancaria NO IBAN</label> 
	    <input type="text" readonly
					       class="mostrar"	
	           name="datSocio[datosFormSocio][CUENTANOIBAN]"
	           value='<?php if (isset($datSocio['datosFormSocio']['CUENTANOIBAN']['valorCampo']))
	                        {  echo $datSocio['datosFormSocio']['CUENTANOIBAN']['valorCampo'];}
																			?>'
	           size="50"
	           maxlength="100"
	    /> 	

			 					
		  <?php 
		 }//!isset($datSocio['CUENTANOIBAN']['valorCampo']) && $datSocio['CUENTANOIBAN']['valorCampo'] !==NULL...	
		 else 
		 { 
	  	?>
			 <label>Actualmente <?php echo date('Y-m-d');?> no está domicialiado el pago de la cuota</label> 		
		  <?php	
		 } //else
		 ?>
		 </p>
	 </fieldset>
  <!-- **** Fin datSocio tabla SOCIO datos bancarios (no se modifican aquí) ***** -->		
				
		<br /> 
		
	 <!-- ********************** Inicio datos personales de SOCIO ************** -->	
	 <fieldset>	 
	  <legend><strong>Datos personales</strong></legend>	
		<p>
		<label>Documento</label>		
	    <input type="text" readonly
											 class="mostrar"		
	           name="datSocio[datosFormMiembro][TIPODOCUMENTOMIEMBRO]"
	           value='<?php if (isset($datSocio['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']['valorCampo']))
	           {  echo $datSocio['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']['valorCampo'];}
	           ?>'
	           size="9"
	           maxlength="20"
	    />	
	  <label>País documento</label>
			 <input type="text" readonly
									  class="mostrar"		
			        name="datSocio[datosFormMiembro][CODPAISDOC]"
			        value='<?php if ($datSocio['datosFormMiembro']['CODPAISDOC']['valorCampo'])
			        {  echo $datSocio['datosFormMiembro']['CODPAISDOC']['valorCampo'];}
       				?>'
			        size="30"
			        maxlength="50"
	     /> 
	   <label>Nº documento</label> <!--obligatorio y se valida para NIF y NIE pero no para pasaporte-->
	    <input type="text" readonly
					       class="mostrar"	
	           name="datSocio[datosFormMiembro][NUMDOCUMENTOMIEMBRO]"
	           value='<?php if (isset($datSocio['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['valorCampo']))
	           {  echo $datSocio['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['valorCampo'];}
	           ?>'
	           size="12"
	           maxlength="20"
	    />
	  <br />
		<label>email</label>
	    <input type="text" readonly
					       class="mostrar"	
	           name="datSocio[datosFormMiembro][EMAIL]"
	           value='<?php if (isset($datSocio['datosFormMiembro']['EMAIL']['valorCampo']))
	           {  echo $datSocio['datosFormMiembro']['EMAIL']['valorCampo'];}
	           ?>'
	           size="60"
	           maxlength="200"
	    />	 
	  <br />
		 <label>Recibir emails de Europa Laica</label>					
	    <input type="text" readonly
						      class="mostrar"		
	           name="datSocio[datosFormMiembro][INFORMACIONEMAIL]"
	           value='<?php if ($datSocio['datosFormMiembro']['INFORMACIONEMAIL']['valorCampo'] =='SI')
		                          {  echo "SI";}
									              else {  echo "NO";}
																			?>'
						 					size="3"
	         		maxlength="3"	
					 />
	  <br />
	  <label>Teléfono fijo</label>
	    <input type="text" readonly
					       class="mostrar"	
	           name="datSocio[datosFormMiembro][TELFIJOCASA]"
	           value='<?php if (isset($datSocio['datosFormMiembro']['TELFIJOCASA']['valorCampo']))
	           {  echo $datSocio['datosFormMiembro']['TELFIJOCASA']['valorCampo'];}
 											?>'
	           size="14"
	           maxlength="14"
	    />	 
			<label>Teléfono móvil</label>
     <input type="text" readonly
					       class="mostrar"	
	           name="datSocio[datosFormMiembro][TELMOVIL]"
	           value='<?php if (isset($datSocio['datosFormMiembro']['TELMOVIL']['valorCampo']))
	           {  echo $datSocio['datosFormMiembro']['TELMOVIL']['valorCampo'];}
	           ?>'
	           size="14"
	           maxlength="14"
	    />	
  <br /><br />
		<label>Agrupación actual del socio/a </label>				
			 <span class='mostrar'><?php echo $datSocio['datosFormSocio']['NOMAGRUPACION']['valorCampo'] ?>	</span>
  <br />			
		<label>Fecha de alta como soci@ (dd/mm/aaaa)</label> 
	    <input type="text" readonly
						 class="mostrar"		
	           name="datSocio[datosFormSocio][FECHAALTA][dia]"
	           value='<?php if (isset($datSocio['datosFormSocio']['FECHAALTA']['dia']['valorCampo'])
						         && $datSocio['datosFormSocio']['FECHAALTA']['dia']['valorCampo']!=='00')
	           {  echo $datSocio['datosFormSocio']['FECHAALTA']['dia']['valorCampo'];}
	           ?>'
	           size="2"
	           maxlength="2"
	    />
	    <input type="text" readonly
						 class="mostrar"		
	           name="datSocio[datosFormSocio][FECHAALTA][mes]"
	           value='<?php if (isset($datSocio['datosFormSocio']['FECHAALTA']['mes']['valorCampo'])
						         && $datSocio['datosFormSocio']['FECHAALTA']['mes']['valorCampo']!=='00')
	           {  echo $datSocio['datosFormSocio']['FECHAALTA']['mes']['valorCampo'];}
	           ?>'
	           size="2"
	           maxlength="2"
	    />
					
	    <input type="text" readonly
						 class="mostrar"		
	           name="datSocio[datosFormSocio][FECHAALTA][anio]"
	           value='<?php if (isset($datSocio['datosFormSocio']['FECHAALTA']['anio']['valorCampo'])
						         && $datSocio['datosFormSocio']['FECHAALTA']['anio']['valorCampo']!=='0000')
	           {  echo $datSocio['datosFormSocio']['FECHAALTA']['anio']['valorCampo'];}
	           ?>'
	           size="4"
	           maxlength="4"
	    />						
		</p>
	 </fieldset>
	 <br />	
	 <!-- ********************** Fin datos personales de SOCIO ************** -->		

	 <!-- *************************** Inicio  datosFormDomicilio ******************* --> 	
	 <fieldset>	
	  <legend><strong>Domicilio</strong></legend>
		<p>	
		
		 <label>País domicilio</label>		
			<input type="text" readonly
						    class="mostrar"		
			       name="datSocio[datosFormDomicilio][CODPAISDOM]"
			       value='<?php if ($datSocio['datosFormDomicilio']['CODPAISDOM']['valorCampo'])
			       {  echo $datSocio['datosFormDomicilio']['CODPAISDOM']['valorCampo'];}
 									?>'
			       size="30"
			       maxlength="50"
				     />					
		 <br />		

   <!--***** Tipo vía mejor que lo escriban, se evita problemas con otros países *****-->	
		<label>Dirección</label> <!-- no se valida tipos datos-->	
	    <input type="text" readonly
						 	    class="mostrar"			
	           name="datSocio[datosFormDomicilio][DIRECCION]"
	           value='<?php if (isset($datSocio['datosFormDomicilio']['DIRECCION']['valorCampo']))
                         {  echo " ".$datSocio['datosFormDomicilio']['DIRECCION']['valorCampo'];}
																			?>'
	           size="60"
	           maxlength="255"
	    />		
	  <br />	
		<label>Código postal</label>	
	    <input type="text" readonly
						      class="mostrar"			
	           name="datSocio[datosFormDomicilio][CP]"
	           value='<?php if (isset($datSocio['datosFormDomicilio']['CP']['valorCampo']))
	                        {  echo $datSocio['datosFormDomicilio']['CP']['valorCampo'];}
																			?>'
	           size="6"
	           maxlength="10"
	    />		
		<label>Localidad</label>	
	    <input type="text" readonly
						      class="mostrar"			
	           name="datSocio[datosFormDomicilio][LOCALIDAD]"
	           value='<?php if (isset($datSocio['datosFormDomicilio']['LOCALIDAD']['valorCampo']))
	                        {  echo $datSocio['datosFormDomicilio']['LOCALIDAD']['valorCampo'];}
	                  ?>'
	           size="50"
	           maxlength="255"
	    />
		<br />			
  <?php 
		 if (isset($datSocio['datosFormDomicilio']['CODPAISDOM']['valorCampo']) && 
		           $datSocio['datosFormDomicilio']['CODPAISDOM']['valorCampo']=='ES'
		          )
   { 
			?>
		<label>Provincia</label>	
	    <input type="text" readonly
						      class="mostrar"			
	           name="datSocio[datosFormDomicilio][NOMPROVINCIA]"
	           value='<?php if (isset($datSocio['datosFormDomicilio']['NOMPROVINCIA']['valorCampo']))
	                        {  echo $datSocio['datosFormDomicilio']['NOMPROVINCIA']['valorCampo'];}
																		?>'
	           size="50"
	           maxlength="255"
	    />											
			<?php 
			 }
	  ?>		
		<br />	
		 <label>Recibir cartas de Europa Laica</label>
	    <input type="text" readonly
						      class="mostrar"		
	           name="datSocio[datosFormMiembro][INFORMACIONCARTAS]"
						 	    value='<?php if ($datSocio['datosFormMiembro']['INFORMACIONCARTAS']['valorCampo']=='SI')
		                          {  echo "SI";}
									        else {  echo "NO";}						 
	           ?>'
					       size="3"
					       maxlength="3"						 
	    />
		</p>
	 </fieldset>
	 <br />	
	 <!-- ******************* Fin datosFormMiembro Domicilio *********************** -->	
	
		<!-- ********************** Inicio Botones de formActualizarIngresoCuotas *************** -->  		
 	<div align="center">
			<input type="submit" name="comprobarYactualizar" value="Guardar datos actualizados">		
			 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		 <input type='submit' name="salirSinActualizar" value="No actualizar datos"
		       onClick="return confirm('¿Salir sin guardar los campos actualizados del formulario?')">	
		</div>							
		<!-- ************************* Fin Botones de formActualizarIngresoCuota *************** -->
 </form>
 <br />
<!-- &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
 <form method="post" class="linea"
	     action="./index.php?controlador=controladorSocios&amp;accion=actualizarSocio"
			 onSubmit="return confirm('¿Salir sin guardar los campos actualizados del formulario?')">		
	  <input type="submit" name="salirSinActualizar" value="No actualizar datos">
 </form>
 <form method="post" class="linea"
	     action="./index.php?controlador=controladorLogin&amp;accion=logOut"		
			 onSubmit="return confirm('¿Salir sin guardar los campos actualizados en el formulario?')">		
	  <input type="submit" name="salirSinActualizar" value="Salir sin actualizar datos">
 </form>
 -->
</div>
