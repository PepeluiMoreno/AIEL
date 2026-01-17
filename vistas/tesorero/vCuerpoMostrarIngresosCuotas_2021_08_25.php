<?php
/****************************** Inicio vCuerpoMostrarIngresosCuotas **************************************
FICHERO: vCuerpoMostrarIngresosCuotas.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION:
Se forma y muestra una tabla-lista páginada "ESTADO CUOTAS SOCIOS" con el resumen de las cuotas pagadas
y pendientes de los socios. 
Decreciente por años (5 últimos), y creciente		por nombre en la selección por años.  
Incluye un botón para elegir por AÑO (por defecto el actual), ESTADO CUOTA (por defecto el Todos) 
y AGRUPACION, y otro botón para elegir por APE1, APE2.

En la parte inferior se muestran número de páginas para poder ir directamente a un página, 
anterior, siguiente, primera, última.
Además de de mostrar en cada fila datos sobre las cuotas de los socios:	Ingreso, Gasto cobro cuota, 
Fecha ingreso, 	Estado cuota, 	etc. al final para cada fila, hay iconos con links para acciones sobre
el socio:Ver,	Pago cuota, Actualiza cuota, Baja socio 	

En el formulario-tabla, en la parte superior también está el botón  "Totales pagos cuotas por años" 
que dirige a cTesorero.php:mostrarTotalesCuotas()

LLAMADA: vistas/tesorero/vMostrarIngresosCuotasInc.php, a su vez desde "cTesorero.php: mostrarIngresosCuotas()"
y menú izdo del rol tesorería "Cuotas socios/as"

LLAMA: vistas/tesorero/formMostrarIngresosCuotas.php
e incluye plantillasGrales

OBSERVACIONES: 
/********************************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';
		
/************************* Inicio Cuerpo central *************************/

echo $resCuotasSocios['navegacion']['cadNavegEnlaces'];

?>

<br /><br />
<h3 align="center">
    ESTADO CUOTAS SOCIOS/AS	(incluye bajas, pero sin datos personales)  	
</h3>		 

<?php require_once './vistas/tesorero/formMostrarIngresosCuotas.php'; ?>

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
    <br /><br />			
</div 
<!-- ********************  Fin Form botón anterior *********************** --> 
</div>
<!-- ****************************** Fin Cuerpo central  ******************** -->
</div>
<!-- *********************** Fin vCuerpoMostrarIngresosCuotas*********** -->