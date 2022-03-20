<template>
    <Head :title="__('Tags')" />

    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ taggroup.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <button class="bg-black text-white px-4 py-2 mb-3 sm:rounded-lg" @click="add">{{ __('Add') }}</button>
                        <draggable 
                            :list="tags" 
                            item-key="position" 
                            handle=".handle"
                            @end="changePosition"
                        >
                            <template #item="{ element, index }">
                                <div class="flex flex-col p-3" :class="{'bg-yellow-400 sm:rounded-lg my-2' : !element.is_approved && element.suggested_by != null && !element.is_active}">
									<span v-if="!element.is_approved && element.suggested_by != null && !element.is_active && element.suggestor.detail != null">
										{{ __('This tag was suggested by') + ' ' + element.suggestor.detail.name + __('. If you want to use it, please approve and activate it.') }}
									</span>
									<div class="list-group-item flex items-center py-3 gap-4">
										<i class="fas fa-align-justify handle" style="cursor: grab;"></i>  
										<span>{{ index }}</span>  
										<div class="flex flex-col w-full">
											<input type="text" class="form-control w-full sm:rounded-lg" v-model="element.name" />
											<span 
												v-if="Object.keys($page.props.errors).length && $page.props.errors.tags['tags.'+index+'.name']"
												class="text-red-500"
											>
												{{ $page.props.errors.tags['tags.'+index+'.name'] }}
											</span>
										</div>
										<div class="flex flex-col justify-center items-center">
											<label>{{ __('Icon') }}</label>
											<span v-if="element.icon">{{ element.icon }}</span>
											<input type="file" class="form-control" @change="setIcon($event, element)" />
										</div>
										<div class="flex flex-col justify-center items-center">
											<label>{{ __('Active') }}</label>
											<input type="checkbox" class="form-control" v-model="element.is_active" />
										</div>
										<div class="flex flex-col justify-center items-center">
											<label>{{ __('Approved') }}</label>
											<input type="checkbox" class="form-control" v-model="element.is_approved" />
										</div>
										<button @click="remove(index, element)">
											<i class="fas fa-trash-alt cursor-pointer text-red-500"></i>
										</button>
									</div>
								</div>
                            </template>
                        </draggable>
                        <Link 
                            :href="route($page.props.locale + '.admin.tags.update', taggroup)" 
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

export default {
    components: {
        BreezeAuthenticatedLayout,
        Head,
        Draggable,
        Link,
    },
    props: {
        tags: Object,
        taggroup: Object,
    },
    setup (props) {
        const tags = toRef(props, 'tags')
        const taggroup = toRef(props, 'taggroup')
    
        function remove(idx, element) {
            if (element.id == null) tags.value.splice(idx, 1)
            else Inertia.post(route(usePage().props.value.locale + '.admin.tags.destroy.tag', [taggroup.value, element]))
        }

        function add() {
            tags.value.push({
                id: null, 
                name: '', 
                is_active: false, 
                position: tags.value.length,
                is_approved: true,
				icon: ''
            });
        }

        function changePosition() {
            tags.value.forEach((element, index) => {
                element.position = index
            });
        }

		function setIcon(e, element) {
			element.icon = e.target.files[0]
		}

        return { remove, add, changePosition, taggroup, setIcon }
    },
}
</script>
