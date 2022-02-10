<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Consignee extends Model
{
    use HasFactory;
    use softDeletes;

    protected $guarded=["id"];

    public function customer(){
        return $this->belongsTo(Customer::class);
    }

    public function loads(){
        return $this->belongsTo(Load::class);
    }
}
