<template>
    <Head title="Profile" />

    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ company.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div v-if="company.detail == null" class="bg-red-500 text-white sm:rounded-lg p-2 mb-6">
                            Your profile is incomplete. Please provide the missing information.
                        </div>
                        <form @submit.prevent="submit" class="flex flex-col">
                            <div class="mb-6">
                                <label class="text-lg font-semibold">Company name</label>
                                <input type="text" v-model="form.name" class="sm:rounded-lg w-full mt-1">
                                <div v-if="errors.name" class="text-red-500">{{ errors.name }}</div>
                            </div>
                            <div class="mb-6">
                                <label class="text-lg font-semibold">Email</label>
                                <input type="text" v-model="form.email" class="sm:rounded-lg w-full mt-1">
                                <div v-if="errors.email" class="text-red-500">{{ errors.email }}</div>
                            </div>
                            <div v-if="!canChangeAddress" class="mb-6">
                                <button type="button" :disabled="form.processing" @click="setChangeAddress(true)" class="bg-black text-white px-4 py-2 sm:rounded-lg">Change location</button>
                            </div>
                            <div v-if="canChangeAddress" class="mb-6">
                                <label class="text-lg font-semibold">Address</label>
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
                            <div class="mb-6">
                                <label class="text-lg font-semibold">Website</label>
                                <input type="text" v-model="form.website_link" class="sm:rounded-lg w-full mt-1">
                                <div v-if="errors.website_link" class="text-red-500">{{ errors.website_link }}</div>
                            </div>
                            <div class="mb-6 flex">
                                <label class="text-lg font-semibold mr-6">Logo</label>
                                <div v-if="canChangeLogo || errors.logo">
                                    <input type="file" @input="form.logo = $event.target.files[0]" />
                                    <div v-if="errors.logo" class="text-red-500">{{ errors.logo }}</div>
                                </div>
                                <button v-else type="button" :disabled="form.processing" @click="setChangeLogo(true)" class="bg-black text-white px-4 py-2 sm:rounded-lg">
                                    <span v-if="company.detail.logo">Change logo</span>
                                    <span v-else>Upload logo</span>
                                </button>
                            </div>
                            <div v-if="company.detail && company.detail.logo" class="mb-6 flex items-center">
                                <img :src="'/img/' + company.detail.logo" class="rounded-full w-32 h-32 mr-6"/>
                                <span>{{ company.detail.logo }}</span>
                            </div>
                            <button type="submit" :disabled="form.processing" class="bg-black text-white px-4 py-2 sm:rounded-lg">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Company/Authenticated.vue'
import { Head } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia'
import { useForm } from '@inertiajs/inertia-vue3'
import { onBeforeMount, ref, watch } from 'vue'

export default {
    components: {
        BreezeAuthenticatedLayout,
        Head,
    },
    props: {
      company: Object,
      errors: Object
    },
    setup (props) {
        const form = useForm({
            email: props.company.email,
            name: props.company.name,
            address: '',
            latitude: '',
            longitude: '',
            logo: '',
            website_link: '',
        })

        const markers = ref([])
        const mapCenter = ref({lat: 51.093048, lng: 6.842120})
        const myMapRef = ref()
        const canChangeAddress = ref(true)
        const canChangeLogo = ref(true)

        onBeforeMount(() => {
            if (props.company.detail != null) {
                setChangeAddress(false)
                setChangeLogo(false)

                form.address = props.company.detail.address
                form.latitude = props.company.detail.latitude
                form.longitude = props.company.detail.longitude
                form.logo = props.company.detail.logo
                form.website_link = props.company.detail.website_link

                setMarker(
                    {
                        position:{
                            lat: parseFloat(props.company.detail.latitude),
                            lng: parseFloat(props.company.detail.longitude)
                        }
                    }
                )
            }
        })

        watch([myMapRef, markers], ([googleMap]) => {
            if (googleMap) googleMap.$mapPromise.then(map => map.setCenter(mapCenter.value))
        })

        function submit() {
            Inertia.post(route($page.props.locale + '.company.profile.edit', props.company), form, {
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

        return { form, submit, setPlace, setMarker, setMapCenter, markers, mapCenter, myMapRef, canChangeAddress, setChangeAddress, canChangeLogo, setChangeLogo }
    },
}
</script>
