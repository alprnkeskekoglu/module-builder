<div class="row">
    <div class="col-sm-3 mb-2 mb-sm-0">
        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
            @foreach($properties as $property)
            <a class="nav-link {{ $loop->first ? 'active show' :'' }}"
               id="tab-{{ $property->id }}"
               data-bs-toggle="pill"
               href="#pill-{{ $property->id }}"
               role="tab"
               aria-controls="v-pills-home"
               aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                <span class="d-none d-md-block">{{ $property->translation->name }}</span>
            </a>
            @endforeach
        </div>
    </div>

    <div class="col-sm-9">
        <div class="tab-content" id="v-pills-tabContent">
            @foreach($properties as $property)
            <div class="tab-pane fade {{ $loop->first ? 'active show' :'' }}" id="pill-{{ $property->id }}" role="tabpanel" aria-labelledby="v-pills-home-tab">

                <div class="mt-2">
                    @foreach($property->options as $option)
                    <div class="form-check form-large-check form-check-inline">
                        <input type="checkbox" class="form-check-input"
                               id="property-{{ $option->id }}"
                               value="{{ $option->id }}"
                               {{ in_array($option->id, $value) ? 'checked' : '' }}
                               name="property_options[]">
                        <label class="form-check-label" for="property-{{ $option->id }}">{!! $option->translation->name !!}</label>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
