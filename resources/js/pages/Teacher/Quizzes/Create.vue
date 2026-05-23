<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({ lessons: Array });

const form = useForm({
    title: '',
    lesson_id: '',
    passing_score: 70,
    questions: [
        { question_text: '', question_type: 'multiple_choice', options: ['', '', '', ''], correct_answer: '', points: 1 }
    ]
});

const addQuestion = () => {
    form.questions.push({ 
        question_text: '', 
        question_type: 'multiple_choice', 
        options: ['', '', '', ''], 
        correct_answer: '', 
        points: 1 
    });
};

const removeQuestion = (index) => {
    if (form.questions.length > 1) {
        form.questions.splice(index, 1);
    } else {
        alert('A quiz must have at least one question.');
    }
};

const submit = () => {
    form.post(route('teacher.quizzes.store'), {
        onSuccess: () => {
            alert('Quiz schema deployed successfully!');
        }
    });
};
</script>

<template>
    <Head title="Create Quiz" />

    <div class="min-h-screen bg-gradient-to-br from-[#0c1a3a] via-[#1e3a8a] via-[#1e40af] to-[#3730a3] text-gray-900 font-sans pb-16">

        <main class="max-w-4xl mx-auto px-4 md:px-0 pt-10 space-y-6">

            <section class="bg-white rounded-[24px] p-6 shadow-xl flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-lg shadow-sm">
                        <i class="fa-solid fa-brain text-sm"></i>
                    </div>
                    <div>
                        <h1 class="text-xl md:text-2xl font-bold text-blue-950">Quiz Maker</h1>
                        <p class="text-xs text-gray-500">Draft assessment tests or exams.</p>
                    </div>
                </div>
                
                <Link :href="route('teacher.quizzes.index')" class="inline-flex items-center gap-2 text-gray-600 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-xl text-xs font-bold transition">
                    <i class="fa-solid fa-arrow-left"></i> Back to Hub
                </Link>
            </section>

            <form @submit.prevent="submit" class="space-y-6">
                
                <section class="bg-white p-6 rounded-[24px] shadow-xl border border-gray-100 space-y-4">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-600 uppercase tracking-wider">Quiz Title</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <i class="fa-solid fa-paragraph text-xs"></i>
                            </span>
                            <input v-model="form.title" type="text" placeholder="e.g., Midterm Examination - Object Oriented Programming" class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 transition" required />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-2 space-y-1">
                            <label class="text-xs font-bold text-gray-600 uppercase tracking-wider">Link to Specific Lesson (Optional)</label>
                            <select v-model="form.lesson_id" class="w-full border border-gray-200 p-3 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 bg-white transition">
                                <option value="">Standalone Evaluation (No Linked Topic)</option>
                                <option v-for="lesson in lessons" :key="lesson.id" :value="lesson.id">{{ lesson.title }}</option>
                            </select>
                        </div>
                        
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-600 uppercase tracking-wider">Passing Rate (%)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                    <i class="fa-solid fa-percent text-xs"></i>
                                </span>
                                <input v-model="form.passing_score" type="number" min="1" max="100" class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 transition" required />
                            </div>
                        </div>
                    </div>
                </section>

                <div 
                    v-for="(question, index) in form.questions" 
                    :key="index" 
                    class="bg-white p-6 rounded-[24px] shadow-xl border-l-4 border-blue-600 border border-gray-100 space-y-5 transition duration-150"
                >
                    <div class="flex justify-between items-center pb-3 border-b border-gray-50">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-blue-50 text-blue-600 font-bold text-xs flex items-center justify-center">
                                {{ index + 1 }}
                            </span>
                            <span class="font-bold text-blue-950 text-sm tracking-tight">Question Structure</span>
                        </div>
                        <button 
                            type="button" 
                            @click="removeQuestion(index)" 
                            class="inline-flex items-center gap-1 text-xs font-bold text-red-500 hover:text-red-700 transition"
                        >
                            <i class="fa-solid fa-trash text-[10px]"></i> Remove Question
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Question Text</label>
                            <textarea v-model="question.question_text" rows="3" placeholder="Input your question here..." class="w-full border border-gray-200 p-3 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 transition" required></textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Structural Logic Type</label>
                                <select v-model="question.question_type" class="w-full border border-gray-200 p-3 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 bg-white transition">
                                    <option value="multiple_choice">Multiple Choice</option>
                                    <option value="true_or_false">True or False</option>
                                    <option value="short_answer">Short Answer Box</option>
                                </select>
                            </div>
                            
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Score Point Value</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                        <i class="fa-solid fa-award text-xs"></i>
                                    </span>
                                    <input v-model="question.points" type="number" min="1" class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 transition" placeholder="Points Value" />
                                </div>
                            </div>
                        </div>

                        <div v-if="question.question_type === 'multiple_choice'" class="space-y-2 bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                            <label class="text-xs font-bold text-blue-900 uppercase tracking-wide block mb-1">Populate Option Selections</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div v-for="(opt, oIdx) in 4" :key="oIdx" class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center font-bold text-[11px] text-gray-400 uppercase">
                                        {{ String.fromCharCode(65 + oIdx) }} </span>
                                    <input v-model="question.options[oIdx]" type="text" :placeholder="'Option Value'" class="w-full pl-8 pr-4 py-2.5 border border-gray-200 bg-white rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-500 transition" required />
                                </div>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-emerald-800 uppercase tracking-wide">Correct Answer</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-600">
                                    <i class="fa-solid fa-circle-check text-xs"></i>
                                </span>
                                <input v-model="question.correct_answer" type="text" placeholder="Specify exact string or option key value for absolute auto-match grading..." class="w-full pl-10 pr-4 py-3 border border-emerald-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-emerald-50/40 text-emerald-950 font-medium placeholder-emerald-600/50" required />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-white/10 flex flex-col sm:flex-row justify-between items-center gap-3">
                    <button 
                        type="button" 
                        @click="addQuestion" 
                        class="inline-flex items-center gap-2 text-gray-700 bg-white hover:bg-gray-100 border border-gray-200 px-5 py-3 rounded-xl text-xs font-bold shadow transition w-full sm:w-auto justify-center"
                    >
                        <i class="fa-solid fa-circle-plus text-blue-600"></i> Append Another Question Block
                    </button>
                    
                    <button 
                        type="submit" 
                        :disabled="form.processing" 
                        class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-700 to-blue-600 hover:from-blue-800 hover:to-blue-700 text-white px-8 py-3.5 rounded-xl text-xs font-bold shadow-lg transition duration-150 hover:-translate-y-0.5 disabled:opacity-50 w-full sm:w-auto justify-center"
                    >
                        <i class="fa-solid" :class="form.processing ? 'fa-spinner animate-spin' : 'fa-floppy-disk-pen'"></i>
                        {{ form.processing ? 'Deploying Examination Schema...' : 'Deploy Live Exam Script' }}
                    </button>
                </div>
            </form>
        </main>
    </div>
</template>