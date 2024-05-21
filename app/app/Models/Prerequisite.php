<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Response; 
use App\Models\Form; 
class Prerequisite extends Model
{
    use HasFactory;

    public function responses(){
        return $this->hasMany(Response::class, 'question_id', 'prerequisite_section_id');
    }

    public function forms(){
        return $this->hasMany(Response::class, 'id', 'form_id');
    }
}
