<template>
    <Head :title="__('Payment preview')" />

    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Payment preview') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
						<Alert v-if="$page.props.session.success" :message="$page.props.session.success" :type="'success'" class="mb-4"/>
						<Alert v-if="$page.props.session.error" :message="$page.props.session.error" :type="'error'" class="mb-4"/>
						<div class="mb-4">
							<h2 class="text-lg font-semibold mb-4">{{ __('Order review') }}</h2>
							<div class="grid grid-cols-2 gap-4">
								<div class="flex flex-col">
									<span class="font-semibold">{{ __('Job offer title') }}</span>
									<span>{{ jobOffer.title }}</span>
								</div>
								<div class="flex flex-col">
									<span class="font-semibold">{{ __('Package') }}</span>
									<span>{{ jobOffer.job_offer_type.name + ' - ' + jobOffer.job_offer_type.price + ' ' + jobOffer.job_offer_type.currency }}</span>
								</div>
							</div>
						</div>
						<hr />
                        <form @submit.prevent="submit" class="flex flex-col mb-4 mt-4" v-if="!hasInvoiceDetails || editInvoiceData">
							<h2 class="font-semibold text-lg mb-4">
								{{ __('Invoice data') }}
								<span v-if="hasInvoiceDetails" class="text-sm text-gray-500 cursor-pointer hover:text-gray-700" @click="setEditInvoiceData">{{ __('Do not modify') }}</span>
							</h2>
                            <div class="mb-6">
                                <label class="text-lg font-semibold">{{ __('Name') }}</label>
                                <input type="text" v-model="form.invoice_name" class="sm:rounded-lg w-full mt-1">
                                <div v-if="errors.invoice_name" class="text-red-500">{{ errors.invoice_name }}</div>
                            </div>
							<div class="mb-6">
                                <label class="text-lg font-semibold">{{ __('Street') }}</label>
                                <input type="text" v-model="form.invoice_street" class="sm:rounded-lg w-full mt-1">
                                <div v-if="errors.invoice_street" class="text-red-500">{{ errors.invoice_street }}</div>
                            </div>
							<div class="mb-6">
                                <label class="text-lg font-semibold">{{ __('City') }}</label>
                                <input type="text" v-model="form.invoice_city" class="sm:rounded-lg w-full mt-1">
                                <div v-if="errors.invoice_city" class="text-red-500">{{ errors.invoice_city }}</div>
                            </div>
							<div class="mb-6">
                                <label class="text-lg font-semibold">{{ __('Postal code') }}</label>
                                <input type="text" v-model="form.invoice_postal_code" class="sm:rounded-lg w-full mt-1">
                                <div v-if="errors.invoice_postal_code" class="text-red-500">{{ errors.invoice_postal_code }}</div>
                            </div>
							<div class="mb-6">
                                <label class="text-lg font-semibold">{{ __('Country') }}</label>
                                <input type="text" v-model="form.invoice_country" class="sm:rounded-lg w-full mt-1">
                                <div v-if="errors.invoice_country" class="text-red-500">{{ errors.invoice_country }}</div>
                            </div>
							<div class="mb-6">
                                <label class="text-lg font-semibold">{{ __('VAT Number') }}</label>
                                <input type="text" v-model="form.invoice_vat_number" class="sm:rounded-lg w-full mt-1">
                                <div v-if="errors.invoice_vat_number" class="text-red-500">{{ errors.invoice_vat_number }}</div>
                            </div>
							<div class="mb-6">
                                <label class="text-lg font-semibold">{{ __('Phone number') }}</label>
                                <input type="text" v-model="form.invoice_phone" class="sm:rounded-lg w-full mt-1">
                                <div v-if="errors.invoice_phone" class="text-red-500">{{ errors.invoice_phone }}</div>
                            </div>
							<div class="mb-6">
                                <label class="text-lg font-semibold">{{ __('Email') }}</label>
                                <input type="text" v-model="form.invoice_email" class="sm:rounded-lg w-full mt-1">
                                <div v-if="errors.invoice_email" class="text-red-500">{{ errors.invoice_email }}</div>
                            </div>
                            <button type="submit" :disabled="form.processing" class="bg-black text-white px-4 py-2 sm:rounded-lg">{{ __('Save and Pay') }}</button>
                        </form>
						<div class="mt-4" v-else>
							<h2 class="font-semibold text-lg mb-4">
								{{ __('Invoice data') }}
								<span class="text-xs text-gray-500 cursor-pointer hover:text-gray-700" @click="setEditInvoiceData">{{ __('Modify') }}</span>
							</h2>
							<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mb-6">
								<div class="flex flex-col">
									<span class="font-semibold">{{ __('Name') }}</span>
									<span>{{ company.invoice_details.invoice_name }}</span>
								</div>
								<div class="flex flex-col">
									<span class="font-semibold">{{ __('Street') }}</span>
									<span>{{ company.invoice_details.invoice_street }}</span>
								</div>
								<div class="flex flex-col">
									<span class="font-semibold">{{ __('Postal code') }}</span>
									<span>{{ company.invoice_details.invoice_postal_code }}</span>
								</div>
								<div class="flex flex-col">
									<span class="font-semibold">{{ __('Country') }}</span>
									<span>{{ company.invoice_details.invoice_country }}</span>
								</div>
								<div class="flex flex-col">
									<span class="font-semibold">{{ __('Email') }}</span>
									<span>{{ company.invoice_details.invoice_email }}</span>
								</div>
								<div class="flex flex-col">
									<span class="font-semibold">{{ __('Phone') }}</span>
									<span>{{ company.invoice_details.invoice_phone }}</span>
								</div>
								<div class="flex flex-col">
									<span class="font-semibold">{{ __('VAT Number') }}</span>
									<span>{{ company.invoice_details.invoice_vat_number }}</span>
								</div>
							</div>
							<Link :href="route($page.props.locale + '.company.payment', [company, jobOffer])" :data="{ upgrade : isUpgrade }" class="whitespace-nowrap bg-black text-white px-4 py-2 sm:rounded-lg">
								{{ __('Pay') }}
							</Link>
						</div>
					</div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Company/Authenticated.vue'
import Alert from '@/Components/Alert.vue'
import { Head, usePage, useForm, Link } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia'
import { onBeforeMount, ref } from 'vue'

export default {
    components: {
        BreezeAuthenticatedLayout,
        Head,
        Alert,
		Link
    },
    props: {
      company: Object,
      errors: Object,
	  jobOffer: Object,
	  hasInvoiceDetails: Boolean,
	  isUpgrade: Boolean
    },
    setup (props) {
        const form = useForm({
            invoice_name: '',
			invoice_street: '',
			invoice_city: '',
			invoice_postal_code: '',
			invoice_country: '',
			invoice_phone: '',
			invoice_email: '',
			invoice_vat_number: '',
			upgrade: props.isUpgrade
		})

		let editInvoiceData = ref(false)

		const isUpgrade = props.isUpgrade

		onBeforeMount(() => {
            if (props.company.invoice_details != null) {
                form.invoice_name = props.company.invoice_details.invoice_name
                form.invoice_street = props.company.invoice_details.invoice_street
                form.invoice_city = props.company.invoice_details.invoice_city
                form.invoice_postal_code = props.company.invoice_details.invoice_postal_code
                form.invoice_country = props.company.invoice_details.invoice_country
                form.invoice_phone = props.company.invoice_details.invoice_phone
                form.invoice_email = props.company.invoice_details.invoice_email
                form.invoice_vat_number = props.company.invoice_details.invoice_vat_number
            }
		})

        function submit() {
            Inertia.post(route(usePage().props.value.locale + '.company.payment.invoicedata', [props.company, props.jobOffer]), form, {
                preserveScroll: (page) => Object.keys(page.props.errors).length,
            })
        }

		function setEditInvoiceData()
		{
			editInvoiceData.value = !editInvoiceData.value
		}

        return { form, submit, editInvoiceData, setEditInvoiceData, isUpgrade }
    },
}
</script>
