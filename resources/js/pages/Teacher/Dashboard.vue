<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    quizAttempts: Array,
});
</script>

<template>
    <Head title="Teacher Dashboard" />

    <div class="min-h-screen bg-gradient-to-br from-[#0c1a3a] via-[#1e3a8a] via-[#1e40af] to-[#3730a3] text-gray-900 font-sans pb-16">

        <main class="max-w-7xl mx-auto px-4 lg:px-8 pt-8 space-y-6">
            
            <div v-if="$page.props.flash && $page.props.flash.message" class="bg-emerald-50 border border-emerald-300 text-emerald-800 p-4 rounded-xl shadow-sm flex items-center gap-2 text-sm font-medium">
                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                {{ $page.props.flash.message }}
            </div>

            <section class="bg-white rounded-[24px] p-6 md:p-8 shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-blue-950">Instructor Control Center</h1>
                    <p class="text-sm text-gray-500 mt-1">Deploy module paths, coordinate interactive activities, and observe real-time classroom statistics.</p>
                </div>
                
                <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                    <Link :href="route('teacher.quizzes.index')" class="flex-1 sm:flex-initial text-center bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 px-5 py-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-paste"></i> Quizzes Hub
                    </Link>
                    <Link :href="route('teacher.assignments.index')" class="flex-1 sm:flex-initial text-center bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 px-5 py-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-briefcase"></i> Activities Hub
                    </Link>
                </div>
            </section>

            <section class="bg-white p-6 md:p-8 rounded-[24px] shadow-xl border border-gray-100 space-y-4">
                <div>
                    <h2 class="text-xl font-bold text-blue-950 flex items-center gap-2">
                        <i class="fa-solid fa-trophy text-amber-500"></i> Live Exam & Assessment Score Metrics
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">Comprehensive grid log tracking individual student assessment completion logs and grade distributions.</p>
                </div>
                
                <div class="overflow-x-auto rounded-xl border border-gray-100">
                    <table class="min-w-full divide-y divide-gray-200 text-xs md:text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4.5 text-left font-bold text-gray-600 uppercase tracking-wider">Student Academic Name</th>
                                <th class="px-6 py-4.5 text-left font-bold text-gray-600 uppercase tracking-wider">Exam Quiz Module Header</th>
                                <th class="px-6 py-4.5 text-left font-bold text-gray-600 uppercase tracking-wider">Total Score Metric</th>
                                <th class="px-6 py-4.5 text-left font-bold text-gray-600 uppercase tracking-wider">Percentage Grade Status</th>
                                <th class="px-6 py-4.5 text-left font-bold text-gray-600 uppercase tracking-wider">Submission Log Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="attempt in quizAttempts" :key="attempt.id" class="hover:bg-gray-50/80 transition duration-100">
                                <td class="px-6 py-4 font-bold text-gray-900 flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs uppercase">
                                        {{ attempt.student?.name ? attempt.student.name.charAt(0) : 'S' }}
                                    </div>
                                    {{ attempt.student?.name || 'Unknown Enrollee' }}
                                </td>
                                <td class="px-6 py-4 text-gray-600 font-medium">{{ attempt.quiz?.title || 'System Quiz Module' }}</td>
                                <td class="px-6 py-4 font-bold text-blue-950">
                                    {{ attempt.score }} <span class="text-gray-400 font-normal">/ {{ attempt.total_questions }} items</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold border" :class="(attempt.score / attempt.total_questions) * 100 >= 70 ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-red-50 text-red-600 border-red-200'">
                                        {{ Math.round((attempt.score / attempt.total_questions) * 100) }}% Grade
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500 font-medium">
                                    {{ new Date(attempt.completed_at).toLocaleDateString([], { year: 'numeric', month: 'short', day: 'numeric' }) }}
                                </td>
                            </tr>
                            
                            <tr v-if="quizAttempts.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic text-sm">
                                    No records found. No student attempts logged in current evaluation parameters.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

        </main>
    </div>
</template>