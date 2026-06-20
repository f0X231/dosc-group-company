<script setup>
import { onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const props = defineProps({
    blog:    { type: Object, required: true },
    related: { type: Array,  default: () => [] },
});

function formatDate(d) {
    if (!d) return '';
    return new Date(d).toLocaleDateString('th-TH', { day: 'numeric', month: 'long', year: 'numeric' });
}

// Inject per-article scripts
function injectScript(html, id) {
    if (!html) return;
    const existing = document.getElementById(id);
    if (existing) existing.remove();
    const div = document.createElement('div');
    div.id = id;
    div.innerHTML = html;
    div.querySelectorAll('script').forEach(orig => {
        const s = document.createElement('script');
        [...orig.attributes].forEach(a => s.setAttribute(a.name, a.value));
        s.textContent = orig.textContent;
        div.appendChild(s);
        orig.remove();
    });
    if (id === 'blog-head-script') {
        document.head.appendChild(div);
    } else {
        document.body.appendChild(div);
    }
}

function removeScript(id) {
    document.getElementById(id)?.remove();
}

onMounted(() => {
    injectScript(props.blog.head_script, 'blog-head-script');
    injectScript(props.blog.body_script, 'blog-body-script');
});

onUnmounted(() => {
    removeScript('blog-head-script');
    removeScript('blog-body-script');
});
</script>

<template>
    <PublicLayout>

        <!-- Article header -->
        <section class="bg-gray-950 pt-28 pb-12">
            <div class="max-w-3xl mx-auto px-4 sm:px-6">
                <!-- Breadcrumb -->
                <div class="flex items-center gap-2 text-xs text-gray-500 mb-6">
                    <Link :href="route('blog.index')" class="hover:text-gray-300 transition">บทความ</Link>
                    <span>/</span>
                    <span v-if="blog.category"
                          class="text-white font-medium px-2 py-0.5 rounded-full text-xs"
                          :style="`background: ${blog.category?.color}`">
                        {{ blog.category?.name }}
                    </span>
                </div>

                <h1 class="text-3xl sm:text-4xl font-bold text-white leading-tight font-kanit">
                    {{ blog.title }}
                </h1>
                <p class="text-gray-400 text-base mt-4">{{ blog.excerpt }}</p>

                <div class="flex items-center gap-4 mt-6 text-xs text-gray-500">
                    <span>{{ formatDate(blog.published_at ?? blog.created_at) }}</span>
                    <span>·</span>
                    <span>{{ blog.reading_time }} นาทีในการอ่าน</span>
                    <span>·</span>
                    <span>{{ blog.view_count?.toLocaleString() }} วิว</span>
                </div>
            </div>
        </section>

        <!-- Cover image -->
        <div v-if="blog.cover_image_url" class="max-w-4xl mx-auto px-4 sm:px-6 -mt-1">
            <img :src="blog.cover_image_url" :alt="blog.title"
                 class="w-full aspect-video object-cover rounded-2xl shadow-xl"/>
        </div>

        <!-- Article content -->
        <article class="max-w-3xl mx-auto px-4 sm:px-6 py-14">
            <div class="prose prose-gray prose-lg max-w-none
                        prose-headings:font-bold prose-headings:font-kanit
                        prose-a:text-blue-600 prose-a:no-underline hover:prose-a:underline
                        prose-img:rounded-xl prose-img:shadow-md
                        prose-blockquote:border-blue-500 prose-blockquote:bg-blue-50 prose-blockquote:rounded-r-xl prose-blockquote:py-1
                        prose-code:bg-gray-100 prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded prose-code:text-sm prose-code:before:content-none prose-code:after:content-none"
                 v-html="blog.content"/>

            <!-- Tags -->
            <div v-if="blog.tags?.length" class="mt-10 pt-8 border-t border-gray-200 flex flex-wrap gap-2">
                <span v-for="tag in blog.tags" :key="tag"
                      class="px-3 py-1 bg-gray-100 text-gray-600 text-xs rounded-full font-medium">
                    #{{ tag }}
                </span>
            </div>
        </article>

        <!-- Related articles -->
        <section v-if="related.length" class="bg-gray-50 py-14 border-t border-gray-200">
            <div class="max-w-6xl mx-auto px-4 sm:px-6">
                <h2 class="text-2xl font-bold text-gray-900 font-kanit mb-8">บทความที่เกี่ยวข้อง</h2>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <Link v-for="item in related" :key="item.id"
                          :href="route('blog.show', item.slug)"
                          class="group block bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition">
                        <div class="aspect-video overflow-hidden bg-gray-100">
                            <img v-if="item.thumbnail_url || item.cover_image_url"
                                 :src="item.thumbnail_url ?? item.cover_image_url"
                                 :alt="item.title"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500"/>
                            <div v-else class="w-full h-full flex items-center justify-center text-gray-200">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Z"/></svg>
                            </div>
                        </div>
                        <div class="p-5">
                            <h3 class="font-bold text-gray-900 leading-snug group-hover:text-blue-600 transition line-clamp-2 font-kanit">
                                {{ item.title }}
                            </h3>
                            <p class="text-gray-500 text-sm mt-2 line-clamp-2">{{ item.excerpt }}</p>
                            <p class="text-xs text-gray-400 mt-3">{{ formatDate(item.published_at) }} · {{ item.reading_time }} นาที</p>
                        </div>
                    </Link>
                </div>
            </div>
        </section>

        <!-- Back to blog -->
        <div class="py-10 flex justify-center border-t border-gray-200">
            <Link :href="route('blog.index')"
                  class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                </svg>
                กลับหน้าบทความทั้งหมด
            </Link>
        </div>

    </PublicLayout>
</template>
