<?php
/*----------------------------------------------------------------------------------------------------														
FICHERO: formBorrarCambiarCoordArea.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Es el formulario que muestra algunos datos personales, buscados previamente, de un socio que ya tiene
un área de coordinación asignada.

Mediante tres botones: "Cambiar el área asignada", "Eliminar asignación ", y para "Cancelar" 
se puede retirarle o cambiarle una coordinación de área territorial,

LLAMADA: vistas/presidente/vCuerpoBorrarCambiarCoordArea.php
y previamente desde cPresidente.php:asignarCoordinacionAreaBuscar()

OBSERVACIONES:											
-----------------------------------------------------------------------------------------------------*/
?>
<div id="registro">

 <br />	
 <span class="textoAzu112Left2">
	 Este socio/a es coordinador/a de un área de gestión territorial
  </span>
  <br /> <br />
 <div id="formLinea">
		<form method="post" class="linea" 
      action="./index.php?controlador=cPresidente&amp;accion=cambiarCoordinacionArea">			
						
	 <!-- ******************* Inicio Form para cambiar área de coordinación ************* -->
    <input type="hidden"
         	id="codUser"
          name="datosFormSocio[CODUSER]"
          value='<?php if (isset($datSocio['datosFormSocio']['CODUSER']['valorCampo']))
                       {  echo $datSocio['datosFormSocio']['CODUSER']['valorCampo'];}
                 ?>'
   />
    <input type="hidden"
         	id="codUser"
          name="datosFormSocio[CODAREAGESTIONAGRUP]"
          value='<?php if (isset($datSocio['datosFormSocio']['CODAREAGESTIONAGRUP']))
                       {  echo $datSocio['datosFormSocio']['CODAREAGESTIONAGRUP'];}
                 ?>'
   />			
		 <!-- ****************** Inicio Datos de  ÁREA DE COORDINACIÓN  Socio************ -->
	 <fieldset>
	  <legend><b>Área de gestión territorial coordinada por el coordinador/a</b></legend>
		<p>
				<label>Área de gestión agrupaciones territorial</label> 
	    <input type="text" readonly
						      class="mostrar"			        
	           name="datosFormSocio[NOMBREAREAGESTION]"
	           value='<?php if (isset($datSocio['datosFormSocio']['NOMBREAREAGESTION']))
	                        {  echo $datSocio['datosFormSocio']['NOMBREAREAGESTION'];}
	                  ?>'
	           size="80"
	           maxlength="150"
	     />
						<br />
		</p>
	 </fieldset>
	 <!-- ******************Fin Datos de  ÁREA DE COORDINACIÓN  Socio*************** -->	 
					
 	 <!-- ******************* Inicio Datos de identificación Socio ************** --> 
	 <fieldset>	 
	  <legend><b>Datos personales</b></legend>	
		<p>
	   <label>Estado socio/a (alta, baja, ...)</label> 
	    <input type="text" readonly
											 class="mostrar"		
	           name="datosFormSocio[ESTADO]"
	           value='<?php if (isset($datSocio['datosFormSocio']['ESTADO']['valorCampo']))
	           {  echo $datSocio['datosFormSocio']['ESTADO']['valorCampo'];}
	           ?>'
	           size="35"
	           maxlength="100"
	    />
					<br />		
				
	   <label>Nombre</label> 
	    <input type="text" readonly
						      class="mostrar"		
	           name="datosFormSocio[NOM]"
	           value='<?php if (isset($datSocio['datosFormSocio']['NOM']['valorCampo']))
	           {  echo $datSocio['datosFormSocio']['NOM']['valorCampo'];}
	           ?>'
	           size="35"
	           maxlength="100"
	    />	
	  <br />
		<label>Apellido primero</label> 
	    <input type="text" readonly
						 class="mostrar"		
	           name="datosFormSocio[APE1]"
	           value='<?php if (isset($datSocio['datosFormSocio']['APE1']['valorCampo']))
	           {  echo $datSocio['datosFormSocio']['APE1']['valorCampo'];}
	           ?>'
	           size="35"
	           maxlength="100"
	    />
	
	   <label>Apellido segundo</label> 
	    <input type="text" readonly
						 class="mostrar"		
	           name="datosFormSocio[APE2]"
	           value='<?php if (isset($datSocio['datosFormSocio']['APE2']['valorCampo']))
	                 {  echo $datSocio['datosFormSocio']['APE2']['valorCampo'];}
	                  ?>'
	           size="35"
	           maxlength="100"
	    />	 
		<br /><br />
		<label>Correo electrónico</label>
	    <input type="text" readonly
						 class="mostrar"		
	           name="datosFormSocio[EMAIL]"
	           value='<?php if (isset($datSocio['datosFormSocio']['EMAIL']['valorCampo']))
	           {  echo $datSocio['datosFormSocio']['EMAIL']['valorCampo'];}
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
          value='<?php if (isset($datSocio['datosFormSocio']['TIPODOCUMENTOMIEMBRO']['valorCampo']))
                       {  echo $datSocio['datosFormSocio']['TIPODOCUMENTOMIEMBRO']['valorCampo'];}
                 ?>'
	         size="10"
	         maxlength="20"							
   />	
			<label for="user">Documento</label> 
   <input type="text" readonly
						    class="mostrar"		
         	id="codUser"
          name="datosFormSocio[NUMDOCUMENTOMIEMBRO]"
          value='<?php if (isset($datSocio['datosFormSocio']['NUMDOCUMENTOMIEMBRO']['valorCampo']))
                       {  echo $datSocio['datosFormSocio']['NUMDOCUMENTOMIEMBRO']['valorCampo'];}
                 ?>'
          size="12"
          maxlength="20"																	
   />		
  <label for="user">Código País</label> 
  <input type="text" readonly
						   class="mostrar"		
         	id="codUser"
          name="datosFormSocio[CODPAISDOC]"
          value='<?php if (isset($datSocio['datosFormSocio']['CODPAISDOC']['valorCampo']))
                       {  echo $datSocio['datosFormSocio']['CODPAISDOC']['valorCampo'];}
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
          value='<?php if (isset($datSocio['datosFormSocio']['LOCALIDAD']['valorCampo']))
                       {  echo $datSocio['datosFormSocio']['LOCALIDAD']['valorCampo'];}
                 ?>'
         size="60"
         maxlength="120"																	
   />		
			<br />
					<label>Observaciones</label> 				
	    <input type="text" readonly
						   class="mostrar"
	           name="datosFormSocio[OBSERVACIONES]"
	           value='<?php if(isset($datSocio['datosFormSocio']['OBSERVACIONES']))
	           {  echo $datSocio['datosFormSocio']['OBSERVACIONES'];}
	           ?>'
	           size="60"
	           maxlength="250"
	    />	
		
						
		</p>
	 </fieldset>
	 <br />	
	 <!-- ******************* Fin Datos de identificación Socio ************** --> 	 
			 <input type="submit"  value="Cambiar el ára asignada" class="enviar">	
 </form>
	<!-- ******************* Fin Form para cambiar área de coordinación ****************** -->
	
	
 <!-- ******************* Inicio Form para eliminarr área de coordinación ************* -->

  <form method="post" class="linea"
     	  action="./index.php?controlador=cPresidente&amp;accion=eliminarCoordinacionArea"
	       onSubmit="return confirm('¿Eliminar coordinación?')">											
    <input type="hidden"
           name="datosFormSocio[CODUSER]"
           value='<?php if (isset($datSocio['datosFormSocio']['CODUSER']['valorCampo']))
                       {  echo $datSocio['datosFormSocio']['CODUSER']['valorCampo'];}
                 ?>'
   />
    <input type="hidden"
           name="datosFormSocio[CODAREAGESTIONAGRUP]"
           value='<?php if (isset($datSocio['datosFormSocio']['CODAREAGESTIONAGRUP']))
                       {  echo $datSocio['datosFormSocio']['CODAREAGESTIONAGRUP'];}
                 ?>'
   />			
	    <input type="hidden"
						      name="datosFormSocio[NOM]"
	           value='<?php if (isset($datSocio['datosFormSocio']['NOM']['valorCampo']))
	           {  echo $datSocio['datosFormSocio']['NOM']['valorCampo'];}
	           ?>'
	    />
	    <input type="hidden"	
	           name="datosFormSocio[APE1]"
	           value='<?php if (isset($datSocio['datosFormSocio']['APE1']['valorCampo']))
	           {  echo $datSocio['datosFormSocio']['APE1']['valorCampo'];}
	           ?>'
	    />
	    <input type="hidden"			
	           name="datosFormSocio[EMAIL]"
	           value='<?php if (isset($datSocio['datosFormSocio']['EMAIL']['valorCampo']))
	           {  echo $datSocio['datosFormSocio']['EMAIL']['valorCampo'];}
	           ?>'
	    />	  
		
	    <input type="submit" name="SiEliminar" value="Eliminar asignación" class="enviar" 
				        onSubmit="return confirm('¿Eliminar?')">
  </form>	
<!-- ******************* Fin Form para eliminar área de coordinación ************* -->	

<!-- ******************* Inicio Form botón para cancelar ************************** -->	
		<form method="post" class="linea"
      action="./index.php?controlador=cPresidente&amp;accion=asignarCoordinacionAreaBuscar">		
   <input type="submit" name="Cancelar" value="Cancelar operación" class="enviar">
  </form>	
<!-- ******************* Fin Form botón para cancelar ******************************* -->					
 </div><!--<div id="formLinea">-->
</div><!-- <div id="registro">-->




