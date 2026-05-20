<script setup>
import { useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    assignment: Object,
    submission: Object // Pwedeng may laman na kung nag-pasa na siya dati
});

const form = useForm({
    submission_text: props.submission ? props.submission.submission_text : '',
    file: null,
    old_file_path: props.submission ? props.submission.file_path : null
});

const handleFileUpload = (e) => {
    form.file = e.target.files[0];
};

const submitAssignment = () => {
    // TRICK: Kapag may file upload sa Inertia gamit ang POST/PUT, mas ligtas mag-POST 
    form.post(route('student.assignments.submit', props.assignment.id), {
        onSuccess: () => alert('Activity submitted successfully!')
    });
};
</script>

<template>
    <div class="p-8 max-w-3xl mx-auto bg-gray-50 min-h-screen">
        <Link :href="route('student.dashboard')" class="text-sm text-blue-600 hover:underline">← Back to Dashboard</Link>

        <div class="bg-white p-6 rounded shadow mt-4 mb-6">
            <h1 class="text-2xl font-bold text-gray-800">{{ assignment.title }}</h1>
            <div class="flex gap-4 text-sm text-gray-500 mt-2 mb-4">
                <span>Max Points: <strong class="text-gray-700">{{ assignment.max_points }}</strong></span>
                <span v-if="assignment.due_date">Due: <strong class="text-red-500">{{ new Date(assignment.due_date).toLocaleString() }}</strong></span>
            </div>
            <div class="border-t pt-4 text-gray-700 whitespace-pre-wrap">
                <p class="font-semibold mb-2 text-gray-900">Instructions:</p>
                {{ assignment.instructions }}
            </div>
        </div>

        <div v-if="submission && submission.grade !== null" class="bg-green-50 border border-green-300 p-4 rounded mb-6">
            <h3 class="font-bold text-green-800 text-lg">Grade: {{ submission.grade }} / {{ assignment.max_points }}</h3>
            <p v-if="submission.teacher_feedback" class="text-sm text-green-700 mt-1"><strong>Feedback:</strong> {{ submission.teacher_feedback }}</p>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-lg font-bold mb-4 text-gray-800">Your Submission</h2>

            <form @submit.prevent="submitAssignment" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-600">Text Response / Notes (Optional)</label>
                    <textarea v-model="form.submission_text" rows="4" placeholder="Type your online response or paste your code link here..." class="w-full border p-2 rounded"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-600">Attach File (PDF, DOCX, ZIP, Images)</label>
                    <input type="file" @change="handleFileUpload" class="w-full border p-2 rounded bg-gray-50" />
                    
                    <p v-if="submission && submission.file_path" class="text-xs text-blue-600 mt-1">
                        📁 You have already uploaded a file. Uploading a new one will replace it.
                    </p>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" :disabled="form.processing" class="bg-purple-600 text-white px-6 py-2 rounded font-bold shadow hover:bg-purple-700 transition">
                        {{ submission ? 'Resubmit Assignment' : 'Submit Assignment' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>