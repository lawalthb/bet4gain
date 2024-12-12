<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Model;

class Setting extends EloquentModel
{
    protected $fillable = ['key', 'value'];

    public static function get($key)
    {
        return static::where('key', $key)->value('value');
    }
}
