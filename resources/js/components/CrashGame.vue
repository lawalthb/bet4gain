<template>
  <div class="crash-game">
    <div class="game-stats">
      <div class="current-stats">
        <span>Players: {{ activePlayers }}</span>
        <span>Total Bets: ₦{{ totalBets }}</span>
        <span v-if="isLoggedIn">Balance: ₦{{ userBalance }}</span>
        <span v-else>Balance: ₦{{ demoBalance.toFixed(2) }}</span>
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
      <div class="airplane" ref="airplane"><img src="/resources/img/rocket3.png" style="height: 100px;" /> </div>
      <div class="multiplier" :class="{ 'crashed': hasCrashed }">
        {{ currentMultiplier.toFixed(2) }}x
      </div>
      <div v-if="!isGameActive && !hasCrashed" class="status">
        Starting in {{ countdown }}s
      </div>
      <div v-if="hasCrashed" class="crash-point">
        Crashed at {{ crashPoint.toFixed(2) }}x
      </div>
    </div>

    <div class="betting-panel">
      <div class="bet-controls-container">
        <div class="input-group">
          <div class="amount-input">
            <label>Amount </label>
            <input
              type="number"
              v-model="betAmount"
              :disabled="isGameActive"
              min="1"
              :max="userBalance"
            />
          </div>

          <div class="cashout-input">
            <label>Auto Cashout (x)</label>
            <input
              type="number"
              v-model="autoCashoutPoint"
              :disabled="!autoEnabled"
              step="0.1"
              min="1.1"
            />
          </div>
        </div>

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
          :disabled="!canPlaceBet"
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
</template>

<script>
import { ref, onMounted, onUnmounted , computed} from 'vue'
import gsap from 'gsap'

export default {
  name: 'CrashGame',
  setup() {
    const gameCanvas = ref(null)
    const airplane = ref(null)
    const currentMultiplier = ref(1.00)
    const isGameActive = ref(false)
    const hasCrashed = ref(false)
      let flightAnimation = null
      const currentGameId = ref(null)
    const currentBetId = ref(null)

    const isLoggedIn = ref(window.auth.isLoggedIn)
    const userBalance = ref(window.auth.user ? window.auth.user.wallet_balance : 0)
      const demoBalance = ref(1000)

  const canPlaceBet = computed(() => {
      if (isLoggedIn.value) {
        return !isGameActive.value && betAmount.value > 0 && betAmount.value <= userBalance.value;
      }
      return !isGameActive.value && betAmount.value > 0 && betAmount.value <= demoBalance.value;
    });

    onMounted(() => {
      window.Echo.channel('game')
        .listen('.GameStarted', (e) => {
          try {
              console.log('🎮 Game Started:', e)
             currentGameId.value = e.game.id
            startGame()
          } catch (error) {
            console.error('Error starting game:', error)
          }
        })
        .listen('.GameUpdated', (e) => {
          try {
            console.log('📈 Multiplier:', e.multiplier)
            updateMultiplier(e.multiplier)
          } catch (error) {
            console.error('Error updating multiplier:', error)
          }
        })
        .listen('.GameCrashed', (e) => {
          try {
            console.log('💥 Crashed at:', e.crash_point)
            let crashNumber = Number(e.crash_point)
            crashGame(crashNumber)
          } catch (error) {
            console.error('Error handling crash:', error)
          }
        })

      if (isLoggedIn.value) {
        loadUserBalance()
      }
    })

    const loadUserBalance = async () => {
      try {
        const response = await axios.get('/user/balance')
        userBalance.value = response.data.balance
      } catch (error) {
        console.error('Failed to load balance:', error)
      }
    }

    const startGame = () => {
      isGameActive.value = true
      hasCrashed.value = false
      currentMultiplier.value = 1.00

      gsap.set(airplane.value, {
        x: 0,
        y: gameCanvas.value.clientHeight,
        scale: 1,
        opacity: 1,
        rotation: 0
      })

      flightAnimation = gsap.to(airplane.value, {
        x: gameCanvas.value.clientWidth,
        y: 0,
        ease: "power1.in",
        duration: 15
      })
    }

    const updateMultiplier = (multiplier) => {
      currentMultiplier.value = multiplier
      const progress = (multiplier - 1) / 9
      gsap.to(airplane.value, {
        y: gameCanvas.value.clientHeight * (1 - progress),
        duration: 0.1
      })
    }

    const crashGame = (finalMultiplier) => {
      if (flightAnimation) flightAnimation.kill()
      console.log('game as stooped');
      isGameActive.value = false
      hasCrashed.value = true
      crashPoint.value = finalMultiplier
      currentMultiplier.value = finalMultiplier

      if (gameCanvas.value && airplane.value) {
        const canvasHeight = gameCanvas.value.offsetHeight || 400

        gsap.to(airplane.value, {
          rotation: 90,
          y: canvasHeight,
          scale: 0.5,
          opacity: 0,
          duration: 0.5,
          ease: "power2.in",
          onComplete: () => {
            setTimeout(() => {
              startCountdown()
            }, 3000)
            if (hasActiveBet.value) {
              hasActiveBet.value = false
              canCashOut.value = false
              activePlayers.value--
            }
          }
        })
      }

      previousCrashes.value.unshift(finalMultiplier)
      if (previousCrashes.value.length > 5) {
        previousCrashes.value.pop()
      }
    }

    const startCountdown = () => {
      hasCrashed.value = false
      countdown.value = 5
      const timer = setInterval(() => {
        countdown.value--
        if (countdown.value <= 0) {
          clearInterval(timer)
        }
      }, 1000)
    }

    onUnmounted(() => {
      if (flightAnimation) {
        flightAnimation.kill()
      }
    })

    const flightPath = ref(null)
    const betAmount = ref(10)
    const canCashOut = ref(false)
    const hasActiveBet = ref(false)
    const previousCrashes = ref([1.98, 3.42, 1.23, 8.56, 2.34])
    const activePlayers = ref(0)
    const totalBets = ref(0)
    const autoEnabled = ref(true)
    const autoCashoutPoint = ref(2.00)
    const crashPoint = ref(0)
    const countdown = ref(5)

    // const placeBet = () => {
    //   if (betAmount.value <= userBalance.value) {
    //     userBalance.value -= betAmount.value
    //     hasActiveBet.value = true
    //     canCashOut.value = true
    //     totalBets.value += betAmount.value
    //     activePlayers.value++
    //   }
    // }

    // const cashOut = () => {
    //   if (canCashOut.value) {
    //     const winnings = betAmount.value * currentMultiplier.value
    //     userBalance.value += winnings
    //     canCashOut.value = false
    //     hasActiveBet.value = false
    //     activePlayers.value--
    //   }
    // }


   const placeBet = async () => {
  if (!isGameActive.value && betAmount.value > 0) {
    // Handle demo betting
    if (!isLoggedIn.value) {
      if (betAmount.value <= demoBalance.value) {
        demoBalance.value -= betAmount.value;
        hasActiveBet.value = true;
        canCashOut.value = true;
        totalBets.value += betAmount.value;
        activePlayers.value++;
      }
      return;
    }

    // Handle real betting for authenticated users
    try {
      const response = await axios.post('/bet/place', {
        amount: betAmount.value,
        auto_cashout: autoCashoutPoint.value,
        game_id: currentGameId.value
      });

      if (response.data.success) {
        hasActiveBet.value = true;
        canCashOut.value = true;
        userBalance.value = response.data.wallet_balance;
        currentBetId.value = response.data.bet.id;
        totalBets.value += betAmount.value;
        activePlayers.value++;
      }
    } catch (error) {
      console.error('Betting error:', error.response?.data?.message || error.message);
    }
  }
};


 const cashOut = async () => {
  if (canCashOut.value && hasActiveBet.value) {
    try {
      const response = await axios.post('/bet/cashout', {
        bet_id: currentBetId.value,
        crash_point: currentMultiplier.value
      });

      if (response.data.success) {
        canCashOut.value = false;
        hasActiveBet.value = false;
        userBalance.value = response.data.wallet_balance;
        activePlayers.value--;

        // Show win notification
        showWinAmount(response.data.win_amount);
      }
    } catch (error) {
      console.error('Cashout error:', error.response?.data?.message || error.message);
    }
  }
};


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
      currentMultiplier,
      isGameActive,
      hasCrashed,
      flightPath,
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
      betMax,
      isLoggedIn,
        demoBalance,
      currentBetId: null,
    currentGameId: null,
        betErrors: null,
     canPlaceBet,
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
@media (max-width: 768px) {
  .game-stats {
    flex-direction: column;
  }

  .current-stats {
    order: 1;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
  }

  .previous-crashes {
    order: 2;
  }
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
  bottom: 0;
  left: 0;
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
  font-size: 48px;
  color: #4CAF50;
  font-weight: bold;
  text-shadow: 0 0 10px rgba(76, 175, 80, 0.5);
}

.multiplier.crashed {
  color: #f44336;
}

.status, .crash-point {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  font-size: 32px;
  color: white;
  text-align: center;
}

.crash-point {
  color: #f44336;
  font-weight: bold;
}

.betting-panel {
  margin-top: 5px;
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
  margin-top: 10px;
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

   .bet-controls-container {
      width: 100%;
    }

    .input-group {
      display: flex;
      gap: 15px;
      margin-bottom: 15px;
    }

    .amount-input, .cashout-input {
      flex: 1;
    }

    label {
      display: block;
      margin-bottom: 5px;
      color: #888;
    }

    input[type="number"] {
      width: 100%;
      padding: 10px;
      border-radius: 4px;
      background: #444;
      color: white;
      border: 1px solid #555;
    }

    .quick-amounts {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
    }
</style>

