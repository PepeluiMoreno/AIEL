<?php
/******************* Inicio vCuerpoAnotarIngresoDonacionSiEncontrado.php ***************************
FICHERO:  vCuerpoAnotarIngresoDonacionSiEncontrado.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Formulario para el anotar el ingreso de una donación realizada por donante socio o 
donante ya registrado previamente y buscadoNúmero de docuomento (NIF, NIE, pasaporte, otros), o EMAIL, 

Además de los datos personales recuperados de las tablas MIEMBRO o DONACION,  y se añaden los datos de la donación.
De losrecuperados estos (sexo, APE1,APE2) se dejarán sin modificar (readonly) y los demás 
se podrán introducir o modificar. 

LLAMADA: vistas/tesorero/vAnotarIngresoDonacionSiEncontradoInc.php
y previamente desde cTesorero.php:comprobarDonantePrevio_Socio(), o anotarIngresoDonacion()

LLAMA: vistas/tesorero/formAnotarIngresoDonacionSiEncontrado.php
e incluye plantillasGrales

OBSERVACIONES:
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
<!-- ****************************** Fin Cuerpo vCuerpoAnotarIngresoDonacionSiEncontrado.php *********** -->