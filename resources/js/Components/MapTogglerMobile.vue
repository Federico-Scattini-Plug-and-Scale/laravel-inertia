<template>
  	<div class="mobile-map-toggler block lg:hidden" @click="$emit('toggle-mobile-map'); toggleShowMap()">
		<span v-if="showMap">
			<i class="fas fa-map"></i>
		</span>
		<span v-else>
			<i class="fas fa-times"></i>
		</span>
		{{ __('Map') }}
	</div>
</template>
<script>
import { ref } from '@vue/reactivity';

export default {
	setup() {
		const showMap = ref(true)

		const toggleShowMap = () => { showMap.value = !showMap.value }

		return {
			showMap,
			toggleShowMap
		}
	},
}
</script>