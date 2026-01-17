<?php
/***************************** Inicio vCuerpoExcelMenuCoord ******************************
FICHERO: vCuerpoExcelMenuCoord.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: 
Formulario para exportar a un fichero Excel, los socios de todas las agrupaciones 
correspondientes a un área de gestión o bien de una agrupación concreta elegida 
dentro de ese área territorial que gestiona un coordinador. 
En el caso del que el área de gestión incluya varias posibles agrupaciones, 
por ejemplo Andalucía, en este formulario se permite elegir una Agrupación territorial concreta

Se descarga en el PC en carpeta "Descargas" el archivo Excel mediante el navegador
Al abrir el archivo Excel, puede dar un aviso sobre seguridad.

LLAMADA: vistas/coordinador/vExcelMenuCoord.php 
y a su vez desde cCoordinador.php: excelSociosCoord()

LLAMA: vistas/coordinador/formExcelMenuCoord.php y plantillas generales													

OBSERVACIONES: 
*****************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';

/************************* Inicio Cuerpo central *************************/
echo $navegacion['cadNavegEnlaces'];
?>
<br /><br />
<h3 align="center">
    
				EXPORTAR DATOS DE LOS SOCIOS/AS A ARCHIVO EXCEL PARA USO INTERNO DE COORDINACIÓN
</h3>

<!-- *** Inicio formActualizarSocioPres (se podría pasar com parámetro)******* -->
<?php require_once './vistas/coordinador/formExcelMenuCoord.php'; ?>
<!-- ********************  Inicio form botón anterior******************** --> 		
<div align="center">
    <?php
    /* if (isset($botonSubmit['enlaceBoton'])&&($botonSubmit['enlaceBoton']!=='')&&
      ($botonSubmit['enlaceBoton']!==NULL))
      { echo	"<form method='post' action=./".$botonSubmit['enlaceBoton'].">".
      " <input type='submit' value=".$botonSubmit['textoBoton'].">";
      echo " </form>";
      }
     */
    ?>		
    <?php
    if (isset($navegacion['anterior']))
				{
        echo $navegacion['anterior'];
    }
    ?>				
    <br /><br />			
</div 
<!-- ********************  Fin Form botón anterior *********************** --> 
</div>
<!-- ****************************** Fin Cuerpo central  ******************** -->
</div>
<!-- ******************************* Fin vCuerpoExcelMenuCoord ************** -->