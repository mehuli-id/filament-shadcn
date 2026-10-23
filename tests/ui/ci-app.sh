#!/usr/bin/env bash
# Membangun aplikasi Laravel 13 + Filament 5 + package ini (dari folder kerja) untuk uji tampilan.
# Susunannya sama dengan proyek uji lokal: shadcn:install, profil di panel, sidebar bisa diciutkan,
# resource User (form hasil generator + kolom tanggal), user admin & staf.
#
#   tests/ui/ci-app.sh <folder-aplikasi> [port]
set -euo pipefail

PACKAGE_DIR="$(cd "$(dirname "$0")/../.." && pwd)"
APP_DIR="${1:?folder aplikasi}"
PORT="${2:-8765}"

composer create-project laravel/laravel:^13.0 "$APP_DIR" --no-interaction --quiet
cd "$APP_DIR"

composer config repositories.filament-shadcn "{\"type\":\"path\",\"url\":\"$PACKAGE_DIR\",\"options\":{\"symlink\":true}}"
composer require filament/filament:^5.0 mehuli/filament-shadcn:@dev -W --no-interaction --quiet

php artisan filament:install --panels --no-interaction
PROVIDER=app/Providers/Filament/AdminPanelProvider.php
perl -0pi -e 's/->login\(\)/->login()\n            ->profile()\n            ->sidebarCollapsibleOnDesktop()/' "$PROVIDER"
php artisan shadcn:install --panel=admin --no-interaction
php artisan make:filament-resource User --generate --no-interaction

perl -pi -e "s#^APP_URL=.*#APP_URL=http://127.0.0.1:$PORT#; s#^APP_ENV=.*#APP_ENV=local#" .env
php artisan migrate --force --no-interaction
php artisan tinker --execute='
    App\Models\User::create(["name" => "Admin", "email" => "admin@example.com", "password" => bcrypt("password")]);
    App\Models\User::create(["name" => "Staf", "email" => "staf@example.com", "password" => bcrypt("password")])
        ->forceFill(["email_verified_at" => now()])->save();
'

npm install --silent
npm run build
