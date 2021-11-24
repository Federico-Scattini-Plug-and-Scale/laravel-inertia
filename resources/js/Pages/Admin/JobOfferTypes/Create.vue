<template>
    <Head title="Crea un nuovo tipo di offerta" />

    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Crea un nuovo tipo di offerta
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
						<form @submit.prevent="store()" class="flex flex-col">
							<label for="name">Nome</label>
							<input id="name" type="text" v-model="form.name" class="form-control w-full sm:rounded-lg">
							<div v-if="form.errors.name">{{ form.errors.name }}</div>
							<label for="stripe_product_name">Nome Stripe</label>
							<input id="stripe_product_name" type="text" v-model="form.stripe_product_name" class="form-control w-full sm:rounded-lg">
							<div v-if="form.errors.stripe_product_name">{{ form.errors.stripe_product_name }}</div>
							<label for="ranking">Ranking</label>
							<input id="ranking" type="number" min="0" max="10" v-model="form.ranking" class="form-control w-full sm:rounded-lg">
							<div v-if="form.errors.ranking">{{ form.errors.ranking }}</div>
							<label for="currency">Valuta</label>
							<input id="currency" type="text" v-model="form.currency" class="form-control w-full sm:rounded-lg">
							<div v-if="form.errors.currency">{{ form.errors.currency }}</div>
							<label for="price">Prezzo (Inserire il prezzo senza virgole o punti - esempio: 20,99 deve essere inserito come 2099)</label>
							<input id="price" type="number" min="0" v-model="form.price" class="form-control w-full sm:rounded-lg">
							<div v-if="form.errors.currency">{{ form.errors.currency }}</div>
							<label for="is_active">Attiva</label>
							<input id="is_active" type="checkbox" v-model="form.is_active">
							<button type="submit" class="bg-black text-white px-4 py-2 sm:rounded-lg mt-4 w-fit-content" :disabled="form.processing">Salva</button>
						</form>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Admin/Authenticated.vue'
import { Head, Link, useForm  } from '@inertiajs/inertia-vue3';

export default {
    components: {
        BreezeAuthenticatedLayout,
        Head,
        Link
    },
	setup () {
		const form = useForm({
			name: null,
			stripe_product_name: null,
			ranking: 0,
			currency: null,
			price: null,
			is_active: false
		})

		function store() {
			form.post(route('admin.joboffertypes.store'), {
				preserveScroll: true,
				onSuccess: () => form.reset(),
			})
		}

		return { form, store }
  	},
}
</script>
