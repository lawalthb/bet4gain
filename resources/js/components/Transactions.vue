<template>
  <div class="transactions-container">
    <!-- Balance Display -->
    <div class="balance-card">
      <h2>Wallet Balance</h2>
      <h3>${{ userBalance }}</h3>
    </div>

    <!-- Action Buttons -->

    <div class="action-buttons">
      <button @click="showDepositForm = true" class="btn-deposit">Deposit</button>
      <button @click="showWithdrawForm = true" class="btn-withdraw">Withdraw</button>
    </div>

    <!-- Deposit Modal -->
    <div v-if="showDepositForm" class="modal">
      <div class="modal-content">
        <h3>Deposit Funds</h3>
        <form @submit.prevent="initiateDeposit">
          <div class="form-group">
            <label>Amount (₦)</label>
            <input type="number" v-model="depositAmount" min="1" required>
          </div>
          <button type="submit" class="btn-submit">Pay with Paystack</button>
          <button @click="showDepositForm = false" class="btn-cancel">Cancel</button>


        </form>



      </div>
    </div>

    <!-- Withdraw Modal -->
    <div v-if="showWithdrawForm" class="modal">
        <div v-if="errorMessage" class="error-message">
  {{ errorMessage }}
</div>
      <div class="modal-content">
        <h3>Withdraw Funds</h3>
        <form @submit.prevent="initiateWithdraw">
          <div class="form-group">
            <label>Amount (₦)</label>
            <input type="number" v-model="withdrawAmount" min="1" required>
          </div>
          <div class="fee-info">
            <p>Fee (20%): ${{ calculateFee }}</p>
            <p>You will receive: ${{ calculateNetAmount }}</p>
          </div>
          <div class="form-group">
            <label>Bank Name</label>
            <input type="text" v-model="bankName" required>
          </div>
          <div class="form-group">
            <label>Account Number</label>
            <input type="text" v-model="accountNumber" required>
          </div>
          <div class="form-group">
            <label>Account Name</label>
            <input type="text" v-model="accountName" required>
          </div>
          <button type="submit" class="btn-submit">Submit Withdrawal</button>
          <button @click="showWithdrawForm = false" class="btn-cancel">Cancel</button>
        </form>
      </div>
    </div>

    <!-- Transaction History -->
    <div class="transaction-history">
      <h3>Transaction History</h3>
      <table>
        <thead>
          <tr>
            <th>Date</th>
            <th>Type</th>
            <th>Amount</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="transaction in transactions" :key="transaction.id">
            <td>{{ formatDate(transaction.created_at) }}</td>
            <td>{{ transaction.type }}</td>
            <td :class="transaction.type === 'Deposit' ? 'text-green' : 'text-red'">
              {{ transaction.type === 'Deposit' ? '+' : '-' }}${{ transaction.amount }}
            </td>
            <td>{{ transaction.status }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      showDepositForm: false,
      showWithdrawForm: false,
      depositAmount: null,
      withdrawAmount: null,
      bankName: '',
      accountNumber: '',
      accountName: '',
      transactions: [],
      userBalance: 0
    }
  },

  computed: {
    calculateFee() {
      return this.withdrawAmount ? (this.withdrawAmount * 0.2).toFixed(2) : 0
    },
    calculateNetAmount() {
      return this.withdrawAmount ? (this.withdrawAmount * 0.8).toFixed(2) : 0
    }
  },

  methods: {
    async initiateDeposit() {
      try {
        const response = await axios.post('/deposit', {
          amount: this.depositAmount,
          payment_method: 'Paystack'
        })

        if (response.data.authorization_url) {
          const handler = PaystackPop.setup({
            key: process.env.MIX_PAYSTACK_PUBLIC_KEY,
            email: this.userEmail,
            amount: this.depositAmount * 100,
            ref: response.data.reference,
            callback: (response) => {
              this.loadTransactions()
              this.showDepositForm = false
            },
            onClose: () => {
              console.log('Payment window closed')
            }
          })
          handler.openIframe()
        }
      } catch (error) {
        console.error('Deposit failed:', error)
      }
    },

    async initiateWithdraw() {
      try {
        const response = await axios.post('/withdraw', {
          amount: this.withdrawAmount,
          bank_name: this.bankName,
          account_number: this.accountNumber,
          account_name: this.accountName
        })

        if (response.data.success) {
          this.loadTransactions()
          this.showWithdrawForm = false
        }
      } catch (error) {
    // Display error message from backend
    if (error.response?.data?.message) {
      this.errorMessage = error.response.data.message
    } else {
      this.errorMessage = 'Withdrawal failed. Please try again.'
    }
  }
    },

    async loadTransactions() {
      try {
        const response = await axios.get('/transactions')
        this.transactions = response.data.transactions
        this.userBalance = response.data.balance
      } catch (error) {
        console.error('Failed to load transactions:', error)
      }
    },

    formatDate(date) {
      return new Date(date).toLocaleDateString()
    }
  },

  mounted() {
    this.loadTransactions()
  }
}
</script>

<style scoped>
.transactions-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

.modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-content {
  background: white;
  padding: 20px;
  border-radius: 8px;
  width: 90%;
  max-width: 500px;
}

.form-group {
  margin-bottom: 15px;
}

.fee-info {
  margin: 15px 0;
  padding: 10px;
  background: #f5f5f5;
  border-radius: 4px;
}

.text-green { color: #4CAF50; }
.text-red { color: #f44336; }

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

th, td {
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

.btn-deposit, .btn-withdraw {
  padding: 10px 20px;
  margin: 10px;
  border-radius: 4px;
  cursor: pointer;
}

.btn-deposit {
  background: #4CAF50;
  color: white;
}

.btn-withdraw {
  background: #f44336;
  color: white;
}
.paystack-logo {
    max-width: 200px;
    height: auto;
    margin: 20px auto;
    display: block;
}
</style>


