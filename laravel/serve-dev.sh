#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")"
mkdir -p storage/framework/tmp
cd public
exec php -d "upload_tmp_dir=$(cd .. && pwd)/storage/framework/tmp" -S 127.0.0.1:8000 ../vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php
