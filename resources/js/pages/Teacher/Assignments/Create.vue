<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';

defineOptions({ layout: AuthenticatedLayout });

// Pinasahan ng 'lessons' mula sa controller para doon i-tag ng teacher ang assignment
const props = defineProps({
    lessons: Array
});

// Swak na sa original fields at variable names ng validation at model mo sa backend
const form = useForm({
    title: '',
    instructions: '', 
    max_points: 100,  
    due_date: '',
    lesson_id: ''
});

const submitAssignment = () => {
    form.post(route('teacher.assignments.store'), {
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

        <main class="max-w-3xl mx-auto px-4 md:px-0 pt-10 space-y-6">

            <section class="bg-white rounded-[24px] p-6 shadow-xl flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-lg shadow-sm">
                        <i class="fa-solid fa-file-signature text-sm"></i>
                    </div>
                    <div>
                        <h1 class="text-xl md:text-2xl font-bold text-blue-950">Create an Assignment/Activity</h1>
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
                            <input v-model="form.title" type="text" placeholder="e.g., Laboratory 1: Relational DB Schema Design" class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-600 transition" />
                        </div>
                        <div v-if="form.errors.title" class="text-red-500 text-xs font-semibold">{{ form.errors.title }}</div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-600 uppercase tracking-wider">Instructions & Guidelines</label>
                        <textarea v-model="form.instructions" rows="6" placeholder="Detail the submission requirements here. Explicitly describe text answers or specific target directions..." class="w-full border border-gray-200 p-3 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-600 transition"></textarea>
                        <div v-if="form.errors.instructions" class="text-red-500 text-xs font-semibold">{{ form.errors.instructions }}</div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        
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
                            <label class="text-xs font-bold text-gray-600 uppercase tracking-wider">Max Score Points</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                    <i class="fa-solid fa-star text-xs"></i>
                                </span>
                                <input v-model="form.max_points" type="number" min="1" class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-600 transition" />
                            </div>
                            <div v-if="form.errors.max_points" class="text-red-500 text-xs font-semibold">{{ form.errors.max_points }}</div>
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