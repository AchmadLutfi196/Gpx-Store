<?php

namespace App\Filament\Resources\HomeBannerResource\Pages;

use App\Filament\Resources\HomeBannerResource;
use App\Models\HomeBanner;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHomeBanner extends EditRecord
{
    protected static string $resource = HomeBannerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function beforeSave(): void
    {
        // If this banner is being set to active, deactivate all other banners
        if ($this->data['is_active']) {
            HomeBanner::where('id', '!=', $this->record->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);
        }
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
