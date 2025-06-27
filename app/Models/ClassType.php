<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassType extends Model
{
    public function scheduledClasses()
    {
         return $this->hasMany(ScheduledClass::class);
    }
}
