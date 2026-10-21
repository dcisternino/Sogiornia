<?php

namespace App\Models;

use App\Notifications\AccountPasswordReset;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/** Identifica un amministratore o un host con le proprie strutture. */
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    public const ROLE_ADMIN = 'admin';

    public const ROLE_HOST = 'host';

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isHost(): bool
    {
        return $this->role === self::ROLE_HOST;
    }

    public function canWrite(): bool
    {
        return $this->isAdmin() || ($this->isHost() && $this->is_active);
    }

    public function hasPendingInvitation(): bool
    {
        return $this->invited_at !== null && $this->invitation_accepted_at === null;
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new AccountPasswordReset($token));
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'invited_at' => 'datetime',
            'invitation_accepted_at' => 'datetime',
        ];
    }
}
