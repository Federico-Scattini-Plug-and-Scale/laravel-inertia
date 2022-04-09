<template>
	<Navbar />
	<div class="main-title-wrapper">
		<div class="container mx-auto text-center py-10 px-10 md:px-0">
			<h1 class="font-medium main-title">{{ __('All the IT job offers in one place.') }}</h1>
		</div>
	</div>
	<div class="main flex justify-center container mx-auto px-10 md:px-0 gap-x-8">
		<div class="main__content-wrapper">
			<FilterForm :technologies="technologies" @toggle-filter-modal="toggleFilterModal"/>
			<div class="listing">
				<JobOffer />
				<JobOffer />
				<JobOffer />
				<JobOffer />
				<JobOffer />
				<JobOffer />
				<JobOffer />
				<JobOffer />
				<JobOffer />
				<JobOffer />
				<JobOffer />
			</div>
		</div>
		<div v-if="showMap" class="main__map-wrapper block">
			<Map :markers="markers" />
		</div>
	</div>
	<Footer />
	<MapTogglerMobile v-show="showMapToggler" @toggle-mobile-map="toggleMobileMap"/>
	<div v-if="openFilterModal" @click="toggleFilterModal()" class="overlay"></div>
	<div v-if="openFilterModal" class="filter-modal">
		<div>
			<div class="flex justify-between filter-modal__header">
				<div>
					<span class="filter-modal__header-title">{{ __('More filters') }}</span>
				</div>
				<div>
					<div @click="toggleFilterModal()">
						<i class="fas fa-times close"></i>
					</div>
				</div>
			</div>
			<div class="filter-modal__body">
				<div class="flex flex-col filter-modal__body-section">
					<span class="title">{{ __('Salary range') }}</span>
					<div>
						<Slider class="slider" :max="20000" :step="500" @change="setSalaryRange($event)" v-model="filterModalForm.salaryRange" />
					</div>
				</div>
				<div class="flex flex-col filter-modal__body-section">
					<span class="title">{{ __('Employement Type') }}</span>
					<div class="flex gap-x-5">
						<button 
							@click.prevent="setEmployementType()" 
							:class="{'active' : filterModalForm.employementType == null}"
						>
							{{ __('All') }}
						</button>
						<button 
							v-for="type in employementTypes" 
							:key="type.id" 
							@click.prevent="setEmployementType(type.id)"
							:class="{'active' : filterModalForm.employementType == type.id}"
						>
							{{ type.name }}
						</button>
					</div>
				</div>
				<div class="flex flex-col filter-modal__body-section">
					<span class="title">{{ __('Seniority') }}</span>
					<div class="flex gap-x-5">
						<button 
							@click.prevent="setSeniority()"
							:class="{'active' : filterModalForm.seniority == null}"
						>
							{{ __('All') }}
						</button>
						<button 
							v-for="exp in seniorities" 
							:key="exp.id" 
							@click.prevent="setSeniority(exp.id)"
							:class="{'active' : filterModalForm.seniority == exp.id}"
						>
							{{ exp.name }}
						</button>
					</div>
				</div>
			</div>
		</div>
		<div class="flex justify-between filter-modal__footer">
			<div>
				<button class="secondary-btn" @click.prevent="resetModalFilters()">{{ __('Clear') }}</button>
			</div>
			<div>
				<button class="primary-btn" @click.prevent="submitFilterForm()">{{ __('Filter') }}</button>
			</div>
		</div>
	</div>
</template>
<script>
import { Link } from '@inertiajs/inertia-vue3';
import Map from '@/Components/Map';
import Navbar from '@/Components/Navbar';
import Footer from '@/Components/Footer';
import FilterForm from '@/Components/FilterForm';
import MapTogglerMobile from '@/Components/MapTogglerMobile';
import JobOffer from '@/Components/JobOffer';
import { ref } from '@vue/reactivity';
import Slider from '@vueform/slider';
import { Inertia } from '@inertiajs/inertia';

export default {
	components : {
		Link,
		Map,
		Navbar,
		FilterForm,
		MapTogglerMobile,
		JobOffer,
		Footer,
		Slider
	},
	props: {
		offers: Object,
		markers: Object,
		technologies: Object,
		employementTypes: Object,
		seniorities: Object,
		filters: Object
	},
	setup(props) {
		const showMap = ref(false)
		const openFilterModal = ref(false)
		const showMapToggler = ref(true)
		const filterModalForm = ref({
			employementType : props.filters.employementType ?? null,
			salaryRange : props.filters.salaryRange ?? [0, 20000],
			seniority: props.filters.seniority ?? null
		})

		const toggleMobileMap = () => { 
			showMap.value = !showMap.value
		}

		const toggleFilterModal = () => { 
			openFilterModal.value = !openFilterModal.value
			showMapToggler.value = !showMapToggler.value
		}

		const setEmployementType = (id = null) => {
			filterModalForm.value.employementType = id
		}

		const setSeniority = (id = null) => {
			filterModalForm.value.seniority = id
		}

		const setSalaryRange = (value) => {
			filterModalForm.value.salaryRange = value
		}

		const submitFilterForm = () => {
			Inertia.get(window.location.href.split('?')[0], filterModalForm.value)
		}

		const resetModalFilters = () => {
			filterModalForm.value = {
				employementType : null,
				salaryRange : [0, 20000],
				seniority: null
			}
			submitFilterForm()
		}

		if (window.innerWidth >= 1024) showMap.value = true

		return {
			showMap,
			toggleMobileMap,
			openFilterModal,
			toggleFilterModal,
			showMapToggler,
			filterModalForm,
			setEmployementType,
			setSeniority,
			resetModalFilters,
			submitFilterForm,
			setSalaryRange
		}
	},
}
</script>
<style src="@vueform/slider/themes/default.css"></style>
