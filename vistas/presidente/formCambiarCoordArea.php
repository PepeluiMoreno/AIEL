<?php
/*-----------------------------------------------------------------------------
FICHERO: formCambiarCoordArea.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Es el formulario que muestra algunos datos personales, buscados previamente, de un socio que ya tiene
un área de coordinación asignada.

Mediante botón: "Cambiar área coordinación", se puede cambiarle la coordinación del área territorial
actual por otra área, (o botón para "Cancelar" )

LLAMADA: vistas/presidente/vCuerpoCambiarCoordAreaInc.php
y previamente cPresidente.php:cambiarCoordinacionArea() 

OBSERVACIONES:							
-------------------------------------------------------------------------------*/
require_once './modelos/libs/comboLista.php';
?>

<div id="registro">
 <br /><br />
		<span class="error">	
			</span>
		<br /><br />		
		
 <div id="formLinea">
	
  <form method="post" class="linea" 
        action="./index.php?controlador=cPresidente&amp;accion=cambiarCoordinacionArea"	
			     onSubmit="return confirm('¿Asignar coordinación?')">			
			
	 <!-- ****************** Inicio Datos de Area de gestión ************ -->
	 <fieldset>
	  <legend><b>Elegir un área de gestión de agrupaciones territoriales (dentro de las existentes)</b></legend>
		<p>
	  <label>*Área de gestión</label>
	   <?php
				
					//---------- Inicio reordenar listado Área de gestión --------------------------------------------
					
					unset($parValorComboAreaGestion['lista']['00000000']);//elimina el elemento correspondiente a Europa Laica Estatal e Internacional
					
					$parValorComboAreaGestion['lista']['00000000']='Europa Laica Estatal e Internacional'; //añade al final del array el elemento correspondiente a Europa Laica Estatal e Internacional

					//---------- Fin reordenar listado Área de gestión -----------------------------------------------
					
			  //function comboLista($parValor,$identificadorCampo,$valorPrevio,$descPrevio,$valorInicial,$descInicial)
	    //echo utf8_encode(comboLista($parValorComboAreaGestion['lista']));
				 echo comboLista($parValorComboAreaGestion['lista'], "datosFormSocio[CODAREAGESTIONAGRUP]",
	        	           $parValorComboAreaGestion['valorDefecto'],$parValorComboAreaGestion['descDefecto'],"","");	 
    ?> 	
		<br />
		</p>
	 </fieldset>
	 <!-- ******************Fin Datos de iDatos de Area de gestión ************ -->			
						
	 <!-- ******************* Inicio Datos de SOCIO ******************************** -->
    <input type="hidden"
           name="datosFormSocio[CODUSER]"
           value='<?php if (isset($datSocio['datosFormSocio']['CODUSER']))
                       {  echo $datSocio['datosFormSocio']['CODUSER'];}
                 ?>'
   />
    <input type="hidden"
           name="datosFormSocio[codAreaGestionOld]"
           value='<?php if (isset($datSocio['datosFormSocio']['codAreaGestionOld']))
                       {  echo $datSocio['datosFormSocio']['codAreaGestionOld'];}
                 ?>'
   />			
 	
	 <fieldset>	 
	  <legend><b>Datos personales</b></legend>	
		<p>
	   <label>Estado socio/a (alta, baja, ...)</label> 
	    <input type="text" readonly
											 class="mostrar"		
	           name="datosFormSocio[ESTADO]"
	           value='<?php if (isset($datSocio['datosFormSocio']['ESTADO']))
	           {  echo $datSocio['datosFormSocio']['ESTADO'];}
	           ?>'
	           size="35"
	           maxlength="100"
	    />
					<br />		<br />							
	   <label>Nombre</label> 
	    <input type="text" readonly
						      class="mostrar"		
	           name="datosFormSocio[NOM]"
	           value='<?php if (isset($datSocio['datosFormSocio']['NOM']))
	           {  echo $datSocio['datosFormSocio']['NOM'];}
	           ?>'
	           size="35"
	           maxlength="100"
	    />	
	  <br />
		<label>Apellido primero</label> 
	    <input type="text" readonly
						 class="mostrar"		
	           name="datosFormSocio[APE1]"
	           value='<?php if (isset($datSocio['datosFormSocio']['APE1']))
	           {  echo $datSocio['datosFormSocio']['APE1'];}
	           ?>'
	           size="35"
	           maxlength="100"
	    />
	   <label>Apellido segundo</label> 
	    <input type="text" readonly
						 class="mostrar"		
	           name="datosFormSocio[APE2]"
	           value='<?php if (isset($datSocio['datosFormSocio']['APE2']))
	                 {  echo $datSocio['datosFormSocio']['APE2'];}
	                  ?>'
	           size="35"
	           maxlength="100"
	    />	 
		<br /><br />
		<label>Correo electrónico</label>
	    <input type="text" readonly
						 class="mostrar"		
	           name="datosFormSocio[EMAIL]"
	           value='<?php if (isset($datSocio['datosFormSocio']['EMAIL']))
	           {  echo $datSocio['datosFormSocio']['EMAIL'];}
	           ?>'
	           size="60"
	           maxlength="200"
	    />	  
	  <br />
		
		 <label for="user">Tipo documento</label> 
   <input type="text" readonly
						    class="mostrar"		
         	id="codUser"
          name="datosFormSocio[TIPODOCUMENTOMIEMBRO]"
          value='<?php if (isset($datSocio['datosFormSocio']['TIPODOCUMENTOMIEMBRO']))
                       {  echo $datSocio['datosFormSocio']['TIPODOCUMENTOMIEMBRO'];}
                 ?>'
	         size="10"
	         maxlength="20"							
   />	
			<label for="user">Documento</label> 
   <input type="text" readonly
						    class="mostrar"		
         	id="codUser"
          name="datosFormSocio[NUMDOCUMENTOMIEMBRO]"
          value='<?php if (isset($datSocio['datosFormSocio']['NUMDOCUMENTOMIEMBRO']))
                       {  echo $datSocio['datosFormSocio']['NUMDOCUMENTOMIEMBRO'];}
                 ?>'
          size="12"
          maxlength="20"																	
   />		
  <label for="user">Código País</label> 
  <input type="text" readonly
						   class="mostrar"		
         	id="codUser"
          name="datosFormSocio[CODPAISDOC]"
          value='<?php if (isset($datSocio['datosFormSocio']['CODPAISDOC']))
                       {  echo $datSocio['datosFormSocio']['CODPAISDOC'];}
                 ?>'
         size="3"
         maxlength="4"																	
   />	
			<br />
  <label for="user">Localidad</label> 
  <input type="text" readonly
						   class="mostrar"		
         	id="codUser"
          name="datosFormSocio[LOCALIDAD]"
          value='<?php if (isset($datSocio['datosFormSocio']['LOCALIDAD']))
                       {  echo $datSocio['datosFormSocio']['LOCALIDAD'];}
                 ?>'
         size="50"
         maxlength="100"																	
   />
				
		</p>
	 </fieldset>		
	 <br />	
		
		<!--************ Inicio Datos de datosFormSocio[OBSERVACIONES]***********-->
			<fieldset>
							<legend><b>Observaciones</b></legend>
							<p>
											<textarea  id='OBSERVACIONES' onKeyPress="limitarTextoArea(250, 'OBSERVACIONES');"	
																						class="textoAzul8Left" name="datosFormSocio[OBSERVACIONES]" rows="3" cols="80"><?php
if (isset($datSocio['datosFormSocio']['OBSERVACIONES'])) {
echo htmlspecialchars(stripslashes($datSocio['datosFormSocio']['OBSERVACIONES']));
}
?></textarea> 			 
							</p>
			</fieldset>
	 <!-- ******************* Fin Datos de identificación Socio ************** -->		
 
	  <input type="submit" name="SiCambiar" value="Cambiar área de coordinación" class="enviar">	
			
  </form>
		

  <form method="post" class="linea"
      action="./index.php?controlador=cPresidente&amp;accion=cambiarCoordinacionArea">		
						
   <input type="submit" name="NoCambiar" value="Cancelar cambiar área de coordinación" class="enviar">
			
  </form>			
		
 </div><!--<div id="formLinea">-->

</div><!-- <div id="registro">-->




