<!DOCTYPE html>
<html lang="es">
<head>
	<link rel="stylesheet" type="text/css" href="css/index.css">
	<link rel="stylesheet" type="text/css" href="css/general.css">
	<title>LogIn</title>
	<meta charset="UTF-8">
</head>
<body>

<a href="l_calendario.php"><img src="imagenes/Calendar-Date-01-256.png" alt="Inicio" width="15%" height="20%"></a>
<header>
	<h1 class="titulo">Calendario</h1>
</header>		

<?php
//Llama al archivo config.php con los datos de la conexion 
require_once("config.php");
$mysqli = new mysqli($host, $user, $password, $database);

//Verifica si se puede realizar la conexion
if (mysqli_connect_errno()) {
    printf("Conexión fallida: %s\n", mysqli_connect_error());
    exit();
}

//Declaracion de variables
$usuario = "";
$clave = "";
$user = "";
$pass = "";
$error = false;
$textoError = "";

//Valida si viene un usuario por POST
if (isset($_POST['txtUsuario'])) {
	
	//guarda el contenido de ese usuario
	$usuario = $_POST['txtUsuario'];

	//verifica si su contenido no esta vacio
	if ($usuario != "") {

		//Valida si viene una clave por POST
		if (isset($_POST['txtClave'])) {

			//guarda el contenido de esa clave
			$clave = $_POST['txtClave'];

			//verifica si su contenido no esta vacio
			if ($clave != "") {

				//Busca en la BD algun registro que coincida con el usuario y contrasena digitados
				$query = "SELECT * FROM usuario WHERE id='".$usuario."' and clave = '".md5($clave)."';";

				//Verifica si se puede realizar la consulta
				if( $result = $mysqli->query($query) ) {

					//Crear un arreglo asociativo con los resultados de la consulta
					while ($row = $result->fetch_assoc()) {

						//Guarda los datos en nuevas variables
						$user = $row['id'];
						$pass = $row['clave'];
					}	
				}
				
				//valida si el usuario y contrasena son iguales a los digitados
				if ($usuario == $user && md5($clave) == $pass) {
					//Muestra un mensaje de correcta autenticacion
					echo "Correcto!";

					//crea uan variable de sesion 
					session_start();

					//Almacena el id del usuario
					$_SESSION['usuario'] = $usuario;

					//Se redirige al calendario
					header("Location: l_calendario.php?");
				}
				else{
					$textoError = "Error! Usuario o clave incrrectos...";
					$error = true;
				}
				
				//Cierra la conexion
				$mysqli->close();
			}
			else{
				$textoError = "Error! Debe digitar la clave...";
				$error = true;
			}
		}
	}
	else{
		$textoError = "Error! Debe digitar un nombre de usuario...";
		$error = true;
	}
}
?>

<div id="centraTabla">
	<form method="POST" action="index.php" >
		<table id="contenido">
			<tr>
				<td colspan="2">
					<?php  
						if($error == true){
							echo '<label class="error">'.$textoError.'</label>';
						}
					?>
				</td>
			</tr>

			<tr>
				<th colspan="2">Log In</th>
			</tr>

			<tr>
				<td>Usuario: </td>
				<td> <input type="text" name="txtUsuario" id="idUsuario"> </td>
			</tr>

			<tr>
				<td>Clave: </td>
				<td> <input type="password" name="txtClave" id="idClave"> </td>
			</tr>

			<tr>
				<td colspan="2">
					<input type="submit" name="btnIngresar" value="Ingresar" class="boton">
				</td>
			</tr>
		</table>
	</form>
</div>

<br>
<br>

<footer id="piePagina">
	<h3>
		<b>Examen #2</b>
	</h3>	

	<section>
		Jose Bolaños <br>
		Jose Avila
	</section>

</footer>

</body>
</html>
<?php
?>