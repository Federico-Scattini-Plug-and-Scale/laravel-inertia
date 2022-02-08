<template>
	<Map :markers="markers" />
</template>
<script>
import { Link } from '@inertiajs/inertia-vue3';
import Map from '@/Components/Map';

export default {
	components : {
		Link,
		Map
	},
	props: {
		offers: Object,
		markers: Object
	},
	setup(props) {
	},
}
</script>
