<template>
    <div>
        <div 
			:class="{ 'bg-red-500' : isError, 'bg-green-500' : isSuccess, 'bg-yellow-500' : isInfo }" class="text-white d-block p-2 sm:rounded-lg text-center">
			{{ message }}
		</div>
    </div>
</template>

<script>
import {  toRef } from 'vue'
export default {
    props: {
		message: String,
		type: String
	},
	setup(props) {
		let message = toRef(props, 'message')
		let type = toRef(props, 'type')

		let isError = type.value == 'error'
		let isSuccess = type.value == 'success'
		let isInfo = type.value == 'info'
		
		return { message, type, isError, isSuccess, isInfo }
	}
}
</script>
