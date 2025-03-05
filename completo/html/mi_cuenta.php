<!DOCTYPE html>
<html>
<head>
    <title>Mi Cuenta</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="assets/css/main.css" />
    <style>
        .container {
            margin-top: 50px;  
            display: flex;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
            background: #5e42a6;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }
        .sidebar {
            width: 30%;
            padding: 20px;
            text-align: center;
            border-right: 2px solid #444;
        }
        .profile-pic {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 0 5px rgba(255, 255, 255, 0.5);
        }
        .username {
            font-size: 20px;
            font-weight: bold;
            margin-top: 10px;
            color: #fff;
           
        }
        .sidebar nav ul {
            list-style: none;
            padding: 0;
        }
        .sidebar nav ul li {
            padding: 10px;
            background: #333;
            margin: 5px 0;
            border-radius: 5px;
            text-align: left;
        }
        .sidebar nav ul li a {
            color: #fff;
            text-decoration: none;
            display: block;
        }
        .content {
            width: 70%;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            color: #fff;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #777;
            border-radius: 5px;
            background: #222;
            color: #fff;
        }
        .btn {
            background: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            display: inline-block;
        }
        .btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body class="is-preload">
    <header id="header">
        <a href="index.php" class="title">BLEET</a>
        <nav>
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="subir-archivos.php">Subir archivo</a></li>
                <li><a href="mi-cuenta.php" class="active">Mi Cuenta</a></li>
            </ul>
        </nav>
    </header>
    <div class="container">
        <div class="sidebar">
            <img src="assets/images/perfil.jpg" alt="Foto de perfil" class="profile-pic">
            <div class="username">Antonio Pérez</div>
            <nav>
                <ul>
                    <li><a href="#">Cuenta</a></li>
                    <li><a href="#">Cambiar la contraseña</a></li>
                    <li><a href="#">Privacidad</a></li>
                    <li><a href="#">Borrar la cuenta</a></li>
                </ul>
            </nav>
        </div>
        <div class="content">
            <h2 style="color:#fff;">Cuenta</h2>
            <form>
                <div class="form-group">
                    <label>Nombre de usuario</label>
                    <div class="row gtr-uniform">
                        <div class="col-6 col-12-xsmall">
                            <input type="text" name="usuario" id="usuario" value="" placeholder="Nombre de usuario" />
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" value="Antonio">
                </div>
                <div class="form-group">
                    <label>Apellidos</label>
                    <input type="text" value="Pérez">
                </div>
                <div class="form-group">
                    <label>Dirección de correo electrónico</label>
                    <input type="email" value="antonioperez@gmail.com">
                </div>
                <button type="submit" class="btn">Actualizar la cuenta</button>
            </form>
        </div>
    </div>
    <script src="assets/js/main.js"></script>
</body>
</html>
