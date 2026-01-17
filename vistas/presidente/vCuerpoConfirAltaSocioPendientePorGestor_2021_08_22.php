<?php
/************************* Inicio Inicio vCuerpoConfirAltaSocioPendientePorGestor.php ************
FICHERO: vCuerpoConfirAltaSocioPendientePorGestor.php
VERSION: PHP 7.3.21

DESCRIPCION: 
En esta función se confirma el alta de un socio (aún "PENDIENTE-CONFIRMACION" su alta por el mismo),
por un gestor autorizado (presidencia,vice,secretaría,teorería), normalmente después de contacto 
con el socio y este le solicita a un gestor que le confirme el alta (email, teléfono, etc.) 
y entonces el gestor le confirma el alta ocn esta función.

Al confirmar el alta, se insertarán en todas las tablas que correspondan los datos del socio, 
se eliminan físicamente de SOCIOSCONFIRMAR para que no salga también duplicado en mostrar pendientes,
y otras posibles búsquedas y USUARIO.ESTADO ='alta'
 
Se enviará un email al socio y también a secretaria, tesoreria, coordinador y presidencia para comunicar el alta.

LLAMADA: vistas/presidente/vConfirAltaSocioPendientePorGestor.php
y previamente desde cPresidente.php:confirmarAltaSocioPendientePorGestor()

LLAMA: vistas/presidente/formConfirAltaSocioPendientePorGestor.php e incluye plantillasGrales. 

OBSERVACIONES: 
*******************************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';

/**************************** Inicio Cuerpo central **************************/

if (isset($navegacion['cadNavegEnlaces'])) 
{
    echo $navegacion['cadNavegEnlaces'];
}
?>	
<br /><br />		

<h3 align="center">
    CONFIRMAR ALTA DE SOCIO/A PENDIENTE DE CONFIRMACIÓN
</h3>		

<?php require_once './vistas/presidente/formConfirAltaSocioPendientePorGestor.php'; ?>

</div>
<!--****************************** Fin Cuerpo central  ********************-->
</div>
<!--******************************* Fin vCuerpoConfirAltaSocioPendientePorGestor.php ***************-->