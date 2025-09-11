<?php
// app/Models/Announcement.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = ['title', 'content', 'user_id', 'section_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
