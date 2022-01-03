<template>
  	<div v-if="editor" class="flex space-x-2 space-y-2 flex-wrap mb-3">
        <button class="sm:rounded-lg border-black border px-1" @click.stop.prevent="editor.chain().focus().toggleBold().run()" :class="{ 'is-active': editor.isActive('bold') }">
            {{ __('bold') }}
        </button>
        <button class="sm:rounded-lg border-black border px-1" @click.stop.prevent="editor.chain().focus().toggleItalic().run()" :class="{ 'is-active': editor.isActive('italic') }">
            {{ __('italic') }}
        </button>
        <button class="sm:rounded-lg border-black border px-1" @click.stop.prevent="editor.chain().focus().toggleStrike().run()" :class="{ 'is-active': editor.isActive('strike') }">
            {{ __('strike') }}
        </button>
        <button class="sm:rounded-lg border-black border px-1" @click.stop.prevent="editor.chain().focus().unsetAllMarks().run()">
            {{ __('clear marks') }}
        </button>
        <button class="sm:rounded-lg border-black border px-1" @click.stop.prevent="editor.chain().focus().clearNodes().run()">
            {{ __('clear nodes') }}
        </button>
        <button class="sm:rounded-lg border-black border px-1" @click.stop.prevent="editor.chain().focus().setParagraph().run()" :class="{ 'is-active': editor.isActive('paragraph') }">
            {{ __('paragraph') }}
        </button>
        <button class="sm:rounded-lg border-black border px-1" @click.stop.prevent="editor.chain().focus().toggleHeading({ level: 1 }).run()" :class="{ 'is-active': editor.isActive('heading', { level: 1 }) }">
            {{ __('h1') }}
        </button>
        <button class="sm:rounded-lg border-black border px-1" @click.stop.prevent="editor.chain().focus().toggleHeading({ level: 2 }).run()" :class="{ 'is-active': editor.isActive('heading', { level: 2 }) }">
            {{ __('h2') }}
        </button>
        <button class="sm:rounded-lg border-black border px-1" @click.stop.prevent="editor.chain().focus().toggleHeading({ level: 3 }).run()" :class="{ 'is-active': editor.isActive('heading', { level: 3 }) }">
            {{ __('h3') }}  
        </button>
        <button class="sm:rounded-lg border-black border px-1" @click.stop.prevent="editor.chain().focus().toggleHeading({ level: 4 }).run()" :class="{ 'is-active': editor.isActive('heading', { level: 4 }) }">
            {{ __('h4') }}
        </button>
        <button class="sm:rounded-lg border-black border px-1" @click.stop.prevent="editor.chain().focus().toggleHeading({ level: 5 }).run()" :class="{ 'is-active': editor.isActive('heading', { level: 5 }) }">
            {{ __('h5') }}
        </button>
        <button class="sm:rounded-lg border-black border px-1" @click.stop.prevent="editor.chain().focus().toggleHeading({ level: 6 }).run()" :class="{ 'is-active': editor.isActive('heading', { level: 6 }) }">
            {{ __('h6') }}
        </button>
        <button class="sm:rounded-lg border-black border px-1" @click.stop.prevent="editor.chain().focus().toggleBulletList().run()" :class="{ 'is-active': editor.isActive('bulletList') }">
            {{ __('bullet list') }}
        </button>
        <button class="sm:rounded-lg border-black border px-1" @click.stop.prevent="editor.chain().focus().toggleOrderedList().run()" :class="{ 'is-active': editor.isActive('orderedList') }">
            {{ __('ordered list') }}
        </button>
        <button class="sm:rounded-lg border-black border px-1" @click.stop.prevent="editor.chain().focus().setHardBreak().run()">
            {{ __('hard break') }}
        </button>
        <button class="sm:rounded-lg border-black border px-1" @click.stop.prevent="editor.chain().focus().undo().run()">
            {{ __('undo') }}
        </button>
        <button class="sm:rounded-lg border-black border px-1" @click.stop.prevent="editor.chain().focus().redo().run()">
            {{ __('redo') }}
        </button>
    </div>
    <EditorContent :editor="editor" class="border border-black sm:rounded-lg custom-editor"/>
</template>

<script>
import { Editor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'

export default {
	components: {
		EditorContent,
	},

	props: {
		modelValue: {
			type: String,
			default: '',
		},
	},

	data() {
		return {
			editor: null,
		}
	},

	watch: {
		modelValue(value) {
			const isSame = this.editor.getHTML() === value

			if (isSame) {
				return
			}

			this.editor.commands.setContent(value, false)
		},
	},

	mounted() {
		this.editor = new Editor({
		extensions: [
			StarterKit,
		],
		content: this.modelValue,
		onUpdate: () => {
			this.$emit('update:modelValue', this.editor.getHTML())
		},
		})
	},

	beforeUnmount() {
		this.editor.destroy()
	},
}	
</script>