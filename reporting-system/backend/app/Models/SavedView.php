<?php
 
 namespace App\Models;
 
 use Illuminate\Database\Eloquent\Model;
 
 class SavedView extends Model
 {
     protected $fillable = [
         'name',
         'config',
     ];
 
     protected $casts = [
         'config' => 'array',
     ];
 }
 
