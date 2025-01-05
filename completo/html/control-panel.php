<html>
	<head>
		<title>Generic - Hyperspace by HTML5 UP</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="assets/css/main.css" />
		<noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
	</head>
	<body class="is-preload">

		<!-- Header -->
		<header id="header">
			<a href="index.php" class="title">BLEET</a>
			<nav>
				<ul>
					<li><a href="index.php">Inicio</a></li>
					<li><a href="generic.html">Subir archivo</a></li>
					<li><a href="generic.html" class="active">Panel de administración</a></li>
					<li><a href="login.php">Iniciar sesión</a></li>
				</ul>
			</nav>
		</header>

		<!-- Wrapper -->
		<div id="wrapper">

			<!-- Main -->
			<section id="main" class="wrapper">
				
				<div class="inner">
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
						<div class="col-12">
							<label for="admin">Administrador</label>
							<select name="admin" id="admin">
								<option value="0">No</option>
								<option value="1">Sí</option>
							</select>
						</div>
						<div class="col-12">
							<ul class="actions">
								<li><input type="submit" value="Agregar Usuario" class="primary" onclick="this.disabled=true; this.form.submit();" />
								</li>
								<li><input type="reset" value="Limpiar" /></li>
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