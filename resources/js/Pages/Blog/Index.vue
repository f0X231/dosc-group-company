<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const props = defineProps({
    blogs:      { type: Object, default: () => ({}) },
    categories: { type: Array,  default: () => [] },
    filters:    { type: Object, default: () => ({}) },
});

const activeCategory = ref(props.filters.category ?? '');

function filterCategory(slug) {
    activeCategory.value = slug;
    router.get(route('blog.index'), slug ? { category: slug } : {}, { preserveState: true, replace: true });
}

function formatDate(d) {
    if (!d) return '';
    return new Date(d).toLocaleDateString('th-TH', { day: 'numeric', month: 'long', year: 'numeric' });
}
</script>

<template>
    <Head title="บทความ" />
    <PublicLayout>

        <!-- Page header -->
        <section class="bg-gray-950 pt-28 pb-14">
            <div class="max-w-6xl mx-auto px-4 sm:px-6">
                <p class="text-blue-400 text-sm font-semibold tracking-widest uppercase mb-3">Blog</p>
                <h1 class="text-4xl sm:text-5xl font-bold text-white font-kanit">บทความ &amp; ความรู้</h1>
                <p class="text-gray-400 mt-4 max-w-xl">เรื่องราวของการทำธุรกิจออนไลน์ เว็บไซต์ และ Digital Marketing จากทีมงาน DOSC</p>
            </div>
        </section>

        <!-- Category filter bar -->
        <div v-if="categories.length" class="bg-white border-b border-gray-100 sticky top-0 z-20 shadow-sm">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 flex items-center gap-1.5 py-3 overflow-x-auto">
                <button @click="filterCategory('')"
                        class="flex-shrink-0 px-4 py-1.5 rounded-full text-sm font-medium transition"
                        :class="activeCategory === '' ? 'bg-gray-900 text-white' : 'text-gray-500 hover:bg-gray-100'">
                    ทั้งหมด
                </button>
                <button v-for="cat in categories" :key="cat.id"
                        @click="filterCategory(cat.slug)"
                        class="flex-shrink-0 px-4 py-1.5 rounded-full text-sm font-medium transition text-white"
                        :class="activeCategory !== cat.slug ? '!text-gray-500 !bg-gray-100 hover:!bg-gray-200' : ''"
                        :style="activeCategory === cat.slug ? `background: ${cat.color}` : ''">
                    {{ cat.name }}
                </button>
            </div>
        </div>

        <!-- Article grid -->
        <section class="bg-gray-50 min-h-screen py-12">
            <div class="max-w-6xl mx-auto px-4 sm:px-6">

                <!-- Featured card (page 1 first item) -->
                <template v-if="blogs.current_page === 1 && blogs.data?.length">
                    <Link :href="route('blog.show', blogs.data[0].slug)"
                          class="group block rounded-2xl overflow-hidden bg-white shadow-sm hover:shadow-md transition mb-10">
                        <div class="grid md:grid-cols-2">
                            <div class="aspect-video md:aspect-auto md:min-h-[320px] overflow-hidden bg-gray-100">
                                <img v-if="blogs.data[0].thumbnail_url || blogs.data[0].cover_image_url"
                                     :src="blogs.data[0].thumbnail_url ?? blogs.data[0].cover_image_url"
                                     :alt="blogs.data[0].title"
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-500"/>
                                <div v-else class="w-full h-full flex items-center justify-center text-gray-200">
                                    <svg class="w-16 h-16" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Z"/></svg>
                                </div>
                            </div>
                            <div class="p-8 md:p-10 flex flex-col justify-center">
                                <div class="flex items-center gap-3 mb-4">
                                    <span v-if="blogs.data[0].is_featured"
                                          class="text-xs font-bold text-amber-600 bg-amber-50 border border-amber-200 px-2.5 py-0.5 rounded-full">
                                        ★ แนะนำ
                                    </span>
                                    <span v-if="blogs.data[0].category"
                                          class="text-xs font-semibold text-white px-2.5 py-0.5 rounded-full"
                                          :style="`background: ${blogs.data[0].category.color}`">
                                        {{ blogs.data[0].category.name }}
                                    </span>
                                </div>
                                <h2 class="text-2xl font-bold text-gray-900 group-hover:text-blue-600 transition leading-snug font-kanit">
                                    {{ blogs.data[0].title }}
                                </h2>
                                <p class="text-gray-500 text-sm mt-3 line-clamp-3">{{ blogs.data[0].excerpt }}</p>
                                <div class="flex items-center gap-3 mt-6 text-xs text-gray-400">
                                    <span>{{ formatDate(blogs.data[0].published_at ?? blogs.data[0].created_at) }}</span>
                                    <span>·</span>
                                    <span>{{ blogs.data[0].reading_time }} นาที</span>
                                    <span>·</span>
                                    <span>{{ blogs.data[0].view_count.toLocaleString() }} วิว</span>
                                </div>
                            </div>
                        </div>
                    </Link>
                </template>

                <!-- Card grid -->
                <div v-if="(blogs.current_page === 1 ? blogs.data?.slice(1) : blogs.data)?.length"
                     class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <Link v-for="blog in (blogs.current_page === 1 ? blogs.data.slice(1) : blogs.data)"
                          :key="blog.id"
                          :href="route('blog.show', blog.slug)"
                          class="group block bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition">
                        <div class="aspect-video overflow-hidden bg-gray-100">
                            <img v-if="blog.thumbnail_url || blog.cover_image_url"
                                 :src="blog.thumbnail_url ?? blog.cover_image_url"
                                 :alt="blog.title"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500"/>
                            <div v-else class="w-full h-full flex items-center justify-center text-gray-200">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Z"/></svg>
                            </div>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-3">
                                <span v-if="blog.category"
                                      class="text-xs font-semibold text-white px-2 py-0.5 rounded-full"
                                      :style="`background: ${blog.category.color}`">
                                    {{ blog.category.name }}
                                </span>
                                <span v-if="blog.is_featured" class="text-xs text-amber-400" title="แนะนำ">★</span>
                            </div>
                            <h3 class="font-bold text-gray-900 leading-snug group-hover:text-blue-600 transition line-clamp-2 font-kanit">
                                {{ blog.title }}
                            </h3>
                            <p class="text-gray-500 text-sm mt-2 line-clamp-2">{{ blog.excerpt }}</p>
                            <div class="flex items-center gap-2 mt-4 text-xs text-gray-400">
                                <span>{{ formatDate(blog.published_at ?? blog.created_at) }}</span>
                                <span>·</span>
                                <span>{{ blog.reading_time }} นาที</span>
                            </div>
                        </div>
                    </Link>
                </div>

                <div v-if="!blogs.data?.length" class="text-center py-24 text-gray-400">
                    <svg class="mx-auto w-12 h-12 mb-4 text-gray-300" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                    <p class="text-lg font-medium text-gray-500">ยังไม่มีบทความ</p>
                </div>

                <!-- Pagination -->
                <div v-if="blogs.last_page > 1" class="mt-10 flex justify-center gap-1.5">
                    <Link v-for="link in blogs.links" :key="link.label"
                          :href="link.url ?? ''"
                          :class="['px-4 py-2 rounded-xl text-sm font-medium transition',
                              link.active ? 'bg-gray-900 text-white' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200',
                              !link.url ? 'opacity-40 pointer-events-none' : '']"
                          v-html="link.label"/>
                </div>
            </div>
        </section>

    </PublicLayout>
</template>
