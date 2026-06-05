@echo off
setlocal
cd /d "%~dp0"
if not exist "storage\framework\tmp" mkdir "storage\framework\tmp"
cd public
php -d upload_tmp_dir="%CD%\..\storage\framework\tmp" -S 127.0.0.1:8000 ..\vendor\laravel\framework\src\Illuminate\Foundation\resources\server.php
