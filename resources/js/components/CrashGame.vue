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

    <div class="game-canvas" ref="gameCanvas">
      <div class="flight-path" ref="flightPath"></div>
      <div class="airplane" ref="airplane">✈️</div>
      <div class="multiplier">{{ currentMultiplier.toFixed(2) }}x</div>
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
import { ref, onMounted, onUnmounted } from 'vue'
import gsap from 'gsap'

export default {
  name: 'CrashGame',
  setup() {
    const currentMultiplier = ref(1.00)
    const isGameActive = ref(false)
    const airplane = ref(null)
    let flightAnimation

    onMounted(() => {
      Echo.channel('game')
        .listen('GameStarted', (e) => {
          startGame(e.game)
        })
        .listen('GameUpdated', (e) => {
          updateGame(e.multiplier)
        })
        .listen('GameCrashed', (e) => {
          crashGame(e.game)
        })
    })

    const startGame = (game) => {
      isGameActive.value = true
      currentMultiplier.value = 1.00

      flightAnimation = gsap.timeline()
        .to(airplane.value, {
          motionPath: {
            path: createFlightPath(),
            autoRotate: true
          },
          ease: "power1.in",
          duration: 30
        })
    }

    const updateGame = (multiplier) => {
      currentMultiplier.value = multiplier
      gsap.to(airplane.value, {
        y: -(multiplier - 1) * 100,
        duration: 0.1
      })
    }

    const crashGame = (game) => {
      isGameActive.value = false
      gsap.to(airplane.value, {
        rotation: 720,
        scale: 0,
        opacity: 0,
        duration: 1,
        ease: "power4.in"
      })
    }

    const createFlightPath = () => {
      return `M0,${gameCanvas.value.clientHeight} Q${gameCanvas.value.clientWidth/2},${gameCanvas.value.clientHeight} ${gameCanvas.value.clientWidth},0`
    }

    const gameCanvas = ref(null)
    const flightPath = ref(null)
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

    return {
      gameCanvas,
      airplane,
      flightPath,
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
  background: linear-gradient(to bottom, #1a1a2e, #16213e);
  overflow: hidden;
}

.airplane {
  position: absolute;
  font-size: 24px;
  transform-origin: center;
}

.flight-path {
  position: absolute;
  width: 100%;
  height: 100%;
}

.multiplier {
  position: absolute;
  top: 20px;
  right: 20px;
  font-size: 32px;
  color: #4CAF50;
  font-weight: bold;
  text-shadow: 0 0 10px rgba(76, 175, 80, 0.5);
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
