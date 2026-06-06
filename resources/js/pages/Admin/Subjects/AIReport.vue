<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';

const props = defineProps({
    subject: Object,
    student: Object,
    analytics: Object,
    ai_report: Object
});

const triggerPrint = () => {
    window.print();
};
</script>

<template>
    <Head :title="'AI Analytics - ' + student.name" />

    <div class="min-h-screen bg-gradient-to-br from-[#0c1a3a] via-[#1e3a8a] via-[#1e40af] via-[#1d4ed8] to-[#3730a3] text-gray-900 font-sans pb-12 print:hidden">
        
        <AdminLayout>
            <main class="max-w-6xl mx-auto px-4 md:px-[9%] pt-11 space-y-6">
                
                <div class="flex items-center gap-2 text-white/80 text-sm">
                    <Link :href="route('admin.subjects.manage', subject.id)" class="hover:text-white transition font-semibold">Subject Center</Link>
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                    <span class="text-white font-black">{{ student.name }} Analysis</span>
                </div>

                <div class="flex justify-between items-center bg-white/15 backdrop-blur-md p-4 rounded-2xl border border-white/10 mb-6 print:hidden">
                    <span class="text-white text-xs font-semibold">
                        <i class="fa-solid fa-circle-info text-blue-300 mr-1"></i> Ready for administrative documentation layout.
                    </span>
                    <button 
                        @click="triggerPrint" 
                        class="bg-gradient-to-r from-emerald-600 to-teal-600 text-white px-5 py-2 rounded-xl text-xs font-black hover:from-emerald-700 shadow-lg transition active:scale-95 flex items-center gap-1.5"
                    >
                        <i class="fa-solid fa-print"></i> Print Academic Report
                    </button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                    
                    <div class="lg:col-span-1 space-y-5">
                        <div class="bg-white rounded-[22px] p-6 shadow-xl border border-gray-100 space-y-5 print:shadow-none print:border-slate-300 print:rounded-none">
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
                                    <div class="flex justify-between text-xs"><span class="text-gray-500">On-Time Submits:</span><span class="font-bold text-emerald-600">{{ analytics.assignments_summary.on_time }}</span></div>
                                    <div class="flex justify-between text-xs"><span class="text-gray-500">Late Overdue:</span><span class="font-bold text-red-500">{{ analytics.assignments_summary.late }}</span></div>
                                    <div class="flex justify-between text-xs border-t pt-1.5"><span class="text-gray-600 font-semibold">Average Grade:</span><span class="font-bold text-indigo-600">{{ analytics.assignments_summary.average_grade_percentage }}</span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white/95 backdrop-blur-sm rounded-[24px] p-6 md:p-8 shadow-2xl border border-blue-900/10 space-y-6 print:shadow-none print:border-slate-300 print:rounded-none">
                            
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


    <div class="hidden print:block text-slate-900 p-6 font-sans bg-white text-xs leading-relaxed print:break-after-page">
    
        <div class="text-center border-b-4 border-double border-slate-800 pb-4 mb-6">
            <h1 class="text-xl font-black uppercase tracking-wider text-slate-950">CAMPUS STUDENT ACADEMIC CONTROL CENTER</h1>
            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mt-0.5">Automated AI Diagnostics & Evaluation Dossier</p>
            <p class="text-[10px] text-gray-400 italic mt-1">Generated via Gemini AI Core Implementation Engine</p>
        </div>

        <div class="grid grid-cols-2 gap-4 border border-slate-300 bg-slate-50 p-4 rounded-xl mb-6">
            <div>
                <p class="text-[11px] font-bold text-slate-500 uppercase">Student Evaluated:</p>
                <p class="text-base font-black text-slate-950">{{ student?.name || 'N/A' }}</p>
                <p class="text-xs text-slate-600">Student Academic Account ID: #{{ student?.id }}</p>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-500 uppercase">Subject Parameters:</p>
                <p class="text-base font-black text-slate-950">{{ subject?.name || 'N/A' }}</p>
                <p class="text-xs text-slate-600">Course Code: {{ subject?.code || 'N/A' }}</p>
            </div>
        </div>

        <div class="mb-6">
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-800 border-b border-slate-300 pb-1 mb-3 flex items-center gap-1">
                I. Quantitative Performance Metrics
            </h3>
            <div class="grid grid-cols-4 gap-2 text-center">
                <div class="border border-slate-200 p-2.5 rounded-lg">
                    <span class="block text-[10px] font-bold text-slate-500 uppercase">Coursework Submits</span>
                    <span class="text-lg font-black text-slate-950">{{ analytics.assignments_summary?.total_submitted ?? 0 }}</span>
                </div>
                <div class="border border-slate-200 p-2.5 rounded-lg">
                    <span class="block text-[10px] font-bold text-slate-500 uppercase">On-Time Submissions</span>
                    <span class="text-lg font-black text-emerald-700">{{ analytics.assignments_summary?.on_time ?? 0 }}</span>
                </div>
                <div class="border border-slate-200 p-2.5 rounded-lg">
                    <span class="block text-[10px] font-bold text-slate-500 uppercase">Average Grade Metric</span>
                    <span class="text-lg font-black text-blue-800">{{ analytics.assignments_summary?.average_grade_percentage ?? 0 }}</span>
                </div>
                <div class="border border-slate-200 p-2.5 rounded-lg">
                    <span class="block text-[10px] font-bold text-slate-500 uppercase">Quiz Evaluated Logs</span>
                    <span class="text-lg font-black text-slate-950">{{ analytics.quizzes_summary?.total_quizzes_taken ?? 0 }}</span>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-800 border-b border-slate-300 pb-1 flex items-center gap-1">
                II. AI Generative Cognitive Summary & Diagnostic Assessment
            </h3>
            
            <div>
                <h4 class="text-xs font-bold text-slate-700 uppercase">[Academic Standing Status]</h4>
                <p class="text-sm font-black text-indigo-900 mt-0.5 ml-2">👉 {{ ai_report?.standing || 'No Assessment Logged' }}</p>
            </div>

            <div>
                <h4 class="text-xs font-bold text-slate-700 uppercase">[Executive Narrative Evaluation]</h4>
                <p class="text-xs text-slate-800 mt-1 ml-2 text-justify indent-6">{{ ai_report?.summary || 'N/A' }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4 pt-2">
                <div>
                    <h4 class="text-xs font-bold text-emerald-800 uppercase">[Identified Competencies / Strengths]</h4>
                    <ul class="list-disc list-inside text-xs text-slate-700 mt-1 ml-2 space-y-1">
                        <li v-for="(strength, idx) in ai_report?.strengths" :key="idx">{{ strength }}</li>
                        <li v-if="!ai_report?.strengths?.length" class="italic text-gray-400">None registered.</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-red-800 uppercase">[Areas for Growth / Vulnerabilities]</h4>
                    <ul class="list-disc list-inside text-xs text-slate-700 mt-1 ml-2 space-y-1">
                        <li v-for="(weakness, idx) in ai_report?.weaknesses" :key="idx">{{ weakness }}</li>
                        <li v-if="!ai_report?.weaknesses?.length" class="italic text-gray-400">None registered.</li>
                    </ul>
                </div>
            </div>

            <div class="pt-2">
                <h4 class="text-xs font-bold text-slate-700 uppercase">[Strategic Intervention & Recommendations]</h4>
                <ul class="list-decimal list-inside text-xs text-slate-800 mt-1 ml-2 space-y-1">
                    <li v-for="(rec, idx) in ai_report?.recommendations" :key="idx" class="text-justify">{{ rec }}</li>
                    <li v-if="!ai_report?.recommendations?.length" class="italic text-gray-400">No interventions required.</li>
                </ul>
            </div>
        </div>

        <div class="mt-16 grid grid-cols-2 gap-12 text-center pt-8">
            <div>
                <div class="w-48 mx-auto border-b border-slate-400 h-6"></div>
                <p class="text-[10px] font-bold uppercase text-slate-500 mt-1">Instructor / Evaluator Signature</p>
                <p class="text-[9px] text-slate-400">Date Logged: {{ new Date().toLocaleDateString() }}</p>
            </div>
            <div>
                <div class="w-48 mx-auto border-b border-slate-400 h-6"></div>
                <p class="text-[10px] font-bold uppercase text-slate-500 mt-1">Office of Academic Affairs Certification</p>
                <p class="text-[9px] text-slate-400">System Verification Token: SECURE_AI_LOG_PACKET</p>
            </div>
        </div>
    </div>
</template>