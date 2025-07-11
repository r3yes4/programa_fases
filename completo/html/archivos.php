<?php
session_start();
require 'db.php'; // Asegúrate de incluir el archivo de conexión a la base de datos

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php"); // Redirige a la página de inicio de sesión si no está autenticado
    exit;
}

$id_usuario = $_SESSION['usuario']; // Obtener el id del usuario desde la sesión
// Conexion mongodb

require 'vendor/autoload.php'; // Requerido para MongoDB
$usuario = 'mongoadmin';
$contraseña = 'mongop@ssw0rd';
$contraseñaCodificada = urlencode($contraseña);
// Conexión a MongoDB con la contraseña codificada
$mongoClient = new MongoDB\Client("mongodb://{$usuario}:{$contraseñaCodificada}@mongo:27017/admin");

$mongoDB = $mongoClient->file_uploads;
// Determinar qué archivo mostrar dependiendo de la selección
$view = isset($_GET['view']) ? $_GET['view'] : 'mis_archivos'; // Valor por defecto: 'mis_archivos'

// Obtener los archivos que están analizados (analizado = 1) del usuario dependiendo de la vista seleccionada
if ($view == 'mis_archivos') {
    $stmt = $conn->prepare("SELECT * FROM archivos WHERE id_usuario = :id_usuario AND analizado = 1");
    $stmt->execute([':id_usuario' => $id_usuario]);
    $archivos = $stmt->fetchAll();
} elseif ($view == 'compartidos') {
    $stmt = $conn->prepare("SELECT a.* FROM archivos a
                            JOIN archivos_compartidos ac ON a.id = ac.id_archivo
                            WHERE ac.id_usuario_compartido = :id_usuario AND a.analizado = 1");
    $stmt->execute([':id_usuario' => $id_usuario]);
    $archivos = $stmt->fetchAll();
} elseif ($view == 'resumen') {
    if ($_SESSION['is_admin'] == 1) {
        // Admin ve todos los logs con [usuario]
        $subidos = $mongoDB->subidos->find([])->toArray();
        $eliminados = $mongoDB->eliminados->find([])->toArray();
        $infectados = $mongoDB->infectados->find([])->toArray();
    } else {
        // Usuario normal solo ve sus propios logs
        $subidos = $mongoDB->subidos->find(['id_usuario' => $id_usuario])->toArray();
        $eliminados = $mongoDB->eliminados->find(['usuario_id' => $id_usuario])->toArray();
        $infectados = $mongoDB->infectados->find(['id_usuario' => $id_usuario])->toArray();
    }
    $archivos = []; // <-- Vacío o lo que se deba mostrar en resumen
} else {
    // Si la vista no es válida, redirigir a 'mis_archivos'
    header("Location: archivos.php?view=mis_archivos");
    exit;
}
?>
<html>
<head>
    <title>Mis archivos - BLEET</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="assets/css/main.css" />
    <noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
    <!-- Incluir Font Awesome para íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Estilos para la ventana modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            color: black;
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
            border-radius: 10px;
        }
        .modal-content h2{
            color: black;
            
        }
        .close {
            color: black;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover {
            color: black;
            cursor: pointer;
        }
        .user-list {
            color: black;
            list-style: none;
            padding: 0;
        }
        .user-list li {
            border-color: black;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            cursor: pointer;
        }
        .user-list li:hover {
            background-color: #f1f1f1;
        }
		.container {
            background-color: rgb(241, 218, 254);
            border-radius: 10px;
            width: 100%;
            margin: 0 auto;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
        }

        /* Estilos para los botones dentro del cuadro */
        .buttons-container {
            text-align: center;
            background-color: #5e42a6; /* Rosa */
            padding: 15px 0;
            border-radius: 8px;
        }
        
        .buttons-container a {
            display: inline-block;
            font-size: 18px;
            color: #fff;
            text-decoration: none;
            background-color: #5e42a6; /* Morado */
            border: 2px solid rgb(90, 8, 93);
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .buttons-container a:hover {
            background-color: #312450; /* Fondo morado más oscuro al pasar el ratón */
        }

        /* Estilo para el botón activo (cuando se selecciona) */
        .buttons-container a.active {
            background-color: #312450; /* Fondo activo más oscuro */
        }

        /* Estilos para la lista de archivos */
        .file-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column; /* Muestra los elementos verticalmente */
            border-top: 1px solid #ccc;
            background-color: rgb(255, 240, 245); /* Color de fondo diferente (rosa claro) */
            border-radius: 8px;
            color: #000;
        }

        .file-list li {
            display: flex;
            justify-content: space-between; /* Para separar el nombre del archivo y la ruta */
            padding: 10px;
            border-bottom: 1px solid #ddd;
            align-items: center;
        }

        .file-list li span {
            color: #333;
            font-weight: bold;
        }

        .file-path {
            color: #666;
            font-size: 0.9em;
        }

        /* Estilo para los botones de acción */
        .file-actions {
            display: flex;
            gap: 10px;
        }

        .file-actions a {
            padding: 8px 8px;
            background-color: #312450; 
            color: white;
            border-radius: 10px;
            text-decoration: none;
            font-size: 14px;
            text-align: center;
			transition: background-color 0.4s ease;
        }

        .file-actions a:hover {
            opacity: 0.8;
			background-color:rgb(155, 115, 249);
        }

        /* Íconos de descarga y compartir */
        .fa-download, .fa-share .fa-thrash-alt {
            font-size: 15px; /* Ajusta el tamaño del ícono si lo deseas */
			
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
                <li><a href="archivos.php" class="active">Mis Archivos</a></li>
                <li><a href="subir-archivos.php">Subir archivo</a></li>
                <?php if ($_SESSION['is_admin'] == 1): ?>
                    <li>
                        <a href="control-panel.php">Panel de administración</a>
                    </li>
                <?php endif; ?>
                <li><a href="mi_cuenta.php"><?php echo htmlspecialchars($_SESSION['usuario']); ?></a></li>
            </ul>
        </nav>
    </header>

    <!-- Wrapper -->
    <div id="wrapper">

        <!-- Main -->
        <section id="main" class="wrapper">
            <div class="inner">
                <h1 class="major">Archivos</h1>

                <!-- Contenedor principal con fondo celeste -->
                <div class="container">

                    <!-- Botones para cambiar entre "Mis archivos" y "Compartidos" -->
                    <div class="buttons-container">
                        <a href="archivos.php?view=mis_archivos" class="button <?php echo ($view == 'mis_archivos') ? 'active' : ''; ?>">Mis archivos</a>
                        <a href="archivos.php?view=compartidos" class="button <?php echo ($view == 'compartidos') ? 'active' : ''; ?>">Compartidos</a>
                        <a href="archivos.php?view=resumen" class="button <?php echo ($view == 'resumen') ? 'active' : ''; ?>">Resumen</a>

                    </div>

                    <!-- Lista de archivos -->
                    <ul class="file-list">
                        <?php if ($view == 'resumen'): ?>
                            <?php foreach ($subidos as $archivo): ?>
                                <?php
                                $fileName = basename($archivo['ruta_archivo']);
                                $realFileName = substr($fileName, strpos($fileName, '_') + 1);
                                if (str_ends_with($realFileName, '.aes')) {
                                    $realFileName = substr($realFileName, 0, -4);
                                }
                                // NUEVO: Añade etiqueta [usuario] solo para admin
                                $userTag = ($_SESSION['is_admin'] == 1) ? '['.$archivo['id_usuario'].'] ' : '';
                                ?>
                                <li>
                                    <span><?php echo $userTag . $realFileName; ?> (Subido)</span>
                                    <div class="file-actions">
                                        <a href="#"><i class="fas fa-file-upload"></i></a>
                                    </div>
                                </li>
                            <?php endforeach; ?>

                            <?php foreach ($eliminados as $archivo): ?>
                                <?php
                                $fileName = basename($archivo['ruta_archivo']);
                                $realFileName = substr($fileName, strpos($fileName, '_') + 1);
                                if (str_ends_with($realFileName, '.aes')) {
                                    $realFileName = substr($realFileName, 0, -4);
                                }
                                // NUEVO: Campo diferente para eliminados (usuario_id)
                                $userTag = ($_SESSION['is_admin'] == 1) ? '['.$archivo['usuario_id'].'] ' : '';
                                ?>
                                <li>
                                    <span><?php echo $userTag . $realFileName; ?> (Eliminado)</span>
                                    <div class="file-actions">
                                        <i class="fas fa-trash-alt" style="color: red;"></i>
                                    </div>
                                </li>
                            <?php endforeach; ?>

                            <?php foreach ($infectados as $archivo): ?>
                                <?php
                                $fileName = basename($archivo['ruta_archivo']);
                                $realFileName = substr($fileName, strpos($fileName, '_') + 1);
                                if (str_ends_with($realFileName, '.aes')) {
                                    $realFileName = substr($realFileName, 0, -4);
                                }
                                // NUEVO: Igual que subidos
                                $userTag = ($_SESSION['is_admin'] == 1) ? '['.$archivo['id_usuario'].'] ' : '';
                                ?>
                                <li>
                                    <span><?php echo $userTag . $realFileName; ?> (Infectado)</span>
                                    <div class="file-actions">
                                        <i class="fas fa-bug" style="color: darkred;"></i>
                                    </div>
                                </li>
                            <?php endforeach; ?>

                        <?php elseif (count($archivos) > 0): ?>
                            <?php foreach ($archivos as $archivo): ?>
                                <?php
                                $fileName = basename($archivo['ruta_archivo']);
                                $realFileName = substr($fileName, strpos($fileName, '_') + 1);
                                if (str_ends_with($realFileName, '.aes')) {
                                    $realFileName = substr($realFileName, 0, -4);
                                }
                                ?>
                                <li>
                                    <span><?php echo $realFileName; ?></span>
                                    <div class="file-actions">
                                        <a href="download.php?file=<?php echo urlencode(basename($archivo['ruta_archivo'])); ?>"><i class="fas fa-download"></i></a>
                                        <a href="#" onclick="openShareModal(<?php echo $archivo['id']; ?>)"><i class="fas fa-share"></i></a>
                                        <a href="#" onclick="confirmDelete(<?php echo $archivo['id']; ?>)"><i class="fas fa-trash-alt" style="color:red;"></i></a>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>No hay archivos para mostrar en esta categoría.</li>
                        <?php endif; ?>
                    </ul>

                </div> <!-- Fin del contenedor celeste -->

            </div>
        </section>
    </div>

    <!-- Ventana modal para compartir -->
    <div id="shareModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeShareModal()">&times;</span>
        <h2>Compartir archivo</h2>
        <p>Selecciona un usuario o departamento para compartir:</p>

        <!-- Botones para cambiar entre "Usuarios" y "Departamentos" -->
        <div class="buttons-container">
            <button id="btnUsers" class="active" onclick="showUsers()">Usuarios</button>
            <button id="btnDepartments" onclick="showDepartments()">Departamentos</button>
        </div>

        <!-- Lista de usuarios -->
        <ul id="userList" class="user-list"></ul>

        <!-- Lista de departamentos (inicialmente oculta) -->
        <ul id="departmentList" class="user-list" style="display: none;"></ul>
    </div>
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

    <!-- Script para manejar la ventana modal y compartir archivos -->
    <script>
    function confirmDelete(archivoId) {
        if (confirm('¿Estás seguro de que deseas eliminar este archivo?')) {
            window.location.href = 'eliminar_archivo.php?archivo_id=' + archivoId;
        }
    }
    function openShareModal(archivoId) {
        window.currentArchivoId = archivoId;
        showUsers(); // Mostrar usuarios por defecto
        document.getElementById('shareModal').style.display = 'block';
    }

    function closeShareModal() {
        document.getElementById('shareModal').style.display = 'none';
    }

    function showUsers() {
        document.getElementById('userList').style.display = 'block';
        document.getElementById('departmentList').style.display = 'none';
        document.getElementById('btnUsers').classList.add('active');
        document.getElementById('btnDepartments').classList.remove('active');
        loadUsers();
    }

    function showDepartments() {
        document.getElementById('userList').style.display = 'none';
        document.getElementById('departmentList').style.display = 'block';
        document.getElementById('btnUsers').classList.remove('active');
        document.getElementById('btnDepartments').classList.add('active');
        loadDepartments();
    }

    function loadUsers() {
        fetch('get_users.php')
            .then(response => response.json())
            .then(users => {
                const userList = document.getElementById('userList');
                userList.innerHTML = ''; // Limpiar la lista
                users.forEach(user => {
                    const li = document.createElement('li');
                    li.textContent = user.usuario;
                    li.onclick = () => shareFileWithUser(user.usuario);
                    userList.appendChild(li);
                });
            });
    }

    function loadDepartments() {
        fetch('get_departments.php')
            .then(response => response.json())
            .then(departments => {
                const departmentList = document.getElementById('departmentList');
                departmentList.innerHTML = ''; // Limpiar la lista
                departments.forEach(department => {
                    const li = document.createElement('li');
                    li.textContent = department.nombre;
                    li.onclick = () => shareFileWithDepartment(department.nombre);
                    departmentList.appendChild(li);
                });
            });
    }

    function shareFileWithUser(usuarioCompartido) {
        const archivoId = window.currentArchivoId;

        fetch('share_file.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ archivoId, usuarioCompartido })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.success ? 'Archivo compartido correctamente.' : 'Error al compartir el archivo.');
            if (data.success) closeShareModal();
        });
    }

    function shareFileWithDepartment(departamentoCompartido) {
        const archivoId = window.currentArchivoId;

        fetch('share_file_department.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ archivoId, departamentoCompartido })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.success ? 'Archivo compartido correctamente.' : 'Error al compartir el archivo.');
            if (data.success) closeShareModal();
        });
    }
    </script>
</body>
</html>



