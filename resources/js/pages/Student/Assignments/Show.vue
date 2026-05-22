<script setup>
import { useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    assignment: Object,
    submission: Object // Pwedeng may laman na kung nag-pasa na siya dati
});

const form = useForm({
    submission_text: props.submission ? props.submission.submission_text : '',
    file: null,
    old_file_path: props.submission ? props.submission.file_path : null
});

const handleFileUpload = (e) => {
    form.file = e.target.files[0];
};

const submitAssignment = () => {
    form.post(route('student.assignments.submit', props.assignment.id), {
        onSuccess: () => alert('Activity submitted successfully!')
    });
};
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-[#0c1a3a] via-[#1e3a8a] via-[#1e40af] via-[#1d4ed8] to-[#3730a3] text-gray-900 font-sans pb-12">
        
        <header class="h-[66px] bg-white border-b-3 border-blue-600 px-[5%] md:px-[9%] flex items-center justify-between shadow-lg sticky top-0 z-50">
            <div class="flex items-center gap-2.5">
                <img src="/LOGO.png" alt="School Logo" class="w-[30px] h-[30px] object-contain" />
                <h2 class="text-xl font-bold text-blue-900">GLMS</h2>
            </div>

            <nav class="hidden md:flex items-center gap-[34px]">
                <Link :href="route('student.dashboard')" class="text-gray-600 hover:text-blue-600 text-sm font-semibold transition">Dashboard</Link>
                <a href="#" class="text-gray-600 hover:text-blue-600 text-sm font-semibold transition">My Courses</a>
                <Link :href="route('student.dashboard')" class="text-blue-600 font-bold text-sm py-6 relative after:content-[''] after:absolute after:left-0 after:bottom-[-3px] after:w-full after:h-[3px] after:bg-blue-600 after:rounded-t">Assignments</Link>
                <a href="#" class="text-gray-600 hover:text-blue-600 text-sm font-semibold transition">Profile</a>
            </nav>

            <Link method="post" href="/logout" as="button" class="bg-gradient-to-r from-blue-800 to-blue-600 text-white px-4 py-2.5 rounded-lg text-xs font-semibold flex items-center gap-2 transition hover:bg-red-600 shadow-md">
                Logout
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
            </Link>
        </header>

        <div class="fixed right-6 bottom-6 w-[58px] h-[58px] rounded-full bg-gradient-to-br from-blue-800 to-blue-600 text-white flex items-center justify-center text-xl shadow-2xl cursor-pointer z-50 hover:from-blue-900 hover:to-blue-800 transition">
            <i class="fa-solid fa-robot"></i>
        </div>

        <main class="max-w-4xl mx-auto px-4 md:px-0 pt-11 space-y-6">
            
            <div>
                <Link :href="route('student.dashboard')" class="inline-flex items-center gap-2 text-white/90 bg-white/10 hover:bg-white/20 px-4 py-2 rounded-xl text-sm font-semibold backdrop-blur-sm transition">
                    <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
                </Link>
            </div>

            <section class="bg-white rounded-[24px] p-6 md:p-8 shadow-xl space-y-5">
                <div class="flex flex-col md:flex-row justify-between items-start gap-4 border-b border-gray-100 pb-5">
                    <div class="space-y-1">
                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold tracking-wide">Assignment Activity</span>
                        <h1 class="text-2xl md:text-3xl font-bold text-blue-950 mt-2">{{ assignment.title }}</h1>
                    </div>
                    
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-blue-50 text-blue-700 px-[12px] py-[7px] rounded-xl text-xs font-bold flex items-center gap-1.5 border border-blue-100">
                            <i class="fa-solid fa-star"></i> {{ assignment.max_points }} Max XP
                        </span>
                        <span v-if="assignment.due_date" class="bg-red-50 text-red-600 px-[12px] py-[7px] rounded-xl text-xs font-bold flex items-center gap-1.5 border border-red-100">
                            <i class="fa-solid fa-clock"></i> Due: {{ new Date(assignment.due_date).toLocaleString([], {month: 'short', day: 'numeric', hour: '2-digit', minute:'2-digit'}) }}
                        </span>
                    </div>
                </div>

                <div class="text-gray-700 leading-relaxed">
                    <h3 class="font-bold text-gray-900 text-base mb-2 flex items-center gap-2">
                        <i class="fa-solid fa-circle-info text-blue-600 text-sm"></i>
                        Instructions & Guidelines:
                    </h3>
                    <div class="bg-gray-50/80 p-4 rounded-xl border border-gray-100 whitespace-pre-wrap text-sm md:text-base">
                        {{ assignment.instructions || 'No special instructions provided by the instructor.' }}
                    </div>
                </div>
            </section>

            <section v-if="submission && submission.grade !== null" class="bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 p-5 rounded-[18px] shadow-sm flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-lg shadow-sm">
                    <i class="fa-solid fa-award"></i>
                </div>
                <div>
                    <h3 class="font-bold text-emerald-900 text-lg">Grade Released: {{ submission.grade }} / {{ assignment.max_points }}</h3>
                    <p v-if="submission.teacher_feedback" class="text-sm text-emerald-800 mt-1 bg-white/60 p-3 rounded-lg border border-emerald-100/50">
                        <strong class="text-emerald-950">Instructor Feedback:</strong> {{ submission.teacher_feedback }}
                    </p>
                </div>
            </section>

            <section class="bg-white rounded-[24px] p-6 md:p-8 shadow-xl">
                <h2 class="text-xl font-bold text-blue-950 mb-5 flex items-center gap-2">
                    <i class="fa-solid fa-cloud-arrow-up text-blue-600 text-base"></i>
                    Your Submission Workspace
                </h2>

                <form @submit.prevent="submitAssignment" class="space-y-5">
                    <div class="space-y-1.5">
                        <label class="block text-sm font-bold text-gray-700">Text Response / Notes (Optional)</label>
                        <textarea v-model="form.submission_text" rows="5" placeholder="Type your online response, answers, or paste your public repository/code link here..." class="w-full border border-gray-200 p-3.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm md:text-base transition"></textarea>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">Attach File Module (PDF, DOCX, ZIP, Images)</label>
                        <div class="border-2 border-dashed border-gray-200 hover:border-blue-500 rounded-xl p-5 bg-gray-50/50 transition relative flex flex-col items-center justify-center text-center cursor-pointer">
                            <input type="file" @change="handleFileUpload" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
                            <i class="fa-solid fa-file-arrow-up text-gray-400 text-2xl mb-2"></i>
                            <span class="text-sm font-semibold text-gray-600">
                                {{ form.file ? form.file.name : 'Click to browse files or drag and drop here' }}
                            </span>
                            <span class="text-xs text-gray-400 mt-0.5">Maximum file upload threshold: 15MB</span>
                        </div>
                        
                        <div v-if="submission && submission.file_path" class="bg-blue-50/60 text-blue-800 p-3 rounded-xl text-xs font-semibold flex items-center gap-2 border border-blue-100">
                            <i class="fa-solid fa-folder-keep"></i>
                            <span>You have already uploaded a file. Uploading a new one will replace it.</span>
                        </div>
                    </div>

                    <div class="flex justify-end pt-2 border-t border-gray-100">
                        <button type="submit" :disabled="form.processing" class="bg-gradient-to-br from-blue-800 to-blue-600 hover:from-blue-900 hover:to-blue-800 text-white px-6 py-3 rounded-xl font-bold text-sm shadow-md transition duration-200 hover:-translate-y-0.5 disabled:opacity-50 active:scale-95 w-full sm:w-auto">
                            <i class="fa-solid fa-paper-plane mr-1.5 text-xs"></i>
                            {{ submission ? 'Resubmit Activity Assignment' : 'Submit Assignment Task' }}
                        </button>
                    </div>
                </form>
            </section>

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