<?php
/***************************** Inicio vCuerpoExportarEmailDomiciliadosPendSinCC.php *****************
FICHERO: vCuerpoExportarEmailDomiciliadosPendSinCC.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Formulario de selección de campos para buscar Emails y otros datos de todos 
las cuotas de los socios y exportar los emails en forma de lista separados por (;)
a un fichero ".txt" para copiar y pegar en el correo de NODO50 (tesoreria@europalica.org), 
y enviar un email a los socios de la lista, con texto libre para avisar a los socios 
que aún "no han abonado la cuota" que están de alta en el momento actual y según
la siguiente selección:

- No tiene cuenta bancaria domiciliada, - Tiene cuenta bancaria de países NO SEPA (o no es IBAN, ya 
  no se permite CUENTAS NO IBAN, este caso devolverá 0 socios), - Cuenta bancaria de países SEPA (distintos
 	de España), junto con "Ordenar cobro banco = NO" (por falta de BIC necesario para otros países SEPA, 
		por eso se envía este email de aviso no pagado)
- FechaAltaExentosPago		
- Agrupaciones seleccionadas

LLAMADA: cTesorero.php:vExportarEmailDomiciliadosPendSinCCInc()
LLAMA: vistas/tesorero/formExportarEmailDomiciliadosPendSinCC.php
													
OBSERVACIONES:
*****************************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';

/************************** Inicio Cuerpo central ***************************/

echo $navegacion['cadNavegEnlaces'];
?>

<br /><br />
<h3 align="center">
				EXPORTAR EMAILS DE SOCIOS/AS SIN DOMICILIACIÓN BANCARIA PARA INFORMAR DE CUOTA ANUAL AÚN NO PAGADA
</h3>
<?php require_once './vistas/tesorero/formExportarEmailDomiciliadosPendSinCC.php'; ?>

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
<!-- ******************************* Fin vCuerpoAEB19Cuotas.php **************** -->