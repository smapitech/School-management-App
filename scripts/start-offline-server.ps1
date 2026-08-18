param(
    [int]$Port = 8020
)

$ErrorActionPreference = 'Stop'

$projectRoot = Split-Path -Parent $PSScriptRoot
$php = (Get-Command php -ErrorAction Stop).Source
$initScript = Join-Path $PSScriptRoot 'init-offline-database.php'
$routerScript = Join-Path $PSScriptRoot 'offline-router.php'
$dbPath = Join-Path $projectRoot 'storage\offline-testing.sqlite'
$stdoutLog = Join-Path $projectRoot 'storage\offline-server.out.log'
$stderrLog = Join-Path $projectRoot 'storage\offline-server.err.log'

if (Get-NetTCPConnection -LocalPort $Port -State Listen -ErrorAction SilentlyContinue) {
    throw "Port $Port is already in use."
}

& $php $initScript

if (-not (Test-Path $stdoutLog)) {
    New-Item -ItemType File -Path $stdoutLog -Force | Out-Null
}

if (-not (Test-Path $stderrLog)) {
    New-Item -ItemType File -Path $stderrLog -Force | Out-Null
}

Start-Process -FilePath $php `
    -ArgumentList @('-S', "127.0.0.1:$Port", '-t', 'public', $routerScript) `
    -WorkingDirectory $projectRoot `
    -WindowStyle Hidden `
    -RedirectStandardOutput $stdoutLog `
    -RedirectStandardError $stderrLog | Out-Null

Write-Host "Offline testing server started on http://127.0.0.1:$Port"
Write-Host "SQLite database: $dbPath"

