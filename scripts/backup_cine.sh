#!/bin/bash

BACKUP_DIR="/backups/cine"
mkdir -p "$BACKUP_DIR"
FECHA=$(date +%Y%m%d_%H%M%S)
ARCHIVO="$BACKUP_DIR/cine_$FECHA.sql"
LOG="/var/log/cine_error.log"

# Verificar espacio disponible (porcentaje usado)
# Elegimos la partición raíz (/) o donde esté MySQL, pero usamos /var/lib/mysql por defecto
USO=$(df /var/lib/mysql | awk 'NR==2 {print $5}' | tr -d '%')
DISPONIBLE=$((100 - USO))

if [ "$DISPONIBLE" -le 15 ]; then
    echo "$(date) - Respaldo cancelado: espacio disponible $DISPONIBLE% (<=15%)" >> "$LOG"
    exit 1
fi

# Realizar el respaldo (ajusta usuario/contraseña si usas .my.cnf o variables de entorno)
mysqldump -u root -p'TuClaveSegura' --databases cine > "$ARCHIVO" 2>> "$LOG"

if [ $? -eq 0 ]; then
    echo "$(date) - Respaldo exitoso: $ARCHIVO" >> "$LOG"
    # Opcional: comprimir
    gzip "$ARCHIVO"
else
    echo "$(date) - ERROR en el respaldo" >> "$LOG"
fi