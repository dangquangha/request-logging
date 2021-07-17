<?php

namespace Workable\RequestLogging\Models;

use Illuminate\Database\Eloquent\Model;

class RobotsCounterReport extends Model
{
    protected $table = 'robots_counter_reports';

    protected $hidden = ['created_at', 'updated_at'];
}
