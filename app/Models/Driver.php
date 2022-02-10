<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use HasFactory;
    use softDeletes;

    protected $guarded=[];

    public function loads(){
        return $this->hasMany(Load::class);
    }

    public function loads_payable(){
        return $this->hasMany(Load::class)->whereNotIn("status",["Pending","Needs Driver","Dispatched"]);
    }

    public function notes()
    {
        return $this->hasMany(Note::class, 'driver_id');
    }


}
