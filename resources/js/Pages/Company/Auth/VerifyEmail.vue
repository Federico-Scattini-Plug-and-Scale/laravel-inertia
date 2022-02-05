<template>
    <Head :title="__('Email Verification')" />

    <div class="mb-4 text-sm text-gray-600">
        {{ __("Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.") }}
    </div>

    <div class="mb-4 font-medium text-sm text-green-600" v-if="verificationLinkSent" >
        {{ __('A new verification link has been sent to the email address you provided during registration.') }}
    </div>

    <form @submit.prevent="submit">
        <div class="mt-4 flex items-center justify-between">
            <BreezeButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                {{ __('Resend Verification Email') }}
            </BreezeButton>

            <Link :href="route($page.props.locale + '.company.logout')" method="post" as="button" class="underline text-sm text-gray-600 hover:text-gray-900">{{ __('Log Out') }}</Link>
        </div>
    </form>
</template>

<script>
import BreezeButton from '@/Components/Button.vue'
import BreezeGuestLayout from '@/Layouts/Company/Guest.vue'
import { Head, Link, usePage } from '@inertiajs/inertia-vue3';

export default {
    layout: BreezeGuestLayout,

    components: {
        BreezeButton,
        Head,
        Link,
    },

    data() {
        return {
            form: this.$inertia.form()
        }
    },

    methods: {
        submit() {
            this.form.post(this.route(usePage().props.value.locale + '.company.verification.send'))
        },
    },

    computed: {
        verificationLinkSent() {
            return usePage().props.value.status === 'verification-link-sent';
        }
    }
}
</script>
