<?php
/***************************** Inicio vCuerpoDonacionConceptos.php ********************************
FICHERO: vCuerpoDonacionConceptos.php
VERSION: PHP 7.3.21

DESCRIPCION:
Se obtienen los datos de los "Concepto de Donación" existentes partir de la tabla "DONACIONCONCEPTOS" 
para después formar un formulario tabla-lista  "DONACIÓN CONCEPTOS", con las columnas:
"CONCEPTO,NOMBRECONCEPTO,FECHACREACIONCONCEPTO,OBSERVACIONES" y el botón "Añadir Nuevo Concepto de Donación"

LLAMADA: vistas/tesorero/vDonacionConceptosInc.php y previamente desde cTesorero.php:donacionConceptos()

LLAMA: vistas/tesorero/formDonacionConceptos.php e incluye plantillasGrales

OBSERVACIONES:
****************************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';

/************************** Inicio Cuerpo central ***************************/

if (isset($navegacion['cadNavegEnlaces'])) 
{
    echo $navegacion['cadNavegEnlaces'];
}

?>

<br /><br />
<h3 align="center">
    CONCEPTOS DE DONACIÓN
    <br /><br />
    <!-- ******************** Inicio informa fecha ***************** --> 
    <span class='textoAzulClaro8L'>
				 <?php echo "Fecha: "; echo date("d-m-Y"); ?> </span>
    <br /><br />
    <!-- ******************** Fin informa. fecha ***************** -->						
</h3>		 

<?php require_once './vistas/tesorero/formDonacionConceptos.php'; ?>

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
</div>		
<br /><br />		
<!-- ********************  Fin Form botón anterior *********************** --> 

</div>
<!-- ****************************** Fin Cuerpo central  ******************** -->
</div>
<!-- *********************** Fin vCuerpoDonacionConceptos.php ******* -->