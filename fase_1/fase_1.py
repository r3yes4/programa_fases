import os
import requests
import json
import mimetypes

def obtenerArchivos(directorio, ext):
    ext = f".{ext}"
    archivos_encontrados = [] 

    for root, dirs, files in os.walk(directorio):
        for filename in files:
            if filename.endswith(ext):  
                ruta_completa = os.path.join(root, filename)  # Usar os.path.join para construir la ruta
                archivos_encontrados.append(ruta_completa)  # Agregar la ruta a la lista
    return archivos_encontrados

results = obtenerArchivos("DirectorioPrincipal", "py")


### Upload file

#Inicio peticion API
mime_type, _ = mimetypes.guess_type("a.py")

url = "https://www.virustotal.com/api/v3/files"

files = { "file": (results[0], open(results[0], "rb"), mime_type) }
headers = {
    "accept": "application/json",
    "x-apikey": "890d64820c761129bf48777e0182b612a1acfb590b04045171faff730633d686"
}

response = requests.post(url, files=files, headers=headers)

#Fin peticion API

data = json.loads(response.text)

id = data["data"]["id"]


### Verificar si tiene virus

#Inicio peticion API
url2 = "https://www.virustotal.com/api/v3/analyses/"+id

headers = {
    "accept": "application/json",
    "x-apikey": "890d64820c761129bf48777e0182b612a1acfb590b04045171faff730633d686"
}

response = requests.get(url2, headers=headers)


json_data = response.json() # Convierte el JSON de la respuesta en un formato legible
#Fin peticion API

# Acceder a los atributos y resultados con json
attributes = json_data['data']['attributes']
results = attributes['results']

for key, value in results.items():
    if value['result'] == None:
        print(f"no virus en {key}")
    else:
        print("alerta tiene viru!!!!!!!!!!!!!")

