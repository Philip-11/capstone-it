<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    quiz: Object
});

// Gagawa tayo ng form gamit ang Inertia useForm
const form = useForm({
    answers: {}
});

const submitQuiz = () => {
    if (confirm('Are you sure you want to submit your answers?')) {
        form.post(route('student.quiz.submit', props.quiz.id), {
            onSuccess: () => {
                alert('Quiz submitted successfully!');
            },
            onError: (errors) => {
                console.error(errors);
                alert('Something went wrong. Please check your answers.');
            }
        });
    }
};

// Helper function para i-parse ang JSON options kung string pa ito galing sa DB
const parseOptions = (options) => {
    if (!options) return [];
    if (typeof options === 'string') {
        try {
            return JSON.parse(options);
        } catch (e) {
            return [];
        }
    }
    return options; // If array/object na siya talaga
};
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-[#0c1a3a] via-[#1e3a8a] via-[#1e40af] via-[#1d4ed8] to-[#3730a3] text-gray-900 font-sans pb-16">
        
        <header class="h-[66px] bg-white border-b-3 border-blue-600 px-[5%] md:px-[9%] flex items-center justify-between shadow-lg sticky top-0 z-50">
            <div class="flex items-center gap-2.5">
                <img src="/LOGO.png" alt="School Logo" class="w-[30px] h-[30px] object-contain" />
                <h2 class="text-xl font-bold text-blue-900">GLMS</h2>
            </div>

            <nav class="hidden md:flex items-center gap-[34px]">
                <Link :href="route('student.dashboard')" class="text-gray-600 hover:text-blue-600 text-sm font-semibold transition">Dashboard</Link>
                <Link :href="route('student.dashboard')" class="text-blue-600 font-bold text-sm py-6 relative after:content-[''] after:absolute after:left-0 after:bottom-[-3px] after:w-full after:h-[3px] after:bg-blue-600 after:rounded-t">My Courses</Link>
                <a href="#" class="text-gray-600 hover:text-blue-600 text-sm font-semibold transition">Assignments</a>
                <a href="#" class="text-gray-600 hover:text-blue-600 text-sm font-semibold transition">Profile</a>
            </nav>

            <Link method="post" href="/logout" as="button" class="bg-gradient-to-r from-blue-800 to-blue-600 text-white px-4 py-2.5 rounded-lg text-xs font-semibold flex items-center gap-2 transition hover:bg-red-600 shadow-md">
                Logout
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
            </Link>
        </header>

        <main class="max-w-4xl mx-auto px-4 md:px-0 pt-10 space-y-6">

            <section class="bg-white rounded-[24px] p-6 md:p-8 shadow-xl flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="space-y-1">
                    <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs font-bold tracking-wide">
                        <i class="fa-solid fa-gamepad text-xs mr-1"></i> Active Assessment
                    </span>
                    <h1 class="text-2xl md:text-3xl font-bold text-blue-950 mt-2">{{ quiz.title }}</h1>
                    <p class="text-sm text-gray-500 flex items-center gap-1.5 mt-0.5">
                        <i class="fa-solid fa-circle-check text-emerald-500"></i>
                        Passing Score Requirement: <span class="font-bold text-blue-700">{{ quiz.passing_score }}%</span>
                    </p>
                </div>
                
                <Link :href="route('student.subjects.show', quiz.lesson?.subject_id || 1)" class="inline-flex items-center gap-2 text-gray-600 bg-gray-100 hover:bg-gray-200 px-4 py-2.5 rounded-xl text-xs font-bold transition whitespace-nowrap">
                    <i class="fa-solid fa-arrow-left"></i> Back to Subject
                </Link>
            </section>

            <form @submit.prevent="submitQuiz" class="space-y-6">
                
                <div v-for="(question, index) in quiz.questions" :key="question.id" class="bg-white p-6 md:p-8 rounded-[22px] shadow-lg border border-gray-100 space-y-5 transition duration-200 hover:shadow-xl">
                    
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <span class="font-bold text-blue-600 text-sm tracking-wider uppercase">Question {{ index + 1 }}</span>
                        <span class="text-xs bg-blue-50 text-blue-700 font-bold px-3 py-1 rounded-xl border border-blue-100">
                            {{ question.points }} {{ question.points > 1 ? 'points' : 'point' }}
                        </span>
                    </div>
                    
                    <p class="text-blue-950 font-bold text-base md:text-lg leading-snug">{{ question.question_text }}</p>

                    <div v-if="question.question_type === 'multiple_choice' || question.question_type === 'true_or_false'" class="grid grid-cols-1 gap-3 pt-1">
                        <label 
                            v-for="option in parseOptions(question.options)" 
                            :key="option" 
                            class="flex items-center gap-3.5 p-4 rounded-xl border border-gray-200 hover:border-blue-400 hover:bg-blue-50/30 cursor-pointer transition-all duration-150 select-none"
                            :class="{'border-blue-600 bg-blue-50/70 shadow-sm ring-1 ring-blue-600': form.answers[question.id] === option}"
                        >
                            <input 
                                type="radio" 
                                :name="'question_' + question.id" 
                                :value="option" 
                                v-model="form.answers[question.id]"
                                class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 scale-110"
                            />
                            <span class="text-sm md:text-base font-semibold" :class="form.answers[question.id] === option ? 'text-blue-900' : 'text-gray-700'">
                                {{ option }}
                            </span>
                        </label>
                    </div>

                    <div v-else-if="question.question_type === 'short_answer'" class="pt-1">
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <i class="fa-solid fa-keyboard text-sm"></i>
                            </div>
                            <input 
                                type="text" 
                                v-model="form.answers[question.id]" 
                                placeholder="Type your dynamic code response or concept answer here..." 
                                class="w-full pl-10 pr-4 py-3.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 text-sm md:text-base font-medium transition"
                            />
                        </div>
                    </div>
                </div>

                <div class="pt-4 flex justify-end border-t border-white/10">
                    <button 
                        type="submit" 
                        :disabled="form.processing"
                        class="bg-gradient-to-r from-blue-800 to-blue-600 hover:from-blue-900 hover:to-blue-800 text-white font-bold px-8 py-3.5 rounded-xl shadow-lg transition duration-200 hover:-translate-y-0.5 active:scale-95 disabled:opacity-50 flex items-center gap-2 text-sm md:text-base"
                    >
                        <i class="fa-solid" :class="form.processing ? 'fa-spinner animate-spin' : 'fa-paper-plane'"></i>
                        {{ form.processing ? 'Submitting Answers...' : 'Submit Final Answers' }}
                    </button>
                </div>
            </form>
        </main>
    </div>
</template>

<style>
nav a.active::after {
    content: "";
    position: absolute;
    left: 0;
    bottom: -3px;
    width: 100%;
    height: 3px;
    background: #1d4ed8;
    border-radius: 10px 10px 0 0;
}
</style>