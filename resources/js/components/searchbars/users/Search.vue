<script setup>
    import { ref } from 'vue';

    const props = defineProps({
        page: Number,
        limit: Number,
    });

    const searchQuery = ref(null);

    const emit = defineEmits(['toggleSearch']);

    const userSearch = () => {
        emit('toggleSearch', {page: props.page, query: searchQuery.value, limit: props.limit});
    }

</script>

<template>
    <div class="input-group">
        <input type="text"
            class="form-control"
            placeholder="Search"
            aria-label="Search"
            aria-describedby="button-addon2"
            v-model="searchQuery"
            @keyup="userSearch">
        <div class="input-group-append">
            <button class="btn btn-outline-secondary"
                type="button"
                id="button-addon2"
                @click.prevent="userSearch"><i class="fas fa-search"></i></button>
        </div>
    </div>
</template>
