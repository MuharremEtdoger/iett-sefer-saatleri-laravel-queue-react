<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class iettsefersaatleri extends Model
{
    protected $fillable = ['id','code','title','html'];
    protected $table = 'iettsefersaatleri';

}
