<?php
/***********************************************************************************************
FICHERO:vCuerpoMenuEstadisticasPres.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION:													
Se forma el menú estadísticas que llamar a las páginas para mostrar: Datos de socios fallecidos,
Exportar los nombres de socias/os y otros datos a Excel para el informe anual de secretaría, 
Exportar a Excel estadísticas de totales de socias/os y altas y bajas anuales organizado por:
AGRUPACIONES, PROVINCIAS o CCAA 

Uso exclusivo de rol presidencia.
													
LLAMADA: vistas/presidente/vMenuEstadisticasPresInc.php 
y a su vez cPresidente.php:cMenuEstadisticasPres()

LLAMA: vistas/presidente/formMenuEstadisticasPres.php

OBSERVACIONES: 
**********************************************************************************************/

/************************** Inicio Cuerpo  *******************************/

require_once './vistas/plantillasGrales/vContent.php';

/*********************** Inicio Cuerpo central **************************/

if (isset($navegacion['cadNavegEnlaces'])) 
{
    echo $navegacion['cadNavegEnlaces'];
}
?>	
<br /><br />
<h3 align="center">
<br />

    ESTADÍSTICAS, FALLECIDOS Y OTROS DATOS DISPONIBLES PARA PRESIDENCIA, VICEPRESIDENCIA Y SECRETARÍA  
    
</h3>
<br /><br /><br />
<!-- *****************  Inicio Formulario  ************ -->
<?php require_once './vistas/presidente/formMenuEstadisticasPres.php';
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
</div>
<br /><br />
<!-- ************************** Fin Cuerpo central  ************************ -->	
</div>
<!-- ******************************** Fin Cuerpo  ****************************** -->

