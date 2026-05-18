<script setup>
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({ quizzes: Array });
const form = useForm({});

const deleteQuiz = (id) => {
    if (confirm('Are you sure you want to delete this quiz?')) {
        form.delete(route('teacher.quizzes.destroy', id));
    }
};
</script>

<template>
    <div class="p-6 bg-white rounded shadow max-w-5xl mx-auto mt-10">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">My Quizzes</h1>
            <Link :href="route('teacher.quizzes.create')" class="bg-blue-600 text-white px-4 py-2 rounded">Create New Quiz</Link>
        </div>

        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100 border-b">
                    <th class="p-3 text-left">Title</th>
                    <th class="p-3 text-left">Questions</th>
                    <th class="p-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="quiz in quizzes" :key="quiz.id" class="border-b hover:bg-gray-50">
                    <td class="p-3">{{ quiz.title }}</td>
                    <td class="p-3">{{ quiz.questions_count }} items</td>
                    <td class="p-3 text-center space-x-2">
                        <Link :href="route('teacher.quizzes.edit', quiz.id)" class="text-blue-600">Edit</Link>
                        <button @click="deleteQuiz(quiz.id)" class="text-red-600">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>