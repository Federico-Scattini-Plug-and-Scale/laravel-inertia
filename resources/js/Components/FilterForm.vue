<template>
	<div class="filter-form-wrapper">
		<form class="filter-form">
			<div class="flex bg-white pl-5 filter-form__inputs-wrapper">
				<div class="flex gap-x-3 items-center input py-2">
					<i class="fas fa-briefcase icon"></i>
					<input type="text" :placeholder="__('Category')">
				</div>
				<div class="flex gap-x-3 items-center ml-4 input py-2">
					<i class="fas fa-map-pin icon"></i>
					<Multiselect 
                        :options="options"
                        label="label"
                        trackBy="label"
                        :placeholder="__('Location')"
                        :searchable="true"
                    />
				</div>
				<button class="px-5 py-3 search-btn">{{ __('Search') }}</button>
			</div>
			<div>

			</div>
			<div>

			</div>
		</form>
	</div>
</template>
<script>
import { Link } from '@inertiajs/inertia-vue3';
import { ref } from '@vue/reactivity'
import { usePage } from '@inertiajs/inertia-vue3'
import Multiselect from '@vueform/multiselect'

export default {
	components : {
		Link,
		Multiselect,
	},
	props: {
	},
	setup() {
		const options = [
			{value: "remote", label: "Remote"},
			{value: "mi", label: "Milano"},
			{value: "ro", label: "Roma"},
			{value: "na", label: "Napoli"},
			{value: "to", label: "Torino"}
		]

        return {
			options
        }
	},
}
</script>
<style src="@vueform/multiselect/themes/default.css"></style>