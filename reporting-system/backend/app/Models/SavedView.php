<?php
 
 namespace App\Models;
 
 use Illuminate\Database\Eloquent\Model;
 use App\Models\User;
 
 class SavedView extends Model
 {
     protected $fillable = [
         'name',
         'config',
        'version',
        'parent_id',
        'user_id',
        'is_public',
     ];
 
     protected $casts = [
         'config' => 'array',
        'version' => 'integer',
        'is_public' => 'boolean',
        'user_id' => 'integer',
     ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schedule()
    {
        return $this->hasOne(ScheduledReport::class, 'saved_view_id');
    }
 }
