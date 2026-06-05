<?php

namespace App\Console\Commands;

use Illuminate\Foundation\Console\ServeCommand as BaseServeCommand;
use Symfony\Component\Console\Attribute\AsCommand;

use function Illuminate\Support\php_binary;

/**
 * Windows fix: pass an explicit upload_tmp_dir to the PHP built-in server child process.
 * Without this, multipart uploads fail with "unable to create a temporary file".
 */
#[AsCommand(name: 'serve')]
class ServeCommand extends BaseServeCommand
{
    /**
     * @return list<string>
     */
    protected function serverCommand()
    {
        $server = file_exists(base_path('server.php'))
            ? base_path('server.php')
            : __DIR__.'/../../../vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php';

        $command = [
            php_binary(),
        ];

        if (PHP_OS_FAMILY === 'Windows') {
            $tmp = str_replace('\\', '/', storage_path('framework/tmp'));
            if (! is_dir($tmp)) {
                mkdir($tmp, 0777, true);
            }
            $command[] = '-d';
            $command[] = 'upload_tmp_dir='.$tmp;
        }

        $command[] = '-S';
        $command[] = $this->host().':'.$this->port();
        $command[] = $server;

        return $command;
    }
}
