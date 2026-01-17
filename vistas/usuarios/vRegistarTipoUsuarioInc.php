<?php
/*-----------------------------------------------------------------------------
FICHERO: vRegistarTipoUsuarioInc.php
VERSION: PHP 5.2.3
DESCRIPCION: incluye las plantillas
OBSERVACIONES:Llamado desde controladorUsuarios.php Función registrarTipoUsuario
------------------------------------------------------------------------------*/
//function vRegistarTipoUsuarioInc($texto1,$error,$parValorComboPaisMiembro,$parValorComboPaisDomicilio,$parValorComboProvDomicilio)
function vRegistarTipoUsuarioInc($texto1,$error)
{ //echo "<br><br>.....> Dentro de vRegistarUsuarioInc";print_r($parValorComboProvDomicilio);
  require_once './vistas/plantillasGrales/vCabeceraGral.php';

  require_once './vistas/usuarios/vCuerpoRegistrarTipoUsuario.php';
//	vCuerpoRegistrarUsuario("",$error,$parValorDescrip);

  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>