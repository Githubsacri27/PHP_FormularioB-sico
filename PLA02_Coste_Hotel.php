<?php
const PRECIO_POR_NOCHE = 60;
const PRECIO_ALQUILER_COCHE = 40;
const DESCUENTO20 = 20;
const DESCUENTO50 = 50;

// Función para calcular el precio del hotel
function calcularPrecioHotel($noches)
{
	if ($noches < 1) {
		throw new Exception('El número de noches debe ser mayor o igual a 1.');
	}
	return $noches * PRECIO_POR_NOCHE;
}

// Función para calcular el precio del viaje en avión
function calcularPrecioAvion($ciudad)
{
	$ciudades = array(
		'Madrid' => 150,
		'Paris' => 250,
		'Los Angeles' => 450,
		'Roma' => 200
	);
	if (!array_key_exists($ciudad, $ciudades)) {
		throw new Exception('Ciudad no válida. Selecciona una ciudad de destino.');
	}
	return $ciudades[$ciudad];
}
//Funcion aplicar decuento alquiler coche
function aplicarDescuentoAlquilerCoche($diasCoche)
{
	if ($diasCoche < 1) {
		throw new Exception('El número de días de alquiler debe ser mayor o igual a 1.');
	}
	if ($diasCoche >= 3 && $diasCoche <= 6) {
		return $diasCoche * PRECIO_ALQUILER_COCHE - DESCUENTO20;
	} elseif ($diasCoche >= 7) {
		return $diasCoche * PRECIO_ALQUILER_COCHE - DESCUENTO50;
	} else {
		return $diasCoche * PRECIO_ALQUILER_COCHE;
	}
}
// Se declara un array para capturar todos los errores dentro de la comprobación del envío
$mensajeError = array();
// Comprobación de envío de formulario
if (isset($_POST['enviar'])) {
	
	try {
		// Recoger datos del formulario
		$noches = isset($_POST['noches']) ? (int)$_POST['noches'] : 0;
		$ciudad = isset($_POST['ciudad']) ? trim($_POST['ciudad']) : '';
		$diasCoche = isset($_POST['coche']) ? (int)$_POST['coche'] : 0;

		// Validar los campos del formulario
		if ($noches < 1) {
			$mensajeError[] = 'El número de noches debe ser mayor o igual a 1.';
		}

		if (empty($ciudad)) {
			$mensajeError[] = 'Debes seleccionar una ciudad de destino.';
		} elseif (!in_array($ciudad, array('Madrid', 'Paris', 'Los Angeles', 'Roma'))) {
			$mensajeError[] = 'Ciudad no válida. Selecciona una ciudad de destino.';
		}

		if ($diasCoche < 0) {
			$mensajeError[] = 'El número de días de alquiler debe ser mayor o igual a 1.';
		}

		// Si no hay errores, realizar cálculos
		if (empty($mensajeError)) {
			// Cálculo de precios
			$precioHotel = calcularPrecioHotel($noches);
			$precioAvion = calcularPrecioAvion($ciudad);
			$precioCoche = aplicarDescuentoAlquilerCoche($diasCoche);
			$descuentoCoche = $diasCoche * PRECIO_ALQUILER_COCHE - $precioCoche;

			$precioTotal = $precioHotel + $precioAvion + $precioCoche;
			if (!empty($mensajeError)) {
				throw new Exception($mensajeError[]);
			}
		}
	} catch (Exception $e) {
		// En caso de error, se guarda el mensaje de error para mostrarlo después
		$mensajeError[] = $e->getMessage();
	}
}
// Para borrar los datos del formulario
if (isset($_POST['limpiar'])) {
	$_POST = array();
	$noches = '';
	$ciudad = '';
	$diasCoche = '';
	$precioTotal = '';
}
?>
<?php
if (isset($_POST['limpiar'])) {
	$_POST = array();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<title>PLA02</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="css/estilos.css">
</head>

<body>
	<main>
		<h1 class='centrar'>PLA02: COSTE HOTEL</h1>
		<br>
		<form method="post" action="#">
			<div class="row mb-3">
				<label for="noches" class="col-sm-3 col-form-label">Número de noches:</label>
				<div class="col-sm-9">
					<input type="number" class="form-control" name="noches" id="noches" value="<?php echo $noches; ?>">
				</div>
			</div>
			<div class="row mb-3">
				<label for="ciudad" class="col-sm-3 col-form-label">Destino:</label>
				<div class="col-sm-9">
					<select class="form-select" name='ciudad'>
						<option selected value=''>Selecciona un destino</option>
						<option value="Madrid">Madrid</option>
						<option value="Paris">Paris</option>
						<option value="Los Angeles">Los Angeles</option>
						<option value="Roma">Roma</option>
					</select>
				</div>
			</div>
			<div class="row mb-3">
				<label for="coche" class="col-sm-3 col-form-label">Días alquiler coche:</label>
				<div class="col-sm-9">
					<input type="number" class="form-control" name="coche" id="coche" value="<?php echo isset($_POST['coche']) ? $_POST['coche'] : null; ?>">
				</div>
			</div>
			<label class="col-sm-3 col-form-label"></label>
			<button type="submit" class="btn btn-primary" name='enviar'>Enviar datos</button>
			<button type="sumit" class="btn btn-primary" name='limpiar' value="Limpiar">limpiar </button>
			<br><br>
			<div class="row mb-3">
				<label class="col-sm-3 col-form-label">Coste total: </label>
				<div class="col-sm-9">
					<input type="text" class="form-control" name="total" id="total" disabled value="<?php echo isset($precioTotal) ? $precioTotal : null; ?>">
				</div>
				<!-- Mostrar el mensaje si hay descuento -->
				<div class="row mb-3">
					<div class="col-sm-9 offset-sm-3">
						<?php if (isset($descuentoCoche)) : ?>
							<?php if ($descuentoCoche === DESCUENTO20) : ?>
								<p>Se ha aplicado un descuento de 20 euros en el alquiler del coche.</p>
							<?php elseif ($descuentoCoche === DESCUENTO50) : ?>
								<p>Se ha aplicado un descuento de 50 euros en el alquiler del coche.</p>
							<?php else : ?>
								<p>No se ha aplicado ningún descuento en el alquiler del coche.</p>
							<?php endif; ?>
						<?php endif; ?>
					</div>
				</div>
			</div><br>
			<span class='errores'>
				<?php
				if (!empty($mensajeError)) {
					echo "<div class='error'>";
					echo "<p>Por favor, verifica los siguientes errores:</p>";
					echo "<ul>";
					foreach ($mensajeError as $error) {
						echo "<li>$error</li>";
					}
					echo "</ul>";
					echo "</div>";
				}
				?>
			</span>
		</form>
	</main>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>

</html>