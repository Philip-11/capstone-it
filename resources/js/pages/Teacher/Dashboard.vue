<script setup>
import { useForm, Head, router, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    lessons: Array,
    subjects: Array,
    quizAttempts: Array,
});

const isEditing = ref(false);
const currentLessonId = ref(null);
const fileInput = ref(null);

const form = useForm({
    title: '',
    content: '',
    subject_id: '',
    document: null,
});

const onFileChange = (e) => {
    form.document = e.target.files[0];
};

const submit = () => {
    if (isEditing.value){
        form.transform((data) => ({
            ...data,
            _method: 'PUT',
        })).post(route('teacher.lessons.update', currentLessonId.value), {
            forceFormData: true,
            onSuccess: () => {
                cancelEdit();
                if (fileInput.value) fileInput.value.value = null;
            }
        });
    } else {
        form.post(route('teacher.lessons.store'), {
            forceFormData: true,
            onSuccess: () => {
                form.reset(); 
                if (fileInput.value) fileInput.value.value = null;
            }
        });
    }
};

const startEdit = (lesson) => {
    isEditing.value = true;
    currentLessonId.value = lesson.id;
    form.title = lesson.title;
    form.content = lesson.content;
    form.subject_id = lesson.subject_id || '';
};

const cancelEdit = () => {
    isEditing.value = false;
    currentLessonId.value = null;
    form.reset();
};

const deleteLesson = (lesson) => {
    if (confirm(`Are you sure you want to delete "${lesson.title}"?`)){
        router.delete(route('teacher.lessons.destroy', lesson.id));
    }
};

// Placeholder action para sa AI feature mo
const processAI = (id) => {
    alert(`Processing AI Summary for Lesson ID: ${id}`);
};
</script>

<template>
    <Head title="Teacher Dashboard" />

    <div class="min-h-screen bg-gradient-to-br from-[#0c1a3a] via-[#1e3a8a] via-[#1e40af] to-[#3730a3] text-gray-900 font-sans pb-16">
        
        <header class="h-[66px] bg-white border-b-3 border-blue-600 px-[5%] md:px-[9%] flex items-center justify-between shadow-lg sticky top-0 z-50">
            <div class="flex items-center gap-2.5">
                <img src="/LOGO.png" alt="School Logo" class="w-[30px] h-[30px] object-contain" />
                <h2 class="text-xl font-bold text-blue-900 tracking-tight">GLMS <span class="text-xs font-semibold bg-blue-100 text-blue-700 px-2 py-0.5 rounded ml-1">Faculty</span></h2>
            </div>

            <nav class="hidden md:flex items-center gap-[34px]">
                <a href="#" class="text-blue-600 font-bold text-sm py-6 relative after:content-[''] after:absolute after:left-0 after:bottom-[-3px] after:w-full after:h-[3px] after:bg-blue-600 after:rounded-t">Lessons & Hub</a>
                <a href="#" class="text-gray-600 hover:text-blue-600 text-sm font-semibold transition">Classes</a>
                <a href="#" class="text-gray-600 hover:text-blue-600 text-sm font-semibold transition">Academic Reports</a>
            </nav>

            <Link method="post" href="/logout" as="button" class="bg-gradient-to-r from-blue-800 to-blue-600 text-white px-4 py-2.5 rounded-lg text-xs font-semibold flex items-center gap-2 transition hover:from-red-600 hover:to-red-700 shadow-md">
                Logout Dashboard
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
            </Link>
        </header>

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
                    <a :href="route('teacher.quizzes.index')" class="flex-1 sm:flex-initial text-center bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 px-5 py-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-paste"></i> Quizzes
                    </a>
                    <a :href="route('teacher.assignments.index')" class="flex-1 sm:flex-initial text-center bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 px-5 py-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-briefcase"></i> Activities
                    </a>
                </div>
            </section>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                <section class="bg-white p-6 rounded-[22px] shadow-xl border border-gray-100 lg:col-span-5 space-y-4">
                    <div class="border-b border-gray-100 pb-3">
                        <h3 class="font-bold text-blue-950 text-lg flex items-center gap-2">
                            <i class="fa-solid" :class="isEditing ? 'fa-pen-to-square text-amber-500' : 'fa-folder-plus text-blue-600'"></i>
                            {{ isEditing ? 'Update Curated Lesson' : 'Create New Core Lesson' }}
                        </h3>
                    </div>

                    <form @submit.prevent="submit" class="space-y-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-600">Lesson Header Title</label>
                            <input v-model="form.title" type="text" placeholder="e.g., Intro to Advanced Relational SQL" class="w-full border border-gray-200 p-3 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 transition" />
                            <div v-if="form.errors.title" class="text-red-500 text-xs font-medium">{{ form.errors.title }}</div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-600">Module Outline / Lectures Text</label>
                            <textarea v-model="form.content" rows="6" placeholder="Construct core concepts or copy paste course summaries here..." class="w-full border border-gray-200 p-3 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 transition"></textarea>
                            <div v-if="form.errors.content" class="text-red-500 text-xs font-medium">{{ form.errors.content }}</div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-600">Deploy Destination Subject</label>
                            <select v-model="form.subject_id" class="w-full border border-gray-200 p-3 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 bg-white transition">
                                <option value="" disabled>-- Select Target Core Subject --</option>
                                <option v-for="subject in subjects" :key="subject.id" :value="subject.id">
                                    {{ subject.name }} ({{ subject.subject_code }})
                                </option>
                            </select>
                            <div v-if="form.errors.subject_id" class="text-red-500 text-xs font-medium mt-1">{{ form.errors.subject_id }}</div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-600">Attached Documents Support (PDF, PPT, PPTX)</label>
                            <input type="file" ref="fileInput" @change="onFileChange" accept=".pdf,.ppt,.pptx" class="w-full text-xs text-gray-500 bg-gray-50 border border-gray-200 p-2.5 rounded-xl file:mr-4 file:py-1.5 file:px-3.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer" />
                            
                            <div v-if="form.progress" class="w-full bg-gray-100 rounded-full h-2 mt-2">
                                <div class="bg-blue-600 h-2 rounded-full transition-all" :style="{ width: form.progress.percentage + '%' }"></div>
                            </div>
                            <div v-if="form.errors.document" class="text-red-500 text-xs font-medium">{{ form.errors.document }}</div>
                        </div>

                        <div class="flex items-center gap-2 pt-2 border-t border-gray-50">
                            <button type="submit" :disabled="form.processing" class="flex-1 text-white font-bold px-4 py-3 rounded-xl text-xs shadow-md transition duration-150" :class="isEditing ? 'bg-amber-500 hover:bg-amber-600' : 'bg-blue-600 hover:bg-blue-700'">
                                <i class="fa-solid fa-save mr-1"></i> {{ isEditing ? 'Update Lesson Block' : 'Deploy Live Lesson' }}
                            </button>
                            <button v-if="isEditing" type="button" @click="cancelEdit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold px-4 py-3 rounded-xl text-xs transition">
                                Abort
                            </button>
                        </div>
                    </form>
                </section>

                <section class="bg-white p-6 rounded-[22px] shadow-xl border border-gray-100 lg:col-span-7 space-y-4">
                    <div class="border-b border-gray-100 pb-3">
                        <h3 class="font-bold text-blue-950 text-lg flex items-center gap-2">
                            <i class="fa-solid fa-layer-group text-blue-600"></i> Active Course Materials Catalog
                        </h3>
                    </div>

                    <div class="max-h-[515px] overflow-y-auto space-y-3 pr-1.5">
                        <div v-for="lesson in lessons" :key="lesson.id" class="p-4 border border-gray-100 hover:border-blue-200 rounded-xl bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition">
                            <div class="space-y-1">
                                <div class="flex items-center flex-wrap gap-2">
                                    <h4 class="font-bold text-sm text-gray-900 leading-tight">{{ lesson.title }}</h4>
                                    <span v-if="lesson.quiz" class="text-[10px] font-bold bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded border border-emerald-100 flex items-center gap-0.5">
                                        <i class="fa-solid fa-circle-nodes"></i> Linked Quiz
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 max-w-md line-clamp-1">{{ lesson.content || 'No text summary specified.' }}</p>
                                
                                <div v-if="lesson.file_path" class="pt-1.5 flex items-center gap-2 flex-wrap">
                                    <span class="text-[11px] font-medium text-blue-950 bg-blue-50/70 border border-blue-100 rounded-lg px-2 py-1 max-w-xs truncate block">
                                        📁 {{ lesson.file_name || 'Attached Asset File' }}
                                    </span>
                                    <button @click="processAI(lesson.id)" class="bg-purple-100 text-purple-700 text-[10px] font-bold px-2 py-1 rounded-lg border border-purple-200 flex items-center gap-1 hover:bg-purple-200 transition">
                                        <i class="fa-solid fa-wand-magic-sparkles"></i> AI Summary
                                    </button>
                                </div>
                            </div>

                            <div class="flex items-center gap-1.5 self-end sm:self-center">
                                <a v-if="lesson.file_path" :href="route('teacher.lessons.download', lesson.id)" target="_blank" class="p-2 bg-gray-100 text-gray-700 rounded-lg text-xs font-semibold hover:bg-blue-50 hover:text-blue-600 transition" title="Download Asset">
                                    <i class="fa-solid fa-download"></i>
                                </a>
                                <button @click="startEdit(lesson)" class="p-2 bg-gray-100 text-amber-600 rounded-lg text-xs font-semibold hover:bg-amber-50 transition" title="Modify Lesson Data">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button @click="deleteLesson(lesson)" class="p-2 bg-gray-100 text-red-600 rounded-lg text-xs font-semibold hover:bg-red-50 transition" title="Purge Record">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </div>

                        <div v-if="lessons.length === 0" class="text-center py-12 text-gray-400 italic text-sm">
                            No course content materials deployed to your portal registry database.
                        </div>
                    </div>
                </section>
            </div>

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