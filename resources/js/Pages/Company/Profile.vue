<template>
    <Head :title="__('Profile')" />

    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ company.email }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <Alert v-if="company.detail == null" :message="__('Your profile is incomplete. Please provide the missing information.')" :type="'error'" class="mb-4"/>
                        <Alert v-if="$page.props.session.success" :message="$page.props.session.success" :type="'success'" class="mb-4"/>
                        <form @submit.prevent="submit" class="flex flex-col">
                            <div class="mb-6">
                                <label class="text-lg font-semibold">{{ __('Company name') }}</label>
                                <input type="text" v-model="form.name" class="sm:rounded-lg w-full mt-1">
                                <div v-if="errors.name" class="text-red-500">{{ errors.name }}</div>
                            </div>
                            <div class="mb-6">
                                <label class="text-lg font-semibold mr-3">{{ __('Are you an agency?') }}</label>
                                <input type="checkbox" v-model="form.is_agency" class="sm:rounded-lg mt-1">
                                <div v-if="errors.is_agency" class="text-red-500">{{ errors.is_agency }}</div>
                            </div>
                            <div class="mb-6">
                                <label class="text-lg font-semibold">{{ __('Contact name') }}</label>
                                <input type="text" v-model="form.contact_name" class="sm:rounded-lg w-full mt-1">
                                <div v-if="errors.contact_name" class="text-red-500">{{ errors.contact_name }}</div>
                            </div>
                            <div class="mb-6">
                                <label class="text-lg font-semibold">{{ __('Phone number') }}</label>
                                <input type="text" v-model="form.phone" class="sm:rounded-lg w-full mt-1">
                                <div v-if="errors.phone" class="text-red-500">{{ errors.phone }}</div>
                            </div>
                            <div class="mb-6">
                                <label class="text-lg font-semibold">{{ __('Email') }}</label>
                                <input type="text" v-model="form.email" class="sm:rounded-lg w-full mt-1" readonly>
                                <div v-if="errors.email" class="text-red-500">{{ errors.email }}</div>
                            </div>
                            <div class="mb-6 flex">
                                <label class="text-lg font-semibold mr-6">{{ __('Logo') }}</label>
                                <div v-if="canChangeLogo || errors.logo">
                                    <input type="file" @input="form.logo = $event.target.files[0]" />
                                    <div v-if="errors.logo" class="text-red-500">{{ errors.logo }}</div>
                                </div>
                                <button v-else type="button" :disabled="form.processing" @click="setChangeLogo(true)" class="bg-black text-white px-4 py-2 sm:rounded-lg">
                                    <span v-if="company.detail.logo">{{ __('Change logo') }}</span>
                                    <span v-else>{{ __('Upload logo') }}</span>
                                </button>
                            </div>
                            <div v-if="company.detail && company.detail.logo" class="mb-6 flex items-center">
                                <img :src="'/img/' + company.detail.logo" class="rounded-full w-32 h-32 mr-6"/>
                                <span>{{ company.detail.logo }}</span>
                            </div>
                            <div class="mb-6">
                                <label class="text-lg font-semibold">{{ __('Sectors') }}</label>
                                <Multiselect 
                                    v-model="form.sectors" 
                                    :options="sectors"
                                    label="label"
                                    trackBy="value"
                                    :placeholder="__('Select the sector')"
                                    mode="tags"
                                    :createTag="true"
                                    :searchable="true"
                                    :appendNewTag="true"
                                />
                                <div v-if="errors.sectors" class="text-red-500">{{ errors.sectors }}</div>
                            </div>
                            <div class="mb-6">
                                <label class="text-lg font-semibold">{{ __('Website') }}</label>
                                <input type="text" v-model="form.website_link" class="sm:rounded-lg w-full mt-1">
                                <div v-if="errors.website_link" class="text-red-500">{{ errors.website_link }}</div>
                            </div>
                            <div class="mb-6">
                                <label class="text-lg font-semibold">{{ __('Description') }}</label>
                                <textarea v-model="form.description" class="sm:rounded-lg w-full mt-1"></textarea>
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
import Alert from '@/Components/Alert.vue'
import { Head, usePage, useForm } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia'
import { onBeforeMount, ref, watch, toRef } from 'vue'
import Multiselect from '@vueform/multiselect'

export default {
    components: {
        BreezeAuthenticatedLayout,
        Head,
        Alert,
        Multiselect
    },
    props: {
      company: Object,
      errors: Object,
      sectors: Object,
      companySectors: Object
    },
    setup (props) {
        const form = useForm({
            email: props.company.email,
            name: '',
            address: '',
            latitude: '',
            longitude: '',
            logo: '',
            website_link: '',
            description: '',
            is_agency: false,
            phone: '',
            sectors: []
        })

        const markers = ref([])
        const mapCenter = ref({lat: 51.093048, lng: 6.842120})
        const myMapRef = ref()
        const canChangeAddress = ref(true)
        const canChangeLogo = ref(true)
        const sectors = toRef(props, 'sectors')

        onBeforeMount(() => {
            if (props.company.detail != null) {
                setChangeAddress(false)
                setChangeLogo(false)

                form.name = props.company.detail.name
                form.address = props.company.detail.address
                form.latitude = props.company.detail.latitude
                form.longitude = props.company.detail.longitude
                form.logo = props.company.detail.logo
                form.website_link = props.company.detail.website_link
                form.phone = props.company.detail.phone
                form.description = props.company.detail.description
                form.is_agency = props.company.detail.is_agency
                form.contact_name = props.company.detail.contact_name

                setMarker(
                    {
                        position:{
                            lat: parseFloat(props.company.detail.latitude),
                            lng: parseFloat(props.company.detail.longitude)
                        }
                    }
                )
            }

            if (props.companySectors != null)
            {
                form.sectors = props.companySectors
            }
        })

        watch([myMapRef, markers], ([googleMap]) => {
            if (googleMap) googleMap.$mapPromise.then(map => map.setCenter(mapCenter.value))
        })

        function submit() {
            Inertia.post(route(usePage().props.value.locale + '.company.profile.edit', props.company), form, {
                preserveScroll: (page) => Object.keys(page.props.errors).length,
            })
        }

        function setPlace(e) {
            form.latitude = e.geometry.location.lat()
            form.longitude = e.geometry.location.lng()
            form.address = e.formatted_address

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

        function setChangeLogo(value) {
            canChangeLogo.value = value
        }

        return { form, submit, setPlace, setMarker, setMapCenter, markers, mapCenter, myMapRef, canChangeAddress, setChangeAddress, canChangeLogo, setChangeLogo, sectors }
    },
}
</script>
<style src="@vueform/multiselect/themes/default.css"></style>