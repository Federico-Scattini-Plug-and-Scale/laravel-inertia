<template>
	<div class="filter-form-wrapper">
		<form class="filter-form">
			<div class="flex bg-white pl-5 filter-form__inputs-wrapper">
				<div class="flex gap-x-3 items-center input py-2">
					<i class="fas fa-briefcase icon"></i>
					<Multiselect 
                        :options="options"
                        label="label"
                        trackBy="label"
                        :placeholder="__('Category')"
                        :searchable="true"
                    />				
				</div>
				<div class="flex gap-x-3 items-center md:ml-4 input py-2">
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
			<div class="flex mt-5 items-center justify-between tech-section">
				<div class="flex items-center gap-x-1">
					<div class="tech-box-main">
						<div class="tech-box-wrapper">
							<div class="tech-box">
								<Link class="tech-box__link">
									<div class="tech-box__icon-wrapper">
										<i class="fab fa-php"></i>
									</div>
									<!-- <span class="texh-box__label">PHP</span> -->
								</Link>
							</div>
						</div>
					</div>
					<div class="tech-box-main">
						<div class="tech-box-wrapper">
							<div class="tech-box">
								<Link class="tech-box__link">
									<div class="tech-box__icon-wrapper">
										<i class="fab fa-php"></i>
									</div>
									<!-- <span class="texh-box__label">PHP</span> -->
								</Link>
							</div>
						</div>
					</div>
					<div class="tech-box-main">
						<div class="tech-box-wrapper">
							<div class="tech-box">
								<Link class="tech-box__link">
									<div class="tech-box__icon-wrapper">
										<i class="fab fa-php"></i>
									</div>
									<!-- <span class="texh-box__label">PHP</span> -->
								</Link>
							</div>
						</div>
					</div>
					<div class="tech-box-main">
						<div class="tech-box-wrapper">
							<div class="tech-box">
								<Link class="tech-box__link">
									<div class="tech-box__icon-wrapper">
										<i class="fab fa-php"></i>
									</div>
									<!-- <span class="texh-box__label">PHP</span> -->
								</Link>
							</div>
						</div>
					</div>
					<div class="tech-box-main">
						<div class="tech-box-wrapper">
							<div class="tech-box">
								<Link class="tech-box__link">
									<div class="tech-box__icon-wrapper">
										<i class="fab fa-php"></i>
									</div>
									<!-- <span class="texh-box__label">PHP</span> -->
								</Link>
							</div>
						</div>
					</div>
					<div class="tech-box-main">
						<div class="tech-box-wrapper">
							<div class="tech-box">
								<Link class="tech-box__link">
									<div class="tech-box__icon-wrapper">
										<i class="fab fa-php"></i>
									</div>
									<!-- <span class="texh-box__label">PHP</span> -->
								</Link>
							</div>
						</div>
					</div>
					<div class="tech-box-main">
						<div class="tech-box-wrapper">
							<div class="tech-box">
								<Link class="tech-box__link">
									<div class="tech-box__icon-wrapper">
										<i class="fab fa-php"></i>
									</div>
									<!-- <span class="texh-box__label">PHP</span> -->
								</Link>
							</div>
						</div>
					</div>
					<div class="tech-box-main">
						<div class="tech-box-wrapper">
							<div class="tech-box">
								<Link class="tech-box__link">
									<div class="tech-box__icon-wrapper">
										<i class="fab fa-php"></i>
									</div>
									<!-- <span class="texh-box__label">PHP</span> -->
								</Link>
							</div>
						</div>
					</div>
					<div class="tech-box-main">
						<div class="tech-box-wrapper">
							<div class="tech-box">
								<Link class="tech-box__link">
									<div class="tech-box__icon-wrapper">
										<i class="fab fa-php"></i>
									</div>
									<!-- <span class="texh-box__label">PHP</span> -->
								</Link>
							</div>
						</div>
					</div>
					<div class="tech-box-main">
						<div class="tech-box-wrapper">
							<div class="tech-box">
								<Link class="tech-box__link">
									<div class="tech-box__icon-wrapper">
										<i class="fab fa-php"></i>
									</div>
									<!-- <span class="texh-box__label">PHP</span> -->
								</Link>
							</div>
						</div>
					</div>
					<div class="tech-box-main">
						<div class="tech-box-wrapper">
							<div class="tech-box">
								<Link class="tech-box__link">
									<div class="tech-box__icon-wrapper">
										<i class="fab fa-php"></i>
									</div>
									<!-- <span class="texh-box__label">PHP</span> -->
								</Link>
							</div>
						</div>
					</div>
				</div>
				<div>
					<button class="more-filters-btn" @click.prevent="$emit('toggle-filter-modal')">{{ __('More filters') }}</button>
				</div>
			</div>
			<div class="sub-filter-section">
				<div class="flex items-center">
					<span class="mr-2">{{ __('Order by:') }}</span>
					<select class="order-select">
						<option value="">{{ __('Date') }}</option>
						<option value="">{{ __('Date') }}</option>
						<option value="">{{ __('Date') }}</option>
					</select>
				</div>
			</div>
			<div class="mobile-section">
				mobile
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
			options,
        }
	},
}
</script>
<style src="@vueform/multiselect/themes/default.css"></style>