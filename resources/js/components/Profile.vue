<template>
  <div class="profile-container bg-gray-800 p-6 rounded-lg shadow-lg max-w-4xl mx-auto">
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
        <h3 class="text-xl font-bold mb-6 text-white">Bank Account</h3>
        <bank-account-form />
      </div>
    </div>
  </div>
</template>

<script>
import BankAccountForm from './BankAccountForm.vue'

export default {
  components: {
    BankAccountForm
  },
  data() {
    return {
      user: {
        name: '',
        email: '',
        phone: ''
      }
    }
  },
  mounted() {
    this.loadUserData()
  },
  methods: {
    loadUserData() {
      this.user = { ...window.auth.user }
    },
    async updateProfile() {
      try {
        const response = await axios.post('/profile/update', this.user)
        // Handle success
      } catch (error) {
        // Handle error
      }
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
</style>