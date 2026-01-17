<?php
/*****************************************************************************
FICHERO:vCuerpoCierreAnioPasadoAperturaAnioNuevo.php
VERSION: PHP 7.3.21

Es el formulario para ejecutar el "Cierre de año pasado y apertura de año nuevo" 
En los botones al final de formulario, se eligen en las opciones disponibles 
- $anioActual = date('Y') para ejecutar en año actual.
-	$anioPruebaYmas1 = date('Y')+1) para simular para año siguiente antes de enero

A partir del directorio "$dirHome", se muestra en que versión de Gestión de Soci@s, 
que ese momento se está ejecutando: 
- La REAL "europalaica.com/usuarios" 
- Versiones para PRUEBA: "europalaica.com/usuarios_copia", o "europalaica.com/usuarios_desarrollo"

E informa también de la SECUENCIA DE EJECUCIÓN 

LLAMADO: vistas/admin/vCierreAnioPasadoAperturaAnioNuevo.php
         previamente desde cAdmin.php:cierreAnioPasadoAperturaAnioNuevoAdmin()	
LLAMA: vistas/admin/formCierreAnioPasadoAperturaAnioNuevo.php									
									
OBSERVACIONES:
******************************************************************************/
?>

<!-- ************************* Inicio Cuerpo  ****************************** -->
<?php
 require_once './vistas/plantillasGrales/vContent.php';
?>
<!-- *********************** Inicio Cuerpo central ************************ -->
<?php
if (isset($navegacion['cadNavegEnlaces'])) 
{
    echo $navegacion['cadNavegEnlaces'];
}
?>	
<br /><br />

<h3 align="center">
            
		<?php 
			echo $datosMensaje['textoCabecera']
		?>
    
</h3>
 <br /><br />
	
	<span class="textoNegro9Left">		
		<?php
			//echo $textoCuerpo;
			echo $datosMensaje['textoComentarios'];
		?>	 
	</span>
 <br /><br />			
	
<!-- *****************  Inicio Formulario  ************ -->
<?php require_once './vistas/admin/formCierreAnioPasadoAperturaAnioNuevo.php';
?>
<!-- ******************  Fin Formulario   ************* -->

<!-- ********************  Inicio form botón anterior******************** --> 		
<div align="center">	
    <br />		
    <?php
    if (isset($navegacion['anterior'])) 
				{
        echo $navegacion['anterior'];
    }
    ?>	
    <br />			
</div 
<!-- ********************  Fin Form botón anterior *********************** --> 			
</div><!-- se abrió dentro vContent.php -->

<br /><br />
<!-- ************************** Fin Cuerpo central  ************************ -->	
</div>
<!-- ******************************** Fin Cuerpo  ****************************** -->

