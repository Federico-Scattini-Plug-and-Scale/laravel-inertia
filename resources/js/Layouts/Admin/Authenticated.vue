<template>
    <div>
        <Toast v-if="$page.props.flash.message" :message="$page.props.flash.message"/>
        <div class="min-h-screen bg-gray-100">
            <nav class="bg-white border-b border-gray-100">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex-shrink-0 flex items-center">
                                <Link :href="route($page.props.locale + '.admin.dashboard')">
                                    <BreezeApplicationLogo class="block h-9 w-auto" />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <BreezeNavLink :href="route($page.props.locale + '.admin.dashboard')" :active="route().current($page.props.locale + '.admin.dashboard')">
                                    {{ __('Dashboard') }}
                                </BreezeNavLink>
                                <BreezeNavLink :href="route($page.props.locale + '.admin.tags.index')" :active="route().current($page.props.locale + '.admin.tags.index') || route().current($page.props.locale + '.admin.tags.edit')">
                                    {{ __('Tags') }}
                                </BreezeNavLink>
                                <BreezeNavLink :href="route($page.props.locale + '.admin.joboffertypes.index')" :active="route().current($page.props.locale + '.admin.joboffertypes.index') || route().current($page.props.locale + '.admin.joboffertypes.create') || route().current($page.props.locale + '.admin.joboffertypes.edit')">
                                    {{ __('Job offer types') }}
                                </BreezeNavLink>
                                <BreezeNavLink :href="route($page.props.locale + '.admin.categories.index')" :active="route().current($page.props.locale + '.admin.categories.index') || route().current($page.props.locale + '.admin.categories.create') || route().current($page.props.locale + '.admin.categories.edit')">
                                    {{ __('Categories') }}
                                </BreezeNavLink>
                                <BreezeNavLink :href="route($page.props.locale + '.admin.joboffers.index')" :active="route().current($page.props.locale + '.admin.joboffers.index')">
                                    {{ __('Job offers') }}
                                </BreezeNavLink>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ml-6">
                            <!-- Settings Dropdown -->
                            <div class="ml-3 relative">
                                <BreezeDropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                                {{ $page.props.auth.user.email }}

                                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <BreezeDropdownLink :href="route($page.props.locale + '.admin.profile', $page.props.auth.user)" as="button">
                                            {{ __('Profile') }}
                                        </BreezeDropdownLink>
                                        <BreezeDropdownLink :href="route($page.props.locale + '.admin.logout')" method="post" as="button">
                                            {{ __('Log Out') }}
                                        </BreezeDropdownLink>
                                    </template>
                                </BreezeDropdown>
                            </div>
                            <LangSwitcher />
                        </div>

                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button @click="showingNavigationDropdown = ! showingNavigationDropdown" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{'hidden': showingNavigationDropdown, 'inline-flex': ! showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{'hidden': ! showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{'block': showingNavigationDropdown, 'hidden': ! showingNavigationDropdown}" class="sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <BreezeResponsiveNavLink :href="route($page.props.locale + '.admin.dashboard')" :active="route().current($page.props.locale + '.admin.dashboard')">
                            {{ __('Dashboard') }}
                        </BreezeResponsiveNavLink>
                        <BreezeResponsiveNavLink :href="route($page.props.locale + '.admin.tags.index')" :active="route().current($page.props.locale + '.admin.tags.index') || route().current($page.props.locale + '.admin.tags.edit')">
                            {{ __('Tags') }}
                        </BreezeResponsiveNavLink>
                        <BreezeResponsiveNavLink :href="route($page.props.locale + '.admin.joboffertypes.index')" :active="route().current($page.props.locale + '.admin.joboffertypes.index') || route().current($page.props.locale + '.admin.joboffertypes.create') || route().current($page.props.locale + '.admin.joboffertypes.edit')">
                            {{ __('Job offer types') }}
                        </BreezeResponsiveNavLink>
                        <BreezeResponsiveNavLink :href="route($page.props.locale + '.admin.categories.index')" :active="route().current($page.props.locale + '.admin.categories.index') || route().current($page.props.locale + '.admin.categories.create') || route().current($page.props.locale + '.admin.categories.edit')">
                            {{ __('Categories') }}
                        </BreezeResponsiveNavLink>
                        <BreezeResponsiveNavLink :href="route($page.props.locale + '.admin.joboffers.index')" :active="route().current($page.props.locale + '.admin.joboffers.index')">
                            {{ __('Job offers') }}
                         </BreezeResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="px-4">
                            <div class="font-medium text-base text-gray-800">{{ $page.props.auth.user.name }}</div>
                            <div class="font-medium text-sm text-gray-500">{{ $page.props.auth.user.email }}</div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <BreezeResponsiveNavLink :href="route($page.props.locale + '.admin.profile', $page.props.auth.user)" as="button">
                                {{ __('Profile') }}
                            </BreezeResponsiveNavLink>
                            <BreezeResponsiveNavLink :href="route($page.props.locale + '.admin.logout')" method="post" as="button">
                                {{ __('Log Out') }}
                            </BreezeResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>

<script>
import BreezeApplicationLogo from '@/Components/ApplicationLogo.vue'
import BreezeDropdown from '@/Components/Dropdown.vue'
import BreezeDropdownLink from '@/Components/DropdownLink.vue'
import BreezeNavLink from '@/Components/NavLink.vue'
import BreezeResponsiveNavLink from '@/Components/ResponsiveNavLink.vue'
import LangSwitcher from '@/Components/LangSwitcher.vue'
import { Link } from '@inertiajs/inertia-vue3';
import Toast from '@/Components/Toast.vue'

export default {
    components: {
        BreezeApplicationLogo,
        BreezeDropdown,
        BreezeDropdownLink,
        BreezeNavLink,
        BreezeResponsiveNavLink,
        Link,
        LangSwitcher,
        Toast,
    },

    data() {
        return {
            showingNavigationDropdown: false,
        }
    },
}
</script>
