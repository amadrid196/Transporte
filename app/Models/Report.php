<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $guarded = [];

    public const FirstPcikUpDate = "FPU";
    public const LastDeliveryDate = "LDD";
    public const InvoiceData = "ID";

    public function origin()
    {
        return $this->hasMany('App\Models\Customer', 'id','orgin_id');
    } 

    public function destination()
    {
        return $this->hasMany('App\Models\Customer',  'id','destination_id');
    }
}
