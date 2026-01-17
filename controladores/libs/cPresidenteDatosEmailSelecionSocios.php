<?php
/*cPresidenteDatosEmailSelecionSocios.php*/
echo "<br><br>0 cAdmin:enviarEmailAttachFiles:cPresidenteDatosEmailSeleccionSocios:";print_r($cPresidenteDatosEmailSelecionSocios);	
if ($cPresidenteDatosEmailSelecionSocios['CODAGRUPACION'] !== 'NINGUNA' && $cPresidenteDatosEmailSelecionSocios['CODPAISDOM'] == 'NINGUNA' &&
    $cPresidenteDatosEmailSelecionSocios['CCAA'] == 'NINGUNA' && $cPresidenteDatosEmailSelecionSocios['CODPROV'] == 'NINGUNA')
{
 $tablasBusqueda = 'USUARIO,MIEMBRO,SOCIO';
 $camposBuscados = "MIEMBRO.EMAIL,UPPER(CONCAT(APE1,' ',IFNULL(APE2,''),', ',NOM)) as apeNom";
	$cadCondicionesBuscar = 	"WHERE USUARIO.CODUSER = MIEMBRO.CODUSER 
	                          AND MIEMBRO.CODUSER   = SOCIO.CODUSER 
																											AND SOCIO.CODAGRUPACION = '".$cPresidenteDatosEmailSelecionSocios['CODAGRUPACION'].
	                          "' AND SUBSTRING(USUARIO.ESTADO,1,4) = 'alta'
																											AND SUBSTRING(MIEMBRO.EMAIL, -9) != 'falta.com'";	
 $cadBuscarDatosEmailSelecionSocios = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";
}
elseif ($cPresidenteDatosEmailSelecionSocios['CODAGRUPACION'] == 'NINGUNA' && $cPresidenteDatosEmailSelecionSocios['CODPAISDOM'] !== 'NINGUNA' &&
    $cPresidenteDatosEmailSelecionSocios['CCAA'] == 'NINGUNA' && $cPresidenteDatosEmailSelecionSocios['CODPROV'] == 'NINGUNA')
{
 $tablasBusqueda = 'USUARIO,MIEMBRO';
 $camposBuscados = "MIEMBRO.EMAIL,UPPER(CONCAT(APE1,' ',IFNULL(APE2,''),', ',NOM)) as apeNom";
	$cadCondicionesBuscar = 	"WHERE USUARIO.CODUSER = MIEMBRO.CODUSER 
	                          AND MIEMBRO.CODPAISDOM = '".$cPresidenteDatosEmailSelecionSocios['CODPAISDOM'].
	                          "' AND SUBSTRING(USUARIO.ESTADO,1,4) = 'alta'
																											AND SUBSTRING(MIEMBRO.EMAIL, -9) != 'falta.com'";	
 $cadBuscarDatosEmailSelecionSocios ="SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";
}
elseif ($cPresidenteDatosEmailSelecionSocios['CODAGRUPACION'] == 'NINGUNA' && 
($cPresidenteDatosEmailSelecionSocios['CODPAISDOM']=='NINGUNA' || $cPresidenteDatosEmailSelecionSocios['CODPAISDOM']=='ES') &&
 $cPresidenteDatosEmailSelecionSocios['CCAA'] !== 'NINGUNA' && $cPresidenteDatosEmailSelecionSocios['CODPROV'] == 'NINGUNA')
{
 $tablasBusqueda = 'USUARIO,MIEMBRO,PROVINCIA';
 $camposBuscados = "MIEMBRO.EMAIL,UPPER(CONCAT(APE1,' ',IFNULL(APE2,''),', ',NOM)) as apeNom";
	$cadCondicionesBuscar = 	"WHERE USUARIO.CODUSER = MIEMBRO.CODUSER 
	                          AND MIEMBRO.CODPAISDOM = 'ES'
	                          AND MIEMBRO.CODPROV = PROVINCIA.CODPROV
																											AND PROVINCIA.CCAA = '".$cPresidenteDatosEmailSelecionSocios['CCAA'].	
	                          "' AND SUBSTRING(USUARIO.ESTADO,1,4) = 'alta'
																											AND SUBSTRING(MIEMBRO.EMAIL, -9)!= 'falta.com'";	
 $cadBuscarDatosEmailSelecionSocios ="SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";
}
elseif ($cPresidenteDatosEmailSelecionSocios['CODAGRUPACION']=='NINGUNA' && 
($cPresidenteDatosEmailSelecionSocios['CODPAISDOM']=='NINGUNA' || $cPresidenteDatosEmailSelecionSocios['CODPAISDOM']=='ES') && 
 $cPresidenteDatosEmailSelecionSocios['CCAA']=='NINGUNA' && $cPresidenteDatosEmailSelecionSocios['CODPROV'] !=='NINGUNA')
{
 $tablasBusqueda = 'USUARIO,MIEMBRO';
 $camposBuscados = "MIEMBRO.EMAIL,UPPER(CONCAT(APE1,' ',IFNULL(APE2,''),', ',NOM)) as apeNom";
	$cadCondicionesBuscar = 	"WHERE USUARIO.CODUSER = MIEMBRO.CODUSER 
                           AND MIEMBRO.CODPROV = '".$cPresidenteDatosEmailSelecionSocios['CODPROV'].	
	                          "' AND SUBSTRING(USUARIO.ESTADO,1,4) = 'alta'
																											AND SUBSTRING(MIEMBRO.EMAIL, -9) != 'falta.com'";	
 $cadBuscarDatosEmailSelecionSocios ="SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";
}		
else
{echo "<br><br>0 cAdmin:enviarEmailAttachFiles:cadBuscarDatosEmailSelecionSocios:ERROR:";print_r($cPresidenteDatosEmailSelecionSocios);	
$cPresidenteDatosEmailSelecionSocios['errorMensaje'] = 'ERROR: Debes elegir una opción y no más de una de las siguientes';
}
echo "<br><br>0 cAdmin:enviarEmailAttachFiles:cadBuscarDatosEmailSelecionSocios:";print_r($cadBuscarDatosEmailSelecionSocios);	


?>