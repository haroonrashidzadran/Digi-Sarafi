<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected string $view = 'filament.pages.settings';

    public array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'settings' => Setting::all()->map(fn ($s) => [
                'key'         => $s->key,
                'value'       => $s->value,
                'description' => $s->description,
            ])->toArray(),
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Repeater::make('settings')
                    ->schema([
                        TextInput::make('key')->required()->label('Key'),
                        TextInput::make('value')->label('Value'),
                        TextInput::make('description')->label('Description'),
                    ])
                    ->columns(3)
                    ->addActionLabel('Add Setting')
                    ->label('System Settings'),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->action('save'),
        ];
    }

    public function save(): void
    {
        $state = $this->form->getState();

        Setting::truncate();

        foreach ($state['settings'] ?? [] as $item) {
            if (!empty($item['key'])) {
                Setting::create([
                    'key'         => $item['key'],
                    'value'       => $item['value'] ?? null,
                    'description' => $item['description'] ?? null,
                ]);
            }
        }

        Notification::make()->title('Settings saved.')->success()->send();
    }
}
