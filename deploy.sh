#!/usr/bin/env bash
cd ${0%/*}

mkdir -p storage/framework/views
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/logs

chmod 777 -R storage

mkdir -p public/upload
chmod 777 public/upload

if [ ! -f "./.env" ]; then
  composer install
  cp .env.example .env
  php artisan key:generate
  chmod -R 777 ./storage 
fi
