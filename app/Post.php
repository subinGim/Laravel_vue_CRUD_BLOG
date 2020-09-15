<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Post extends Model
{
    protected $fillable = ['title', 'author', 'content'];
    // protected $dateFormat = 'Y-m-d';
    public function setCreated_atAttribute($value){
        $this->attributes['created_at'] = (new Carbon($value))->format('y/m/d');
    }
    public function setUpdated_atAttribute($value){
        $this->attributes['updated_at'] = (new Carbon($value))->format('y/m/d');
    }
}
