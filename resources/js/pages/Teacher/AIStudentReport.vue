<script setup>
import { Head, Link } from '@inertiajs/vue3';

// 🛡️ TUKUYIN ANG MGA EKSAKTONG PROPS NA GALING SA TEACHER CONTROLLER MO PRE
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
    <Head :title="student?.name + ' - AI Performance Report'" />

    <div class="min-h-screen bg-gradient-to-br from-[#0c1a3a] via-[#1e3a8a] via-[#1e40af] via-[#1d4ed8] to-[#3730a3] text-gray-900 font-sans p-6 md:p-10 pb-12 print:hidden">
        
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <div>
                <Link :href="route('teacher.lessons.name')" class="text-white/70 hover:text-white text-xs font-bold flex items-center gap-1 transition mb-2">
                    <i class="fa-solid fa-arrow-left"></i> Back to Instructor Dashboard
                </Link>
                <h1 class="text-2xl md:text-3xl font-black text-white tracking-tight">
                    <i class="fa-solid fa-brain text-purple-400"></i> Student Performance Insights
                </h1>
                <p class="text-xs text-white/60 mt-0.5">Automated deep diagnostic processing for classroom evaluation control.</p>
            </div>

            <button 
                @click="triggerPrint" 
                class="bg-gradient-to-r from-emerald-500 to-teal-500 text-white px-5 py-2.5 rounded-xl text-xs font-black shadow-lg hover:from-emerald-600 transition active:scale-95 flex items-center gap-1.5"
            >
                <i class="fa-solid fa-print"></i> Print Academic Report
            </button>
        </div>

        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <div class="lg:col-span-5 space-y-6">
                <section class="bg-white p-6 md:p-8 rounded-[24px] shadow-xl border border-gray-100 space-y-6">
                    <div>
                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 uppercase tracking-wider">Target Profile</span>
                        <h2 class="text-xl font-black text-blue-950 mt-2">{{ student?.name || 'Unknown Student' }}</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Student Account Control Code: #{{ student?.id }}</p>
                    </div>

                    <div class="border-t border-gray-100 pt-4 space-y-2">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">Subject Reference:</span>
                        <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                            <p class="font-bold text-sm text-gray-800">{{ subject?.name || 'N/A' }}</p>
                            <p class="text-[10px] text-gray-400 font-medium mt-0.5">Subject Code: {{ subject?.code || 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-4">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-3">Academic Performance Summary:</span>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="p-4 bg-slate-50 border border-slate-100 rounded-xl">
                                <span class="block text-[10px] font-bold text-slate-500 uppercase">Submissions</span>
                                <span class="text-xl font-black text-slate-900 mt-0.5 block">{{ analytics?.total_submissions ?? 0 }}</span>
                            </div>
                            <div class="p-4 bg-emerald-50/60 border border-emerald-100 rounded-xl">
                                <span class="block text-[10px] font-bold text-emerald-600 uppercase">On-Time</span>
                                <span class="text-xl font-black text-emerald-700 mt-0.5 block">{{ analytics?.on_time_count ?? 0 }}</span>
                            </div>
                            <div class="p-4 bg-blue-50/60 border border-blue-100 rounded-xl">
                                <span class="block text-[10px] font-bold text-blue-600 uppercase">Avg Grade</span>
                                <span class="text-xl font-black text-blue-950 mt-0.5 block">{{ analytics?.average_grade ?? 0 }}%</span>
                            </div>
                            <div class="p-4 bg-purple-50/60 border border-purple-100 rounded-xl">
                                <span class="block text-[10px] font-bold text-purple-600 uppercase">Quiz Attempts</span>
                                <span class="text-xl font-black text-purple-950 mt-0.5 block">{{ analytics?.quiz_attempts_count ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="lg:col-span-7">
                <section class="bg-white p-6 md:p-8 rounded-[24px] shadow-xl border border-gray-100 space-y-6">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                        <div>
                            <h2 class="text-lg font-black text-blue-950 flex items-center gap-2">
                                <i class="fa-solid fa-wand-magic-sparkles text-purple-600"></i> AI Cognitive Evaluation
                            </h2>
                            <p class="text-xs text-gray-400 mt-0.5">Real-time learning diagnostics compiled by Gemini AI core.</p>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Estimated Academic Standing:</span>
                        <div class="px-3 py-2 bg-indigo-50 border border-indigo-100 rounded-xl text-xs font-black text-indigo-950">
                            ✨ {{ ai_report?.standing || 'Evaluation Pending' }}
                        </div>
                    </div>

                    <div class="space-y-1">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Executive Diagnostic Summary:</span>
                        <p class="text-xs md:text-sm text-gray-700 leading-relaxed text-justify bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                            {{ ai_report?.summary || 'No analytics generated for this active profile parameter set.' }}
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- <div class="p-4 bg-emerald-50/30 border border-emerald-100 rounded-xl space-y-2">
                            <h4 class="text-xs font-black text-emerald-800 uppercase flex items-center gap-1">
                                <i class="fa-solid fa-circle-check"></i> Competencies / Strengths
                            </h4>
                            <ul v-if="Array.isArray(ai_report?.strengts)" class="list-disc list-inside text-xs text-gray-700 space-y-1 pl-1">
                                <li v-for="(strength, idx) in ai_report?.strengths" :key="idx">{{ strength }}</li>
                                <li v-if="!ai_report?.strengths?.length" class="italic text-gray-400 list-none">None mapped.</li>
                            </ul>
                        </div> -->
                        <div class="p-4 bg-emerald-50/30 border border-emerald-100 rounded-xl space-y-2">
                            <h4 class="text-xs font-black text-emerald-800 uppercase flex items-center gap-1">
                                <i class="fa-solid fa-circle-check"></i> Competencies / Strengths
                            </h4>
                            <ul v-if="Array.isArray(ai_report?.strengths)" class="list-disc list-inside text-xs text-gray-700 space-y-1 pl-1">
                                <li v-for="(strength, idx) in ai_report.strengths" :key="idx">{{ strength }}</li>
                            </ul>
                            <p v-else class="text-xs text-gray-700 leading-relaxed text-justify">
                                {{ ai_report?.strengths || 'None mapped.' }}
                            </p>
                        </div>
                        <!-- <div class="p-4 bg-red-50/30 border border-red-100 rounded-xl space-y-2">
                            <h4 class="text-xs font-black text-red-800 uppercase flex items-center gap-1">
                                <i class="fa-solid fa-triangle-exclamation"></i> Areas for Growth
                            </h4>
                            <ul v-if="Array.isArray(ai_report?.weaknesses)" class="list-disc list-inside text-xs text-gray-700 space-y-1 pl-1">
                                <li v-for="(weakness, idx) in ai_report?.weaknesses" :key="idx">{{ weakness }}</li>
                                <li v-if="!ai_report?.weaknesses?.length" class="italic text-gray-400 list-none">None mapped.</li>
                            </ul>
                        </div> -->
                        <div class="p-4 bg-red-50/30 border border-red-100 rounded-xl space-y-2">
                            <h4 class="text-xs font-black text-red-800 uppercase flex items-center gap-1">
                                <i class="fa-solid fa-triangle-exclamation"></i> Areas for Growth
                            </h4>
                            <ul v-if="Array.isArray(ai_report?.weaknesses)" class="list-disc list-inside text-xs text-gray-700 space-y-1 pl-1">
                                <li v-for="(weakness, idx) in ai_report.weaknesses" :key="idx">{{ weakness }}</li>
                            </ul>
                            <p v-else class="text-xs text-gray-700 leading-relaxed text-justify">
                                {{ ai_report?.weaknesses || 'None mapped.' }}
                            </p>
                        </div>
                    </div>

                    <!-- <div class="space-y-2">
                        <h4 class="text-xs font-black text-gray-800 uppercase flex items-center gap-1">
                            <i class="fa-solid fa-lightbulb text-amber-500"></i> Instructional Recommendations
                        </h4>
                        <ol v-if="Array.isArray(ai_report?.recommendations)" class="list-decimal list-inside text-xs text-gray-700 space-y-1.5 pl-1 bg-amber-50/20 p-4 rounded-xl border border-amber-100">
                            <li v-for="(rec, idx) in ai_report?.recommendations" :key="idx" class="text-justify leading-relaxed">{{ rec }}</li>
                            <li v-if="!ai_report?.recommendations?.length" class="italic text-gray-400 list-none">No urgent strategies flagged.</li>
                        </ol>
                    </div> -->
                    <div class="space-y-2">
                        <h4 class="text-xs font-black text-gray-800 uppercase flex items-center gap-1">
                            <i class="fa-solid fa-lightbulb text-amber-500"></i> Instructional Recommendations
                        </h4>
                        <ol v-if="Array.isArray(ai_report?.recommendations)" class="list-decimal list-inside text-xs text-gray-700 space-y-1.5 pl-1 bg-amber-50/20 p-4 rounded-xl border border-amber-100">
                            <li v-for="(rec, idx) in ai_report.recommendations" :key="idx" class="text-justify leading-relaxed">{{ rec }}</li>
                        </ol>
                        <p v-else class="text-xs text-gray-700 leading-relaxed bg-amber-50/20 p-4 rounded-xl border border-amber-100 text-justify">
                            {{ ai_report?.recommendations || 'No urgent strategies flagged.' }}
                        </p>
                    </div>
                </section>
            </div>

        </div>
    </div>

    <div class="hidden print:block text-slate-900 p-6 font-sans bg-white text-xs leading-relaxed print:break-after-page">
        <div class="text-center border-b-4 border-double border-slate-800 pb-3 mb-6">
            <h1 class="text-lg font-black uppercase tracking-wider text-slate-950">CAMPUS INSTRUCTOR CONTROL NETWORK</h1>
            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-0.5">Automated Student Diagnostics & Coursework Evaluation Dossier</p>
        </div>

        <div class="grid grid-cols-2 gap-4 border border-slate-300 bg-slate-50 p-4 rounded-xl mb-6">
            <div>
                <p class="text-[10px] font-bold text-slate-500 uppercase">Student Evaluated:</p>
                <p class="text-sm font-black text-slate-950">{{ student?.name || 'N/A' }}</p>
                <p class="text-[11px] text-slate-600">Student ID Link: #{{ student?.id }}</p>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-500 uppercase">Subject Cluster Parameters:</p>
                <p class="text-sm font-black text-slate-950">{{ subject?.name || 'N/A' }}</p>
                <p class="text-[11px] text-slate-600">Course Identifier: {{ subject?.code || 'N/A' }}</p>
            </div>
        </div>

        <div class="mb-6">
            <h3 class="text-[11px] font-black uppercase text-slate-800 border-b border-slate-300 pb-1 mb-2">I. Quantitative Performance Summary</h3>
            <div class="grid grid-cols-4 gap-2 text-center">
                <div class="border border-slate-200 p-2 rounded-lg">
                    <span class="block text-[9px] font-bold text-slate-500 uppercase">Total Submissions</span>
                    <span class="text-base font-black text-slate-950">{{ analytics?.total_submissions ?? 0 }}</span>
                </div>
                <div class="border border-slate-200 p-2 rounded-lg">
                    <span class="block text-[9px] font-bold text-slate-500 uppercase">On-Time Submissions</span>
                    <span class="text-base font-black text-emerald-700">{{ analytics?.on_time_count ?? 0 }}</span>
                </div>
                <div class="border border-slate-200 p-2 rounded-lg">
                    <span class="block text-[9px] font-bold text-slate-500 uppercase">Average Grade</span>
                    <span class="text-base font-black text-blue-800">{{ analytics?.average_grade ?? 0 }}%</span>
                </div>
                <div class="border border-slate-200 p-2 rounded-lg">
                    <span class="block text-[9px] font-bold text-slate-500 uppercase">Quiz Assessment Logs</span>
                    <span class="text-base font-black text-slate-950">{{ analytics?.quiz_attempts_count ?? 0 }}</span>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <h3 class="text-[11px] font-black uppercase text-slate-800 border-b border-slate-300 pb-1">II. AI Generative Narrative Analysis</h3>
            <div>
                <h4 class="text-[10px] font-bold text-slate-700 uppercase">[Academic Standing]</h4>
                <p class="text-xs font-black text-indigo-900 mt-0.5 ml-1">👉 {{ ai_report?.standing || 'N/A' }}</p>
            </div>
            <div>
                <h4 class="text-[10px] font-bold text-slate-700 uppercase">[Diagnostic Evaluation Narrative]</h4>
                <p class="text-xs text-slate-800 mt-1 ml-1 text-justify indent-4">{{ ai_report?.summary || 'N/A' }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4 pt-2">
                <!-- <div>
                    <h4 class="text-[10px] font-bold text-emerald-800 uppercase">[Mapped Strengths]</h4>
                    <ul v-if="Array.isArray(ai_report?.strengths)" class="list-disc list-inside text-xs text-slate-700 mt-1 ml-1 space-y-0.5">
                        <li v-for="(strength, idx) in ai_report?.strengths" :key="idx">{{ strength }}</li>
                    </ul>
                </div> -->
                <div class="p-4 bg-emerald-50/30 border border-emerald-100 rounded-xl space-y-2">
                    <h4 class="text-xs font-black text-emerald-800 uppercase flex items-center gap-1">
                        <i class="fa-solid fa-circle-check"></i> Competencies / Strengths
                    </h4>
                    <ul v-if="Array.isArray(ai_report?.strengths)" class="list-disc list-inside text-xs text-gray-700 space-y-1 pl-1">
                        <li v-for="(strength, idx) in ai_report.strengths" :key="idx">{{ strength }}</li>
                    </ul>
                    <p v-else class="text-xs text-gray-700 leading-relaxed text-justify">
                        {{ ai_report?.strengths || 'None mapped.' }}
                    </p>
                </div>
                <!-- <div>
                    <h4 class="text-[10px] font-bold text-red-800 uppercase">[Mapped Vulnerabilities]</h4>
                    <ul v-if="Array.isArray(ai_report?.weaknesses)" class="list-disc list-inside text-xs text-slate-700 mt-1 ml-1 space-y-0.5">
                        <li v-for="(weakness, idx) in ai_report?.weaknesses" :key="idx">{{ weakness }}</li>
                    </ul>
                </div> -->
                <div class="p-4 bg-red-50/30 border border-red-100 rounded-xl space-y-2">
                    <h4 class="text-xs font-black text-red-800 uppercase flex items-center gap-1">
                        <i class="fa-solid fa-triangle-exclamation"></i> Areas for Growth
                    </h4>
                    <ul v-if="Array.isArray(ai_report?.weaknesses)" class="list-disc list-inside text-xs text-gray-700 space-y-1 pl-1">
                        <li v-for="(weakness, idx) in ai_report.weaknesses" :key="idx">{{ weakness }}</li>
                    </ul>
                    <p v-else class="text-xs text-gray-700 leading-relaxed text-justify">
                        {{ ai_report?.weaknesses || 'None mapped.' }}
                    </p>
                </div>
            </div>

            <!-- <div class="pt-2">
                <h4 class="text-[10px] font-bold text-slate-700 uppercase">[Strategic Recommendations & Interventions]</h4>
                <ul v-if="Array.isArray(ai_report?.recommendations)" class="list-decimal list-inside text-xs text-slate-800 mt-1 ml-1 space-y-1">
                    <li v-for="(rec, idx) in ai_report?.recommendations" :key="idx" class="text-justify">{{ rec }}</li>
                </ul>
            </div> -->
            <div class="space-y-2">
                <h4 class="text-xs font-black text-gray-800 uppercase flex items-center gap-1">
                    <i class="fa-solid fa-lightbulb text-amber-500"></i> Instructional Recommendations
                </h4>
                <ol v-if="Array.isArray(ai_report?.recommendations)" class="list-decimal list-inside text-xs text-gray-700 space-y-1.5 pl-1 bg-amber-50/20 p-4 rounded-xl border border-amber-100">
                    <li v-for="(rec, idx) in ai_report.recommendations" :key="idx" class="text-justify leading-relaxed">{{ rec }}</li>
                </ol>
                <p v-else class="text-xs text-gray-700 leading-relaxed bg-amber-50/20 p-4 rounded-xl border border-amber-100 text-justify">
                    {{ ai_report?.recommendations || 'No urgent strategies flagged.' }}
                </p>
            </div>
        </div>

        <div class="mt-12 grid grid-cols-2 gap-12 text-center pt-8">
            <div>
                <div class="w-40 mx-auto border-b border-slate-400 h-4"></div>
                <p class="text-[9px] font-bold uppercase text-slate-500 mt-1">Instructor / Class Adviser Signature</p>
                <p class="text-[8px] text-slate-400">Review Date: {{ new Date().toLocaleDateString() }}</p>
            </div>
            <div>
                <div class="w-40 mx-auto border-b border-slate-400 h-4"></div>
                <p class="text-[9px] font-bold uppercase text-slate-500 mt-1">Institutional Automated Validation</p>
                <p class="text-[8px] text-slate-400">System Log Packet Authenticated</p>
            </div>
        </div>
    </div>
</template>