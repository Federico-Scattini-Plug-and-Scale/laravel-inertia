<template>
    <Head :title="__('Job offer types')" />

    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Job offer types') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
						<Alert v-if="$page.props.session.success" :message="$page.props.session.success" :type="'success'" class="mb-4"/>
						<Link 
                            :href="route($page.props.locale + '.admin.joboffertypes.create')" 
                            class="bg-black text-white px-4 py-2 sm:rounded-lg"
                        >
                            {{ __('Create a new job offer type') }}
                        </Link>
						<div class="overflow-auto mt-6">
							<div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
								<table class="min-w-full leading-normal mb-16" v-if="jobOfferTypes.data.length > 0">
									<thead>
										<tr>
											<th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
												{{ __('Name') }}
											</th>
											<th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
												{{ __('Stripe name') }}
											</th>
											<th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
												{{ __('Price') }}
											</th>
											<th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
												{{ __('Ranking') }}
											</th>
											<th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
												{{ __('Stripe product ID') }}
											</th>
											<th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
												{{ __('Stripe price ID') }}
											</th>
											<th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
												{{ __('Active') }}
											</th>
											<th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
												{{ __('Free') }}
											</th>
											<th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100"></th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="(item, index) in jobOfferTypes.data" :key="index">
											<td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
												<p class="text-gray-900 whitespace-nowrap">{{ item.name }}</p>
											</td>
											<td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
												<p class="text-gray-900 whitespace-nowrap">{{ item.stripe_product_name }}</p>
											</td>
											<td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
												<p class="text-gray-900 whitespace-nowrap">{{ item.is_free ? 'Gratis' : item.price + ' ' + item.currency }}</p>
											</td>
											<td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
												<p class="text-gray-900 whitespace-nowrap">{{ item.ranking }}</p>
											</td>
											<td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
												<p class="text-gray-900 whitespace-nowrap">{{ item.stripe_product_id }}</p>
											</td>
											<td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
												<p class="text-gray-900 whitespace-nowrap">{{ item.stripe_price_id }}</p>
											</td>
											<td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
												<span class="relative inline-block px-3 py-1 font-semibold leading-tight" :class="{'text-green-900' : item.is_active, 'text-red-900' : !item.is_active}">
												<span aria-hidden="" class="absolute inset-0 opacity-50 rounded-full" :class="{'bg-green-200' : item.is_active, 'bg-red-200' : !item.is_active}"></span>
												<span class="relative">{{ item.is_active ? __('Yes') : __('No') }}</span>
												</span>
											</td>
											<td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
												<span class="relative inline-block px-3 py-1 font-semibold leading-tight" :class="{'text-green-900' : item.is_free, 'text-red-900' : !item.is_free}">
												<span aria-hidden="" class="absolute inset-0 opacity-50 rounded-full" :class="{'bg-green-200' : item.is_free, 'bg-red-200' : !item.is_free}"></span>
												<span class="relative">{{ item.is_free ? __('Yes') : __('No') }}</span>
												</span>
											</td>
											<td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-right">
												<BreezeDropdown align="right" width="48">
													<template #trigger>
														<span class="inline-flex rounded-md">
															<button type="button" class="inline-block text-gray-500 hover:text-gray-700">
																<svg class="inline-block h-6 w-6 fill-current" viewBox="0 0 24 24">
																	<path d="M12 6a2 2 0 110-4 2 2 0 010 4zm0 8a2 2 0 110-4 2 2 0 010 4zm-2 6a2 2 0 104 0 2 2 0 00-4 0z"></path>
																</svg>
															</button>
														</span>
													</template>
													<template #content>
														<BreezeDropdownLink :href="route($page.props.locale + '.admin.joboffertypes.edit', item)" as="button">
															{{ __('Modify') }}
														</BreezeDropdownLink>
														<BreezeDropdownLink :href="route($page.props.locale + '.admin.joboffertypes.destroy', item)" method="post" as="button">
															{{ __('Delete') }}
														</BreezeDropdownLink>
													</template>
												</BreezeDropdown>
											</td>
										</tr>
									</tbody>
								</table>
								<Pagination class="mt-6" :links="jobOfferTypes.links" />
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
import BreezeDropdown from '@/Components/Dropdown.vue'
import BreezeDropdownLink from '@/Components/DropdownLink.vue'
import Pagination from '@/Components/Pagination.vue'
import Alert from '@/Components/Alert.vue'
import { Head, Link  } from '@inertiajs/inertia-vue3'

export default {
    components: {
        BreezeAuthenticatedLayout,
		BreezeDropdown,
		BreezeDropdownLink,
        Head,
        Link,
		Pagination,
		Alert
    },
	props: {
		jobOfferTypes: Object,
	},
}
</script>
