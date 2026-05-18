<script setup>
import { useForm, Link } from '@inertiajs/vue3';

const props = defineProps({ lessons: Array });

const form = useForm({
    title: '',
    lesson_id: '',
    instructions: '',
    max_points: 100,
    due_date: ''
});

const submit = () => {
    form.post(route('teacher.assignments.store'));
};
</script>

<template>
    <div class="p-8 max-w-3xl mx-auto bg-white rounded shadow mt-10">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">Create Activity / Assignment</h1>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <label class="block font-semibold mb-1">Activity Title</label>
                <input v-model="form.title" type="text" placeholder="e.g., HTML Basic Coding Challenge" class="w-full border p-2 rounded" required />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold mb-1">Link to Lesson (Optional)</label>
                    <select v-model="form.lesson_id" class="w-full border p-2 rounded">
                        <option value="">Select Lesson</option>
                        <option v-for="lesson in lessons" :key="lesson.id" :value="lesson.id">{{ lesson.title }}</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Max Points</label>
                    <input v-model="form.max_points" type="number" class="w-full border p-2 rounded" />
                </div>
            </div>

            <div>
                <label class="block font-semibold mb-1">Due Date</label>
                <input v-model="form.due_date" type="datetime-local" class="w-full border p-2 rounded" />
            </div>

            <div>
                <label class="block font-semibold mb-1">Instructions / Description</label>
                <textarea v-model="form.instructions" rows="6" placeholder="Write the instructions here..." class="w-full border p-2 rounded" required></textarea>
            </div>

            <div class="flex justify-end space-x-2 pt-4">
                <Link :href="route('teacher.assignments.index')" class="bg-gray-200 px-4 py-2 rounded">Cancel</Link>
                <button type="submit" :disabled="form.processing" class="bg-indigo-600 text-white px-6 py-2 rounded font-bold hover:bg-indigo-700">
                    Publish Activity
                </button>
            </div>
        </form>
    </div>
</template>