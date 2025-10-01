#!/usr/bin/env bash
set -euo pipefail

# ATCS Provisioning Script
# This script provisions a full Laravel 12 application with Filament v4, Livewire + Volt + Flux,
# Spatie Permission, Excel, Redis, FFmpeg, Leaflet, Echo, and HLS streaming support.

# Usage (run as root or with sudo):
#   export APP_DIR=/var/www/atcs
#   export APP_URL=https://your-domain
#   export DB_HOST=127.0.0.1
#   export DB_PORT=3306
#   export DB_DATABASE=atcs
#   export DB_USERNAME=atcs
#   export DB_PASSWORD=strong-password
#   bash ops/provision.sh

REQUIRED_VARS=(APP_DIR DB_HOST DB_PORT DB_DATABASE DB_USERNAME DB_PASSWORD)
for v in "${REQUIRED_VARS[@]}"; do
  if [[ -z "${!v:-}" ]]; then
    echo "[ERROR] Missing environment variable: $v" >&2
    exit 1
  fi
done

APP_URL=${APP_URL:-http://localhost}
APP_NAME=${APP_NAME:-ATCS PEMANTAUAN CCTV}
APP_PHP=${APP_PHP:-8.2}

log() { echo -e "\033[1;32m[PROVISION]\033[0m $*"; }
warn() { echo -e "\033[1;33m[WARN]\033[0m $*"; }
err() { echo -e "\033[1;31m[ERROR]\033[0m $*"; }

is_cmd() { command -v "$1" >/dev/null 2>&1; }

detect_pkg_manager() {
  if is_cmd apt-get; then echo apt; return; fi
  if is_cmd dnf; then echo dnf; return; fi
  if is_cmd yum; then echo yum; return; fi
  echo none
}

install_packages() {
  local pm=$(detect_pkg_manager)
  log "Using package manager: $pm"
  case "$pm" in
    apt)
      export DEBIAN_FRONTEND=noninteractive
      apt-get update -y
      apt-get install -y curl ca-certificates lsb-release gnupg git zip unzip software-properties-common ffmpeg redis-server
      add-apt-repository -y ppa:ondrej/php || true
      apt-get update -y
      apt-get install -y php${APP_PHP} php${APP_PHP}-cli php${APP_PHP}-fpm php${APP_PHP}-mbstring php${APP_PHP}-xml \
        php${APP_PHP}-curl php${APP_PHP}-zip php${APP_PHP}-bcmath php${APP_PHP}-gd php${APP_PHP}-sqlite3 php${APP_PHP}-mysql php${APP_PHP}-redis
      ;;
    dnf)
      dnf install -y epel-release git unzip zip curl ffmpeg redis php php-cli php-fpm php-mbstring php-xml php-json php-curl php-zip php-bcmath php-gd php-sqlite3 php-mysqlnd
      systemctl enable --now redis || true
      ;;
    yum)
      yum install -y epel-release git unzip zip curl ffmpeg redis php php-cli php-fpm php-mbstring php-xml php-json php-curl php-zip php-bcmath php-gd php-sqlite3 php-mysqlnd
      systemctl enable --now redis || true
      ;;
    *)
      warn "Unsupported package manager. Please install PHP ${APP_PHP}, Composer, FFmpeg, Redis manually."
      ;;
  esac
}

ensure_composer() {
  if ! is_cmd composer; then
    log "Installing Composer"
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
  fi
  composer -V
}

ensure_node() {
  if ! is_cmd node || ! is_cmd npm; then
    warn "Node.js/npm not found. Please install Node 18+ before building assets."
  else
    node -v && npm -v
  fi
}

create_app() {
  if [[ -d "$APP_DIR" && -n "$(ls -A "$APP_DIR" 2>/dev/null || true)" ]]; then
    warn "$APP_DIR exists and is not empty. Skipping 'composer create-project'."
  else
    log "Creating Laravel 12 app in $APP_DIR"
    mkdir -p "$APP_DIR"
    composer create-project --prefer-dist laravel/laravel:"^12.0" "$APP_DIR"
  fi
}

configure_env() {
  log "Configuring .env"
  cd "$APP_DIR"
  cp -n .env.example .env || true
  php -r 'file_exists(".env") || copy(".env.example", ".env");'
  php artisan key:generate --force
  sed -i "s|^APP_NAME=.*$|APP_NAME=\"${APP_NAME}\"|" .env
  sed -i "s|^APP_URL=.*$|APP_URL=${APP_URL}|" .env
  sed -i "s|^DB_HOST=.*$|DB_HOST=${DB_HOST}|" .env
  sed -i "s|^DB_PORT=.*$|DB_PORT=${DB_PORT}|" .env
  sed -i "s|^DB_DATABASE=.*$|DB_DATABASE=${DB_DATABASE}|" .env
  sed -i "s|^DB_USERNAME=.*$|DB_USERNAME=${DB_USERNAME}|" .env
  sed -i "s|^DB_PASSWORD=.*$|DB_PASSWORD=${DB_PASSWORD}|" .env
  sed -i "s|^BROADCAST_DRIVER=.*$|BROADCAST_DRIVER=redis|" .env
  sed -i "s|^CACHE_DRIVER=.*$|CACHE_DRIVER=redis|" .env
  sed -i "s|^QUEUE_CONNECTION=.*$|QUEUE_CONNECTION=redis|" .env
  sed -i "s|^SESSION_DRIVER=.*$|SESSION_DRIVER=redis|" .env
  if grep -q '^REDIS_CLIENT=' .env; then
    sed -i "s|^REDIS_CLIENT=.*$|REDIS_CLIENT=predis|" .env
  else
    echo "REDIS_CLIENT=predis" >> .env
  fi
  # Mail defaults (use Gmail App Password in production)
  grep -q '^MAIL_MAILER=' .env || echo "MAIL_MAILER=smtp" >> .env
  grep -q '^MAIL_HOST=' .env || echo "MAIL_HOST=smtp.gmail.com" >> .env
  grep -q '^MAIL_PORT=' .env || echo "MAIL_PORT=587" >> .env
  grep -q '^MAIL_USERNAME=' .env || echo "MAIL_USERNAME=your-email@gmail.com" >> .env
  grep -q '^MAIL_PASSWORD=' .env || echo "MAIL_PASSWORD=your-app-password" >> .env
  grep -q '^MAIL_ENCRYPTION=' .env || echo "MAIL_ENCRYPTION=tls" >> .env
  grep -q '^MAIL_FROM_ADDRESS=' .env || echo "MAIL_FROM_ADDRESS=\"your-email@gmail.com\"" >> .env
  grep -q '^MAIL_FROM_NAME=' .env || echo "MAIL_FROM_NAME=\"KILANG PERTAMINA INTERNASIONAL\"" >> .env
}

install_php_packages() {
  log "Installing PHP packages"
  cd "$APP_DIR"
  composer require \
    filament/filament:^4.0 \
    livewire/livewire:^3.5 \
    livewire/volt:^1.0 \
    spatie/laravel-permission:^6.0 \
    maatwebsite/excel:^3.1 \
    predis/predis:^2.2 \
    protonemedia/laravel-ffmpeg:^8.0 \
    inerba/filament-db-config:^1.0

  php artisan vendor:publish --provider="Spatie\\Permission\\PermissionServiceProvider" --force || true
  php artisan vendor:publish --provider="Maatwebsite\\Excel\\ExcelServiceProvider" --force || true
  php artisan vendor:publish --provider="ProtoneMedia\\LaravelFFMpeg\\Support\\ServiceProvider" --force || true
  php artisan vendor:publish --provider="Inerba\\FilamentDbConfig\\FilamentDbConfigServiceProvider" --force || true
}

install_js_packages() {
  if ! is_cmd npm; then warn "Skipping JS packages (npm missing)"; return; fi
  log "Installing JS packages"
  cd "$APP_DIR"
  npm install -D tailwindcss postcss autoprefixer
  npx tailwindcss init -p || true
  npm install leaflet hls.js laravel-echo socket.io-client @livewire/flux
  if is_cmd npm; then npm install -g laravel-echo-server || true; fi
}

install_livewire_starter_kit() {
  log "Installing Livewire + Volt + Flux starter kit"
  cd "$APP_DIR"
  # Prefer official Livewire starter kit if available
  if php artisan | grep -q 'install:livewire'; then
    php artisan install:livewire --no-interaction || true
  else
    # Fallback to Breeze Livewire scaffolding
    composer require laravel/breeze:^2.0 || true
    if php artisan | grep -q 'breeze:install'; then
      php artisan breeze:install livewire --no-interaction || true
    fi
  fi
  php artisan volt:install || true
}

apply_overlay() {
  log "Applying overlay files"
  rsync -av --exclude ".DS_Store" --exclude "vendor/" --exclude "node_modules/" "$(dirname "$0")/overlay/" "$APP_DIR/"
  mkdir -p "$APP_DIR/public/images"
  # Placeholders for required assets
  if [[ ! -f "$APP_DIR/public/images/kilang.png" ]]; then
    convert -size 1920x1080 xc:#0f172a -gravity center -pointsize 72 -fill white -draw "text 0,0 'KILANG'" "$APP_DIR/public/images/kilang.png" 2>/dev/null || true
  fi
  if [[ ! -f "$APP_DIR/public/images/logo-pertamina.png" ]]; then
    convert -size 512x512 xc:white "$APP_DIR/public/images/logo-pertamina.png" 2>/dev/null || true
  fi
}

generate_migrations_and_models() {
  log "Generating models and migrations"
  cd "$APP_DIR"
  php artisan make:model Models/Building -m
  php artisan make:model Models/Room -m
  php artisan make:model Models/Cctv -m
  php artisan make:model Models/Contact -m
  php artisan make:model Models/Message -m
  php artisan make:model Models/SystemNotification -m

  # Replace generated migrations with overlay definitions
  OVERLAY_DIR="$(dirname "$0")/overlay/database/migrations"
  for f in $(ls "$OVERLAY_DIR"/*.php); do
    base=$(basename "$f")
    ts=$(date +%Y_%m_%d_%H%M%S)
    cp "$f" "$APP_DIR/database/migrations/${ts}_${base}"
    sleep 1
  done
}

seed_buildings() {
  log "Adding seeders and seeding buildings"
  cd "$APP_DIR"
  mkdir -p database/seeders
  cp "$(dirname "$0")/overlay/database/seeders/BuildingSeeder.php" database/seeders/BuildingSeeder.php
  if ! grep -q BuildingSeeder database/seeders/DatabaseSeeder.php; then
    sed -i "s|public function run\(\): void\n\s*\{\n|public function run(): void\n    {\n        \$this->call(\\Database\\Seeders\\BuildingSeeder::class);\n|" database/seeders/DatabaseSeeder.php || true
  fi
}

setup_permissions_roles() {
  log "Setting up roles and permissions seed"
  cd "$APP_DIR"
  cat > database/seeders/RolePermissionSeeder.php <<'EOS'
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'dashboard.view',
            'maps.view',
            'location.view',
            'streaming.view',
            'notification.view',
            'message.view',
            'crud.full',
        ];
        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }

        $super = Role::firstOrCreate(['name' => 'Super Admin']);
        $super->givePermissionTo($permissions);

        $ui = Role::firstOrCreate(['name' => 'User Interface']);
        $ui->givePermissionTo([
            'dashboard.view','maps.view','location.view','streaming.view','notification.view','message.view'
        ]);
    }
}
EOS
  if ! grep -q RolePermissionSeeder database/seeders/DatabaseSeeder.php; then
    sed -i "s|public function run\(\): void\n\s*\{\n|public function run(): void\n    {\n        \$this->call(\\Database\\Seeders\\RolePermissionSeeder::class);\n|" database/seeders/DatabaseSeeder.php || true
  fi
}

artisan_configure() {
  log "Running artisan setup"
  cd "$APP_DIR"
  # Merge routes
  if ! grep -q "Route::view('/dashboard'" routes/web.php; then
    echo "" >> routes/web.php
    echo "require __DIR__.'/atcs.php';" >> routes/web.php
    cp "$(dirname "$0")/overlay/routes/atcs.php" routes/atcs.php
  fi
  # Merge API routes
  if ! grep -q "rooms-with-cctv" routes/api.php; then
    cat "$(dirname "$0")/overlay/routes/api.php" >> routes/api.php
  fi
  # OTP routes
  if ! grep -q "OtpController" routes/web.php; then
    echo "require __DIR__.'/otp.php';" >> routes/web.php
    cp "$(dirname "$0")/overlay/routes/otp.php" routes/otp.php
  fi
  php artisan migrate --force
  php artisan db:seed --force
}

install_filament_and_volt() {
  log "Installing Filament and Volt scaffolding"
  cd "$APP_DIR"
  php artisan filament:install || true
  php artisan volt:install || true
}

setup_supervisor_and_systemd() {
  log "Installing Supervisor and systemd service files"
  local pm=$(detect_pkg_manager)
  if [[ "$pm" == "apt" ]]; then
    apt-get install -y supervisor || true
    systemctl enable --now supervisor || true
  fi

  # Supervisor for Laravel queue worker
  mkdir -p /etc/supervisor/conf.d
  cp "$(dirname "$0")/supervisor/laravel-worker.conf" /etc/supervisor/conf.d/laravel-worker.conf || true
  supervisorctl reread || true
  supervisorctl update || true

  # systemd services
  cp "$(dirname "$0")/systemd/laravel-echo-server.service" /etc/systemd/system/laravel-echo-server.service || true
  cp "$(dirname "$0")/systemd/laravel-scheduler.service" /etc/systemd/system/laravel-scheduler.service || true
  systemctl daemon-reload || true
  systemctl enable laravel-echo-server || true
  systemctl enable laravel-scheduler || true
}

post_notes() {
  cat <<EOF

Provisioning completed.

Next steps:
- Ensure Nginx/Apache points to $APP_DIR/public and HTTPS is configured.
- Upload actual images to $APP_DIR/public/images: kilang.png, logo-pertamina.png.
- Configure Gmail with an App Password, then set MAIL_* in .env.
- Build frontend assets: (cd $APP_DIR && npm install && npm run build)
- Start services: systemctl start laravel-echo-server laravel-scheduler; supervisorctl start all
- Create a Filament user and assign the 'Super Admin' role.

EOF
}

main() {
  install_packages
  ensure_composer
  ensure_node
  create_app
  configure_env
  install_php_packages
  install_js_packages
  install_livewire_starter_kit
  apply_overlay
  generate_migrations_and_models
  seed_buildings
  setup_permissions_roles
  artisan_configure
  install_filament_and_volt
  setup_supervisor_and_systemd
  post_notes
}

main "$@"

