<?php
/*----------------------------------------------------------------------------------------------------------
FICHERO: formModificarIngresoDonacionTes.php
VERSION: PHP 7.3.21

DESCRIPCION:
Formulario para modificar una donación previa anotada en la tabla DONACION, pero solo los 
se podría modificar datos referentes a los pagos cantidad, gastos, concepto, modo pago, fecha pago 
y observaciones, pero no los datos personales como NIF, email,

Solo se permite modificar donaciones de año anterior y actual. (Por eso condición enero año anterior al actual)

Sólo se usará la cuando después de anotar una donación se comprueba que se ha cometido un error que exige 
una rectificación. 

LLAMADA: vistas/tesorero/vCuerpoModificarIngresoDonacionTes.php
LLAMA: modelos/libs/comboLista.php

OBSERVACIONES: 

2022 cambios en "CONCEPTO" en tabla DONACION. Ahora las distintas opciones de "$parValoresDonacionConceptos" 
están y vienen de la tabla "DONACIONCONCEPTOS" con valores como "COSTAS-MEDALLA-VIRGEN-MERITO-POLICIAL", 
VIII-CONGRESO-AILP-MADRID-2022, y otros que se puedan añadir mas adelante.
-----------------------------------------------------------------------------------------------------------*/

require_once './modelos/libs/comboLista.php';
?>

<script languaje='JavaScript'>
<!-- 
function limitarTextoArea(max, id)
{	if(max < document.getElementById(id).value.length)
	{ document.getElementById(id).value = document.getElementById(id).value.substr(0, max);
    alert("Llegó al máximo de caracteres permitidos");
	}			
}
-->
</script> 

<div id="registro">
  <br />
	<span class="textoAzu112Left2">
	Se puede modificar una donación previamente anotada, en el caso de que se haya detectado un error en los 	datos 
	referentes a "Donación (euros), Gastos, Fecha del ingreso donación, Modo de pago, Concepto, Observaciones del tesorero",
	pero no en los datos personales NIF, o  email.
 <br /> <br /> 
 Solo se permite modificar donaciones de año anterior y actual.
	<br /> <br /> <br /> 
 Los campos con asterisco (<b>*</b>) son obligatorios	 
 </span> 
	
 <form name="registrarSocio" method="post"
       action="./index.php?controlador=cTesorero&amp;accion=modificarIngresoDonacionTes">
							
   <input type="hidden"	name="datosFormDonacion[CODDONACION]"
          value='<?php echo $datosAnotarDonacion['datosFormDonacion']['CODDONACION']['valorCampo']; ?>'
    />									
   <input type="hidden"	name="datosFormDonacion[CODAGRUPACION]"
          value='<?php echo $datosAnotarDonacion['datosFormDonacion']['CODAGRUPACION']['valorCampo']; ?>'
    />											
 <br /> 
  
 <!-- ********************** Inicio tipo DONANTE **************************** -->	 			
	 <fieldset>	
	 <legend><b>Tipo de donante</b></legend>
		<p>	
		<label>*Tipo donante</label>
		<?php	
	/*			echo "<span class='textoAzu112Left2'>Tipo: ".$datosAnotarDonacion['datosFormDonacion']['TIPODONANTE']['valorCampo']. 
 "</span>";
	*/?>
					 <input type="text" readonly class="mostrar"	name="datosFormDonacion[TIPODONANTE]" 
						 value='<?php echo $datosAnotarDonacion['datosFormDonacion']['TIPODONANTE']['valorCampo']?>' 
							size="40" maxlength="40"/>	 
	
		</p>
	 </fieldset>
		<br />	
 <!-- ********************** Fin tipo DONANTE ********************..******** -->	 			
	
  <!-- ********************** Inicio datos de personales DONANTE **************************** -->	 	
		<fieldset>	 
	 <legend><b>Datos personales</b></legend>	
		<p>
		<label>Tipo documento</label>	

				<input type="text" readonly class="mostrar"	
				       name="datosFormDonacion[TIPODOCUMENTOMIEMBRO]" 
											size="12" maxlength="20" 
				       value="<?php {echo $datosAnotarDonacion['datosFormDonacion']['TIPODOCUMENTOMIEMBRO']['valorCampo'];}?>"
					/>
   <label>Nº documento</label> 
	    <input type="text"
					       readonly class='mostrar'
												value="<?php echo $datosAnotarDonacion['datosFormDonacion']['NUMDOCUMENTOMIEMBRO']['valorCampo'];?>"
	           name="datosFormDonacion[NUMDOCUMENTOMIEMBRO]"
	           size="12"
	           maxlength="20"					
	    />																		
	  <br />
	  <label>País documento</label> 
					 
							<input type="text" readonly class="mostrar"	
							       name="datosFormDonacion[CODPAISDOC]"
											   value="<?php echo $datosAnotarDonacion['datosFormDonacion']['CODPAISDOC']['valorCampo'];?>"
								      size="12" maxlength="20"
					/>					
		 <br /><br />	
		 <label>Sexo</label>	
	    <input type="text" readonly class="mostrar"	
	           name="datosFormDonacion[SEXO]"
	           value="<?php echo $datosAnotarDonacion['datosFormDonacion']['SEXO']['valorCampo'];?>"
												size="2"
	           maxlength="2"
					 />								
		  <br />    
	   <label>Nombre</label> 
	    <input type="text" readonly class="mostrar"		
	           name="datosFormDonacion[NOM]"
	           value="<?php echo $datosAnotarDonacion['datosFormDonacion']['NOM']['valorCampo'];?>"
	           size="35"
	           maxlength="100"
	    />	 
		  <br />
		 <label>Apellido primero</label> 
				 <input type="text" readonly class="mostrar"		
	           name="datosFormDonacion[APE1]"
	           value="<?php echo $datosAnotarDonacion['datosFormDonacion']['APE1']['valorCampo'];?>"
	           size="35"
	           maxlength="100"
	    />	

		 <br />	
	   <label>Apellido segundo</label> <!--no obligatorio pero se valida si existe-->
				 <input type="text" readonly class="mostrar"		
	           name="datosFormDonacion[APE2]"
	           value="<?php echo $datosAnotarDonacion['datosFormDonacion']['APE2']['valorCampo'];?>"
	           size="35"
	           maxlength="100"
	    />	
			<br /> 

		 <label>email</label>
	    <input type="text" readonly class="mostrar"		
	           name="datosFormDonacion[EMAIL]"
	           value="<?php echo $datosAnotarDonacion['datosFormDonacion']['EMAIL']['valorCampo'];?>"
	           size="60"
	           maxlength="200"
	    />	 
	
		  <br />	

	   <label>Teléfono fijo</label> 
	    <input type="text" readonly class="mostrar"		
	           name="datosFormDonacion[TELFIJOCASA]"
	           value='<?php if (isset($datosAnotarDonacion['datosFormDonacion']['TELFIJOCASA']['valorCampo']))
	           {  echo $datosAnotarDonacion['datosFormDonacion']['TELFIJOCASA']['valorCampo'];}
	           ?>'
	           size="14"
	           maxlength="14"
	    />	 

		  <br />		
		 <label>Teléfono móvil</label> 
     <input type="text" readonly class="mostrar"		
	           name="datosFormDonacion[TELMOVIL]"
	           value='<?php if (isset($datosAnotarDonacion['datosFormDonacion']['TELMOVIL']['valorCampo']))
	           {  echo $datosAnotarDonacion['datosFormDonacion']['TELMOVIL']['valorCampo'];}
	           ?>'
	           size="14"
	           maxlength="14"
	    />	
		</p>
	 </fieldset>	
	 <!-- ********************** Fin datos de personales DONANTE ******************************* --> 	
				
	 
		<!-- ********************* Inicio Datos donación  ***************************************** -->
	 <br />
		<fieldset>
	  <legend><b>Datos de la donación</b></legend>
		<p>
		<!--*** Inicio AGRUPACION por ahora será siempre '00000000' Europa Laica Estatal, 
		    dejo esto por si más adelante se cambiase **********************************-->			

	  <!-- <label>*La donación se hace a la agrupación territorial</label>-->
	     <?php
				  //echo comboLista($parValorComboAgrupaSocio['lista'], "datosFormDonacion[CODAGRUPACION]",
						//$parValorComboAgrupaSocio['valorDefecto'],$parValorComboAgrupaSocio['descDefecto'],"","");	 
       ?> 					
			<!--*** Fin AGRUPACION por ahora será siempre '00000000' Europa Laica Estatal **-->				
						
			
		<label>*Donación (euros)</label>
	    <input type="text"		        
	           name="datosFormDonacion[IMPORTEDONACION]"
	           value='<?php if (isset($datosAnotarDonacion['datosFormDonacion']['IMPORTEDONACION']['valorCampo']))
	                        {  echo $datosAnotarDonacion['datosFormDonacion']['IMPORTEDONACION']['valorCampo'];}
	                  ?>'
	           size="12"
	           maxlength="30"
	     />			
		<span class="error">
			<?php
			  if (isset($datosAnotarDonacion['datosFormDonacion']['IMPORTEDONACION']['errorMensaje']))
		    {echo $datosAnotarDonacion['datosFormDonacion']['IMPORTEDONACION']['errorMensaje'];}
			?>
  </span>
		
				<br />
		<label>Gastos al abonar la donación si los hubiese (cobrados a EL por PayPal o la entidad bancaria), (euros)</label>
	    <input type="text"		        
	           name="datosFormDonacion[GASTOSDONACION]"
	           value="<?php if (isset($datosAnotarDonacion['datosFormDonacion']['GASTOSDONACION']['valorCampo']))
	                        {  echo $datosAnotarDonacion['datosFormDonacion']['GASTOSDONACION']['valorCampo'];}
																								 else
																									{ echo '0.00';}	
	                  ?>"
	           size="12"
	           maxlength="30"
	     />			
		<span class="error">
			<?php
			  if (isset($datosAnotarDonacion['datosFormDonacion']['GASTOSDONACION']['errorMensaje']))
		    {echo $datosAnotarDonacion['datosFormDonacion']['GASTOSDONACION']['errorMensaje'];}
			?>
  </span>
						
	 <!-- ************************ Inicio FECHAAS *********************** -->
		<br /><br />
		<label>*Fecha del ingreso donación. Sólo permite modificar fecha año actual y anterior (dd/mm/aaaa)</label> 
		<?php    	
 		 $parValorDia["00"]="día"; 
		 for ($d=1;$d<=31;$d++) 
		 {if ($d<10) {$valor="0"."$d";}//para que los días tengan el formato 01, 02,...10,...31
			 else {$valor="$d";}
			 $parValorDia[$valor]=$valor;
		 }
		 //function comboLista($parValor,$identificadorCampo,$valorPrevio,$descPrevio,$valorInicial,$descInicial)
		 echo comboLista($parValorDia,"datosFormDonacion[FECHAINGRESO][dia]",
																			$datosAnotarDonacion['datosFormDonacion']['FECHAINGRESO']['dia']['valorCampo'],
			                $parValorDia[$datosAnotarDonacion['datosFormDonacion']['FECHAINGRESO']['dia']['valorCampo']],"","");	
										 	
		 $parValorMes=array("00"=>"mes","01"=>"Enero","02"=>"Febrero","03"=>"Marzo","04"=>"Abril","05"=>"Mayo","06"=>"Junio",
		 "07"=>"Julio","08"=>"Agosto","09"=>"Septiembre","10"=>"Octubre","11"=>"Noviembre","12"=>"Diciembre");
					 
  	echo comboLista($parValorMes,"datosFormDonacion[FECHAINGRESO][mes]",
			                $datosAnotarDonacion['datosFormDonacion']['FECHAINGRESO']['mes']['valorCampo'],
		                 $parValorMes[$datosAnotarDonacion['datosFormDonacion']['FECHAINGRESO']['mes']['valorCampo']],"","");

		 $parValorAnio["0000"]="año"; 	
			$anioInferior = date("Y")-2;
			for ($a=$anioInferior; $a<=date("Y"); $a++) {$parValorAnio[$a]=$a;} //año anterior y actual pero da problemas de modificación
		 			
			//for ($a = 2012; $a<=date("Y"); $a++){$parValorAnio[$a]=$a;}//todos los años
		 echo comboLista($parValorAnio,"datosFormDonacion[FECHAINGRESO][anio]",
			                $datosAnotarDonacion['datosFormDonacion']['FECHAINGRESO']['anio']['valorCampo'],
			                $parValorAnio[$datosAnotarDonacion['datosFormDonacion']['FECHAINGRESO']['anio']['valorCampo']],"","");	
		 ?>	
 		<span class="error">
			<?php
			if (isset($datosAnotarDonacion['datosFormDonacion']['FECHAINGRESO']['errorMensaje']))
			{echo $datosAnotarDonacion['datosFormDonacion']['FECHAINGRESO']['errorMensaje'];}
			?>
		</span>	
		<br />
		<label>Fecha de la anotación por el tesorero: <b><?php echo date("d/m/Y") ?> </b></label> 
  
		<!-- ************************ Fin FECHAS *************************** -->
		<br /><br />		
	
		<label>Modo de pago</label> 
			<?php 			
		 $parValorModoIngreso=array("SIN-DATOS"=>"Sin datos","DOMICILIADA"=>"Domiciliada","TRANSFERENCIA"=>"Transferencia",
			                           "TARJETA"=>"Tarjeta","CHEQUE"=>"Cheque","METALICO"=>"Metálico","PAYPAL"=>"PayPal");
				 //function comboLista($parValor,$identificadorCampo,$valorPrevio,$descPrevio,$valorInicial,$descInicial)	 
  	echo comboLista($parValorModoIngreso,"datosFormDonacion[MODOINGRESO]",
			                $datosAnotarDonacion['datosFormDonacion']['MODOINGRESO']['valorCampo'],
		                 $parValorModoIngreso[$datosAnotarDonacion['datosFormDonacion']['MODOINGRESO']['valorCampo']],"","");	
		?>
		<br /><br />			

		</p>
	 </fieldset>
	 <br />
 
 <!--************ Inicio Datos de datosFormDonacion[CONCEPTO] ***********-->
		 <fieldset>
	  <legend><b>Concepto</b></legend>
	 <p>
		<?php 			

		 echo comboLista($parValoresDonacionConceptos['lista'], "datosFormDonacion[CONCEPTO]", 
																			$parValoresDonacionConceptos['valorDefecto'], $parValoresDonacionConceptos['descDefecto'], "","");	
		?>
		
		</p>
	 </fieldset>
  <br /><br />
		<!--************ Fin Datos de datosFormDonacion[CONCEPTO] ***********-->	
		
		<!-- ********************* Fin Datos donación  ******************************************** -->
 
	 <!--************ Inicio Datos de datosFormDonacion[OBSERVACIONES] ***********-->
	 <fieldset>
	  <legend><b>Observaciones del tesorero</b></legend>
	 <p>
		<textarea id='OBSERVACIONES' onKeyPress="limitarTextoArea(2000,'OBSERVACIONES');"	
		class="textoAzul8Left" name="datosFormDonacion[OBSERVACIONES]" rows="10" cols="80"><?php 
		  if (isset($datosAnotarDonacion['datosFormDonacion']['OBSERVACIONES']['valorCampo']))                    
			{echo htmlspecialchars(stripslashes($datosAnotarDonacion['datosFormDonacion']['OBSERVACIONES']['valorCampo']));}
		?></textarea> 			 
		</p>
	 </fieldset>
		<!--************ Fin Datos de datosFormDonacion[OBSERVACIONES] ***********-->
		
		<!--********************** Fin Datos de datosFormDonacion ***************-->  
  <div align="center">
    <input type="submit" name="siGuardarDatosDonacion" value="Guardar modificaciones" class="enviar" />
			&nbsp;		&nbsp;		&nbsp;
			<input type="submit" name="salirDonacion" 
			 onClick="return confirm('¿Salir de donación sin guardar datos?')"
			 value='No guardar modificaciones' />
	  </div>
 </form> 
</div>



