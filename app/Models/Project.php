<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'project_name',
        'quote_id',
        'project_budget',
        'is_active',
        'project_start_date',
        'project_end_date',
        'project_description',
        'project_location',
        'client_id',
        'head_landscaper_id',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function images()
    {
        return $this->hasMany(ProjectImage::class);
    }

    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }

    public function headLandscaper()
    {
        return $this->belongsTo(User::class, 'head_landscaper_id');
    }

    public function fieldCrew()
    {
        return $this->belongsToMany(User::class, 'project_field_crew');
    }
}
