<?php
/*-----------------------------------------------------------------------------
Agustín 2016_12_11: cambios añadir PAYPAL en modo de pago

FICHERO: formAnotarIngresoDonacionSocio.php
VERSION: PHP 5.2.3
DESCRIPCION: Es el formulario para el registro de un donante socio, del que se han buscado los datos existentes,
             se muestran los datos personales, pero solo se pueden cambiar los relacionados
													con la donación
OBSERVACIONES:Es incluida desde "./vistas/tesorero/vCuerpoAnotarIngresoDonacionSocio.php"
-------------------------------------------------------------------------------*/
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

	<span class='comentario11'>
							 Se han encontrado datos para este donante que corresponden a un socio/a. 
								Si los datos mostrados no se corresponde con los esperados, puede que hayas cometido un error 
								al introducir su identificación (NIF, NIE, Pasaporte, o email)
								<br /><br />
								En ese caso vuelve a introducirlos de nuevo en <b>'-Anotar donación'</b>.  
								Si siguiese dando el mismo problema podría existir un error de identificación, por lo que debieras 
								ponerte en contacto con el administrador de la aplicación
					   <br /><br />
								Los campos con asterisco (<b>*</b>) son obligatorios
								<br />	
		</span>
		
 <form name="registrarSocio" method="post"
       action="./index.php?controlador=cTesorero&amp;accion=anotarIngresoDonacion">
		<br /><br /> 	
		
  <!-- ********************** Inicio datos de  TIPODONANTE *************************** -->	 	 
	 <fieldset>
	 <legend><b>Tipo de donante</b></legend>
		<p>	
		<label>Tipo donante</label>
											
			<input type="hidden" readonly class="mostrar"	name="datosFormDonacion[encontrado]" 
						    value="<?php echo $datosAnotarDonacion['datosFormDonacion']['encontrado']['valorCampo']?>" 
						    	size="40" maxlength="60" />	
		
		 <input type="text" readonly class="mostrar"	name="datosFormDonacion[TIPODONANTE]" 
						    value="<?php echo $datosAnotarDonacion['datosFormDonacion']['TIPODONANTE']['valorCampo']?>" 
						    	size="40" maxlength="60" />
		</p>
	 </fieldset>
		<br /> 	
	 <!-- ********************** Fin datos de TIPODONANTE ********************************* --> 
		
  <!-- ********************** Inicio datos de identificación MIEMBRO-DONANTE************ --> 	
		<fieldset>	 
	 <legend><b>Datos personales</b></legend>	
		<p>
		<label>Tipo documento</label>		
		 <?php	 
			 if (isset($datosAnotarDonacion['datosFormDonacion']['TIPODOCUMENTOMIEMBRO']['valorCampo']) && 
				          !empty($datosAnotarDonacion['datosFormDonacion']['TIPODONANTE']['valorCampo']))
				{
				?>
				<input type="text" readonly class="mostrar"	
				       name="datosFormDonacion[TIPODOCUMENTOMIEMBRO]" 
											size="12" maxlength="20" 
				       value="<?php {echo $datosAnotarDonacion['datosFormDonacion']['TIPODOCUMENTOMIEMBRO']['valorCampo'];}?>"
					/>	
				<?php 		
				}					
				else	
				{$parValorTipoDoc=array("NIF"=>"NIF","NIE"=>"NIE","Pasaporte"=>"Pasaporte","OTROS"=>"Otros");										 
		   echo comboLista($parValorTipoDoc,"datosFormDonacion[TIPODOCUMENTOMIEMBRO]",
		                 $datosAnotarDonacion['datosFormDonacion']['TIPODOCUMENTOMIEMBRO']['valorCampo'],
										         $parValorTipoDoc[$datosAnotarDonacion['datosFormDonacion']['TIPODOCUMENTOMIEMBRO']['valorCampo']],"NIF","NIF");
				}
																				
		 ?>
   <label>Nº documento</label> 
	    <input type="text"
	           name="datosFormDonacion[NUMDOCUMENTOMIEMBRO]"
	           size="12"
	           maxlength="20"					
					<?php 
					if (isset($datosAnotarDonacion['datosFormDonacion']['NUMDOCUMENTOMIEMBRO']['valorCampo']) && 
					     !empty($datosAnotarDonacion['datosFormDonacion']['NUMDOCUMENTOMIEMBRO']['valorCampo']))
										
					{echo " readonly class='mostrar' value='".$datosAnotarDonacion['datosFormDonacion']['NUMDOCUMENTOMIEMBRO']['valorCampo']."' ";}
					else
					{
					?>									
						value="<?php if (isset($datosAnotarDonacion['datosFormDonacion']['NUMDOCUMENTOMIEMBRO']['valorCampo']))
            {echo $datosAnotarDonacion['datosFormDonacion']['NUMDOCUMENTOMIEMBRO']['valorCampo'];}
            ?>"
						/>
					<?php	
					}
					?>
			<span class="error">
				<?php
				if (isset($datosAnotarDonacion['datosFormDonacion']['NUMDOCUMENTOMIEMBRO']['errorMensaje']))
				{echo $datosAnotarDonacion['datosFormDonacion']['NUMDOCUMENTOMIEMBRO']['errorMensaje'];}
				?>
			</span>																			
	  <br />
	  <label>País documento</label> 
    <?php
			 if (isset($datosAnotarDonacion['datosFormDonacion']['CODPAISDOC']['valorCampo']) && 
				          !empty($datosAnotarDonacion['datosFormDonacion']['CODPAISDOC']['valorCampo']))
					{
					?>						 
							<input type="text" readonly class="mostrar"	
							       name="datosFormDonacion[CODPAISDOC]"
							       size="20" maxlength="40" 
								      value= "<?php echo $datosAnotarDonacion['datosFormDonacion']['CODPAISDOC']['valorCampo'];?>"
							/>	  
					<?php 		
					}					
					else	
					{//$datosAnotarDonacion['datosFormDonacion']['CODPAISDOC']['valorCampo']='FR'; 
					 echo comboLista($parValorComboPaisMiembro['lista'], "datosFormDonacion[CODPAISDOC]",
        	             $parValorComboPaisMiembro['valorDefecto'],$parValorComboPaisMiembro['descDefecto'],'ES',"");																						
					}																		
    ?> 
				<span class="error">
				<?php
					if (isset($datosAnotarDonacion['datosFormDonacion']['CODPAISDOC']['errorMensaje']))
					{echo $datosAnotarDonacion['datosFormDonacion']['CODPAISDOC']['errorMensaje'];}
				?>
				</span>															
		 <br /><br />	
				 <label>Sexo</label>	
		   <span class="error">
			   <?php
			   if (isset($datosAnotarDonacion['datosFormDonacion']['SEXO']['errorMensaje']))
			   {echo $datosAnotarDonacion['datosFormDonacion']['SEXO']['errorMensaje'];}
			   ?>
					</span>	
	    <input type="text" readonly class="mostrar" 	
	           name="datosFormDonacion[SEXO]"
	           value="<?php echo $datosAnotarDonacion['datosFormDonacion']['SEXO']['valorCampo'];?>"												
												size="2"
	           maxlength="2"
						/>												
		  <br /> 
	   <label>Nombre</label> 
	    <input type="text" readonly class="mostrar"
											<?php
													 if (isset($datosAnotarDonacion['datosFormDonacion']['TIPODONANTE']['valorCampo']) && 
									     $datosAnotarDonacion['datosFormDonacion']['TIPODONANTE']['valorCampo']=='ANONIMO')
														{echo " readonly  class='mostrar' value='".$datosAnotarDonacion['datosFormDonacion']['APE1']['valorCampo']."' ";}
											?>			
	           name="datosFormDonacion[NOM]"
	           value="<?php if (isset($datosAnotarDonacion['datosFormDonacion']['NOM']['valorCampo']))
	           {  echo $datosAnotarDonacion['datosFormDonacion']['NOM']['valorCampo'];}
	           ?>"
	           size="35"
	           maxlength="100"
	    />	 
			<span class="error">
				<?php
				if (isset($datosAnotarDonacion['datosFormDonacion']['NOM']['errorMensaje']))
				{echo $datosAnotarDonacion['datosFormDonacion']['NOM']['errorMensaje'];}
				?>
			</span>		
		 <br />	
		 <label>Apellido primero</label> 
	    <input type="text" readonly class="mostrar"
											<?php
													 if (isset($datosAnotarDonacion['datosFormDonacion']['APE1']['valorCampo']) && 
									     $datosAnotarDonacion['datosFormDonacion']['APE1']['valorCampo']=='ANONIMO')
									     {echo " readonly  class='mostrar' value='".$datosAnotarDonacion['datosFormDonacion']['APE1']['valorCampo']."' ";}
											?>				
	           name="datosFormDonacion[APE1]"
	           value="<?php if (isset($datosAnotarDonacion['datosFormDonacion']['APE1']['valorCampo']))
	                 {  echo $datosAnotarDonacion['datosFormDonacion']['APE1']['valorCampo'];}
	                 ?>"
	           size="35"
	           maxlength="100"
	    />	 
			<span class="error">
				<?php
				if (isset($datosAnotarDonacion['datosFormDonacion']['APE1']['errorMensaje']))
				{echo $datosAnotarDonacion['datosFormDonacion']['APE1']['errorMensaje'];}
				?>
		 </span>	
		 <br />	
	   <label>Apellido segundo</label> 
	    <input type="text" readonly class="mostrar"
											<?php
													 if (isset($datosAnotarDonacion['datosFormDonacion']['APE2']['valorCampo']) && 
									     $datosAnotarDonacion['datosFormDonacion']['APE2']['valorCampo']=='ANONIMO')
									     {echo " readonly  class='mostrar' value='".$datosAnotarDonacion['datosFormDonacion']['APE2']['valorCampo']."' ";}
											?>					
	           name="datosFormDonacion[APE2]"
	           value="<?php if (isset($datosAnotarDonacion['datosFormDonacion']['APE2']['valorCampo']))
	                 {  echo $datosAnotarDonacion['datosFormDonacion']['APE2']['valorCampo'];}
	                  ?>"
	           size="35"
	           maxlength="100"
	    />	 
			<span class="error">
				<?php
				if (isset($datosAnotarDonacion['datosFormDonacion']['APE2']['errorMensaje']))
				{echo $datosAnotarDonacion['datosFormDonacion']['APE2']['errorMensaje'];}
				?>
			</span>	
			<br /><br />

		 <label>email</label>
	    <input type="text" readonly class="mostrar"
	           name="datosFormDonacion[EMAIL]"
	           value="<?php if (isset($datosAnotarDonacion['datosFormDonacion']['EMAIL']['valorCampo']))
	           {  echo $datosAnotarDonacion['datosFormDonacion']['EMAIL']['valorCampo'];}
	           ?>"
	           size="60"
	           maxlength="200"
	    />	 
			<span class="error">
				<?php
				if (isset($datosAnotarDonacion['datosFormDonacion']['EMAIL']['errorMensaje']))
				{echo $datosAnotarDonacion['datosFormDonacion']['EMAIL']['errorMensaje'];}
				?>
			</span>	
		  <br />	

	   <label>Teléfono fijo</label> 
	    <input type="text" readonly class="mostrar"
	           name="datosFormDonacion[TELFIJOCASA]"
	           value="<?php if (isset($datosAnotarDonacion['datosFormDonacion']['TELFIJOCASA']['valorCampo']))
	           {  echo $datosAnotarDonacion['datosFormDonacion']['TELFIJOCASA']['valorCampo'];}
	           ?>"
	           size="14"
	           maxlength="14"
	    />	 
			<span class="error">
				<?php
				if (isset($datosAnotarDonacion['datosFormDonacion']['TELFIJOCASA']['errorMensaje']))
				{echo $datosAnotarDonacion['datosFormDonacion']['TELFIJOCASA']['errorMensaje'];}
				?>
			</span>
		  <br />		
		 <label>Teléfono móvil</label> 
     <input type="text" readonly class="mostrar"
	           name="datosFormDonacion[TELMOVIL]"
	           value="<?php if (isset($datosAnotarDonacion['datosFormDonacion']['TELMOVIL']['valorCampo']))
	           {  echo $datosAnotarDonacion['datosFormDonacion']['TELMOVIL']['valorCampo'];}
	           ?>"
	           size="14"
	           maxlength="14"
	    />	 
			<span class="error">
				<?php
				if (isset($datosAnotarDonacion['datosFormDonacion']['TELMOVIL']['errorMensaje']))
				{echo $datosAnotarDonacion['datosFormDonacion']['TELMOVIL']['errorMensaje'];}
				?>
			</span>
		</p>
	 </fieldset>	
	 <!-- ********************** Fin datos de identificación MIEMBRO *************** --> 
	 
		<!-- ********************* Inicio Datos donación  ***************************** -->
	 <br />
		<fieldset>
	  <legend><b>Datos de la donación</b></legend>
		<p>
		<label>*Donación (euros)</label>
	    <input type="text"		        
	           name="datosFormDonacion[IMPORTEDONACION]"
	           value="<?php if (isset($datosAnotarDonacion['datosFormDonacion']['IMPORTEDONACION']['valorCampo']))
	                        {  echo $datosAnotarDonacion['datosFormDonacion']['IMPORTEDONACION']['valorCampo'];}
	                  ?>"
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
		<label>Gastos al abonar la cuota si los hubiese (cobrados a EL por PayPal o la entidad bancaria)</label>
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
								
	 <!-- ************************ Inicio [FECHAPAGO] *********************** -->
		<br /><br />
		<label>*Fecha del ingreso realizado de la donación (dd/mm/aaaa)</label> 
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
		 for ($a=date("Y")-1; $a<=date("Y"); $a++){$parValorAnio[$a]=$a;} 
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
		<label>Fecha de la anotación por tesorería <b><?php echo date("d/m/Y") ?> </b></label> 		
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
		<br />	
		<!-- ************************ Fin datSocio[FECHAPAGO] ************************* -->
					
		<!-- *********************** Fin Datos de Cuotas  ***************************** -->						
		<br />			
	  <label>*La donación se hace a la agrupación territorial</label>
	     <?php
				    echo comboLista($parValorComboAgrupaSocio['lista'], "datosFormDonacion[CODAGRUPACION]",
	        	           $parValorComboAgrupaSocio['valorDefecto'],$parValorComboAgrupaSocio['descDefecto'],"","");	 
       ?> 	
		<br />

		</p>
	 </fieldset>
	 <br />	 
	 <!--********************** Fin Datos de SOCIO ************************************-->			
 
 <!--************ Inicio Datos de datosFormDonacion[CONCEPTO] ***********-->
	 <fieldset>
	  <legend><b>Concepto</b></legend>
	 <p>
		<textarea  id='CONCEPTO' onKeyPress="limitarTextoArea(250,'CONCEPTO');"	
		class="textoAzul8Left" name="datosFormDonacion[CONCEPTO]" rows="3" cols="80"><?php 
		  if (isset($datosAnotarDonacion['datosFormDonacion']['CONCEPTO']['valorCampo']))                    
			{echo htmlspecialchars(stripslashes($datosAnotarDonacion['datosFormDonacion']['CONCEPTO']['valorCampo']));}
		?></textarea> 			 
		</p>
	 </fieldset>
		<!--************ Fin Datos de datosFormDonacion[CONCEPTO] ***********-->	
 
	 <!--************ Inicio Datos de datosFormDonacion[OBSERVACIONES] ***********-->
	 <fieldset>
	  <legend><b>Observaciones del tesorero</b></legend>
	 <p>
		<textarea id='OBSERVACIONES' onKeyPress="limitarTextoArea(250,'OBSERVACIONES');"	
		class="textoAzul8Left" name="datosFormDonacion[OBSERVACIONES]" rows="3" cols="80"><?php 
		  if (isset($datosAnotarDonacion['datosFormDonacion']['OBSERVACIONES']['valorCampo']))                    
			{echo htmlspecialchars(stripslashes($datosAnotarDonacion['datosFormDonacion']['OBSERVACIONES']['valorCampo']));}
		?></textarea> 			 
		</p>
	 </fieldset>
		<!--************ Fin Datos de datosFormDonacion[comentarioSocio] ***********-->
		<!--********************** Fin Datos de datosFormDonacion ***************-->  
  <div align="center">
    <input type="submit" name="siGuardarDatosDonacion" value="Guardar datos de la donación" class="enviar" />
			&nbsp;		&nbsp;		&nbsp;
			<input type="submit" name="salirDonacion" 
			 onClick="return confirm('¿Salir de donación sin guardar datos?')"
			 value='No guardar los datos' />
	  </div>
 </form> 
</div>



