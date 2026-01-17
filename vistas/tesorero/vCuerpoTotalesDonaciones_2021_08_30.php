<?php
/***************************** Inicio vCuerpoTotalesDonaciones.php ********************************
FICHERO: vCuerpoTotalesDonaciones.php
VERSION: PHP 7.3.21

DESCRIPCION: Contiene el formulario que muesta la tabla "TOTALES DONACIONES". Decreciente por años. 
Entre otros campos incluye: 
Nº total donantes, 	Tipo de donante (socios, donantes identificados, anónimos) , Modo de ingreso, 
Gastos donación, Total donaciones €,  

LLAMADA: vistas/tesorero/vTotalesDonacionesInc.php y previamente desde el formulario:
vMostrarDonacionesInc.php ( Botón "Totales donaciones" )

LLAMa: vistas/tesorero/formTotalesDonaciones.php
e incluye plantillasGrales

OBSERVACIONES:
****************************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';

/************************** Inicio Cuerpo central ***************************/

echo $totalesAniosPagosDonaciones['navegacion']['cadNavegEnlaces'];

?>

<br /><br />
<h3 align="center">
    TOTALES DONACIONES POR AÑOS
    <br /><br />
    <!-- ******************** Inicio informa fecha ***************** --> 
    <span class='textoAzulClaro8L'>
				 <?php echo "Fecha: "; echo date("d-m-Y"); ?> </span>
    <br /><br />
    <!-- ******************** Fin informa. fecha ***************** -->						
</h3>		 

<?php require_once './vistas/tesorero/formTotalesDonaciones.php'; ?>

<!-- ********************  Inicio form botón anterior******************** --> 		

<div align="center">
				<br />	
				<?php
				if (isset($totalesAniosPagosDonaciones['navegacion']['anterior'])) 
				{
								echo $totalesAniosPagosDonaciones['navegacion']['anterior'];
				}
				?>	
				<br />
</div>		
<br /><br />			

<!-- ********************  Fin Form botón anterior *********************** --> 
</div>
<!-- ****************************** Fin Cuerpo central  ******************** -->
</div>
<!-- *********************** Fin vCuerpoTotalesDonaciones.php ******* -->