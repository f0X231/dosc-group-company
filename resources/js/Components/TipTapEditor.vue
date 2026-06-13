<script setup>
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Image from '@tiptap/extension-image';
import Link from '@tiptap/extension-link';
import Placeholder from '@tiptap/extension-placeholder';
import TextAlign from '@tiptap/extension-text-align';
import Underline from '@tiptap/extension-underline';
import { ref } from 'vue';

const props = defineProps({
    modelValue: { type: String, default: '' },
    blogUuid:   { type: String, required: true },
    uploadUrl:  { type: String, required: true },
    placeholder: { type: String, default: 'เริ่มเขียนบทความที่นี่...' },
});

const emit = defineEmits(['update:modelValue']);

const imageInput = ref(null);
const uploading  = ref(false);

const editor = useEditor({
    content: props.modelValue,
    extensions: [
        StarterKit.configure({ heading: { levels: [2, 3, 4] } }),
        Underline,
        TextAlign.configure({ types: ['heading', 'paragraph'] }),
        Link.configure({ openOnClick: false, HTMLAttributes: { class: 'text-blue-600 underline hover:text-blue-800' } }),
        Image.configure({ HTMLAttributes: { class: 'rounded-xl max-w-full my-4' } }),
        Placeholder.configure({ placeholder: props.placeholder }),
    ],
    editorProps: {
        attributes: { class: 'outline-none min-h-[400px] focus:outline-none' },
    },
    onUpdate({ editor }) {
        emit('update:modelValue', editor.getHTML());
    },
});

// ─── Toolbar actions ──────────────────────────────────────────────────────────
function setLink() {
    const prev = editor.value.getAttributes('link').href ?? '';
    const url  = window.prompt('URL:', prev);
    if (url === null) return;
    if (url === '') {
        editor.value.chain().focus().extendMarkRange('link').unsetLink().run();
    } else {
        editor.value.chain().focus().extendMarkRange('link').setLink({ href: url }).run();
    }
}

function insertImageFromUrl() {
    const url = window.prompt('URL รูปภาพ:');
    if (url) editor.value.chain().focus().setImage({ src: url }).run();
}

async function uploadAndInsertImage(e) {
    const file = e.target.files?.[0];
    if (!file) return;
    e.target.value = '';

    uploading.value = true;
    try {
        const fd = new FormData();
        fd.append('file', file);
        fd.append('blog_uuid', props.blogUuid);

        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const res = await fetch(props.uploadUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfMeta?.content ?? '', 'Accept': 'application/json' },
            body: fd,
        });
        const json = await res.json();
        if (json.url) {
            editor.value.chain().focus()
                .setImage({ src: json.url, 'data-media-id': json.media_id })
                .run();
        }
    } catch (err) {
        alert('อัปโหลดรูปไม่สำเร็จ');
    } finally {
        uploading.value = false;
    }
}

const btn = (active, disabled = false) => [
    'p-1.5 rounded text-sm transition',
    active   ? 'bg-gray-800 text-white'       : 'text-gray-600 hover:bg-gray-100',
    disabled ? 'opacity-30 cursor-not-allowed' : '',
].join(' ');
</script>

<template>
    <div class="border border-gray-300 rounded-xl overflow-hidden bg-white">

        <!-- Toolbar -->
        <div class="flex flex-wrap items-center gap-0.5 px-3 py-2 border-b border-gray-200 bg-gray-50 sticky top-0 z-10">

            <!-- History -->
            <button type="button" :class="btn(false, !editor?.can().undo())"
                    @click="editor?.chain().focus().undo().run()" title="Undo">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3"/></svg>
            </button>
            <button type="button" :class="btn(false, !editor?.can().redo())"
                    @click="editor?.chain().focus().redo().run()" title="Redo">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m15 15 6-6m0 0-6-6m6 6H9a6 6 0 0 0 0 12h3"/></svg>
            </button>

            <div class="w-px h-5 bg-gray-300 mx-1"></div>

            <!-- Headings -->
            <button type="button" v-for="level in [2,3,4]" :key="level"
                    :class="btn(editor?.isActive('heading', { level }))"
                    @click="editor?.chain().focus().toggleHeading({ level }).run()"
                    :title="`Heading ${level}`">
                <span class="text-xs font-bold">H{{ level }}</span>
            </button>

            <div class="w-px h-5 bg-gray-300 mx-1"></div>

            <!-- Inline marks -->
            <button type="button" :class="btn(editor?.isActive('bold'))"
                    @click="editor?.chain().focus().toggleBold().run()" title="Bold">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6 4h8a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"/><path d="M6 12h9a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"/></svg>
            </button>
            <button type="button" :class="btn(editor?.isActive('italic'))"
                    @click="editor?.chain().focus().toggleItalic().run()" title="Italic">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="19" y1="4" x2="10" y2="4"/><line x1="14" y1="20" x2="5" y2="20"/><line x1="15" y1="4" x2="9" y2="20"/></svg>
            </button>
            <button type="button" :class="btn(editor?.isActive('underline'))"
                    @click="editor?.chain().focus().toggleUnderline().run()" title="Underline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 4v6a6 6 0 0 0 12 0V4"/><line x1="4" y1="20" x2="20" y2="20" stroke-linecap="round"/></svg>
            </button>
            <button type="button" :class="btn(editor?.isActive('strike'))"
                    @click="editor?.chain().focus().toggleStrike().run()" title="Strikethrough">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M16 4H9a3 3 0 0 0-2.83 4M4 12h16m-7 4v4M12 4v4"/></svg>
            </button>

            <div class="w-px h-5 bg-gray-300 mx-1"></div>

            <!-- Lists -->
            <button type="button" :class="btn(editor?.isActive('bulletList'))"
                    @click="editor?.chain().focus().toggleBulletList().run()" title="Bullet List">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><circle cx="3" cy="6" r="1" fill="currentColor"/><circle cx="3" cy="12" r="1" fill="currentColor"/><circle cx="3" cy="18" r="1" fill="currentColor"/></svg>
            </button>
            <button type="button" :class="btn(editor?.isActive('orderedList'))"
                    @click="editor?.chain().focus().toggleOrderedList().run()" title="Numbered List">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="10" y1="6" x2="21" y2="6"/><line x1="10" y1="12" x2="21" y2="12"/><line x1="10" y1="18" x2="21" y2="18"/><path stroke-linecap="round" d="M4 6h1v4"/><path stroke-linecap="round" d="M4 10H5"/><path stroke-linecap="round" d="M3 14a1 1 0 0 1 1-1h1a1 1 0 0 1 0 2H4a1 1 0 0 0-1 1v1h3"/></svg>
            </button>

            <div class="w-px h-5 bg-gray-300 mx-1"></div>

            <!-- Alignment -->
            <button v-for="align in ['left','center','right']" :key="align"
                    type="button" :class="btn(editor?.isActive({ textAlign: align }))"
                    @click="editor?.chain().focus().setTextAlign(align).run()" :title="`Align ${align}`">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <template v-if="align === 'left'"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="15" y2="12"/><line x1="3" y1="18" x2="18" y2="18"/></template>
                    <template v-else-if="align === 'center'"><line x1="3" y1="6" x2="21" y2="6"/><line x1="6" y1="12" x2="18" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/></template>
                    <template v-else><line x1="3" y1="6" x2="21" y2="6"/><line x1="9" y1="12" x2="21" y2="12"/><line x1="6" y1="18" x2="21" y2="18"/></template>
                </svg>
            </button>

            <div class="w-px h-5 bg-gray-300 mx-1"></div>

            <!-- Blockquote + HR -->
            <button type="button" :class="btn(editor?.isActive('blockquote'))"
                    @click="editor?.chain().focus().toggleBlockquote().run()" title="Blockquote">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"/><path d="M15 21c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2h.75c0 2.25.25 4-2.75 4v3c0 1 0 1 1 1z"/></svg>
            </button>
            <button type="button" :class="btn(editor?.isActive('codeBlock'))"
                    @click="editor?.chain().focus().toggleCodeBlock().run()" title="Code Block">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
            </button>
            <button type="button" :class="btn(false)"
                    @click="editor?.chain().focus().setHorizontalRule().run()" title="Divider">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="3" y1="12" x2="21" y2="12"/></svg>
            </button>

            <div class="w-px h-5 bg-gray-300 mx-1"></div>

            <!-- Link -->
            <button type="button" :class="btn(editor?.isActive('link'))"
                    @click="setLink" title="Link">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M13.828 10.172a4 4 0 0 0-5.656 0l-4 4a4 4 0 1 0 5.656 5.656l1.102-1.101"/><path stroke-linecap="round" d="M14.828 14.828a4 4 0 0 0 5.656 0l4-4a4 4 0 1 0-5.656-5.656l-1.1 1.1"/></svg>
            </button>

            <!-- Image: upload -->
            <button type="button" :class="btn(false)" :disabled="uploading"
                    @click="imageInput.click()" title="อัปโหลดรูปภาพ">
                <svg class="w-4 h-4" :class="uploading ? 'animate-spin' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18"/>
                </svg>
            </button>
            <input ref="imageInput" type="file" accept="image/*" class="hidden" @change="uploadAndInsertImage"/>
        </div>

        <!-- Editor area -->
        <div class="px-6 py-5">
            <EditorContent :editor="editor"
                           class="prose prose-gray max-w-none prose-headings:font-bold prose-a:text-blue-600 prose-img:rounded-xl"/>
        </div>
    </div>
</template>

<style>
.tiptap p.is-editor-empty:first-child::before {
    content: attr(data-placeholder);
    float: left;
    color: #9ca3af;
    pointer-events: none;
    height: 0;
}
</style>
