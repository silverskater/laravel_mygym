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
        // This defines a many-to-many relationship with ScheduledClass through the bookings table.
        // The pivot table 'bookings' contains additional fields 'status' and 'created_at'.
        // The 'user_id' is the foreign key in the bookings table that references this User model,
        // and 'scheduled_class_id' is the foreign key that references the ScheduledClass model
        // This allows us to retrieve all scheduled classes a user has booked, along with the status
        // and creation date of each booking.
        // The withTimestamps() method ensures that the pivot table will automatically manage the created_at
        // and updated_at timestamps for each booking record.
        // This is useful for tracking when a user booked a class and any updates to the booking
        // status over time.
        // Note: The pivot table 'bookings' should have the columns 'user_id',
        // 'scheduled_class_id', 'status', and 'created_at'.
        // Ensure the bookings table is created with these columns in your migration.
        return $this->belongsToMany(ScheduledClass::class, 'bookings', 'user_id', 'scheduled_class_id')
            // ->withPivot('status', 'created_at')
            ->withTimestamps();
    }
}
