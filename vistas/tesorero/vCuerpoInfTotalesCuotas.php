<!--***************************** Inicio vCuerpoInfTotalesCuotas.php ***************
FICHERO: vCuerpoInfTotalesCuotas.php
Agustín : modificación 2017-01-31
PROYECTO: EL
VERSION: PHP 5.2.3
DESCRIPCION: Contiene el texto de la  información del significado de las columns del
             formulario formTotalesCuotas.php
OBSERVACIONES:Se le llama desde vInfTotalesCuotasInc.php
*******************************************************************************-->
<div class="content0">

  <!--************************ Inicio Cuerpo central ************************-->
	<div class="blank">
		<h3 align="center">
	  	INFORMACIÓN SOBRE LAS COLUMNAS DE "TOTALES PAGOS CUOTAS POR AÑO"
				<br /><br />
		</h3>
	 <span class="textoAzul9Left">Incluye los pagos realizados por los socios/as que son bajas, hasta el año 
		siguiente a la fecha de darse de baja, puesto que han podido pagar y darse de baja posteriomente
		</span>
		<br /><br /> 		
		<!--********************  Inicio Formulario Login  **********************-->
		<?php //require_once './vistas/login/formularioCuerpoLogin.php';?>
		<!--********************  Fin Formulario Login  *************************-->
 <span class="textoNegro8Left">
	<ol>
 <li class="textoNegro8Left" class="textoNegro8Left">
 <b>Año:</b> Año de los pagos de las cuotas
	</li><br />
	<li class="textoNegro8Left" class="textoNegro8Left">
	<b>Soci@s Num:</b> Número total de soci@s de EL en ese año (incluye a los/las que se dieron de baja en ese año puesto que pudieron pagar y después darse de baja)
	</li><br />
	<li class="textoNegro8Left">	
 <b>General Num:</b> Número de soci@s con cuota General. 
	</li><br />
	<li class="textoNegro8Left">	
 <b>Parado Num:</b> Número de soci@s con cuota Parado (o dificultades económicas)
	</li><br />	
	<li class="textoNegro8Left">	
 <b>Joven Num:</b> Número de soci@s con cuota Joven (sin trabajo)
	</li><br />	
	<li class="textoNegro8Left">	
 <b>Honorario Num:</b> Número de soci@s Honorararios (exentos de pago cuota)
	</li><br />	
	
 <li class="textoNegro8Left">
	<b>Cuotas EL €:</b> Suma de las cuotas obligatorias que tienen que pagar todos los soci@s. 
	Las cuotas de los soci@s pueden ser diferentes según el tipo de soci@:General,Parado,Joven,... 
	</li><br />	
		
	<li class="textoNegro8Left">	
	<b>Abonan Num:</b> Número de soci@s que han abonado toda la cuota (no se contabilizan los que han abonado solo parte,
	         ni los exentos (Jóvenes y Parados) incluso aunque hayan realizado algún pago
	</li><br />	
 <li class="textoNegro8Left">
	<b>Cuotas Abonadas €:</b> Cuotas abonadas por los soci@s: no incluye Exentos, ni Abona Parte, ni lo Donado en la cuota, 
	y en el caso de pago con PayPal son ingresos reales (a la cuota se le ha descontado los gastos de PayPal)
	</li><br />

	<li class="textoNegro8Left">	
	<b>Saldo cuotas €:</b> Cuotas EL - Cuotas Abonadas
	</li><br />	
	<li class="textoNegro8Left">	
	<b>NO Abon. + Pend. Cobro Num:</b> NO Abon: Núm. de soci@s que no han abonado o solo han abonado parte (los exentos no se cuentan)+ Pend. Cobro: Núm. de soci@s que no se ha pasado el cobro
	</li><br />
	<li class="textoNegro8Left">		
 <b>NO Abon. + Pend. Cobro €:</b>El importe de  NO Abon: Núm. de soci@s que no han abonado (incluidos los que solo han abonado parte) * cuota general + Pend. Cobro: Núm. de socios que no se ha pasado el cobro * cuota general
	</li><br />	

	<li class="textoNegro8Left">	
 <b>Devuelta y Error CC Num.</b> El número de soci@s con cuota domiciliada y que el banco ha devuelto el cobro (si se corrige el motivo de devolución y se cobra en la siguiente remesa, no se contabiliza como devuelta, pero el banco sí cobra gastos)
	</li><br />	
	<li class="textoNegro8Left">	
 <b>Devuelta y Error CC €:</b> El importe de las cuotas de los soci@s con cuota domiciliada y que el banco ha devuelto el cobro (devolución o error en cuenta)
	</li><br />	
	
	<li class="textoNegro8Left">	
 <b>Abona Parte Num:</b> El número de soci@s que han abonado solo una parte de la cuota
	</li><br />	
	<li class="textoNegro8Left">	
 <b>Abona Parte €:</b> El importe de las cuotas abonadas solo en parte
	</li><br />	
	
	<li class="textoNegro8Left">	
 <b>Exento Num:</b> Número de soci@s exento de pago (hasta 2013 estaban exento de pago Parad@s, Jóvenes y Honorari@s, pero desde 2014 solo están exentos los soci@s honorari@s) 
	</li><br />	
	<li class="textoNegro8Left">	
 <b>Exento paga €:</b> Lo que han pagado l@s que están exent@s de pagar, pero han hecho alguna aportación en concepto de cuota voluntaria
	</li><br />		
	
	<li class="textoNegro8Left">	
 <b>Dona en la cuota Num:</b> El número de soci@s que con el abono de la cuota incluyen una donación (cuota general + donación)
	</li><br />	
	<li class="textoNegro8Left">	
 <b>Dona en la cuota €:</b> La suma de las cantidades donadas en las cuotas: Suma(Pagado - Cuota general)
	</li><br />
	
	<li class="textoNegro8Left">	
 <b>Domiciliada Num:</b> El número de soci@s que han abonado la cuota mediante domiciliación bancaria
	</li><br />
	<li class="textoNegro8Left">	
 <b>Pay Pal Num:</b> El número de soci@s que han abonado la cuota mediante Pay Pal
	</li><br />	
	<li class="textoNegro8Left">	
 <b>Transferencia Num:</b> El número de soci@s que han abonado la cuota mediante transferencia bancaria
	</li><br />	
	<li class="textoNegro8Left">	
 <b>Metálico Num:</b> El número de soci@s que han abonado la cuota mediante pago en metálico
	</li><br />	
	<li class="textoNegro8Left">	
 <b>Sin datos Num:</b> El número de soci@s que han abonado de los cuáles no se tienen los datos de modo de pago (suelen ser anotaciones realizadas por tesorería y que proceden de información incompleta desde coordinadores)
	</li><br />	

	<li class="textoNegro8Left">	
 <b>Gastos cobro cuotas €:</b> La suma de las gastos generados por el cobro de las cuotas de los socio/as: pago mediante PayPal (se anotan por cada pago) y otros gastos que hubiese anotado el tesorero (domiciliaciones es global, devoluciones individual).
	</li><br />

	<li class="textoNegro8Left">
	<b>TOTALES CUOTAS PAGADAS:</b>Esta cifra es la suma total de ingresos por cuotas y no están descontados los gastos de cobro de cuotas.

Se obtiene: Cuotas pagadas + Pagos de Exentos + Pagado parte + Donado en la cuota. Es decir la suma de las columnas (9+16+18+20).
	<br /><br />NOTA: En la tabla se muestran números enteros (sin decimales por problemas de espacio),
	 y si se comprueba sumando manualmente para obtener los TOTALES INGRESOS, en algún caso podría no coincidir (diferencia inferior a 5 euros), 
		pero internamente TOTALES CUOTAS PAGADAS se han calculado con un decimal.		
	</li><br />
	</ol>	
	</span>
	
</div>
<div align="center">
  <input onclick=window.close(); type=button value="Aceptar" />
</div>
<br />

	<!-- ****************************** Fin Cuerpo central  ******************** -->
</div>
<!-- ******************************* Fin Cuerpo vCuerpoInfTotalesCuotas.php ************* -->