<?php

namespace App\Filament\Pages;

use Filament\Actions;
use Filament\Pages\Page;
use App\Models\CanvasData;
use Filament\Notifications\Notification;

class SettingsPage extends Page
{
    protected static ?string $title = 'Configuración';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.settings-page';
    protected static ?int $navigationSort = 4;

    public $canvasData = [];

    public function mount()
    {
        $canvasData = CanvasData::first();
        if ($canvasData) {
            $this->canvasData = $canvasData->data;
        }
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('update')
                ->label(__('messages.update'))
                ->action('saveCanvas'),
        ];
    }

    public function saveCanvas()
    {
        $canvasData = CanvasData::firstOrNew([]);
        $canvasData->data = $this->canvasData;
        $canvasData->save();

        Notification::make()
            ->title(__('messages.settings.updated_successfully'))
            ->success()
            ->send();
    }
}
