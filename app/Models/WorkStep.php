<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkStep extends Model
{
    protected $fillable = [
        'step_number', 'title', 'description',
        'icon_name', 'image_path', 'image_url', 'sort_order',
    ];
}
