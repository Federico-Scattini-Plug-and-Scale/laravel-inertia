<template></template>
<script>
import { watch } from '@vue/runtime-core'
import Swal from 'sweetalert2'

export default {
	props: {
		message: Object
	},
	setup(props) {
		function showToast (message) {
			Swal.fire({
				toast: true,
				position: 'top-end',
				showConfirmButton: false,
				timer: 4000,
				timerProgressBar: true,
				didOpen: (toast) => {
					toast.addEventListener('mouseenter', Swal.stopTimer)
					toast.addEventListener('mouseleave', Swal.resumeTimer)
    			},
				icon: message.type,
				title: message.content
			})
		}

		showToast(props.message)

		watch(() => props.message, (newMessage) => {
			showToast(newMessage)
		})
	},
}
</script>