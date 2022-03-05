<template>
	<nav>
		<div class="flex justify-between items-center px-16 navbar container mx-auto">
			<div class="navbar__logo">
				<Link>
					<span>{{  __('Hire ++') }}</span>
				</Link>
			</div>
			<div class="navbar__links lg:flex gap-x-6 items-center hidden">
				<Link>{{ __('Lorem ipsum') }}</Link>
				<Link>{{ __('Lorem ipsum') }}</Link>
				<Link>{{ __('Lorem ipsum') }}</Link>
			</div>
			<div class="navbar__buttons flex items-center gap-x-6">
				<Link>{{ __("Posta un'offerta") }}</Link>
				<CustomDropdown align="right" class="cta-dropdown">
					<template #trigger>
						<span class="inline-flex rounded-md">
							<button type="button" class="inline-flex items-center leading-4 font-medium rounded-md focus:outline-none transition ease-in-out duration-150">
								{{ __('Registrati') }}
								<svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
									<path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
								</svg>
							</button>
						</span>
					</template>
					<template #content>
						<div class="flex flex-col">
							<label>{{ __('Language') }}</label>
						</div>
						<div class="flex justify-between">
							<button>{{ __('Save') }}</button>
						</div>
					</template>
				</CustomDropdown>
				<CustomDropdown align="right" class="lg:block hidden">
					<template #trigger>
						<span class="inline-flex rounded-md">
							<button type="button" class="inline-flex items-center leading-4 focus:outline-none transition ease-in-out duration-150">
								{{ $page.props.locale }}, {{ $page.props.countryName }}
								<svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
									<path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
								</svg>
							</button>
						</span>
					</template>
					<template #content>
						<div class="flex flex-col">
							<label>{{ __('Language') }}</label>
							<select v-model="switcher.lang">
								<option v-for="(locale, index) in $page.props.locales" :key="index" :value="locale">{{ locale }}</option>
							</select>
						</div>
						<div class="flex flex-col">
							<label>{{ __('Country') }}</label>
							<select v-model="switcher.country">
								<option v-for="(country, index) in $page.props.countries" :key="index" :value="country.label">{{ __(country.value) }}</option>
							</select>
						</div>
						<div class="flex justify-between">
							<button @click.prevent="switchLoc()">{{ __('Save') }}</button>
						</div>
					</template>
				</CustomDropdown>
				<!-- Mobile menu button -->
				<div @click="toggleNav" class="flex lg:hidden">
					<button
						type="button"
						class="
						hover:text-gray-400
						focus:outline-none focus:text-gray-400
						"
					>
						<i class="fa-solid fa-bars"></i>
					</button>
				</div>
			</div>
		</div>
		<!-- Mobile Menu open: "block", Menu closed: "hidden" -->
		<div class="overlay" :class="showMenu ? 'block' : 'hidden'"></div>
		<div
			:class="showMenu ? 'flex' : 'hidden'"
			class="
			flex-col
			mt-8
			space-y-4
			space-y-0 flex-row space-x-10 mt-0
			mobile-navbar
			"
		>
			<div class="mobile-navbar__header flex justify-between">
				<span>{{ __('Menu') }}</span>
				<i class="fa-solid fa-xmark"></i>
			</div>
			<div class="navbar__links">
				<Link>{{ __('Lorem ipsum') }}</Link>
				<Link>{{ __('Lorem ipsum') }}</Link>
				<Link>{{ __('Lorem ipsum') }}</Link>
			</div>
		</div>
	</nav>
</template>
<script>
import { Link } from '@inertiajs/inertia-vue3';
import CustomDropdown from '@/Components/CustomDropdown.vue'
import { ref } from '@vue/reactivity'
import { usePage } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'

export default {
	components : {
		Link,
		CustomDropdown
	},
	props: {
		offers: Object,
		markers: Object
	},
	setup() {
		const switcher = ref({
            lang : usePage().props.value.locale,
            country: usePage().props.value.country
        })

        function switchLoc () {
            Inertia.reload({data: {changeLocale: true, locale: switcher.value.lang, country: switcher.value.country}})
        }

		let showMenu = ref(false);
    	
		const toggleNav = () => (showMenu.value = !showMenu.value);

        return {
            switcher,
            switchLoc,
			toggleNav,
			showMenu
        }
	},
}
</script>