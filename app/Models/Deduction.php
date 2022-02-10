<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deduction extends Model
{
    use HasFactory;
    use softDeletes;

    protected $guarded=["id"];

    public function accessorial()
    {
        return $this->belongsTo(Accessorial::class);
    }
}
