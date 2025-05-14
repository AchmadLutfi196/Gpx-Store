<?php

namespace App\Filament\Resources\HomeBannerResource\Pages;

use App\Filament\Resources\HomeBannerResource;
use App\Models\HomeBanner;
use Filament\Resources\Pages\CreateRecord;

class CreateHomeBanner extends CreateRecord
{
    protected static string $resource = HomeBannerResource::class;
    
    protected function beforeCreate(): void
    {
        // If the new banner is active, deactivate all other banners
        if ($this->data['is_active']) {
            HomeBanner::where('is_active', true)->update(['is_active' => false]);
        }
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
