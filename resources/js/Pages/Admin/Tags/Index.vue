<template>
    <Head :title="__('Tags')" />

    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tags Management') }}
            </h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <button class="bg-black text-white px-4 py-2 mb-3 sm:rounded-lg" @click="add">{{ __('Add') }}</button>
                        <Draggable 
                            :list="tags" 
                            item-key="position" 
                            handle=".handle"
                            @end="changePosition"
                        >
                            <template #item="{ element, index }">
                              <div class="list-group-item flex items-center py-3 gap-4">
                                <i class="fas fa-align-justify handle" style="cursor: grab;"></i>  
                                <span>{{ index }}</span>  
                                <div class="flex flex-col w-full">
                                    <input type="text" class="form-control w-full sm:rounded-lg" v-model="element.name" />
                                    <span 
                                        v-if="Object.keys($page.props.errors).length && $page.props.errors.tagGroup['tags.'+index+'.name']"
                                        class="text-red-500"
                                    >
                                        {{ $page.props.errors.tagGroup['tags.'+index+'.name'] }}
                                    </span>
                                </div>
                                <div class="flex flex-col w-full">
                                    <Multiselect 
                                        v-model="element.type" 
                                        :options="tagTypes"
                                        label="label"
                                        trackBy="value"
                                        :placeholder="__('Select the type')"
                                    />
                                    <span 
                                        v-if="Object.keys($page.props.errors).length && $page.props.errors.tagGroup['tags.'+index+'.type']"
                                        class="text-red-500"
                                    >
                                        {{ $page.props.errors.tagGroup['tags.'+index+'.type'] }}
                                    </span>
                                </div>
                                <div class="flex flex-col justify-center items-center">
                                    <label>{{ __('Active') }}</label>
                                    <input type="checkbox" class="form-control" v-model="element.is_active" />
                                </div>
                                <Link v-if="element.id != null" :href="route($page.props.locale + '.admin.tags.edit', element)">
                                    <i class="fas fa-edit cursor-pointer"></i>
                                </Link>
                                <button @click="remove(index, element)">
                                    <i class="fas fa-trash-alt cursor-pointer text-red-500"></i>
                                </button>
                              </div>
                            </template>
                        </Draggable>
                        <Link 
                            :href="route($page.props.locale + '.admin.tags.save')" 
                            as="button" 
                            type="button" 
                            method="post" 
                            :data="{ tags: tags }" 
                            class="bg-black text-white px-4 py-2 sm:rounded-lg mt-3"
                            v-if="tags.length > 0"
                        >
                            {{ __('Save') }}
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Admin/Authenticated.vue'
import { Head, Link, usePage  } from '@inertiajs/inertia-vue3';
import Draggable from "vuedraggable";
import { toRef } from 'vue'
import { Inertia } from '@inertiajs/inertia';
import Multiselect from '@vueform/multiselect'

export default {
    components: {
        BreezeAuthenticatedLayout,
        Head,
        Draggable,
        Link,
        Multiselect
    },
    props: {
        tags: Object,
        tagTypes: Object
    },
    setup (props) {
        const tags = toRef(props, 'tags')
        const tagTypes = toRef(props, 'tagTypes')
    
        function remove(idx, element) {
            if (element.id == null) tags.value.splice(idx, 1)
            else Inertia.post(route(usePage().props.value.locale + '.admin.tags.destroy', element))
        }

        function add() {
            tags.value.push({
                id: null, 
                name: '',
                type: '', 
                is_active: false, 
                position: tags.value.length
            });
        }

        function changePosition() {
            tags.value.forEach((element, index) => {
                element.position = index
            });
        }

        return { remove, add, changePosition, tags, tagTypes }
    },
}
</script>
<style src="@vueform/multiselect/themes/default.css"></style>