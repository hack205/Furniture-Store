<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DatabaseBackup extends Command
{
    protected $signature = 'backup:db';
    protected $description = 'Backup the database periodically';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $host = env('DB_HOST');
        $backupPath = storage_path('backups/' . $database . '_backup_' . now()->format('Y_m_d_H_i_s') . '.sql');

        // Crear directorio de backups si no existe
        if (!file_exists(storage_path('backups'))) {
            mkdir(storage_path('backups'), 0777, true);
        }

        $command = "mysqldump --user={$username} --password={$password} --host={$host} {$database} > {$backupPath}";

        $returnVar = null;
        $output = null;

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            $this->error('The backup process has failed.');
            return 1;
        }

        $this->info('The backup has been successfully created at ' . $backupPath);
        return 0;
    }
}
