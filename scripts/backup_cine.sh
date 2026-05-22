#!/bin/bash

BACKUP_DIR="/backups/cine"
mkdir -p "$BACKUP_DIR"
FECHA=$(date +%Y%m%d_%H%M%S)
ARCHIVO="$BACKUP_DIR/cine_$FECHA.sql"
# Usamos /tmp para evitar problemas de permisos con el usuario www-data de PHP
LOG="/tmp/cine_error.log"

# Verificar espacio disponible (porcentaje usado)
# En el contenedor web no existe /var/lib/mysql, así que verificamos la raíz (/)
USO=$(df / | awk 'NR==2 {print $5}' | tr -d '%')
DISPONIBLE=$((100 - USO))

if [ "$DISPONIBLE" -le 15 ]; then
    echo "$(date) - Respaldo cancelado: espacio disponible $DISPONIBLE% (<=15%)" >> "$LOG"
    exit 1
fi

echo "$(date) - Iniciando volcado de base de datos..." >> "$LOG"
# Realizar el respaldo (agregamos --no-tablespaces para evitar errores de privilegios en MySQL 8)
mysqldump -h db -u root --password='root_password' --skip-ssl --no-tablespaces cine > "$ARCHIVO" 2>> "$LOG"

if [ $? -eq 0 ]; then
    echo "$(date) - Respaldo exitoso: $ARCHIVO" >> "$LOG"
    # Opcional: comprimir
    # gzip "$ARCHIVO" 
else
    echo "$(date) - ERROR en el respaldo" >> "$LOG"
fi