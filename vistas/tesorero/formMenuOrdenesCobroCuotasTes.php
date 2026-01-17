<?php
/*-------------------------------------------------------------------------------------------
FICHERO: formMenuOrdenesCobroCuotasTes.php
VERSION: PHP 7.3.21

DESCRIPCION: Muesta las opciones del menú relacionadas con la órdenes de cobro 
de cuotas domiciliadas.	
I- Enviar emails para avisos de próxima orden de cobro de cuota.

II- Generar archivo SEPA-XML con órdenes cobro y otras operaciones relacionadas
- Estado de las órdenes de cobro de cuotas domiciliadas: 
 .Ver órdenes de cobro, 
	.Eliminar remesa de ORDENES_COBRO, 
	.Descargar Archivo SEPA-XML,
	.Actualizar pagos remesa en CUOTA ANIO SOCIO 
- Generar archivo SEPA_ISO20022CORE-XML con las órdenes cobro B.Santander 
  e Insertar órdenes en tabla ORDENES_COBRO.
- Exportar las órdenes de pago de cuotas a archivo Excel 
  para trabajo y contrastar con archivo XML SEPA 
- Exportar las cuotas y otros datos de los socios/as a Excel para uso interno 

III- Exportar listas emails para avisos órdenes cobro cuotas par envío desde Nodo50
   													
LLAMADA: vCuerpoMenuOrdenesCobroCuotasTes.php 
LLAMA: forms llaman a varias funciones de cTesorero.php relacionadas con órdenes de cuotas
													
OBSERVACIONES:
2020-10-12: Cambio nombre archivo, añado información procedimientos para remesa
--------------------------------------------------------------------------------------------*/
?>

<div id="registro">

	<span class="error">
   <strong>
			PRIVACIDAD DE DATOS:</strong> Los datos descargados en archivos externos, 
			se podrán utilizar para dar las órdenes de cobro a los bancos, 
			y los archivos para comunicarse con los socios/as 
			<br /><br /> 
			Deberán ser destuidos una vez utilizados. Es responsabildad del gestor que no sean usados con otros fines.
			<br />
	</span>
 <br /><br />		

	<div align="left"> 		
 
   <br /><br />
			<a href="./index.php?controlador=cTesorero&amp;accion=infOrdenCobroCuotasDomiciliadas" 
			   title="Información sobre el procedimiento de órdenes de cobro de cuotas domiciliadas">
            <strong>>> VER INFORMACIÓN SOBRE EL PROCEDIMIENTO DE ÓRDENES DE COBRO DE CUOTAS DOMICILIADAS EN BANCOS DE ESPAÑA Y BANCOS SEPA</strong>												
   </a>	
			<br /><br /><br />			

		<strong>I.- Enviar emails para avisos de próxima orden de cobro de cuotas domiciliadas (Cuenta bancaria España o SEPA) </strong>
	  <br /><br />			
			<a href="./index.php?controlador=cTesorero&amp;accion=emailAvisarDomiciliadosProximoCobro" 
			   title="Enviar emails para avisos de próxima orden de cobro de cuota">
            I.1 - Enviar email personalizado de aviso de próximo cobro de cuotas domiciliadas (Cuenta bancaria España o SEPA)         
   </a>	
			<br /><br />
						<a href="./index.php?controlador=cTesorero&amp;accion=exportarEmailDomiciliadosPend" 
			   title="Exportar email socios/as para aviso cobro cuotas">
            I.2 - Exportar direcciones emails a archivo -txt- para aviso de próximo cobro de cuotas domiciliadas (Cuenta bancaria España o SEPA)         
   </a>	

		<br /><br /><br />

	 <strong>II.- Generar archivo SEPA-XML para remesa con órdenes de cobro de cuotas domiciliadas para envío al banco y otras operaciones</strong>
			<br /><br />
			
			<a href="./index.php?controlador=cTesorero&amp;accion=estadoOrdenesCobroRemesasTes" 
			   title="Estado de los órdenes de cobro de cuotas domiciliadas en bancos y acciones">
            II.1 - Estado de las remesas y órdenes de cobro de cuotas domiciliadas: Ver órdenes cobro, Eliminar remesa ORDENES_COBRO, <strong>Descargar Archivo SEPA-XML de la remesa</strong>, Actualizar pagos remesa en CUOTA ANIO SOCIO 										
   
			</a>	
			<br /><br />			
			
		 <a href="./index.php?controlador=cTesorero&amp;accion=XMLCuotasTesoreroSantander" 
			   title="Exportar las cuotas de los socios/as norma XML cobro por el Santander">
            II.2 - <strong>Generar archivo SEPA_ISO20022CORE-XML</strong> para remesa con las órdenes cobro de cuotas domiciliadas, para envío al B.Santander y anotar esas órdenes de pagos en ORDENES_COBRO  
   </a>		
	  <br /><br />
			
		 <a href="./index.php?controlador=cTesorero&amp;accion=excelCuotasTesoreroBancos" 
			   title="Exportar las órdenes de pagos cuotas de los socios/as a Excel para trabajo (antiguo cobro por el Tríodos)">
            II.3 - Exportar las órdenes de cobo cuotas domiciliadas a archivo Excel, (no modifica tabla ORDENES_COBRO). Útil para uso Tesorería y contrastar con archivos SEPA-XML  B.Santander (Antes usado para B.TRÍODOS)         									
   </a>	
			<br /><br />			

	  <a href="./index.php?controlador=cTesorero&amp;accion=excelCuotasInternoTesorero" 
			   title="Exportar las cuotas de los socios/as a Excel para uso interno">
            II.4 - Exportar las cuotas y otros datos de los socios/as a Excel para uso interno           
   </a>			
			<!--	  <a href="./index.php?controlador=cTesorero&amp;accion=excelCuotasTesoreroBancos" 
			   title="Exportar las cuotas de los socios/as a Excel cobro por el Tríodos">
            I.3 - Exportar las cuotas de los socios/as a Excel para cobro por Tríodos           
   </a>	<br /><br />
		 <a href="./index.php?controlador=cTesorero&amp;accion=AEB19CuotasTesoreroSantander" 
			   title="Exportar las cuotas de los socios/as norma AEB19 cobro por el Santander">
            I.4 - AEB19: Exportar las cuotas de los socios/as a archivo norma AEB19 para cobro por Santander           
   </a>		-->				
		<br /><br /><br />		
				
	 <strong>III.- Enviar emails para recordar que aún no han abonado la cuota anual y sin Cuenta Bancaria domiciliada </strong>
	 <br /><br />
		<a href="./index.php?controlador=cTesorero&amp;accion=emailAvisarCuotaNoCobradaSinCC" 
			   title="Exportar email socios/as para aviso cobro cuotas">
            III.1 - Enviar email personalizado para recordar que aún no han abonado la cuota anual (Sin Cuenta Bancaria domiciliada)		
   </a>			
			<br /><br />
			<a href="./index.php?controlador=cTesorero&amp;accion=exportarEmailDomiciliadosPendSinCC" 
			   title="Exportar email socios/as para aviso cobro cuotas">
            III.2 - Exportar direcciones emails a archivo -txt- para recordar que aún no han abonado la cuota anual (Sin Cuenta Bancaria domiciliada)        
   </a>	

	 </div> <!-- <div align="left"> 		-->
</div>	<!-- <div id="registro"> -->	
