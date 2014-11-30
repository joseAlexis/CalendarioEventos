<!DOCTYPE html5>
<html lang="es">
<head>
	<link rel="stylesheet" type="text/css" href="css/calendario.css">
	<link rel="stylesheet" type="text/css" href="css/general.css">
	<title>Calendario</title>
	<meta charset="UTF-8">
</head>

<body onload="limpiarTextArea()">
	<a href="l_calendario.php"><img src="imagenes/Calendar-Date-01-256.png" alt="Inicio" width="15%" height="32%"></a>
	<header>
		<h1 class="titulo">Calendario</h1>
	</header>	

	<?php
	//--------------------Valida Sesion------------------------//
	session_start();

	if (!isset($_SESSION['usuario'])) {
		header("Location: index.php");
	}
	//-------------------------------------------------------//
	?>

	<div class="navegacion">
		<nav>
			<br>
			<a href="index.php">Inicio Sesión</a> | 
			<a href="l_calendario.php">Calendario</a> |
			<a href="salir.php">Salir</a> 
		</nav>
		
	</div>
	
	<br>
	<br>

	<?php
		require_once("config.php");

		//Datos de la BD
		$mysqli = new mysqli($host, $user, $password, $database);

		if (mysqli_connect_errno()) {
		    printf("Conexión fallida: %s\n", mysqli_connect_error());
		    exit();
		}

		//cambiar de Mes o año
		if(isset($_GET['auxMes']) && isset($_GET['auxAnio']))
		{
			$auxMes = $_GET['auxMes'];
			$auxAnio = $_GET['auxAnio'];
			
			if($auxMes ==  13) {
				//Si el mes es igual a 13, tengo que pasar al año siguiente
				$mes = 1;				
				$anio = $auxAnio + 1;				
			}
			else if($auxMes == 0) {
				//Si el mes es igual a 0, tengo que pasar al año anterior
				$mes = 12;
				$anio = $auxAnio - 1;			
			}
			else{	
				//Sino simplemente pongo las mismas fechas
				$mes = $auxMes;
				$anio = $auxAnio;
			}
		
		}	
		else {
			//Sino Viene ningun mes o año calculo las actuales 
			$mes = date("n");
			$anio = date("Y");
		}

		//Se saca el mes pero en texto, basado en el mes y año
		$tMes = date("M", mktime(0,0,0,$mes,1,$anio));
		
		//Se saca el total de dias que tiene un mes
		$diasMes = date("t", mktime(0,0,0,$mes,1,$anio));	

		//Siempre se va a empezar desde la semana 1
		$semana = 1;
		
		//Cosntruyo el calendario en un array bidimensional
		for($i = 1; $i <= $diasMes; $i++) {
			
			//Calculo el dia en que empieza una fecha
			$diasSemana = date("N", mktime(0,0,0,$mes,$i,$anio));
			
			$calendario[$semana][$diasSemana] = $i;
			
			if($diasSemana == 7) {
				$semana ++;
			}
		}
		
		//Control de los meses anterior y siguiente
		$mesAnterior = $mes - 1; 
		$mesSiguiente = $mes + 1;
	?>
	
	<div id="centraTabla" class="enLinea">
		<table id="contenido">

			<tr>
				<th colspan="7">  <?php echo $anio; ?> </th>
			</tr>	
		
			<tr>
				<?php
				echo "<th colspan='7'> <a href='l_calendario.php?auxMes=$mesAnterior&auxAnio=$anio' class='ant'> <img src='imagenes/anterior.png'> </a>".$tMes." 
				<a href='l_calendario.php?auxMes=$mesSiguiente&auxAnio=$anio' class='sig'> <img src='imagenes/siguiente.png'> </a> </th>";
				// echo '<th colspan="7">'.$tMes.'</th>';
				?>
			</tr>
			
			<tr>
				<th> L </th>
				<th> M </th>
				<th> M </th>
				<th> J </th>
				<th> V </th>
				<th> S </th>
				<th> D </th>
			</tr>
			
			<?php
				foreach($calendario as $dias){
					echo "<tr>";
					
					for($i = 1; $i <= 7; $i++) {
						if($i == 6 || $i == 7){
							echo '<td class="finSemana">';
						}
						else {
							echo "<td>";
						}					

						if(isset($dias[$i])){
							
							//Dia con 0's
							$diaEnviar = date('d', mktime(0,0,0,$mes,$dias[$i], $anio));

							//Mes con 0's
							$mesEnviar = date('m', mktime(0,0,0,$mes,$dias[$i], $anio));

							$fecha = $anio."-".$mesEnviar."-".$diaEnviar;

							$query = "SELECT e.id, e.fecha FROM evento e JOIN evento_persona ep  ON e.id = ep.evento 
							JOIN persona p ON p.id = ep.persona JOIN usuario u ON u.id = p.id 
							WHERE p.id =".$_SESSION['usuario'].";";

							$recordatorio = false;

							if( $result = $mysqli->query($query) ) {

								while ($row = $result->fetch_assoc()) {

									if ($row['fecha'] == $fecha) {
										$idRecordatorio = $row['id'];
										echo "<a href='l_evento.php?fecha=$fecha&id=$idRecordatorio' class='dia'> 
										<img src='imagenes/recordatorio.png'> $dias[$i] </a>";						
										$recordatorio = true;
									} 
								}	
							}
							if ($recordatorio == false) {
								echo "<a href='l_evento.php?fecha=$fecha' class='dia'> $dias[$i] </a>";
							}
						}
						else {
							echo " - ";
						}
						
						echo "</td>";				
					}
					echo "</tr>";			
				}
			?>
		</table>
	</div>	

	<br>
	<br>

	<footer id="piePagina">
		<h3>
			<b>Examen #2</b>
		</h3>	

		<section>
			Jose Bolaños
		</section>

	</footer>
</body>
</html>
