<template>
  <div class="chat-container">
    <div class="messages-container">
      <div class="message-bubble" v-for="(msg, index) in messages" :key="index">
        <div class="message-header">
          <span class="user-name">{{ msg.user.name }}</span>
          <span class="message-time">{{ msg.time || "Just now" }}</span>
        </div>
        <div class="message-content">{{ msg.message }}</div>
      </div>
    </div>

    <div class="input-container">
      <input
        class="message-input"
        v-model="newMessage"
        @keyup.enter="sendMessage"
        placeholder="Type your message..."
      />
      <button
        class="send-button"
        @click="sendMessage"
        :disabled="sending || !newMessage.trim()"
      >
        {{ sending ? "Sending..." : "Send" }}
      </button>
    </div>
  </div>
</template>

<script>
import Echo from 'laravel-echo';
import axios from 'axios';

export default {
  data() {
    return {
      messages: [],
      newMessage: '',
      sending: false,  // Add sending state
    };
  },
  mounted() {
    window.Echo.channel('public-chat')
      .listen('.Chats', (e) => {
        this.messages.push({
          user: e.user,
          message: e.message.chats,
          time: new Date().toLocaleTimeString(),
        });
      });
  },
  methods: {
      sendMessage() {

         if (!window.auth.isLoggedIn) {
        window.location.href = '/login';
        return;
      }
      if (this.newMessage.trim() === '' || this.sending) return; // Prevent empty messages or double send

      this.sending = true;  // Disable button

      axios.post('/chat/send', { message: this.newMessage })
        .then(() => {
          this.newMessage = '';
        })
        .catch(error => {
          console.error('Error sending message:', error);
        })
        .finally(() => {
          this.sending = false;  // Re-enable button
        });
    },
  },
};
</script>

<style scoped>
.chat-container {
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  padding: 20px;
  max-width: 800px;
  margin: 20px auto;
  background: #f8f9fa; /* Light background for contrast */
}

.messages-container {
  max-height: 500px;
  overflow-y: auto;
  margin-bottom: 15px;
  padding: 10px;
  background: #ffffff;
  border-radius: 12px;
  box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.1);
}

.message-bubble {
  margin-bottom: 12px;
  padding: 10px 15px;
  background: #e0e7ff; /* Subtle blue for message bubbles */
  color: #1a1a2e;
  border-radius: 12px;
  max-width: 80%;
  animation: fadeIn 0.3s ease-in;
}

.message-header {
  display: flex;
  justify-content: space-between;
  margin-bottom: 5px;
}

.user-name {
  font-weight: bold;
  color: #2a4365; /* Dark blue for user names */
}

.message-time {
  font-size: 0.8em;
  color: #718096; /* Gray color for timestamps */
}

.message-content {
  color: #1c1e21;
  line-height: 1.4;
  word-wrap: break-word;
}

.input-container {
  display: flex;
  gap: 10px;
  padding: 10px;
  background: #edf2f7; /* Light gray for the input container */
  border-radius: 24px;
}

.message-input {
  flex: 1;
  padding: 12px 15px;
  border: none;
  border-radius: 20px;
  background: #ffffff;
  font-size: 14px;
  color: #2a4365;
  transition: all 0.3s ease;
}

.message-input:focus {
  outline: none;
  box-shadow: 0 0 0 2px #2b6cb0; /* Blue outline on focus */
}

.send-button {
  padding: 10px 16px;
  background: #2b6cb0; /* Blue button color */
  color: #ffffff;
  border: none;
  border-radius: 20px;
  cursor: pointer;
  font-weight: bold;
  transition: background 0.3s ease;
}

.send-button:hover {
  background: #2c5282; /* Darker blue on hover */
}

button[disabled] {
  opacity: 0.6;
  cursor: not-allowed;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Custom Scrollbar */
.messages-container::-webkit-scrollbar {
  width: 8px;
}

.messages-container::-webkit-scrollbar-track {
  background: #e1e1e1;
  border-radius: 4px;
}

.messages-container::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 4px;
}

.messages-container::-webkit-scrollbar-thumb:hover {
  background: #555;
}

/* Responsive Styling */
@media (max-width: 600px) {
  .chat-container {
    padding: 15px;
    margin: 10px;
  }

  .message-bubble {
    font-size: 14px;
    padding: 8px 12px;
    margin-bottom: 10px;
  }

  .message-input {
    font-size: 13px;
    padding: 10px;
  }

  .send-button {
    font-size: 14px;
    padding: 8px 14px;
  }
}
</style>
