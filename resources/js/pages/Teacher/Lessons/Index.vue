<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    lessons: Array,
    subjects: Array // Para sa validation selection array menu sa controller natin
});

// Tracker state para malaman kung nasa Edit Mode o Create Mode si teacher
const isEditing = ref(false);
const currentLessonId = ref(null);

const form = useForm({
    title: '',
    content: '',
    subject_id: '',
    document: null // Para sa storage validation uploading feature niyo
});

// File change handle logic wrapper
const onFileChange = (e) => {
    form.document = e.target.files[0];
};

// Ilo-load ang lumang records sa form inputs kapag pinindot ang edit pencil icon
const startEdit = (lesson) => {
    isEditing.value = true;
    currentLessonId.value = lesson.id;
    
    form.title = lesson.title;
    form.content = lesson.content || '';
    form.subject_id = lesson.subject_id || '';
    form.document = null; // Blanko muna dahil opsyonal lang magpalit ng bagong file
};

// Ika-cancel ang kasalukuyang pag-e-edit para bumalik sa Create Form view
const cancelEdit = () => {
    isEditing.value = false;
    currentLessonId.value = null;
    form.reset();
};

// Master execution pipeline trigger para sa create/update logic
const submitLesson = () => {
    if (isEditing.value) {
        form.transform((data) => ({
            ...data,
            _method: 'PUT',
        })).post(route('teacher.lessons.update', currentLessonId.value), {
            forceFormData: true,
            queryParams: { _method: 'PUT' },
            onSuccess: () => {
                cancelEdit();
                alert('Course material parameters updated successfully!');
            }
        // Gagamit tayo ng post with _method=PUT para gumana nang ligtas ang file uploads sa Laravel update route
        });
    } else {
        // Standard create store route path trigger
        form.post(route('teacher.lessons.store'), {
            forceFormData: true,
            onSuccess: () => {
                form.reset();
                alert('New course syllabus document deployed successfully!');
            }
        });
    }
};

// Purge action trigger hooked direkta sa backend destroy controller logic mo
const deleteLesson = (lesson) => {
    if (confirm(`Are you absolutely sure you want to completely purge "${lesson.title}" from your database repository?`)) {
        form.delete(route('teacher.lessons.destroy', lesson.id), {
            onSuccess: () => {
                alert('Lesson record cleared successfully.');
                if (currentLessonId.value === lesson.id) cancelEdit();
            }
        });
    }
};

// Placeholder structure para sa feature handle ng AI engine parameters mo
const processAI = (id) => {
    alert(`Deploying AI Summary Engine tracking request for Lesson ID: ${id}`);
};
</script>

<template>
    <Head title="Course Materials Management Hub" />

    <div class="min-h-screen bg-gradient-to-br from-[#0c1a3a] via-[#1e3a8a] via-[#1e40af] to-[#3730a3] text-gray-900 font-sans pb-16">

        <main class="max-w-7xl mx-auto px-4 lg:px-6 pt-10 space-y-6">
            <div v-if="$page.props.flash && $page.props.flash.message" class="bg-emerald-50 border border-emerald-300 text-emerald-800 p-4 rounded-xl shadow-sm flex items-center gap-2 text-sm font-medium">
                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                {{ $page.props.flash.message }}
            </div>
            
            <section class="bg-white rounded-[24px] p-6 shadow-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-lg shadow-sm">
                        <i class="fa-solid fa-book-bookmark text-sm"></i>
                    </div>
                    <div>
                        <h1 class="text-xl md:text-2xl font-bold text-blue-950">Academic Content Warehouse</h1>
                        <p class="text-xs text-gray-500">Formulate text lectures, link subject metrics, and attach core reference files.</p>
                    </div>
                </div>
            </section>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                <section class="bg-white p-6 rounded-[22px] shadow-xl border border-gray-100 lg:col-span-5 space-y-4">
                    <div class="border-b border-gray-100 pb-3 flex justify-between items-center">
                        <h3 class="font-bold text-blue-950 text-md flex items-center gap-2">
                            <i class="fa-solid" :class="isEditing ? 'fa-pen-to-square text-amber-600' : 'fa-circle-plus text-purple-600'"></i>
                            {{ isEditing ? 'Modify Lesson Parameters' : 'Deploy New Syllabus Unit' }}
                        </h3>
                        <button v-if="isEditing" @click="cancelEdit" class="text-[10px] font-bold text-gray-400 hover:text-gray-600 border border-gray-200 px-2 py-1 rounded-md bg-gray-50 transition">
                            Cancel Edit Mode
                        </button>
                    </div>

                    <form @submit.prevent="submitLesson" class="space-y-4 text-sm">
                        
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-600 uppercase tracking-wider">Select Assigned Course Folder</label>
                            <select v-model="form.subject_id" class="w-full border border-gray-200 p-3 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-purple-600 bg-white transition" required>
                                <option value="" disabled>-- Select Database Course Unit --</option>
                                <option v-for="subject in subjects" :key="subject.id" :value="subject.id">
                                    {{ subject.name }} ({{ subject.code || 'N/A' }})
                                </option>
                            </select>
                            <div v-if="form.errors.subject_id" class="text-red-500 text-xs mt-1">{{ form.errors.subject_id }}</div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-600 uppercase tracking-wider">Lecture / Module Title</label>
                            <input v-model="form.title" type="text" placeholder="e.g., Chapter 1: Introduction to Data Structures" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-purple-600 transition" required />
                            <div v-if="form.errors.title" class="text-red-500 text-xs mt-1">{{ form.errors.title }}</div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-600 uppercase tracking-wider">Lecture Core Content Text</label>
                            <textarea v-model="form.content" rows="7" placeholder="Input core summary definitions, instructions, or reading copy blocks here..." class="w-full border border-gray-200 p-3 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-purple-600 transition" required></textarea>
                            <div v-if="form.errors.content" class="text-red-500 text-xs mt-1">{{ form.errors.content }}</div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-600 uppercase tracking-wider">
                                Reference Asset Document <span class="text-gray-400 normal-case">(PDF, PPT, PPTX - Max 20MB)</span>
                            </label>
                            <input type="file" @change="onFileChange" class="w-full text-xs text-gray-500 bg-gray-50 border border-gray-200 p-2 rounded-xl file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 cursor-pointer" />
                            <p v-if="isEditing" class="text-[10px] text-amber-600 italic">Leave empty unless you intend to completely replace the current file asset.</p>
                            <div v-if="form.errors.document" class="text-red-500 text-xs mt-1">{{ form.errors.document }}</div>
                        </div>

                        <div class="pt-2">
                            <button type="submit" :disabled="form.processing" class="w-full bg-gradient-to-r text-white font-bold py-3 px-4 rounded-xl shadow-md transition duration-150 hover:-translate-y-0.5 disabled:opacity-50 flex items-center justify-center gap-2 text-xs uppercase tracking-wider"
                                :class="isEditing ? 'from-amber-600 to-amber-500 hover:from-amber-700 hover:to-amber-600 shadow-amber-600/10' : 'from-purple-700 to-purple-600 hover:from-purple-800 hover:to-purple-700 shadow-purple-700/10'">
                                <i class="fa-solid" :class="form.processing ? 'fa-spinner animate-spin' : (isEditing ? 'fa-floppy-disk' : 'fa-cloud-arrow-up')"></i>
                                {{ form.processing ? 'Saving Data...' : (isEditing ? 'Commit Changes' : 'Upload Material to Course') }}
                            </button>
                        </div>

                    </form>
                </section>

                <section class="bg-white p-6 rounded-[22px] shadow-xl border border-gray-100 lg:col-span-7 space-y-4">
                    <div class="border-b border-gray-100 pb-3">
                        <h3 class="font-bold text-blue-950 text-md flex items-center gap-2">
                            <i class="fa-solid fa-layer-group text-blue-600"></i> Active Course Materials Catalog
                        </h3>
                    </div>

                    <div class="max-h-[515px] overflow-y-auto space-y-3 pr-1.5 custom-scrollbar">
                        <div v-for="lesson in lessons" :key="lesson.id" class="p-4 border rounded-xl bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition duration-150"
                            :class="currentLessonId === lesson.id ? 'border-amber-400 bg-amber-50/20 ring-1 ring-amber-300' : 'border-gray-100 hover:border-blue-200'">
                            
                            <div class="space-y-1 min-w-0 flex-1">
                                <div class="flex items-center flex-wrap gap-2">
                                    <h4 class="font-bold text-sm text-gray-900 leading-tight truncate">{{ lesson.title }}</h4>
                                    <span v-if="lesson.quiz" class="text-[10px] font-bold bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded border border-emerald-100 flex items-center gap-0.5 whitespace-nowrap">
                                        <i class="fa-solid fa-circle-nodes"></i> Linked Quiz
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 line-clamp-1 pr-4">{{ lesson.content || 'No text summary specified.' }}</p>
                                
                                <div v-if="lesson.file_path" class="pt-1.5 flex items-center gap-2 flex-wrap">
                                    <span class="text-[10px] font-bold text-blue-950 bg-blue-50/70 border border-blue-100 rounded-lg px-2 py-1 max-w-[240px] truncate block" :title="lesson.file_name">
                                        📁 {{ lesson.file_name || 'Attached Asset File' }}
                                    </span>
                                    <button @click="processAI(lesson.id)" class="bg-purple-100 text-purple-700 text-[10px] font-bold px-2 py-1 rounded-lg border border-purple-200 flex items-center gap-1 hover:bg-purple-200 transition">
                                        <i class="fa-solid fa-wand-magic-sparkles"></i> AI Summary
                                    </button>
                                </div>
                            </div>

                            <div class="flex items-center gap-1.5 self-end sm:self-center shrink-0">
                                <a v-if="lesson.file_path" :href="route('teacher.lessons.download', lesson.id)" target="_blank" class="p-2.5 bg-white text-gray-600 border border-gray-100 rounded-xl text-xs font-semibold hover:bg-blue-50 hover:text-blue-600 shadow-sm transition" title="Download Asset">
                                    <i class="fa-solid fa-download"></i>
                                </a>
                                <button @click="startEdit(lesson)" class="p-2.5 bg-white text-amber-600 border border-gray-100 rounded-xl text-xs font-semibold hover:bg-amber-50 hover:border-amber-200 shadow-sm transition" title="Modify Lesson Data">
                                    <i class="fa-solid fa-pen text-[11px]"></i>
                                </button>
                                <button @click="deleteLesson(lesson)" class="p-2.5 bg-white text-red-600 border border-gray-100 rounded-xl text-xs font-semibold hover:bg-red-50 hover:border-red-200 shadow-sm transition" title="Purge Record">
                                    <i class="fa-solid fa-trash-can text-[11px]"></i>
                                </button>
                            </div>
                        </div>

                        <div v-if="lessons.length === 0" class="text-center py-16 text-gray-400 italic text-xs space-y-2 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                            <i class="fa-solid fa-folder-open text-2xl text-gray-300 block"></i>
                            <span>No course content materials deployed to your portal registry database.</span>
                        </div>
                    </div>
                </section>

            </div>
        </main>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 5px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 9999px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #cbd5e1;
}
</style>