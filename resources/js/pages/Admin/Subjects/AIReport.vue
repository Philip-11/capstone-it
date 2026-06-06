<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';

const props = defineProps({
    subject: Object,
    student: Object,
    analytics: Object,
    ai_report: Object
});
</script>

<template>
    <Head :title="'AI Analytics - ' + student.name" />

    <div class="min-h-screen bg-gradient-to-br from-[#0c1a3a] via-[#1e3a8a] via-[#1e40af] via-[#1d4ed8] to-[#3730a3] text-gray-900 font-sans pb-12">
        
        <AdminLayout>
            <main class="max-w-6xl mx-auto px-4 md:px-[9%] pt-11 space-y-6">
                
                <div class="flex items-center gap-2 text-white/80 text-sm">
                    <Link :href="route('admin.subjects.manage', subject.id)" class="hover:text-white transition font-semibold">Subject Center</Link>
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                    <span class="text-white font-black">{{ student.name }} Analysis</span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                    
                    <div class="lg:col-span-1 space-y-5">
                        <div class="bg-white rounded-[22px] p-6 shadow-xl border border-gray-100 space-y-5">
                            <div class="text-center pb-4 border-b">
                                <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-2xl mx-auto flex items-center justify-center text-2xl font-black mb-3 shadow-inner">
                                    {{ student.name.substring(0, 2).toUpperCase() }}
                                </div>
                                <h2 class="font-black text-xl text-blue-950">{{ student.name }}</h2>
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mt-0.5">{{ subject.name }}</p>
                            </div>

                            <div class="space-y-3">
                                <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest"><i class="fa-solid fa-vial text-purple-500"></i> Quizzes Audit</h4>
                                <div class="bg-slate-50 p-3.5 rounded-xl space-y-2 border">
                                    <div class="flex justify-between text-xs"><span class="text-gray-500">Total Taken:</span><span class="font-bold text-slate-800">{{ analytics.quizzes_summary.total_quizzes_taken }} exam(s)</span></div>
                                    <div class="flex justify-between text-xs"><span class="text-gray-500">Average Points:</span><span class="font-bold text-purple-600">{{ analytics.quizzes_summary.average_score_points }}</span></div>
                                    <div class="flex justify-between text-xs"><span class="text-gray-500">Time Metric:</span><span class="font-bold text-gray-700">{{ analytics.quizzes_summary.average_time_spent }}</span></div>
                                </div>
                            </div>

                            <div class="space-y-3 pt-2">
                                <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest"><i class="fa-solid fa-file-lines text-indigo-500"></i> Coursework Logs</h4>
                                <div class="bg-slate-50 p-3.5 rounded-xl space-y-2 border">
                                    <div class="flex justify-between text-xs"><span class="text-gray-500">Total Submissions:</span><span class="font-bold text-slate-800">{{ analytics.assignments_summary.total_submitted }} files</span></div>
                                    <div class="flex justify-between text-xs"><span class="text-gray-500">On-Time Subits:</span><span class="font-bold text-emerald-600">{{ analytics.assignments_summary.on_time }}</span></div>
                                    <div class="flex justify-between text-xs"><span class="text-gray-500">Late Overdue:</span><span class="font-bold text-red-500">{{ analytics.assignments_summary.late }}</span></div>
                                    <div class="flex justify-between text-xs border-t pt-1.5"><span class="text-gray-600 font-semibold">Average Grade:</span><span class="font-bold text-indigo-600">{{ analytics.assignments_summary.average_grade_percentage }}</span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white/95 backdrop-blur-sm rounded-[24px] p-6 md:p-8 shadow-2xl border border-blue-900/10 space-y-6">
                            
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b pb-4 gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-tr from-purple-600 to-blue-600 text-white rounded-xl flex items-center justify-center shadow-md animate-pulse">
                                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-black text-lg text-blue-950">Gemini LLM Cognitive Summary</h3>
                                        <p class="text-[11px] text-gray-400">Automated machine learning insight generation report</p>
                                    </div>
                                </div>
                                <span class="bg-blue-600 text-white font-black text-xs px-3 py-1.5 rounded-full uppercase tracking-wider shadow-sm">
                                    Standing: {{ ai_report.standing }}
                                </span>
                            </div>

                            <div class="space-y-2">
                                <h4 class="text-xs font-black text-blue-900 uppercase tracking-widest">Executive Diagnostics Summary</h4>
                                <p class="text-gray-700 text-sm leading-relaxed bg-blue-50/50 border border-blue-100 p-4 rounded-xl font-medium">
                                    {{ ai_report.summary }}
                                </p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                                <div class="p-4 bg-emerald-50/40 border border-emerald-200/60 rounded-xl space-y-2">
                                    <h5 class="text-xs font-black text-emerald-800 uppercase tracking-widest flex items-center gap-1">
                                        <i class="fa-solid fa-circle-check text-emerald-600"></i> Identified Strengths
                                    </h5>
                                    <ul class="space-y-1.5">
                                        <li v-for="str in ai_report.strengths" :key="str" class="text-xs text-slate-700 font-semibold list-disc list-inside">
                                            {{ str }}
                                        </li>
                                    </ul>
                                </div>

                                <div class="p-4 bg-amber-50/40 border border-amber-200/60 rounded-xl space-y-2">
                                    <h5 class="text-xs font-black text-amber-800 uppercase tracking-widest flex items-center gap-1">
                                        <i class="fa-solid fa-triangle-exclamation text-amber-600"></i> Areas of Concern
                                    </h5>
                                    <ul class="space-y-1.5">
                                        <li v-for="weak in ai_report.weaknesses" :key="weak" class="text-xs text-slate-700 font-semibold list-disc list-inside">
                                            {{ weak }}
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="space-y-3 pt-2">
                                <h4 class="text-xs font-black text-purple-900 uppercase tracking-widest">AI Prescriptive Action Recommendations</h4>
                                <div class="space-y-2">
                                    <div v-for="(rec, index) in ai_report.recommendations" :key="index" class="flex gap-3 items-start bg-slate-50 p-3 rounded-xl border">
                                        <span class="w-5 h-5 bg-purple-100 text-purple-600 text-xs font-black rounded-full flex items-center justify-center shrink-0 mt-0.5">
                                            {{ index + 1 }}
                                        </span>
                                        <p class="text-xs text-slate-700 font-medium leading-relaxed">{{ rec }}</p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </main>
        </AdminLayout>

    </div>
</template>