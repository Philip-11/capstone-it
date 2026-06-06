<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    quizAttempts: Array,
    assignmentSubmissions: Array,
});

const processGrading = (submission) => {
    // 1. Validation check sa input pre
    if (submission.inputScore === undefined || submission.inputScore === '') {
        alert('Please specify a valid numeric grade value first before clicking save.');
        return;
    }
    
    if (submission.inputScore < 0 || submission.inputScore > 100) {
        alert('Academic Grade Over-range: Please keep scores strictly between 0 and 100 points.');
        return;
    }

    // 2. Put action call via Inertia core router pre
    router.put(route('teacher.submissions.grade', submission.id), {
        grade: submission.inputScore
    }, {
        preserveScroll: true, // Para hindi umalon o tumalon ang scrollbar kapag clinick si save
        onSuccess: () => {
            submission.inputScore = ''; // Reset input buffer box pre pagkatapos ma-save
        }
    });
};
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

            <section class="bg-white p-6 md:p-8 rounded-[24px] shadow-xl border border-gray-100 space-y-4">
                <div>
                    <h2 class="text-xl font-bold text-blue-950 flex items-center gap-2">
                        <i class="fa-solid fa-file-arrow-up text-indigo-500"></i> Coursework & Assignment Submission Audit
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">Track individual student assignment file submissions, evaluate timing compliance, and register numeric grades.</p>
                </div>
                
                <div class="overflow-x-auto rounded-xl border border-gray-100">
                    <table class="min-w-full divide-y divide-gray-200 text-xs md:text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4.5 text-left font-bold text-gray-600 uppercase tracking-wider">Student Academic Name</th>
                                <th class="px-6 py-4.5 text-left font-bold text-gray-600 uppercase tracking-wider">Assignment Header</th>
                                <th class="px-6 py-4.5 text-left font-bold text-gray-600 uppercase tracking-wider">Timing Status</th>
                                <th class="px-6 py-4.5 text-left font-bold text-gray-600 uppercase tracking-wider">Evaluated Grade</th>
                                <th class="px-6 py-4.5 text-left font-bold text-gray-600 uppercase tracking-wider text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="submission in assignmentSubmissions" :key="submission.id" class="hover:bg-gray-50/80 transition duration-100">
                                <td class="px-6 py-4 font-bold text-gray-900 flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs uppercase">
                                        {{ submission.student?.name ? submission.student.name.charAt(0) : 'S' }}
                                    </div>
                                    {{ submission.student?.name || 'Unknown Enrollee' }}
                                </td>
                                
                                <td class="px-6 py-4 text-gray-600 font-medium">
                                    {{ submission.assignment?.title || 'System Task Module' }}
                                </td>
                                
                                <td class="px-6 py-4">
                                    <span v-if="submission.is_late" 
                                        class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-red-600 border border-red-200">
                                        <i class="fa-solid fa-clock-rotate-left mr-1"></i> Late Submission
                                    </span>
                                    <span v-else class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">
                                        <i class="fa-solid fa-circle-check mr-1"></i> On-Time
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 font-black">
                                    <span v-if="submission.grade !== null" class="text-blue-900 bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-200">
                                        {{ submission.grade }} <span class="text-gray-400 font-normal">/ 100</span>
                                    </span>
                                    <span v-else class="text-amber-600 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200 text-[11px] font-bold">
                                        <i class="fa-solid fa-hourglass-half animate-spin text-[10px] mr-1"></i> Pending Review
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <input 
                                            v-model="submission.inputScore" 
                                            type="number" 
                                            placeholder="0-100" 
                                            class="w-16 p-1 text-center font-bold text-xs border rounded-lg focus:outline-indigo-600"
                                        />
                                        <button 
                                            @click="processGrading(submission)" 
                                            class="bg-gradient-to-r from-indigo-600 to-blue-600 text-white px-3 py-1.5 rounded-lg text-xs font-black hover:shadow-md transition active:scale-95 flex items-center gap-1"
                                        >
                                            <i class="fa-solid fa-marker"></i> Save Grade
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <tr v-if="assignmentSubmissions.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic text-sm">
                                    No records found. No student coursework submissions logged in current parameters.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

        </main>
    </div>
</template>