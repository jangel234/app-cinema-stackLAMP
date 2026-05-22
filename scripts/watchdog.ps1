# Nombres de tus servicios
$Servicios = @("taquilla_web", "taquilla_db", "taquilla_pma")

# Ruta del log en la misma carpeta del script
$LogFile = Join-Path -Path $PSScriptRoot -ChildPath "cine_watchdog.log"

Write-Host "Iniciando Watchdog en PowerShell... Presiona Ctrl+C para detener." -ForegroundColor Green

while ($true) {
    foreach ($svc in $Servicios) {
        # Verificamos si está corriendo
        $isRunning = docker inspect -f '{{.State.Running}}' $svc 2>$null
        
        if ($isRunning -ne "true") {
            $Fecha = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
            
            Write-Host "[$Fecha] ALERTA: $svc está caído. Intentando reiniciar..." -ForegroundColor Yellow
            Add-Content -Path $LogFile -Value "$Fecha - $svc está caído. Reiniciando..."
            
            # Intentamos reiniciar y capturamos el resultado
            $StartOutput = docker start $svc 2>&1
            
            # Pausa de 2 segundos para ver si sobrevive
            Start-Sleep -Seconds 2
            
            # Volvemos a comprobar
            $isRunningNow = docker inspect -f '{{.State.Running}}' $svc 2>$null
            
            if ($isRunningNow -eq "true") {
                Write-Host "[$Fecha] ÉXITO: $svc reiniciado correctamente." -ForegroundColor Green
                Add-Content -Path $LogFile -Value "$Fecha - $svc reiniciado con éxito."
            } else {
                Write-Host "[$Fecha] ERROR CRÍTICO: No se pudo mantener $svc en ejecución." -ForegroundColor Red
                Write-Host "Motivo reportado por Docker: $StartOutput" -ForegroundColor Red
                Add-Content -Path $LogFile -Value "$Fecha - ERROR: No se pudo reiniciar $svc. Detalle: $StartOutput"
            }
        }
    }
    
    # Espera de 5 segundos
    Start-Sleep -Seconds 5
}