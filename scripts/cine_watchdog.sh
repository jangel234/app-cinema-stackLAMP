#!/bin/bash

LOG="/var/log/cine_error.log"
SERVICIOS=("apache2" "mysql")   # Ajustar según distribución (httpd, mariadb)

for svc in "${SERVICIOS[@]}"; do
    if ! systemctl is-active --quiet "$svc"; then
        echo "$(date '+%Y-%m-%d %H:%M:%S') - $svc está caído. Reiniciando..." >> "$LOG"
        systemctl restart "$svc"
        if systemctl is-active --quiet "$svc"; then
            echo "$(date '+%Y-%m-%d %H:%M:%S') - $svc reiniciado con éxito." >> "$LOG"
        else
            echo "$(date '+%Y-%m-%d %H:%M:%S') - ERROR: No se pudo reiniciar $svc." >> "$LOG"
        fi
    fi
done