<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';

const props = defineProps({
    subject: Object
});

// ==========================================
// 1. LESSON OVERRIDE STATES & LOGIC
// ==========================================
const showLessonModal = ref(false);
const isEditMode = ref(false);
const currentLessonId = ref(null);

const lessonForm = useForm({
    title: '',
    content: ''
});

const openAddLesson = () => {
    isEditMode.value = false;
    currentLessonId.value = null;
    lessonForm.reset();
    lessonForm.clearErrors();
    showLessonModal.value = true;
};

const openEditLesson = (lesson) => {
    isEditMode.value = true;
    currentLessonId.value = lesson.id;
    lessonForm.clearErrors();
    lessonForm.title = lesson.title;
    lessonForm.content = lesson.content || '';
    showLessonModal.value = true;
};

const submitLesson = () => {
    if (isEditMode.value) {
        lessonForm.put(route('admin.lessons.update', currentLessonId.value), {
            onSuccess: () => showLessonModal.value = false
        });
    } else {
        lessonForm.post(route('admin.lessons.store', props.subject.id), {
            onSuccess: () => showLessonModal.value = false
        });
    }
};

const deleteLesson = (id) => {
    if (confirm('Admin Override Warning: Are you sure you want to permanently delete this lesson? All nested assignments/quizzes will be removed.')) {
        lessonForm.delete(route('admin.lessons.destroy', id));
    }
};

// ==========================================
// 2. ASSIGNMENT OVERRIDE STATES & LOGIC
// ==========================================
const showAssignmentModal = ref(false);
const isAssignmentEdit = ref(false);
const currentAssignmentId = ref(null);
const currentLessonIdForAssignment = ref(null);

const assignmentForm = useForm({
    title: '',
    due_date: ''
});

const openAddAssignment = (lessonId) => {
    isAssignmentEdit.value = false;
    currentLessonIdForAssignment.value = lessonId;
    assignmentForm.reset();
    assignmentForm.clearErrors();
    showAssignmentModal.value = true;
};

const openEditAssignment = (assignment) => {
    isAssignmentEdit.value = true;
    currentAssignmentId.value = assignment.id;
    assignmentForm.clearErrors();
    assignmentForm.title = assignment.title;
    assignmentForm.due_date = assignment.due_date ? assignment.due_date.substring(0, 10) : '';
    showAssignmentModal.value = true;
};

const submitAssignment = () => {
    if (isAssignmentEdit.value) {
        assignmentForm.put(route('admin.assignments.update', currentAssignmentId.value), {
            onSuccess: () => showAssignmentModal.value = false
        });
    } else {
        assignmentForm.post(route('admin.assignments.store', currentLessonIdForAssignment.value), {
            onSuccess: () => showAssignmentModal.value = false
        });
    }
};

const deleteAssignment = (id) => {
    if (confirm('Delete this assignment permanently?')) {
        assignmentForm.delete(route('admin.assignments.destroy', id));
    }
};

// ==========================================
// 3. QUIZ OVERRIDE STATES & LOGIC
// ==========================================
const showQuizModal = ref(false);
const isQuizEdit = ref(false);
const currentLessonIdForQuiz = ref(null);
const currentQuizId = ref(null);

const quizForm = useForm({
    title: '',
    time_limit: ''
});

const openAddQuiz = (lessonId) => {
    isQuizEdit.value = false;
    currentLessonIdForQuiz.value = lessonId;
    quizForm.reset();
    quizForm.clearErrors();
    showQuizModal.value = true;
};

const openEditQuiz = (quiz) => {
    isQuizEdit.value = true;
    currentQuizId.value = quiz.id;
    quizForm.clearErrors();
    quizForm.title = quiz.title;
    quizForm.time_limit = quiz.time_limit || '';
    showQuizModal.value = true;
};

const submitQuiz = () => {
    if (isQuizEdit.value) {
        quizForm.put(route('admin.quizzes.update', currentQuizId.value), {
            onSuccess: () => showQuizModal.value = false
        });
    } else {
        quizForm.post(route('admin.quizzes.store', currentLessonIdForQuiz.value), {
            onSuccess: () => showQuizModal.value = false
        });
    }
};

const deleteQuiz = (id) => {
    if (confirm('Delete this quiz permanently?')) {
        quizForm.delete(route('admin.quizzes.destroy', id));
    }
};
</script>

<template>
    <Head :title="'Manage - ' + subject.name" />

    <div class="min-h-screen bg-gradient-to-br from-[#0c1a3a] via-[#1e3a8a] via-[#1e40af] via-[#1d4ed8] to-[#3730a3] text-gray-900 font-sans pb-12">
        
        <AdminLayout>
            <main class="max-w-7xl mx-auto px-4 md:px-[9%] pt-11 space-y-7">
                
                <div class="flex items-center gap-2 text-white/80 text-sm">
                    <Link :href="route('admin.subjects.index')" class="hover:text-white transition font-semibold">Subject Class Management</Link>
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                    <span class="text-white font-black">{{ subject.name }} Control Center</span>
                </div>

                <section class="bg-gradient-to-br from-blue-800 to-blue-600 text-white p-8 md:p-12 rounded-[24px] flex flex-col md:flex-row justify-between items-center gap-6 shadow-xl">
                    <div class="text-center md:text-left">
                        <span class="bg-white/20 px-3 py-1 rounded-full text-xs tracking-wide">Administrative Coursework Override</span>
                        <h1 class="text-3xl md:text-4xl font-bold my-[18px] text-white">{{ subject.name }}</h1>
                        <p class="max-w-[500px] text-blue-100 text-sm">
                            Instructor: <span class="text-white font-bold">{{ subject.teacher ? subject.teacher.name : 'Unassigned / Vacant' }}</span> &bull; 
                            Course: <span class="text-white font-bold uppercase">{{ subject.course || 'N/A' }}</span> &bull; 
                            Term: <span class="text-white font-bold uppercase">{{ subject.sem || 'N/A' }}</span>
                        </p>
                    </div>
                    
                    <div class="flex flex-col items-center gap-2 shrink-0">
                        <span class="text-xs font-black tracking-widest bg-slate-900/40 text-amber-400 px-4 py-2 rounded-xl border border-white/10 select-all font-mono" title="Click to copy subject key">
                            CODE: {{ subject.subject_code }}
                        </span>
                        <button @click="openAddLesson" class="bg-emerald-600 text-white px-[18px] py-2.5 rounded-xl font-bold hover:bg-emerald-700 transition duration-200 shadow-md flex items-center gap-2 active:scale-95 text-xs">
                            <i class="fa-solid fa-folder-plus"></i> Inject New Lesson
                        </button>
                    </div>
                </section>

                <section class="space-y-4">
                    <div class="flex justify-between items-center text-white">
                        <h3 class="text-xl font-bold flex items-center gap-2">
                            <i class="fa-solid fa-folder-open text-blue-300"></i> Curriculum Structure & Class Logs
                        </h3>
                        <span class="text-xs font-semibold bg-white/10 px-3 py-1 rounded-md border border-white/10">Lessons Loaded: {{ subject.lessons ? subject.lessons.length : 0 }}</span>
                    </div>

                    <div class="space-y-4">
                        <div v-for="lesson in subject.lessons" :key="lesson.id" class="bg-white rounded-[18px] p-6 border-l-[5px] border-blue-600 shadow-md space-y-[18px]">
                            
                            <div class="flex justify-between items-start gap-4">
                                <div class="space-y-1.5 flex-1">
                                    <h4 class="font-bold text-blue-950 text-lg flex items-center gap-2">
                                        <i class="fa-solid fa-book-open text-blue-600/60 text-base"></i> {{ lesson.title }}
                                    </h4>
                                    <p class="text-gray-500 text-sm pr-4 whitespace-pre-line" v-if="lesson.content">{{ lesson.content }}</p>
                                </div>
                                
                                <div class="flex gap-2 shrink-0">
                                    <button @click="openEditLesson(lesson)" class="px-2.5 py-1.5 bg-slate-100 hover:bg-blue-50 hover:text-blue-600 text-gray-600 text-xs font-bold rounded-lg transition flex items-center gap-1">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </button>
                                    <button @click="deleteLesson(lesson.id)" class="px-2.5 py-1.5 bg-slate-50 hover:bg-red-50 hover:text-red-600 text-gray-400 text-xs font-bold rounded-lg transition flex items-center gap-1">
                                        <i class="fa-solid fa-trash-can"></i> Remove
                                    </button>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-100">
                                
                                <div class="p-4 bg-white rounded-xl border border-slate-200/70 flex flex-col justify-between space-y-4 shadow-sm">
                                    <div class="space-y-2.5">
                                        <div class="flex justify-between items-center border-b border-gray-100 pb-2">
                                            <span class="text-sm font-bold text-blue-950 flex items-center gap-1.5">
                                                <i class="fa-solid fa-file-lines text-indigo-500"></i> Assignments Track
                                            </span>
                                            <span class="text-xs font-black bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded-full">
                                                {{ lesson.assignments ? lesson.assignments.length : 0 }}
                                            </span>
                                        </div>
                                        
                                        <div class="space-y-1.5 max-h-[130px] overflow-y-auto pr-1">
                                            <div v-for="assign in lesson.assignments" :key="assign.id" class="flex justify-between items-center bg-slate-50 p-2.5 rounded-xl text-xs border border-slate-200/40 hover:bg-slate-100/70 transition">
                                                <div class="flex flex-col gap-0.5 flex-1 min-w-0">
                                                    <span class="font-bold text-gray-800 line-clamp-1">{{ assign.title }}</span>
                                                    <span v-if="assign.due_date" class="text-[10px] text-gray-400 font-medium"><i class="fa-regular fa-calendar text-[9px]"></i> Due: {{ assign.due_date.substring(0,10) }}</span>
                                                </div>
                                                <div class="flex gap-1 ml-2 shrink-0">
                                                    <button @click="openEditAssignment(assign)" class="text-gray-400 hover:text-blue-600 p-1 hover:bg-blue-50 rounded"><i class="fa-solid fa-pen text-[10px]"></i></button>
                                                    <button @click="deleteAssignment(assign.id)" class="text-gray-400 hover:text-red-600 p-1 hover:bg-red-50 rounded"><i class="fa-solid fa-trash-can text-[10px]"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button @click="openAddAssignment(lesson.id)" class="w-full py-2 border border-dashed border-gray-300 text-gray-400 hover:text-blue-600 hover:border-blue-300 hover:bg-blue-50/30 text-xs font-bold rounded-xl transition">
                                        <i class="fa-solid fa-plus-circle"></i> Inject New Assignment
                                    </button>
                                </div>

                                <div class="p-4 bg-white rounded-xl border border-slate-200/70 flex flex-col justify-between space-y-4 shadow-sm">
                                    <div class="space-y-2.5">
                                        <div class="flex justify-between items-center border-b border-gray-100 pb-2">
                                            <span class="text-sm font-bold text-blue-950 flex items-center gap-1.5">
                                                <i class="fa-solid fa-vial text-purple-500"></i> Examination Quizzes
                                            </span>
                                            <span class="text-xs font-black bg-purple-50 text-purple-600 px-2 py-0.5 rounded-full">
                                                {{ lesson.quizzes ? lesson.quizzes.length : 0 }}
                                            </span>
                                        </div>

                                        <div class="space-y-1.5 max-h-[130px] overflow-y-auto pr-1">
                                            <div v-for="quiz in lesson.quizzes" :key="quiz.id" class="flex justify-between items-center bg-slate-50 p-2.5 rounded-xl text-xs border border-slate-200/40 hover:bg-slate-100/70 transition">
                                                <div class="flex flex-col gap-0.5 flex-1 min-w-0">
                                                    <span class="font-bold text-gray-800 line-clamp-1">{{ quiz.title }}</span>
                                                    <span v-if="quiz.time_limit" class="text-[10px] text-gray-400 font-medium"><i class="fa-regular fa-clock text-[9px]"></i> Limit: {{ quiz.time_limit }} mins</span>
                                                </div>
                                                <div class="flex gap-1 ml-2 shrink-0">
                                                    <button @click="openEditQuiz(quiz)" class="text-gray-400 hover:text-blue-600 p-1 hover:bg-blue-50 rounded"><i class="fa-solid fa-pen text-[10px]"></i></button>
                                                    <button @click="deleteQuiz(quiz.id)" class="text-gray-400 hover:text-red-600 p-1 hover:bg-red-50 rounded"><i class="fa-solid fa-trash-can text-[10px]"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button @click="openAddQuiz(lesson.id)" class="w-full py-2 border border-dashed border-gray-300 text-gray-400 hover:text-blue-600 hover:border-blue-300 hover:bg-blue-50/30 text-xs font-bold rounded-xl transition">
                                        <i class="fa-solid fa-plus-circle"></i> Inject Quiz Evaluation
                                    </button>
                                </div>

                            </div>
                        </div>

                        <div v-if="!subject.lessons || subject.lessons.length === 0" class="col-span-full bg-white/10 text-white/70 border border-dashed border-white/20 p-12 rounded-[18px] text-center">
                            <i class="fa-solid fa-folder-minus text-3xl mb-3 opacity-40 block"></i>
                            No active lessons setup inside this syllabus layout yet. Use the action button above to initialize tracks.
                        </div>
                    </div>
                </section>
            </main>

            <div v-if="showLessonModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
                <div class="bg-white rounded-[20px] max-w-md w-full p-6 shadow-2xl border border-slate-100 animate-[fadeIn_0.2s_ease-out]">
                    <div class="flex justify-between items-center mb-5">
                        <h3 class="text-lg font-bold text-blue-900 flex items-center gap-2">
                            <i class="fa-solid fa-screwdriver-wrench text-blue-600"></i>
                            {{ isEditMode ? 'Modify Lesson Track' : 'Create New Lesson Module' }}
                        </h3>
                        <button @click="showLessonModal = false" class="text-gray-400 hover:text-gray-600 text-lg"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <form @submit.prevent="submitLesson" class="space-y-4">
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Lesson Title</label>
                            <input v-model="lessonForm.title" type="text" placeholder="e.g. Chapter 1: Architectural Systems" class="w-full mt-1 border p-2.5 rounded-xl focus:outline-blue-600 text-sm font-medium">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Lecture Summary Content</label>
                            <textarea v-model="lessonForm.content" placeholder="Enter brief technical summary scope..." rows="4" class="w-full mt-1 border p-2.5 rounded-xl focus:outline-blue-600 text-sm font-medium"></textarea>
                        </div>
                        <div class="pt-2 flex gap-2">
                            <button type="button" @click="showLessonModal = false" class="flex-1 bg-gray-100 text-gray-700 py-2.5 rounded-xl font-bold text-sm hover:bg-gray-200 transition">Cancel</button>
                            <button type="submit" class="flex-1 bg-gradient-to-r from-blue-800 to-blue-600 text-white py-2.5 rounded-xl font-bold text-sm hover:from-blue-900 hover:to-blue-700 transition shadow-md">Save Lesson Module</button>
                        </div>
                    </form>
                </div>
            </div>

            <div v-if="showAssignmentModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
                <div class="bg-white rounded-[20px] max-w-md w-full p-6 shadow-2xl border border-slate-100 animate-[fadeIn_0.2s_ease-out]">
                    <div class="flex justify-between items-center mb-5">
                        <h3 class="text-lg font-bold text-blue-900 flex items-center gap-2">
                            <i class="fa-solid fa-file-pen text-indigo-600"></i>
                            {{ isAssignmentEdit ? 'Edit Assignment Track' : 'Deploy Assignment Task' }}
                        </h3>
                        <button @click="showAssignmentModal = false" class="text-gray-400 hover:text-gray-600 text-lg"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <form @submit.prevent="submitAssignment" class="space-y-4">
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Assignment Activity Name</label>
                            <input v-model="assignmentForm.title" type="text" placeholder="e.g. Case Study Exercise 1" class="w-full mt-1 border p-2.5 rounded-xl focus:outline-blue-600 text-sm font-medium">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Target Deadline Due Date</label>
                            <input v-model="assignmentForm.due_date" type="date" class="w-full mt-1 border p-2.5 rounded-xl focus:outline-blue-600 text-sm font-bold text-gray-700">
                        </div>
                        <div class="pt-2 flex gap-2">
                            <button type="button" @click="showAssignmentModal = false" class="flex-1 bg-gray-100 text-gray-700 py-2.5 rounded-xl font-bold text-sm hover:bg-gray-200 transition">Cancel</button>
                            <button type="submit" class="flex-1 bg-gradient-to-r from-blue-800 to-blue-600 text-white py-2.5 rounded-xl font-bold text-sm hover:from-blue-900 hover:to-blue-700 transition shadow-md">Deploy Task</button>
                        </div>
                    </form>
                </div>
            </div>

            <div v-if="showQuizModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
                <div class="bg-white rounded-[20px] max-w-md w-full p-6 shadow-2xl border border-slate-100 animate-[fadeIn_0.2s_ease-out]">
                    <div class="flex justify-between items-center mb-5">
                        <h3 class="text-lg font-bold text-blue-900 flex items-center gap-2">
                            <i class="fa-solid fa-receipt text-purple-600"></i>
                            {{ isQuizEdit ? 'Edit Quiz Assessment Settings' : 'Deploy Assessment Form Quiz' }}
                        </h3>
                        <button @click="showQuizModal = false" class="text-gray-400 hover:text-gray-600 text-lg"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <form @submit.prevent="submitQuiz" class="space-y-4">
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Quiz Identity Title</label>
                            <input v-model="quizForm.title" type="text" placeholder="e.g. Chapter 1 Quiz Evaluation" class="w-full mt-1 border p-2.5 rounded-xl focus:outline-blue-600 text-sm font-medium">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Countdown Time Limit (Minutes)</label>
                            <input v-model="quizForm.time_limit" type="number" placeholder="e.g. 45" class="w-full mt-1 border p-2.5 rounded-xl focus:outline-blue-600 text-sm font-medium">
                        </div>
                        <div class="pt-2 flex gap-2">
                            <button type="button" @click="showQuizModal = false" class="flex-1 bg-gray-100 text-gray-700 py-2.5 rounded-xl font-bold text-sm hover:bg-gray-200 transition">Cancel</button>
                            <button type="submit" class="flex-1 bg-gradient-to-r from-blue-800 to-blue-600 text-white py-2.5 rounded-xl font-bold text-sm hover:from-blue-900 hover:to-blue-700 transition shadow-md">Deploy Exam Form</button>
                        </div>
                    </form>
                </div>
            </div>
            
        </AdminLayout>
    </div>
</template>