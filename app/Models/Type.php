<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Type extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'format',
        'description'
    ];

    public function fields() {
        return $this->hasMany(Field::class); // Указывает на наличие множества полей, связанных с данным типом
    }
}
