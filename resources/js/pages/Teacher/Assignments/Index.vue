<script setup>
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({ assignments: Array });
const form = useForm({});

const deleteAssignment = (id) => {
    if (confirm('Are you sure you want to delete this assignment?')) {
        form.delete(route('teacher.assignments.destroy', id));
    }
};
</script>

<template>
    <div class="p-6 bg-white rounded shadow max-w-5xl mx-auto mt-10">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">My Assignments</h1>
            <Link :href="route('teacher.assignments.create')" class="bg-blue-600 text-white px-4 py-2 rounded">Create New Assignment</Link>
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
                <tr v-for="assignment in assignments" :key="assignment.id" class="border-b hover:bg-gray-50">
                    <td class="p-3">{{ assignment.title }}</td>
                    <td class="p-3">{{ assignment.questions_count }} items</td>
                    <td class="p-3 text-center space-x-2">
                        <Link :href="route('teacher.assignments.edit', assignment.id)" class="text-blue-600">Edit</Link>
                        <button @click="deleteAssignment(assignment.id)" class="text-red-600">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>