<?php
/*-----------------------------------------------------------------------------
FICHERO: arrayEnviaRecibeUrl.php
PROYECTO: Europa Laica
VERSION: PHP 7.3.21

DESCRIPCION: Dos funciones para enviar y recibir un array en campo hidden en URL
LLAMADAS: 
controladorSocios.php:actualizarDatosSocio(),
cCoordinador.php:actualizarDatosSocioCoord(),
cPresidente.php:actualizarDatosSocioPres(),
cTesorero.php:actualizarDatosCuotaSocioTes(),
modelos/libs/prepMostrarActualizarCuotaSocio.php:prepMostrarActualizarCuotaSocio() 
------------------------------------------------------------------------------*/

/*---------------------------------------------------------------------------
Función: arrayEnviaUrl
Uso: en esta aplicación para enviar array en campo hidden como string serializado 
dentro de un url de un formulario.

Recibe: un array y lo prepara, convirtiendo en una cadena, para enviarlo por URL
dentro de un <form ... input hidden> 
<input name ="array" type="hidden" value="<?php echo $tmpString; ?>" />

Ejemplo con <href ....>
echo "<a href=\"recibir_array.php?array=$tmpString\">pasar array</a>"; 
---------------------------------------------------------------------------*/
function arrayEnviaUrl($array)//Como parámetro entrada yo solo lo utilizo para array
{ 
		$tmp = serialize($array);//serialize() convierte un valor (incluido un array) para almacenar como string serializado.
		
		$tmpString = urlencode($tmp); //Devuelve una cadena en la que todos los caracteres no-alfanuméricos excepto -_. han sido reemplazados con un signo de porcentaje (%)

		return $tmpString; 
} 
/*-------------------------------------------------------------------------
Función: arrayRecibeUrl
Uso: Para recibir un string serializado y volver al formato de array original 
     una cadena previamente generada con la función "arrayEnviaUrl()".
					
Recibe: un string $urlArray y lo prepara, convirtiendo en un array en nuestro caso 

LLamada: arrayRecibeUrl($urlArray)
----------------------------------------------------------------------------*/
function arrayRecibeUrl($urlArray) //$urlArray es mi caso es el string serializado que representa el array generado con "arrayEnviaUrl($array)"
{ 
		$tmp = stripslashes($urlArray); //Quita las barras de un string con comillas escapadas
		
		$tmp = urldecode($tmp);//Decodifica cualquier cifrado tipo %## en la cadena dada. Los símbolos ('+') son decodificados como el caracter espacio. 
		
		$tmpOriginal = unserialize($tmp);
		//Retorna a su valor original un string que fue convertido en string con serialize(). 
		//El valor retornado, y puede ser un boolean, integer, float, string, array u object. segun el origen
		
	return $tmpOriginal; 
} 
?>