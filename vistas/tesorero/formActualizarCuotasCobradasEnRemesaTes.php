<?php
/*-----------------------------------------------------------------------------
FICHERO: formActualizarCuotasCobradasEnRemesaTes.php
VERSION: PHP 7.3.21

Formulario que muestra los datos de una remesa SEPA XML y pide confirmar para 
actualizar la tabla "CUOTAANIOSOCIOS", SOCIO (FRST->RCUR), "ORDENES_COBRO" 
y "REMESAS_SEPAXML" a partir de las filas de orden de pago de cada cuota en tabla 
"ORDENES_COBRO" de una remesa concreta que se buscará por "NOMARCHIVOSEPAXML"

Se elimina el archivo "NOMARCHIVOSEPAXML" del servidor una vez actualizas las 
tablas antes citadas 

Se pide fecha pago por el banco e importe de gastos y comisiones cobrados por el banco

LLAMADA: vistas/tesorero/vistas/tesorero/vCuerpovActualizarCuotasCobradasEnRemesaTes.php	
           
OBSERVACIONES: Solo se actualiza una vez que esté cobrada por el banco
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
	<span class="error">
		 <strong> *** O J O ACCIÓN IRREVERSIBLE ***</strong>			
 </span>
	
		<span class="textoAzu112Left2">
			<br /><br />
		Una vez comprobado que sea efectivo el cobro de la remesa, con fecha <strong><?php echo $arrOrdenesCobro['datosFormOrdenCobroRemesa']['FECHAORDENCOBRO']['valorCampo']?></strong>, 
		en la cuenta del banco de Europa Laica, se anotará el pago de las cuotas de los socios/as incluidas en esa remesa.
		<br />
	 (Se utilizará la tabla ORDENES_COBRO, para anotar esos pagos en la tabla "CUOTAANIOSOCIO").
 <br /><br />	
 Rellena los datos de este formulario: 		
	</span> 	

	<span class="error">		 
			<?php 
			if (isset($arrOrdenesCobro['errorMensaje']))
			{	echo "<strong> ERROR AL INTRODUCIR LOS DATOS: revisa los datos con comentarios de error en color rojo</strong>";
			}
			?>								
 </span>
	
	<br />
 <div id="formLinea">
	
  <form method="post" class="linea" 
        action="./index.php?controlador=cTesorero&amp;accion=actualizarCuotasCobradasEnRemesaTes"	
			     onSubmit="return confirm('¿Anotar el pago de las cuotas de la remesa en la tabla de cuotas de socios/as? Acción irreversible')">				
	
 	
	 <fieldset>	 
	  <legend><b>Datos de la remesa de órdenes de cobro de cuotas domiciliadas</b></legend>	
		 <p>
		  <label>Año de la cuota cobrada</label> 
	    <input type="text" readonly
						      class="mostrar"		
	           name="datosFormOrdenCobroRemesa[ANIOCUOTA]"
	           value='<?php if (isset($arrOrdenesCobro['datosFormOrdenCobroRemesa']['ANIOCUOTA']['valorCampo']))
	           {  echo $arrOrdenesCobro['datosFormOrdenCobroRemesa']['ANIOCUOTA']['valorCampo'];}
	           ?>'
	           size="6"
	           maxlength="6"
	    />
					<br />
					<label for="user">Fecha de orden de cobro por el banco</label> 
     <input type="text" readonly
						      class="mostrar"		
            name="datosFormOrdenCobroRemesa[FECHAORDENCOBRO]"
            value='<?php if (isset($arrOrdenesCobro['datosFormOrdenCobroRemesa']['FECHAORDENCOBRO']['valorCampo']))
                       {  echo $arrOrdenesCobro['datosFormOrdenCobroRemesa']['FECHAORDENCOBRO']['valorCampo'];}
                 ?>'
            size="10"
            maxlength="10"																	
     />	
					<br />
					
					<label for="user">Archivo Remesa con las órdenes de cobro</label> 
     <input type="text" readonly
						      class="mostrar"		
            name="datosFormOrdenCobroRemesa[NOMARCHIVOSEPAXML]"
            value='<?php if (isset($arrOrdenesCobro['datosFormOrdenCobroRemesa']['NOMARCHIVOSEPAXML']['valorCampo']))
                       {  echo $arrOrdenesCobro['datosFormOrdenCobroRemesa']['NOMARCHIVOSEPAXML']['valorCampo'];}
                 ?>'
            size="60"
            maxlength="80"																	
     />					
					<br />	

					<label for="user">Directorio Archivo Remesa </label> 
     <input type="text" readonly
						      class="mostrar"		
            name="datosFormOrdenCobroRemesa[DIRECTORIOARCHIVOREMESA]"
            value='<?php if (isset($arrOrdenesCobro['datosFormOrdenCobroRemesa']['DIRECTORIOARCHIVOREMESA']['valorCampo']))
                       {  echo $arrOrdenesCobro['datosFormOrdenCobroRemesa']['DIRECTORIOARCHIVOREMESA']['valorCampo'];}
                 ?>'
            size="60"
            maxlength="80"																	
     />					
					<br />					
					<label for="user">Fecha de creación del archivo de las órdenes de cobro de la remesa </label> 
     <input type="text" readonly
						      class="mostrar"		
            name="datosFormOrdenCobroRemesa[FECHA_CREACION_ARCHIVO_SEPA]"
            value='<?php if (isset($arrOrdenesCobro['datosFormOrdenCobroRemesa']['FECHA_CREACION_ARCHIVO_SEPA']['valorCampo']))
                       {  echo $arrOrdenesCobro['datosFormOrdenCobroRemesa']['FECHA_CREACION_ARCHIVO_SEPA']['valorCampo'];}
                 ?>'
            size="20"
            maxlength="20"																	
     />					
					<br />
					<label for="user">Fecha altas de socios/as exentas de pago (últimos meses del año)</label> 
     <input type="text" readonly
						      class="mostrar"		
            name="datosFormOrdenCobroRemesa[FECHAALTASEXENTOSPAGO]"
            value='<?php if (isset($arrOrdenesCobro['datosFormOrdenCobroRemesa']['FECHAALTASEXENTOSPAGO']['valorCampo']))
                       {  echo $arrOrdenesCobro['datosFormOrdenCobroRemesa']['FECHAALTASEXENTOSPAGO']['valorCampo'];}
                 ?>'
            size="10"
            maxlength="10"																	
     />	
				 <br />
					<label for="user">Gestor CODUSER: </label> 
	    <input type="text"	readonly
            class="mostrar"						
	           name="datosFormOrdenCobroRemesa[CODUSER]"
	           value='<?php if (isset($arrOrdenesCobro['datosFormOrdenCobroRemesa']['CODUSER']['valorCampo']))
	           {  echo $arrOrdenesCobro['datosFormOrdenCobroRemesa']['CODUSER']['valorCampo'];}
	           ?>'
	           size="10"
	           maxlength="20"
	    /> 
					<br />
	   <label>Anotado en tabla CUOTAANIOSOCIO (solo se pueden actualizar si este valor es NO)</label> 
	    <input type="text" readonly
											 class="mostrar"		
	           name="datosFormOrdenCobroRemesa[ANOTADO_EN_CUOTAANIOSOCIO]"
	           value='<?php if (isset($arrOrdenesCobro['datosFormOrdenCobroRemesa']['ANOTADO_EN_CUOTAANIOSOCIO']['valorCampo']))
	           {  echo $arrOrdenesCobro['datosFormOrdenCobroRemesa']['ANOTADO_EN_CUOTAANIOSOCIO']['valorCampo'];}
	           ?>'
	           size="4"
	           maxlength="4"
	    />
					<br />
	   <label>Número de órdenes pagos cuotas correspondientes a esta remesa</label> 
	    <input type="text" readonly
											 class="mostrar"		
	           name="datosFormOrdenCobroRemesa[NUMRECIBOS]"
	           value='<?php if (isset($arrOrdenesCobro['datosFormOrdenCobroRemesa']['NUMRECIBOS']['valorCampo']))
	           {  echo $arrOrdenesCobro['datosFormOrdenCobroRemesa']['NUMRECIBOS']['valorCampo'];}
	           ?>'
	           size="4"
	           maxlength="4"
	    />
					<br />
	    <label>Total importe de pagos cuotas correspondientes a esta remesa (euros)</label> 
	    <input type="text"	readonly
            class="mostrar"						
	           name="datosFormOrdenCobroRemesa[IMPORTEREMESA]"
	           value='<?php if (isset($arrOrdenesCobro['datosFormOrdenCobroRemesa']['IMPORTEREMESA']['valorCampo']))
	           {  echo $arrOrdenesCobro['datosFormOrdenCobroRemesa']['IMPORTEREMESA']['valorCampo'];}
	           ?>'
	           size="10"
	           maxlength="20"
	    /> 
					<br /><br />										
				
						<label>*Fecha de pago por el banco</label> 
						<?php		
						
						 require_once './modelos/libs/comboLista.php';
				 		$parValorDia["00"]="día"; 
						 for ($d=1;$d<=31;$d++) 
						 {if ($d<10) {$valor="0"."$d";}//para que los días tengan el formato 01, 02,...10,...31
							 else {$valor="$d";}
							 $parValorDia[$valor]=$valor;
						 }
       //añado if para evitar notices								
							if (!isset($arrOrdenesCobro['datosFormOrdenCobroRemesa']['FECHAPAGO']['dia']['valorCampo']) || empty($arrOrdenesCobro['datosFormOrdenCobroRemesa']['FECHAPAGO']['dia']['valorCampo']))
							{
						   $arrOrdenesCobro['datosFormOrdenCobroRemesa']['FECHAPAGO']['dia']['valorCampo'] = '00';//valor por defecto														
							}									

							//function comboLista($parValor,$identificadorCampo,$valorPrevio,$descPrevio,$valorInicial,$descInicial)
						 echo comboLista($parValorDia, "datosFormOrdenCobroRemesa[FECHAPAGO][dia]",$arrOrdenesCobro['datosFormOrdenCobroRemesa']['FECHAPAGO']['dia']['valorCampo'],
															$parValorDia[$arrOrdenesCobro['datosFormOrdenCobroRemesa']['FECHAPAGO']['dia']['valorCampo']],"00","día");
				 
						 $parValorMes=array("00"=>"mes","01"=>"Enero","02"=>"Febrero","03"=>"Marzo","04"=>"Abril","05"=>"Mayo","06"=>"Junio",
						 "07"=>"Julio","08"=>"Agosto","09"=>"Septiembre","10"=>"Octubre","11"=>"Noviembre","12"=>"Diciembre");
							
       //añado if para evitar notices																																							
							if (!isset($arrOrdenesCobro['datosFormOrdenCobroRemesa']['FECHAPAGO']['mes']['valorCampo']) || empty($arrOrdenesCobro['datosFormOrdenCobroRemesa']['FECHAPAGO']['mes']['valorCampo']))
							{ $arrOrdenesCobro['datosFormOrdenCobroRemesa']['FECHAPAGO']['mes']['valorCampo'] = '00';//valor por defecto	
							}										
			
						 echo comboLista($parValorMes,"datosFormOrdenCobroRemesa[FECHAPAGO][mes]",$arrOrdenesCobro['datosFormOrdenCobroRemesa']['FECHAPAGO']['mes']['valorCampo'],
						                 $parValorMes[$arrOrdenesCobro['datosFormOrdenCobroRemesa']['FECHAPAGO']['mes']['valorCampo']],"00","mes");		
						
						 //$parValorAnio["0000"]="año:"; $anioSiguiente=date("Y")+1;
							
							$anioActual=date("Y");						 
						 $parValorAnio=array('0000'=>"año",$anioActual=>$anioActual/*,$anioSiguiente=>$anioSiguiente*/);
							
							//añado if para evitar notices	
							if (!isset($arrOrdenesCobro['datosFormOrdenCobroRemesa']['FECHAPAGO']['anio']['valorCampo']) ||  empty($arrOrdenesCobro['datosFormOrdenCobroRemesa']['FECHAPAGO']['anio']['valorCampo']))
							{ $arrOrdenesCobro['datosFormOrdenCobroRemesa']['FECHAPAGO']['anio']['valorCampo'] = '0000';
							}												
				
						 echo comboLista($parValorAnio,"datosFormOrdenCobroRemesa[FECHAPAGO][anio]",
							                $arrOrdenesCobro['datosFormOrdenCobroRemesa']['FECHAPAGO']['anio']['valorCampo'],
							                $parValorAnio[$arrOrdenesCobro['datosFormOrdenCobroRemesa']['FECHAPAGO']['anio']['valorCampo']],"0000","año");																							
										
						 ?>	
				  	<span class="error">
							<?php 
							if (isset($arrOrdenesCobro['datosFormOrdenCobroRemesa']['FECHAPAGO']['errorMensaje']))
							{echo $arrOrdenesCobro['datosFormOrdenCobroRemesa']['FECHAPAGO']['errorMensaje'];}
							?>
						</span>
     <br /><br />						
							
	   <label for="user">*Total importe gastos y comisiones del banco por el cobro de esta remesa (euros)</label> 
	    <input type="text"
	           name="datosFormOrdenCobroRemesa[IMPORTEGASTOSABONOCUOTA]"
	           value="<?php if (isset($arrOrdenesCobro['datosFormOrdenCobroRemesa']['IMPORTEGASTOSABONOCUOTA']['valorCampo']))
	           {  echo $arrOrdenesCobro['datosFormOrdenCobroRemesa']['IMPORTEGASTOSABONOCUOTA']['valorCampo'];}
	           ?>"
	           size="10"
	           maxlength="20"
	    /> 	
						<span class="error">
							<?php
									if (isset($arrOrdenesCobro['datosFormOrdenCobroRemesa']['IMPORTEGASTOSABONOCUOTA']['errorMensaje']))
									{echo $arrOrdenesCobro['datosFormOrdenCobroRemesa']['IMPORTEGASTOSABONOCUOTA']['errorMensaje'];}
								?>
						</span>	
       <br />						
						</p>
					</fieldset>						
					<br />
						<!-- ********** Inicio de datSocio[OBSERVACIONES] ******* -->
					<fieldset>
	
						<legend><b>Observaciones del gestor que emitió esta remesa</b></legend>  <!--No obligatorio -->
     <p>
					<textarea  id='OBSERVACIONES' onKeyPress="limitarTextoArea(999,'OBSERVACIONES');" 	
					class="textoAzul8Left"	 name="datosFormOrdenCobroRemesa[OBSERVACIONES]" rows="12" cols="80"><?php 
							if (isset($arrOrdenesCobro['datosFormOrdenCobroRemesa']['OBSERVACIONES']['valorCampo']))                    
						{echo htmlspecialchars(stripslashes($arrOrdenesCobro['datosFormOrdenCobroRemesa']['OBSERVACIONES']['valorCampo']));}
					?></textarea> 			 
					</p>
					</fieldset>
						<!-- ************ Fin de datSocio[OBSERVACIONES] ********* -->
						<span class="error">
							<?php
									//if (isset($arrOrdenesCobro['datosFormOrdenCobroRemesa']['OBSERVACIONES']['errorMensaje']))
									//{echo $arrOrdenesCobro['datosFormOrdenCobroRemesa']['OBSERVACIONES']['errorMensaje'];}
								?>
						</span>					
					<br />		
				
	  <input type="submit" name="comprobarYactualizar" value="Anotar el pago de las cuotas de la remesa en la tabla de cuotas de socios/as" class="enviar" />	
	<!--		<input type="submit" name="SiEliminarOrdenesCobro" value="Eliminar las órdenes cobro de remesa?" class="enviar" />	-->
  </form>

  <form method="post" class="linea"
      action="./index.php?controlador=cTesorero&amp;accion=actualizarCuotasCobradasEnRemesaTes">		
   <input type="submit" name="salirSinActualizar" value="Cancelar la operación de actualización" class="enviar">
  </form>					
 </div><!--<div id="formLinea">-->
</div><!-- <div id="registro">-->




