<?php

namespace Dawnstar\ModuleBuilder\Contracts;

use Dawnstar\ModuleBuilder\Models\ModuleBuilder;
use Dawnstar\Core\Models\Structure;
use Illuminate\Database\Eloquent\Collection;

interface ModuleBuilderInterface
{
    public function getById(int $id): ModuleBuilder;

    public function getAll(): Collection;

    public function store(Structure $structure): void;

    public function update(ModuleBuilder $moduleBuilder): void;

    public function createFiles(Structure $structure): void;
}
