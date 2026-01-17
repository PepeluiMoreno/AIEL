<?php
/******************* Inicio vCuerpoAnotarIngresoDonacionSiEncontrado.php ***************************
FICHERO:  vCuerpoAnotarIngresoDonacionSiEncontrado.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Formulario para el anotar el ingreso de una donación realizada por donante socio o 
donante ya registrado previamente y buscado por Nº documento o por su email. 

Se escriben los datos personales existentes y se añaden los datos de la donación.
De los con datos previos ya existentes estos (sexo, APE1,APE2) se dejarán sin modificar (readonly)
y los demás se podrán introducir o modificar. 

LLAMADA: vistas/tesorero/vAnotarIngresoDonacionSiEncontradoInc.php
y previamente desde cTesorero.php:anotarIngresoDonacionMenu o cTesorero.php:anotarIngresoDonacion()

LLAMA: vistas/tesorero/formAnotarIngresoDonacionSiEncontrado.php
e incluye plantillasGrales

OBSERVACIONES:

2018_10_03: cambio CONCEPTO que era textarea, para poner un selector que incluya "GENERAL", 
"COSTAS_MEDALLA_VIRGEN_MERITO_POLICIAL", "Otros", acaso mas adelente añadir una nueva tabla CONCEPTO_DONACION.
****************************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';
?>
<!-- ************************ Inicio Cuerpo central ************************ -->
<?php
if (isset($navegacion['cadNavegEnlaces'])) {
    echo $navegacion['cadNavegEnlaces'];
}
?>	
<br /><br />
<h3 align="center">	
    ANOTAR DONACIONES 	
</h3>	 

<?php require_once './vistas/tesorero/formAnotarIngresoDonacionSiEncontrado.php'; ?>

</div>
<!-- ****************************** Fin Cuerpo central  ******************** -->
</div>
<!-- ******************************* Fin Cuerpo vCuerpoAnotarIngresoDonacionSiEncontrado.php*********** -->