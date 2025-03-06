db = db.getSiblingDB('file_uploads'); // Crea la base de datos si no existe

db.createCollection('subidos');
db.createCollection('eliminados');
db.createCollection('infectados'); 


db.subidos.insertOne({
  action: "init",
  message: "Base de datos inicializada con colecciones",
  timestamp: new Date()
});

print("✅ Base de datos y colecciones creadas correctamente");



