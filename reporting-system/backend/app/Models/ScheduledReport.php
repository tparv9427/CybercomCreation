<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduledReport extends Model
{
    protected $fillable = [
        'user_id',
        'saved_view_id',
        'frequency',
        'last_sent_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function savedView()
    {
        return $this->belongsTo(SavedView::class);
    }

    public function scopeDue($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('last_sent_at')
              ->orWhere(function ($sq) {
                $sq->where('frequency', 'daily')->where('last_sent_at', '<=', now()->subDay());
              })
              ->orWhere(function ($sq) {
                $sq->where('frequency', 'weekly')->where('last_sent_at', '<=', now()->subWeek());
              })
              ->orWhere(function ($sq) {
                $sq->where('frequency', 'monthly')->where('last_sent_at', '<=', now()->subMonth());
              });
        });
    }
}
