<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Rest;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','date', 'started_at', 'finished_at'];

    public function user(){
		return $this->belongsTo('App\Models\User');
    }

    public function rests(){
		return $this->hasMany('App\Models\Rest');
    }

    
}
