<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AIChatbot from '@/components/AIChatbot.vue';

const props = defineProps({
    quizzes: Array
});

// State para sa napiling Tab
const currentTab = ref('pending'); // Pwedeng 'all', 'pending', o 'completed'

// Filter para sa mga Quizzes base sa pinindot na tab
const filteredQuizzes = computed(() => {
    if (currentTab.value === 'pending') {
        return props.quizzes.filter(q => !q.is_submitted);
    } else if (currentTab.value === 'completed') {
        return props.quizzes.filter(q => q.is_submitted);
    }
    return props.quizzes;
});
</script>

<template>
    <Head title="My Quizzes" />

    <!-- Eksaktong Dashboard Gradient Background mo pre -->
    <div class="min-h-screen bg-gradient-to-br from-[#0c1a3a] via-[#1e3a8a] via-[#1e40af] via-[#1d4ed8] to-[#3730a3] text-gray-900 font-sans pb-12">
        
        <!-- HEADER: Yung active border-b ay nakatutok naman ngayon sa Quizzes/Courses navigation context niyo -->
        <header class="h-[66px] bg-white border-b-3 border-blue-600 px-[5%] md:px-[9%] flex items-center justify-between shadow-lg sticky top-0 z-50">
            <div class="flex items-center gap-2.5">
                <img src="/images/LOGO.png" alt="School Logo" class="w-[30px] h-[30px] object-contain" />
                <h2 class="text-xl font-bold text-blue-900">GLMS</h2>
            </div>

            <nav class="hidden md:flex items-center gap-[34px]">
                <Link :href="route('student.dashboard')" class="text-gray-600 hover:text-blue-600 text-sm font-semibold transition">
                    Dashboard
                </Link>
                <!-- Pwede mo rin itong lagyan ng dynamic link balang araw pre -->
                <Link :href="route('student.quizzes.index')" class="text-blue-600 font-bold text-sm py-6 relative after:content-[''] after:absolute after:left-0 after:bottom-[-3px] after:w-full after:h-[3px] after:bg-blue-600 after:rounded-t">
                    Quizzes
                </Link>
                <Link :href="route('student.assignments.index')" class="text-gray-600 hover:text-blue-600 text-sm font-semibold transition">
                    Assignments
                </Link>
            </nav>

            <Link method="post" href="/logout" as="button" class="bg-gradient-to-r from-blue-800 to-blue-600 text-white px-4 py-2.5 rounded-lg text-xs font-semibold flex items-center gap-2 transition hover:bg-red-600 shadow-md">
                Logout
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
            </Link>
        </header>

        <!-- AI Chatbot Buddy -->
        <AIChatbot 
            user-role="student"
            greeting-message="Hi! Ready to review for your quizzes? Let me know if you need help studying!"
        />

        <!-- MAIN CONTAINER -->
        <main class="max-w-7xl mx-auto px-4 md:px-[9%] pt-11 space-y-7">

            <!-- Welcome/Banner Section sa Quizzes -->
            <section class="bg-gradient-to-br from-indigo-800 to-blue-700 text-white p-8 md:p-12 rounded-[24px] text-center md:text-left shadow-xl">
                <span class="bg-white/20 px-3 py-1 rounded-full text-xs tracking-wide">Knowledge Assessment</span>
                <h1 class="text-3xl md:text-4xl font-bold my-[18px]">My Quizzes</h1>
                <p class="max-w-[600px] text-blue-100 text-sm md:text-base">Test your understanding, take ongoing subject tests, and view your evaluation records.</p>
            </section>

            <!-- Pill Navigation Controls -->
            <section class="flex gap-2 p-1.5 bg-white/10 backdrop-blur-sm rounded-xl max-w-md border border-white/10">
                <button 
                    @click="currentTab = 'pending'"
                    :class="[currentTab === 'pending' ? 'bg-white text-blue-900 font-bold shadow-md' : 'text-white/80 hover:bg-white/5 hover:text-white']"
                    class="flex-1 py-2 text-center text-xs md:text-sm rounded-lg transition"
                >
                    Pending Quizzes
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

            <!-- Main White List Box (Gaya ng Dashboard mo) -->
            <section class="bg-white rounded-[18px] p-6 shadow-md space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-blue-900 capitalize">{{ currentTab }} Quizzes</h3>
                    <span class="text-xs font-bold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-md">
                        Total: {{ filteredQuizzes.length }}
                    </span>
                </div>

                <div class="space-y-2">
                    <div v-if="filteredQuizzes.length > 0" class="space-y-2">
                        <div 
                            v-for="quiz in filteredQuizzes" 
                            :key="quiz.id"
                            class="flex flex-col sm:flex-row justify-between sm:items-center p-4 rounded-xl hover:bg-gray-50 transition border border-gray-100 gap-3"
                        >
                            <div class="space-y-0.5">
                                <h4 class="font-bold text-blue-900 text-sm md:text-base">
                                    {{ quiz.title || quiz.name }}
                                </h4>
                                <!-- Course Metadata Subtext -->
                                <p class="text-gray-400 text-xs" v-if="quiz.lesson && quiz.lesson.subject">
                                    {{ quiz.lesson.subject.name }} &bull; Lesson: {{ quiz.lesson.title }}
                                </p>
                            </div>
                            
                            <div class="flex items-center gap-3 self-start sm:self-center w-full sm:w-auto justify-between sm:justify-end border-t border-gray-50 sm:border-t-0 pt-2 sm:pt-0">
                                <!-- Status Badges (Green kapag tapos na, Light Purple kapag sasagutan pa lang) -->
                                <span 
                                    :class="[quiz.is_submitted ? 'bg-emerald-100 text-emerald-600' : 'bg-purple-100 text-purple-600']"
                                    class="px-3 py-1 rounded-full text-xs font-bold whitespace-nowrap min-w-[95px] text-center"
                                >
                                    {{ quiz.is_submitted ? 'Completed' : 'Available' }}
                                </span>

                                <!-- Action Buttons (Ituro mo sa link kung saan sumasagot ng quiz sa view code mo pre) -->
                                <Link 
                                    :href="route('student.quiz.show', quiz.id)"
                                    :class="[quiz.is_submitted ? 'bg-gray-100 hover:bg-gray-200 text-gray-700' : 'bg-gradient-to-br from-indigo-700 to-blue-600 hover:from-indigo-800 hover:to-blue-700 text-white shadow-sm']"
                                    class="px-4 py-2 rounded-xl font-bold text-xs transition whitespace-nowrap text-center flex-1 sm:flex-none"
                                >
                                    {{ quiz.is_submitted ? 'View Results' : 'Start Quiz' }}
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State Container -->
                    <div v-else class="text-center py-12 text-gray-400 text-sm border-2 border-dashed border-gray-100 rounded-xl">
                        <i class="fa-solid fa-file-signature text-indigo-400 text-3xl mb-2 block"></i>
                        No quizzes found in this category.
                    </div>
                </div>
            </section>

        </main>
    </div>
</template>