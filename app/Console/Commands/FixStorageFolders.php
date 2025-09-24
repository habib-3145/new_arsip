<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixStorageFolders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:storage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Buat folder storage/framework yang hilang (sessions, cache, views)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $paths = [
            storage_path('framework/sessions'),
            storage_path('framework/cache'),
            storage_path('framework/views'),
        ];

        foreach ($paths as $path) {
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
                $this->info("Folder dibuat: {$path}");
            } else {
                $this->info("Folder sudah ada: {$path}");
            }
        }

        $this->info('Semua folder storage/framework sudah dicek!');
    }
}
