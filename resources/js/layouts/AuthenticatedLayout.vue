<script setup>
import AIChatbot from '@/components/AIChatbot.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-[#0c1a3a] via-[#1e3a8a] via-[#1e40af] to-[#3730a3] text-gray-900 font-sans pb-16">
        
        <header class="h-[66px] bg-white border-b-3 border-blue-600 px-[5%] md:px-[9%] flex items-center justify-between shadow-lg sticky top-0 z-50">
            <div class="flex items-center gap-2.5">
                <img src="/LOGO.png" alt="School Logo" class="w-[30px] h-[30px] object-contain" />
                <h2 class="text-xl font-bold text-blue-900 tracking-tight">GLMS <span class="text-xs font-semibold bg-blue-100 text-blue-700 px-2 py-0.5 rounded ml-1">Faculty</span></h2>
            </div>

            <nav class="hidden md:flex items-center gap-[34px]">
                <Link :href="route('teacher.lessons.name')" :class="route().current('teacher.lessons.name') ? 'text-blue-600 font-bold text-sm py-6 relative after:content-[\'\'] after:absolute after:left-0 after:bottom-[-3px] after:w-full after:h-[3px] after:bg-blue-600 after:rounded-t' : 'text-gray-600 hover:text-blue-600 text-sm font-semibold transition'">Dashboard</Link>
                <Link :href="route('teacher.lessons.index')" :class="route().current('teacher.lessons.index') ? 'text-blue-600 font-bold text-sm py-6 relative after:content-[\'\'] after:absolute after:left-0 after:bottom-[-3px] after:w-full after:h-[3px] after:bg-blue-600 after:rounded-t' : 'text-gray-600 hover:text-blue-600 text-sm font-semibold transition'">Lessons</Link>
                <Link :href="route('teacher.quizzes.index')" :class="route().current('teacher.quizzes.index') ? 'text-blue-600 font-bold text-sm py-6 relative after:content-[\'\'] after:absolute after:left-0 after:bottom-[-3px] after:w-full after:h-[3px] after:bg-blue-600 after:rounded-t' : 'text-gray-600 hover:text-blue-600 text-sm font-semibold transition'">Quizzes</Link>
                <Link :href="route('teacher.assignments.index')" :class="route().current('teacher.assignments.index') ? 'text-blue-600 font-bold text-sm py-6 relative after:content-[\'\'] after:absolute after:left-0 after:bottom-[-3px] after:w-full after:h-[3px] after:bg-blue-600 after:rounded-t' : 'text-gray-600 hover:text-blue-600 text-sm font-semibold transition'">Coursework</Link>
            </nav>

            <Link method="post" href="/logout" as="button" class="bg-gradient-to-r from-blue-800 to-blue-600 text-white px-4 py-2.5 rounded-lg text-xs font-semibold flex items-center gap-2 transition hover:from-red-600 hover:to-red-700 shadow-md">
                Logout Dashboard
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
            </Link>
        </header>

        <main class="max-w-7xl mx-auto px-4 lg:px-8 pt-8 space-y-6">
            <AIChatbot 
             user-role="teacher"
            greeting-message="Hi! I'm your AI helper! How can I help?"
            />

            <div v-if="$page.props.flash && $page.props.flash.message" class="bg-emerald-50 border border-emerald-300 text-emerald-800 p-4 rounded-xl shadow-sm flex items-center gap-2 text-sm font-medium">
                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                {{ $page.props.flash.message }}
            </div>
            <slot />
        </main>

    </div>
</template>