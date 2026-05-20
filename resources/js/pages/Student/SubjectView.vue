<script setup>
const props = defineProps({
    subject: Object
});
</script>

<template>
    <div class="p-8 max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold">{{ subject.name }}</h1>
        <p class="text-gray-600 mb-6 italic">Instructor: {{ subject.teacher.name }}</p>

        <div class="space-y-4">
            <h2 class="text-xl font-semibold">Available Lessons</h2>
            
            <div v-for="lesson in subject.lessons" :key="lesson.id" class="p-4 bg-white shadow rounded border">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-lg">{{ lesson.title }}</h3>
                        <p class="text-sm text-gray-500">{{ lesson.content }}</p>
                    </div>
                    
                    <a v-if="lesson.file_path" 
                       :href="route('teacher.lessons.download', lesson.id)" 
                       class="bg-blue-100 text-blue-700 px-3 py-1 rounded text-sm font-medium">
                        Download File
                    </a>

                    <a v-if="lesson.quiz" :href="route('student.quiz.show', lesson.quiz.id)" class="bg-purple-600 text-white px-3 py-1 rounded text-sm font-medium hover:bg-purple-700">
                        Take Quiz
                    </a>

                    <div v-if="lesson.assignments && lesson.assignments.length > 0" class="space-y-2">
                        <div v-for="assignment in lesson.assignments" :key="assignment.id" class="flex justify-between items-center bg-purple-50 p-3 rounded">
                            <div>
                                <span class="text-sm font-medium text-purple-800">💼 Activity: {{ assignment.title }}</span>
                                <span class="block text-xs text-purple-600">Max Points: {{ assignment.max_points }}</span>
                            </div>
                            
                            <a :href="route('student.assignments.show', assignment.id)" class="bg-purple-600 text-white text-xs px-3 py-1 rounded">
                                Open Assignment
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="subject.lessons.length === 0" class="text-gray-500 italic">
                No lessons uploaded yet for this subject.
            </div>
        </div>
    </div>
</template>