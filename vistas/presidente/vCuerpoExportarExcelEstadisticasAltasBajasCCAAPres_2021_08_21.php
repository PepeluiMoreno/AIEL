<?php
/**************** Inicio vCuerpoExportarExcelEstadisticasAltasBajasCCAAPres.php ********************
FICHERO: vCuerpoExportarExcelEstadisticasAltasBajasCCAAPres.php
VERSION: PHP 7.3.21

DESCRIPCION: Formulario para exportar a Excel informes estadísticos por "CCAA" y "años" 
a fecha Y-12-31 con los datos siguientes: 

Total de Alta,	ALTAS_ANIO(Total	H	%H	M	%M),BAJAS_ANIO(Total	H	%H	M	%M),	Netas	de Alta(por año)

Permite elegir CCAA (lo normal es que se incluyan todas) y "rangos de año inferior-superior" 
a fecha Y-12-31 desde el año 2009 al actual.

LLAMADA: vistas/presidente/vExportarExcelEstadisticasAltasBajasCCAAPres.php
y previamente desde  cPresidente.php:cExportarExcelEstadisticasAltasBajasCCAAPres(),

LLAMA: vistas/presidente/formExportarExcelEstadisticasAltasBajasCCAAPres.php
e incluye plantillasGrales

OBSERVACIONES: El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario.

***************************************************************************************************/
require_once './vistas/plantillasGrales/vContent.php';

/************************** Inicio Cuerpo central *************************/

echo $navegacion['cadNavegEnlaces'];

?>

<br /><br />

<h3 align="center">
    EXPORTAR ESTADÍSTICAS A EXCEL POR CCAA (Comunidades Autónomas) Y POR AÑOS CON LOS SIGUIENTES DATOS:
				<br /><br />
				Total de Alta (socias/os en alta a final de año),	ALTAS AÑO (Total,	H,	%H,	M,	%M), BAJAS AÑO (Total,	H,	%H,	M,	%M),	Altas Netas (por año)
</h3>

<!-- ** Inicio formExportarExcelEstadisticasAltasBajasCCAAPres(se podría pasar con parámetro) ** -->
<?php require_once './vistas/presidente/formExportarExcelEstadisticasAltasBajasCCAAPres.php'; ?>
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

</div 
<!-- ********************  Fin Form botón anterior *********************** --> 
</div>
<!-- ****************************** Fin Cuerpo central  ******************** -->
</div>
<!-- ************ Fin vCuerpoExportarExcelEstadisticasAltasBajasCCAAPres.php ****************** -->