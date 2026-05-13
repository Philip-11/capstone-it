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
                </div>
            </div>

            <div v-if="subject.lessons.length === 0" class="text-gray-500 italic">
                No lessons uploaded yet for this subject.
            </div>
        </div>
    </div>
</template>