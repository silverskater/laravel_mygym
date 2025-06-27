<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

}
