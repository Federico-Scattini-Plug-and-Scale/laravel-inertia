<template>
    <Head :title="__('Upgrade package')" />

    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Upgrade package') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
						<Alert v-if="$page.props.session.success" :message="$page.props.session.success" :type="'success'" class="mb-4"/>
						<Alert v-if="$page.props.session.error" :message="$page.props.session.error" :type="'error'" class="mb-4"/>
						<div class="mb-4">
							<h2 class="text-lg font-semibold mb-4">{{ __('Upgrade the package for the current job offer') }}</h2>
							<div class="flex">
								<form @submit.prevent="submit">
									<div class="grid grid-cols-3 gap-2 w-full max-w-screen-sm">
										<div v-for="(jobOfferType, index) in jobOfferTypes" :key="index">
											<label class="flex flex-col p-4 border-2 border-gray-400 cursor-pointer" :class="{ 'border border-red-500' : form.job_offer_type == jobOfferType }">
												<input class="hidden package-radio" type="radio" v-model="form.job_offer_type_id" :value="jobOfferType">
												<span class="text-xs font-semibold uppercase">{{ jobOfferType.name }}</span>
												<span class="text-xl font-bold mt-2">{{ jobOfferType.price + ' ' + jobOfferType.currency }}</span>
											</label>
										</div>
									</div>
									<button type="submit" :disabled="form.processing" class="bg-black text-white px-4 py-2 sm:rounded-lg mt-4">{{ __('Preview and Pay') }}</button>								
								</form>
							</div>
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
		jobOfferTypes: Object
    },
    setup (props) {
        const form = useForm({
            job_offer_type: props.jobOfferTypes[0]
		})

        function submit() {
            Inertia.post(route(usePage().props.value.locale + '.company.payment.upgrade.store', [props.company, props.jobOffer]), form, {
                preserveScroll: (page) => Object.keys(page.props.errors).length,
            })
        }

        return { form, submit }
    },
}
</script>
