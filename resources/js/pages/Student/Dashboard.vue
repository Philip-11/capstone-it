<script setup>
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    joinedSubjects: Array,
});

const form = useForm({
    subject_code: '',
});

const join = () => {
    form.post(route('student.join'), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <div class="p-8 max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Student Dashboard</h1>

        <div class="bg-white p-6 rounded shadow mb-8">
            <h2 class="text-lg font-semibold mb-2">Join a Subject</h2>
            <div class="flex gap-2">
                <input v-model="form.subject_code" type="text" placeholder="Enter Subject Code (e.g. AB1234)" class="border p-2 rounded flex-1 uppercase">
                <button @click="join" :disabled="form.processing" class="bg-green-600 text-white px-4 py-2 rounded">
                    Join Class
                </button>
            </div>
            <p v-if="form.errors.subject_code" class="text-red-500 text-sm mt-2">{{ form.errors.subject_code }}</p>
        </div>

        <h2 class="text-xl font-semibold mb-4">My Enrolled Subjects</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-for="subject in joinedSubjects" :key="subject.id" class="bg-white p-4 rounded shadow border-l-4 border-blue-500">
                <h3 class="font-bold text-lg">{{ subject.name }}</h3>
                <p class="text-gray-600 text-sm">Instructor: {{ subject.teacher.name }}</p>
                <a :href="route('student.subjects.show', subject.id)" class="mt-4 text-blue-500 font-semibold">View Lessons →</a>
            </div>
            <div v-if="joinedSubjects.length === 0" class="text-gray-500">
                You haven't joined any subjects yet.
            </div>
        </div>
    </div>
</template>