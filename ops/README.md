## ATCS Production Provisioning

This folder contains scripts and configs to provision the ATCS PEMANTAUAN CCTV application on a fresh VPS. The scripts will:

- Install PHP, Composer, FFmpeg, Redis, Node.js (if missing)
- Create a Laravel 12 app
- Install required PHP and JS packages (Filament v4, Livewire + Volt + Flux, Spatie Permission, Laravel Excel, Redis, FFmpeg integration, Leaflet, Echo, hls.js, TailwindCSS v4)
- Copy overlay files (welcome page and configs)
- Prepare Supervisor and systemd services

### Prerequisites
- Ubuntu 22.04/24.04 (root or sudo)
- MySQL/MariaDB database ready (DB name/user/password)
- DNS pointing to the server (optional)

### Quick Start (Production)
1) Copy repo to your VPS and `cd` into it.
2) Export DB and app variables, then run provision script:

```bash
export APP_DIR=/var/www/atcs
export APP_URL=https://your-domain
export DB_HOST=127.0.0.1
export DB_PORT=3306
export DB_DATABASE=atcs
export DB_USERNAME=atcs
export DB_PASSWORD=strong-password
sudo bash ops/provision.sh
```

3) Configure Nginx to point to `$APP_DIR/public` and reload Nginx.

4) Start background services:

```bash
sudo systemctl enable --now laravel-echo-server
sudo supervisorctl reread && sudo supervisorctl update && sudo supervisorctl start all
```

5) Build frontend assets:

```bash
cd $APP_DIR
npm install
npm run build
```

6) Create Super Admin in Filament:

```bash
cd $APP_DIR
php artisan make:filament-user # follow prompts, then assign Super Admin role via seeder/Filament
```

### Features provisioned
- Separate Admin (Filament v4) and User Interface (Livewire + Volt + Flux)
- Role & permission (Spatie)
- Redis + Echo broadcasting skeleton
- FFmpeg service scaffolding (RTSP → HLS stored under `public/live`)
- Leaflet map setup (OSM + Satellite toggle)
- Excel export integration
- Gmail SMTP example configuration
- Welcome page per spec

### Security Notes
- Replace Gmail password with an app password; do not keep real secrets in git.
- Ensure proper firewalling and HTTPS.

