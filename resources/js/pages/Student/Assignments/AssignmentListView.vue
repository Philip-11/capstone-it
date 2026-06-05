<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AIChatbot from '@/components/AIChatbot.vue';

const props = defineProps({
    assignments: Array
});

// State para sa napiling Tab
const currentTab = ref('pending'); // Pwedeng 'all', 'pending', o 'completed'

// Computed property para sa pag-filter ng assignments base sa active tab
const filteredAssignments = computed(() => {
    if (currentTab.value === 'pending') {
        return props.assignments.filter(a => !a.is_submitted);
    } else if (currentTab.value === 'completed') {
        return props.assignments.filter(a => a.is_submitted);
    }
    return props.assignments;
});

// Helper function para sa due date text at badge styling base sa dashboard spec mo
const formatDueDate = (dateString, isSubmitted) => {
    if (isSubmitted) {
        return { text: 'Completed', class: 'bg-emerald-100 text-emerald-600' };
    }
    
    const dueDate = new Date(dateString);
    const today = new Date();
    
    today.setHours(0,0,0,0);
    dueDate.setHours(0,0,0,0);
    
    const diffTime = dueDate - today;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays < 0) {
        return { text: 'Overdue', class: 'bg-red-100 text-red-600' };
    } else if (diffDays === 0) {
        return { text: 'Due Today', class: 'bg-red-100 text-red-600 animate-pulse' };
    } else if (diffDays === 1) {
        return { text: 'Due Tomorrow', class: 'bg-amber-100 text-amber-600' };
    } else if (diffDays <= 3) {
        return { text: `Due in ${diffDays} days`, class: 'bg-orange-100 text-orange-600' };
    } else {
        const formatted = new Date(dateString).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        return { text: `Due: ${formatted}`, class: 'bg-blue-100 text-blue-600' };
    }
};
</script>

<template>
    <Head title="Assignments" />

    <div class="min-h-screen bg-gradient-to-br from-[#0c1a3a] via-[#1e3a8a] via-[#1e40af] via-[#1d4ed8] to-[#3730a3] text-gray-900 font-sans pb-12">
        
        <header class="h-[66px] bg-white border-b-3 border-blue-600 px-[5%] md:px-[9%] flex items-center justify-between shadow-lg sticky top-0 z-50">
            <div class="flex items-center gap-2.5">
                <img src="/images/LOGO.png" alt="School Logo" class="w-[30px] h-[30px] object-contain" />
                <h2 class="text-xl font-bold text-blue-900">GLMS</h2>
            </div>

            <nav class="hidden md:flex items-center gap-[34px]">
                <Link :href="route('student.dashboard')" class="text-gray-600 hover:text-blue-600 text-sm font-semibold transition">
                    Dashboard
                </Link>
                <Link :href="route('student.quizzes.index')" class="text-gray-600 hover:text-blue-600 text-sm font-semibold transition">
                    Quizzes
                </Link>
                <Link :href="route('student.assignments.index')" class="text-blue-600 font-bold text-sm py-6 relative after:content-[''] after:absolute after:left-0 after:bottom-[-3px] after:w-full after:h-[3px] after:bg-blue-600 after:rounded-t">
                    Assignments
                </Link>
            </nav>

            <Link method="post" href="/logout" as="button" class="bg-gradient-to-r from-blue-800 to-blue-600 text-white px-4 py-2.5 rounded-lg text-xs font-semibold flex items-center gap-2 transition hover:bg-red-600 shadow-md">
                Logout
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
            </Link>
        </header>

        <AIChatbot 
            user-role="student"
            greeting-message="Hi! I'm your AI study buddy! Do you have any questions about your assignments?"
        />

        <main class="max-w-7xl mx-auto px-4 md:px-[9%] pt-11 space-y-7">

            <section class="bg-gradient-to-br from-blue-800 to-blue-600 text-white p-8 md:p-12 rounded-[24px] text-center md:text-left shadow-xl">
                <span class="bg-white/20 px-3 py-1 rounded-full text-xs tracking-wide">Academic Tasks</span>
                <h1 class="text-3xl md:text-4xl font-bold my-[18px]">My Assignments</h1>
                <p class="max-w-[600px] text-blue-100 text-sm md:text-base">Keep track of your active tasks, submission deadlines, and review your completed coursework.</p>
            </section>

            <section class="flex gap-2 p-1.5 bg-white/10 backdrop-blur-sm rounded-xl max-w-md border border-white/10">
                <button 
                    @click="currentTab = 'pending'"
                    :class="[currentTab === 'pending' ? 'bg-white text-blue-900 font-bold shadow-md' : 'text-white/80 hover:bg-white/5 hover:text-white']"
                    class="flex-1 py-2 text-center text-xs md:text-sm rounded-lg transition"
                >
                    Pending Tasks
                </button>
                <button 
                    @click="currentTab = 'completed'"
                    :class="[currentTab === 'completed' ? 'bg-white text-blue-900 font-bold shadow-md' : 'text-white/80 hover:bg-white/5 hover:text-white']"
                    class="flex-1 py-2 text-center text-xs md:text-sm rounded-lg transition"
                >
                    Completed
                </button>
                <button 
                    @click="currentTab = 'all'"
                    :class="[currentTab === 'all' ? 'bg-white text-blue-900 font-bold shadow-md' : 'text-white/80 hover:bg-white/5 hover:text-white']"
                    class="flex-1 py-2 text-center text-xs md:text-sm rounded-lg transition"
                >
                    All
                </button>
            </section>

            <section class="bg-white rounded-[18px] p-6 shadow-md space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-blue-900 capitalize">{{ currentTab }} Assignments</h3>
                    <span class="text-xs font-bold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-md">
                        Total: {{ filteredAssignments.length }}
                    </span>
                </div>

                <div class="space-y-2">
                    <div v-if="filteredAssignments.length > 0" class="space-y-2">
                        <div 
                            v-for="assignment in filteredAssignments" 
                            :key="assignment.id"
                            class="flex flex-col sm:flex-row justify-between sm:items-center p-4 rounded-xl hover:bg-gray-50 transition border border-gray-100 gap-3"
                        >
                            <div class="space-y-0.5">
                                <h4 class="font-bold text-blue-900 text-sm md:text-base">
                                    {{ assignment.title }}
                                </h4>
                                <p class="text-gray-400 text-xs" v-if="assignment.lesson && assignment.lesson.subject">
                                    {{ assignment.lesson.subject.name }} &bull; Lesson: {{ assignment.lesson.title }}
                                </p>
                            </div>
                            
                            <div class="flex items-center gap-3 self-start sm:self-center w-full sm:w-auto justify-between sm:justify-end border-t border-gray-50 sm:border-t-0 pt-2 sm:pt-0">
                                <span 
                                    :class="formatDueDate(assignment.due_date, assignment.is_submitted).class"
                                    class="px-3 py-1 rounded-full text-xs font-bold transition-colors whitespace-nowrap min-w-[95px] text-center"
                                >
                                    {{ formatDueDate(assignment.due_date, assignment.is_submitted).text }}
                                </span>

                                <Link 
                                    :href="route('student.assignments.show', assignment.id)"
                                    :class="[assignment.is_submitted ? 'bg-gray-100 hover:bg-gray-200 text-gray-700' : 'bg-gradient-to-br from-blue-800 to-blue-600 hover:from-blue-900 hover:to-blue-800 text-white shadow-sm']"
                                    class="px-4 py-2 rounded-xl font-bold text-xs transition whitespace-nowrap text-center flex-1 sm:flex-none"
                                >
                                    {{ assignment.is_submitted ? 'Review Submission' : 'Take Assignment' }}
                                </Link>
                            </div>
                        </div>
                    </div>

                    <div v-else class="text-center py-12 text-gray-400 text-sm border-2 border-dashed border-gray-100 rounded-xl">
                        <i class="fa-solid fa-circle-check text-emerald-400 text-3xl mb-2 block"></i>
                        No assignments found in this category.
                    </div>
                </div>
            </section>

        </main>
    </div>
</template>