<?php
/*----------------------------------------------------------------------------------------------------
FICHERO: formInfOrdenCobroCuotasDomiciliadas.php
VERSION: PHP 7.3.21

DESCRIPCION: Muestra la información sobre el procedimiento de enviar remesas al B. Santander 
con las órdenes de cobro de las cuotas domiciliadas en bancos de España y bancos SEPA

LLAMADA: vistas/tesorero/vCuerpoOrdenCobroCuotasDomiciliadas.php

OBSERVACIONES: 2020-11-27 creación script   
----------------------------------------------------------------------------------------------------*/
?>
<div id="registro">	
  <br /><br />

  <span class="textoAzu112Left2">
				
			<strong>INFORMACIÓN GENERAL</strong>
			<br /><br />- Los cobros domiciliados de la cuotas anuales, actualmente se realizan desde la cuenta que la asociación que tiene en el B. Santander.	
		 <br /><br />- Mediante la aplicación de Gestión de Soci@s, se genera un archivo XML "SEPA_ISO200022CORE_fecha.xml" con las órdenes de cobro de las cuotas domiciliadas (remesa), 
		             	 para a continuación importarlo en la web del B. Santander y firmar la orden de cobro de esa remesa.
			<br /><br />- Normalmente se realizan dos cobros domiciliados al año: uno a primeros de marzo y el último a mediados de noviembre.
			<br /><br />- Esta aplicación de Gestión de Soci@s, NO incluye en el archivo "SEPA_ISO200022CORE_fecha.xml" las cuentas bancarias domiciliadas en bancos 
			              de estados SEPA distintos de España, (por falta de los códigos BIC), pero existe una opción que mostrará el listado con los datos de esas cuentas IBAN y nombres de los soci@s. 
																	Con esos datos Tesorería podría generar una remesa manualmente en la web del B. Santander, o se les puede enviar un email sugiriendo que paguen 
																	su cuota mediante PayPal.																	
					
			<br /><br /><br /><strong>PROCEDIMIENTO PARA EXPORTAR (ENVIAR) ÓRDENES DE COBRO DE CUOTAS DOMICILIADAS AL BANCO Y OTRAS OPERACIONES RELACIONADAS </strong>
			
			<br /><br /><strong>I.- PASOS PREVIOS</strong>
			<br /><br />- <strong>Tesorería anotará para que "estén al día" todas las cuotas pagadas</strong> por los socios/as, antes de iniciar todo el proceso.
			<br /><br />- <strong>Enviar un email a los socios/as con cuotas domiciliadas.</strong> Unos 10 antes días de generar el archivo con la orden de cobro. Desde: 
			<br /><br />  <i>I.1 - Enviar email personalizado de aviso de próximo cobro de cuotas domiciliadas. </i>	       	     
                 Se pide a los socios/as que comprueben que su cuenta IBAN es correcta y se les da un plazo para corregirla.
			<br /><br />  o sin datos personalizados <i>I.2 - Exportar direcciones emails a archivo -txt-</i>, para pegarlas en el servidor de correo de tesorería@europalaica.org
																		y enviar un aviso de próximo cobro de cuotas domiciliadas.
																		
			<br /><br />	.Tesorería atiende las peticiones en las respuestas a los emails.			
			
			<br /><br /><br /><strong>II.- EXPORTAR ÓRDENES DE COBRO DE CUOTAS PARA BANCO Y OTRAS OPERACIONES RELACIONADAS </strong> (una vez vencido el plazo de correcciones del email)
			
			<br /><br />- <strong>Copia de seguridad de la base de datos</strong> (lo debe hacer una persona con permisos sobre la BBDD)
   
			<br /><br />- <strong>GENERAR EL ARCHIVO SEPA-XML</strong>. Desde: <i>II.2 - Generar archivo SEPA_ISO20022CORE-XML para remesa con las órdenes cobro de cuotas domiciliadas,
			               para envío al B.Santander</i>. 
			<br /><br />   En este proceso se elige la fecha de cobro y agrupaciones a incluir en la remesa, 
																 	y a la vez automáticamente se insertan los datos de las órdenes cobro en las tablas "ORDENES_COBRO" y "REMESAS_SEPAXML".
																	
			<br /><br />- <strong>Comprobar datos generales de la remesa y el archivo generado</strong>. Desde: <i>II.1 - Estado de las remesas y órdenes de cobro de cuotas domiciliadas: Ver</i>
	   
  	<br /><br /> .Comprobar que en la tabla hay una nueva fila con datos de la remesa y son correctos
			<br />							.Contrastar "Número de cuotas en la remesa", "Total importe cuotas de la remesa" y demás datos
   <br /><br /> .En caso de ERROR o no coincidencia, <strong>lo antes posible "Eliminar esa remesa de las ORDENES_COBRO"</strong> 
											      y así se volverán a poner en la tabla "ORDENES_COBRO" de la BBDD sus valores anteriores. Desde:
   <br />						 	<i>II.1 - Estado de las remesas y órdenes de cobro de cuotas domiciliadas: Eliminar remesa ORDENES_COBRO</i>																			
			               
			<br /><br />- <strong>DESCARGAR EL ARCHIVO "SEPA_ISO200022CORE_fecha.xml"</strong>, y comprobar que los datos de los socios/as se corresponden y especialmente 
																	que no están desplazados. 
																	El archivo se puede abrir con un navegador (estructura en árbol) o con Excel, pero mejor para comprobaciones hacerlo en una copia del archivo
																	para evitar alteraciones involutarias. Desde: 
			<br />        <i>II.1 - Estado de las remesas y órdenes de cobro de cuotas domiciliadas: Descargar Archivo SEPA-XML de la remesa.</i>			             																	

			<br /><br />- <strong>Muy conviente: Exportar y descargar las órdenes de cobo cuotas domiciliadas a archivo Excel</strong>, 
			              este archivo será útil para contrastar cantidades totales y posteriormente para anotar las posibles devoluciones en ese archivo. Desde: 
   <br />							<i>II.3 - Exportar las órdenes de cobo cuotas domiciliadas a archivo Excel</i>
																	
			<br /><br />- <strong>COMPROBAR Y CONTRASTAR DATOS.</strong> Si algo no estuviese correcto,
                 <strong>lo antes posible "Eliminar esa remesa de las ORDENES_COBRO"</strong> para deshacer las anotaciones 
																	realizadas en tabla "ORDENES_COBRO" en BBDD, (y desechar los archivos descargados SEPA-XML y Excel, para evitar confusiones). Desde: 
			<br />        <i>II.1 - Estado de las remesas y órdenes de cobro de cuotas domiciliadas: Eliminar remesa de tabla "ORDENES_COBRO"</i>
																	
			<br /><br />- <strong>SUBIR EL ARCHIVO A LA WEB DEL B. Santander Empresas.</strong> Si todo está correcto, 
			              el tesorero/a entrará en la <strong>web B. Santander Empresas</strong>, en "Envío de remesas" 
																	se subirá el archivo "SEPA_ISO200022CORE_fecha.xml". Si no da errores, para que se procese esa remesa es necesario que 
																	dos personas autorizadas en B. Santander firmen la remesa con las órdenes de pago.
			<br /><br />		(Para esta parte hay un manual específico con los pasos a seguir en la web del B. Santander, que lo están cambiando y cada vez es más sencillo)
																	
			<br /><br />- <strong>El día de la fecha de orden de cobro, Tesorería comprobará en el B. Santander</strong>
              			que se ha efectuado el abono de la remesa, y anotará los gastos cobrados por el banco por la remesa pues los necesitará después. 
			<br /><br />		En el caso de que no se hubiese efectuado el abono, consultar al Banco.
																	Si no se pudiese ejecutar la orden de cobro, <strong>lo antes posible "Eliminar esa remesa de las ORDENES_COBRO"</strong> 
																	para deshacer las anotaciones realizadas en tabla "ORDENES_COBRO" en BBDD. Desde: 
			<br />        <i>II.1 - Estado de las remesas y órdenes de cobro de cuotas domiciliadas: Eliminar remesa de tabla "ORDENES_COBRO"</i>
				
   <br /><br />- <strong>ACTUALIZAR EL ESTADO DE LAS CUOTAS A "ABONADA".</strong> Una vez comprobado el abono de la remesa, 
	                 se anotará automáticamente en la tabla "CUOTAANIOSOCIO" el estado de las cuotas a "ABONADA" 
																		para todos los socios/as incluidos en la remesa (comprobar después que se han anotado correctamente). 	Desde:
			<br />        <i>II.1 - Estado de las remesas y órdenes de cobro de cuotas domiciliadas: Actualizar pagos remesa en CUOTA ANIO SOCIO</i>
																	
   <br /><br />- <strong>AVISO: antes y después del paso anterior hacer una Copia de seguridad de la base de datos,</strong> 
			               pues el proceso de "Actualizar pagos remesa" realiza cambios irreversibles en algunas tablas de la BBD,	
																	(lo debe hacer una persona con permisos sobre la BBDD)	
																		
			<br /><br /><br /><strong>--- DEVOLUCIONES ---</strong>													
			<br /><br />- <strong>Posteriormente, "Tesorería" anotará las devoluciones</strong> con sus gastos a medida que se vayan produciendo
			
	<br /><br /><br /><br /><strong>III.- ENVIAR EMAILS A LOS SOCIOS/AS SIN CUENTA BANCARIA DOMICILIADA Y QUE NO HAN ABONADO LA CUOTA</strong>		
	  <br /><br />  Cuando tesorería lo considere oportuno, normalmente después de haber sido abonada una remesa y una vez actualizado 
			el estado de las cuotas correspondientes a "ABONADA", se enviará un email recordatorio a los socios/as (Sin Cuenta Bancaria domiciliada) 
			para recordarles que aún no han abonado la cuota anual.
			<br /><br />  <i>III.1 - Enviar email personalizado para recordar que aún no han abonado la cuota anual (Sin Cuenta Bancaria domiciliada) </i>    
                 
			<br /><br />  o sin datos personalizados <i>III.2 - Exportar direcciones emails a archivo -txt-</i>, para pegarlas en el servidor de correo de tesorería@europalaica.org
																		y enviar un email para recordar que aún no han abonado la cuota anual (Sin Cuenta Bancaria domiciliada).
																		
			<br /><br />	.Tesorería atiende las peticiones en las respuestas a los emails.					

	 </span>			
  
</div>		
