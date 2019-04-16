<?php

namespace App\Clientes;

use Illuminate\Database\Eloquent\Model;

class Clientes extends Model
{

    protected $table = 'clientes';
    protected $fillable = ['nombres', 'apaterno', 'amaterno', 'direccion', 'telefono', 'correo'];

    /*public function collection(){
        return $this->belongsTo(Collection::class);
    }

    public function pictures(){
        return $this->hasMany(Picture::class);
    }

    public function categories(){
        return $this->belongsToMany(Category::class);
    }*/

}
