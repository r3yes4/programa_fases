<?php 
session_start();
// Unificar el nombre de la variable de sesión
if (isset($_SESSION['usuario']) && !isset($_SESSION['username'])) {
    $_SESSION['username'] = $_SESSION['usuario'];
}
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>Bleet</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="assets/css/main.css" />
		<noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
		<style>
			.dropdown {
				position: relative;
				display: inline-block;
			}
			.dropdown-menu {
				display: none;
				position: absolute;
				background-color: #5e42a6;
				min-width: 200px;
				box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
				z-index: 1;
				border-radius: 5px;
				padding: 10px 0;
			}
			.dropdown-menu li {
				padding: 10px 20px;
			}
			.dropdown-menu li:hover {
				background-color: #4a3685;
			}
			.dropdown:hover .dropdown-menu {
				display: block;
			}
		</style>
	</head>
	<body class="is-preload">

		<!-- Sidebar -->
		<section id="sidebar">
			<div class="inner">
				<nav>
					<ul>
						<li><a href="#intro">Bleet</a></li>
						<li><a href="#subir-archivo">Subir archivo</a></li>
						<li><a href="#three">Contacto</a></li>
						<?php
						if (isset($_SESSION['username'])) {
							$username = htmlspecialchars($_SESSION['username']);
							echo '<li><a href="mi_cuenta.php">'.$username.'</a></li>
								<li><a href="logout.php">Cerrar Sesión</a></li>';
						} else {
							echo '<li><a href="login.php">Iniciar Sesión</a></li>';
						}
						?>
					</ul>
				</nav>
			</div>
		</section>

		<!-- Wrapper -->
		<div id="wrapper">

			<!-- Intro -->
			<section id="intro" class="wrapper style1 fullscreen fade-up">
				<div class="inner">
					<h1>Bleet</h1>
					<p>Plataforma segura para compartir archivos</p>
					<ul class="actions">
						<li><a href="#subir-archivo" class="button scrolly">Comenzar</a></li>
					</ul>
				</div>
			</section>

			<!-- Two -->
			<section id="subir-archivo" class="wrapper style3 fade-up">
				<div class="inner">
					<h2>Subir archivos</h2>
					<p>Comparte tus archivos y carpetas de forma segura analizando los archivos previamente en búsqueda de virus o contenido malicioso</p>
					<div class="features">
						<section>
							<span class="icon solid major fa-code"></span>
							<h3>Desarrollo OpenSource</h3>
							<p>Código con integraciones con VirusTotal, para realizar la búsqueda de malware en los archivos.</p>
						</section>
						<section>
							<span class="icon solid major fa-lock"></span>
							<h3>Seguridad</h3>
							<p>Descarga de forma segura archivos sin riesgo de infección.</p>
						</section>
						<section>
							<span class="icon solid major fa-cog"></span>
							<h3>Configura a tus usuarios</h3>
							<p>Phasellus convallis elit id ullam corper amet et pulvinar. Duis aliquam turpis mauris, sed ultricies erat dapibus.</p>
						</section>
						<section>
							<span class="icon solid major fa-desktop"></span>
							<h3>Monitorización de archivos</h3>
							<p>Se realiza un seguimiento, permitiendo ver quién ha intentado subir archivos infectados.</p>
						</section>
					</div>
					<ul class="actions">
						<?php if (isset($_SESSION['username'])): ?>
							<li><a href="subir-archivos.php" class="button primary">Subir archivo</a></li>
							<li><a href="archivos.php" class="button">Mis Archivos</a></li>
						<?php else: ?>
							<li><a href="login.php?redirect=subir_archivos.php" class="button">Inicia sesión para subir archivos</a></li>
						<?php endif; ?>
					</ul>
				</div>
			</section>

			<!-- Three -->
			<section id="three" class="wrapper style1 fade-up">
				<div class="inner">
					<h2>Contacto</h2>
					<p>¿Tienes alguna pregunta o sugerencia? Contáctanos a través del siguiente formulario.</p>
					<div class="split style1">
						<section>
							<form method="post" action="#">
								<div class="fields">
									<div class="field half">
										<label for="name">Nombre</label>
										<input type="text" name="name" id="name" />
									</div>
									<div class="field half">
										<label for="email">Email</label>
										<input type="text" name="email" id="email" />
									</div>
									<div class="field">
										<label for="message">Mensaje</label>
										<textarea name="message" id="message" rows="5"></textarea>
									</div>
								</div>
								<ul class="actions">
									<li><a href="" class="button submit">Enviar Mensaje</a></li>
								</ul>
							</form>
						</section>
						<section>
							<ul class="contact">
								<li>
									<h3>Dirección</h3>
									<span>Calle Jesus Lagos Solis #654<br />
									Teruel, España</span>
								</li>
								<li>
									<h3>Email</h3>
									<a href="#">contacto@bleet.com</a>
								</li>
								<li>
									<h3>Teléfono</h3>
									<span>(+34) 695745824</span>
								</li>
								<li>
									<h3>Redes Sociales</h3>
									<ul class="icons">
										<li><a href="#" class="icon brands fa-twitter"><span class="label">Twitter</span></a></li>
										<li><a href="#" class="icon brands fa-facebook-f"><span class="label">Facebook</span></a></li>
										<li><a href="#" class="icon brands fa-github"><span class="label">GitHub</span></a></li>
									</ul>
								</li>
							</ul>
						</section>
					</div>
				</div>
			</section>

		</div>

		<!-- Footer -->
		<footer id="footer" class="wrapper style1-alt">
			<div class="inner">
				<ul class="menu">
					<li>&copy; Bleet. Todos los derechos reservados.</li><li>Design: <a href="http://html5up.net">HTML5 UP</a></li>
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