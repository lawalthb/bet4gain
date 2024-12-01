<template>
  <div class="bank-form">
    <div class="mb-6">
      <label class="text-gray-300 mb-2 block">Select Bank</label>
      <select
        v-model="bankCode"
        class="w-full p-3 rounded bg-gray-600 border border-gray-500 text-white"
      >
        <option value="">Choose your bank</option>
        <option v-for="bank in banks" :key="bank.code" :value="bank.code">
          {{ bank.name }}
        </option>
      </select>
    </div>

    <div class="mb-6">
      <label class="text-gray-300 mb-2 block">Account Number</label>
      <input
        type="text"
        v-model="accountNumber"
        class="w-full p-3 rounded bg-gray-600 border border-gray-500 text-white"
        placeholder="Enter 10-digit account number"
        maxlength="10"
      />
    </div>

    <button
      @click="addBankAccount"
      class="w-full bg-green-600 p-3 rounded-lg font-medium text-white hover:bg-green-700 transition-colors duration-200 flex items-center justify-center"
      :disabled="!isFormValid"
    >
      <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
      </svg>
      Add Bank Account
    </button>

    <!-- Status Messages -->
    <div v-if="status" :class="['mt-4 p-3 rounded', statusClass]">
      {{ status }}
    </div>
  </div>

</template>

<script>
export default {
  data() {
    return {
      banks: [],
      bankCode: '',
      accountNumber: '',
      status: '',
      statusType: ''
    }
  },
  computed: {
    isFormValid() {
      return this.bankCode && this.accountNumber.length === 10
    },
    statusClass() {
      return {
        'bg-green-600 text-white': this.statusType === 'success',
        'bg-red-600 text-white': this.statusType === 'error'
      }
    }
  },
  mounted() {
    this.loadBanks()
  },
  methods: {
    async loadBanks() {
      try {
        const response = await axios.get('/banks')
        this.banks = response.data
      } catch (error) {
        this.showStatus('Error loading banks', 'error')
      }
    },
    async addBankAccount() {
        if (!this.isFormValid) return

        try {
            const response = await axios.post('/bank-account', {
                bank_code: this.bankCode,
                account_number: this.accountNumber
            })

            this.showStatus('Bank account added successfully', 'success')
            this.$emit('bank-added', response.data.bank_details)
            this.resetForm()
        } catch (error) {
            this.showStatus(error.response?.data?.error || 'Failed to add bank account', 'error')
        }
    },
    showStatus(message, type) {
      this.status = message
      this.statusType = type
      setTimeout(() => {
        this.status = ''
        this.statusType = ''
      }, 3000)
    },
    resetForm() {
      this.bankCode = ''
      this.accountNumber = ''
    }
  }
}
</script>

<style scoped>
select, input {
  appearance: none;
  outline: none;
}

select:focus, input:focus {
  border-color: #4CAF50;
  box-shadow: 0 0 0 1px #4CAF50;
}

button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.bank-form {
  animation: fadeIn 0.3s ease-in;
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
</style>
