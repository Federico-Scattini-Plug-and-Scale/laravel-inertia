<template>
    <Head title="Tipi di offerte" />

    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tipi di offerte
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
						<Link 
                            :href="route('admin.joboffertypes.create')" 
                            class="bg-black text-white px-4 py-2 sm:rounded-lg"
                        >
                            Crea nuovo tipo di offerta
                        </Link>
						<div class="overflow-auto mt-6">
							<div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
								<table class="min-w-full leading-normal" v-if="jobOfferTypes.length > 0">
									<thead>
										<tr>
											<th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
												Nome
											</th>
											<th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
												Nome Stripe
											</th>
											<th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
												Prezzo
											</th>
											<th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
												Ranking
											</th>
											<th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
												ID Prodotto Stripe
											</th>
											<th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
												ID Prezzo Stripe
											</th>
											<th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
												Attivo
											</th>
											<th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
												Gratis
											</th>
											<th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100"></th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="(item, index) in jobOfferTypes" :key="index">
											<td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
												<p class="text-gray-900 whitespace-no-wrap">{{ item.name }}</p>
											</td>
											<td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
												<p class="text-gray-900 whitespace-no-wrap">{{ item.stripe_product_name }}</p>
											</td>
											<td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
												<p class="text-gray-900 whitespace-no-wrap">{{ item.is_free ? 'Gratis' : item.price + ' ' + item.currency }}</p>
											</td>
											<td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
												<p class="text-gray-900 whitespace-no-wrap">{{ item.ranking }}</p>
											</td>
											<td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
												<p class="text-gray-900 whitespace-no-wrap">{{ item.stripe_product_id }}</p>
											</td>
											<td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
												<p class="text-gray-900 whitespace-no-wrap">{{ item.stripe_price_id }}</p>
											</td>
											<td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
												<span class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight">
												<span aria-hidden="" class="absolute inset-0 bg-green-200 opacity-50 rounded-full"></span>
												<span class="relative">{{ item.is_active ? 'Si' : 'No' }}</span>
												</span>
											</td>
											<td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
												<span class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight">
												<span aria-hidden="" class="absolute inset-0 bg-green-200 opacity-50 rounded-full"></span>
												<span class="relative">{{ item.is_free ? 'Si' : 'No' }}</span>
												</span>
											</td>
											<td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-right">
												<button type="button" class="inline-block text-gray-500 hover:text-gray-700">
													<svg class="inline-block h-6 w-6 fill-current" viewBox="0 0 24 24">
														<path d="M12 6a2 2 0 110-4 2 2 0 010 4zm0 8a2 2 0 110-4 2 2 0 010 4zm-2 6a2 2 0 104 0 2 2 0 00-4 0z"></path>
													</svg>
												</button>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Admin/Authenticated.vue'
import { Head, Link  } from '@inertiajs/inertia-vue3';
import { toRef } from 'vue'
import { Inertia } from '@inertiajs/inertia';

export default {
    components: {
        BreezeAuthenticatedLayout,
        Head,
        Link
    },
	props: {
		jobOfferTypes: Object,
	},
	setup(props) {
		const jobOfferTypes = toRef(props, 'jobOfferTypes')
		
		return {
			jobOfferTypes,
		}
	}
}
</script>
