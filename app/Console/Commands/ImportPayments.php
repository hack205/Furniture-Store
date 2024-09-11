<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Payment;
use App\PaymentProviderEnum;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use function Laravel\Prompts\select;

class ImportPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:orders-payments';

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

        $payments = $data['abonos'];
        $totalPayments = count($payments);
        foreach ($payments as $key => $value) {
            $order = Order::where('number', $value['CLAVABF'])->first();
            if($order){
                $payment = Payment::firstOrCreate(
                    [
                        'order_id' => $order->id,
                        'amount' => $value['abono'],
                        'created_at' => $value['fechad'],
                    ]
                    ,
                    [
                        'amount' => $value['abono'],
                        'created_at' => $value['fechad'],
                    ]
                );
                $status = $payment->wasRecentlyCreated ? 'imported' : 'updated';
                $row = $key + 1;
                $this->info("{$row}/{$totalPayments} Payment {$payment->amount} {$status}.");
            }

        }

    }
}
