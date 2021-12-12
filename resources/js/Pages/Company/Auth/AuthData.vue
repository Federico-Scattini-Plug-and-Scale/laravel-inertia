<template>
    <Head :title="__('Access data')" />

    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manage your access data') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
						<Alert v-if="$page.props.session.errorEmail" :message="$page.props.session.errorEmail" :type="'error'" class="mb-4"/>
                        <form @submit.prevent="submitEmail" class="flex flex-col mb-4">
                            <div class="mb-6">
                                <label class="text-lg font-semibold">{{ __('Email') }}</label>
                                <input type="text" v-model="formEmail.email" class="sm:rounded-lg w-full mt-1">
                                <div v-if="errors.email" class="text-red-500">{{ errors.email }}</div>
                            </div>
							<p class="text-md text-yellow-500 mb-2">{{ __('After changing the email, you will need to re-verify the email in order to continue to use the service.') }}</p>
                            <button type="submit" :disabled="formEmail.processing" class="bg-black text-white px-4 py-2 sm:rounded-lg">{{ __('Save') }}</button>
                        </form>
                        <Alert v-if="$page.props.session.successPass" :message="$page.props.session.successPass" :type="'success'" class="mb-4"/>
						<Alert v-if="$page.props.session.errorPass" :message="$page.props.session.errorPass" :type="'error'" class="mb-4"/>
                        <form @submit.prevent="submitPass" class="flex flex-col">
                            <div class="mb-6">
                                <label class="text-lg font-semibold">{{ __('Old password') }}</label>
                                <input type="password" v-model="formPass.old_password" class="sm:rounded-lg w-full mt-1">
                                <div v-if="errors.old_password" class="text-red-500">{{ errors.old_password }}</div>
                            </div>
							<div class="mb-6">
                                <label class="text-lg font-semibold">{{ __('New password') }}</label>
                                <input type="password" v-model="formPass.new_password" class="sm:rounded-lg w-full mt-1">
                                <div v-if="errors.new_password" class="text-red-500">{{ errors.new_password }}</div>
                            </div>
							<div class="mb-6">
                                <label class="text-lg font-semibold">{{ __('Confirm the new password') }}</label>
                                <input type="password" v-model="formPass.new_password_confirmation" class="sm:rounded-lg w-full mt-1">
                                <div v-if="errors.new_password_confirmation" class="text-red-500">{{ errors.new_password_confirmation }}</div>
                            </div>
                            <button type="submit" :disabled="formPass.processing" class="bg-black text-white px-4 py-2 sm:rounded-lg">{{ __('Save') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Company/Authenticated.vue'
import Alert from '@/Components/Alert.vue'
import { Head, usePage, useForm } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia'

export default {
    components: {
        BreezeAuthenticatedLayout,
        Head,
        Alert
    },
    props: {
      company: Object,
      errors: Object
    },
    setup (props) {
        const formPass = useForm({
            old_password: '',
			new_password: '',
			new_password_confirmation: ''
		})

		const formEmail = useForm({
			email: props.company.email,
		})

        function submitPass() {
            Inertia.post(route(usePage().props.value.locale + '.company.authdata.password.edit', props.company), formPass, {
                preserveScroll: (page) => Object.keys(page.props.errors).length,
            })
        }

		function submitEmail() {
            Inertia.post(route(usePage().props.value.locale + '.company.authdata.email.edit', props.company), formEmail, {
                preserveScroll: (page) => Object.keys(page.props.errors).length,
            })
        }

        return { formPass, formEmail, submitPass, submitEmail }
    },
}
</script>
