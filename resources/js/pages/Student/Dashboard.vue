<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import { ref, nextTick } from 'vue';
import axios from 'axios';

const props = defineProps({
    joinedSubjects: Array,
});

const form = useForm({
    subject_code: '',
});

const join = () => {
    form.post(route('student.join'), {
        onSuccess: () => form.reset(),
    });
};

// Isang helper function para magbigay ng angkop na icon depende sa pangalan ng subject
const getSubjectIcon = (subjectName) => {
    const name = subjectName.toLowerCase();
    if (name.includes('web')) return 'fa-code';
    if (name.includes('data') || name.includes('sql')) return 'fa-database';
    if (name.includes('prog') || name.includes('java') || name.includes('c#')) return 'fa-laptop-code';
    return 'fa-book'; // Default icon kung walang tumugma
};


// Mga tracking states para sa AI Chat UI
const isChatOpen = ref(false);
const chatInput = ref('');
const isAiThinking = ref(false);
const chatMessagesContainer = ref(null);

// Unang message na makikita ni teacher sa box
const aiMessages = ref([
    { role: 'assistant', content: 'Hello Teacher! Kumusta? May maitutulong ba ako sa pag-check ng data, lessons, o student quiz scores mo ngayon?' }
]);

// Function para mag-scroll down kusa kapag may bagong chat
const autoScrollChat = async () => {
    await nextTick();
    if (chatMessagesContainer.value) {
        chatMessagesContainer.value.scrollTop = chatMessagesContainer.value.scrollHeight;
    }
};

// Function para magpadala ng mensahe sa Laravel backend
const sendChatMessage = async () => {
    if (!chatInput.value.trim() || isAiThinking.value) return;

    const userText = chatInput.value;
    aiMessages.value.push({ role: 'user', content: userText });
    chatInput.value = '';
    isAiThinking.value = true;
    autoScrollChat();

    try {
        // Heto ang tatawaging Laravel Route controller mamaya
        const response = await axios.post('/teacher/ai-chat', { message: userText });
        aiMessages.value.push({ role: 'assistant', content: response.data.reply });
    } catch (error) {
        aiMessages.value.push({ role: 'assistant', content: 'Sensya na pre, nagkaroon ng error sa pagkonekta sa AI controller.' });
    } finally {
        isAiThinking.value = false;
        autoScrollChat();
    }
};
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-[#0c1a3a] via-[#1e3a8a] via-[#1e40af] via-[#1d4ed8] to-[#3730a3] text-gray-900 font-sans pb-12">
        
        <header class="h-[66px] bg-white border-b-3 border-blue-600 px-[5%] md:px-[9%] flex items-center justify-between shadow-lg sticky top-0 z-50">
            <div class="flex items-center gap-2.5">
                <img src="/LOGO.png" alt="School Logo" class="w-[30px] h-[30px] object-contain" />
                <h2 class="text-xl font-bold text-blue-900">GLMS</h2>
            </div>

            <nav class="hidden md:flex items-center gap-[34px]">
                <Link :href="route('student.dashboard')" class="text-blue-600 font-bold text-sm py-6 relative after:content-[''] after:absolute after:left-0 after:bottom-[-3px] after:w-full after:h-[3px] after:bg-blue-600 after:rounded-t">
                    Dashboard
                </Link>
                <a href="#" class="text-gray-600 hover:text-blue-600 text-sm font-semibold transition">My Courses</a>
                <a href="#" class="text-gray-600 hover:text-blue-600 text-sm font-semibold transition">Assignments</a>
                <a href="#" class="text-gray-600 hover:text-blue-600 text-sm font-semibold transition">Profile</a>
            </nav>

            <Link method="post" href="/logout" as="button" class="bg-gradient-to-r from-blue-800 to-blue-600 text-white px-4 py-2.5 rounded-lg text-xs font-semibold flex items-center gap-2 transition hover:bg-red-600 shadow-md">
                Logout
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
            </Link>
        </header>

        //Ai Chatbot
        <div class="fixed right-6 bottom-6 z-50 font-sans text-xs">
            <div @click="isChatOpen = !isChatOpen; if(isChatOpen) autoScrollChat();" 
                class="w-[58px] h-[58px] rounded-full bg-gradient-to-br from-blue-800 to-blue-600 text-white flex items-center justify-center text-xl shadow-2xl cursor-pointer hover:from-blue-900 hover:to-blue-800 transition transform active:scale-95">
                <i class="fa-solid" :class="isChatOpen ? 'fa-xmark' : 'fa-robot'"></i>
            </div>

            <div v-if="isChatOpen" class="absolute bottom-[72px] right-0 w-[350px] h-[450px] bg-white rounded-2xl shadow-2xl border border-gray-100 flex flex-col overflow-hidden animate-slide-up">
                
                <div class="p-4 bg-gradient-to-r from-[#0c1a3a] to-[#1e3a8a] text-white flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-white/10 flex items-center justify-center">
                        <i class="fa-solid fa-brain text-blue-300 text-xs"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm tracking-tight">GLMS AI Copilot</h4>
                        <p class="text-[10px] text-emerald-400 font-medium flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Active Helper
                        </p>
                    </div>
                </div>

                <div ref="chatMessagesContainer" class="flex-1 p-4 overflow-y-auto space-y-3 bg-gray-50/50">
                    <div v-for="(msg, idx) in aiMessages" :key="idx" class="flex flex-col" :class="msg.role === 'user' ? 'items-end' : 'items-start'">
                        <div class="max-w-[85%] p-3 rounded-2xl shadow-sm text-[11px] leading-relaxed font-medium"
                            :class="msg.role === 'user' ? 'bg-blue-600 text-white rounded-tr-none' : 'bg-white text-gray-800 border border-gray-100 rounded-tl-none'">
                            {{ msg.content }}
                        </div>
                    </div>

                    <div v-if="isAiThinking" class="flex items-center gap-1.5 text-gray-400 font-semibold text-[10px] bg-white border border-gray-100 p-2.5 rounded-xl rounded-tl-none w-max shadow-sm">
                        <i class="fa-solid fa-spinner animate-spin text-blue-600"></i> AI is working...
                    </div>
                </div>

                <form @submit.prevent="sendChatMessage" class="p-3 bg-white border-t border-gray-100 flex items-center gap-2">
                    <input 
                        v-model="chatInput" 
                        type="text" 
                        placeholder="Ask about materials, metrics or queries..." 
                        class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-blue-600 text-[11px] bg-gray-50/50"
                        :disabled="isAiThinking"
                    />
                    <button type="submit" :disabled="isAiThinking || !chatInput.trim()" 
                        class="p-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition disabled:opacity-40 shadow-sm flex items-center justify-center">
                        <i class="fa-solid fa-paper-plane text-[10px]"></i>
                    </button>
                </form>

            </div>
        </div>

        <main class="max-w-7xl mx-auto px-4 md:px-[9%] pt-11 space-y-7">

            <section class="bg-gradient-to-br from-blue-800 to-blue-600 text-white p-8 md:p-12 rounded-[24px] text-center md:text-left shadow-xl">
                <span class="bg-white/20 px-3 py-1 rounded-full text-xs tracking-wide">Student Learning Portal</span>
                <h1 class="text-3xl md:text-4xl font-bold my-[18px]">Welcome back, Student</h1>
                <p class="max-w-[600px] text-blue-100 text-sm md:text-base mb-5">Access your enrolled subjects, view lessons, and continue your learning progress.</p>
                <!-- <button class="bg-white text-blue-600 px-[18px] py-3 rounded-xl font-bold hover:-translate-y-0.5 transition duration-200 shadow-md active:scale-95">
                    Continue Learning
                </button> -->
            </section>

            <section class="grid grid-cols-2 md:grid-cols-4 gap-[18px]">
                <div class="bg-white p-5 rounded-[18px] shadow-md flex flex-col justify-between">
                    <i class="fa-solid fa-book-open text-blue-600 text-xl mb-3"></i>
                    <div>
                        <h4 class="text-gray-400 text-xs font-medium">Courses</h4>
                        <p class="text-2xl font-bold text-gray-900">{{ joinedSubjects.length }}</p>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-[18px] shadow-md flex flex-col justify-between">
                    <i class="fa-solid fa-circle-check text-blue-600 text-xl mb-3"></i>
                    <div>
                        <h4 class="text-gray-400 text-xs font-medium">Completed</h4>
                        <p class="text-2xl font-bold text-gray-900">4</p>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-[18px] shadow-md flex flex-col justify-between">
                    <i class="fa-solid fa-clock text-blue-600 text-xl mb-3"></i>
                    <div>
                        <h4 class="text-gray-400 text-xs font-medium">Hours</h4>
                        <p class="text-2xl font-bold text-gray-900">42</p>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-[18px] shadow-md flex flex-col justify-between">
                    <i class="fa-solid fa-file-lines text-blue-600 text-xl mb-3"></i>
                    <div>
                        <h4 class="text-gray-400 text-xs font-medium">Pending</h4>
                        <p class="text-2xl font-bold text-gray-900">2</p>
                    </div>
                </div>
            </section>

            <section class="bg-white p-6 rounded-[18px] shadow-md">
                <h2 class="text-lg font-bold mb-2 text-blue-900">Join a Subject Class</h2>
                <div class="flex flex-col sm:flex-row gap-2">
                    <input v-model="form.subject_code" type="text" placeholder="Enter Subject Code (e.g. AB1234)" class="border p-2.5 rounded-xl flex-1 uppercase tracking-wider font-semibold focus:outline-blue-600">
                    <button @click="join" :disabled="form.processing" class="bg-emerald-600 text-white px-5 py-2.5 rounded-xl font-bold hover:bg-emerald-700 transition disabled:opacity-50">
                        Join Class
                    </button>
                </div>
                <p v-if="form.errors.subject_code" class="text-red-500 text-xs mt-2 font-medium">{{ form.errors.subject_code }}</p>
            </section>

            <section class="space-y-4">
                <div class="flex justify-between items-center text-white">
                    <h3 class="text-xl font-bold">Enrolled Subjects</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-[18px]">
                    <div v-for="subject in joinedSubjects" :key="subject.id" class="bg-white rounded-[18px] p-[22px] border-l-[5px] border-blue-600 shadow-md flex items-start gap-4 hover:-translate-y-1 transition duration-200">
                        
                        <div class="w-11 h-11 min-w-[44px] rounded-[14px] bg-blue-100 text-blue-600 flex items-center justify-center text-lg">
                            <i :class="['fa-solid', getSubjectIcon(subject.name)]"></i>
                        </div>

                        <div class="flex flex-col justify-between h-full">
                            <div>
                                <h4 class="font-bold text-blue-950 text-base mb-1 line-clamp-1">{{ subject.name }}</h4>
                                <p class="text-gray-400 text-sm mb-3">Instructor: {{ subject.teacher ? subject.teacher.name : 'Unknown Teacher' }}</p>
                            </div>
                            <Link :href="route('student.subjects.show', subject.id)" class="text-blue-600 font-bold text-sm inline-flex items-center gap-1.5 hover:underline">
                                View Lessons <i class="fa-solid fa-arrow-right text-xs"></i>
                            </Link>
                        </div>
                    </div>

                    <div v-if="joinedSubjects.length === 0" class="col-span-full bg-white/10 text-white/70 border border-dashed border-white/20 p-8 rounded-[18px] text-center">
                        You haven't joined any subject classes yet. Enter a code above to get started!
                    </div>
                </div>
            </section>

            <section class="bg-white rounded-[18px] p-6 shadow-md space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-blue-900">Upcoming Assignments</h3>
                    <a href="#" class="text-blue-600 text-sm font-semibold hover:underline">View All</a>
                </div>

                <div class="space-y-2">
                    <div class="flex flex-col sm:flex-row justify-between sm:items-center p-3.5 rounded-xl hover:bg-gray-50 transition border border-gray-100 gap-2">
                        <div>
                            <h4 class="font-bold text-blue-900 text-sm md:text-base">Final Project: Web Portfolio</h4>
                            <p class="text-gray-400 text-xs">Introduction to Web Development</p>
                        </div>
                        <span class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-xs font-bold self-start sm:self-center">Due Tomorrow</span>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-between sm:items-center p-3.5 rounded-xl hover:bg-gray-50 transition border border-gray-100 gap-2">
                        <div>
                            <h4 class="font-bold text-blue-900 text-sm md:text-base">Database Activity: SQL Queries</h4>
                            <p class="text-gray-400 text-xs">Database Management</p>
                        </div>
                        <span class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-xs font-bold self-start sm:self-center">Due Soon</span>
                    </div>
                </div>
            </section>

        </main>
    </div>
</template>

<style>
nav a.active::after {
    content: "";
    position: absolute;
    left: 0;
    bottom: -3px;
    width: 100%;
    height: 3px;
    background: #1d4ed8;
    border-radius: 10px 10px 0 0;
}

.animate-slide-up {
    animation: slideUp 0.18s ease-out forwards;
}
@keyframes slideUp {
    from { opacity: 0; transform: translateY(15px) scale(0.96); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}
</style>