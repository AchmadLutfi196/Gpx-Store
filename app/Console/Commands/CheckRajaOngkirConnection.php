<?php

namespace App\Console\Commands;

use App\Services\RajaOngkirService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CheckRajaOngkirConnection extends Command
{
    protected $signature = 'rajaongkir:check';
    protected $description = 'Check RajaOngkir API connection';

    protected $rajaOngkir;

    public function __construct(RajaOngkirService $rajaOngkir)
    {
        parent::__construct();
        $this->rajaOngkir = $rajaOngkir;
    }

    public function handle()
    {
        $this->info('Checking RajaOngkir API connection...');
        
        // Test the connection first
        $connectionTest = $this->rajaOngkir->checkConnection();
        if ($connectionTest['success']) {
            $this->info('✅ RajaOngkir API connection successful.');
        } else {
            $this->error('❌ RajaOngkir API connection failed: ' . $connectionTest['message']);
        }
        
        // Display configuration
        $this->info("\nRajaOngkir Configuration:");
        $this->table(
            ['Setting', 'Value'],
            [
                ['API Key', config('services.rajaongkir.key') ? '******' . substr(config('services.rajaongkir.key'), -4) : 'Not set'],
                ['Base URL', config('services.rajaongkir.sandbox') ? config('services.rajaongkir.sandbox_url') : config('services.rajaongkir.url')],
                ['Mode', config('services.rajaongkir.sandbox') ? 'Sandbox' : 'Production'],
            ]
        );
        
        // Try Komerce API directly
        $this->info("\nTrying direct connection to Komerce API...");
        
        try {
            $response = Http::withHeaders([
                'key' => config('services.rajaongkir.key')
            ])->get('https://api.komerce.id/api/ongkir/v2/province');
            
            if ($response->successful()) {
                $this->info('✅ Komerce API connection successful.');
                $this->info('Sample province data:');
                $data = $response->json();
                
                // Show sample data if available
                if (isset($data['data']) && count($data['data']) > 0) {
                    $this->info('First province: ' . $data['data'][0]['province']);
                }
                
                return 0;
            } else {
                $this->error('❌ Komerce API connection failed: Status ' . $response->status());
                $this->error('Response: ' . $response->body());
            }
        } catch (\Exception $e) {
            $this->error('❌ Komerce API connection exception: ' . $e->getMessage());
        }
        
        return 1;
    }
}
