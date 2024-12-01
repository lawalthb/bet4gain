<template>
  <div class="profile-container bg-gray-800 p-6 rounded-lg shadow-lg max-w-4xl mx-auto">
    <!-- User Stats Overview -->
    <div class="bg-gray-700 p-6 rounded-lg mb-8">
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="stat-card">
          <div class="text-gray-400 text-sm">Balance</div>
          <div class="text-xl font-bold text-green-400">₦{{ user.wallet_balance }}</div>
        </div>
        <div class="stat-card">
          <div class="text-gray-400 text-sm">Total Bets</div>
          <div class="text-xl font-bold text-blue-400">{{ user.total_bets || 0 }}</div>
        </div>
        <div class="stat-card">
          <div class="text-gray-400 text-sm">Wins</div>
          <div class="text-xl font-bold text-yellow-400">{{ user.total_wins || 0 }}</div>
        </div>
        <div class="stat-card">
          <div class="text-gray-400 text-sm">Member Since</div>
          <div class="text-xl font-bold text-purple-400">{{ formatDate(user.created_at) }}</div>
        </div>
      </div>
    </div>

    <div class="grid md:grid-cols-2 gap-8">
      <!-- Personal Information -->
      <div class="bg-gray-700 p-6 rounded-lg">
        <h3 class="text-xl font-bold mb-6 text-white">Personal Information</h3>
        <form @submit.prevent="updateProfile" class="space-y-4">
          <div>
            <label class="text-gray-300">Name</label>
            <input type="text" v-model="user.name" class="form-input">
          </div>
          <div>
            <label class="text-gray-300">Email</label>
            <input type="email" v-model="user.email" class="form-input" disabled>
          </div>
          <div>
            <label class="text-gray-300">Phone</label>
            <input type="tel" v-model="user.phone" class="form-input">
          </div>
          <button type="submit" class="btn-primary">Update Profile</button>
        </form>
      </div>

      <!-- Bank Account Information -->
      <div class="bg-gray-700 p-6 rounded-lg">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-bold text-white">Bank Account</h3>
        <button
            v-if="user.account_number"
            @click="showUpdateForm = !showUpdateForm"
            class="text-sm bg-blue-600 px-3 py-1 rounded hover:bg-blue-700 transition"
        >
            {{ showUpdateForm ? 'Cancel' : 'Update Bank' }}
        </button>
    </div>

    <!-- Show existing bank details if available and not updating -->
    <div v-if="user.account_number && !showUpdateForm" class="mb-6 p-4 bg-gray-600 rounded-lg">
        <div class="space-y-2">
            <div class="flex justify-between">
                <span class="text-gray-400">Bank Name:</span>
                <span class="text-white">{{ user.bank_name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">Account Name:</span>
                <span class="text-white">{{ user.account_name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">Account Number:</span>
                <span class="text-white">{{ user.account_number }}</span>
            </div>
        </div>
    </div>

    <!-- Bank Account Form -->
    <bank-account-form v-if="!user.account_number || showUpdateForm" @bank-added="handleBankUpdate" />
</div>
  </div>
  </div>
</template>

<script>
import BankAccountForm from './BankAccountForm.vue';
import { format } from 'date-fns'

export default {
  components: {
    BankAccountForm
  },
  data() {
    return {
      user: {
        name: '',
        email: '',
        phone: '',
        wallet_balance: 0,
        bank_name: '',
        account_number: '',
        account_name: '',
        created_at: '',
        total_bets: 0,
            total_wins: 0,

      },
      showUpdateForm: false,
    }
  },
  mounted() {
    this.loadUserData()
  },
  methods: {
    loadUserData() {
        this.user = { ...window.auth.user }
    },
    formatDate(date) {
        if (!date) return 'N/A';
        return new Date(date).toLocaleDateString('en-US', {
            month: 'short',
            year: 'numeric'
        });
      },
    handleBankUpdate(bankDetails) {
        this.user = { ...this.user, ...bankDetails };
        this.showUpdateForm = false;
    }
}
}
</script>

<style scoped>
.form-input {
  @apply w-full p-3 rounded bg-gray-600 border border-gray-500 text-white mt-1;
}

.btn-primary {
  @apply w-full bg-green-600 p-3 rounded-lg font-medium text-white hover:bg-green-700 transition-colors duration-200;
}

.stat-card {
  @apply p-4 bg-gray-600 rounded-lg;
}
</style>
