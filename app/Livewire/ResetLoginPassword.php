<?php

namespace App\Livewire;

use App\Ragnarok\Login;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;
use Illuminate\Contracts\View\View;

class ResetLoginPassword extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Reset Account Password')
                    ->description('Prevent abuse by limiting the number of requests per period')
                    ->schema([
                    Forms\Components\TextInput::make('Username'),
                    Forms\Components\TextInput::make('New Password'),
                ])
            ])
            ->statePath('data');
    }

    public function save(): void
    {
//        $data = $this->form->getState();
//
//        $this->record->update($data);
    }

    public function render(): View
    {
        return view('livewire.reset-login-password');
    }
}
