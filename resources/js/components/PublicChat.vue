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
    this.loadInitialMessages();
    window.Echo.channel('public-chat')
      .listen('.Chats', (e) => {
        this.messages.push({
          user: e.user,
          message: e.message.chats,
          time: new Date().toLocaleTimeString(),
        });
         this.$nextTick(() => {
            this.scrollToBottom();
          });
      });
  },
    methods: {
    loadInitialMessages() {
      axios.get('/chat/messages')
        .then(response => {
          this.messages = response.data.map(msg => ({
            user: msg.user,
            message: msg.chats,
            time: new Date(msg.created_at).toLocaleTimeString()
          }));
        })
        .catch(error => {
          console.error('Error loading messages:', error);
        });
    },
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
            this.$nextTick(() => {
            this.scrollToBottom();
          });
        })
        .catch(error => {
          console.error('Error sending message:', error);
        })
        .finally(() => {
          this.sending = false;  // Re-enable button
        });
        },
     scrollToBottom() {
      const container = this.$el.querySelector('.messages-container');
      container.scrollTop = container.scrollHeight;
    },
    },

    watch: {
    messages() {
      this.$nextTick(() => {
        this.scrollToBottom();
      });
    }
  }
};
</script>

<style scoped>
.chat-container {
  border-radius: 8px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
  padding: 15px;
  max-width: 800px;
  margin: 20px auto;
  background: #1a1a1a;
  border: 1px solid #333;
}

.messages-container {
  max-height: 500px;
  overflow-y: auto;
  margin-bottom: 15px;
  padding: 10px;
  background: #242424;
  border-radius: 8px;
}

.message-bubble {
  margin-bottom: 12px;
  padding: 10px 15px;
  background: #2d2d2d;
  color: #e0e0e0;
  border-radius: 8px;
  border: 1px solid #3a3a3a;
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
  color: #00ff9d; /* Neon green for usernames */
}

.message-time {
  font-size: 0.8em;
  color: #666;
}

.message-content {
  color: #ffffff;
  line-height: 1.4;
  word-wrap: break-word;
}

.input-container {
  display: flex;
  gap: 10px;
  padding: 10px;
  background: #242424;
  border-radius: 8px;
}

.message-input {
  flex: 1;
  padding: 12px 15px;
  border: 1px solid #333;
  border-radius: 6px;
  background: #1a1a1a;
  color: #ffffff;
  font-size: 14px;
  transition: all 0.3s ease;
}

.message-input:focus {
  outline: none;
  border-color: #00ff9d;
  box-shadow: 0 0 5px rgba(0, 255, 157, 0.3);
}

.message-input::placeholder {
  color: #666;
}

.send-button {
  padding: 10px 20px;
  background: #00ff9d;
  color: #000000;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: bold;
  transition: all 0.3s ease;
}

.send-button:hover {
  background: #00cc7d;
  box-shadow: 0 0 10px rgba(0, 255, 157, 0.5);
}

.send-button:disabled {
  background: #1a1a1a;
  color: #666;
  cursor: not-allowed;
  box-shadow: none;
}

/* Custom Scrollbar */
.messages-container::-webkit-scrollbar {
  width: 6px;
}

.messages-container::-webkit-scrollbar-track {
  background: #1a1a1a;
}

.messages-container::-webkit-scrollbar-thumb {
  background: #333;
  border-radius: 3px;
}

.messages-container::-webkit-scrollbar-thumb:hover {
  background: #444;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(5px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Responsive Design */
@media (max-width: 600px) {
  .chat-container {
    margin: 10px;
    padding: 10px;
  }

  .message-bubble {
    font-size: 14px;
  }

  .send-button {
    padding: 8px 15px;
  }
}
</style>
