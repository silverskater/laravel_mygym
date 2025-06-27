<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class ScheduledClass extends Model
{

    protected $guarded = NULL;

    protected $casts = [
        'date_time' => 'datetime',
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
