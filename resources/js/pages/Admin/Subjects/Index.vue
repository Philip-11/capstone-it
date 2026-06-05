<script setup>
import { computed, ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';

const props = defineProps({
    subjects: Array,
    teachers: Array
});

// Modal state toggles
const showModal = ref(false);
const isEditMode = ref(false);
const currentSubjectId = ref(null);

const filterInstructor = ref('all');
const filterTerm = ref('all');
const filterCourse = ref('all');

const form = useForm({
    name: '',
    user_id: '',
    course: '',
    sem: '',
    subject_code: ''
});

const openAddModal = () => {
    isEditMode.value = false;
    currentSubjectId.value = null;
    form.reset();
    form.clearErrors();
    showModal.value = true;
};

const openEditModal = (subject) => {
    isEditMode.value = true;
    currentSubjectId.value = subject.id;
    form.clearErrors();
    
    form.name = subject.name;
    form.user_id = subject.user_id || '';
    form.course = subject.course;
    form.sem = subject.sem;
    form.subject_code = subject.subject_code;
    
    showModal.value = true;
};

const submit = () => {
    if (isEditMode.value) {
        form.put(route('admin.subjects.update', currentSubjectId.value), {
            onSuccess: () => closeModal()
        });
    } else {
        form.post(route('admin.subjects.store'), {
            onSuccess: () => closeModal()
        });
    }
};

const closeModal = () => {
    form.reset();
    showModal.value = false;
};

const deleteSubject = (id) => {
    if (confirm('Are you sure you want to delete this subject? All associated lessons, quizzes, and student connections might be affected.')) {
        form.delete(route('admin.subjects.destroy', id));
    }
};

// Icon helper katulad nung nasa dashboard mo pre
const getSubjectIcon = (name) => {
    const lower = name.toLowerCase();
    if (lower.includes('math') || lower.includes('alge')) return 'fa-calculator';
    if (lower.includes('sci') || lower.includes('phys') || lower.includes('chem')) return 'fa-flask';
    if (lower.includes('prog') || lower.includes('code') || lower.includes('tech') || lower.includes('it')) return 'fa-laptop-code';
    if (lower.includes('eng') || lower.includes('read') || lower.includes('write')) return 'fa-spell-check';
    if (lower.includes('hist') || lower.includes('soc')) return 'fa-earth-asia';
    return 'fa-book'; // Default fallback icon
};

const filteredSubjects = computed(() => {
    return props.subjects.filter(subject => {
        // 1. Filter sa Instructor (tumutugma sa user_id / teacher relation)
        const matchInstructor = filterInstructor.value === 'all' || 
                                String(subject.user_id) === String(filterInstructor.value);
        
        // 2. Filter sa Term (e.g., "1st Semester", "2nd Semester")
        const matchTerm = filterTerm.value === 'all' || 
                          subject.sem === filterTerm.value;
        
        // 3. Filter sa Course (e.g., "BSIT", "BSCS")
        const matchCourse = filterCourse.value === 'all' || 
                            subject.course === filterCourse.value;

        // Dapat pumasa sa lahat ng tatlong kondisyon para lumabas ang subject card
        return matchInstructor && matchTerm && matchCourse;
    });
});

// Helper function para makakuha ng listahan ng Unique Terms mula sa subjects prop para sa dropdown array
const uniqueTerms = computed(() => {
    const terms = props.subjects.map(s => s.sem).filter(Boolean);
    return [...new Set(terms)];
});

// Helper function para makakuha ng listahan ng Unique Courses mula sa subjects prop para sa dropdown array
const uniqueCourses = computed(() => {
    const courses = props.subjects.map(s => s.course).filter(Boolean);
    return [...new Set(courses)];
});
</script>

<template>
    <Head title="Subject Management" />

    <div class="min-h-screen bg-gradient-to-br from-[#0c1a3a] via-[#1e3a8a] via-[#1e40af] via-[#1d4ed8] to-[#3730a3] text-gray-900 font-sans pb-12">
        
        <!-- HEADER (Admin Navbar Configuration) -->
        <AdminLayout>
            <!-- MAIN SECTION CONTROLLER -->
            <main class="max-w-7xl mx-auto px-4 md:px-[9%] pt-11 space-y-7">
                
                <!-- Hero Banner Section -->
                <section class="bg-gradient-to-br from-blue-800 to-blue-600 text-white p-8 md:p-12 rounded-[24px] flex flex-col md:flex-row justify-between items-center gap-6 shadow-xl">
                    <div class="text-center md:text-left">
                        <span class="bg-white/20 px-3 py-1 rounded-full text-xs tracking-wide">Academic Management Center</span>
                        <h1 class="text-3xl md:text-4xl font-bold my-[18px]">Subject Class Management</h1>
                        <p class="max-w-[500px] text-blue-100 text-sm">Create foundational courses, auto-generate student access keys, and assign instructors to handle class tracks.</p>
                    </div>
                    <button @click="openAddModal" class="bg-emerald-600 text-white px-5 py-3 rounded-xl font-bold hover:bg-emerald-700 transition duration-200 shadow-md flex items-center gap-2 shrink-0 active:scale-95">
                        <i class="fa-solid fa-folder-plus"></i> Create New Subject
                    </button>
                </section>

                <section class="bg-white rounded-[18px] p-5 shadow-md border border-gray-100 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wide">Filter by Instructor</label>
                        <select v-model="filterInstructor" class="border p-2.5 rounded-xl text-sm font-semibold text-blue-900 bg-white focus:outline-blue-600 transition">
                            <option value="all">All Instructors / Teachers</option>
                            <option v-for="teacher in teachers" :key="teacher.id" :value="teacher.id">
                                {{ teacher.name }}
                            </option>
                        </select>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wide">Filter by Academic Term</label>
                        <select v-model="filterTerm" class="border p-2.5 rounded-xl text-sm font-semibold text-blue-900 bg-white focus:outline-blue-600 transition">
                            <option value="all">All Terms</option>
                            <option v-for="sem in uniqueTerms" :key="sem" :value="sem">
                                {{ sem }}
                            </option>
                        </select>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wide">Filter by Course Track</label>
                        <select v-model="filterCourse" class="border p-2.5 rounded-xl text-sm font-semibold text-blue-900 bg-white focus:outline-blue-600 transition">
                            <option value="all">All Courses</option>
                            <option v-for="course in uniqueCourses" :key="course" :value="course">
                                {{ course }}
                            </option>
                        </select>
                    </div>
                </section>

                <!-- Grid Display (Inihawig sa layout ng Enrolled Subjects ng Dashboard mo pre) -->
                <section class="space-y-4">
                    <div class="flex justify-between items-center text-white">
                        <h3 class="text-xl font-bold">Active Course Subjects</h3>
                        <span class="text-xs font-semibold bg-white/10 px-3 py-1 rounded-md border border-white/10">Matched: {{ filteredSubjects.length }}</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-[18px]">
                        <div v-for="subject in filteredSubjects" :key="subject.id" class="bg-white rounded-[18px] p-[22px] border-l-[5px] border-blue-600 shadow-md flex flex-col justify-between hover:-translate-y-1 transition duration-200 min-h-[160px]">
                            
                            <div class="flex items-start gap-4">
                                <div class="w-11 h-11 min-w-[44px] rounded-[14px] bg-blue-100 text-blue-600 flex items-center justify-center text-lg shadow-sm">
                                    <i :class="['fa-solid', getSubjectIcon(subject.name)]"></i>
                                </div>

                                <div class="space-y-0.5 flex-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <h4 class="font-bold text-blue-950 text-base line-clamp-1 flex-1">{{ subject.name }}</h4>
                                        <span class="text-[10px] font-black tracking-wider bg-slate-100 text-slate-700 px-2 py-0.5 rounded border border-slate-200 select-all">
                                            {{ subject.subject_code }}
                                        </span>
                                    </div>
                                    <p class="text-gray-400 text-sm">
                                        Instructor: <span class="font-medium text-gray-700">{{ subject.teacher ? subject.teacher.name : 'Unassigned' }}</span>
                                    </p>
                                    
                                    <div class="flex gap-1.5 pt-1.5">
                                        <span v-if="subject.course" class="text-[10px] font-bold bg-blue-50 text-blue-600 px-2 py-0.5 rounded-md">{{ subject.course }}</span>
                                        <span v-if="subject.sem" class="text-[10px] font-bold bg-purple-50 text-purple-600 px-2 py-0.5 rounded-md">{{ subject.sem }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-2 border-t border-gray-50 pt-3 mt-4">
                                <button @click="openEditModal(subject)" class="px-3 py-1.5 bg-slate-100 hover:bg-blue-50 hover:text-blue-600 text-gray-600 text-xs font-bold rounded-lg transition flex items-center gap-1">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </button>
                                <button @click="deleteSubject(subject.id)" class="px-3 py-1.5 bg-slate-50 hover:bg-red-50 hover:text-red-600 text-gray-400 text-xs font-bold rounded-lg transition flex items-center gap-1">
                                    <i class="fa-solid fa-trash-can"></i> Remove
                                </button>
                            </div>
                        </div>

                        <div v-if="filteredSubjects.length === 0" class="col-span-full bg-white/10 text-white/70 border border-dashed border-white/20 p-12 rounded-[18px] text-center">
                            <i class="fa-solid fa-filter-circle-xmark text-3xl mb-3 opacity-40 block"></i>
                            No active subjects match your selected instructor, term, or course filters.
                        </div>
                    </div>
                </section>
            </main>

            <!-- SUBJECT DUAL-MODE MODAL LAYER -->
            <div v-if="showModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
                <div class="bg-white rounded-[20px] max-w-md w-full p-6 shadow-2xl border border-slate-100 animate-[fadeIn_0.2s_ease-out]">
                    <div class="flex justify-between items-center mb-5">
                        <h3 class="text-lg font-bold text-blue-900 flex items-center gap-2">
                            <i class="fa-solid fa-book-bookmark text-blue-600"></i> 
                            {{ isEditMode ? 'Modify Subject Info' : 'Create New Course Subject' }}
                        </h3>
                        <button @click="closeModal" class="text-gray-400 hover:text-gray-600 text-lg"><i class="fa-solid fa-xmark"></i></button>
                    </div>

                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Subject Name</label>
                            <input v-model="form.name" type="text" placeholder="e.g. Introduction to Computing" class="w-full mt-1 border p-2.5 rounded-xl focus:outline-blue-600 text-sm font-medium">
                            <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
                        </div>

                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Course</label>
                            <input v-model="form.course" type="text" placeholder="e.g. BSIT" class="w-full mt-1 border p-2.5 rounded-xl focus:outline-blue-600 text-sm font-medium">
                            <p v-if="form.errors.course" class="text-red-500 text-xs mt-1">{{ form.errors.course }}</p>
                        </div>

                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Term</label>
                            <input v-model="form.sem" type="text" placeholder="e.g. 1st" class="w-full mt-1 border p-2.5 rounded-xl focus:outline-blue-600 text-sm font-medium">
                            <p v-if="form.errors.sem" class="text-red-500 text-xs mt-1">{{ form.errors.sem }}</p>
                        </div>

                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Assign Instructor (Teacher)</label>
                            <select v-model="form.user_id" class="w-full mt-1 border p-2.5 rounded-xl focus:outline-blue-600 text-sm font-bold text-gray-700 bg-white">
                                <option value="">-- Leave Vacant / Select Later --</option>
                                <option v-for="teacher in teachers" :key="teacher.id" :value="teacher.id">
                                    {{ teacher.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.user_id" class="text-red-500 text-xs mt-1">{{ form.errors.user_id }}</p>
                        </div>

                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Custom Subject Code <span class="text-[10px] text-gray-400 normal-case font-medium">(Optional)</span></label>
                            <input v-model="form.subject_code" type="text" placeholder="e.g. IT101 (Leave blank to auto-generate)" class="w-full mt-1 border p-2.5 rounded-xl focus:outline-blue-600 text-sm uppercase font-semibold">
                            <p v-if="form.errors.subject_code" class="text-red-500 text-xs mt-1">{{ form.errors.subject_code }}</p>
                        </div>

                        <div class="pt-2 flex gap-2">
                            <button type="button" @click="closeModal" class="flex-1 bg-gray-100 text-gray-700 py-2.5 rounded-xl font-bold text-sm hover:bg-gray-200 transition">Cancel</button>
                            <button type="submit" :disabled="form.processing" class="flex-1 bg-gradient-to-r from-blue-800 to-blue-600 text-white py-2.5 rounded-xl font-bold text-sm hover:from-blue-900 hover:to-blue-700 transition shadow-md disabled:opacity-50">
                                {{ isEditMode ? 'Update Subject' : 'Deploy Subject' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AdminLayout>

        

    </div>
</template>