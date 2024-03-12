#!/bin/bash

# Iniciar o schedule:work em background
php artisan schedule:work &

# Iniciar o queue:work em background
php artisan queue:work &

# Esperar todos os processos em background terminarem
wait
