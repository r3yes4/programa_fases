import smtplib
from email.mime.text import MIMEText

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

mensaje="test"
destinatario="poldark3@gmail.com"
enviar_correo(mensaje, destinatario)