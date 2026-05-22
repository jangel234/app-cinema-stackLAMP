#!/bin/bash

# Colores para las alertas en consola
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # Sin color

# Guardamos el log en la carpeta del proyecto
# LOG="$(dirname "$0")/../cine_watchdog.log"

# CORRECCIÓN: Espacio agregado correctamente
SERVICIOS=("taquilla_web" "taquilla_db" "taquilla_pma")

echo -e "${GREEN}Iniciando Watchdog (Simulación para Windows)... Presiona Ctrl+C para detener.${NC}"

# docker ps -a 

while true; do
    touch "$LOG" 2>/dev/null

    for svc in "${SERVICIOS[@]}"; do
        if [ "$(docker inspect -f '{{.State.Running}}' "$svc" 2>/dev/null)" != "true" ]; then
            FECHA=$(date '+%Y-%m-%d %H:%M:%S')
            
            echo -e "${YELLOW}[$FECHA] ALERTA: $svc está caído. Intentando reiniciar...${NC}"
            echo "$FECHA - $svc está caído. Reiniciando..." >> "$LOG"
            
            # MEJORA: En lugar de ocultar el error, lo capturamos en una variable
            START_OUTPUT=$(docker start "$svc" 2>&1)
            
            # Damos 2 segundos para ver si el contenedor sobrevive al arranque
            sleep 2
            
            if [ "$(docker inspect -f '{{.State.Running}}' "$svc" 2>/dev/null)" == "true" ]; then
                echo -e "${GREEN}[$FECHA] ÉXITO: $svc reiniciado correctamente.${NC}"
                echo "$FECHA - $svc reiniciado con éxito." >> "$LOG"
            else
                # Ahora imprimimos exactamente por qué falló
                echo -e "${RED}[$FECHA] ERROR CRÍTICO: No se pudo mantener $svc en ejecución.${NC}"
                echo -e "${RED}Motivo reportado por Docker: $START_OUTPUT${NC}"
                echo "$FECHA - ERROR: No se pudo reiniciar $svc. Detalle: $START_OUTPUT" >> "$LOG"
            fi
        fi
    done
    
    sleep 5
done