<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamSetting extends Model
{
    protected $table = 'exam_settings';

    protected $fillable = ['key', 'value'];

    public $timestamps = true;

    public static function get(string $key, $default = null)
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    public static function set(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
