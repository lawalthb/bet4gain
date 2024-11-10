<template>
  <div class="crash-game">
    <div class="game-stats">
      <div class="current-stats">
        <span>Players: {{ activePlayers }}</span>
        <span>Total Bets: ${{ totalBets }}</span>
      </div>
      <div class="previous-crashes">
        <span v-for="(crash, index) in previousCrashes"
              :key="index"
              :class="{ 'high-crash': crash > 2 }">
          {{ crash.toFixed(2) }}x
        </span>
      </div>
    </div>

    <div class="game-canvas">
      <canvas ref="gameCanvas"></canvas>
      <div class="game-overlay" :class="{ 'crashed': hasCrashed }">
        <template v-if="isGameActive">
          <div class="multiplier">{{ currentMultiplier.toFixed(2) }}x</div>
          <div class="rocket" :style="rocketStyle">🚀</div>
        </template>
        <div v-else-if="hasCrashed" class="crash-text">
          CRASHED AT {{ crashPoint.toFixed(2) }}x
        </div>
        <div v-else class="waiting-text">
          Next round in {{ countdown }}s
        </div>
      </div>
    </div>

    <div class="betting-panel">
      <div class="bet-info">
        <div class="balance">
          Balance: ${{ userBalance }}
        </div>
        <div class="potential-win" v-if="isGameActive">
          Potential Win: ${{ (betAmount * currentMultiplier).toFixed(2) }}
        </div>
      </div>

      <div class="betting-controls">
        <div class="bet-amount">
          <input
            type="number"
            v-model="betAmount"
            :disabled="isGameActive"
            min="1"
            :max="userBalance"
          />
          <div class="quick-amounts">
            <button @click="quickBet(5)">$5</button>
            <button @click="quickBet(10)">$10</button>
            <button @click="quickBet(50)">$50</button>
            <button @click="betHalf">1/2</button>
            <button @click="betDouble">2x</button>
            <button @click="betMax">Max</button>
          </div>
        </div>

        <div class="action-buttons">
          <button
            class="bet-button"
            @click="placeBet"
            :disabled="isGameActive || betAmount > userBalance"
            v-if="!hasActiveBet">
            Place Bet
          </button>
          <button
            class="cashout-button"
            @click="cashOut"
            :disabled="!canCashOut"
            v-else>
            Cash Out ({{ (betAmount * currentMultiplier).toFixed(2) }})
          </button>
        </div>
      </div>
    </div>

    <div class="auto-cashout">
      <label>
        <input type="checkbox" v-model="autoEnabled">
        Auto Cash Out at
      </label>
      <input
        type="number"
        v-model="autoCashoutPoint"
        :disabled="!autoEnabled"
        step="0.1"
        min="1.1"
      />x
    </div>
  </div>
</template>

<script>
import { ref, onMounted, onUnmounted, computed, watch } from 'vue'
import { gsap } from 'gsap'

export default {
  name: 'CrashGame',
  setup() {
    // State
    const gameCanvas = ref(null)
    const currentMultiplier = ref(1.00)
    const isGameActive = ref(false)
    const hasCrashed = ref(false)
    const betAmount = ref(10)
    const canCashOut = ref(false)
    const userBalance = ref(1000)
    const hasActiveBet = ref(false)
    const crashPoint = ref(0)
    const countdown = ref(5)
    const previousCrashes = ref([1.98, 3.42, 1.23, 8.56, 2.34])
    const activePlayers = ref(0)
    const totalBets = ref(0)
    const autoEnabled = ref(false)
    const autoCashoutPoint = ref(2.00)

    // Animation
    let gameLoop
    let multiplierInterval

    const rocketStyle = computed(() => ({
      transform: `translateY(-${(currentMultiplier.value - 1) * 50}px) rotate(45deg)`
    }))

    // Game Logic
    const startGame = () => {
      isGameActive.value = true
      hasCrashed.value = false
      currentMultiplier.value = 1.00

      multiplierInterval = setInterval(() => {
        currentMultiplier.value *= 1.01

        if (autoEnabled.value && currentMultiplier.value >= autoCashoutPoint.value) {
          cashOut()
        }
      }, 100)
    }

    const endGame = (crashMultiplier) => {
      clearInterval(multiplierInterval)
      crashPoint.value = crashMultiplier
      isGameActive.value = false
      hasCrashed.value = true
      canCashOut.value = false
      hasActiveBet.value = false

      previousCrashes.value.unshift(crashMultiplier)
      previousCrashes.value = previousCrashes.value.slice(0, 10)

      startCountdown()
    }

    const startCountdown = () => {
      countdown.value = 5
      const timer = setInterval(() => {
        countdown.value--
        if (countdown.value <= 0) {
          clearInterval(timer)
          startGame()
        }
      }, 1000)
    }

    const placeBet = () => {
      if (betAmount.value <= userBalance.value) {
        userBalance.value -= betAmount.value
        hasActiveBet.value = true
        canCashOut.value = true
        totalBets.value += betAmount.value
        activePlayers.value++
      }
    }

    const cashOut = () => {
      if (canCashOut.value) {
        const winnings = betAmount.value * currentMultiplier.value
        userBalance.value += winnings
        canCashOut.value = false
        hasActiveBet.value = false
        activePlayers.value--
      }
    }

    const quickBet = (amount) => {
      if (!isGameActive.value) {
        betAmount.value = amount
      }
    }

    const betHalf = () => {
      betAmount.value = Math.floor(betAmount.value / 2)
    }

    const betDouble = () => {
      betAmount.value = Math.min(betAmount.value * 2, userBalance.value)
    }

    const betMax = () => {
      betAmount.value = userBalance.value
    }

    onMounted(() => {
      startCountdown()
    })

    onUnmounted(() => {
      clearInterval(multiplierInterval)
    })

    return {
      gameCanvas,
      currentMultiplier,
      isGameActive,
      hasCrashed,
      betAmount,
      canCashOut,
      userBalance,
      hasActiveBet,
      crashPoint,
      countdown,
      previousCrashes,
      activePlayers,
      totalBets,
      autoEnabled,
      autoCashoutPoint,
      rocketStyle,
      placeBet,
      cashOut,
      quickBet,
      betHalf,
      betDouble,
      betMax
    }
  }
}
</script>

<style scoped>
.crash-game {
  width: 100%;
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
  background: #1a1a1a;
  border-radius: 12px;
  color: white;
}

.game-stats {
  display: flex;
  justify-content: space-between;
  margin-bottom: 20px;
}

.previous-crashes span {
  margin-left: 8px;
  padding: 4px 8px;
  background: #333;
  border-radius: 4px;
  font-size: 14px;
}

.high-crash {
  color: #4CAF50;
}

.game-canvas {
  position: relative;
  width: 100%;
  height: 400px;
  background: #000;
  border-radius: 8px;
  overflow: hidden;
}

.game-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.multiplier {
  font-size: 48px;
  color: #4CAF50;
  font-weight: bold;
  text-shadow: 0 0 10px rgba(76, 175, 80, 0.5);
}

.rocket {
  font-size: 40px;
  transition: transform 0.1s ease-out;
}

.crash-text {
  font-size: 36px;
  color: #f44336;
  font-weight: bold;
}

.betting-panel {
  margin-top: 20px;
  padding: 20px;
  background: #333;
  border-radius: 8px;
}

.bet-info {
  display: flex;
  justify-content: space-between;
  margin-bottom: 15px;
}

.betting-controls {
  display: grid;
  gap: 15px;
}

.quick-amounts {
  display: flex;
  gap: 5px;
  margin-top: 10px;
}

.quick-amounts button {
  padding: 5px 10px;
  font-size: 12px;
  background: #555;
}

.action-buttons button {
  width: 100%;
  padding: 15px;
  font-size: 18px;
}

.bet-button {
  background: #4CAF50;
}

.cashout-button {
  background: #f44336;
}

.auto-cashout {
  margin-top: 15px;
  display: flex;
  align-items: center;
  gap: 10px;
}

input[type="number"] {
  background: #333;
  color: white;
  border: 1px solid #555;
  padding: 8px;
  border-radius: 4px;
}

button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
</style>
