<script setup>
import { ref, nextTick, onMounted } from 'vue';
import axios from 'axios';


const props = defineProps({
    userRole: {
        type: String,
        default: 'student' // 'teacher' o 'student'
    },
    greetingMessage: {
        type: String,
        default: "Hello! I'm your GLMS AI assistant, how can i help?"
    }
});

const isChatOpen = ref(false);
const chatInput = ref('');
const isAiThinking = ref(false);
const chatMessagesContainer = ref(null);
const aiMessages = ref([]);
const currentConversationId = ref(null);


onMounted(async () => {
    try {
        const response = await axios.get('/teacher/ai-chat/history');

        currentConversationId.value = response.data.conversation_id;

        if (response.data.messages.length > 0){
            aiMessages.value = response.data.messages;
        } else {
            aiMessages.value.push({
                role: 'assistant',
                content: props.greetingMessage
            });
        }
    } catch (error){
        console.error('The history cannot be loaded', error);
    }
});

const autoScrollChat = async () => {
    await nextTick();
    if (chatMessagesContainer.value) {
        chatMessagesContainer.value.scrollTop = chatMessagesContainer.value.scrollHeight;
    }
};

const sendChatMessage = async () => {
    if (!chatInput.value.trim() || isAiThinking.value) return;

    const userText = chatInput.value;
    aiMessages.value.push({ role: 'user', content: userText });
    chatInput.value = '';
    isAiThinking.value = true;
    autoScrollChat();

    try {
        const response = await axios.post('/teacher/ai-chat', { 
            message: userText,
            conversation_id: currentConversationId.value,
            role: props.userRole 
        });

        if (!currentConversationId.value){
            currentConversationId.value = response.data.conversation_id;
        }
        aiMessages.value.push({ role: 'assistant', content: response.data.reply });
    } catch (error) {
        aiMessages.value.push({ role: 'assistant', content: "Sorry, I can't connect to the server right now." });
    } finally {
        isAiThinking.value = false;
        autoScrollChat();
    }
};
</script>

<template>
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
                    <h4 class="font-bold text-sm tracking-tight">
                        {{ userRole === 'teacher' ? 'GLMS AI Copilot' : 'GLMS AI Study Buddy' }}
                    </h4>
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
                    placeholder="Mag-tanong dito..." 
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
</template>

<style scoped>
.animate-slide-up {
    animation: slideUp 0.18s ease-out forwards;
}
@keyframes slideUp {
    from { opacity: 0; transform: translateY(15px) scale(0.96); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}
</style>