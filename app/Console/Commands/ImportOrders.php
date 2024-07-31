<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Customer;
use App\OrderStatusEnum;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use function Laravel\Prompts\select;

class ImportOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:orders';

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
    
        $orders = $data['clieficha'];
        $totalOrders = count($orders);
        foreach ($orders as $key => $value) {

            $customerId = Customer::where('name', $value['nombrec'])->first('id');

            if($customerId){
                $customerId = $customerId->id;
                $order = Order::firstOrCreate(
                    [
                        'number' => $value['clavef'],
                        'customer_id' => $customerId,
                    ]
                    ,
                    [
                        'number' => $value['clavef'],
                        'customer_id' => $customerId,
                        'status' => OrderStatusEnum::PENDING->value
                    ]
                );
    
                $status = $order->wasRecentlyCreated ? 'imported' : 'updated';
                $row = $key + 1;
                $this->info("{$row}/{$totalOrders} Order {$order->number} {$status}.");
            }
         }
    }
}
