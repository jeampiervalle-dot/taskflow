#!/bin/bash
# =============================================================================
#  TaskFlow - Instalador Automático para Ubuntu / Debian
#  Versión: 2.0
#  Requiere: Ubuntu 22.04+ / Debian 12+
#  Uso: chmod +x setup.sh && sudo ./setup.sh
# =============================================================================

set -e

# ──────────────────────────────────────────────
#  Colores para mensajes
# ──────────────────────────────────────────────
ROJO='\033[0;31m'
VERDE='\033[0;32m'
AMARILLO='\033[1;33m'
AZUL='\033[0;34m'
MAGENTA='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m'

info()  { echo -e "${AZUL}[INFO]${NC}  $1"; }
ok()    { echo -e "${VERDE}[✔]${NC}  $1"; }
warn()  { echo -e "${AMARILLO}[⚠]${NC}  $1"; }
error() { echo -e "${ROJO}[✘]${NC}  $1"; }
titulo() {
    echo
    echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${MAGENTA}  $1${NC}"
    echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo
}

# ──────────────────────────────────────────────
#  Verificar que se ejecute con sudo
# ──────────────────────────────────────────────
if [[ $EUID -ne 0 ]]; then
    error "Este script debe ejecutarse con sudo:"
    echo "  sudo ./setup.sh"
    exit 1
fi

# ──────────────────────────────────────────────
#  1. Detectar sistema operativo
# ──────────────────────────────────────────────
titulo "Detectando sistema operativo"

if [[ -f /etc/os-release ]]; then
    . /etc/os-release
    DISTRO=$ID
    VERSION=$VERSION_ID
else
    error "No se pudo detectar la distribución."
    exit 1
fi

info "Distribución: $DISTRO $VERSION"

if [[ "$DISTRO" != "ubuntu" && "$DISTRO" != "debian" && "$DISTRO" != "linuxmint" && "$DISTRO" != "pop" && "$DISTRO" != "elementary" && "$DISTRO" != "zorin" ]]; then
    warn "Este script está optimizado para Ubuntu/Debian."
    warn "Se intentará la instalación de todos modos..."
fi

# ──────────────────────────────────────────────
#  2. Actualizar repositorios
# ──────────────────────────────────────────────
titulo "Actualizando repositorios del sistema"

apt update -qq

# ──────────────────────────────────────────────
#  3. Instalar PHP 8.3 y extensiones
# ──────────────────────────────────────────────
titulo "Instalando PHP 8.3 y extensiones"

if ! command -v php &> /dev/null || ! php -v | grep -q "PHP 8.3"; then
    info "Agregando repositorio ondrej/php..."

    # Instalar software-properties-common si no está
    apt install -y software-properties-common curl gnupg2 ca-certificates lsb-release > /dev/null 2>&1

    # Agregar PPA de ondrej
    add-apt-repository -y ppa:ondrej/php > /dev/null 2>&1
    apt update -qq

    info "Instalando PHP 8.3 y extensiones requeridas..."
    apt install -y \
        php8.3 \
        php8.3-cli \
        php8.3-common \
        php8.3-xml \
        php8.3-mbstring \
        php8.3-curl \
        php8.3-zip \
        php8.3-gd \
        php8.3-bcmath \
        php8.3-mongodb \
        > /dev/null 2>&1

    ok "PHP 8.3 instalado correctamente"
else
    ok "PHP 8.3 ya está instalado"
fi

# ──────────────────────────────────────────────
#  4. Instalar Composer
# ──────────────────────────────────────────────
titulo "Instalando Composer"

if ! command -v composer &> /dev/null; then
    info "Descargando Composer..."
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer > /dev/null 2>&1
    php -r "unlink('composer-setup.php');"
    ok "Composer $(composer --version --no-ansi | cut -d' ' -f3) instalado"
else
    ok "Composer ya está instalado: $(composer --version --no-ansi | cut -d' ' -f3)"
fi

# ──────────────────────────────────────────────
#  5. Instalar Node.js + npm
# ──────────────────────────────────────────────
titulo "Instalando Node.js y npm"

if ! command -v node &> /dev/null || [[ $(node --version | cut -d'v' -f2 | cut -d'.' -f1) -lt 20 ]]; then
    info "Instalando Node.js 22.x desde NodeSource..."
    curl -fsSL https://deb.nodesource.com/setup_22.x | bash - > /dev/null 2>&1
    apt install -y nodejs > /dev/null 2>&1
    ok "Node.js $(node --version) + npm $(npm --version) instalados"
else
    ok "Node.js $(node --version) + npm $(npm --version) ya están instalados"
fi

# ──────────────────────────────────────────────
#  6. Instalar MongoDB Community Server
# ──────────────────────────────────────────────
titulo "Instalando MongoDB Community Server"

install_mongodb() {
    local MONGO_VERSION="7.0"
    info "Agregando repositorio oficial de MongoDB $MONGO_VERSION..."

    # Importar clave GPG
    curl -fsSL https://www.mongodb.org/static/pgp/server-$MONGO_VERSION.asc | \
        gpg -o /usr/share/keyrings/mongodb-server-$MONGO_VERSION.gpg --dearmor > /dev/null 2>&1

    # Detectar versión de Ubuntu/Debian para el repo
    if [[ "$DISTRO" == "ubuntu" || "$DISTRO" == "linuxmint" || "$DISTRO" == "pop" || "$DISTRO" == "elementary" || "$DISTRO" == "zorin" ]]; then
        UBUNTU_CODENAME=$(lsb_release -cs)
        echo "deb [ arch=amd64,arm64 signed-by=/usr/share/keyrings/mongodb-server-$MONGO_VERSION.gpg ] https://repo.mongodb.org/apt/ubuntu $UBUNTU_CODENAME/mongodb-org/$MONGO_VERSION multiverse" | \
            tee /etc/apt/sources.list.d/mongodb-org-$MONGO_VERSION.list > /dev/null
    elif [[ "$DISTRO" == "debian" ]]; then
        DEBIAN_CODENAME=$(lsb_release -cs)
        echo "deb [ arch=amd64,arm64 signed-by=/usr/share/keyrings/mongodb-server-$MONGO_VERSION.gpg ] https://repo.mongodb.org/apt/debian $DEBIAN_CODENAME/mongodb-org/$MONGO_VERSION main" | \
            tee /etc/apt/sources.list.d/mongodb-org-$MONGO_VERSION.list > /dev/null
    else
        # Fallback a Ubuntu Jammy
        echo "deb [ arch=amd64,arm64 signed-by=/usr/share/keyrings/mongodb-server-$MONGO_VERSION.gpg ] https://repo.mongodb.org/apt/ubuntu jammy/mongodb-org/$MONGO_VERSION multiverse" | \
            tee /etc/apt/sources.list.d/mongodb-org-$MONGO_VERSION.list > /dev/null
    fi

    apt update -qq
    info "Instalando mongodb-org..."
    apt install -y mongodb-org > /dev/null 2>&1
}

if ! command -v mongod &> /dev/null; then
    install_mongodb
    ok "MongoDB $MONGO_VERSION instalado"
else
    ok "MongoDB ya está instalado: $(mongod --version | head -1 | cut -d' ' -f3 | tr -d ',')"
fi

# ──────────────────────────────────────────────
#  7. Iniciar y habilitar MongoDB
# ──────────────────────────────────────────────
titulo "Configurando servicio de MongoDB"

if systemctl is-active --quiet mongod 2>/dev/null; then
    ok "MongoDB ya está en ejecución"
else
    info "Iniciando MongoDB..."
    systemctl enable --now mongod > /dev/null 2>&1 || true
    sleep 2
    if systemctl is-active --quiet mongod 2>/dev/null; then
        ok "MongoDB iniciado correctamente"
    else
        warn "No se pudo iniciar MongoDB automáticamente."
        warn "Ejecuta manualmente: sudo systemctl start mongod"
    fi
fi

# ──────────────────────────────────────────────
#  8. Verificar requisitos
# ──────────────────────────────────────────────
titulo "Verificando requisitos"

errores=0

# PHP
if command -v php &> /dev/null; then
    PHP_VER=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
    if (( $(echo "$PHP_VER >= 8.2" | bc -l) )); then
        ok "PHP $PHP_VER"
    else
        error "PHP $PHP_VER (se requiere 8.2+)"
        ((errores++))
    fi
else
    error "PHP no está instalado"
    ((errores++))
fi

# Extensión mongodb
if php -m | grep -q mongodb; then
    ok "Extensión PHP mongodb"
else
    error "Extensión PHP mongodb no está cargada"
    error "Ejecuta: sudo apt install php8.3-mongodb"
    ((errores++))
fi

# Composer
if command -v composer &> /dev/null; then
    ok "Composer $(composer --version --no-ansi | head -1 | cut -d' ' -f3)"
else
    error "Composer no está instalado"
    ((errores++))
fi

# Node.js
if command -v node &> /dev/null; then
    NODE_VER=$(node --version | sed 's/v//' | cut -d'.' -f1)
    if [[ $NODE_VER -ge 18 ]]; then
        ok "Node.js $(node --version)"
    else
        error "Node.js $(node --version) (se requiere 18+)"
        ((errores++))
    fi
else
    error "Node.js no está instalado"
    ((errores++))
fi

# npm
if command -v npm &> /dev/null; then
    ok "npm $(npm --version)"
else
    error "npm no está instalado"
    ((errores++))
fi

# MongoDB
if command -v mongod &> /dev/null; then
    ok "MongoDB $(mongod --version | head -1 | cut -d' ' -f3 | tr -d ',')"
else
    error "MongoDB no está instalado"
    ((errores++))
fi

if [[ $errores -gt 0 ]]; then
    echo
    error "Se encontraron $errores error(es). Corrige los problemas y vuelve a ejecutar el script."
    exit 1
fi

# ──────────────────────────────────────────────
#  9. Configurar proyecto
# ──────────────────────────────────────────────
titulo "Configurando proyecto TaskFlow"

PROJECT_DIR="$(cd "$(dirname "$0")" && pwd)"
cd "$PROJECT_DIR"

info "Directorio del proyecto: $PROJECT_DIR"

# 9.1 Instalar dependencias PHP
info "Instalando dependencias PHP (composer install)..."
sudo -u "$SUDO_USER" composer install --no-interaction --prefer-dist --optimize-autoloader 2>&1 | tail -1
ok "Dependencias PHP instaladas"

# 9.2 Instalar dependencias JS
info "Instalando dependencias JS (npm install)..."
sudo -u "$SUDO_USER" npm install 2>&1 | tail -1
ok "Dependencias JS instaladas"

# 9.3 Crear archivo .env
if [[ ! -f .env ]]; then
    info "Creando .env desde .env.example..."
    sudo -u "$SUDO_USER" cp .env.example .env
    ok "Archivo .env creado"
else
    ok "Archivo .env ya existe (se omite)"
fi

# 9.4 Generar APP_KEY
info "Generando APP_KEY..."
sudo -u "$SUDO_USER" php artisan key:generate --force 2>&1 | tail -1
ok "APP_KEY generada"

# 9.5 Crear enlace simbólico de storage
info "Creando enlace simbólico de storage..."
sudo -u "$SUDO_USER" php artisan storage:link 2>/dev/null && ok "Storage link creado" || warn "Storage link ya existe"

# 9.6 Compilar assets
info "Compilando assets (npm run build)..."
sudo -u "$SUDO_USER" npm run build 2>&1 | tail -3
ok "Assets compilados correctamente"

# 9.7 Ajustar permisos
info "Ajustando permisos de storage y cache..."
chown -R "$SUDO_USER":"$SUDO_USER" storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
ok "Permisos ajustados"

# ──────────────────────────────────────────────
#  10. Resumen final
# ──────────────────────────────────────────────
titulo "✅ TaskFlow instalado correctamente"

echo
echo -e "  ${CYAN}Resumen de la instalación:${NC}"
echo
echo -e "  ${VERDE}PHP:${NC}       $(php -v | head -1 | cut -d' ' -f2)"
echo -e "  ${VERDE}Composer:${NC}  $(composer --version --no-ansi | head -1 | cut -d' ' -f3)"
echo -e "  ${VERDE}Node.js:${NC}   $(node --version)"
echo -e "  ${VERDE}npm:${NC}       $(npm --version)"
echo -e "  ${VERDE}MongoDB:${NC}   $(mongod --version | head -1 | cut -d' ' -f3 | tr -d ',')"
echo

echo -e "  ${CYAN}Para iniciar el servidor de desarrollo:${NC}"
echo
echo -e "    ${AMARILLO}php artisan serve${NC}"
echo
echo -e "  Luego abre: ${AZUL}http://localhost:8000${NC}"
echo
echo -e "  ${CYAN}Comandos útiles:${NC}"
echo
echo -e "    ${AMARILLO}npm run dev${NC}          Hot-reload de assets (en otra terminal)"
echo -e "    ${AMARILLO}php artisan optimize:clear${NC}  Limpiar cachés"
echo -e "    ${AMARILLO}php artisan route:list${NC}      Listar rutas"
echo -e "    ${AMARILLO}php artisan tinker${NC}          Consola interactiva"
echo

# Estado de MongoDB
if systemctl is-active --quiet mongod 2>/dev/null; then
    echo -e "  ${VERDE}✔ MongoDB está corriendo${NC}"
else
    echo -e "  ${ROJO}✘ MongoDB NO está corriendo${NC}"
    echo -e "  Ejecuta: ${AMARILLO}sudo systemctl start mongod${NC}"
fi

echo

# ──────────────────────────────────────────────
#  11. Preguntar si iniciar servidor
# ──────────────────────────────────────────────
read -p "$(echo -e "${CYAN}¿Deseas iniciar el servidor ahora? (s/N): ${NC}")" -n 1 -r
echo
if [[ $REPLY =~ ^[Ss]$ ]]; then
    echo
    titulo "Iniciando servidor en http://localhost:8000"
    echo -e "  Presiona ${AMARILLO}Ctrl+C${NC} para detener el servidor."
    echo
    sudo -u "$SUDO_USER" php artisan serve
fi
