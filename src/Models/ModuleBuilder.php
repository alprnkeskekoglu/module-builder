<?php

namespace Dawnstar\ModuleBuilder\Models;

use Dawnstar\Core\Models\Structure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModuleBuilder extends Model
{
    use SoftDeletes;

    protected $table = 'module_builders';
    protected $guarded = ['id'];
    protected $casts = ['data' => 'array', 'meta_tags' => 'array'];

    public function structure()
    {
        return $this->belongsTo(Structure::class);
    }
}
