<template>
    <Head :title="__('Modify the category') + ' ' + category.name" />

    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Modify the category') + ' ' + category.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <Alert v-if="$page.props.session.success" :message="$page.props.session.success" :type="'success'" class="mb-4"/>
						<form @submit.prevent="update()" class="flex flex-col">
							<label for="name">{{ __('Name') }}</label>
							<input id="name" type="text" v-model="form.name" class="form-control w-full sm:rounded-lg">
							<div v-if="form.errors.name" class="text-red-500">{{ form.errors.name }}</div>
							<label for="description">{{ __('Description') }}</label>
							<textarea id="description" type="text" v-model="form.description" class="form-control w-full sm:rounded-lg" />
							<div v-if="form.errors.description" class="text-red-500">{{ form.errors.description }}</div>
							<label for="is_active">{{ __('Active') }}</label>
							<input id="is_active" type="checkbox" v-model="form.is_active">
							<div v-if="form.errors.is_active" class="text-red-500">{{ form.errors.is_active }}</div>
							<button type="submit" class="bg-black text-white px-4 py-2 sm:rounded-lg mt-4 w-fit-content" :disabled="form.processing">{{ __('Save') }}</button>
						</form>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Admin/Authenticated.vue';
import { Head, Link, useForm, usePage  } from '@inertiajs/inertia-vue3';
import Alert from '@/Components/Alert.vue'
import { ref, toRef } from 'vue';

export default {
    components: {
        BreezeAuthenticatedLayout,
        Head,
        Link,
		Alert
    },
	props: {
		category: Object
	},
	setup (props) {
		const category = toRef(props, 'category')

		const form = useForm({
			name: category.value.name,
			is_active: category.value.is_active,
			description: category.value.description
		})

		function update() {
			form.post(route(usePage().props.value.locale + '.admin.categories.update', category.value), {
				preserveScroll: true,
			})
		}

		return { form, update, category }
  	},
}
</script>
