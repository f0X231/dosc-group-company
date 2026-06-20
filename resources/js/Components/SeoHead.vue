<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const seo  = computed(() => page.props.seo  ?? {});
const site = computed(() => page.props.site ?? {});

const siteName = computed(() => site.value.site_name ?? 'DOSC Group');

const title = computed(() => {
    if (seo.value.meta_title) return `${seo.value.meta_title} | ${siteName.value}`;
    return siteName.value;
});

const description  = computed(() => seo.value.meta_description ?? '');
const ogTitle      = computed(() => seo.value.og_title      || seo.value.meta_title    || siteName.value);
const ogDesc       = computed(() => seo.value.og_description || seo.value.meta_description || '');
const ogImage      = computed(() => seo.value.og_image_url  ?? '');
const robots       = computed(() => seo.value.robots        ?? 'index,follow');
const schemaJson   = computed(() => seo.value.schema_json   ?? null);

const canonical = computed(() => {
    if (seo.value.canonical_url) return seo.value.canonical_url;
    if (typeof window !== 'undefined') return window.location.origin + page.url;
    return '';
});
</script>

<template>
    <Head>
        <title>{{ title }}</title>
        <meta head-key="description"        name="description"        :content="description" />
        <meta head-key="robots"             name="robots"             :content="robots" />
        <link head-key="canonical"          rel="canonical"           :href="canonical" />

        <!-- Open Graph -->
        <meta head-key="og:type"            property="og:type"        content="website" />
        <meta head-key="og:site_name"       property="og:site_name"   :content="siteName" />
        <meta head-key="og:title"           property="og:title"       :content="ogTitle" />
        <meta head-key="og:description"     property="og:description" :content="ogDesc" />
        <meta head-key="og:url"             property="og:url"         :content="canonical" />
        <meta head-key="og:locale"          property="og:locale"      content="th_TH" />
        <meta v-if="ogImage" head-key="og:image" property="og:image"  :content="ogImage" />

        <!-- Twitter Card -->
        <meta head-key="twitter:card"        name="twitter:card"        content="summary_large_image" />
        <meta head-key="twitter:title"       name="twitter:title"       :content="ogTitle" />
        <meta head-key="twitter:description" name="twitter:description" :content="ogDesc" />
        <meta v-if="ogImage" head-key="twitter:image" name="twitter:image" :content="ogImage" />

        <!-- JSON-LD Structured Data -->
        <component v-if="schemaJson" :is="'script'" type="application/ld+json">{{ schemaJson }}</component>
    </Head>
</template>
