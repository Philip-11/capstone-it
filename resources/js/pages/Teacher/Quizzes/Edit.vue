<script setup>
import { useForm, Link } from '@inertiajs/vue3';

const props = defineProps({ quiz: Object, lessons: Array });

const form = useForm({
    title: props.quiz.title,
    lesson_id: props.quiz.lesson_id ?? '',
    passing_score: props.quiz.passing_score,
    questions: props.quiz.questions
});

const addQuestion = () => {
    form.questions.push({ 
        question_text: '', 
        question_type: 'multiple_choice', 
        options: ['', '', '', ''], 
        correct_answer: '', 
        points: 1 
    });
};

const removeQuestion = (index) => {
    form.questions.splice(index, 1);
};

const submit = () => {
    form.put(route('teacher.quizzes.update', props.quiz.id), {
        onSuccess: () => alert('Quiz updated successfully'),
    });
};
</script>

<template>
    <div class="p-8 max-w-4xl mx-auto bg-gray-50 min-h-screen">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">Edit Quiz: {{ quiz.title }}</h1>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="bg-white p-6 rounded shadow-sm space-y-4">
                <input v-model="form.title" type="text" placeholder="Quiz Title" class="w-full border p-2 rounded" required />
                <div class="flex gap-4">
                    <select v-model="form.lesson_id" class="flex-1 border p-2 rounded">
                        <option value="">Link to a Lesson (Optional)</option>
                        <option v-for="lesson in lessons" :key="lesson.id" :value="lesson.id">{{ lesson.title }}</option>
                    </select>
                    <input v-model="form.passing_score" type="number" placeholder="Passing Score %" class="w-32 border p-2 rounded" />
                </div>
            </div>

            <div v-for="(question, index) in form.questions" :key="index" class="bg-white p-6 rounded shadow border-l-4 border-blue-500">
                <div class="flex justify-between items-center mb-4">
                    <span class="font-bold text-lg">Question #{{ index + 1 }}</span>
                    <button type="button" @click="removeQuestion(index)" class="text-red-500 text-sm">Remove</button>
                </div>

                <div class="space-y-4">
                    <textarea v-model="question.question_text" placeholder="Enter question text" class="w-full border p-2 rounded" required></textarea>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <select v-model="question.question_type" class="border p-2 rounded">
                            <option value="multiple_choice">Multiple Choice</option>
                            <option value="true_or_false">True or False</option>
                            <option value="short_answer">Short Answer</option>
                        </select>
                        <input v-model="question.points" type="number" class="border p-2 rounded" placeholder="Points" />
                    </div>

                    <div v-if="question.question_type === 'multiple_choice'" class="grid grid-cols-2 gap-2 mt-2">
                        <input v-for="(opt, oIdx) in 4" :key="oIdx" v-model="question.options[oIdx]" type="text" :placeholder="'Option ' + (oIdx+1)" class="border p-2 rounded text-sm" />
                    </div>

                    <input v-model="question.correct_answer" type="text" placeholder="Correct Answer (Exact match)" class="w-full border p-2 border-green-300 rounded bg-green-50" required />
                </div>
            </div>

            <div class="flex justify-between">
                <button type="button" @click="addQuestion" class="bg-gray-200 px-4 py-2 rounded font-medium">+ Add Another Question</button>
                <button type="submit" :disabled="form.processing" class="bg-blue-600 text-white px-8 py-2 rounded font-bold shadow hover:bg-blue-700">Save Quiz</button>
            </div>
        </form>
    </div>
</template>