<template>
    <Head title="Profile" />

    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Company Profile
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div v-if="company.detail == null" class="bg-red-500 text-white">
                            Your profile is incomplete. Please provide the missing information.
                        </div>
                        <form @submit.prevent="submit" class="flex flex-col">
                            <div class="mb-6">
                                <input type="text" v-model="form.name" class="sm:rounded-lg w-full">
                                <div v-if="errors.name" class="text-red-500">{{ errors.name }}</div>
                            </div>
                            <div class="mb-6">
                                <input type="text" v-model="form.email" class="sm:rounded-lg w-full">
                                <div v-if="errors.email" class="text-red-500">{{ errors.email }}</div>
                            </div>
                            <div v-if="!canChangeAddress" class="mb-6">
                                <button type="button" :disabled="form.processing" @click="setChangeAddress(true)" class="bg-black text-white px-4 py-2 sm:rounded-lg">Change location</button>
                            </div>
                            <div v-if="canChangeAddress" class="mb-6">
                                <GMapAutocomplete
                                    placeholder="This is a placeholder"
                                    @place_changed="setPlace"
                                    id="googlePlaceInput"
                                    class="w-full"
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
                                <input type="text" v-model="form.website_link" class="sm:rounded-lg w-full">
                                <div v-if="errors.website_link" class="text-red-500">{{ errors.website_link }}</div>
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
            logo: 'test',
            website_link: '',
        })

        const markers = ref([])
        const mapCenter = ref({lat: 51.093048, lng: 6.842120})
        const myMapRef = ref()
        const canChangeAddress = ref(true)

        onBeforeMount(() => {
            if (props.company.detail != null) {
                setChangeAddress(false)

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
            Inertia.post(route('company.profile.edit', props.company), form)
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

        return { form, submit, setPlace, setMarker, setMapCenter, markers, mapCenter, myMapRef, canChangeAddress, setChangeAddress }
    },
}
</script>
