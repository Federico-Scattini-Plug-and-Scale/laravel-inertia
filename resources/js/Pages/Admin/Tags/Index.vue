<template>
    <Head title="Tags" />

    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tags Management
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <button class="bg-black text-white px-4 py-2 mb-3 sm:rounded-lg" @click="add">Add</button>
                        <draggable 
                            :list="tags" 
                            item-key="position" 
                            handle=".handle"
                            @end="changePosition"
                        >
                            <template #item="{ element, index }">
                              <div class="list-group-item flex items-center py-3 gap-4">
                                <i class="fas fa-align-justify handle" style="cursor: grab;"></i>  
                                <span>{{index}}</span>  
                                <input type="text" class="form-control w-full lg:w-9/12 sm:rounded-lg" v-model="element.name" />
                                <div class="flex flex-col justify-center items-center">
                                    <label>Active</label>
                                    <input type="checkbox" class="form-control" v-model="element.is_active" />
                                </div>
                                <Link v-if="element.id != null" :href="route('admin.tags.edit', element)">
                                    <i class="fas fa-edit cursor-pointer"></i>
                                </Link>
                                <button @click="remove(index, element)">
                                    <i class="fas fa-trash-alt cursor-pointer text-red-500"></i>
                                </button>
                              </div>
                            </template>
                        </draggable>
                        <Link 
                            :href="route('admin.tags.save')" 
                            as="button" 
                            type="button" 
                            method="post" 
                            :data="{ tags: tags }" 
                            class="bg-black text-white px-4 py-2 sm:rounded-lg mt-3"
                        >
                            Save
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Admin/Authenticated.vue'
import { Head, Link  } from '@inertiajs/inertia-vue3';
import Draggable from "vuedraggable";
import { toRef } from 'vue'
import { Inertia } from '@inertiajs/inertia';

export default {
    components: {
        BreezeAuthenticatedLayout,
        Head,
        Draggable,
        Link
    },
    props: {
        tags: Object
    },
    setup (props) {
        const tags = toRef(props, 'tags')
    
        function remove(idx, element) {
            if (element.id == null) tags.value.splice(idx, 1)
            else Inertia.post(route('admin.tags.destroy', element))
        }

        function add() {
            tags.value.push({
                id: null, 
                name: '', 
                is_active: false, 
                position: tags.value.length
            });
        }

        function changePosition() {
            tags.value.forEach((element, index) => {
                element.position = index
            });
        }

        return { remove, add, changePosition }
    },
}
</script>
