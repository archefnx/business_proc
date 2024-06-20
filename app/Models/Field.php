<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Field extends Model
{
    use HasFactory, SoftDeletes; // Объединение использования трейтов в одну строку

    protected $fillable = [
        'process_id',
        'type_id',
        'name',
        'value',
        'description'
    ];

    public function process() {
        return $this->belongsTo(Process::class); // Связь с моделью Process
    }

    public function type() {
        return $this->belongsTo(Type::class); // Связь с моделью Type
    }
}
