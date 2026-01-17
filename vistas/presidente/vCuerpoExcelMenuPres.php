<?php
/***************************** Inicio vCuerpoExcelMenuPres ******************************
FICHERO: vCuerpoExcelMenuPres.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: 
Formulario para exportar a un fichero Excel por un gestor con rol de Presidencia, 
Vice. y Secretaría, algunos datos de los socios de las agrupaciones elegidas
en este formulario 

Se descarga en el PC en carpeta "Descargas" el archivo Excel mediante el navegador
Al abrir el archivo Excel, puede dar un aviso sobre seguridad.

LLAMADA: vistas/presidente/vExcelMenuPresInc.php 
y a su vez desde cPresidente.php: excelSociosPres()

LLAMA: vistas/presidente/formExcelMenuCoord.php y plantillas generales													

OBSERVACIONES: 
*****************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';

/*************************** Inicio Cuerpo central ***************************/

echo $navegacion['cadNavegEnlaces'];
?>
<br /><br />
<h3 align="center">  

    EXPORTAR DATOS DE LOS SOCIOS/AS A ARCHIVO EXCEL PARA USO INTERNO				
</h3>

<!-- *** Inicio formActualizarSocioPres (se podría pasar com parámetro)******* -->
<?php require_once './vistas/presidente/formExcelMenuPres.php'; ?>
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
    if (isset($navegacion['anterior'])) {
        echo $navegacion['anterior'];
    }
    ?>				

</div 
<!-- ********************  Fin Form botón anterior *********************** --> 
</div>
<!-- ****************************** Fin Cuerpo central  ******************** -->
</div>
<!-- ******************************* FinvCuerpoExcelMenuPres **************** -->