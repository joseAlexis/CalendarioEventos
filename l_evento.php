<!DOCTYPE html>
<html lang="es">
<head>
	<link rel="stylesheet" type="text/css" href="css/evento.css">
	<link rel="stylesheet" type="text/css" href="css/general.css">
	<script type="text/javascript" src="js/script.js"></script>
	<meta charset="UTF-8">
	<title>Evento</title>
</head>

<body>
	<a href="l_calendario.php"><img src="imagenes/Calendar-Date-01-256.png" alt="Inicio" width="15%" height="20%"></a>
	<header>
		<h1 class="titulo">Calendario</h1>
	</header>	

	<div class="navegacion">
		<nav>
			<br>
			<a href="index.php">Inicio Sesión</a> | 
			<a href="l_calendario.php">Calendario</a> |
			<a href="salir.php">Salir</a>
		</nav>		
	</div>

	<?php
	//Valido si la sesion existe
	session_start();

	if (!isset($_SESSION['usuario'])) {
		header("Location: index.php");
	}

	//Datos de la BD
	require_once("config.php");

	$mysqli = new mysqli($host, $user, $password, $database);

	if (mysqli_connect_errno()) {
	    printf("Conexión fallida: %s\n", mysqli_connect_error());
	    exit();
	}

	//Definicion de variables 
	$id = "";
	$fecha = "";
	$inicio = "";
	$fin = "";
	$detalle = "";

	$error = false;
	$textoError = "";

	//Si viene un id cargo los datos de ese evento
	if (isset($_GET['id']) ) {
		$id = $_GET['id'];

		if($id != ""){

			$query = "SELECT * FROM evento WHERE id=".$id.";";

			if( $result = $mysqli->query($query) ) {

				while ($row = $result->fetch_assoc()) {

					$fecha = $row['fecha'];
					$inicio = $row['horaInicio'];
					$fin = $row['horaFin'];
					$detalle = $row['Detalle'];
				}	
			}

			$query = "SELECT p.nombre FROM persona p JOIN evento_persona ep ON p.id = ep.persona 
			JOIN evento e ON e.id = ep.evento WHERE e.id =".$id.";";

			if( $result = $mysqli->query($query) ) {

				while ($row = $result->fetch_assoc()) {

					$data[] = $row['nombre'];
				}	
			}
		}
	}

	//si le dio clic al boton Guardar
	if(isset($_POST['btnGuardar']))
	{
		if ( isset($_GET['fecha']) && isset($_POST['inicio']) &&  isset($_POST['fin']) 
			&& isset($_POST['detalle']) && isset($_POST['contactos']) ) 
		{	
			$fecha = $_GET['fecha'];
			$inicio = $_POST['inicio'];
			$fin = $_POST['fin'];
			$detalle = $_POST['detalle'];

			if ($fecha != "" && $inicio != "" && $fin != "" && $detalle != "") {
				$query = "INSERT INTO evento (fecha, horaInicio, horaFin, detalle) 
						VALUES ('".$fecha."', '".$inicio."', '".$fin."', '".$detalle."')";			
				$sentencia = $mysqli->prepare($query);
				$sentencia->execute();

				//Obtengo el id de la ultima consulta generada
				$id = $mysqli->insert_id;
			

				for ($i=0; $i < count($_POST['contactos']); $i++) { 
					$query = "INSERT INTO evento_persona (persona, evento) VALUES (".$_POST['contactos'][$i].", ".$id.")";			
					$sentencia = $mysqli->prepare($query);
					$sentencia->execute();			
				}
			}
			else{
				$error = true;
				$textoError = '<label class="error">Error! Faltan algunos datos importantes</label>';
			}
		}
		else{
			$error = true;
			$textoError = '<label class="error">Error! Faltan algunos datos importantes</label>';
		}
	}

	//Le dio clic al boton Modificar
	if (isset($_POST['btnModificar'])) {
		// echo "entre al modificar";
		if (isset($_POST['txtId']) && isset($_GET['fecha']) && isset($_POST['inicio']) 
			&&  isset($_POST['fin']) && isset($_POST['detalle']) && isset($_POST['contactos']) ) 
		{			
			$id = $_POST['txtId'];
			$fecha = $_GET['fecha'];
			$inicio = $_POST['inicio'];
			$fin = $_POST['fin'];
			$detalle = $_POST['detalle'];

			if ($id != "" && $fecha != "" && $inicio != "" && $fin != "" && $detalle != "") {

				//Actualizo los valores de la tabla evento
				$query = "UPDATE evento SET fecha = '".$fecha."', horaInicio = '".$inicio."', horaFin = '".$fin."', 
				Detalle = '".$detalle."' WHERE id=".$id.";";			
				$sentencia = $mysqli->prepare($query);
				$sentencia->execute();	

				//Extraigo las personas que estan el el evento a actualizar
				$query = "SELECT * FROM evento_persona WHERE evento = ".$id.";";
				if( $result = $mysqli->query($query) ) {

					while ($row = $result->fetch_assoc()) {	
				
						//Elimino los registros las personas que participan 
						$query = "DELETE FROM evento_persona WHERE evento = ".$row['evento'].";";
						$sentencia = $mysqli->prepare($query);
						$sentencia->execute();
					}	
				}

				//Inserto los nuevos contactos en evento_persona
				for ($i=0; $i < count($_POST['contactos']); $i++) { 
					$query = "INSERT INTO evento_persona (persona, evento) VALUES (".$_POST['contactos'][$i].", ".$id.")";			
					$sentencia = $mysqli->prepare($query);
					$sentencia->execute();			
				}

				//Se redirige a el calendario
				header("Location: l_calendario.php");
			}
			else{
				$error = true;
				$textoError = '<label class="error">Error! Faltan algunos datos importantes</label>';
			}
		}
		else{
			$error = true;
			$textoError = '<label class="error">Error! Faltan algunos datos importantes</label>';
		}
	}

	//Le dio clic al boton Eliminar
	if (isset($_POST['btnEliminar'])) {
		
		if (isset($_POST['txtId'])) {
			
			$id = $_POST['txtId'];

			if ($id != "") {
				$query = "DELETE FROM evento WHERE id = ".$id.";";

				$sentencia = $mysqli->prepare($query);
				$sentencia->execute();

				$id = "";
				$fecha = "";
				$inicio = "";
				$fin = "";
				$detalle = "";

				header("Location: l_calendario.php");
			}
			else{
				$error = true;
				$textoError = '<label class="error">Error! Faltan algunos datos importantes</label>';
			}
		}
		else{
			$error = true;
			$textoError = '<label class="error">Error! Faltan algunos datos importantes</label>';
		}
	}

	//Si se le dio click a insertar o eliminar, se hace una consuta con los usuarios registrados en 
	//ese evento
	if ( isset($_POST['btnModificar']) || isset($_POST['btnGuardar']) ) {
		//Cargo los contactos del evento actual
		$query = "SELECT p.nombre FROM persona p JOIN evento_persona ep ON p.id = ep.persona 
			JOIN evento e ON e.id = ep.evento WHERE e.id =".$id.";";

		if( $result = $mysqli->query($query) ) {

			while ($row = $result->fetch_assoc()) {

				$data[] = $row['nombre'];
			}	
		}
	}
	?>
	
	<div id="centraTabla">
		<form method="POST" action="l_evento.php" id="frmEvento">
			<table id="contenido">
				<?php
				if (isset($_GET['fecha'])) {
					$fecha = $_GET['fecha'];

					if($fecha != ""){
						?>
						<tr>
							<td colspan="2">
								<?php 
									if ($error == true) {
										echo $textoError;
									}
								?>
							</td>	
						</tr>

						<tr>
							<th colspan="2">Evento</th>
						</tr>

						<tr>
							<td> <label>Día: </label> </td>
							<td> <input type="date" name="fecha" id="idFecha" value="<?php echo $fecha; ?>" disabled> </td>
						</tr>	
						
						<tr>
							<td>Hora de inicio: </td>	
							<td> <input type="time" name="inicio" value="<?php echo $inicio; ?>"> </td>
						</tr>
						
						<tr>
							<td>Hora de finalización: </td>
							<td> <input type="time" name="fin" value="<?php echo $fin; ?>"> </td>
						</tr>

						<tr>
							<td> <label>Detalle: </label> </td>
							<td> <textarea name="detalle" id="idDetalle"> <?php echo $detalle; ?> </textarea> </td>
						</tr>

						<tr>
							<td>
								<label>Contactos</label>
							</td>
							
							<td>
							<?php
								$query = "SELECT * FROM persona";

								if($id != ""){

									if( $result = $mysqli->query($query) ) {

										while ($row = $result->fetch_assoc()) {
											$auxNombre = 0; 
											for ($i=0; $i < count($data); $i++) { 

												if ($row['nombre'] == $data[$i]) {
													$auxNombre = $row['nombre'];
												}
											}

											if ($auxNombre != "") {
												echo '<input type="checkbox" name="contactos[]" value="'.$row['id'].'" checked>'.$row['nombre'].'<br>';		
											}
											else{
												echo '<input type="checkbox" name="contactos[]" value="'.$row['id'].'">'.$row['nombre'].'<br>';
											}
										}	
									}
								}
								else{
									if( $result = $mysqli->query($query) ) {

										while ($row = $result->fetch_assoc()) {
											if($row['id'] == $_SESSION['usuario']){
												echo '<input type="checkbox" name="contactos[]" value="'.$row['id'].'" checked>'.$row['nombre'].'<br>';
											}
											else{
												echo '<input type="checkbox" name="contactos[]" value="'.$row['id'].'">'.$row['nombre'].'<br>';
											}
										}
									}
								}

							?>
							</td>
						</tr>

						<tr>
							<td colspan="2"> <input type="hidden" id="idEvento" name="txtId" value="<?php echo $id; ?>"> </td>	
						</tr>

						<tr>
							<td colspan="2">
							<?php
								if($id != ""){
									echo '<input type="submit" name="btnModificar" value="Modificar" class="boton" onclick="actualizarEvento()">&nbsp';
									echo '<input type="submit" name="btnEliminar" value="Eliminar" class="boton">';
								}
								else{
									echo '<input type="submit" name="btnGuardar" value="Guardar" onclick="agregarFecha()" class="boton" id="idFecha">';
								}
							?>
							</td>
						</tr>

						<?php
					}
					else{
						echo '<tr> <td colspan="2"> <label class="error"> Error! Fecha Incorrecta! </label> </td></tr>';
					}
				}
				else{
					echo '<tr> <td colspan="2"> <label class="error"> Error! Fecha Incorrecta! </label> </td></tr>';
				}
				?>
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
