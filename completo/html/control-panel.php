
<?php
session_start();
require 'db.php';

// Verificar si el usuario está autenticado y es admin
if (!isset($_SESSION['usuario']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

// Mostrar mensajes de éxito/error
$mensaje = '';
if (isset($_SESSION['mensaje'])) {
    $mensaje = '<div class="alert success">' . $_SESSION['mensaje'] . '</div>';
    unset($_SESSION['mensaje']);
}
if (isset($_SESSION['error'])) {
    $mensaje = '<div class="alert error">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}

// Obtener los departamentos desde la base de datos
$stmt = $conn->prepare("SELECT id_departamento, nombre FROM departamentos");
$stmt->execute();
$departamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Si pasa las verificaciones, muestra el panel

?>
<html>
	<head>
		<title>Generic - Hyperspace by HTML5 UP</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="assets/css/main.css" />
		<noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
		<style>
		.alert {
			padding: 15px;
			margin-bottom: 20px;
			border: 1px solid transparent;
			border-radius: 4px;
			font-weight: bold;
		}
		.alert.success {
			color: #3c763d;
			background-color: #dff0d8;
			border-color: #d6e9c6;
		}
		.alert.error {
			color: #a94442;
			background-color: #f2dede;
			border-color: #ebccd1;
		}
	</style>
	</head>
	<body class="is-preload">

		<!-- Header -->
		<header id="header">
			<a href="index.php" class="title">BLEET</a>
			<nav>
				<ul>
					<li><a href="index.php">Inicio</a></li>
					<li><a href="subir-archivos.php">Subir archivo</a></li>
					<li><a href="control-panel.php" class="active">Panel de administración</a></li>
					<li><a href="mi_cuenta.php"><?php echo htmlspecialchars($_SESSION['usuario']); ?></a></li>
				</ul>
			</nav>
		</header>

		<!-- Wrapper -->
		<div id="wrapper">

			<!-- Main -->
			<section id="main" class="wrapper">
				
				<div class="inner">
					<?php echo $mensaje; ?>  <!-- Aquí aparecerán los mensajes -->
					<h1 class="major">Panel de administración</h1>
					<form action="agregar-usuario.php" method="post">
						<div class="row gtr-uniform">
							<div class="col-6 col-12-xsmall">
								<label for="username">Usuario</label>
								<input type="text" name="username" id="username" value="" placeholder="Usuario" required />
							</div>
							<div class="col-6 col-12-xsmall">
								<label for="password">Contraseña</label>
								<input type="password" name="password" id="password" value="" placeholder="Contraseña" required />
							</div>
							<div class="col-6 col-12-xsmall">
								<label for="email">Correo Electrónico</label>
								<input type="email" name="email" id="email" value="" placeholder="Correo Electrónico" required />
							</div>
							<div class="col-6 col-12-xsmall">
								<label for="departamento">Departamento</label>
								<select name="departamento" id="departamento" required>
									<option value="">Selecciona un departamento</option>
									<?php foreach ($departamentos as $dep): ?>
										<option value="<?= $dep['id_departamento'] ?>"><?= htmlspecialchars($dep['nombre']) ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="col-12">
								<label for="admin">Administrador</label>
								<select name="admin" id="admin">
									<option value="0">No</option>
									<option value="1">Sí</option>
								</select>
							</div>
							<div class="col-12">
								<ul class="actions">
									<li><input type="submit" value="Agregar Usuario" class="primary" /></li>
									<li><input type="reset" value="Limpiar" /></li>
								</ul>
							</div>
						</div>
					</form>
					<form action="agregar-departamento.php" method="post">
						<h1 class="major">Crear departamento</h1>
						<div class="row gtr-uniform">
							<div class="col-6 col-12-xsmall">
								<label for="dep_name">Nombre de departamento</label>
								<input type="text" name="dep_name" id="dep_name" value="" placeholder="Nombre de departamento" />
							</div>
							<div class="col-12">
								<ul class="actions">
									<li><input type="submit" value="Agregar Departamento" class="primary" /></li>
									<li><input type="reset" value="Limpiar" /></li>
								</ul>
							</div>
						</div>
					</form>
					<form action="eliminar-usuario.php" method="post">
					<h1 class="major">Eliminar Usuario</h1>
					<div class="row gtr-uniform">
						<div class="col-6 col-12-xsmall">
							<label for="user_id">Selecciona un usuario</label>
							<select name="user_id" id="user_id" required>
							<option value="">Selecciona un usuario</option>
							<?php
							try {
								$stmtUsers = $conn->prepare("SELECT usuario, nombre, apellidos FROM usuarios");
								$stmtUsers->execute();
								$usuarios = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);
								
								foreach ($usuarios as $user): 
									$displayName = '';
									if (!empty($user['nombre']) || !empty($user['apellidos'])) {
										$displayName = trim($user['nombre'] . ' ' . $user['apellidos']);
									} else {
										$displayName = $user['usuario'];
									}
									?>
									<option value="<?= htmlspecialchars($user['usuario']) ?>">
										<?= htmlspecialchars($displayName) ?>
									</option>
								<?php endforeach;
							} catch (PDOException $e) {
								echo '<option value="" disabled>Error al cargar usuarios: ' . htmlspecialchars($e->getMessage()) . '</option>';
							}
							?>
						</select>
						</div>
						<div class="col-12">
							<ul class="actions">
								<li>
									<input type="submit" value="Eliminar Usuario" class="primary" 
									onclick="return confirm('¿Estás seguro de eliminar este usuario?');" />
								</li>
							</ul>
						</div>
					</div>
				</form>
				<form action="eliminar-departamento.php" method="post">
					<h1 class="major">Eliminar Departamento</h1>
					<div class="row gtr-uniform">
						<div class="col-6 col-12-xsmall">
							<label for="dep_id">Selecciona un departamento</label>
							<select name="dep_id" id="dep_id" required>
								<option value="">Selecciona un departamento</option>
								<?php foreach ($departamentos as $dep): ?>
									<option value="<?= $dep['id_departamento'] ?>"><?= htmlspecialchars($dep['nombre']) ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="col-12">
							<ul class="actions">
								<li><input type="submit" value="Eliminar Departamento" class="primary" onclick="return confirm('¿Estás seguro de eliminar este departamento?');" /></li>
							</ul>
						</div>
					</div>
				</form>
				</div>
			</section>
		</div>

		<!-- Footer -->
		<footer id="footer" class="wrapper alt">
			<div class="inner">
				<ul class="menu">
					<li>&copy; Untitled. All rights reserved.</li><li>Design: <a href="http://html5up.net">HTML5 UP</a></li>
				</ul>
			</div>
		</footer>

		<!-- Scripts -->
		<script src="assets/js/jquery.min.js"></script>
		<script src="assets/js/jquery.scrollex.min.js"></script>
		<script src="assets/js/jquery.scrolly.min.js"></script>
		<script src="assets/js/browser.min.js"></script>
		<script src="assets/js/breakpoints.min.js"></script>
		<script src="assets/js/util.js"></script>
		<script src="assets/js/main.js"></script>
		
	</body>
</html>