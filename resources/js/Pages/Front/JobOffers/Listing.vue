<template>
	<Navbar />
	
	<Map :markers="markers" />
</template>
<script>
import { Link } from '@inertiajs/inertia-vue3';
import Map from '@/Components/Map';
import Navbar from '@/Components/Navbar';

export default {
	components : {
		Link,
		Map,
		Navbar
	},
	props: {
		offers: Object,
		markers: Object
	},
	setup(props) {
	},
}
</script>
