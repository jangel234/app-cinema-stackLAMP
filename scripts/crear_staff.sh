#!/bin/bash
# Uso: ./crear_staff.sh vendedor1 tecnico1 supervisor

if [ $# -eq 0 ]; then
    echo "Debe proporcionar al menos un nombre de usuario."
    exit 1
fi

for usuario in "$@"; do
    # Verificar si el usuario ya existe
    if id "$usuario" &>/dev/null; then
        echo "El usuario $usuario ya existe, omitiendo."
        continue
    fi

    # Crear usuario con directorio home y grupo propio
    useradd -m -s /bin/bash "$usuario"
    # Establecer contraseña temporal (cambiar en primer inicio)
    echo "$usuario:Temp1234" | chpasswd
    # Forzar cambio de contraseña en siguiente login
    chage -d 0 "$usuario"

    echo "Usuario $usuario creado con home /home/$usuario."
done