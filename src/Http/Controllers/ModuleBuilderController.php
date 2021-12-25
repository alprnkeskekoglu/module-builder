<?php

namespace Dawnstar\ModuleBuilder\Http\Controllers;

use Dawnstar\Core\Http\Controllers\BaseController;
use Dawnstar\Core\Http\Requests\StructureRequest;
use Dawnstar\Core\Models\Container;
use Dawnstar\Core\Models\ContainerTranslation;
use Dawnstar\ModuleBuilder\Models\ModuleBuilder;
use Dawnstar\Core\Models\Structure;
use Dawnstar\Region\Models\Country;
use Dawnstar\Core\Repositories\ContainerRepository;
use Dawnstar\Core\Repositories\ContainerTranslationRepository;
use Dawnstar\ModuleBuilder\Repositories\ModuleBuilderRepository;
use Dawnstar\Core\Repositories\StructureRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ModuleBuilderController extends BaseController
{
    protected ModuleBuilderRepository $moduleBuilderRepository;

    public function __construct(ModuleBuilderRepository $moduleBuilderRepository)
    {
        $this->moduleBuilderRepository = $moduleBuilderRepository;
    }

    public function index()
    {
        $moduleBuilders = $this->moduleBuilderRepository->getAll();
        return view('ModuleBuilder::module_builder.index', compact('moduleBuilders'));
    }

    public function edit(ModuleBuilder $moduleBuilder)
    {
        canUser("structure.{$moduleBuilder->structure->id}.edit");

        return view('ModuleBuilder::module_builder.edit', compact('moduleBuilder'));
    }

    public function update(ModuleBuilder $moduleBuilder, Request $request)
    {
        canUser("structure.{$moduleBuilder->structure->id}.edit");

        $data = $request->get('data');
        $metaTags = $request->get('meta_tags');

        $moduleBuilder->update([
            'data' => $data,
            'meta_tags' => $metaTags
        ]);

        return response()->json(['success' => __('ModuleBuilder::general.success.update')]);
    }

    public function getBuilderData(ModuleBuilder $moduleBuilder)
    {
        canUser("structure.{$moduleBuilder->structure->id}.edit");

        return response()->json(['builderData' => $moduleBuilder->data, 'metaTags' => $moduleBuilder->meta_tags]);
    }

    public function getTranslations()
    {
        $return = [
            'back' => __('Core::general.back'),
            'save' => __('Core::general.save'),
            'add_new' => __('Core::general.add_new'),
            'yes' => __('Core::general.yes'),
            'no' => __('Core::general.no'),
            'required' => __('Core::general.required'),
            'translation' => __('ModuleBuilder::general.translation'),
            'type' => __('ModuleBuilder::general.type'),
            'name' => __('ModuleBuilder::general.name'),
            'col' => __('ModuleBuilder::general.col'),
            'label' => __('ModuleBuilder::general.label'),
            'max_count' => __('ModuleBuilder::general.max_count'),
            'selectable' => __('ModuleBuilder::general.selectable'),
            'rules' => __('ModuleBuilder::general.rules'),
            'options' => __('ModuleBuilder::general.options'),
            'queries' => __('ModuleBuilder::general.queries'),
        ];

        return response()->json(['translations' => $return]);
    }
}
