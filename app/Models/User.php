<?php

namespace App\Models;

use App\Notifications\AccountPasswordReset;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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

    /** Soglia facoltativa e consumi delle traduzioni assistite. */
    public function translationLimit(): HasOne
    {
        return $this->hasOne(HostTranslationLimit::class);
    }

    public function translationUsages(): HasMany
    {
        return $this->hasMany(TranslationUsage::class);
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
        return $this->isAdmin() || ($this->isHost() && $this->hasActiveAccess());
    }

    /** Collega la scadenza facoltativa e verifica l'accesso senza alterare i soggiorni. */
    public function hostAccess(): HasOne
    {
        return $this->hasOne(HostAccess::class);
    }

    public function hasActiveAccess(): bool
    {
        return $this->is_active && ($this->hostAccess?->expires_at === null || $this->hostAccess->expires_at->isFuture());
    }

    #[Scope]
    protected function activeAccess(Builder $query): void
    {
        $query->where('is_active', true)->where(function (Builder $query): void {
            $query->whereDoesntHave('hostAccess')->orWhereHas('hostAccess', function (Builder $query): void {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
        });
    }

    #[Scope]
    protected function inactiveAccess(Builder $query): void
    {
        $query->where(function (Builder $query): void {
            $query->where('is_active', false)->orWhereHas('hostAccess', fn (Builder $query) => $query->where('expires_at', '<=', now()));
        });
    }

    /** Seleziona soltanto gli accessi operativi che scadono nei prossimi dieci giorni. */
    #[Scope]
    protected function expiringAccess(Builder $query): void
    {
        $query->activeAccess()->whereHas('hostAccess', fn (Builder $query) => $query->whereBetween('expires_at', [now(), now()->addDays(10)]));
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
