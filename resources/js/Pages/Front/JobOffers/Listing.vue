<template>
	<Navbar />
	<div class="main-title-wrapper">
		<div class="container mx-auto text-center py-10 px-10 md:px-0">
			<h1 class="font-medium main-title">{{ __('All the IT job offers in one place.') }}</h1>
		</div>
	</div>
	<div class="main flex justify-center container mx-auto px-10 md:px-0 gap-x-8">
		<div class="main__content-wrapper">
			<FilterForm :technologies="technologies" @toggle-filter-modal="toggleFilterModal"/>
			<div class="listing">
				<JobOffer />
				<JobOffer />
				<JobOffer />
				<JobOffer />
				<JobOffer />
				<JobOffer />
				<JobOffer />
				<JobOffer />
				<JobOffer />
				<JobOffer />
				<JobOffer />
			</div>
		</div>
		<div v-if="showMap" class="main__map-wrapper block">
			<Map :markers="markers" />
		</div>
	</div>
	<footer></footer>
	<MapTogglerMobile @toggle-mobile-map="toggleMobileMap"/>
	<div v-if="openFilterModal" @click="toggleFilterModal()" class="overlay"></div>
	<div v-if="openFilterModal" class="filter-modal">
		<div class="flex justify-between">
			<div>
				<span>{{ __('More filters') }}</span>
			</div>
			<div>
				<div @click="toggleFilterModal()">
					<i class="fas fa-times"></i>
				</div>
			</div>
		</div>
		<div>
			<span>{{ __('Employement Type') }}</span>
		</div>
		<div class="flex justify-between">
			<div>
				<button>{{ __('Clear') }}</button>
			</div>
			<div>
				<button>{{ __('Filter') }}</button>
			</div>
		</div>
	</div>
</template>
<script>
import { Link } from '@inertiajs/inertia-vue3';
import Map from '@/Components/Map';
import Navbar from '@/Components/Navbar';
import FilterForm from '@/Components/FilterForm';
import MapTogglerMobile from '@/Components/MapTogglerMobile';
import JobOffer from '@/Components/JobOffer';
import { ref } from '@vue/reactivity';

export default {
	components : {
		Link,
		Map,
		Navbar,
		FilterForm,
		MapTogglerMobile,
		JobOffer
	},
	props: {
		offers: Object,
		markers: Object,
		technologies: Object
	},
	setup() {
		const showMap = ref(false)
		const openFilterModal = ref(false)

		const toggleMobileMap = () => { 
			showMap.value = !showMap.value
		}

		const toggleFilterModal = () => { 
			openFilterModal.value = !openFilterModal.value
		}

		if (window.innerWidth >= 1024) showMap.value = true

		return {
			showMap,
			toggleMobileMap,
			openFilterModal,
			toggleFilterModal,
		}
	},
}
</script>
