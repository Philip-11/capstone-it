<script setup>
//import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm, Head } from '@inertiajs/vue3';

// The 'lessons' prop comes directly from your TeacherController index method
const props = defineProps({
    lessons: Array
});

// Inertia's form helper handles CSRF tokens and loading states automatically
const form = useForm({
    title: '',
    content: '',
});

const submit = () => {
    form.post(route('teacher.lessons.store'), {
        onSuccess: () => form.reset(), // Clear the form after saving
    });
};
</script>

<template>
    <Head title="Teacher Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Lessons</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- FORM TO CREATE LESSON -->
                <div class="bg-white p-6 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <h3 class="mb-4 font-bold">Create New Lesson</h3>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <input v-model="form.title" type="text" placeholder="Lesson Title" class="w-full border-gray-300 rounded-md" />
                            <div v-if="form.errors.title" class="text-red-500 text-sm">{{ form.errors.title }}</div>
                        </div>
                        <div>
                            <textarea v-model="form.content" placeholder="Write your lesson here..." class="w-full border-gray-300 rounded-md"></textarea>
                            <div v-if="form.errors.content" class="text-red-500 text-sm">{{ form.errors.content }}</div>
                        </div>
                        <button type="submit" :disabled="form.processing" class="bg-blue-500 text-white px-4 py-2 rounded">
                            {{ form.processing ? 'Saving...' : 'Save Lesson' }}
                        </button>
                    </form>
                </div>

                <!-- LIST OF LESSONS -->
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="mb-4 font-bold">Existing Lessons</h3>
                    <div v-for="lesson in lessons" :key="lesson.id" class="border-b py-2">
                        <span class="font-medium">{{ lesson.title }}</span>
                        <span v-if="lesson.quiz" class="ml-4 text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Has Quiz</span>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>