<template>
	<Navbar />
	<div class="main-title-wrapper">
		<div class="container mx-auto text-center py-10 px-10 md:px-0">
			<h1 class="font-medium main-title">{{ __('All the IT job offers in one place.') }}</h1>
		</div>
	</div>
	<div class="main flex container mx-auto px-10 md:px-0 gap-x-8">
		<div class="main__content-wrapper">
			<FilterForm />
			<div class="listing"></div>
		</div>
		<div v-if="showMap" class="main__map-wrapper block">
			<Map :markers="markers" />
		</div>
	</div>
	<footer></footer>
	<MapTogglerMobile @toggle-mobile-map="toggleMobileMap"/>
</template>
<script>
import { Link } from '@inertiajs/inertia-vue3';
import Map from '@/Components/Map';
import Navbar from '@/Components/Navbar';
import FilterForm from '@/Components/FilterForm';
import MapTogglerMobile from '@/Components/MapTogglerMobile';
import { ref } from '@vue/reactivity';

export default {
	components : {
		Link,
		Map,
		Navbar,
		FilterForm,
		MapTogglerMobile
	},
	props: {
		offers: Object,
		markers: Object
	},
	setup() {
		const showMap = ref(false)

		const toggleMobileMap = () => { 
			showMap.value = !showMap.value
		}

		if (window.innerWidth >= 1024) showMap.value = true

		return {
			showMap,
			toggleMobileMap,
		}
	},
}
</script>
