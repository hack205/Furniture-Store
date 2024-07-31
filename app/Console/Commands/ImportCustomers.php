<?php

namespace App\Console\Commands;

use App\Models\Customer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use function Laravel\Prompts\select;

class ImportCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $directory = storage_path('imports');

        $jsonFiles = File::files($directory);

        $filePath = select('Cúal es el archivo a importar?', $jsonFiles); 

        $jsonContents = file_get_contents($filePath);

        $data = json_decode($jsonContents, true);

        if (!is_array($data)) {
            $this->error('El archivo JSON no tiene un formato válido.');
            return;
        }

        $customers = $data['CLIENTE'];
        $totalCustomers = count($customers);
        foreach ($customers as $key => $value) {

            $customer = Customer::firstOrCreate(
                ['name' => $value['NOMBREC']], // Conditions to check
                [
                    'name' => $value['NOMBREC'],
                    'city' => $value['CIUDADC'],
                    'colony' => $value['COLONIAC'],
                    'address' => $value['DIRECC'],
                ]
            );
            $status = $customer->wasRecentlyCreated ? 'imported' : 'updated';
            $row = $key + 1;
            $this->info("{$row}/{$totalCustomers} Customer {$customer->name} {$status}.");
        }
    }
}
