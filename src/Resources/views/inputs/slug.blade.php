<div class="{{ $input['parent_class'] }}">
    @foreach($languages as $language)
        @php
            $containerTranslation = $structure->container->translations()->where('language_id', $language->id)->first();
            $website = session('dawnstar.website');
        @endphp

        <div class="form-floating mb-2 hasLanguage {{ $loop->first ? '' : 'd-none' }}" data-language="{{ $language->id }}">
            <input type="text"
                   class="form-control slugInput {{ $input['class'] ?? '' }} @if($errors->has($input['key'][$language->id])) is-invalid @endif"
                   data-container="{{ optional($containerTranslation)->slug }}"
                   data-new="{{ isset($input['value'][$language->id]) ? 0 : 1 }}"
                   name="{{ $input['name'][$language->id] }}"
                   value="/{{ ltrim(($input['value'][$language->id] ?? ''), '/') }}"
                   id="{{ $input['id'][$language->id] }}" data-language="{{ $language->id }}"/>
            <label for="{{ $input['id'][$language->id] }}">{{ $input['label'][$language->id] }}</label>
            @if($type != 'property')
                <div class="help-block text-muted ms-2">
                    {{ ($website->url_language_code != 1 && $website->defaultLanguage()->id == $language->id ? '' : "/{$language->code}") . ($type != 'container' ? "/{$containerTranslation->slug}" : '') }}<span>/{{ ltrim(($input['value'][$language->id] ?? ''), '/') }}</span>
                </div>
            @endif
            @if($errors->has($input['key'][$language->id]))
                <div class="invalid-feedback">
                    {{ $errors->first($input['key'][$language->id]) }}
                </div>
            @endif
        </div>
    @endforeach
</div>
