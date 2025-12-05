<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolYear extends Model
{

    use SoftDeletes;

    protected $fillable = ['name','start_date','end_date','status'];

    protected $dates = ['deleted_at'];

    public function sections()
    {
        return $this->hasMany(Section::class, 'school_year_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'school_year_id');
    }

    public function curricula()
    {
        return $this->hasMany(Curriculum::class, 'school_year_id');
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
