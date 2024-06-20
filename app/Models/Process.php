<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Process extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'name',
        'description'
    ];

    public function fields() {
        return $this->hasMany(Field::class); // Указывает на наличие множества полей, связанных с процессом
    }
}
