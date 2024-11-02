<!-- src/views/user/mymessage.vue -->

<template>
    <div class="min-h-[70%] flex flex-col gap-2 md:flex-row items-start md:items-center justify-center">
        <!-- User List -->
        <div class="w-full md:w-[30%] border-2 border-indigo-200 rounded-lg p-4 mb-4 md:mb-0">
            <div class="flex flex-col gap-5">
                <!-- Search -->
                <div>
                    <div class="w-full border border-indigo-600 rounded-full py-1 px-2">
                        <input type="text" v-model="searchQuery" class="focus-visible:outline-none w-full"
                            placeholder="Tìm kiếm " />
                    </div>
                </div>
                <!-- Item List -->
                <div class="flex flex-col gap-2 h-[60vh] md:h-[70vh] overflow-y-auto scrollable-container">
                    <div v-for="user in filteredUsers" :key="user.id" @click="selectUser(user)"
                        class="flex cursor-pointer gap-2 p-2 rounded-md bg-indigo-50 hover:bg-indigo-100">
                        <img class="w-12 h-12 rounded-md object-cover" :src="user.avatar || defaultAvatar" alt="" />
                        <div class="gap-2 w-full">
                            <div class="flex justify-between items-center">
                                <h3 class="text-sm font-medium">{{ user.first_name }} {{ user.last_name }}</h3>
                                <span class="text-[10px] text-gray-600">{{ user.latest_message_time }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <p class="text-sm truncate w-24 text-gray-600">
                                    {{ user.latest_message || 'No messages yet' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Window -->
        <div class="w-full md:w-[70%] bg-white rounded-lg p-4">
            <header v-if="selectedUser" class="flex gap-3 items-center mb-4">
                <img :src="selectedUser.avatar || defaultAvatar" class="w-12 h-12 rounded-full" alt="" />
                <div>
                    <h3 class="text-2xl leading-5 font-semibold text-gray-900">
                        {{ selectedUser.first_name }} {{ selectedUser.last_name }}
                    </h3>
                    <span class="text-sm text-gray-600">{{ selectedUser.email }}</span>
                </div>
            </header>

            <div v-if="selectedUser" class="bg-indigo-50 h-[60vh] md:h-[70vh] p-2 rounded-lg relative overflow-hidden">
                <div class="h-full overflow-y-auto flex flex-col p-4 space-y-4 pb-[80px]">
                    <div v-for="message in messages" :key="message.id" :class="messageClass(message.sender_id)">
                        <div v-if="message.sender_id === user?.id"
                            class="bg-blue-500 text-white rounded-lg p-3 max-w-xs self-end">
                            <p>{{ message.message }}</p>
                            <span class="text-xs text-gray-200">{{ formatDate(message.created_at) }}</span>
                        </div>
                        <div v-else class="bg-white rounded-lg p-3 max-w-xs">
                            <p>{{ message.message }}</p>
                            <span class="text-xs text-gray-600">{{ formatDate(message.created_at) }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center mt-4 rounded-xl bg-gray-50 p-2 shadow-inner sticky bottom-0">
                    <input type="file" ref="fileInput" class="hidden" @change="handleImageUpload" />
                    <button @click="triggerFileUpload" class="bg-transparent hover:bg-gray-200 p-2 rounded-full">
                        <DocumentIcon class="h-6 w-6 text-gray-500" />
                    </button>
                    <textarea v-model="newMessage"
                        class="flex-1 border-none outline-none bg-transparent resize-none p-2 max-h-32 overflow-y-auto"
                        rows="1" placeholder="Type a message..." @input="autoResize"></textarea>
                    <button @click="sendMessage" class="bg-blue-500 text-white p-2 rounded-full hover:bg-blue-600 ml-2">
                        <PaperAirplaneIcon class="w-5 h-5 transform rotate-45" />
                    </button>
                </div>
            </div>

            <div v-else class="flex items-center justify-center h-full">
                <p class="text-gray-500">Select a user to start chatting</p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, watch, nextTick } from 'vue';
import { useAuthStore } from '@/store/auth'; // Đường dẫn đúng đến store
import api from '@/services/axiosConfig'; // Đường dẫn đến file cấu hình axios
import echo from '@/plugins/echo'; // Đường dẫn đến file cấu hình echo
import { PaperAirplaneIcon, DocumentIcon } from '@heroicons/vue/20/solid';

// Sử dụng composable auth
const authStore = useAuthStore();

// Biến avatar mặc định
const defaultAvatar = 'https://th.bing.com/th/id/OIP.caOW02fThowCRbiUUxiQbwHaEN?rs=1&pid=ImgDetMain';

// Danh sách người dùng
const users = ref<any[]>([]);

// Tìm kiếm
const searchQuery = ref('');

// Người dùng được chọn để chat
const selectedUser = ref<any | null>(null);

// Tin nhắn
const messages = ref<any[]>([]);

// Tin nhắn mới
const newMessage = ref('');

// File input ref
const fileInput = ref<HTMLInputElement | null>(null);

// Lấy danh sách người dùng
const fetchUsers = async () => {
    try {
        const response = await api.get('/auth/chat/users');
        users.value = response.data.data;
    } catch (error) {
        console.error('Error fetching users:', error);
    }
};

const currentUser = computed(() => authStore.state.user)

const messageClass = (senderId: number) => {
    return senderId === user.value?.id ? 'flex justify-end' : 'flex';
};

// Lọc danh sách người dùng theo tìm kiếm
const filteredUsers = computed(() => {
    if (!searchQuery.value) return users.value;
    return users.value.filter((user) =>
        `${user.first_name} ${user.last_name}`.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

// Chọn người dùng để chat
const selectUser = async (userItem: any) => {
    selectedUser.value = userItem;
    // console.log(`Selected user: ${userItem.id}`); // Kiểm tra ID người nhận
    await fetchMessages(userItem.id);
    setupBroadcasting();
};


// Hàm xử lý nhận sự kiện broadcast
const setupBroadcasting = () => {
    if (!selectedUser.value || !authStore.state.user) return;
    console.log('current user ' + authStore.state.user.id);
    const channelName = `chat.${authStore.state.user.id}`;
    const channel = echo.private(channelName);
    console.log(`Subscribed to channel: ${channelName}`);
    channel.listen('MessageSent', (e: any) => { // Sử dụng tên sự kiện đúng với backend
        console.log('Received MessageSent event:', e.message);
        if (e.message.sender_id === selectedUser.value.id) {
            messages.value.push(e.message);
            scrollToBottom();
        }
    });
    // Cleanup khi component bị hủy hoặc khi người dùng đổi cuộc trò chuyện
    return () => {
        echo.leave(channelName);
    };
};


// Lấy tin nhắn giữa người dùng hiện tại và người dùng được chọn
const fetchMessages = async (receiverId: number) => {
    try {
        const response = await api.get(`/auth/message/private/${receiverId}`);
        messages.value = response.data.data;
        scrollToBottom();
    } catch (error) {
        console.error('Error fetching messages:', error);
    }
};

// Gửi tin nhắn
const sendMessage = async () => {
    if (newMessage.value.trim() === '' || !selectedUser.value) return;

    try {
        const payload = { message: newMessage.value };
        const response = await api.post(`/auth/messages/${selectedUser.value.id}`, payload);
        messages.value.push(response.data.data);
        newMessage.value = '';
        scrollToBottom();
    } catch (error) {
        console.error('Error sending message:', error);
    }
};

// Gửi hình ảnh
const sendImage = async (imageData: string) => {
    if (!selectedUser.value) return;

    try {
        const payload = { message: 'Image', image: imageData };
        const response = await api.post(`/messages/${selectedUser.value.id}`, payload);
        messages.value.push(response.data.data);
        scrollToBottom();
    } catch (error) {
        console.error('Error sending image:', error);
    }
};


// Cuộn xuống cuối cùng của khung tin nhắn
const scrollToBottom = () => {
    nextTick(() => {
        const container = document.querySelector('.overflow-y-auto');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    });
};

// Xử lý tự động thay đổi kích thước textarea
const autoResize = (event: any) => {
    const textarea = event.target;
    textarea.style.height = 'auto';
    textarea.style.height = `${textarea.scrollHeight}px`;
};

// Xử lý upload hình ảnh
const handleImageUpload = () => {
    if (fileInput.value && fileInput.value.files && fileInput.value.files[0]) {
        const file = fileInput.value.files[0];
        const reader = new FileReader();
        reader.onload = () => {
            // Gửi hình ảnh lên server hoặc xử lý theo nhu cầu
            // Ví dụ: gửi dưới dạng base64
            sendImage(reader.result as string);
        };
        reader.readAsDataURL(file);
        fileInput.value.value = ''; // Reset input
    }
};

// Kích hoạt upload file
const triggerFileUpload = () => {
    fileInput.value?.click();
};

// Định dạng ngày giờ
const formatDate = (date: string) => {
    const options: Intl.DateTimeFormatOptions = { hour: '2-digit', minute: '2-digit' };
    return new Date(date).toLocaleDateString(undefined, options);
};

// Gán người dùng hiện tại từ store
const user = computed(() => authStore.state.user);

// Khởi tạo
onMounted(async () => {
    if (authStore.state.token) {
        // await authStore.fetchCurrentUser();
        await fetchUsers();
    } else {
        // Nếu không có token, redirect đến trang login
        window.location.href = '/login';
    }
});

// Watch selectedUser để setup broadcasting khi người dùng chọn thay đổi
watch(selectedUser, () => {
    // Có thể thêm logic để cleanup các kênh trước nếu cần
});
</script>

<style scoped>
.scrollable-container {
    scrollbar-width: thin;
    scrollbar-color: #a0aec0 transparent;
}

.scrollable-container::-webkit-scrollbar {
    width: 6px;
}

.scrollable-container::-webkit-scrollbar-track {
    background: transparent;
}

.scrollable-container::-webkit-scrollbar-thumb {
    background-color: #a0aec0;
    border-radius: 3px;
}
</style>