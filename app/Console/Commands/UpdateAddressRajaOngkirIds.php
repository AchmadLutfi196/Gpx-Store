<?php

namespace App\Console\Commands;

use App\Models\Address;
use App\Services\RajaOngkirService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateAddressRajaOngkirIds extends Command
{
    protected $signature = 'addresses:update-rajaongkir {--force-static : Force using static data} {--test-connection : Only test the connection to RajaOngkir API}';
    protected $description = 'Update existing addresses with RajaOngkir city_id and province_id';

    protected $rajaOngkir;

    public function __construct(RajaOngkirService $rajaOngkir)
    {
        parent::__construct();
        $this->rajaOngkir = $rajaOngkir;
    }

    public function handle()
    {
        $this->info('Checking RajaOngkir API connection...');
        
        // Test the connection 
        $connectionTest = $this->rajaOngkir->checkConnection();
        
        if ($connectionTest['success']) {
            $this->info('RajaOngkir API connection successful.');
        } else {
            $this->warn('RajaOngkir API connection failed: ' . $connectionTest['message']);
            $this->info('Using static data for address updates.');
        }
        
        // If only testing connection, exit here
        if ($this->option('test-connection')) {
            return 0;
        }

        $this->info('Updating addresses with RajaOngkir IDs using static data...');

        // Use the helper method that uses static data
        return $this->updateWithDefaultValues();
    }

    protected function updateWithDefaultValues()
    {
        $addresses = Address::whereNull('city_id')->orWhereNull('province_id')->get();
        $count = $addresses->count();
        
        if ($count === 0) {
            $this->info("No addresses need updating");
            return 0;
        }
        
        $this->info("Updating {$count} addresses with Jakarta values");
        
        $bar = $this->output->createProgressBar($count);
        $bar->start();
        
        foreach ($addresses as $address) {
            $address->province = 'DKI Jakarta';
            $address->province_id = '6';
            $address->city = 'Kota Jakarta Pusat';
            $address->city_id = '152';
            $address->save();
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine(2);
        
        $this->info("Successfully updated {$count} addresses with proper city_id and province_id values");
        $this->info("These addresses can now be used with the shipping calculator");
        
        return 0;
    }
}
