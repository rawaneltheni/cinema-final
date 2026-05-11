<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'director', 'release_year', 'genre', 'duration_minutes', 'description'])]
class Project extends Model
{
    use HasFactory;
}
