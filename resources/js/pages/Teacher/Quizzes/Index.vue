<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({ quizzes: Array });
const form = useForm({});

const deleteQuiz = (id) => {
    if (confirm('Are you sure you want to delete this quiz?')) {
        form.delete(route('teacher.quizzes.destroy', id));
    }
};
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-[#0c1a3a] via-[#1e3a8a] via-[#1e40af] to-[#3730a3] text-gray-900 font-sans pb-16">

        <main class="max-w-5xl mx-auto px-4 md:px-0 pt-10 space-y-6">

            <section class="bg-white rounded-[24px] p-6 md:p-8 shadow-xl flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="space-y-1">
                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold tracking-wide">
                        <i class="fa-solid fa-lightbulb text-xs mr-1"></i> Exam/Quiz Maker
                    </span>
                    <h1 class="text-2xl md:text-3xl font-bold text-blue-950 mt-1">Quizzes</h1>
                    <p class="text-sm text-gray-500">Create a quiz, exam for your subject.</p>
                </div>
                
                <Link 
                    :href="route('teacher.quizzes.create')" 
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-700 to-blue-600 hover:from-blue-800 hover:to-blue-700 text-white px-5 py-3 rounded-xl text-xs font-bold shadow-lg transition duration-150 hover:-translate-y-0.5 whitespace-nowrap w-full sm:w-auto justify-center"
                >
                    <i class="fa-solid fa-plus"></i> Create New Quiz
                </Link>
            </section>

            <section class="bg-white rounded-[24px] shadow-xl border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-sm">
                        
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100 text-gray-600 font-bold">
                                <th class="p-4 md:p-5 text-left uppercase tracking-wider text-xs">Quiz Title</th>
                                <th class="p-4 md:p-5 text-left uppercase tracking-wider text-xs">Total Registered Items</th>
                                <th class="p-4 md:p-5 text-center uppercase tracking-wider text-xs">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 bg-white">
                            <tr 
                                v-for="quiz in quizzes" 
                                :key="quiz.id" 
                                class="hover:bg-blue-50/30 transition duration-150"
                            >
                                <td class="p-4 md:p-5 font-bold text-blue-950">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
                                            <i class="fa-solid fa-circle-question text-xs"></i>
                                        </div>
                                        <span class="hover:text-blue-700 cursor-pointer transition">{{ quiz.title }}</span>
                                    </div>
                                </td>

                                <td class="p-4 md:p-5 font-medium text-gray-600">
                                    <span class="bg-emerald-50 text-emerald-700 font-bold px-2.5 py-1 rounded-lg text-xs border border-emerald-100">
                                        {{ quiz.questions_count || 0 }} {{ (quiz.questions_count || 0) > 1 ? 'items' : 'item' }}
                                    </span>
                                </td>

                                <td class="p-4 md:p-5 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <Link 
                                            :href="route('teacher.quizzes.edit', quiz.id)" 
                                            class="inline-flex items-center gap-1 px-3 py-2 bg-gray-50 text-amber-600 border border-gray-100 hover:border-amber-200 hover:bg-amber-50 rounded-xl text-xs font-bold transition"
                                            title="Modify Quiz Structure"
                                        >
                                            <i class="fa-solid fa-pen text-[11px]"></i> Edit
                                        </Link>
                                        
                                        <button 
                                            @click="deleteQuiz(quiz.id)" 
                                            class="inline-flex items-center gap-1 px-3 py-2 bg-gray-50 text-red-600 border border-gray-100 hover:border-red-200 hover:bg-red-50 rounded-xl text-xs font-bold transition"
                                            title="Purge Quiz Record"
                                        >
                                            <i class="fa-solid fa-trash-can text-[11px]"></i> Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="quizzes.length === 0">
                                <td colspan="3" class="p-12 text-center text-gray-400 italic font-medium">
                                    <div class="space-y-2">
                                        <i class="fa-solid fa-receipt text-3xl text-gray-300"></i>
                                        <p class="text-sm">No live examination scripts currently deployed in this faculty folder.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>

                    </table>
                </div>
            </section>
            
        </main>
    </div>
</template>