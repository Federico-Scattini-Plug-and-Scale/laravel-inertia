<template>
    <Head title="Modifica l'offerta" />

    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Modifica l'offerta
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
						<Alert v-if="successMessage.length > 0" :message="successMessage" :type="'success'" />
						<form @submit.prevent="update()" class="flex flex-col">
							<label for="name">Nome</label>
							<input id="name" type="text" v-model="form.name" class="form-control w-full sm:rounded-lg">
							<div v-if="form.errors.name" class="text-red-500">{{ form.errors.name }}</div>
							<label for="stripe_product_name">Nome Stripe</label>
							<input id="stripe_product_name" type="text" v-model="form.stripe_product_name" class="form-control w-full sm:rounded-lg">
							<div v-if="form.errors.stripe_product_name" class="text-red-500">{{ form.errors.stripe_product_name }}</div>
							<label for="ranking">Ranking</label>
							<input id="ranking" type="number" min="0" max="10" v-model="form.ranking" class="form-control w-full sm:rounded-lg">
							<div v-if="form.errors.ranking" class="text-red-500">{{ form.errors.ranking }}</div>
							<label for="currency">Valuta</label>
							<input id="currency" type="text" v-model="form.currency" class="form-control w-full sm:rounded-lg">
							<div v-if="form.errors.currency" class="text-red-500">{{ form.errors.currency }}</div>
							<label for="price">Prezzo</label>
							<input id="price" type="number" min="1" step="0.01" v-model="form.price" class="form-control w-full sm:rounded-lg">
							<div v-if="form.errors.price" class="text-red-500">{{ form.errors.price }}</div>
							<label for="is_free">Gratis</label>
							<input id="is_free" type="checkbox" v-model="form.is_free">
							<div v-if="form.errors.is_free" class="text-red-500">{{ form.errors.is_free }}</div>
							<label for="is_active">Attiva</label>
							<input id="is_active" type="checkbox" v-model="form.is_active">
							<div v-if="form.errors.is_active" class="text-red-500">{{ form.errors.is_active }}</div>
							<button type="submit" class="bg-black text-white px-4 py-2 sm:rounded-lg mt-4 w-fit-content" :disabled="form.processing">Salva</button>
						</form>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Admin/Authenticated.vue';
import { Head, Link, useForm  } from '@inertiajs/inertia-vue3';
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
		jobOfferType: Object
	},
	setup (props) {
		const jobOfferType = toRef(props, 'jobOfferType')

		const form = useForm({
			name: jobOfferType.value.name,
			stripe_product_name: jobOfferType.value.stripe_product_name,
			ranking: jobOfferType.value.ranking,
			currency: jobOfferType.value.currency,
			price: jobOfferType.value.price,
			is_active: jobOfferType.value.is_active,
			is_free: jobOfferType.value.is_free
		})

		let successMessage = ref('');

		function update() {
			form.post(route('admin.joboffertypes.update', jobOfferType.value), {
				preserveScroll: true,
				onSuccess: () => {
					successMessage.value = 'Il tipo di offerta è stata modificata correttamente.'
				},
			})
		}

		return { form, update, successMessage, jobOfferType }
  	},
}
</script>
