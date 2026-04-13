<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // Kolom yang bisa diisi massal
    protected $fillable = ['category_name', 'description'];

    // Tambah ini untuk kompatibilitas dengan kode blade yang menggunakan $category->name
    public function getNameAttribute()
    {
        return $this->category_name;
    }

    // Relasi ke tickets
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
