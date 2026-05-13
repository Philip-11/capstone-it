<script setup>
//import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm, Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

// The 'lessons' prop comes directly from your TeacherController index method
const props = defineProps({
    lessons: Array,
    subjects: Array,
});

//State to check if we're editing
const isEditing = ref(false);
const currentLessonId = ref(null);
const fileInput = ref(null);

// Inertia's form helper handles CSRF tokens and loading states automatically
const form = useForm({
    title: '',
    content: '',
    subject_id: '',
    document: null,
});

const onFileChange = (e) => {
    form.document = e.target.files[0];
    console.log("File attached to form:", form.document);
}

const submit = () => {
    console.log("Current file in form: ", form.document);
    if (isEditing.value){
        form.transform((data) => ({
            ...data,
            _method: 'PUT',
        })).post(route('teacher.lessons.update', currentLessonId.value), {
            forceFormData: true,
            onSuccess: () => {
                cancelEdit();
                if (fileInput.value) {
                    fileInput.value.value = null;
                }
            }
        });
    } else {
        form.post(route('teacher.lessons.store'), {
            forceFormData: true,
            onSuccess: () => {
                form.reset(); // Clear the form after saving
                if (fileInput.value) {
                    fileInput.value.value = null;
                }
            }
        });
    }
    
};

const startEdit = (lesson) => {
    isEditing.value = true;
    currentLessonId.value = lesson.id;

    //Fill form with existing data
    form.title = lesson.title;
    form.content = lesson.content;
}

const cancelEdit = () => {
    isEditing.value = false;
    currentLessonId.value = null;
    form.reset();
}

const deleteLesson = (lesson) => {
    if (confirm(`Are you sure you want to delete "${lesson.title}"?`)){
        router.delete(route('teacher.lessons.destroy', lesson.id))
    }
}
</script>

<template>
    <Head title="Teacher Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Lessons</h2>
        </template>

        <div v-if="$page.props.flash.message" class="bg-green-100 text-green-800 p-4 rounded mb-4">
            {{ $page.props.flash.message }}
        </div>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- FORM TO CREATE LESSON -->
                <div class="bg-white p-6 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <h3 class="mb-4 font-bold">
                        {{ isEditing ? 'Edit Lesson' : 'Create New Lesson' }}
                    </h3>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <input v-model="form.title" type="text" placeholder="Lesson Title" class="w-full border-gray-300 rounded-md" />
                            <div v-if="form.errors.title" class="text-red-500 text-sm">{{ form.errors.title }}</div>
                        </div>
                        <div>
                            <textarea v-model="form.content" placeholder="Write your lesson here..." class="w-full border-gray-300 rounded-md"></textarea>
                            <div v-if="form.errors.content" class="text-red-500 text-sm">{{ form.errors.content }}</div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Assign to Subject</label>
                            <select v-model="form.subject_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="" disabled>-- Select Subject --</option>
                                <option v-for="subject in subjects" :key="subject.id" :value="subject.id">
                                    {{ subject.name }} ({{ subject.subject_code }})
                                </option>
                            </select>
                            <div v-if="form.errors.subject_id" class="text-red-500 text-xs mt-1">
                                {{ form.errors.subject_id }}
                            </div>
                        </div>

                        <input 
                            type="file" 
                            @input="form.document = $event.target.files[0]"
                            ref="fileInput" 
                            @change="onFileChange"
                            accept=".pdf,.ppt,.pptx"
                            class="block w-full text-sm text-gray-500"
                        />
                        
                        <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                            {{ form.progress.percentage }}%
                        </progress>
                        <div v-if="form.errors.document" class="text-red-500 text-sm">{{ form.errors.document }}</div>
                        <!-- <button type="submit" :disabled="form.processing" class="bg-blue-500 text-white px-4 py-2 rounded">
                            {{ form.processing ? 'Saving...' : 'Save Lesson' }}
                        </button> -->
                        <button type="submit" :disabled="form.processing" class="bg-blue-500 text-white px-4 py-2 rounded">
                            {{ isEditing ? 'Update Lesson' : 'Save Lesson' }}
                        </button>

                        <button v-if="isEditing" type="button" @click="cancelEdit" class="bg-gray-400 text-white px-4 py-2 rounded">
                            Cancel
                        </button>
                    </form>
                </div>

                <!-- LIST OF LESSONS -->
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="mb-4 font-bold">Existing Lessons</h3>
                    <div v-for="lesson in lessons" :key="lesson.id" class="border-b py-2">
                        <span class="font-medium">{{ lesson.title }}</span>
                        <span v-if="lesson.quiz" class="ml-4 text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Has Quiz</span>
                        <div v-if="lesson.file_path" class="mt-2">
                            <span class="text-xs text-gray-500">Attachment: {{ lesson.file_name }}</span>

                            <button @click="processAI(lesson.id)" class="ml-2 bg-purple-500 text-white text-xs px-2 py-1 rounded">
                                Generate AI Summary
                            </button>
                        </div>
                        <div class="flex gap-2">
                        <a :href="route('teacher.lessons.download', lesson.id)"
                            class="btn-download"
                            target="_blank"
                        >
                            Download
                        </a>
                        </div>
                        <button @click="startEdit(lesson)" class="ml-4 text-blue-500 underline">Edit</button>
                        <button @click="deleteLesson(lesson)" class="bg-red-500 text-white ml-4 px-4 py-2 rounded">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>