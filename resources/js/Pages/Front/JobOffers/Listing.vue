<template>
	<Navbar />
	<div class="container mx-auto text-center py-10 px-10 md:px-0 main-title-wrapper">
		<h1 class="font-medium main-title">{{ __('All the IT job offers in one place.') }}</h1>
	</div>
	<div class="main flex container mx-auto px-10 md:px-0 gap-x-8">
		<div class="main__content-wrapper">
			<FilterForm />
			<div class="listing"></div>
		</div>
		<div class="main__map-wrapper hidden lg:block">
			<Map :markers="markers" />
		</div>
	</div>
	<footer></footer>
</template>
<script>
import { Link } from '@inertiajs/inertia-vue3';
import Map from '@/Components/Map';
import Navbar from '@/Components/Navbar';
import FilterForm from '@/Components/FilterForm';

export default {
	components : {
		Link,
		Map,
		Navbar,
		FilterForm
	},
	props: {
		offers: Object,
		markers: Object
	},
	setup(props) {
	},
}
</script>
