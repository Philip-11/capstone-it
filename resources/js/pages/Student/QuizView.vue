<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    quiz: Object
});

// Gagawa tayo ng form gamit ang Inertia useForm
// Ang 'answers' ay magiging object na ang key ay question_id at ang value ay yung napiling sagot
const form = useForm({
    answers: {}
});

const submitQuiz = () => {
    if (confirm('Are you sure you want to submit your answers?')) {
        form.post(route('student.quiz.submit', props.quiz.id), {
            onSuccess: () => {
                alert('Quiz submitted successfully!');
            },
            onError: (errors) => {
                console.error(errors);
                alert('Something went wrong. Please check your answers.');
            }
        });
    }
};

// Helper function para i-parse ang JSON options kung string pa ito galing sa DB
const parseOptions = (options) => {
    if (!options) return [];
    if (typeof options === 'string') {
        try {
            return JSON.parse(options);
        } catch (e) {
            return [];
        }
    }
    return options; // If array/object na siya talaga
};
</script>

<template>
    <div class="p-8 max-w-3xl mx-auto min-h-screen bg-gray-50 rounded-lg shadow-sm my-6">
        <div class="border-b pb-4 mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ quiz.title }}</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Passing Score Requirement: <span class="font-semibold text-gray-700">{{ quiz.passing_score }}%</span>
                </p>
            </div>
            <Link :href="route('student.subjects.show', quiz.lesson?.subject_id || 1)" class="text-sm text-gray-600 hover:underline">
                ← Back to Subject
            </Link>
        </div>

        <form @submit.prevent="submitQuiz" class="space-y-6">
            <div v-for="(question, index) in quiz.questions" :key="question.id" class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <div class="flex justify-between items-start mb-4">
                    <span class="font-bold text-blue-600">Question {{ index + 1 }}</span>
                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">
                        {{ question.points }} {{ question.points > 1 ? 'points' : 'point' }}
                    </span>
                </div>
                
                <p class="text-gray-800 font-medium text-lg mb-4">{{ question.question_text }}</p>

                <div v-if="question.question_type === 'multiple_choice' || question.question_type === 'true_or_false'" class="space-y-2">
                    <label 
                        v-for="option in parseOptions(question.options)" 
                        :key="option" 
                        class="flex items-center gap-3 p-3 rounded-md border border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors"
                        :class="{'bg-blue-50/50 border-blue-200': form.answers[question.id] === option}"
                    >
                        <input 
                            type="radio" 
                            :name="'question_' + question.id" 
                            :value="option" 
                            v-model="form.answers[question.id]"
                            class="text-blue-600 focus:ring-blue-500"
                        />
                        <span class="text-gray-700">{{ option }}</span>
                    </label>
                </div>

                <div v-else-if="question.question_type === 'short_answer'" class="mt-2">
                    <input 
                        type="text" 
                        v-model="form.answers[question.id]" 
                        placeholder="Type your answer here..." 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border"
                    />
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button 
                    type="submit" 
                    :disabled="form.processing"
                    class="bg-blue-600 text-white font-semibold px-6 py-3 rounded-md shadow hover:bg-blue-700 transition-colors disabled:opacity-50"
                >
                    {{ form.processing ? 'Submitting Answers...' : 'Submit Quiz' }}
                </button>
            </div>
        </form>
    </div>
</template>