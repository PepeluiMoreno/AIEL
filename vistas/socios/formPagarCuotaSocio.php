<?php
/*--------------------------------------------------------------------------------------
FICHERO: formPagarCuotaSocio.php 
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: En el formulario, si la cuota anual NO está pagada, se muestran la cuota del socio
             los datos bancarios (si los hay), y otra información del socio y se le indica 
													los modos de pagar la cuota anual:
           - Se muestran las cuentas bancarias de donde se cobran a las distintas agrupaciones, 
											  se leen de las tablas de AGRUPACIONTERRITORIAL (a fecha 01_08_2021 todas menos Asturias
													están centralizadas y comparten la misma cuenta bancaria, Asturias muestra su cuenta) 
           - Además hay un botón de enlace a PayPal (a fecha 01_08_2021 todas menos Asturias), 
											  donde ya se incluye la cantidad a pagar y demás datos del socio. 

           Si la cuota anual ya está pagada se indica y se ofrece la opción de hacer una donación													
													
OBSERVACIONES:Es incluida desde "vCuerpoPagarCuotaSocio.php"
              mediante require_once './vistas/socios/formPagarCuotaSocio.php'
--------------------------------------------------------------------------------------*/
require_once './modelos/libs/comboLista.php';
?>

<div id="registro">  	
  <br />
  <span class="textoAzu112Left2">En Europa Laica <strong>NO aceptamos subvenciones</strong>, nuestros ingresos proceden de las cuotas y donaciones de nuestras socias, 
		socios y simpatizantes.
 		</span>
		
 <br /><br /><br />
	
 	<!-- ****************** Inicio Datos de Cuotas  ******************************* -->
	 <fieldset>
	  <legend><b>Datos de la cuota</b></legend> 
			
		<p>
		<label>Nombre</label> 
					
					<span class="mostrar">
					 <?php 
								if (isset($datosSocioPayPal['NOM']))
	       {  echo $datosSocioPayPal['NOM']." ";}								
								if (isset($datosSocioPayPal['APE1']))
								{  echo $datosSocioPayPal['APE1']." ";}
								if (isset($datosSocioPayPal['APE2']))
								{  echo $datosSocioPayPal['APE2']." ";}		
	      ?>
					 </span>
		 <br /> 
		
		 <?php 				//bien sale todo	
			if (isset($datosSocioPayPal['ANIOCUOTA']))
   //if (isset($datosSocioPayPal['faltaPagar']) && $datosSocioPayPal['faltaPagar']!==0)
   {
				echo "<label>Estado de pago de la cuota del año <strong>".$datosSocioPayPal['ANIOCUOTA']."</strong></label> ";
		  echo "<span class='mostrar'><strong>".$datosSocioPayPal['ESTADOCUOTA']."</strong></span><br />";
			 
		  echo "<label>Cuota total elegida por el socio/a para el año <strong>".$datosSocioPayPal['ANIOCUOTA']."</strong></label>".
									"<span class='mostrar'>".$datosSocioPayPal['IMPORTECUOTAANIOSOCIO']." euros </span>";
		  echo "<label> cuota tipo </label>";
		  echo "<span class='mostrar'>".$datosSocioPayPal['CODCUOTA']."</span>";															
		
		  echo "<br /><label>Cuota ".//$error['datosFormCuotaSocioVer']['NOMBRECUOTA']['valorCampo'].
         " pagada por el socio/a en <strong>".$datosSocioPayPal['ANIOCUOTA']."</strong></label>"; 
    echo "<span class='mostrar'>".$datosSocioPayPal['IMPORTECUOTAANIOPAGADA']." euros</span>";     
		
				echo "<br /><label>Según la cuota que has elegido, <strong>te falta por pagar</strong></label>";
		  echo "<span class='mostrar'><strong>".$datosSocioPayPal['faltaPagar']." euros</strong></span><br />";
			}

			?>
		</p>
	 </fieldset>
		
	 <br />	 
	 <!-- ************************ Fin Datos de Cuotas  ***************************** -->		
		
		<!-- ********************** Inicio datos bancos y enlace a PayPal  ***************** -->		
			<fieldset>
		  <legend><strong>Modos de pagar tu cuota anual a la asociación Europa Laica</strong></legend>
			<p>  
		<?php 
		 //---------- Inicio cuenta NO pagada ------------------------------------------------------
		 //if ($datosSocioPayPal['faltaPagar'] > 0)
		 if (isset($datosSocioPayPal['faltaPagar']) &&  $datosSocioPayPal['faltaPagar'] > 0)
  //if (isset($datosSocioPayPal['faltaPagar']) && ($datosSocioPayPal['faltaPagar']!== 0))			
  { 
		?>
		 <span class="textoGris8Left2">
			
			<?php // bien
			 if (isset($datosSocioPayPal['CUENTAIBAN']) && !empty($datosSocioPayPal['CUENTAIBAN']) && 
				    $datosSocioPayPal['ORDENARCOBROBANCO'] =='SI'// && $datosSocioPayPal['GESTIONCUOTAS']=='ASOCIACION'
							)				
		  { echo "Tienes domiciliado el pago de la cuota en tu cuenta: <strong>",
				 $datosSocioPayPal['CUENTAIBAN'],"</strong>
					<br /><br />Europa Laica efectuará la orden de cobro en tu cuenta, 
					SOLO SI AUN NO ESTUVIESE ABONADA, y unos días antes de la fecha de cobro te avisaremos por email
					<br /><br />- También puedes pagar mediante ingreso directo o transferencia	a:";
				}
			 else 
			 {
			 	echo "<br />Puedes pagar mediante ingreso directo o transferencia	a:";
			 }				
		 ?>		
				<br />
		  <!--Incio 'cadenaBancos' incluye los datos de la asociación EL siempre actualizados ya que que se leen de la BBDD -->
				
				<strong>
				<?php //Imprime las cuentas bancarias de pago de cuotas de la asociación, o de la agrupación si esta gestiona los cobros 				
					echo $datosSocioPayPal['cadenaBancos'];	
				?>
				<br /><br />	
				</strong>	
				<!-- Señala como concepto: Pago cuota a Europa Laica, NIF y nombre y apellidos.-->
				<?php 
					echo $datosSocioPayPal['concepto'];
				?>		
		  <br /><br /> <br />
				</span>		
		<!--Fin 'cadenaBancos' incluye los datos de la asociación EL siempre actualizados ya que que se leen de la BBDD -->
		 	  
		
	<!--  Llamada al script 'payPalScript' de PayPal que incluye un pago personalizado para cada socio según cuota elegida -->		

		<span class="textoGris8Left2">		
		 <?php //require_once './vistas/plantillasGrales/scriptPayPalPagarAhora.php'; 

			  if (isset($datosSocioPayPal['payPalScript']) && !empty($datosSocioPayPal['payPalScript']))
				 { echo "- También puedes pagar ahora con tarjeta de crédito 
					         (o si tienes una cuenta de PayPal), mediante pago con <strong>PayPal</strong>.
					         <br />
					         Para pagar ahora con PayPal, haz clic en - Pagar ahora - ";
														 
					  require_once $datosSocioPayPal['payPalScript'];
					}					
			?>			
		</span>	
		
  <span class="textoGris8Left2">	
				<?php 
					
					echo $datosSocioPayPal['cadEmailTesoreroAgrupacion'];
				?>							
	  </span>
			<br />		
	<!-- Fin llamada al script 'payPalScript' de PayPal que incluye un pago personalizado para cada socio según cuota elegida -->
	 
		<?php 
		}   //---------- Fin cuenta NO pagada -------------------------------------------------------
		
		else //---------- Inicio cuenta pagada ------------------------------------------------------
		{
		?>
  <span class="textoAzu112Left2">
		<br />
	  - Tu cuota anual de <strong><?php echo $datosSocioPayPal['ANIOCUOTA']?></strong> 
			  ya está pagada, si quieres hacer una donación elige la opción de "Hacer una donación" en el menú izquierdo 
  <br /> 
  </span>		
		<?php		
		} //---------- Fin cuenta pagada ----------------------------------------------------------
		?>	
			</p>
		 </fieldset>				
		<!-- ********************** Fin datos bancos y enlace a PayPal  ***************** -->		
		<br />
		
		<!-- ********************* Inicio Datos de Personales *************************** -->
	 <fieldset>	 
	  <legend><b>Datos personales</b></legend>	
		 <p>		
			  <label>Nombre</label> 
					
					<span class="mostrar">
					 <?php 
								if (isset($datosSocioPayPal['NOM']))
	       {  echo $datosSocioPayPal['NOM']." ";}								
								if (isset($datosSocioPayPal['APE1']))
								{  echo $datosSocioPayPal['APE1']." ";}
								if (isset($datosSocioPayPal['APE2']))
								{  echo $datosSocioPayPal['APE2']." ";}		
	      ?>
					 </span>
		 <br /> 
				
		  <label>Documento</label>	
					<span class="mostrar">
					 <?php 
								if (isset($datosSocioPayPal['TIPODOCUMENTOMIEMBRO']))
	       {  echo $datosSocioPayPal['TIPODOCUMENTOMIEMBRO']." ";}	
							?>
					 </span>
	   <label>Nº documento</label> 
					<span class="mostrar">
					 <?php 
								if (isset($datosSocioPayPal['NUMDOCUMENTOMIEMBRO']))
	       {  echo $datosSocioPayPal['NUMDOCUMENTOMIEMBRO']." ";}		
							?>
					 </span>
	   <label>País documento</label>
					<span class="mostrar">
					 <?php 
								if (isset($datosSocioPayPal['CODPAISDOC']))
	       {  echo $datosSocioPayPal['CODPAISDOC']." ";}	
							?>
					 </span>
		  <br /><br />	
			
		  <label>Correo electrónico</label>
					<span class="mostrar">
					 <?php 
								if (isset($datosSocioPayPal['EMAIL']))
	       {  echo $datosSocioPayPal['EMAIL']." ";}		
							?>
					 </span>

				
		  <label>Teléfono 1</label> 
					<span class="mostrar">
					 <?php 
								if (isset($datosSocioPayPal['TELMOVIL']))
	       {  echo $datosSocioPayPal['TELMOVIL']." ";}		
							?>
					 </span>	
		  <label>Teléfono 2</label> 
					<span class="mostrar">
					 <?php 
								if (isset($datosSocioPayPal['TELFIJOCASA']))
	       {  echo $datosSocioPayPal['TELFIJOCASA']." ";}		
							?>
					 </span>						
	  <br />
		<br />
		
  <span class="textoAzu112Left2">
	  - Si quieres actualizar los datos relacionados con tu cuota elige la opción de "Actualizar datos socio/a" en la aplicación de Área de Soci@s 
  <br /> <br />			
		</p>
	 </fieldset>
	 <br />	
	 <!-- ********************** Fin Datos de identificación Personales *************** -->

</div>
	