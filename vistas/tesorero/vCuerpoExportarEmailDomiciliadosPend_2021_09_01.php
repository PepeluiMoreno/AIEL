<?php
/***************************** Inicio vCuerpoExportarEmailDomiciliadosPendInc.php *****************
FICHERO: vCuerpoExportarEmailDomiciliadosPendInc.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Formulario de selección de campos para buscar Emails y otros datos de todos 
las cuotas de los socios y exportar los emails en forma de lista separados por (;)
a un fichero ".txt" para copiar y pegar en el correo de NODO50 (tesoreria@europalica.org), 
y enviar un email a los socios de la lista, con texto libre para avisar a los socios 
para avisar a los socios que se van enviar "las órdenes de cobro de las cuotas domiciliadas"  
para el cobro por el banco (actualmente B.Santander norma SEPA-XML), 
de las agrupaciones elegidas y que están de alta en el momento actual y según la 
siguiente selección:

- Cuenta bancaria en España
- Cuenta bancaria países SEPA (distintos de España) y "Ordenar cobro banco = SI", 
  POR AHORA NO SE PUEDE GENERAR EL SEPA-XML CON ESTA APLICACIÓN por falta cálculo BIC 
  otros países SEPA, pero se podría hacer manualmente en la web del B.Santander si se 
  consiguiesen esos BICs.	

LLAMADA: vistas/tesorero/vExportarEmailDomiciliadosPendSinCC.php
         que viene de cTesorero.php: exportarEmailDomiciliadosPend()
LLAMA: vistas/tesorero/formExportarEmailDomiciliadosPendSinCC.phps
													
OBSERVACIONES:
*****************************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';
			

/*************************** Inicio Cuerpo central ***************************/

echo $navegacion['cadNavegEnlaces'];
?>

<br /><br />
<h3 align="center">
    EXPORTAR EMAILS DE SOCIOS/AS PARA ENVIAR AVISOS DE PROXIMA ORDEN DE COBRO DE CUOTAS DOMICILIADAS
</h3>
<?php require_once './vistas/tesorero/formExportarEmailDomiciliadosPend.php'; ?>

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
    <br />		
    <?php
    if (isset($navegacion['anterior'])) {
        echo $navegacion['anterior'];
    }
    ?>				
    <br /><br />			
</div 
<!-- ********************  Fin Form botón anterior *********************** --> 
</div>
<!-- ****************************** Fin Cuerpo central  ******************** -->
</div>
<!-- ******************************* Fin vCuerpoExportarEmailDomiciliadosPendInc.php **************** -->