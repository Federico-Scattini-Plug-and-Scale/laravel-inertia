<template>
	<Link>
		<div class="job-offer">
			<div class="job-offer__left">
				<div>
					<div class="company-logo-wrapper">
						<img src="https://media-exp1.licdn.com/dms/image/C4E0BAQGNS7ygQbnBAA/company-logo_200_200/0/1644486427768?e=1655942400&v=beta&t=FBX8YozACx4WMePRIQRg-aHc_kEIOzzBlbGNapRUmgg"/>
					</div>
				</div>
				<div class="job-offer__info">
					<span class="job-offer__info-title">{{ __('Junior PHP developer') }}</span>
					<div class="job-offer__info-box">
						<div class="job-offer__info-company">
							<i class="far fa-building"></i>
							<span>{{ __('JustJoin') }}</span>
						</div>
						<div class="job-offer__info-location">
							<i class="fas fa-map-pin"></i>
							<span>{{ __('Milano MI, via Roma 20') }}</span>
						</div>
					</div>
					<span class="mobile job-offer__info-salary">{{ __('10k - 12k EUR') }}</span>
					<div class="job-offer__tags">
						<span class="job-offer__tags-tag">{{ __('Back-end') }}</span>
						<span class="job-offer__tags-tag">{{ __('Full-time') }}</span>
						<span class="job-offer__tags-tag">{{ __('Junior') }}</span>
					</div>
				</div>
			</div>
			<div class="job-offer__right">
				<div class="job-offer__right-top">
					<span class="job-offer__right-salary">{{ __('10k - 12k EUR') }}</span>
					<i class="far fa-bookmark"></i>
				</div>
				<div class="job-offer__right-bottom">
					<div class="job-offer__right-tech">
						<div class="tech-box-main">
							<div class="tech-box-wrapper">
								<div class="tech-box">
									<Link class="tech-box__link">
										<div class="tech-box__icon-wrapper">
											<i class="fab fa-php"></i>
										</div>
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
									</Link>
								</div>
							</div>
						</div>
					</div>
					<span class="job-offer__right-tag">{{ __('New') }}</span>
					<span class="job-offer__right-time">{{ __('4d') }}</span>
				</div>
			</div>
		</div>
	</Link>
</template>
<script>
import { Link } from '@inertiajs/inertia-vue3';
import { ref } from '@vue/reactivity'
import { usePage } from '@inertiajs/inertia-vue3'

export default {
	components : {
		Link,
	},
	props: {
	},
	setup() {

        return {}
	},
}
</script>
