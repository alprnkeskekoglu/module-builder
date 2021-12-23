<?php

namespace Dawnstar\ModuleBuilder\Repositories;

use Dawnstar\ModuleBuilder\Contracts\ModuleBuilderInterface;
use Dawnstar\ModuleBuilder\Models\ModuleBuilder;
use Dawnstar\Models\Structure;
use Dawnstar\ModuleBuilder\Services\ModuleFileService;
use Illuminate\Database\Eloquent\Collection;

class ModuleBuilderRepository implements ModuleBuilderInterface
{
    public function getAll(): Collection
    {
        return ModuleBuilder::all();
    }

    public function store(Structure $structure): void
    {
        $types = [];

        if ($structure->has_detail) {
            $types[] = 'container';
        }

        if ($structure->type == 'dynamic') {
            $types[] = 'page';

            if ($structure->has_category) {
                $types[] = 'category';

                if ($structure->has_property) {
                    $types[] = 'property';
                }
            }
        }

        foreach ($types as $type) {

            $data = include base_path('vendor/dawnstar/module-builder/src/Resources/data/' . $type . '.php');

            ModuleBuilder::firstOrCreate(
                [
                    'structure_id' => $structure->id,
                    'type' => $type,
                    'data' => $data,
                    'meta_tags' => ['title', 'description']
                ]
            );
        }

        $this->createFiles($structure);
    }

    public function createFiles(Structure $structure): void
    {
        $service = new ModuleFileService($structure);
        $service->createController();
        $service->createBlades();
    }
}
