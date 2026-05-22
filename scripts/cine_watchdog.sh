#!/bin/bash

LOG="/var/log/cine_error.log"
# Guardamos el log en la carpeta del proyecto para evitar problemas de permisos en Windows
LOG="$(dirname "$0")/../cine_watchdog.log"
# Usamos los nombres exactos de tus contenedores en Docker
SERVICIOS=("taquilla_web" "taquilla_db")

# Intenta crear el log si no existe
touch "$LOG" 2>/dev/null
echo "Iniciando Watchdog (Simulación para Windows)... Presiona Ctrl+C para detener."

for svc in "${SERVICIOS[@]}"; do
    # Preguntamos a Docker si el contenedor está en estado "Running"
    if [ "$(docker inspect -f '{{.State.Running}}' "$svc" 2>/dev/null)" != "true" ]; then
        echo "$(date '+%Y-%m-%d %H:%M:%S') - $svc está caído. Reiniciando..." >> "$LOG"
        docker start "$svc" > /dev/null
        if [ "$(docker inspect -f '{{.State.Running}}' "$svc" 2>/dev/null)" == "true" ]; then
            echo "$(date '+%Y-%m-%d %H:%M:%S') - $svc reiniciado con éxito." >> "$LOG"
        else
            echo "$(date '+%Y-%m-%d %H:%M:%S') - ERROR: No se pudo reiniciar $svc." >> "$LOG"
while true; do
    # Intenta crear el log si no existe
    touch "$LOG" 2>/dev/null

    for svc in "${SERVICIOS[@]}"; do
        # Preguntamos a Docker si el contenedor está en estado "Running"
        if [ "$(docker inspect -f '{{.State.Running}}' "$svc" 2>/dev/null)" != "true" ]; then
            echo "$(date '+%Y-%m-%d %H:%M:%S') - $svc está caído. Reiniciando..." >> "$LOG"
            docker start "$svc" > /dev/null
            if [ "$(docker inspect -f '{{.State.Running}}' "$svc" 2>/dev/null)" == "true" ]; then
                echo "$(date '+%Y-%m-%d %H:%M:%S') - $svc reiniciado con éxito." >> "$LOG"
            else
                echo "$(date '+%Y-%m-%d %H:%M:%S') - ERROR: No se pudo reiniciar $svc." >> "$LOG"
            fi
        fi
    fi
    done
    
    # Espera 10 segundos antes de volver a revisar (ideal para presentaciones rápidas)
    sleep 10
done