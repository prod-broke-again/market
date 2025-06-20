@php
    use Illuminate\Support\Facades\Auth;
    use App\Models\UsersInfo;
    $user = Auth::user();
    $usersInfo = UsersInfo::where('user_id', $user->id)->first();
@endphp

<x-filament::page>
    {{ $this->form }}
    <div class="flex justify-end mt-6">
        <x-filament::button wire:click="saveProfile" color="primary">
            Сохранить изменения
        </x-filament::button>
    </div>
</x-filament::page>
