<template>
    <div class="d-flex">

        <div class="dropdown me-2">
            <button class="btn btn-light dropdown-toggle" type="button" id="newElementDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{ $root.trans.add_new }}
            </button>
            <div class="dropdown-menu" aria-labelledby="newElementDropdown">
                <button v-for="element in $root.elements" class="dropdown-item" @click=showModal(element)>{{ element }}</button>
            </div>
        </div>

        <button type="button" class="btn btn-primary" @click="$root.save">{{ $root.trans.save }}</button>
    </div>
</template>

<script>
export default {
    name: "ElementListComponent",
    methods: {
        showModal(element) {
            this.$root.current = {
                translation: false,
                element: null,
                type: null,
                name: null,
                parent_class: "col-lg-12",
                class: null,
                labels: {40: null, 164: null},
                rules: [],
                options: [
                    {
                        key: null,
                        value: {
                            164: null,
                            40: null
                        }
                    }
                ],
                queries: [
                    {
                        0: null,
                        1: null,
                        2: null
                    }
                ],
            };
            this.$root.current.element = element;
            this.$root.new_element = true;
            var elementModal = new bootstrap.Modal(document.getElementById('elementModal'));
            elementModal.show();
        }
    }
}
</script>
