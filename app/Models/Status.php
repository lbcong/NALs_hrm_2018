<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    public $timestamps = false;
    protected $table = 'statuses';
    protected $fillable = [
        'id','name'
    ];
    public function projects()
    {
        return $this->hasMany(Project::class,'status_id')->where('delete_flag', '=', 0);
    }
}
