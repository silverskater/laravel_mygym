<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ScheduledClass extends Model
{

    /** @use HasFactory<\Database\Factories\ScheduledClassFactory> */
    use HasFactory;

    protected $guarded = NULL;

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    /**
     * @extends \Illuminate\Database\Eloquent\Relations\Relation<User, ScheduledClass>
     */
    public function instructor() : BelongsTo {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function classType() : BelongsTo {
        return $this->belongsTo(ClassType::class);
    }

    public function members() : BelongsToMany {
        return $this->belongsToMany(User::class, 'bookings', 'scheduled_class_id', 'user_id')
            //->withPivot('status', 'created_at')
            ->withTimestamps();
    }

    public function scopeUpcoming(Builder $query) {
        return $query->where('scheduled_at', '>', now())
            ->where('scheduled_classes.status', 'scheduled');
    }

    public function scopeNotBookedByUser(Builder $query, int $userId) {
        return $query->whereDoesntHave('members', function (Builder $query) use ($userId) {
            $query->where('user_id', $userId);
        });
    }
}
