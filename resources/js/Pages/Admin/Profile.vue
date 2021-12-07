<template>
    <Head :title="__('Profile')" />

    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ admin.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit" class="flex flex-col">
                            <div class="mb-6">
                                <label class="text-lg font-semibold">{{ __('Admin name') }}</label>
                                <input type="text" v-model="form.name" class="sm:rounded-lg w-full mt-1">
                                <div v-if="errors.name" class="text-red-500">{{ errors.name }}</div>
                            </div>
                            <div class="mb-6">
                                <label class="text-lg font-semibold">{{ __('Email') }}</label>
                                <input type="text" v-model="form.email" class="sm:rounded-lg w-full mt-1">
                                <div v-if="errors.email" class="text-red-500">{{ errors.email }}</div>
                            </div>
                            <button type="submit" :disabled="form.processing" class="bg-black text-white px-4 py-2 sm:rounded-lg">{{ __('Save') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Admin/Authenticated.vue'
import { Head } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import { useForm, usePage } from '@inertiajs/inertia-vue3'

export default {
    components: {
        BreezeAuthenticatedLayout,
        Head,
    },
    props: {
      admin: Object,
      errors: Object
    },
    setup (props) {
        const form = useForm({
            email: props.admin.email,
            name: props.admin.name,
        })

        function submit() {
            Inertia.post(route(usePage().props.value.locale + '.admin.profile.edit', props.admin), form, {
                preserveScroll: (page) => Object.keys(page.props.errors).length,
            })
        }

        return { form, submit }
    },
}
</script>