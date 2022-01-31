<template>
    <Head :title="__('Invoices')" />

    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Invoices') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
						<div>
							<form @submit.prevent="submit" class="mb-6 flex flex-col sm:flex-row sm:space-x-6 sm:items-center space-y-4 sm:space-y-0">
								<input class="sm:rounded-lg w-full" type="text" v-model="form.filters.number" :placeholder="__('Search by invoice number')">
								<input class="sm:rounded-lg w-full" type="date" v-model="form.filters.date" :placeholder="__('Search by date')">
								<button type="submit" :disabled="form.processing" class="bg-black text-white px-4 py-2 sm:rounded-lg max-w-max">
									<i class="fas fa-filter"></i>
								</button>
							</form>
						</div>
						<div class="overflow-auto mt-6">
							<div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
								<table class="min-w-full leading-normal mb-24" v-if="invoices.data.length > 0">
									<thead>
										<tr>
											<th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
												{{ __('Invoice number') }}
											</th>
											<th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
												{{ __('Date') }}
											</th>
											<th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
												{{ __('Amount') }}
											</th>
											<th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100"></th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="(item, index) in invoices.data" :key="index">
											<td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
												<p class="text-gray-900 whitespace-nowrap">{{ item.invoice_number }}</p>
											</td>
											<td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
												<p class="text-gray-900 whitespace-nowrap">{{ item.date }}</p>
											</td>
											<td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
												<p class="text-gray-900 whitespace-nowrap">{{ item.total_price }}</p>
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
														<DropdownBasicLink :href="route($page.props.locale + '.company.invoices.preview', [$page.props.auth.user, item])" target="_blank">
															{{  __('Preview') }}
														</DropdownBasicLink>
														<DropdownBasicLink :href="route($page.props.locale + '.company.invoices.download', [$page.props.auth.user, item])">
															{{  __('Download') }}
														</DropdownBasicLink>
													</template>
												</BreezeDropdown>
											</td>
										</tr>
									</tbody>
								</table>
								<Pagination class="mt-6" :links="invoices.links" />
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
import BreezeDropdown from '@/Components/Dropdown.vue'
import BreezeDropdownLink from '@/Components/DropdownLink.vue'
import DropdownElement from '@/Components/DropdownElement.vue'
import DropdownBasicLink from '@/Components/DropdownBasicLink.vue'
import Pagination from '@/Components/Pagination.vue'
import { Head, Link, useForm, usePage  } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import { toRef, onBeforeMount } from 'vue'

export default {
    components: {
        BreezeAuthenticatedLayout,
		BreezeDropdown,
		BreezeDropdownLink,
        Head,
        Link,
		Pagination,
		DropdownElement,
		DropdownBasicLink
	},
	props: {
		invoices: Object,
		company: Object,
		filters: Object
	},
	setup (props) {
		const filters = toRef(props, 'filters')

        const form = useForm({
            filters : {
				date: '',
				number: '',
			}
		})

		onBeforeMount(() => {
			form.filters.date = filters.value.date
			form.filters.number = filters.value.number
		})	

        function submit() {
            Inertia.get(route(usePage().props.value.locale + '.company.invoices.index', props.company), form, {
                preserveScroll: (page) => Object.keys(page.props.errors).length,
            })
        }

        return { 
			form, 
			submit, 
			filters,
		}
    },
}
</script>
