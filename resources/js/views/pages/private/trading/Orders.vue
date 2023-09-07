<template>
    <Page>
        <div class="w-1/2 m-auto text-left pt-10">
            <h1 class="text-6xl mb-4 font-bold text-gray-600">{{ trans('global.pages.orders') }}</h1>
        </div>
        <footer class="py-5 text-left">
            <h4 class="text-gray-500 text-2xl">{{ new Date() }}</h4>
            <p class="text-gray-400">Order history page...</p>
        </footer>
    </Page>
</template>

<script>
import {defineComponent} from 'vue';
import {trans} from "@/helpers/i18n";
import Page from "@/views/layouts/Page";

export default defineComponent({
    components: {
        Page
    },
    setup() {
        return {
            trans
        }
    }
});
</script>
