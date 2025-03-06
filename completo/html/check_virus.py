import os
import requests
import mimetypes
import time
import shutil
import mysql.connector
import smtplib
from email.mime.text import MIMEText
import pymongo
import urllib.parse

# Credenciales de MongoDB
usuario = "mongoadmin"
password = "mongop@ssw0rd"

# Escapar las credenciales de MongoDB
usuario_codificado = urllib.parse.quote_plus(usuario)
password_codificado = urllib.parse.quote_plus(password)


def agregar_a_mongo(archivo_id, ruta_archivo, usuario):
        # Conexión a MongoDB
    usuario="adminmongo"
    password="mongop@ssw0rd"
    mongo_client = pymongo.MongoClient(f"mongodb://{usuario_codificado}:{password_codificado}@mongo:27017/")
    mongo_db = mongo_client["file_uploads"]  # Base de datos 'file_uploads'
    mongo_collection_infectados = mongo_db["infectados"]  # Colección 'infectados'
    # Datos que deseas almacenar en la colección 'infectados'
    archivo_data = {
        "archivo_id": archivo_id,
        "ruta_archivo": ruta_archivo,
        "usuario": usuario,
        "fecha_detectado": time.strftime("%Y-%m-%d %H:%M:%S"),
        "estado": "infectado"
    }
    # Insertamos el archivo infectado en la colección 'infectados' en MongoDB
    mongo_collection_infectados.insert_one(archivo_data)
    print(f"Archivo infectado agregado a MongoDB: {ruta_archivo}")

def enviar_correo(mensaje, destinatario):
    smtp_server = 'smtp.gmail.com'
    port = 587
    sender_email = 'poldark3@gmail.com'
    password = 'qnap ewjt emtk voex'

    message = MIMEText(mensaje)
    message['From'] = sender_email
    message['To'] = destinatario
    message['Subject'] = f'REPORT: Estado de archivo subido a Bleet'

    with smtplib.SMTP(smtp_server, port) as server:
        server.starttls()
        server.login(sender_email, password)
        server.sendmail(sender_email, destinatario, message.as_string())

def obtener_correo_usuario(id_usuario):
    conexion = mysql.connector.connect(**DB_CONFIG)
    cursor = conexion.cursor(dictionary=True)
    
    # Obtener el correo del usuario
    query = "SELECT email FROM usuarios WHERE usuario = %s"
    cursor.execute(query, (id_usuario,))
    usuario = cursor.fetchone()
    
    cursor.close()
    conexion.close()
    
    if usuario:
        return usuario['email']
    else:
        return None


# Configuración de la base de datos
DB_CONFIG = {
    "host": "mysql_db",
    "user": "root",
    "password": "rootp@ssw0rd",
    "database": "bleet"
}

API_KEY = "890d64820c761129bf48777e0182b612a1acfb590b04045171faff730633d686"

# Conectar a la base de datos y obtener archivos no analizados
def obtener_archivos_pendientes():
    conexion = mysql.connector.connect(**DB_CONFIG)
    cursor = conexion.cursor(dictionary=True)
    
    query = "SELECT id, ruta_archivo, id_usuario FROM archivos WHERE analizado = 0"
    cursor.execute(query)
    archivos = cursor.fetchall()
    
    cursor.close()
    conexion.close()
    return archivos

# Actualizar el estado de análisis y la ruta del archivo en la base de datos
# Actualizar el estado de análisis y la ruta del archivo en la base de datos
def actualizar_estado_archivo(id_archivo, estado, nueva_ruta=None, virus=None):
    conexion = mysql.connector.connect(**DB_CONFIG)
    cursor = conexion.cursor()
    
    if nueva_ruta:
        if virus is not None:
            query = "UPDATE archivos SET analizado = %s, ruta_archivo = %s, virus = %s WHERE id = %s"
            cursor.execute(query, (estado, nueva_ruta, virus, id_archivo))
        else:
            query = "UPDATE archivos SET analizado = %s, ruta_archivo = %s WHERE id = %s"
            cursor.execute(query, (estado, nueva_ruta, id_archivo))
    else:
        if virus is not None:
            query = "UPDATE archivos SET analizado = %s, virus = %s WHERE id = %s"
            cursor.execute(query, (estado, virus, id_archivo))
        else:
            query = "UPDATE archivos SET analizado = %s WHERE id = %s"
            cursor.execute(query, (estado, id_archivo))
    
    conexion.commit()
    cursor.close()
    conexion.close()

while True:
    time.sleep(5)
    # Procesar los archivos obtenidos
    archivos_pendientes = obtener_archivos_pendientes()

    if archivos_pendientes:
        for archivo in archivos_pendientes:
            archivo_id = archivo['id']
            ruta_archivo = archivo['ruta_archivo']
            id_usuario = archivo['id_usuario']
            
            destinatario = obtener_correo_usuario(id_usuario)
            

            if not os.path.exists(ruta_archivo):
                print(f"Archivo no encontrado: {ruta_archivo}")
                continue
            
            # Detectar tipo MIME
            mime_type, _ = mimetypes.guess_type(ruta_archivo)
            stat = os.stat(ruta_archivo)
            umbral = 32 * 1024 * 1024  # 32MB
            umbral2 = 650 * 1024 * 1024  # 650MB

            if stat.st_size > umbral2:
                print(f"El archivo {ruta_archivo} excede el tamaño permitido.")
                actualizar_estado_archivo(archivo_id, 1)  # Marcar como analizado
                continue

            if stat.st_size < umbral:
                url = "https://www.virustotal.com/api/v3/files"
            else:
                url = "https://www.virustotal.com/api/v3/files/upload_url"
                headers = {"accept": "application/json", "x-apikey": API_KEY}
                response = requests.get(url, headers=headers)
                if response.status_code == 200:
                    data = response.json()
                    url = data["data"]
                else:
                    print(f"Error al obtener URL de subida para {ruta_archivo}")
                    continue

            with open(ruta_archivo, "rb") as file_data:
                files = {"file": (ruta_archivo, file_data, mime_type)}
                headers = {"accept": "application/json", "x-apikey": API_KEY}
                response = requests.post(url, files=files, headers=headers)

                if response.status_code == 200:
                    data = response.json()
                    file_id = data["data"]["id"]
                else:
                    print(f"Error al enviar {ruta_archivo}: {response.status_code} - {response.text}")
                    continue

            # Esperar antes de verificar el resultado
            time.sleep(20)

            # Consultar el resultado del análisis
            url2 = f"https://www.virustotal.com/api/v3/analyses/{file_id}"
            headers2 = {"accept": "application/json", "x-apikey": API_KEY}
            response = requests.get(url2, headers=headers2)

            if response.status_code != 200:
                print(f"Error al obtener resultado de {ruta_archivo}")
                continue

            json_data = response.json()
            attributes = json_data['data']['attributes']
            results = attributes['results']

            virus_detectado = any(value['result'] is not None for value in results.values())

            if virus_detectado:
                os.remove(ruta_archivo)
                print(f"Archivo infectado eliminado: {ruta_archivo}")
                actualizar_estado_archivo(archivo_id, 1, virus=1)  # Marcar como analizado
                mensaje = f"El archivo {ruta_archivo} ha sido eliminado por contener virus."
                enviar_correo(mensaje, destinatario)
                agregar_a_mongo(archivo_id, ruta_archivo, id_usuario)
            else:
                destino = "uploads/limpios"
                os.makedirs(destino, exist_ok=True)
                nueva_ruta = os.path.join(destino, os.path.basename(ruta_archivo))
                
                # Mover el archivo limpio a la carpeta 'limpios'
                shutil.move(ruta_archivo, nueva_ruta)
                print(f"Archivo limpio movido: {nueva_ruta}")

                # Actualizar la ruta del archivo en la base de datos
                actualizar_estado_archivo(archivo_id, 1, nueva_ruta)

                mensaje=f"El archivo {ruta_archivo} ha sido analizado y se ha subido con éxito."

                enviar_correo(mensaje, destinatario)
    else:
        print("No hay archivos pendientes de análisis.")
