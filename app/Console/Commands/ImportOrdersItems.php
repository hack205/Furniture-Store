<?php

namespace App\Console\Commands;

use App\Models\Item;
use App\Models\Order;
use App\Models\Payment;
use App\PaymentProviderEnum;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use function Laravel\Prompts\select;

class ImportOrdersItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:orders-items';

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

        $items = $data['FICHA'];
        $totalItems = count($items);

        foreach ($items as $key => $item) {
            $order = Order::where('number', $item['NOFICHA'])->first();

            if($order){

                // Order
                $order->update([
                    'agent' => $item['AGENTE'],
                    'total' => $item['totalmerc'],
                    'created_at' => $item['FECHA'],
                    'product' => $item['MERCANCIA'],
                    'route' => $item['norutf'],
                    'payment_conditions' =>$item['CONDPAGO']
                ]);



                // Payment
                if($item['ANTICIPO'] > 0){
                    Payment::firstOrCreate(
                        [
                            'order_id' => $order->id,
                            'amount' => $item['ANTICIPO'],
                            'created_at' => $item['FECHA']
                        ]
                        ,
                        [
                            'amount' => $item['ANTICIPO'],
                            'method' => PaymentProviderEnum::EFECTIVO->value
                        ]
                    );
                }


                $status = $order->wasRecentlyCreated ? 'imported' : 'updated';
                $row = $key + 1;
                $this->info("{$row}/{$totalItems} Item {$order->number} {$status}.");
            }
        }
    }
}
