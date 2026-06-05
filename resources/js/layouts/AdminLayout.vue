<script setup>
import { Link } from '@inertiajs/vue3';
</script>


<template>
    <header class="h-[66px] bg-white border-b-3 border-blue-600 px-[5%] md:px-[9%] flex items-center justify-between shadow-lg sticky top-0 z-50">
            <div class="flex items-center gap-2.5">
                <img src="/images/LOGO.png" alt="School Logo" class="w-[30px] h-[30px] object-contain" />
                <h2 class="text-xl font-bold text-blue-900">GLMS <span class="text-xs text-red-600 font-black tracking-widest bg-red-50 px-2 py-0.5 rounded ml-1 uppercase">Admin</span></h2>
            </div>

            <nav class="hidden md:flex items-center gap-[34px]">
                <Link 
                    :href="route('admin.users.index')" 
                    :class="route().current('admin.users.index') 
                        ? 'text-blue-600 font-bold text-sm py-6 relative after:content-[\'\'] after:absolute after:left-0 after:bottom-[-3px] after:w-full after:h-[3px] after:bg-blue-600 after:rounded-t' 
                        : 'text-gray-600 hover:text-blue-600 text-sm font-semibold transition py-6'"
                >
                    User Management
                </Link>

                <Link 
                    :href="route('admin.subjects.index')" 
                    :class="route().current('admin.subjects.index') 
                        ? 'text-blue-600 font-bold text-sm py-6 relative after:content-[\'\'] after:absolute after:left-0 after:bottom-[-3px] after:w-full after:h-[3px] after:bg-blue-600 after:rounded-t' 
                        : 'text-gray-600 hover:text-blue-600 text-sm font-semibold transition py-6'"
                >
                    Subjects
                </Link>
            </nav>

            <Link method="post" href="/logout" as="button" class="bg-gradient-to-r from-blue-800 to-blue-600 text-white px-4 py-2.5 rounded-lg text-xs font-semibold flex items-center gap-2 transition hover:bg-red-600 shadow-md">
                Logout <i class="fa-solid fa-arrow-right-from-bracket"></i>
            </Link>
    </header>

    <div v-if="$page.props.flash && $page.props.flash.message" class="bg-emerald-50 border border-emerald-300 text-emerald-800 p-4 rounded-xl shadow-sm flex items-center gap-2 text-sm font-medium">
                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                {{ $page.props.flash.message }}
            </div>
            <slot />
</template>
