<div class="{{ $input['parent_class'] }}">
    <label class="mb-2">{{ $input['label'] }}</label>
    <div id="propertyList">
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function () {
            $('[data-type="category"]').trigger('change');
        })

        $('body').delegate('[data-type="category"]', 'change', function () {
            var categories = $(this).val();
            var value = @json($input['value'])

            $.ajax({
                url: '{{ route('dawnstar.structures.pages.getCategoryProperties', $structure) }}',
                data: {categories, value},
                success: function (response) {
                    $('#propertyList').html(response);
                }
            })
        })
    </script>
@endpush
