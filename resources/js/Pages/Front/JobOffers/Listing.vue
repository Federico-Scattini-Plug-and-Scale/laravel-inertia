<template>
  	<div v-for="(item, index) in offers.data" :key="index">
		  {{ item }}
		  <Link :href="route($page.props.locale + '.joboffers.show', [item.category.slug, item.slug, item.id])">{{ __('Check the offer') }}</Link>
	</div>
</template>
<script>
import { Link } from '@inertiajs/inertia-vue3';

export default {
	components : {
		Link
	},
	props: {
		offers: Object
	},
	setup(props) {
	},
}
</script>
