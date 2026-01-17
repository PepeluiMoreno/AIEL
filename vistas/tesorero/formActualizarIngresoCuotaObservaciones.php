<?php
/*--------------------------------------------------------------------------------------------------
FICHERO: formActualizarIngresoCuotaObservaciones.php
VERSION: PHP 7.3.21
	
DESCRIPCION: 

B- formActualizarIngresoCuotaObservaciones.php: "caso de SÍ tener un cobro de remesa en el banco pendiente"

			Es el form alternativo y es para pendientes de cobro de remesa en el banco, ya emitida 
			(sólo cuando en tabla "ORDENES_COBRO" el ESTADOCUOTA = PENDIENTE-COBRO), para ese socio y año 
			y por eso solo se permite	cambiar los campos "Observaciones y Motivo devolución" para evitar 
			cambios en	cuotas, pagos y gastos producir inconsistencias respecto a la remesa ya enviada al banco. 


LLAMADA: cTesorero.php:actualizarIngresoCuota()-->vActualizarIngresoCuotaInc.php-->
         -->vCuerpoActualizarIngresoCuota.php

OBSERVACIONES:
El formulario alternativo es A- "formActualizarIngresoCuotaTodos.php", donde se permiten cambiar todos los
campos, pero solo para cuando no hay pendientes de cobro de remesa ya enviada al banco.
       
--------------------------------------------------------------------------------------------------*/
//require_once './modelos/libs/comboLista.php'; En este no es necesaria
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

			<?php 
			//echo "<br /><br />datSocio['formIngresoCuotaAnterior']['anteriorIMPORTECUOTAANIOPAGADA']['valorCampo']: "; print_r($datSocio['formIngresoCuotaAnterior']['anteriorIMPORTECUOTAANIOPAGADA']['valorCampo']);echo "<br /><br /> ";
			//echo "<br /><br />datSocio['formIngresoCuota']: "; print_r($datSocio['formIngresoCuota']);echo "<br /><br /> ";
			//echo "<br /><br />datSocio['formOrdenesCobro']: "; print_r($datSocio['formOrdenesCobro']);echo "<br /><br /> ";
			?>
																					
			<!-- Estos campos como ESTADOCUOTA_ANTES_REMESA, ... viene de la tabla ORDENES_COBRO y son los 
			     gastos asociados a una remesa (emisión y devolución si la hubiese) y podrá ser distinto a lo
								que se guarda en CUOTAANIOSOCIO que podría llegar a guardar los gastos de mas 
								de una remesa en el año si hubiese habido devoluciones 
			--> 
			
			<input type="hidden" name="datSocio[formOrdenesCobro][ESTADOCUOTA_ANTES_REMESA]"
									value='<?php if (isset($datSocio['formOrdenesCobro']['ESTADOCUOTA_ANTES_REMESA']['valorCampo']))
																						{  echo $datSocio['formOrdenesCobro']['ESTADOCUOTA_ANTES_REMESA']['valorCampo'];}?>'																						
				/>
			<input type="hidden" name="datSocio[formOrdenesCobro][IMPORTECUOTAANIOPAGADA_ANTES_REMESA]"
									value='<?php if (isset($datSocio['formOrdenesCobro']['IMPORTECUOTAANIOPAGADA_ANTES_REMESA']['valorCampo']))
																						{  echo $datSocio['formOrdenesCobro']['IMPORTECUOTAANIOPAGADA_ANTES_REMESA']['valorCampo'];}?>'																						
				/>	
				<input type="hidden" name="datSocio[formOrdenesCobro][FECHAPAGO_ANTES_REMESA]"
									value='<?php if (isset($datSocio['formOrdenesCobro']['FECHAPAGO_ANTES_REMESA']['valorCampo']))
																						{  echo $datSocio['formOrdenesCobro']['FECHAPAGO_ANTES_REMESA']['valorCampo'];}?>'																						
				/>					

			<!-- ******************** Fin hidden formActualizarIngresoCuota ********************** -->			
	
		<!-- ****************** Inicio Nombre del socio, estado, agrupación *************** -->
	 <fieldset>
	  <legend><strong>Socio/a</strong></legend>
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
			<?php			
    echo "<span class='comentario11'>Agrupación socio/a en ".$datSocio['formIngresoCuota']['ANIOCUOTA']['valorCampo']."</span>";			
				echo "<span class='mostrar'>".$datSocio['datosFormSocio']['NOMAGRUPACION']['valorCampo']."</span><br />";		
   ?>				
		</p>	
 </fieldset>
	<!-- ********************** Fin Nombre del socio , estado, agrupación ************** -->
		<br />		
	 <!-- ************ Inicio Datos de Ingreso Cuota año ******************************* -->
	<fieldset>
		<legend><strong>Cuota del año <?php echo $datSocio['formIngresoCuota']['ANIOCUOTA']['valorCampo'] ?> </strong></legend>
	 <p>			 
			<?php						
				echo "<span class='comentario11'>Cuota tipo </span>";
				echo "<span class='mostrar'>".$datSocio['formIngresoCuota']['CODCUOTA']['valorCampo']."</span>";		
				echo "<span class='comentario11'> mímino a pagar </span>";
				echo "<span class='mostrar'>".$datSocio['formIngresoCuota']['IMPORTECUOTAANIOEL']['valorCampo']."</span>";	
    echo "<span class='comentario11'> euros</span><br />";					
				echo "<span class='comentario11'>El socio/a ha elegido para el año ".$datSocio['formIngresoCuota']['ANIOCUOTA']['valorCampo'].
				     " la cantidad de </span>";
			 echo "<span class='mostrar'>".$datSocio['formIngresoCuota']['IMPORTECUOTAANIOSOCIO']['valorCampo']."</span>";
				echo "<span class='comentario11'> euros</span><br />";	
   ?> 
				<br />
 <!--	Inicio Estado actual de pago de la cuota a ------------------------>			
			
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
			//if (isset($datSocio['formIngresoCuota']['ESTADOCUOTA']['errorMensaje'])) 			{echo $datSocio['formIngresoCuota']['ESTADOCUOTA']['errorMensaje'];}
			?>
		</span>	
  <!--	Fin Estado actual de pago de la cuota a -------------------------->		
		
		<br />			
		<!-- Incio aviso Pendiente cobro por el banco remesa ya emitida ------->
	 <span class="error">
				<?php 
				//if (isset($datSocio['formIngresoCuota']['NOMARCHIVOSEPAXML']['valorCampo']) && !empty($datSocio['formIngresoCuota']['NOMARCHIVOSEPAXML']['valorCampo']) && 
				//    $datSocio['formIngresoCuota']['ESTADOCUOTA']['valorCampo'] == 'PENDIENTE-COBRO') 	//¿¿pudiera se también ABONADA-PARTE??	mas ambiguo		
				if (isset($datSocio['formOrdenesCobro']['NOMARCHIVOSEPAXML']['valorCampo']) && !empty($datSocio['formOrdenesCobro']['NOMARCHIVOSEPAXML']['valorCampo']) && //mejor
								( $datSocio['formOrdenesCobro']['ESTADOCUOTA']['valorCampo'] == 'PENDIENTE-COBRO' ) 	
							)
				{
						echo "<br /><br /><strong>*** PENDIENTE PROXIMO COBRO POR BANCO DE LA REMESA \"".$datSocio['formOrdenesCobro']['NOMARCHIVOSEPAXML']['valorCampo']. 
						" ***<br /><br />No modificar datos bancarios y de cuotas hasta que se cobre y anote el pago por el banco después ".
						" del \"DIA ".$datSocio['formOrdenesCobro']['FECHAORDENCOBRO']['valorCampo']."\"</strong>";
				}
				?>
		</span> 		
  <!-- Fin aviso Pendiente cobro por el banco remesa ya emitida -------->
  <br /><br />
		<!-- Inicio Total bruto pagado  ------------------------------------->
			<?php 
    echo "<label>Cuota <strong>".$datSocio['formIngresoCuota']['ANIOCUOTA']['valorCampo']." PAGADA</strong> por el socio/a (total bruto abonado)</label>";
				?>					
	    <input type="text" readonly 
					       	class='mostrar'
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
	//		if (isset($datSocio['formIngresoCuota']['IMPORTECUOTAANIOPAGADA']['errorMensaje']))			{echo $datSocio['formIngresoCuota']['IMPORTECUOTAANIOPAGADA']['errorMensaje'];		 }			
			?>		
		</span>	
  <br />
  <!-- Inicio Total bruto pagado  -------------------------------->
		
		<!-- Inicio gastos cuota --------------------------------------->
			<?php 
    echo "<label>GASTOS TOTALES, si los hubiese, al abonar y devolver la cuota  (los cobrados por PayPal, transferencia  o la entidad bancaria)</label>";
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
			//if (isset($datSocio['formIngresoCuota']['IMPORTEGASTOSABONOCUOTA']['errorMensaje']))			{echo $datSocio['formIngresoCuota']['IMPORTEGASTOSABONOCUOTA']['errorMensaje'];}
			
			?>		
		</span>		
		<!--	Fin gastos cuota -------------------------------------------------->	

		<br /><br />		
	
			<label>Modo de pago</label> 
			
	    <input type="text" readonly 
					       class='mostrar'
	           name="datSocio[formIngresoCuota][MODOINGRESO]"
	           value='<?php if (isset($datSocio['formIngresoCuota']['MODOINGRESO']['valorCampo']))
	           {  echo  $datSocio['formIngresoCuota']['MODOINGRESO']['valorCampo'];}
	           ?>'
	           size="12"
	           maxlength="20"
	    />		
		<br />	
		
	 <!-- ********** Inicio datSocio[FECHAPAGO] ********* -->
		<label>Fecha del ingreso realizado por el socio/a (dd/mm/aaaa)</label>
					<input type="text" readonly 
												class='mostrar'
												name="datSocio[formIngresoCuota][FECHAPAGO][dia]"
												value='<?php if (isset($datSocio['formIngresoCuota']['FECHAPAGO']['dia']['valorCampo']))
												{  echo  $datSocio['formIngresoCuota']['FECHAPAGO']['dia']['valorCampo'];}
												?>'
												size="2"
												maxlength="2"
	    />	
					<input type="text" readonly 
												class='mostrar'
												name="datSocio[formIngresoCuota][FECHAPAGO][mes]"
												value='<?php if (isset($datSocio['formIngresoCuota']['FECHAPAGO']['mes']['valorCampo']))
												{  echo  $datSocio['formIngresoCuota']['FECHAPAGO']['mes']['valorCampo'];}
												?>'
												size="2"
												maxlength="2"
						/>
	     <input type="text" readonly 
													class='mostrar'
													name="datSocio[formIngresoCuota][FECHAPAGO][anio]"
													value='<?php if (isset($datSocio['formIngresoCuota']['FECHAPAGO']['anio']['valorCampo']))
													{  echo  $datSocio['formIngresoCuota']['FECHAPAGO']['anio']['valorCampo'];}
													?>'
													size="4"
													maxlength="4"
	     />					
 		<span class="error">
			<?php
			if (isset($datSocio['formIngresoCuota']['FECHAPAGO']['errorMensaje']))			
			{echo $datSocio['formIngresoCuota']['FECHAPAGO']['errorMensaje'];}
			?>
		</span>	
		
		<!-- *********** Fin datSocio[FECHAPAGO] *********** -->		
			<br /><br />
		 <!-- Inicio información button para incluir excluir en la próxima lista de cobro por banco la siguiente
     			cuota solo cuando tiene IBAN y no está pendiente de cobro de una remesa ya enviada remesa-->
			
			<?php	
		  if ( //incluye cuentas españolas y extranjeras
								 (isset($datSocio['datosFormSocio']['CUENTAIBAN']['valorCampo']) && !empty($datSocio['datosFormSocio']['CUENTAIBAN']['valorCampo']) ) 
										&& 
									(!isset($datSocio['formOrdenesCobro']['ESTADOCUOTA']['valorCampo']) || $datSocio['formOrdenesCobro']['ESTADOCUOTA']['valorCampo'] !== 'PENDIENTE-COBRO')	 
									 &&									
								 ($datSocio['formIngresoCuota']['ANIOCUOTA']['valorCampo'] == date('Y'))//Para que no se muestre en años anteriores        								 											
							 )							
				{
					?>
					<label>*Incluir en la próxima lista de órdenes de cobro a los bancos (aún no se puede con c. extranjeras) 
					 <strong>del año <?php echo $datSocio['formIngresoCuota']['ANIOCUOTA']['valorCampo'] ?> </strong>: 
					</label>					
					<?php echo "<span class='mostrar'> ".$datSocio['formIngresoCuota']['ORDENARCOBROBANCO']['valorCampo']."</span>";
     echo "<br />";					
		 	}
     ?>    			
				<!-- Fin radio button para incluir excluir en la próxima lista de cobro por banco de la siguiente cuota -->	
				
		
		  <label>Fecha automática de esta modificación <b><?php echo date("d/m/Y") ?> </b></label> 				

		</p>
	 </fieldset>
		<!-- ************ Fin Datos de Ingreso Cuota año ************************** -->
		
	 <br />
		<!-- **** Inicio actualizar tabla ORDENES_COBRO ORDENES_COBRO: fecha y motivo DEVOLUCIONES ***** -->
		<?php 
		//if(isset($datSocio['formIngresoCuota']['NOMARCHIVOSEPAXML']['valorCampo']) && !empty($datSocio['formIngresoCuota']['NOMARCHIVOSEPAXML']['valorCampo']))
	 if(isset($datSocio['formIngresoCuota']['NOMARCHIVOSEPAXML']['valorCampo']) && !empty($datSocio['formIngresoCuota']['NOMARCHIVOSEPAXML']['valorCampo']) &&
			  $datSocio['formOrdenesCobro']['ESTADOCUOTA']['valorCampo'] !== 'PENDIENTE-COBRO')				
		{
			?>		
				<fieldset>
					<legend><strong>Rellenar SÓLO en caso DEVOLUCIÓN POR EL BANCO DE PAGO DOMICILIADO (aquí no se incluyen gastos de PayPal, transferencias, ...)</strong></legend>
					<p>	
						
						<!--				<label>Es necesario que "estado actual de pago de la cuota = Devuelta, No abonada", para que los datos anotados en esta sección de devolución se guarden en la bases de datos, de lo contrario se ignora lo que se escriba</label>  -->

						<label>Fecha orden de cobro para el banco </label>							
							<input type="text" readonly 
														class='mostrar'
														name="datSocio[formOrdenesCobro][FECHAORDENCOBRO]"
														value='<?php if (isset($datSocio['formOrdenesCobro']['FECHAORDENCOBRO']['valorCampo']))
														{  echo $datSocio['formOrdenesCobro']['FECHAORDENCOBRO']['valorCampo'];}
													?>'
														size="14"
														maxlength="14"
							/>
						
					 <label>Fecha de pago de cuota domiciliada por el banco </label>							
				 		<input type="text" readonly 
														class='mostrar'
														name="datSocio[formOrdenesCobro][FECHAPAGO]"
														value='<?php if (isset($datSocio['formOrdenesCobro']['FECHAPAGO']['valorCampo']))
														{  echo $datSocio['formOrdenesCobro']['FECHAPAGO']['valorCampo'];}
													?>'
														size="14"
														maxlength="14"
							/>
						<br />		
						<label>Cuenta del socio/a </label>							
								<input type="text" readonly 
															class='mostrar'
															name="datSocio[formOrdenesCobro][CUENTAIBAN]"
															value='<?php if (isset($datSocio['formOrdenesCobro']['CUENTAIBAN']['valorCampo']))
															{  echo $datSocio['formOrdenesCobro']['CUENTAIBAN']['valorCampo'];}
														?>'
															size="30"
															maxlength="40"
								/>
						<br /><br />						
						<label>Nombre del archivo de cobro domiciliado SEPA-XML CUOTAANIOSOCIO</label>							
							<input type="text" readonly 
														class='mostrar'
														name="datSocio[formIngresoCuota][NOMARCHIVOSEPAXML]"
														value='<?php if (isset($datSocio['formIngresoCuota']['NOMARCHIVOSEPAXML']['valorCampo']))
														{  echo $datSocio['formIngresoCuota']['NOMARCHIVOSEPAXML']['valorCampo'];}
													?>'
														size="30"
														maxlength="40"
							/>
						<br />
						<label>Nombre del archivo de cobro domiciliado SEPA-XML ORDENES_COBRO</label>							
							<input type="text" readonly 
														class='mostrar'
														name="datSocio[formOrdenesCobro][NOMARCHIVOSEPAXML]"
														value='<?php if (isset($datSocio['formOrdenesCobro']['NOMARCHIVOSEPAXML']['valorCampo']))
														{  echo $datSocio['formOrdenesCobro']['NOMARCHIVOSEPAXML']['valorCampo'];}
													?>'
														size="30"
														maxlength="40"
							/>
						<br /><br />	
						<!--	Inicio Estado actual de pago de orden cuota por el banco ------------------------>					
						<label>Estado de pago de esta orden bancaria por el banco</label>							
							<input type="text" readonly 
														class='mostrar'
														name="datSocio[formOrdenesCobro][ESTADOCUOTA]"
														value='<?php if (isset($datSocio['formOrdenesCobro']['ESTADOCUOTA']['valorCampo']))
														{  echo $datSocio['formOrdenesCobro']['ESTADOCUOTA']['valorCampo'];}
													?>'
														size="30"
														maxlength="40"
							/>
						<!--	Fin Estado actual de pago de orden cuota por el banco -------------------------->							
						<br /><br />

						<label>Cantidad PENDIENTE DE COBRAR por el banco en la cuenta del socio/a </label>							
						<input type="text" readonly 
													class='mostrar'
													name="datSocio[formOrdenesCobro][CUOTADONACIONPENDIENTEPAGO]"
													value='<?php if (isset($datSocio['formOrdenesCobro']['CUOTADONACIONPENDIENTEPAGO']['valorCampo']))
													{  echo $datSocio['formOrdenesCobro']['CUOTADONACIONPENDIENTEPAGO']['valorCampo'];}
												?>'
													size="12"
													maxlength="20"
						/>
						<span class='comentario11'> euros</span>
						<br />							
						<label>Cantidad PAGADA por el banco desde la cuenta del socio/a</label>							
						<input type="text" readonly 
													class='mostrar'
													name="datSocio[formOrdenesCobro][IMPORTECUOTAANIOPAGADA]"
													value='<?php if (isset($datSocio['formOrdenesCobro']['IMPORTECUOTAANIOPAGADA']['valorCampo']))
													{  echo $datSocio['formOrdenesCobro']['IMPORTECUOTAANIOPAGADA']['valorCampo'];}
												?>'
													size="12"
													maxlength="20"
						/>
						<span class='comentario11'> euros</span>
					<br /><br />					
					<label>Gastos por COBRO DE LA CUOTA domicilada por entidad bancaria</label>							
						<input type="text" readonly 
													class='mostrar'
													name="datSocio[formOrdenesCobro][IMPORTEGASTOSABONOCUOTA]"
													value='<?php if (isset($datSocio['formOrdenesCobro']['IMPORTEGASTOSABONOCUOTA']['valorCampo']))
													{  echo $datSocio['formOrdenesCobro']['IMPORTEGASTOSABONOCUOTA']['valorCampo'];}
												?>'
													size="12"
													maxlength="20"
						/>
						<span class='comentario11'> euros</span>
						<br /><br />						
						<label>Gastos al DEVOLVER la cuota domicilada por entidad bancaria</label>							
						<input type="text" readonly 
													class='mostrar'
													name="datSocio[formOrdenesCobro][IMPORTEGASTOSDEVOLUCION]"
													value='<?php if (isset($datSocio['formOrdenesCobro']['IMPORTEGASTOSDEVOLUCION']['valorCampo']))
													{  echo $datSocio['formOrdenesCobro']['IMPORTEGASTOSDEVOLUCION']['valorCampo'];}
												?>'
													size="12"
													maxlength="20"
						/>
						<span class='comentario11'> euros</span>
      <br /><br />
						
					<label>Motivo devolución </label>  
							<input type="text" 
														name="datSocio[formOrdenesCobro][MOTIVODEVOLUCION]"
														value='<?php if (isset($datSocio['formOrdenesCobro']['MOTIVODEVOLUCION']['valorCampo']))
																											{  echo $datSocio['formOrdenesCobro']['MOTIVODEVOLUCION']['valorCampo'];}
 																					?>'
														size="100"
														maxlength="500"
														/>		
							<span class="error">
								<?php 
									if (isset($datSocio['formOrdenesCobro']['MOTIVODEVOLUCION']['errorMensaje']))
									{ echo $datSocio['formOrdenesCobro']['MOTIVODEVOLUCION']['errorMensaje'];
									}
								?>
						</span>	
		   <br />
					
					<!-- *********** Inicio datSocio[FECHADEVOLUCION] *********** -->		
						<label>Fecha devolución banco (aaaa/mm/dd):</label>  
							<input type="text" readonly 
							      	class='mostrar'
														name="datSocio[formOrdenesCobro][FECHADEVOLUCION][anio]"
														value='<?php if (isset($datSocio['formOrdenesCobro']['FECHADEVOLUCION']['anio']['valorCampo']))
																											{  echo $datSocio['formOrdenesCobro']['FECHADEVOLUCION']['anio']['valorCampo'];}
 																					?>'
														size="4"
														maxlength="4"
							/>		
							<input type="text" readonly 
							      	class='mostrar'
														name="datSocio[formOrdenesCobro][FECHADEVOLUCION][mes]"
														value='<?php if (isset($datSocio['formOrdenesCobro']['FECHADEVOLUCION']['mes']['valorCampo']))
																											{  echo $datSocio['formOrdenesCobro']['FECHADEVOLUCION']['mes']['valorCampo'];}
 																					?>'
														size="2"
														maxlength="2"
							/>		
							<input type="text" readonly 
							      	class='mostrar'
														name="datSocio[formOrdenesCobro][FECHADEVOLUCION][dia]"
														value='<?php if (isset($datSocio['formOrdenesCobro']['FECHADEVOLUCION']['dia']['valorCampo']))
																											{  echo $datSocio['formOrdenesCobro']['FECHADEVOLUCION']['dia']['valorCampo'];}
 																					?>'
														size="2"
														maxlength="2"
							/>	
							<span class="error">
								<?php 
									if (isset($datSocio['formOrdenesCobro']['FECHADEVOLUCION']['errorMensaje']))
									{ echo $datSocio['formOrdenesCobro']['FECHADEVOLUCION']['errorMensaje'];
									}
								?>
						</span>							
	
						<!-- *********** Fin datSocio[FECHADEVOLUCION] *********** -->							
						<br />					
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
			        size="10"
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
					<br />
					<label>Fecha nacimiento</label>
     <input type="text" readonly
					       class="mostrar"	
	           name="datSocio[datosFormMiembro][FECHANAC]"
	           value='<?php if (isset($datSocio['datosFormMiembro']['FECHANAC']['valorCampo']) && 
												                 $datSocio['datosFormMiembro']['FECHANAC']['valorCampo'] !=='0000-00-00'
																													)
	           {  echo $datSocio['datosFormMiembro']['FECHANAC']['valorCampo'];}
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
