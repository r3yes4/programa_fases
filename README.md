# Proyecto Dockerizado con Apache, PHP, MySQL y phpMyAdmin

Este proyecto configura un entorno de desarrollo utilizando Docker Compose con Apache, PHP, MySQL y phpMyAdmin.

## Requisitos

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/install/)

## Instrucciones

1. Clona el repositorio en tu máquina local.

    ```sh
    git clone https://github.com/r3yes4/programa_fases/tree/main/completo
    cd completo
    ```

2. Construye y levanta los contenedores con Docker Compose.

    ```sh
    docker-compose up --build
    ```

3. Accede a la aplicación web en tu navegador.

    ```
    http://localhost:8081
    ```

4. Accede a phpMyAdmin para gestionar la base de datos.

    ```
    http://localhost:8082
    ```

## Servicios

- **Web**: Servidor Apache con PHP. El usuario y contraseñas por defecto para iniciar sesión en la web es admin y la contraseña admin. Este usuario deberia ser borrado cuando por el administrador cuando este agregue un nuevo usuario marcandolo como administrador en la página control-panel.php.

- **DB**: Servidor MySQL. El usuario root tiene como password rootp@ssw0rd. Esto debería ser modificado en el docker-compose.yml para incrementar la seguridad.

- **phpMyAdmin**: Interfaz web para gestionar MySQL.

## Variables de Entorno

- `MYSQL_HOST=db`
- `MYSQL_USER=root`
- `MYSQL_PASSWORD=rootp@ssw0rd`
- `MYSQL_DATABASE=bleet`

## Volúmenes

- `./html:/var/www/html`: Sincroniza el contenido del directorio [html](http://_vscodecontentref_/1) con el directorio `/var/www/html` en el contenedor.
- `C:/db_data:/var/lib/mysql`: Almacena los datos de MySQL en el directorio `C:/db_data`.
- `./sql:/docker-entrypoint-initdb.d`: Ejecuta los scripts SQL en el inicio del contenedor de MySQL.

## Notas

- Asegúrate de que los puertos `8081`, `8082` y `3306` estén disponibles en tu máquina local.
- Puedes modificar las variables de entorno en el archivo [docker-compose.yml](http://_vscodecontentref_/2) según tus necesidades.

¡Listo! Ahora deberías tener tu entorno de desarrollo configurado y funcionando.