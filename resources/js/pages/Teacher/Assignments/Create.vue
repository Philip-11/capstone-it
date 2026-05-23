<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';

// Pinasahan natin ng 'lessons' mula sa controller para doon i-tag ng teacher ang assignment
const props = defineProps({
    lessons: Array
});

const form = useForm({
    title: '',
    description: '',
    due_date: '',
    lesson_id: '',
    attachment: null // Kung gusto rin ni teacher mag-attach ng guide docs/templates
});

const onFileChange = (e) => {
    form.attachment = e.target.files[0];
};

const submitAssignment = () => {
    form.post(route('teacher.assignments.store'), {
        forceFormData: true,
        onSuccess: () => {
            form.reset();
            alert('Assignment deployed successfully!');
        }
    });
};
</script>

<template>
    <Head title="Create Assignment" />

    <div class="min-h-screen bg-gradient-to-br from-[#0c1a3a] via-[#1e3a8a] via-[#1e40af] to-[#3730a3] text-gray-900 font-sans pb-16">
        
        <header class="h-[66px] bg-white border-b-3 border-purple-600 px-[5%] md:px-[9%] flex items-center justify-between shadow-lg sticky top-0 z-50">
            <div class="flex items-center gap-2.5">
                <img src="/LOGO.png" alt="School Logo" class="w-[30px] h-[30px] object-contain" />
                <h2 class="text-xl font-bold text-blue-900 tracking-tight">GLMS <span class="text-xs font-semibold bg-purple-100 text-purple-700 px-2 py-0.5 rounded ml-1">Faculty</span></h2>
            </div>

            <nav class="hidden md:flex items-center gap-[34px]">
                <Link :href="route('teacher.lessons.name')" class="text-gray-600 hover:text-blue-600 text-sm font-semibold transition">Lessons & Hub</Link>
                <a href="#" class="text-gray-600 hover:text-blue-600 text-sm font-semibold transition">Classes</a>
                <Link :href="route('teacher.assignments.index')" class="text-purple-600 font-bold text-sm py-6 relative after:content-[''] after:absolute after:left-0 after:bottom-[-3px] after:w-full after:h-[3px] after:bg-purple-600 after:rounded-t">Assignments Hub</Link>
            </nav>

            <Link method="post" href="/logout" as="button" class="bg-gradient-to-r from-blue-800 to-blue-600 text-white px-4 py-2.5 rounded-lg text-xs font-semibold flex items-center gap-2 transition hover:from-red-600 hover:to-red-700 shadow-md">
                Logout Dashboard
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
            </Link>
        </header>

        <main class="max-w-3xl mx-auto px-4 md:px-0 pt-10 space-y-6">

            <section class="bg-white rounded-[24px] p-6 shadow-xl flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-lg shadow-sm">
                        <i class="fa-solid fa-file-signature text-sm"></i>
                    </div>
                    <div>
                        <h1 class="text-xl md:text-2xl font-bold text-blue-950">Draft Task Workspace</h1>
                        <p class="text-xs text-gray-500">Configure file upload and text field requirements for active cohorts.</p>
                    </div>
                </div>
                
                <Link :href="route('teacher.assignments.index')" class="inline-flex items-center gap-2 text-gray-600 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-xl text-xs font-bold transition">
                    <i class="fa-solid fa-arrow-left"></i> Back to List
                </Link>
            </section>

            <section class="bg-white p-6 md:p-8 rounded-[24px] shadow-xl border border-gray-100">
                <form @submit.prevent="submitAssignment" class="space-y-5">
                    
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-600 uppercase tracking-wider">Assignment Title / Task Name</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <i class="fa-solid fa-heading text-xs"></i>
                            </span>
                            <input v-model="form.title" type="text" placeholder="e.g., Activity 1" class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-600 transition" />
                        </div>
                        <div v-if="form.errors.title" class="text-red-500 text-xs font-semibold">{{ form.errors.title }}</div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-600 uppercase tracking-wider">Instructions & Guidelines</label>
                        <textarea v-model="form.description" rows="6" placeholder="Detail the submission instructions here." class="w-full border border-gray-200 p-3 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-600 transition"></textarea>
                        <div v-if="form.errors.description" class="text-red-500 text-xs font-semibold">{{ form.errors.description }}</div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-600 uppercase tracking-wider">Link to Specific Lesson</label>
                            <select v-model="form.lesson_id" class="w-full border border-gray-200 p-3 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-600 bg-white transition">
                                <option value="" disabled>-- Select Attached Topic --</option>
                                <option v-for="lesson in lessons" :key="lesson.id" :value="lesson.id">
                                    {{ lesson.title }}
                                </option>
                            </select>
                            <div v-if="form.errors.lesson_id" class="text-red-500 text-xs font-semibold">{{ form.errors.lesson_id }}</div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-600 uppercase tracking-wider">Due Date & Lock Deadline</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                    <i class="fa-solid fa-calendar-clock text-xs"></i>
                                </span>
                                <input v-model="form.due_date" type="datetime-local" class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-600 transition" />
                            </div>
                            <div v-if="form.errors.due_date" class="text-red-500 text-xs font-semibold">{{ form.errors.due_date }}</div>
                        </div>

                    </div>

                    <div class="space-y-1 pt-1">
                        <label class="text-xs font-bold text-gray-600 uppercase tracking-wider">Reference Attachment Template (Optional)</label>
                        <input type="file" @change="onFileChange" class="w-full text-xs text-gray-500 bg-gray-50 border border-gray-200 p-2.5 rounded-xl file:mr-4 file:py-1.5 file:px-3.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 cursor-pointer" />
                        
                        <div v-if="form.progress" class="w-full bg-gray-100 rounded-full h-2 mt-2">
                            <div class="bg-purple-600 h-2 rounded-full transition-all" :style="{ width: form.progress.percentage + '%' }"></div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-50 flex justify-end">
                        <button type="submit" :disabled="form.processing" class="bg-gradient-to-r from-purple-700 to-purple-600 hover:from-purple-800 hover:to-purple-700 text-white font-bold px-8 py-3.5 rounded-xl shadow-lg transition duration-150 hover:-translate-y-0.5 disabled:opacity-50 flex items-center gap-2 text-sm">
                            <i class="fa-solid" :class="form.processing ? 'fa-spinner animate-spin' : 'fa-rocket'"></i>
                            {{ form.processing ? 'Creating Coursework...' : 'Create Coursework' }}
                        </button>
                    </div>

                </form>
            </section>

        </main>
    </div>
</template>