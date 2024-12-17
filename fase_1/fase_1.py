import os
import requests
import json
import mimetypes
import time
import shutil
import hashlib

def obtenerArchivos(directorio):
    archivos_encontrados = [] 
    for root, dirs, files in os.walk(directorio):
        for filename in files:  
            ruta_completa = os.path.join(root, filename)  # Construir la ruta completa
            archivos_encontrados.append(ruta_completa)  # Agregar la ruta a la lista
    return archivos_encontrados

def calcular_sha256(ruta_archivo):
    sha256_hash = hashlib.sha256()
    with open(ruta_archivo, "rb") as archivo:
        for bloque in iter(lambda: archivo.read(4096), b""):
            sha256_hash.update(bloque)
    return sha256_hash.hexdigest()

# Obtener archivos del directorio
archivos = obtenerArchivos("DirectorioPrincipal")

api_key = "890d64820c761129bf48777e0182b612a1acfb590b04045171faff730633d686"

if archivos:
    for archivo in archivos:
        file_hash = calcular_sha256(archivo)
        if os.path.exists("history.json"):
            with open("history.json", "r") as history:
                data = json.load(history)

            for item in data["logs"]:
                if item["hash"] == file_hash:
                    if item["virus"] == False:
                        shutil.move(archivo, "archivos/limpios")
                        exit()
                    
        # Inicio de la petición API
        mime_type, _ = mimetypes.guess_type(archivo)
        stat = os.stat(archivo)
        umbral = 32 * 1024 * 1024
        umbral2 = 650 * 1024 * 1024
        if stat.st_size > umbral2:
            exit()
        if stat.st_size < umbral:
            url = "https://www.virustotal.com/api/v3/files"
        else:
            url = "https://www.virustotal.com/api/v3/files/upload_url"

            headers = {
                "accept": "application/json",
                "x-apikey": "890d64820c761129bf48777e0182b612a1acfb590b04045171faff730633d686"
            }

            response = requests.get(url, headers=headers)
            data = json.loads(response.text)
            url = data["data"]




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
        # Fin de la petición API upload

        ### Verificar si tiene virus
        #Inicio peticion API
        url2 = "https://www.virustotal.com/api/v3/analyses/"+file_id

        headers2 = {
            "accept": "application/json",
            "x-apikey": api_key
        }

        response = requests.get(url2, headers=headers2)
        

        json_data = response.json() 
        #fin api req      
        
        attributes = json_data['data']['attributes']
        results = attributes['results']
        file_hash = json_data['meta']['file_info']['sha256']

        virus = False
        for key, value in results.items():
            if value['result'] != None:
                virus = True
                
        if virus == True:
            shutil.move(archivo, "archivos/infectados")
        else:
            shutil.move(archivo, "archivos/limpios")
        log= {
            'hash' : file_hash,
            'virus' : virus
        }  

        if os.path.exists("history.json"):
            with open("history.json", "r") as history:
                data = json.load(history)
        else:
            data = {"logs": []}
        for item in data["logs"]:
            if item["hash"] != log["hash"]:
                data["logs"].append(log)
                with open("history.json", "w") as history:
                    json.dump(data, history, indent=3)
        

        time.sleep(20)
    
else:
    print("No se han encontrado archivos")