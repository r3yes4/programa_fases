<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php"); // Redirige a la página de inicio de sesión
    exit;
}
?>
<html>
	<head>
		<title>Generic - Hyperspace by HTML5 UP</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="assets/css/main.css" />
		<noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
		<style>
			.upload-container {
				background: #4B0082;
				border: 2px #6c757d;
				border-radius: 10px;
				width: 800px;
				padding: 20px;
				text-align: center;
				transition: background-color 0.3s;
				margin-left: 22%;
				padding-top:50px;
			}
			
			.upload-container.dragover {
				background-color:rgb(148, 66, 249);
			}
			.upload-container h2 {
				color: white;
				font-size: 40px;
			}
			.upload-container input[type="file"] {
				display: none;
			}
			
			
			.file-preview {
				margin-top: 15px;
				text-align: left;
			}
			.file-preview p {
				margin: 5px 0;
				font-size: 14px;
				color: #ffffff;
			}
			.mensaje {
				text-align: center;
				margin-top: 20px;
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
					<li><a href="archivos.php" class="active">Mis archivos</a></li>
					<li><a href="generic.html" class="active">Subir archivo</a></li>
					<li><a href="login.php">Iniciar sesión</a></li>
				</ul>
			</nav>
		</header>

		<!-- Wrapper -->
		<div id="wrapper">

			<!-- Main -->
			<?php
			if (isset($_GET['message'])) {
				$message = $_GET['message'];
				if ($message == 'success') {
					$mensaje = "Archivo subido con éxito.";
				} elseif ($message == 'virus') {
					$mensaje = "El archivo contiene un virus y ha sido eliminado.";
				} elseif ($message == 'error_script') {
					$mensaje = "Error al ejecutar el script de verificación.";
				} elseif ($message == 'error_verification') {
					$mensaje = "Error al verificar el archivo.";
				} elseif ($message == 'move_error') {
					$mensaje = "Error al mover el archivo.";
				} elseif ($message == 'upload_error') {
					$mensaje = "Error al subir el archivo.";
				} elseif ($message == 'no_files') {
					$mensaje = "No se seleccionaron archivos.";
				} elseif ($message == 'session_error') {
					$mensaje = "Error de sesión. Por favor, inicia sesión.";
				} elseif ($message == 'partial_success') {
					$mensaje = "Algunos archivos se subieron con éxito, pero otros fallaron.";
				}
				  else {
					$mensaje = "Error desconocido.";
				}
			}
			?> 

			<section id="main" class="wrapper">
				<div class="inner">
					<h1 class="major">Subir archivo</h1>
					<div class="upload-container" id="drop-zone">
						<h2 class="texto">Arrastra tus archivos aquí o haz clic para seleccionarlos</h2>
						<form action="upload.php" method="post" enctype="multipart/form-data">
						<!-- Botón para seleccionar archivos -->
						<input type="file" name="file[]" id="file-input" multiple>
						<label for="file-input" class="button icon solid fa-upload">Seleccionar archivo(s)</label>

						<!-- Botón para seleccionar carpetas -->
						<input type="file" name="folder[]" id="folder-input" webkitdirectory multiple>
						<label for="folder-input" class="button icon solid fa-folder">Seleccionar carpeta</label>

						<!-- Área donde se mostrarán los archivos seleccionados -->
						<div class="file-preview" id="file-preview"></div>

						<button type="submit" class="button">Subir</button>
						</form>
					</div>
					<div class="mensaje">
						<?php if (isset($mensaje)) { ?>
							<h2><?php echo $mensaje; ?></h2>
						<?php } ?>
					</div>
				</div>
			</section>

		<script>
			const dropZone = document.getElementById('drop-zone');
			const fileInput = document.getElementById('file-input');
			const folderInput = document.getElementById('folder-input');
			const filePreview = document.getElementById('file-preview');

			// Cambia el estilo al arrastrar archivos
			dropZone.addEventListener('dragover', (event) => {
				event.preventDefault();
				dropZone.classList.add('dragover');
			});

			dropZone.addEventListener('dragleave', () => {
				dropZone.classList.remove('dragover');
			});

			dropZone.addEventListener('drop', (event) => {
				event.preventDefault();
				dropZone.classList.remove('dragover');

				// Añadir archivos al input y actualizar vista previa
				const files = event.dataTransfer.files;
				fileInput.files = files;
				updateFilePreview(files);
			});

			// Muestra la vista previa de los archivos seleccionados
			fileInput.addEventListener('change', () => updateFilePreview(fileInput.files));
			folderInput.addEventListener('change', () => updateFilePreview(folderInput.files));

			function updateFilePreview(files) {
				filePreview.innerHTML = ''; // Limpiar lista previa
				if (files.length === 0) {
					filePreview.innerHTML = "<p>No se han seleccionado archivos.</p>";
					return;
				}

				const list = document.createElement('ul');
				list.style.listStyle = "none";
				list.style.padding = "0";

				Array.from(files).forEach(file => {
					const listItem = document.createElement('li');
					listItem.textContent = `📄 ${file.name} (${(file.size / 1024).toFixed(2)} KB)`;
					list.appendChild(listItem);
				});

				filePreview.appendChild(list);
			}
		</script>
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