<?php
/****************************************************************************
FICHERO:vCuerpoCambiarExplotacion_MantenimientoAdmin.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Es el formulario para elegir cambiar el modo de trabajo de la aplicación de 
Gestión de soci@s "MANTENIMIENTO<->EXPLOTACIÓN".
Mostrará el estado actual de modo de trabajo de la aplicación y permitirá al modo alternativo
														
Cambiar el modo de trabajo de la aplicación de Gestión de soci@s "MANTENIMIENTO<->EXPLOTACIÓN"

RECIBE: variables $cabeceraCuerpo y $textoCuerpo (con texto de información sobre 
        los modos de trabajo MANTENIMIENTO<->EXPLOTACIÓN) 
								desde cAdmin:cambiarExplotacion_MantenimientoAdmin()	
														
LLAMADA: vCambiarExplotacion_MantenimientoAdmin.php 
         previamente cAdmin:cambiarExplotacion_MantenimientoAdmin()				
LLAMA: formCambiarExplotacion_MantenimientoAdmin.php											

OBSERVACIONES: 
*******************************************************************************/
?>
<!-- ************************* Inicio Cuerpo  ****************************** -->
<?php
require_once './vistas/plantillasGrales/vContent.php';
?>
<!-- *********************** Inicio Cuerpo central ************************ -->
<?php
if (isset($navegacion['cadNavegEnlaces'])) {
    echo $navegacion['cadNavegEnlaces'];
}
?>	
<br /><br />
<h3 align="center">
            
		<?php 
			echo $cabeceraCuerpo;//texto información modos trabajo MANTENIMIENTO<->EXPLOTACIÓN
		?>
    
</h3>
    <br />
	<span class="textoNegro9Left">		
		<?php
			echo $textoCuerpo;// Información sobre estado de mantenimiento y explotación
		?>	 
	</span>
 <br />
<!-- *****************  Inicio Formulario  ************ -->
<?php require_once './vistas/admin/formCambiarExplotacion_MantenimientoAdmin.php';
?>
<!-- ******************  Fin Formulario   ************* -->
<!-- ********************  Inicio form botón anterior******************** --> 		
<div align="center">	
    <br />		
    <?php
    if (isset($navegacion['anterior'])) {
        echo $navegacion['anterior'];
    }
    ?>	
    <br />			
</div 
<!-- ********************  Fin Form botón anterior *********************** --> 			
</div>
<br /><br />
<!-- ************************** Fin Cuerpo central  ************************ -->	
</div>
<!-- ******************************** Fin Cuerpo  ****************************** -->

