<template>
    <div class="bg-gray-100 flex" v-if="authStore.user && authStore.user.hasOwnProperty('id')">
<!--        <aside class="relative bg-theme-600 h-screen w-64 hidden sm:block shadow-xl">-->
<!--            <div class="p-6 border-b border-theme-600">-->
<!--                <router-link class="text-white text-3xl font-semibold uppercase hover:text-gray-300" to="/panel/dashboard">-->
<!--                    <template v-if="state.app.logo">-->
<!--                        <img :src="state.app.logo" :alt="state.app.name"/>-->
<!--                    </template>-->
<!--                    <template v-else>-->
<!--                        {{ state.app.name }}-->
<!--                    </template>-->
<!--                </router-link>-->

<!--            </div>-->
<!--            <nav class="text-white text-base py-4 px-3 rounded">-->
<!--                <Menu :state="state" :type="'desktop'"/>-->
<!--            </nav>-->
<!--            <template v-if="state.footerLeftLink">-->
<!--                <a v-if="state.footerLeftLink.href" :href="state.footerLeftLink.href" class="absolute w-full bottom-0 bg-theme-800 text-white flex items-center justify-center py-4">-->
<!--                    <Icon :name="state.footerLeftLink.icon" class="mr-3"/>-->
<!--                    {{ state.footerLeftLink.name }}-->
<!--                </a>-->
<!--                <router-link v-else :to="state.footerLeftLink.to">-->
<!--                    <Icon :name="state.footerLeftLink.icon" class="mr-3"/>-->
<!--                    {{ state.footerLeftLink.name }}-->
<!--                </router-link>-->
<!--            </template>-->
<!--        </aside>-->
        <div class="relative w-full flex flex-col h-screen overflow-y-hidden">
            <!-- Desktop Header -->
            <header class="w-full items-center bg-white py-2 px-6 hidden sm:flex">
                <div class="w-5/6">
                    <h1 v-if="globalStateStore.pageHeaderTitle" class="text-2xl mb-1 font-bold text-gray-600">
                        {{ globalStateStore.pageHeaderTitle }}
                    </h1>
                </div>
                <div class="relative w-1/6 flex justify-end">
                    <a class="flex cursor-pointer focus:outline-none align-middle" @click="state.isAccountDropdownOpen = !state.isAccountDropdownOpen">
                        <span class="relative pt-3 mr-2">{{ authStore.user.full_name }} <Icon :name="state.isAccountDropdownOpen ? 'angle-up' : 'angle-down'"/></span>
                        <button class="relative z-10 w-12 h-12 rounded-full overflow-hidden border-4 border-gray-400 hover:border-gray-300 focus:border-gray-300 focus:outline-none">
                            <img :alt="authStore.user.full_name" v-if="authStore.user.avatar_url" :src="authStore.user.avatar_url">
                            <AvatarIcon v-else/>
                        </button>
                    </a>
                    <button v-if="state.isAccountDropdownOpen" @click="state.isAccountDropdownOpen = false" class="h-full w-full fixed inset-0 cursor-pointer"></button>
                    <div v-if="state.isAccountDropdownOpen" class="absolute w-32 bg-white rounded-lg shadow-lg py-2 mt-16 z-50">
                        <router-link to="/panel/dashboard" class="block px-4 py-2 hover:bg-theme-800 hover:text-white hover:opacity-80">
                            {{ trans('global.pages.home') }}
                        </router-link>
                        <router-link to="/page/markets" class="block px-4 py-2 hover:bg-theme-800 hover:text-white hover:opacity-80">
                            {{ trans('global.pages.markets') }}
                        </router-link>
                        <router-link to="/page/homework" class="block px-4 py-2 hover:bg-theme-800 hover:text-white hover:opacity-80">
                            {{ trans('global.pages.homework') }}
                        </router-link>
                        <router-link to="/page/orders" class="block px-4 py-2 hover:bg-theme-800 hover:text-white hover:opacity-80">
                            {{ trans('global.pages.orders') }}
                        </router-link>
                        <router-link to="/page/dev" class="block px-4 py-2 hover:bg-theme-800 hover:text-white hover:opacity-80">
                            {{ trans('global.pages.dev') }}
                        </router-link>
                        <router-link to="/panel/profile" class="block px-4 py-2 hover:bg-theme-800 hover:text-white hover:opacity-80">
                            {{ trans('global.pages.profile') }}
                        </router-link>
                        <router-link to="/panel/users/list" class="block px-4 py-2 hover:bg-theme-800 hover:text-white hover:opacity-80">
                            {{ trans('global.pages.users') }}
                        </router-link>
                        <a href="#" @click.prevent="onLogout" class="block px-4 py-2 hover:bg-theme-800 hover:text-white hover:opacity-80">{{
                                trans('global.phrases.sign_out')
                            }}</a>
                    </div>
                </div>
            </header>

            <!-- Mobile Header & Nav -->
            <header class="w-full bg-theme-600 py-5 px-6 sm:hidden">
                <div class="flex items-center justify-between">
                    <h1 v-if="globalStateStore.pageHeaderTitle">{{ globalStateStore.pageHeaderTitle }}</h1>
                    <button @click="state.isMobileMenuOpen = !state.isMobileMenuOpen" class="text-white text-3xl focus:outline-none">
                        <i v-if="!state.isMobileMenuOpen" class="fa fa-bars"></i>
                        <i v-else class="fa fa-times"></i>
                    </button>
                </div>
                <nav :class="state.isMobileMenuOpen ? 'flex': 'hidden'" class="flex flex-col pt-4 text-base text-white">
                    <Menu :state="state" :type="'mobile'"/>
                </nav>
            </header>

            <div class="w-full h-screen overflow-x-hidden border-t flex flex-col">
                <main class="w-full flex-grow p-6">
                    <router-view/>
                </main>
                <footer class="w-full bg-white text-center text-sm p-4" v-html="trans('global.phrases.copyright')"></footer>
            </div>

        </div>
    </div>
    <template v-else>
        <router-view/>
    </template>
</template>

<script>
import {computed, onBeforeMount, reactive} from "vue";

import {trans} from '@/helpers/i18n';
import Menu from "@/views/layouts/Menu";
import Icon from "@/views/components/icons/Icon";
import AvatarIcon from "@/views/components/icons/Avatar";
import {useAuthStore} from "@/stores/auth";
import {useGlobalStateStore} from "@/stores";
import {useRoute} from "vue-router";
import {useAlertStore} from "@/stores";
import {getAbilitiesForRoute} from "@/helpers/routing";
import Button from "@/views/components/input/Button";
import Spinner from "@/views/components/icons/Spinner";

export default {
    name: "app",
    components: {
        AvatarIcon,
        Menu,
        Icon,
        Button,
        Spinner
    },
    setup() {

        const alertStore = useAlertStore();
        const authStore = useAuthStore();
        const globalStateStore = useGlobalStateStore();
        const route = useRoute();

        const isLoading = computed(() => {
            var value = false;
            for(var i in globalStateStore.loadingElements) {
                if(globalStateStore.loadingElements[i]){
                    value = true;
                    break;
                }
            }
            return value || globalStateStore.isUILoading;
        })

        const state = reactive({
            mainMenu: [
                {
                    name: trans('global.pages.home'),
                    icon: 'tachometer',
                    showDesktop: true,
                    showMobile: true,
                    requiresAbility: false,
                    to: '/panel/dashboard',
                },
                {
                    name: trans('global.pages.markets'),
                    icon: 'bar-chart',
                    showDesktop: true,
                    showMobile: true,
                    requiresAbility: false,
                    to: '/page/markets',
                },
                {
                    name: trans('global.pages.homework'),
                    icon: 'home',
                    showDesktop: true,
                    showMobile: true,
                    requiresAbility: false,
                    to: '/page/homework',
                },
                {
                    name: trans('global.pages.orders'),
                    icon: 'book',
                    showDesktop: true,
                    showMobile: true,
                    requiresAbility: false,
                    to: '/page/orders',
                },
                {
                    name: trans('global.pages.dev'),
                    icon: 'bug',
                    showDesktop: true,
                    showMobile: true,
                    requiresAbility: false,
                    to: '/page/dev',
                },
                {
                    name: trans('global.pages.users'),
                    icon: 'users',
                    showDesktop: true,
                    showMobile: true,
                    requiresAbility: getAbilitiesForRoute(['users.list', 'users.create', 'users.edit']),
                    to: '/panel/users/list',
                    // children: [
                    //     {
                    //         name: trans('global.phrases.all_records'),
                    //         icon: '',
                    //         showDesktop: true,
                    //         showMobile: true,
                    //         requiresAbility: getAbilitiesForRoute('users.list'),
                    //         to: '/panel/users/list',
                    //     },
                    //     {
                    //         name: trans('global.buttons.add_new'),
                    //         icon: '',
                    //         showDesktop: true,
                    //         showMobile: true,
                    //         requiresAbility: getAbilitiesForRoute('users.create'),
                    //         to: '/panel/users/create',
                    //     }
                    // ]
                },
                {
                    name: trans('global.phrases.sign_out'),
                    icon: 'sign-out',
                    showDesktop: false,
                    showMobile: true,
                    showIfRole: false,
                    onClick: onLogout,
                    to: '',
                }
            ],
            headerLeftLink: {
                name: trans('global.buttons.new_record'),
                icon: 'plus',
                to: '',
                href: '#',
            },
            footerLeftLink: {
                name: trans('global.buttons.documentation'),
                icon: 'paperclip',
                to: '',
                href: '#',
            },
            isAccountDropdownOpen: false,
            isMobileMenuOpen: false,
            currentExpandedMenuItem: null,
            app: window.AppConfig,
        });

        function onLogout() {
            authStore.logout()
        }

        onBeforeMount(() => {
            if (route.query.hasOwnProperty('verified') && route.query.verified) {
                alertStore.success(trans('global.phrases.email_verified'));
            }
        });

        return {
            state,
            authStore,
            globalStateStore,
            trans,
            onLogout,
            isLoading,
        }
    }
};
</script>
