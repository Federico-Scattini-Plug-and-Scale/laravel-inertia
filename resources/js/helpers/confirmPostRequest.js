import { Inertia } from "@inertiajs/inertia"
import { usePage } from "@inertiajs/inertia-vue3"
import Swal from "sweetalert2"
import __ from "./translation"

export default function confirmPostRequest(link) {
	Swal.fire({
		title: __(usePage().props.value.lang, 'Are you sure?'),
		icon: 'warning',
		confirmButtonText: __(usePage().props.value.lang, 'Yes'),
		denyButtonText: __(usePage().props.value.lang, 'No'),
		showDenyButton: true
	}).then((result) => {
		if (result.isConfirmed) {
			Inertia.post(link)
		}
	})
}