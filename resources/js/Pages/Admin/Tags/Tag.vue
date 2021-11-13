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
                        {{ tags }}
                        <button class="" @click="add">Add</button>
                        <draggable 
                            :list="tags" 
                            item-key="name" 
                            handle=".handle"
                        >
                            <template #item="{ element, index }">
                              <li class="list-group-item">
                                {{index}}
                                <span class="handle">handle</span>
                                       
                                <input type="text" class="form-control" v-model="element.text" />
                    
                                <button @click="removeAt(index)">Remove</button>
                              </li>
                            </template>
                        </draggable>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Admin/Authenticated.vue'
import { Head } from '@inertiajs/inertia-vue3';
import Draggable from "vuedraggable";
import { ref } from 'vue'

export default {
    components: {
        BreezeAuthenticatedLayout,
        Head,
        Draggable
    },
    props: {
        
    },
    setup () {
        const tags = ref([
            {
              text: "ertert",
            },
            {
              text: '',
            },
            {
              text: '',
            }
        ])

        function removeAt(idx) {
            tags.value.splice(idx, 1);
        }

        function add() {
            tags.value.push({ text: ''});
        }

        return { tags, removeAt, add }
    },
}
</script>
