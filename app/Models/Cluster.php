<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cluster extends Model
{
    use HasFactory;

    protected $fillable = ['url', 'token', 'cacert'];

    public function user() {

        return $this->belongsTo(User::class, 'user_id');
    }
}
