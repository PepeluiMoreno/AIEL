<?php
/*--------------------------------------------------------------------------------------------------
FICHERO: formMenuEstadisticasPres.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION:													
Se forma el menú estadísticas que llamar a las páginas para mostrar: Datos de socios fallecidos,
Exportar los nombres de socias/os y otros datos a Excel para el informe anual de secretaría, 
Exportar a Excel estadísticas de totales de socias/os y altas y bajas anuales organizado por:
AGRUPACIONES, PROVINCIAS o CCAA 

Uso exclusivo de rol presidencia.
													
LLAMADA: vistas/presidente/vCuerpoMenuEstadisticasPres.php		
y a su vez cPresidente.php:cMenuEstadisticasPres()

LLAMA: Desde ese menú se podrá llamar a: cPresidente.php: mostrarSociosFallecidosPres(),
cExportarExcelInformeAnualPres(),cExportarExcelEstadisticasAltasBajasAgrupPres(),
cExportarExcelEstadisticasAltasBajasProvPres(),cExportarExcelEstadisticasAltasBajasCCAAPres()

OBSERVACIONES:

--------------------------------------------------------------------------------------------------*/
?>
<div id="registro">

  <a href="./index.php?controlador=cPresidente&amp;accion=mostrarSociosFallecidosPres" 
      title="mostrarSociosFallecidosPres">
						 <strong>>> Mostrar lista de socia/os fallecidos</strong></a>
							<br /><br />
							En esta lista se muestran las socias y socios que han sido dados de baja por fallecimiento

	 	<br /><br /><br /><br />	
			
  <a href="./index.php?controlador=cPresidente&amp;accion=cExportarExcelInformeAnualPres" 
      title="Cierre de año: exportar datos soci@s a excel para informe anual de secretaría">
						 <strong>>> Exportar los nombres de socias/os y otros datos a Excel para el informe anual de secretaría</strong></a>
							<br /><br />
							. Este informe lo podrá utilizar presidencia para la memoria anual
							<br /><br />. En el archivo Excel, se incluye todos los socios/as que en el año correspondiente estuvieron de alta, aunque después en ese mismo año se diesen de baja.
	
	<br /><br /><br /><br />	

 <strong>Exportar estadísticas de totales de socias/os y altas y bajas anuales organizado por AGRUPACIONES, PROVINCIAS o CCAA </strong>

							<br /><br />	
       . Exporta un archivo Excel con formato de tabla con datos estadísticos de los socios/as de las agrupaciones (todas o una a elegir) y un rango de años a elegir
							<br /><br />	
							. El contenido que se mostrará por agrupaciones y años es el siguiente:
							<br />
							&nbsp;&nbsp;&nbsp;Total de Alta(socias/os en alta a final de año),	ALTAS AÑO (Total,	H,	%H,	M,	%M), BAJAS AÑO(Total,	H,	%H,	M,	%M),	Altas Netas(por año)
						 <br /><br />
       . Los datos se corresponden a finales de los años elegidos (Y-12-31).							
		<br /><br /><br />	
							
  <a href="./index.php?controlador=cPresidente&amp;accion=cExportarExcelEstadisticasAltasBajasAgrupPres"
      title="Exportar estadísticas de totales de socixs y altas y bajas anuales">
						 <strong>>> Exportar estadísticas de totales de socias/os y altas y bajas anuales organizado por AGRUPACIONES</strong></a>  			
							
	 <br /><br /><br />

  <a href="./index.php?controlador=cPresidente&amp;accion=cExportarExcelEstadisticasAltasBajasProvPres" 
      title="Exportar estadísticas de totales de socixs y altas y bajas anuales por PROVINCIAS">
						 <strong>>> Exportar estadísticas de totales de socias/os y altas y bajas anuales organizado por PROVINCIAS</strong></a>

		
			<br /><br /><br />

  <a href="./index.php?controlador=cPresidente&amp;accion=cExportarExcelEstadisticasAltasBajasCCAAPres" 
      title="Exportar estadísticas de totales de socixs y altas y bajas anuales por CCAA">
						 <strong>>> Exportar estadísticas de totales de socias/os y altas y bajas anuales organizado por CCAA</strong></a>
							<br /><br />	
 
							
			<?php 
			if (isset($botonSubmit['enlaceBoton'])&&($botonSubmit['enlaceBoton']!=='')&&
			($botonSubmit['enlaceBoton']!==NULL))
   {
	    echo	"<form method='post' action=./".$botonSubmit['enlaceBoton'].">".
				      " <input type='submit' value=".$botonSubmit['textoBoton'].">";
		   echo " </form>";
			}	
			?>
			 <br /><br />			
	</div>  				