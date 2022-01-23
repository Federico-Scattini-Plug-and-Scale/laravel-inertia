<template>
    <CustomDropdown align="right">
        <template #trigger>
            <span class="inline-flex rounded-md">
                <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                    {{ $page.props.locale }}
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
</template>

<script>
import CustomDropdown from '@/Components/CustomDropdown.vue'
import { ref } from '@vue/reactivity'
import { usePage } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'

export default {
    components: {
		CustomDropdown,
	},
    setup() {
        const switcher = ref({
            lang : usePage().props.value.locale,
            country: usePage().props.value.country
        })

        function switchLoc () {
            Inertia.reload({data: {changeLocale: true, locale: switcher.value.lang, country: switcher.value.country}})
        }

        return {
            switcher,
            switchLoc,
        }
    }
}
</script>