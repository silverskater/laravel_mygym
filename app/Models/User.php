<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The roles that a user can have.
     *
     * @var list<string>
     */
    public const ROLES = ['member', 'instructor', 'admin'];

    /**
     * The default role for a user.
     *
     * @var string
     */
    public const DEFAULT_ROLE = 'member';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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
        ];
    }

    /**
     * Define a one-to-many relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<ScheduledClass, $this>
     */
    public function scheduledClasses(): HasMany
    {
        return $this->hasMany(ScheduledClass::class, 'instructor_id');
    }

    public function bookings(): BelongsToMany
    {
        return $this->belongsToMany(ScheduledClass::class, 'bookings', 'user_id', 'scheduled_class_id')
            ->withTimestamps();
    }
}
