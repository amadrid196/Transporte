<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Load extends Model
{
    use HasFactory;
    use softDeletes;

    protected $guarded=["id"];

   public function tractor(){
       return $this->belongsTo(Tractor::class);
   }

   public function driver(){
       return $this->belongsTo(Driver::class);
   }
   public function broker(){
    return $this->belongsTo(Broker::class);
}

   public function trailer(){
       return $this->belongsTo(Trailer::class);
   }

   public function document(){
       return $this->hasOne(Document::class);
   }
   public function shipper(){//shipper
       return $this->hasMany(Shipper::class)->with("customer");
   }

   public function consignee(){
       return $this->hasMany(Consignee::class)->with("customer");
   }

   public function deductions(){
       return $this->hasMany(Deduction::class);
   }

   public function accessories(){
       return $this->hasMany(AccessorialLoad::class);
   }

   public function messageRecords(){
       return $this->hasMany(MessageRecord::class);
   }
   
   public function customer()
   {
       return $this->belongsTo(Broker::class, 'broker_id');
   }
}
