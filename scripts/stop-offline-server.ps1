param(
    [int]$Port = 8020
)

$ErrorActionPreference = 'Stop'

$listener = Get-NetTCPConnection -LocalPort $Port -State Listen -ErrorAction SilentlyContinue
if (-not $listener) {
    Write-Host "No listening server found on port $Port."
    return
}

$processIds = $listener | Select-Object -ExpandProperty OwningProcess -Unique
foreach ($processId in $processIds) {
    Stop-Process -Id $processId -Force
}

Write-Host "Stopped offline testing server on port $Port."

