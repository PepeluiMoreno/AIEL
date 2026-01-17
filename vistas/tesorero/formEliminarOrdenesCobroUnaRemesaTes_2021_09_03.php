<?php
/*----------------------------------------------------------------------------------------------------------------------
FICHERO: formEliminarOrdenesCobroUnaRemesaTes.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Formulario para eliminar una remesa, y se muestran algunos campos de la tabla REMESAS_SEPAXML de esa remesa
y las filas correspondientes de ORDENES_COBRO correspondientes a esa remesa concreta.  

Entre otros se muestran los campos [NOMARCHIVOSEPAXML] y [DIRECTORIOARCHIVOREMESA] y también se devuelven en el $_POST 
para usar esos datos para eliminar esa remesa, que implicará anular las órdenes de cobro correspondientes 
anotadas previamente en las tablas REMESAS_SEPAXML y ORDENES_COBRO, y después quedarán igual que estaban antes de 
generar esa remesa.
También se eliminará del servidor el correspondiente archivo de orden de cobro de esa remesa, que fue generado para 
subirlo a la web del banco para su cobro, cuyo nombre indicamos más abajo.

Se avisa de que SÓLO SE DEBE ELIMIMAR si ese archivo aún no se envió a la web del Banco, o bien si después de enviarla 
fue cancelada, o no ejecutada la orden del cobro de esa remesa una vez pasada la fecha de orden de cobro por el banco 

Tiene unos botones para "Eliminar las órdenes cobro de remesa", y para "Cancelar"

LLAMADA: vCuerpoEliminarOrdenesCobroUnaRemesaTes.php ()que venndrá de cTesorero.php:eliminarOrdenesCobroUnaRemesaTes()
													
OBSERVACIONES: mediante require_once './vistas/tesorero/vCuerpoEliminarOrdenesCobroUnaRemesaTes.php"										
----------------------------------------------------------------------------------------------------------------------*/
?>
<div id="registro">

 <br />
	<span class="error"> <strong> ***** O J O: ACCIÓN IRREVERSIBLE *****</strong> </span>
		<br /><br />				
	<span class="error"><strong>AVISO:</strong>   </span>
	
		<span class="textoAzu112Left2">&nbsp;&nbsp;Una remesa de órdenes de cobro <strong>SÓLO SE DEBE ELIMIMAR</strong> si ese archivo aún no se envió a la web del Banco, 
		o bien si después de enviarla fue cancelada, anulada, o no ejecutada la orden del cobro de esa remesa una vez pasada la fecha de orden de cobro por el banco 
		(después de consultar al banco los motivos).  
		<br /><br />
		Al eliminar una remesa, se anularán las órdenes de cobro correspondientes en las tablas REMESAS_SEPAXML y ORDENES_COBRO, y quedarán igual que estaban antes de generar esa remesa. 
		<br /><br />Se <strong>eliminará también del servidor el archivo con las órdenes de cobro</strong> de esa remesa, que fue generado para subirlo a la web del banco para su cobro, cuyo nombre indicamos más abajo.							
		<br /><br />
		Los datos de las órdenes de cobro correspondientes a esa remesa, que fueron anotadados en las tablas REMESAS_SEPAXML y ORDENES_COBRO, se utilizarán después, 
		cuando se haya comprobado el cobro de la remesa por el banco, para anotar los correspondientes pagos de cuotas domiciliados de cada socio/a en la tabla CUOTAANIOSOCIO.
	</span> 

		<br /><br />		
  <div id="formLinea">

			<form method="post" class="linea" action="./index.php?controlador=cTesorero&amp;accion=eliminarOrdenesCobroUnaRemesaTes"	
									onSubmit="return confirm('¿Eliminar las órdenes de cobro de esta remesa? Acción irreversible')">		

	 <fieldset>	 
	  <legend><b>Datos de la remesa de órdenes de cobro a eliminar</b></legend>	
		 <p>
		  <label>Año de la remesa de cobro a eliminar de la tabla ORDENES_COBRO</label> 
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
					<label for="user">Fecha de orden de cobro</label> 
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
					<label for="user">Archivo SEPA XML remesa</label> 
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
					<label for="user">Directorio de archivo remesa</label> 
     <input type="text" readonly
						      class="mostrar"		
            name="datosFormOrdenCobroRemesa[DIRECTORIOARCHIVOREMESA]"
            value='<?php if (isset($arrOrdenesCobro['datosFormOrdenCobroRemesa']['DIRECTORIOARCHIVOREMESA']['valorCampo']))
                       {  echo $arrOrdenesCobro['datosFormOrdenCobroRemesa']['DIRECTORIOARCHIVOREMESA']['valorCampo'];}
                 ?>'
            size="40"
            maxlength="40"																	
     />					
					<br />		
					<label for="user">Fecha de creación del archivo XML SEPA de las órdenes de cobro o pago de la remesa </label> 
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
					<label for="user">Fecha altas exentas de pago (últimos meses del año)</label> 
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
					<label>Gestor CODUSER: </label> 
	    <input type="text"	readonly
            class="mostrar"						
	           name="datosFormOrdenCobroRemesa[CODUSER]"
	           value='<?php if (isset($arrOrdenesCobro['datosFormOrdenCobroRemesa']['CODUSER']['valorCampo']))
	           {  echo $arrOrdenesCobro['datosFormOrdenCobroRemesa']['CODUSER']['valorCampo'];}
	           ?>'
	           size="10"
	           maxlength="20"
	    /> 
					<br /><br />
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
					<br /><br />
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
					<br /><br />					
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
	
						</p>
					</fieldset>
     <br />
					
						<!-- ********** Inicio de datSocio[OBSERVACIONES] ******* -->
					<fieldset>
				  <p>
						<legend><b>Observaciones del gestor que emitió esta remesa</b></legend>  <!--No obligatorio -->
      
				 	<textarea  id='OBSERVACIONES' onKeyPress="limitarTextoArea(499,'OBSERVACIONES');"	readonly
					 class="mostrar" name="datosFormCuotasVigentesEL[OBSERVACIONES]" rows="6" cols="180"><?php 
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
					
   <input type="submit" name="SiEliminarOrdenesCobro" value="¿Eliminar las órdenes cobro de remesa?" class="enviar" />	

		</form>	


  <form method="post" class="linea"
      action="./index.php?controlador=cTesorero&amp;accion=eliminarOrdenesCobroUnaRemesaTes">		
   <input type="submit" name="NoEliminarOrdenesCobro" value="Cancelar" class="enviar">
  </form>			

 </div><!--<div id="formLinea">-->
</div><!-- <div id="registro">-->




