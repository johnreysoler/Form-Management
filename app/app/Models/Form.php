<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Status; 
use App\Models\Prerequisite; 
use App\Models\FormSection;
use App\Models\Respondent; 
use App\Models\Response; 
use App\Models\Assignment; 
class Form extends Model
{
    use HasFactory;

    public function statuses(){
        return $this->belongsTo(Status::class,'status_id','id');
    }

    public function sections(){
        return $this->hasMany(FormSection::class,'form_id','id');
    }

    public function respondents(){
        return $this->hasMany(Respondent::class,'form_id','id');
    }

    public function prerequisites(){
        return $this->hasMany(Prerequisite::class,'form_id','id');
    }

    public function responses(){
        return $this->hasMany(Response::class,'form_id','id');
    }

    public function assignments(){
        return $this->belongsTo(Assignment::class,'assignment_id','id');
    }
}
