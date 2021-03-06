<?php

namespace Dawnstar\ModuleBuilder\Services;

use Dawnstar\Core\Models\Category;
use Dawnstar\Core\Models\Language;
use Dawnstar\ModuleBuilder\Models\ModuleBuilder;
use Dawnstar\Core\Models\Page;
use Dawnstar\Core\Models\Structure;
use Dawnstar\Region\Models\Country;
use Illuminate\Support\Facades\Cache;

class ModuleBuilderService
{
    public ModuleBuilder $builder;
    public $languages;

    public function __construct
    (
        public Structure $structure,
        public string $type,
        public $model = null
    )
    {
        $this->builder = $structure->moduleBuilders()->where('type', $type)->first();
        $this->languages = $this->getActiveLanguages();
    }

    public function html(): string
    {
        $inputs = $this->builder->data;

        $html = '';
        foreach ($inputs as $input) {
            $html .= $this->getInputHtml($input);
        }
        return $html;
    }

    public function metaTagHtml(): string
    {
        $tags = $this->builder->meta_tags;

        $this->setMetaTags($tags);

        return view('ModuleBuilder::inputs.meta_tag', [
            'tags' => $tags,
            'languages' => $this->languages,
            'type' => $this->type,
            'structure' => $this->structure,
        ])->render();
    }

    public function validate()
    {
        request()->validate(...$this->getValidationData());
    }

    public function getActiveTranslations()
    {
        return $this->model->translations()->pluck('status', 'language_id')->toArray();
    }

    #region Input
    private function getInputHtml(array $input): string
    {
        $whiteList = [
            'input', 'slug', 'textarea', 'radio', 'checkbox', 'select', 'country', 'media', 'relation', 'category', 'property'
        ];

        if (!in_array($input['element'], $whiteList)) {
            return '';
        }

        $this->setInput($input);

        return view('ModuleBuilder::inputs.' . $input['element'], [
            'input' => $input,
            'languages' => $this->languages,
            'type' => $this->type,
            'structure' => $this->structure,
        ])->render();
    }

    private function setInput(array &$input)
    {
        $input['translation'] = $input['translation'] == 'true';

        $this->setValue($input);
        $this->setInputNameAndId($input);
        $this->setLabel($input);

        if (in_array($input['element'], ['radio', 'checkbox', 'select', 'country', 'city', 'relation', 'category'])) {
            $this->setOptions($input);
        }
    }

    private function setInputNameAndId(array &$input)
    {
        $element = $input['element'] ?? null;

        $input['key'] = $input['id'] = $input['name'];

        if ($element == 'media') {
            $input['id'] = "medias_{$input['name']}";
            $input['column'] = $input['name'];
            $input['key'] = "medias.{$input['name']}";
            $input['name'] = $input['translation'] ? "medias][{$input['name']}" : "medias[{$input['name']}]";
        } elseif ($element == 'relation') {
            $input['id'] = "relations_{$input['name']}";
            $input['column'] = $input['name'];
            $input['key'] = "relations.{$input['name']}";
            $input['name'] = "relations[{$input['name']}]";
        } elseif ($element == 'category') {
            $input['id'] = $input['key'] = $input['name'] = "categories";
            $input['column'] = $input['name'];
        } elseif ($element == 'property') {
            $input['id'] = $input['key'] = $input['name'] = "properties";
            $input['column'] = $input['name'];
        }

        $name = $id = $key = [];
        if ($input['translation'] == 'true') {
            foreach ($this->languages as $language) {
                $id[$language->id] = "translations_{$language->id}_" . $input['id'];
                $key[$language->id] = "translations.{$language->id}." . $input['key'];
                $name[$language->id] = "translations[{$language->id}][" . $input['name'] . "]";
            }
            $input['id'] = $id;
            $input['key'] = $key;
            $input['name'] = $name;
        }
    }

    private function setLabel(array &$input)
    {
        $label = $input['labels'][session('dawnstar.language.id')] ?? '';
        unset($input['labels']);

        if ($input['translation'] == 'true') {
            foreach ($this->languages as $language) {
                $input['label'][$language->id] = $label . ' (' . strtoupper($language->code) . ')';
            }
        } else {
            $input['label'] = $label;
        }
    }

    private function setOptions(array &$input)
    {
        $options = [];

        if ($input['element'] == 'country') {
            $options = $this->getCountries();
        } elseif ($input['element'] == 'relation') {
            $options = $this->getRelations($input);
        } elseif ($input['element'] == 'category') {
            $options = $this->getCategories($input);
        }

        foreach ($input['options'] as $option) {
            $options[$option['key']] = $option['value'][session('dawnstar.language.id')];
        }

        $input['options'] = $options;
    }
    #endregion

    #region Value
    private function setValue(array &$input)
    {
        if ($input['translation'] == 'true') {
            $input['value'] = $this->getTranslationValue($input);
        } else {
            $input['value'] = $this->getNonTranslationValue($input);
        }
    }

    private function getNonTranslationValue(array $input)
    {
        $name = $input['name'];

        if ($input['element'] == 'media') {
            return $this->model ? $this->model->medias()->wherePivot('key', $name)->orderBy('model_medias.order')->pluck('id')->toArray() : [];
        } elseif ($input['element'] == 'relation') {
            return $this->model ? $this->model->subPages($name)->pluck('id')->toArray() : [];
        } elseif ($input['element'] == 'category') {
            return $this->model ? $this->model->categories->pluck('id')->toArray() : [];
        } elseif ($input['element'] == 'property') {
            return $this->model ? $this->model->propertyOptions->pluck('id')->toArray() : [];
        }

        return old($input['name'], ($this->model ? $this->model->{$name} : null));
    }

    private function getTranslationValue(array $input)
    {
        $name = $input['name'];
        $translations = optional($this->model)->translations;

        $values = [];
        foreach ($this->languages as $language) {
            $translation = $translations ? $translations->where('language_id', $language->id)->first() : null;

            if ($translation && $input['element'] == 'media') {
                $values[$language->id] = $translation->medias()->wherePivot('key', $name)->orderBy('model_medias.order')->pluck('id')->toArray();
            } else {
                $values[$language->id] = old("translations.{$language->id}.$name", ($translation ? $translation->{$name} : null));
            };
        }
        return $values;
    }
    #endregion

    #region Validation
    private function getValidationData()
    {
        $rules = $attributes = [];
        $inputs = $this->builder->data;

        foreach ($inputs as $input) {
            if (isset($input['rules'])) {
                $this->setRules($rules, $input);
                $this->setAttributes($attributes, $input);
            }
        }
        return [$rules, [], $attributes];
    }

    private function setRules(array &$rules, array $input)
    {
        $element = $input['element'] ?? null;

        if ($input['translation'] == 'true') {
            foreach ($this->languages as $language) {
                if ($element == 'media') {
                    $rules["translations.*.medias.{$input['name']}"] = $input['rules'];
                } else {
                    $rules["translations.*.{$input['name']}"] = $input['rules'];
                }
            }
        } elseif ($element == 'media') {
            $rules["medias.{$input['name']}"] = $input['rules'];
        } elseif ($element == 'category') {
            $rules['categories'] = $input['rules'];
        } elseif ($element == 'property') {
            $rules['properties'] = $input['rules'];
        } elseif ($element == 'relation') {
            $rules["relations.{$input['name']}"] = $input['rules'];
        } else {
            $rules[$input['name']] = $input['rules'];
        }
    }

    private function setAttributes(array &$attributes, array $input)
    {
        $element = $input['element'] ?? null;
        $panelLanguageId = session('dawnstar.language.id');

        if ($input['translation'] == 'true') {
            foreach ($this->languages as $language) {
                if ($element == 'media') {
                    $attributes["translations.{$language->id}.medias.{$input['name']}"] = $input['labels'][$panelLanguageId] . ' (' . strtoupper($language->code) . ')';
                } else {
                    $attributes["translations.{$language->id}.{$input['name']}"] = $input['labels'][$panelLanguageId] . ' (' . strtoupper($language->code) . ')';
                }
            }
        } elseif ($element == 'media') {
            $attributes["medias.{$input['name']}"] = $input['labels'][$panelLanguageId];
        } elseif ($element == 'category') {
            $attributes['categories'] = $input['labels'][$panelLanguageId];
        } elseif ($element == 'property') {
            $attributes['properties'] = $input['labels'][$panelLanguageId];
        } elseif ($element == 'relation') {
            $attributes["relations.{$input['name']}"] = $input['labels'][$panelLanguageId];
        } else {
            $attributes[$input['name']] = $input['labels'][$panelLanguageId];
        }
    }
    #endregion

    private function setMetaTags(array &$tags)
    {
        $data = [];
        foreach ($tags as $tag) {
            $data[] = [
                'key' => $tag,
                'value' => $this->getMetaTagValue($tag)
            ];
        }

        $tags = $data;
    }

    private function getMetaTagValue(string $tag)
    {
        $values = [];

        $translations = optional($this->model)->translations;
        foreach ($this->languages as $language) {
            $translation = $translations ? $translations->where('language_id', $language->id)->first() : null;

            if ($translation) {
                $metas = $translation->url->metas->pluck('value', 'key')->toArray();
                $values[$language->id] = $metas[$tag] ?? '';
            }

        }

        return $values;
    }

    private function getActiveLanguages()
    {
        $activeLanguageIds = $this->structure->translations()->active()->pluck('language_id')->toArray();
        $defaultLanguageId = $this->structure->website->defaultLanguage()->id;
        return Language::whereIn('id', $activeLanguageIds)->orderByRaw("id = {$defaultLanguageId} DESC")->get();
    }

    private function getCountries()
    {
        $languageCode = session('dawnstar.language.code');
        return Cache::rememberForever('module_builder_country_' . $languageCode, function () use ($languageCode) {
            return Country::all()->pluck("name_{$languageCode}", 'id');
        });
    }

    private function getRelations(array $input)
    {
        $options = Page::with('translation');

        foreach ($input['queries'] as $query) {
            $options = $options->where($query[0], $query[1], $query[2]);
        }

        return $options->get()->pluck('translation.name', 'id')->toArray();
    }

    private function getCategories(array $input)
    {
        return \Illuminate\Support\Facades\Cache::rememberForever('categories' . $this->structure->id . session('dawnstar.language.id'), function () {
            $categories = $this->structure->categories()->orderBy('left')->get();

            $return = [];
            foreach ($categories as $category) {
                $return[$category->id] = $this->getCategoryName($category);
            }
            return $return;
        });
    }

    private function getCategoryName(Category $category): string
    {
        $name[] = $category->translation->name;

        $parent = $category->parent;
        if ($parent) {
            $name[] = $this->getCategoryName($parent);
        }

        return implode(' >> ', array_reverse($name));
    }
}
