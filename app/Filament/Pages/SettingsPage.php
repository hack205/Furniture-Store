<?php

namespace App\Filament\Pages;

use Filament\Actions;
use Filament\Pages\Page;
use Filament\Forms\Form;
use App\Models\CanvasData;
use Livewire\Attributes\On;
use Filament\Notifications\Notification;

class SettingsPage extends Page
{
    protected static ?string $title = 'Configuración';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.settings-page';

    protected static ?int $navigationSort = 4;

    public $canvasData = [];

    public function __construct()
    {
        self::setTitle();
    }

    public static function setTitle(): void
    {
        self::$title = __('messages.settings.settings');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('update')
                ->color('primary')
                ->formId('saveButton')
                ->label(__('messages.update')),
        ];
    }



    public function mount()
    {
        $canvasData = CanvasData::first();
        if($canvasData){
            $this->canvasData = $canvasData->data;
        }
    }

    //TODO:: update this data!
    public function update($data)
    {
        $canvasData = CanvasData::firstOrNew([
            'data' => $data
        ]);

        Notification::make()
            ->title(__('messages.settings.updated_successfully'))
            ->success()
            ->send();
    }

}
