#!/bin/bash

# TaskFlow - Script de instalación para Linux (CachyOS, Ubuntu, CentOS, Fedora, Debian, Arch)
# Uso: chmod +x setup.sh && ./setup.sh

set -e

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # Sin color

log_info() { echo -e "${BLUE}[INFO]${NC} $1"; }
log_ok() { echo -e "${GREEN}[OK]${NC} $1"; }
log_warn() { echo -e "${YELLOW}[WARN]${NC} $1"; }
log_error() { echo -e "${RED}[ERROR]${NC} $1"; }

detect_distro() {
    if [[ -f /etc/os-release ]]; then
        . /etc/os-release
        DISTRO=$ID
        VERSION=$VERSION_ID
    else
        log_error "No se pudo detectar la distribución"
        exit 1
    fi
    log_info "Distribución detectada: $DISTRO $VERSION"
}

install_dependencies() {
    log_info "Instalando dependencias del sistema..."
    
    case $DISTRO in
        ubuntu|debian|linuxmint|pop|elementary|zorin)
            sudo apt update
            sudo apt install -y php8.2 php8.2-cli php8.2-mongodb php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-gd composer nodejs npm mongodb
            ;;
        centos|rhel|rocky|almalinux|fedora)
            if command -v dnf &> /dev/null; then
                sudo dnf install -y php php-cli php-pecl-mongodb php-xml php-mbstring php-curl php-zip php-gd composer nodejs npm mongodb-server
            else
                sudo yum install -y php php-cli php-pecl-mongodb php-xml php-mbstring php-curl php-zip php-gd composer nodejs npm mongodb-server
            fi
            ;;
        arch|manjaro|endeavouros|garuda|cachyos)
            sudo pacman -Sy --needed --noconfirm php php-mongodb composer nodejs npm mongodb
            ;;
        opensuse*)
            sudo zypper install -y php8 php8-mongodb composer nodejs npm mongodb
            ;;
        *)
            log_warn "Distribución no reconocida ($DISTRO). Intentando con comandos genéricos..."
            log_warn "Es posible que necesites instalar manualmente: PHP 8.2+, Composer, Node.js 18+, MongoDB, extensión PHP mongodb"
            ;;
    esac
}

enable_mongodb() {
    log_info "Habilitando e iniciando MongoDB..."
    
    case $DISTRO in
        ubuntu|debian|linuxmint|pop|elementary|zorin|centos|rhel|rocky|almalinux|fedora|arch|manjaro|endeavouros|garuda|cachyos|opensuse*)
            sudo systemctl enable --now mongod 2>/dev/null || sudo systemctl enable --now mongodb 2>/dev/null || log_warn "No se pudo iniciar MongoDB como servicio. Inícialo manualmente con: mongod --dbpath /var/lib/mongodb"
            ;;
    esac
}

verify_requirements() {
    log_info "Verificando requisitos..."
    
    local missing=0
    
    # PHP
    if command -v php &> /dev/null; then
        PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
        if (( $(echo "$PHP_VERSION >= 8.2" | bc -l) )); then
            log_ok "PHP $PHP_VERSION"
        else
            log_error "PHP $PHP_VERSION (se requiere 8.2+)"
            missing=1
        fi
    else
        log_error "PHP no encontrado"
        missing=1
    fi
    
    # Extensión mongodb
    if php -m | grep -q mongodb; then
        log_ok "Extensión PHP mongodb"
    else
        log_error "Extensión PHP mongodb no cargada"
        missing=1
    fi
    
    # Composer
    if command -v composer &> /dev/null; then
        log_ok "Composer $(composer --version --no-ansi | head -1 | cut -d' ' -f3)"
    else
        log_error "Composer no encontrado"
        missing=1
    fi
    
    # Node.js
    if command -v node &> /dev/null; then
        NODE_VERSION=$(node --version | sed 's/v//' | cut -d'.' -f1)
        if [[ $NODE_VERSION -ge 18 ]]; then
            log_ok "Node.js $(node --version)"
        else
            log_error "Node.js $(node --version) (se requiere 18+)"
            missing=1
        fi
    else
        log_error "Node.js no encontrado"
        missing=1
    fi
    
    # npm
    if command -v npm &> /dev/null; then
        log_ok "npm $(npm --version)"
    else
        log_error "npm no encontrado"
        missing=1
    fi
    
    # MongoDB
    if command -v mongod &> /dev/null; then
        log_ok "MongoDB $(mongod --version | head -1 | cut -d' ' -f3)"
    else
        log_warn "MongoDB no encontrado en PATH (puede estar instalado como servicio)"
    fi
    
    if [[ $missing -eq 1 ]]; then
        log_error "Faltan requisitos. Instálalos e intenta de nuevo."
        exit 1
    fi
}

setup_project() {
    log_info "Configurando proyecto TaskFlow..."
    
    # Dependencias PHP
    log_info "Instalando dependencias PHP (composer install)..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
    
    # Dependencias JS
    log_info "Instalando dependencias JS (npm install)..."
    npm install
    
    # Archivo .env
    if [[ ! -f .env ]]; then
        log_info "Creando .env desde .env.example..."
        cp .env.example .env
    else
        log_warn ".env ya existe, se omite"
    fi
    
    # APP_KEY
    log_info "Generando APP_KEY..."
    php artisan key:generate --force
    
    # Storage link
    log_info "Creando enlace simbólico de storage..."
    php artisan storage:link 2>/dev/null || true
    
    # Compilar assets
    log_info "Compilando assets (npm run build)..."
    npm run build 2>/dev/null || npm run dev 2>/dev/null || log_warn "No se pudieron compilar assets (ejecuta 'npm run dev' manualmente)"
    
    # Permisos
    log_info "Ajustando permisos..."
    chmod -R 775 storage bootstrap/cache 2>/dev/null || true
    
    log_ok "Proyecto configurado correctamente"
}

show_summary() {
    echo
    echo -e "${GREEN}════════════════════════════════════════════════════${NC}"
    echo -e "${GREEN}       ✅ TaskFlow instalado correctamente${NC}"
    echo -e "${GREEN}════════════════════════════════════════════════════${NC}"
    echo
    echo -e "Para iniciar el servidor:"
    echo -e "  ${BLUE}php artisan serve${NC}"
    echo
    echo -e "Luego abre: ${BLUE}http://localhost:8000${NC}"
    echo
    echo -e "Para desarrollo con hot-reload (en otra terminal):"
    echo -e "  ${BLUE}npm run dev${NC}"
    echo
    echo -e "Comandos útiles:"
    echo -e "  ${BLUE}php artisan optimize:clear${NC}  # Limpiar caches"
    echo -e "  ${BLUE}php artisan route:list${NC}      # Ver rutas"
    echo -e "  ${BLUE}php artisan tinker${NC}          # Consola interactiva"
    echo
    echo -e "${YELLOW}⚠️  Asegúrate de que MongoDB esté corriendo:${NC}"
    echo -e "  ${BLUE}sudo systemctl status mongod${NC}  # o 'mongodb' según distro"
    echo
}

main() {
    echo -e "${BLUE}"
    echo "╔════════════════════════════════════════════════════╗"
    echo "║          TaskFlow - Instalador Linux               ║"
    echo "║   Soportado: CachyOS, Ubuntu, CentOS, Fedora, etc  ║"
    echo "╚════════════════════════════════════════════════════╝"
    echo -e "${NC}"
    
    detect_distro
    install_dependencies
    enable_mongodb
    verify_requirements
    setup_project
    show_summary
}

main "$@"