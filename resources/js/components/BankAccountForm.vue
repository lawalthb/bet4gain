<template>
    <div class="bank-form">
        <select v-model="bankCode" class="form-select">
            <option value="">Select Bank</option>
            <option v-for="bank in banks" :key="bank.code" :value="bank.code">
                {{ bank.name }}
            </option>
        </select>

        <input
            type="text"
            v-model="accountNumber"
            placeholder="Account Number"
            class="form-input"
            maxlength="10"
        />

        <button @click="addBankAccount" class="btn-submit">
            Add Bank Account
        </button>
    </div>
</template>

<script>
export default {
    data() {
        return {
            banks: [],
            bankCode: '',
            accountNumber: ''
        }
    },
    mounted() {
        this.loadBanks()
    },
    methods: {
        async loadBanks() {
            const response = await axios.get('/banks')
            this.banks = response.data
        },
        async addBankAccount() {
            try {
                await axios.post('/bank-account', {
                    bank_code: this.bankCode,
                    account_number: this.accountNumber
                })
                // Handle success
            } catch (error) {
                // Handle error
            }
        }
    }
}
</script>
