<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolYear extends Model
{
    protected $fillable = ['name','start_date','end_date','status'];

    public function sections()
    {
        return $this->hasMany(Section::class, 'school_year_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'school_year_id');
    }

    protected static function booted()
    {
        static::creating(function ($sy) {
            if (empty($sy->name) && $sy->start_date && $sy->end_date) {
                $sy->name = date('Y', strtotime($sy->start_date)) . '-' . date('Y', strtotime($sy->end_date));
            }
        });
    }
}
