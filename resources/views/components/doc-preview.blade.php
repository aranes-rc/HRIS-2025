<!-- Image Preview Modal -->
<div x-show="previewImage" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
    @click.self="previewImage = null">
    <div class="relative bg-white rounded-lg shadow-xl max-w-4xl max-h-[90vh] p-4">
        <button @click="previewImage = null"
            class="absolute -top-3 -right-3 bg-red-500 text-white rounded-full p-1 hover:bg-red-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        <template x-if="!isPdf">
            <img :src="previewImage" class="max-w-full max-h-[80vh] rounded-lg" alt="Document preview">
        </template>
        <template x-if="isPdf">
            <embed :src="previewImage" type="application/pdf" class="w-full h-[80vh] rounded-lg" frameborder="0">
        </template>
    </div>
</div>