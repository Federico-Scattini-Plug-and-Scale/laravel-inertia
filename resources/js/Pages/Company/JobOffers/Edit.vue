<template>
    <Head :title="__('Job Offers')" />

    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit the job offer') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit" class="flex flex-col mb-4">
                            <div class="mb-6">
                                <label class="text-lg font-semibold">{{ __('Title') }}</label>
                                <input type="text" v-model="form.title" class="sm:rounded-lg w-full mt-1">
                                <div v-if="errors.title" class="text-red-500">{{ errors.title }}</div>
                            </div>
							<div class="mb-6">
                                <label class="text-lg font-semibold">{{ __('Description') }}</label>
                                <div>
                                    <Editor v-model="form.description" />
                                </div>
                                <div v-if="errors.description" class="text-red-500">{{ errors.description }}</div>
                            </div>
							<div v-if="!canChangeAddress" class="mb-6">
                                <button type="button" :disabled="form.processing" @click="setChangeAddress(true)" class="bg-black text-white px-4 py-2 sm:rounded-lg">{{ __('Change location') }}</button>
                            </div>
                            <div v-if="canChangeAddress" class="mb-6">
                                <label class="text-lg font-semibold">{{ __('Address') }}</label>
                                <GMapAutocomplete
                                    @place_changed="setPlace"
                                    id="googlePlaceInput"
                                    class="w-full mt-1"
                                    :options="{
                                        componentRestrictions: {
                                            country: $page.props.country
                                        }
                                    }"
                                >
                                </GMapAutocomplete>
                                <div v-if="errors.address" class="text-red-500">{{ errors.address }}</div>
                            </div>
                            <GMapMap                                 
                                :center="mapCenter"
                                :zoom="7"
                                class="map mb-6 sm:rounded-lg"
                                ref="myMapRef"
                            >
                                <GMapMarker
                                    :key="index"
                                    v-for="(m, index) in markers"
                                    :clickable="false"
                                    :draggable="false"
                                    :position="m.position"
                                />
                            </GMapMap>
                            <input type="hidden" v-model="form.latitude" class="sm:rounded-lg w-full">
                            <input type="hidden" v-model="form.longitude" class="sm:rounded-lg w-full">
                            <div class="mb-6">
                                <label class="text-lg font-semibold">{{ __('Choose the category') }}</label>
                                <Multiselect 
                                    v-model="form.category" 
                                    :options="categories"
                                    label="label"
                                    trackBy="label"
                                    :placeholder="__('Select the category')"
                                    :searchable="true"
                                />
                                <div v-if="errors.category" class="text-red-500">{{ errors.category }}</div>
                            </div>
                            <div class="mb-6" v-if="programmingLang.length != 0">
                                <label class="text-lg font-semibold">{{ __('Must have programming language') }}</label>
                                <Multiselect 
                                    v-model="form.tech_id" 
                                    :options="programmingLang"
                                    label="label"
                                    trackBy="value"
                                    :placeholder="__('Select the programming language')"
                                />
                                <div v-if="errors.programmingLang" class="text-red-500">{{ errors.programmingLang }}</div>
                            </div>
                            <div class="mb-6" v-if="programmingLang.length != 0">
                                <label class="text-lg font-semibold">{{ __('Nice to have programming language') }}</label>
                                <Multiselect 
                                    v-model="form.programmingLang" 
                                    :options="programmingLang"
                                    label="label"
                                    trackBy="value"
                                    :placeholder="__('Select the programming language')"
                                    mode="tags"
                                    :createTag="true"
                                    :searchable="true"
                                    :appendNewTag="true"
                                />
                                <div v-if="errors.programmingLang" class="text-red-500">{{ errors.programmingLang }}</div>
                            </div>
							<div class="mb-6" v-if="languages.length != 0">
                                <label class="text-lg font-semibold">{{ __('Language') }}</label>
                                <Multiselect 
                                    v-model="form.languages" 
                                    :options="languages"
                                    label="label"
                                    trackBy="value"
                                    :placeholder="__('Select the language')"
                                    mode="tags"
                                    :createTag="true"
                                    :searchable="true"
                                    :appendNewTag="true"
                                />
                                <div v-if="errors.languages" class="text-red-500">{{ errors.languages }}</div>
                            </div>
							<div class="mb-6" v-if="exp.length != 0">
                                <label class="text-lg font-semibold">{{ __('Experience') }}</label>
                                <Multiselect 
                                    v-model="form.exp" 
                                    :options="exp"
                                    label="label"
                                    trackBy="value"
                                    :placeholder="__('Select the experience')"
                                    mode="tags"
                                    :createTag="true"
                                    :searchable="true"
                                    :appendNewTag="true"
                                />
                                <div v-if="errors.exp" class="text-red-500">{{ errors.exp }}</div>
                            </div>
							<div class="mb-6" v-if="contracts.length != 0">
                                <label class="text-lg font-semibold">{{ __('Contracts') }}</label>
                                <Multiselect 
                                    v-model="form.contracts" 
                                    :options="contracts"
                                    label="label"
                                    trackBy="value"
                                    :placeholder="__('Select the contracts')"
                                    mode="tags"
                                    :createTag="true"
                                    :searchable="true"
                                    :appendNewTag="true"
                                />
                                <div v-if="errors.contracts" class="text-red-500">{{ errors.contracts }}</div>
                            </div>
							<div class="mb-6">
                                <label class="text-lg font-semibold">{{ __('Min salary') }}</label>
                                <input type="number" min="0" v-model="form.min_salary" class="sm:rounded-lg w-full mt-1">
                                <div v-if="errors.min_salary" class="text-red-500">{{ errors.min_salary }}</div>
                            </div>
							<div class="mb-6">
                                <label class="text-lg font-semibold">{{ __('Max salary') }}</label>
                                <input type="number" min="0" v-model="form.max_salary" class="sm:rounded-lg w-full mt-1">
                                <div v-if="errors.max_salary" class="text-red-500">{{ errors.max_salary }}</div>
                            </div>
							<div class="mb-6">
                                <label class="text-lg font-semibold">{{ __('Currency') }}</label>
                                <input type="text" v-model="form.currency" class="sm:rounded-lg w-full mt-1">
                                <div v-if="errors.currency" class="text-red-500">{{ errors.currency }}</div>
                            </div>
                            <button type="submit" :disabled="form.processing" class="bg-black text-white px-4 py-2 sm:rounded-lg">{{ __('Save') }}</button>
                        </form>
					</div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Company/Authenticated.vue'
import Editor from '@/Components/Editor.vue'
import { Head, usePage, useForm } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia'
import { ref, watch, onMounted } from 'vue'
import Multiselect from '@vueform/multiselect'


export default {
    components: {
        BreezeAuthenticatedLayout,
        Head,
		Multiselect,
        Editor
    },
    props: {
		company: Object,
		jobOffer: Object,
		tags: Object,
      	errors: Object,
		languages: Object,
		programmingLang: Object,
		exp: Object,
		contracts: Object,
        categories: Object,
    },
    setup (props) {
        const form = useForm({
            title: props.jobOffer.title,
			description: props.jobOffer.description,
			address: props.jobOffer.address,
            region: props.jobOffer.region,
            province: props.jobOffer.province,
            city: props.jobOffer.city,
            country: props.jobOffer.country,
            postal_code: props.jobOffer.postal_code,
            latitude: props.jobOffer.latitude,
            longitude: props.jobOffer.longitude,
			languages: props.tags.group_language ? props.tags.group_language : [],
			programmingLang: props.tags.group_programming_lang ? props.tags.group_programming_lang : [],
			exp: props.tags.group_exp ? props.tags.group_exp : [],
			contracts: props.tags.group_contract_type ? props.tags.group_contract_type : [],
			specialization: props.jobOffer.specialization,
			min_salary: props.jobOffer.min_salary,
			max_salary: props.jobOffer.max_salary,
			currency: props.jobOffer.currency,
            category: props.jobOffer.category_id,
            tech_id: props.jobOffer.tech_id
		})

		const markers = ref([])
        const mapCenter = ref({lat: 51.093048, lng: 6.842120})
        const myMapRef = ref()
        const canChangeAddress = ref(true)

        watch([myMapRef, markers], ([googleMap]) => {
            if (googleMap) googleMap.$mapPromise.then(map => map.setCenter(mapCenter.value))
        })

        function submit() {
            Inertia.post(route(usePage().props.value.locale + '.company.joboffers.update', [props.company, props.jobOffer]), form, {
                preserveScroll: (page) => Object.keys(page.props.errors).length,
            })
        }

        function resetPlace() {
            form.reset(
                'latitude',
                'longitude',
                'address',
                'region',
                'country',
                'city',
                'province',
                'postal_code'
            )
        }

        function setPlace(e) {
            resetPlace()

            form.latitude = e.geometry.location.lat()
            form.longitude = e.geometry.location.lng()
            form.address = e.formatted_address

            e.address_components.forEach(item => {
                if (item.types[0] == 'administrative_area_level_1')
                    form.region = item.long_name
                if (item.types[0] == 'administrative_area_level_2')
                    form.province = item.long_name
                if (item.types[0] == 'locality')
                    form.city = item.long_name
                if (item.types[0] == 'country')
                    form.country = item.long_name
                if (item.types[0] == 'postal_code')
                    form.postal_code = item.long_name
            });

            setMarker(
                {
                    position:{
                        lat: e.geometry.location.lat(), 
                        lng: e.geometry.location.lng()
                    }
                }
            )
        }

        function setMarker(position) {
            markers.value = []
            markers.value.push(position)
            setMapCenter(position.position)
            setChangeAddress(false)
        }

        function setMapCenter(position) {
            mapCenter.value = position
        }

        function setChangeAddress(value) {
            canChangeAddress.value = value
        }

		onMounted(() => {
			setMarker({
				position: {
					lat: parseFloat(props.jobOffer.latitude),
					lng: parseFloat(props.jobOffer.longitude),
				}
			})
		})

        return { 
			form, 
			submit, 
			setPlace, 
			setMarker, 
			setMapCenter, 
			markers, 
			mapCenter, 
			myMapRef, 
			canChangeAddress, 
			setChangeAddress,
		}
    },
}
</script>
<style src="@vueform/multiselect/themes/default.css"></style>