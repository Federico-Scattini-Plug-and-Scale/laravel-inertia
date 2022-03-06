<template>
	<nav>
		<div class="flex justify-between items-center lg:px-8 px-10 navbar container mx-auto">
			<div class="navbar__logo">
				<Link>
					<span>{{  __('Hire ++') }}</span>
				</Link>
			</div>
			<div class="navbar__links lg:flex gap-x-6 items-center hidden">
				<Link class="link">{{ __('Lorem ipsum') }}</Link>
				<Link class="link">{{ __('Lorem ipsum') }}</Link>
				<Link class="link">{{ __('Lorem ipsum') }}</Link>
			</div>
			<div class="navbar__buttons flex items-center gap-x-6">
				<Link class="link">{{ __('Post an offer') }}</Link>
				<CustomDropdown align="right" class="cta-dropdown">
					<template #trigger>
						<span class="inline-flex rounded-md">
							<button type="button" class="inline-flex items-center leading-4 font-medium rounded-md focus:outline-none transition ease-in-out duration-150 py-1">
								{{ __('Log in') }}
								<svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
									<path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
								</svg>
							</button>
						</span>
					</template>
					<template #content>
						<div class="flex p-5 justify-center btn-wrapper">
							<Link>{{ __('Log in as developer') }}</Link>
						</div>
						<div class="flex p-5 justify-center btn-wrapper mt-3">
							<Link>{{ __('Log in as company') }}</Link>
						</div>
					</template>
				</CustomDropdown>
				<CustomDropdown align="right" class="lg:block hidden lang-dropdown">
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
						<div class="flex flex-col py-3">
							<label>{{ __('Language') }}</label>
							<select v-model="switcher.lang" class="mt-3">
								<option v-for="(locale, index) in $page.props.locales" :key="index" :value="locale">{{ locale }}</option>
							</select>
						</div>
						<div class="flex flex-col pb-3">
							<label>{{ __('Country') }}</label>
							<select v-model="switcher.country" class="mt-3">
								<option v-for="(country, index) in $page.props.countries" :key="index" :value="country.label">{{ __(country.value) }}</option>
							</select>
						</div>
						<div class="flex justify-between pb-3">
							<button @click.prevent="switchLoc()" class="secondary-btn">{{ __('Save') }}</button>
						</div>
					</template>
				</CustomDropdown>
				<!-- Mobile menu button -->
				<div @click="toggleNav" class="flex lg:hidden">
					<button type="button" class="hover:text-gray-400 focus:outline-none focus:text-gray-400">
						<i class="fas fa-bars"></i>
					</button>
				</div>
			</div>
		</div>
		<!-- Mobile Menu open: "block", Menu closed: "hidden" -->
		<div class="overlay" :class="showMenu ? 'block' : 'hidden'" @click="toggleNav"></div>
		<div
			:class="showMenu ? 'flex' : 'hidden'"
			class="flex-col mobile-navbar"
		>
			<div class="mobile-navbar__header flex justify-between py-5 px-10">
				<span class="text-xl font-medium">{{ __('Menu') }}</span>
				<button class="focus:outline-none focus:text-gray-400 hover:text-gray-400" @click="toggleNav">
					<i class="fas fa-times"></i>
				</button>			
			</div>
			<div class="mobile-navbar__links px-10 py-6 flex flex-col gap-y-5 items-end">
				<Link>{{ __('Lorem ipsum') }}</Link>
				<Link>{{ __('Lorem ipsum') }}</Link>
				<Link>{{ __('Lorem ipsum') }}</Link>
			</div>
			<div class="mobile-navbar__buttons flex flex-col py-6 px-10 gap-y-5">
				<div class="flex p-5 justify-center text-center btn-wrapper">
					<Link>{{ __('Log in as developer') }}</Link>
				</div>
				<div class="flex p-5 justify-center text-center btn-wrapper">
					<Link>{{ __('Log in as company') }}</Link>
				</div>
			</div>
			<div class="flex flex-col py-6 px-10 gap-y-5">
				<div class="flex flex-col">
					<label>{{ __('Language') }}</label>
					<select v-model="switcher.lang" class="mt-3">
						<option v-for="(locale, index) in $page.props.locales" :key="index" :value="locale">{{ locale }}</option>
					</select>
				</div>
				<div class="flex flex-col">
					<label>{{ __('Country') }}</label>
					<select v-model="switcher.country" class="mt-3">
						<option v-for="(country, index) in $page.props.countries" :key="index" :value="country.label">{{ __(country.value) }}</option>
					</select>
				</div>
				<div class="flex justify-between">
					<button @click.prevent="switchLoc()" class="secondary-btn">{{ __('Save') }}</button>
				</div>
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
			if (showMenu.value) toggleNav()
        }

		let showMenu = ref(false);
    	
		const toggleNav = () => (showMenu.value = !showMenu.value);

        return {
            switcher,
            switchLoc,
			toggleNav,
			showMenu,
        }
	},
}
</script>