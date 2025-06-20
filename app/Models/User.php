<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'balance',
        'image',
        'group',
        'is_client',
        'verification_code',
        'action',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_client' => 'boolean',
            'balance' => 'float',
        ];
    }

    public function shops()
    {
        return $this->hasMany(Shop::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function customerRequests()
    {
        return $this->hasMany(CustomerRequest::class);
    }

    public function responses()
    {
        return $this->hasMany(Response::class);
    }

    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasAnyRole(['super_admin', 'seller']);
    }

    public function info()
    {
        return $this->hasOne(\App\Models\UsersInfo::class, 'user_id');
    }

    public function getFilamentAvatarUrl(): ?string
    {
        // Получаем аватар из UsersInfo, если есть
        $avatar = $this->info()->first()?->avatar;

        // Если есть аватар в UsersInfo — возвращаем полный путь
        if (!empty($avatar)) {
            return asset('storage/' . $avatar);
        }

        // Если есть аватар в User — возвращаем полный путь
        if (!empty($this->avatar)) {
            return asset('storage/' . $this->avatar);
        }

        // Если ничего нет — null (Filament покажет дефолтный)
        return null;
    }
}
