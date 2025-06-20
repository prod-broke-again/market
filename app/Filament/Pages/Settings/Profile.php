<?php

namespace App\Filament\Pages\Settings;

use Filament\Forms;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Illuminate\Support\Facades\Auth;
use App\Models\UsersInfo;
use Filament\Forms\Form;
use Filament\Notifications\Notification;

class Profile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationLabel = 'Профиль';
    protected static ?string $title = 'Профиль';
    protected static ?string $navigationGroup = 'Настройки';
    protected static string $view = 'filament.pages.settings.profile';

    public ?array $data = [];

    public function mount(): void
    {
        $user = Auth::user();
        $usersInfo = UsersInfo::where('user_id', $user->id)->first();

        $this->data = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'legal_name' => $usersInfo?->legal_name,
            'inn' => $usersInfo?->inn,
            'kpp' => $usersInfo?->kpp,
            'ogrn' => $usersInfo?->ogrn,
            'adress_ur' => $usersInfo?->adress_ur,
            'avatar' => $user->avatar ? [$user->avatar] : null,
            'avatar_ur' => $usersInfo?->avatar ? [$usersInfo->avatar] : null,
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Личные данные')
                ->schema([
                    Forms\Components\TextInput::make('name')->label('Имя')->required(),
                    Forms\Components\TextInput::make('email')->label('Email')->email()->required(),
                    Forms\Components\TextInput::make('phone')->label('Телефон'),
                    Forms\Components\FileUpload::make('avatar')
                        ->label('Аватар')
                        ->image()
                        ->avatar()
                        ->imageEditor()
                        ->circleCropper()
                        ->disk('public')
                        ->directory('avatars'),
                ])->columns(2),
            Forms\Components\Section::make('Юридическая информация')
                ->schema([
                    Forms\Components\TextInput::make('legal_name')->label('Наименование организации / ФИО')->required(),
                    Forms\Components\TextInput::make('inn')->label('ИНН')->required(),
                    Forms\Components\TextInput::make('kpp')->label('КПП'),
                    Forms\Components\TextInput::make('ogrn')->label('ОГРН/ОГРНИП')->required(),
                    Forms\Components\TextInput::make('adress_ur')->label('Юридический адрес')->required(),
                    Forms\Components\FileUpload::make('avatar_ur')
                        ->label('Логотип/Аватар юр. лица')
                        ->image()
                        ->avatar()
                        ->imageEditor()
                        ->circleCropper()
                        ->disk('public')
                        ->directory('avatars'),
                ])->columns(2),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema($this->getFormSchema())
            ->statePath('data');
    }

    public function saveProfile()
    {
        $data = $this->form->getState();

        $avatar = is_array($data['avatar']) ? $data['avatar'][0] ?? null : $data['avatar'];
        $avatar_ur = is_array($data['avatar_ur']) ? $data['avatar_ur'][0] ?? null : $data['avatar_ur'];
        
        $user = Auth::user();
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'avatar' => $avatar,
        ]);

        $usersInfo = UsersInfo::firstOrCreate(
            ['user_id' => $user->id],
            []
        );
        $usersInfo->update([
            'legal_name' => $data['legal_name'],
            'inn' => $data['inn'],
            'kpp' => $data['kpp'],
            'ogrn' => $data['ogrn'],
            'adress_ur' => $data['adress_ur'],
            'avatar' => $avatar_ur,
        ]);

        // После сохранения — обновить данные для формы!
        $this->data['avatar'] = $avatar ? [$avatar] : null;
        $this->data['avatar_ur'] = $avatar_ur ? [$avatar_ur] : null;

        Notification::make()
            ->title('Профиль успешно обновлён!')
            ->success()
            ->send();
    }
}
