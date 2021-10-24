<template>
    <Head title="Profile" />

    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Company Profile
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit">
                            <input type="text" v-model="form.name">
                            <div v-if="errors.name">{{ errors.name }}</div>
                            <input type="text" v-model="form.email">
                            <div v-if="errors.email">{{ errors.email }}</div>
                            <input type="text" v-model="form.address">
                            <div v-if="errors.address">{{ errors.address }}</div>
                            <input type="text" v-model="form.latitude">
                            <div v-if="errors.latitude">{{ errors.latitude }}</div>
                            <input type="text" v-model="form.longitude">
                            <div v-if="errors.longitude">{{ errors.longitude }}</div>
                            <input type="text" v-model="form.website_link">
                            <div v-if="errors.website_link">{{ errors.website_link }}</div>
                            <button type="submit" :disabled="form.processing">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Company/Authenticated.vue'
import { Head } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia'
import { useForm } from '@inertiajs/inertia-vue3'

export default {
    components: {
        BreezeAuthenticatedLayout,
        Head,
    },
    props: {
      company: Object,
      errors: Object
    },
    setup (props) {
        const form = useForm({
            email: props.company.email,
            name: props.company.name,
            address: '',
            latitude: '',
            longitude: '',
            logo: 'test',
            website_link: '',
        })

        if (props.company.detail != null) {
            form.address = props.company.detail.address
            form.latitude = props.company.detail.latitude
            form.longitude = props.company.detail.longitude
            form.logo = props.company.detail.logo
            form.website_link = props.company.detail.website_link
        }

        function submit() {
            Inertia.post(route('company.profile.edit', props.company), form)
        }

        return { form, submit }
    },
}
</script>
