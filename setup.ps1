<#
.SYNOPSIS
    Instalador automatico de TaskFlow (Laravel 11 + MongoDB).

.DESCRIPTION
    Este script prepara el proyecto desde cero en una PC nueva:
      * Verifica requisitos (PHP, Composer, Node, npm)
      * Instala dependencias de PHP (composer)
      * Instala dependencias de JS (npm)
      * Crea el archivo .env a partir de .env.example
      * Genera la APP_KEY
      * Prepara los symlinks de storage

    Es IDEMPOTENTE: se puede volver a ejecutar sin romper nada.

.NOTES
    Uso (PowerShell):
        .\setup.ps1
#>

# ==========================================================
#  CONFIGURACION VISUAL
# ==========================================================
$ErrorActionPreference = 'Stop'
$Host.UI.RawUI.WindowTitle = "TaskFlow - Instalador"

function Write-Step($msg) {
    Write-Host ""
    Write-Host "==> $msg" -ForegroundColor Cyan
}
function Write-Ok($msg) {
    Write-Host "  [OK] $msg" -ForegroundColor Green
}
function Write-Warn($msg) {
    Write-Host "  [!] $msg" -ForegroundColor Yellow
}
function Write-Err($msg) {
    Write-Host "  [X] $msg" -ForegroundColor Red
}

# ==========================================================
#  1. VERIFICAR REQUISITOS
# ==========================================================
Write-Step "Verificando requisitos..."

# PHP
try {
    $phpVersion = (& php -r "echo PHP_VERSION;" 2>&1)
    if ($LASTEXITCODE -eq 0) {
        Write-Ok "PHP $phpVersion"
    } else { throw }
} catch {
    Write-Err "PHP no esta instalado o no esta en PATH."
    Write-Host "      Descargalo de: https://windows.php.net/download/ (PHP 8.2+)" -ForegroundColor Gray
    exit 1
}

# Extension mongodb
$hasMongoExt = php -m 2>$null | Where-Object { $_ -eq "mongodb" }
if (-not $hasMongoExt) {
    Write-Warn "La extension 'mongodb' de PHP no esta habilitada."
    Write-Host "      Descarga el driver en: https://pecl.php.net/package/mongodb" -ForegroundColor Gray
    Write-Host "      (Necesario para que Laravel se conecte a MongoDB)" -ForegroundColor Gray
} else {
    Write-Ok "Extension mongodb de PHP"
}

# Composer
try {
    $composerVersion = (& composer --version 2>&1) | Select-Object -First 1
    if ($LASTEXITCODE -eq 0) { Write-Ok "Composer ($composerVersion)" }
    else { throw }
} catch {
    Write-Err "Composer no esta instalado."
    Write-Host "      Instalalo desde: https://getcomposer.org/Composer-Setup.exe" -ForegroundColor Gray
    exit 1
}

# Node + npm
try {
    $nodeVersion = (& node --version 2>&1)
    $npmVersion  = (& npm --version 2>&1)
    if ($LASTEXITCODE -eq 0) { Write-Ok "Node $nodeVersion / npm $npmVersion" }
    else { throw }
} catch {
    Write-Warn "Node.js no esta instalado (opcional si no vas a compilar assets Vite)."
    Write-Host "      Descargalo de: https://nodejs.org/" -ForegroundColor Gray
}

# ==========================================================
#  2. INSTALAR DEPENDENCIAS PHP
# ==========================================================
Write-Step "Instalando dependencias de PHP (composer install)..."
if (Test-Path "composer.json") {
    composer install --no-interaction --prefer-dist
    if ($LASTEXITCODE -eq 0) { Write-Ok "Dependencias de PHP instaladas" }
    else { Write-Err "Fall贸 composer install"; exit 1 }
} else {
    Write-Err "No se encontro composer.json. Estas en la raiz del proyecto?"
    exit 1
}

# ==========================================================
#  3. INSTALAR DEPENDENCIAS JS
# ==========================================================
if (Test-Path "package.json") {
    Write-Step "Instalando dependencias de JS (npm install)..."
    npm install
    if ($LASTEXITCODE -eq 0) { Write-Ok "Dependencias de JS instaladas" }
    else { Write-Warn "npm install tuvo advertencias (puede ser normal)" }
}

# ==========================================================
#  4. ARCHIVO .ENV
# ==========================================================
Write-Step "Preparando archivo .env..."
if (-not (Test-Path ".env")) {
    if (Test-Path ".env.example") {
        Copy-Item ".env.example" ".env"
        Write-Ok ".env creado desde .env.example"
    } else {
        Write-Err "No existe .env.example. No puedo crear .env."
        exit 1
    }
} else {
    Write-Ok ".env ya existe, se conserva"
}

# ==========================================================
#  5. APP_KEY
# ==========================================================
Write-Step "Generando APP_KEY..."
$currentKey = (Select-String -Path ".env" -Pattern "^APP_KEY=(.+)$" -ErrorAction SilentlyContinue)
if (-not $currentKey -or [string]::IsNullOrWhiteSpace($currentKey.Matches.Groups[1].Value)) {
    php artisan key:generate --no-interaction | Out-Null
    Write-Ok "APP_KEY generada"
} else {
    Write-Ok "APP_KEY ya existe, se conserva"
}

# ==========================================================
#  6. SYMLINK DE STORAGE
# ==========================================================
Write-Step "Creando enlace simbolico de storage..."
if (-not (Test-Path "public\storage")) {
    php artisan storage:link | Out-Null
    Write-Ok "public\storage creado"
} else {
    Write-Ok "public\storage ya existe"
}

# ==========================================================
#  7. CACHE DE CONFIGURACION
# ==========================================================
Write-Step "Limpiando caches de Laravel..."
php artisan config:clear  | Out-Null
php artisan cache:clear   | Out-Null
php artisan view:clear    | Out-Null
php artisan route:clear   | Out-Null
Write-Ok "Caches limpiados"

# ==========================================================
#  8. RECORDATORIO DE MONGODB
# ==========================================================
Write-Step "Recordatorio sobre MongoDB..."
Write-Host "  Este proyecto usa MongoDB como base de datos." -ForegroundColor White
Write-Host "  Asegurate de que el servicio este corriendo:" -ForegroundColor White
Write-Host ""
Write-Host "      # Si lo instalaste como servicio de Windows, ya inicia solo." -ForegroundColor Gray
Write-Host "      # Si lo inicias manualmente:" -ForegroundColor Gray
Write-Host "      mongod --dbpath C:\data\db" -ForegroundColor Gray
Write-Host ""
Write-Host "  La base de datos se llama: taskflow" -ForegroundColor White
Write-Host "  La URI por defecto es:     mongodb://127.0.0.1:27017" -ForegroundColor White
Write-Host "  (puedes cambiarlo en .env)" -ForegroundColor Gray

# ==========================================================
#  RESUMEN FINAL
# ==========================================================
Write-Host ""
Write-Host "========================================================" -ForegroundColor Green
Write-Host "  INSTALACION COMPLETADA" -ForegroundColor Green
Write-Host "========================================================" -ForegroundColor Green
Write-Host ""
Write-Host "  Para arrancar el servidor de desarrollo:" -ForegroundColor White
Write-Host "      php artisan serve" -ForegroundColor Yellow
Write-Host ""
Write-Host "  Abre en tu navegador:  http://localhost:8000" -ForegroundColor White
Write-Host ""
Write-Host "  (Opcional) Para compilar los assets frontend:" -ForegroundColor White
Write-Host "      npm run dev" -ForegroundColor Yellow
Write-Host ""
