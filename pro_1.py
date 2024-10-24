import os
import requests
import json
import mimetypes
import time
import shutil

# Crear la estructura de carpetas
if not os.path.exists("archivos"):
    os.mkdir("archivos")
    os.mkdir("archivos/limpios")
    os.mkdir("archivos/infectados")

def obtenerArchivos(directorio):
    archivos_encontrados = [] 
    for root, dirs, files in os.walk(directorio):
        for filename in files:  
            ruta_completa = os.path.join(root, filename)  # Construir la ruta completa
            archivos_encontrados.append(ruta_completa)  # Agregar la ruta a la lista
    return archivos_encontrados

# Obtener archivos del directorio
archivos = obtenerArchivos("DirectorioPrincipal")

api_key = "890d64820c761129bf48777e0182b612a1acfb590b04045171faff730633d686"

if archivos:
    for archivo in archivos:
        # Inicio de la petición API
        mime_type, _ = mimetypes.guess_type(archivo)

        url = "https://www.virustotal.com/api/v3/files"

        with open(archivo, "rb") as file_data:  # Asegurarse de cerrar el archivo después
            files = { "file": (archivo, file_data, mime_type) }
            headers = {
                "accept": "application/json",
                "x-apikey": api_key
            }

            response = requests.post(url, files=files, headers=headers)

            if response.status_code == 200:
                data = json.loads(response.text)
                file_id = data["data"]["id"]
            else:
                print(f"Error al enviar {archivo}: {response.status_code} - {response.text}")
                continue  # Saltar al siguiente archivo si hay error

        # Fin de la petición API upload
        ### Verificar si tiene virus
        #Inicio petición API
        url2 = "https://www.virustotal.com/api/v3/analyses/" + file_id

        headers2 = {
            "accept": "application/json",
            "x-apikey": api_key
        }

        response = requests.get(url2, headers=headers2)
        json_data = response.json()

        # Acceder a los atributos y resultados con json
        attributes = json_data['data']['attributes']
        results = attributes['results']

        # Bandera para detectar virus
        tiene_virus = False

        for key, value in results.items():
            if value['result'] is not None:
                tiene_virus = True
                print(f"{archivo}: Alerta, tiene virus detectado por {key}!")
                break

        # Mover archivos a carpetas correspondientes
        if tiene_virus:
            shutil.move(archivo, "archivos/infectados")
        else:
            print(f"{archivo}: No tiene virus.")
            shutil.move(archivo, "archivos/limpios")

        time.sleep(20)
    
else:
    print("No se han encontrado archivos")