<?php

namespace Dawnstar\ModuleBuilder\Contracts;

use Dawnstar\Core\Models\ModuleBuilder;
use Dawnstar\Core\Models\Structure;
use Illuminate\Database\Eloquent\Collection;

interface ModuleBuilderInterface
{
    public function getAll(): Collection;

    public function store(Structure $structure): void;

    public function createFiles(Structure $structure): void;
}
