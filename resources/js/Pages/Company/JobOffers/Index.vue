<template>
    <Head :title="__('Job offers')" />

    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Job offers') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
						<Alert v-if="$page.props.session.success" :message="$page.props.session.success" :type="'success'" class="mb-4"/>
						<Alert v-if="$page.props.session.info" :message="$page.props.session.info" :type="'info'" class="mb-4"/>
						<Alert v-if="$page.props.session.error" :message="$page.props.session.error" :type="'error'" class="mb-4"/>
						<div>
							<form @submit.prevent="submit" class="mb-6 flex flex-col sm:flex-row sm:space-x-6 sm:items-center space-y-4 sm:space-y-0">
								<input class="sm:rounded-lg w-full" type="text" v-model="form.filters.title" :placeholder="__('Search by title')">
								<Multiselect 
                                    v-model="form.filters.status" 
                                    :options="statusOptions"
                                    label="label"
                                    trackBy="label"
                                    :placeholder="__('Search the status')"
                                    :searchable="false"
									class="sm:rounded-lg"
                                />
								<button type="submit" :disabled="form.processing" class="bg-black text-white px-4 py-2 sm:rounded-lg max-w-max">
									<i class="fas fa-filter"></i>
								</button>
							</form>
						</div>
						<Link 
                            :href="route($page.props.locale + '.company.joboffers.create', $page.props.auth.user)" 
                            class="bg-black text-white px-4 py-2 sm:rounded-lg"
                        >
                            {{ __('Create a new job offer') }}
                        </Link>
						<div class="overflow-auto mt-6">
							<div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
								<table class="min-w-full leading-normal mb-16" v-if="jobOffers.data.length > 0">
									<thead>
										<tr>
											<th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
												{{ __('Title') }}
											</th>
											<th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
												{{ __('Package') }}
											</th>
											<th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
												{{ __('Status') }}
											</th>
											<th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
												{{ __('Payment') }}
											</th>
											<th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
												{{ __('Published at') }}
											</th>
											<th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
												{{ __('Expiring in') }}
											</th>
											<th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100"></th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="(item, index) in jobOffers.data" :key="index">
											<td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
												<p class="text-gray-900 whitespace-nowrap">{{ item.title }}</p>
											</td>
											<td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
												<p class="text-gray-900 whitespace-nowrap">{{ item.job_offer_type.name }}</p>
											</td>
											<td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
												<span class="relative inline-block px-3 py-1 font-semibold leading-tight" :class="{ 'text-green-900' : item.status == 'active', 'text-red-900' : item.status != 'active' && item.status != 'under approval', 'text-yellow-900' : item.status == 'under approval' }">
												<span aria-hidden="" class="absolute inset-0 opacity-50 rounded-full" :class="{ 'bg-green-200' : item.status == 'active', 'bg-red-200' : item.status != 'active' && item.status != 'under approval', 'bg-yellow-200' : item.status == 'under approval' }"></span>
												<span class="relative">{{ item.status }}</span>
												</span>
											</td>
											<td v-if="item.job_offer_type.is_free != true" class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
												<Link v-if="item.status != 'under approval'" :href="route($page.props.locale + '.company.payment.preview', [$page.props.auth.user, item])" class="whitespace-nowrap bg-black text-white px-4 py-2 sm:rounded-lg">
													{{ item.status != 'active' ? __('Pay now') : __('Extend validity') }}
												</Link>
												<span v-else class="relative inline-block px-3 py-1 font-semibold leading-tight text-yellow-900">
												<span aria-hidden="" class="absolute inset-0 opacity-50 rounded-full bg-yellow-200"></span>
												<span class="relative">{{ item.status }}</span>
												</span>
											</td>
											<td v-else class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
												<span class="relative inline-block px-3 py-1 font-semibold leading-tight text-green-900">
												<span aria-hidden="" class="absolute inset-0 opacity-50 rounded-full bg-green-200"></span>
												<span class="relative">{{ __('Free') }}</span>
												</span>
											</td>
											<td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
												<p v-if="item.published_at != null" class="text-gray-900 whitespace-nowrap">{{ item.published_at }}</p>
												<span v-else class="relative inline-block px-3 py-1 font-semibold leading-tight text-red-900">
												<span aria-hidden="" class="absolute inset-0 opacity-50 rounded-full bg-red-200"></span>
												<span class="relative">{{ __('Expired') }}</span>
												</span>
											</td>
											<td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
												<p v-if="item.expiring_at != 'Expired'" class="text-gray-900 whitespace-nowrap">{{ item.expiring_at + __(' days') }}</p>
												<span v-else class="relative inline-block px-3 py-1 font-semibold leading-tight text-red-900">
												<span aria-hidden="" class="absolute inset-0 opacity-50 rounded-full bg-red-200"></span>
												<span class="relative">{{ item.expiring_at }}</span>
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
														<BreezeDropdownLink :href="route($page.props.locale + '.company.joboffers.edit', [$page.props.auth.user, item])" as="button">
															{{ __('Modify') }}
														</BreezeDropdownLink>
														<BreezeDropdownLink :href="route($page.props.locale + '.company.joboffers.destroy', [$page.props.auth.user, item])" method="post" as="button">
															{{ __('Delete') }}
														</BreezeDropdownLink>
													</template>
												</BreezeDropdown>
											</td>
										</tr>
									</tbody>
								</table>
								<Pagination class="mt-6" :links="jobOffers.links" />
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
import Pagination from '@/Components/Pagination.vue'
import Alert from '@/Components/Alert.vue'
import { Head, Link, useForm, usePage  } from '@inertiajs/inertia-vue3'
import Multiselect from '@vueform/multiselect'
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
		Alert,
		Multiselect,
    },
	props: {
		jobOffers: Object,
		statusOptions: Object,
		company: Object,
		filters: Object
	},
	setup (props) {
		const filters = toRef(props, 'filters')

        const form = useForm({
            filters : {
				title: '',
				status: '',
			}
		})

		onBeforeMount(() => {
			form.filters.title = filters.value.title
			form.filters.status = filters.value.status
		})

        function submit() {
            Inertia.get(route(usePage().props.value.locale + '.company.joboffers.index', props.company), form, {
                preserveScroll: (page) => Object.keys(page.props.errors).length,
            })
        }

        return { 
			form, 
			submit, 
			filters
		}
    },
}
</script>
<style src="@vueform/multiselect/themes/default.css"></style>