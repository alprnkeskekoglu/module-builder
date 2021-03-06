<?php

namespace Dawnstar\ModuleBuilder\Repositories;

use Dawnstar\ModuleBuilder\Contracts\ModuleBuilderInterface;
use Dawnstar\ModuleBuilder\Models\ModuleBuilder;
use Dawnstar\Core\Models\Structure;
use Dawnstar\ModuleBuilder\Services\ModuleFileService;
use Illuminate\Database\Eloquent\Collection;

class ModuleBuilderRepository implements ModuleBuilderInterface
{
    public function getById(int $id): ModuleBuilder
    {
        return ModuleBuilder::findOrFail($id);
    }

    public function getAll(): Collection
    {
        return ModuleBuilder::all();
    }

    public function store(Structure $structure): void
    {
        $types = ['container'];

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
                ],
                [
                    'data' => $data,
                    'meta_tags' => ['robots', 'title', 'description']
                ]
            );
        }

        $this->createFiles($structure);
    }

    public function update(ModuleBuilder $moduleBuilder): void
    {
        $data = request('data');
        $metaTags = request('meta_tags');
        $moduleBuilder->update([
            'data' => $data,
            'meta_tags' => $metaTags
        ]);
    }

    public function createFiles(Structure $structure): void
    {
        $service = new ModuleFileService($structure);
        $service->createController();
        $service->createBlades();
    }
}
