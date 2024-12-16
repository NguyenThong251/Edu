<template>
    <div class="flex flex-col h-screen bg-gray-50">
        <!-- Header -->
        <header class="bg-white shadow p-4 flex items-center justify-between">
            <div class="text-lg font-semibold text-gray-700 flex items-center gap-2">
                <ChatBubbleLeftRightIcon class="w-6 h-6 text-indigo-600" />
                Chat Bot
            </div>
            <button @click="clearChat" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                Xóa lịch sử
            </button>
        </header>

        <!-- Chat Content -->
        <main id="chat-content" class="flex-1 p-4 overflow-y-auto space-y-4">
            <div v-for="(msg, index) in chatHistory" :key="index" class="flex items-start"
                :class="{ 'justify-end': msg.sender === 'user' }">
                <!-- Bot Message -->
                <template v-if="msg.sender === 'bot'">
                    <div class="flex items-start gap-2">
                        <img src="https://cdn-icons-png.flaticon.com/512/4712/4712027.png" alt="Bot"
                            class="w-8 h-8 rounded-full" />
                        <div class="bg-indigo-100 text-indigo-800 p-3 rounded-xl max-w-md">
                            <p v-html="formatMessage(msg.text)"></p>
                        </div>
                    </div>
                </template>

                <!-- User Message -->
                <template v-else>
                    <div class="flex items-center gap-2">
                        <div class="bg-blue-500 text-white p-3 rounded-xl shadow max-w-md">
                            <p>{{ msg.text }}</p>
                        </div>
                        <img src="https://cdn-icons-png.flaticon.com/512/1144/1144760.png" alt="User"
                            class="w-8 h-8 rounded-full" />
                    </div>
                </template>
            </div>
        </main>

        <!-- Footer/Input -->
        <footer class="p-4 bg-white flex items-center gap-3">
            <input v-model="message" type="text" placeholder="Nhập tin nhắn của bạn..."
                class="flex-1 p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                @keyup.enter="sendMessage" />
            <button @click="sendMessage" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                Gửi
            </button>
        </footer>
    </div>
</template>

<script setup lang="ts">
import { onMounted, ref, watch } from "vue";
import { ChatBubbleLeftRightIcon } from "@heroicons/vue/24/solid";
import { useChatbot } from "@/store/chatbots";
import { storeToRefs } from "pinia";
const chatbotStore = useChatbot()
const { listHistoryChatBot } = storeToRefs(chatbotStore)
const { fetchChatHistory,
    saveChat } = chatbotStore
// Giao diện tin nhắn
interface Message {
    sender: "user" | "bot";
    text: string;
}

const message = ref<string>("");
const chatHistory = ref<Message[]>([]);

// Gửi tin nhắn
const sendMessage = async () => {
    if (!message.value.trim()) return;

    // Thêm tin nhắn user
    chatHistory.value.push({ sender: "user", text: message.value });
    scrollToBottom();

    // Gửi tin nhắn lên server và nhận phản hồi
    await saveChat(message.value);
    const newBotResponse = listHistoryChatBot.value?.conversation.slice(-1)[0];
    if (newBotResponse && newBotResponse.role === "assistant") {
        chatHistory.value.push({
            sender: "bot",
            text: newBotResponse.content,
        });
    }

    message.value = "";
};

// Xóa lịch sử hội thoại
const clearChat = () => {
    chatHistory.value = [];
};

// Cuộn xuống dưới cùng
const scrollToBottom = () => {
    const chatContent = document.getElementById("chat-content");
    if (chatContent) {
        chatContent.scrollTop = chatContent.scrollHeight;
    }
};

// Định dạng tin nhắn chứa link
const formatMessage = (text: string) => {
    return text.replace(
        /(http:\/\/[^\s]+)/g,
        '<a href="$1" target="_blank" class="text-blue-500 underline">$1</a>'
    );
};

watch(chatHistory, scrollToBottom);
onMounted(() => {
    fetchChatHistory()
    if (listHistoryChatBot.value?.conversation) {
        chatHistory.value = listHistoryChatBot.value.conversation.map((conv: any) => ({
            sender: conv.role === "user" ? "user" : "bot",
            text: conv.content,
        }));
    }
})
</script>

<style scoped>
#chat-content {
    max-height: calc(100vh - 140px);
    overflow-y: auto;
}
</style>