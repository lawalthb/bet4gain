<template>
  <div class="crash-game">
   <div class="game-stats bg-gray-800 rounded-lg p-4 shadow-lg">
  <div class="current-stats grid grid-cols-2 md:grid-cols-4 gap-4">
    <div class="stat-card bg-gray-700 p-3 rounded-lg">
      <div class="text-gray-400 text-sm">Players</div>
      <div class="text-xl font-bold">{{ activePlayers }}</div>
    </div>

    <div class="stat-card bg-gray-700 p-3 rounded-lg">
      <div class="text-gray-400 text-sm">Total Bets</div>
      <div class="text-xl font-bold">₦{{ totalBets }}</div>
    </div>

    <div class="stat-card bg-gray-700 p-3 rounded-lg">
      <div class="text-gray-400 text-sm">Balance</div>
      <div class="text-xl font-bold" v-if="isLoggedIn">₦{{ userBalance }}</div>
      <div class="text-xl font-bold" v-else>₦{{ demoBalance.toFixed(2) }}</div>
    </div>
  </div>

  <div class="previous-crashes mt-4 flex flex-wrap gap-2">
    <span
      v-for="(crash, index) in previousCrashes"
      :key="index"
      :class="{
        'high-crash': crash > 2,
        'bg-gray-700 px-3 py-1 rounded-full text-sm font-medium': true
      }"
    >
      {{ crash.toFixed(2) }}x
    </span>
  </div>
</div>
<!-- Add this notification section -->
     <div class="notifications-wrapper">
      <div v-for="(notification, index) in notifications"
           :key="index"
           :class="['game-notification', notification.type]">
        <div class="notification-content">
          <span :class="`${notification.type}-amount`">
            {{ notification.type === 'win' ? '+' : '-' }}₦{{ notification.amount.toFixed(2) }}
          </span>
          <span :class="`${notification.type}-text`">
            {{ notification.type === 'win' ? 'Winner!' : 'Better luck next time!' }}
          </span>
        </div>
      </div>
    </div>
    <div class="game-canvas" ref="gameCanvas">
      <div class="flight-path" ref="flightPath"></div>

    <div class="active-bets" v-if="botBets.length > 0">
        <div v-for="bot in botBets" :key="bot.id" class="bot-bet">
            <span class="bot-name">{{ bot.name }}</span>
            <span class="bot-amount">₦{{ bot.amount }}</span>
        </div>
    </div>


     <div class="rocket-container" ref="airplane" :style="rocketStyle">
      <img src="/resources/img/rocket223.png" alt="rocket" />
    </div>
     <div
  v-show="isGameActive && !hasCrashed"
  class="multiplier"
  :class="{ 'crashed': hasCrashed }"
>
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
        <label>Cashout (x)</label>
        <div class="cashout-controls">
          <input
            type="number"
            v-model="autoCashoutPoint"
            :disabled="!autoEnabled"
            step="0.1"
            min="1.1"
          />
        </div>
      </div>
    </div>

    <div class="controls-row">
      <div class="quick-amounts">
        <button @click="quickBet(5)">₦5</button>
        <button @click="quickBet(10)">₦10</button>
        <button @click="quickBet(50)">₦50</button>
        <button @click="betHalf">1/2</button>
        <button @click="betDouble">2x</button>
        <button @click="betMax">Max</button>
      </div>

      <div class="auto-cashout-toggle">
        <label>
          <input type="checkbox" v-model="autoEnabled" :disabled="isGameActive" />
          Auto Cashout
        </label>
      </div>
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
import gsap from 'gsap';
import botNames from '../data/botNames.json';

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
      const notifications = ref([])
      const botsCount = ref(3) // Number of active bots
    const botBets = ref([]) // Track bot bets

const africanNames = [
    'Oluwaseun', 'Chioma', 'Kwame', 'Amara', 'Zainab',
    'Folake', 'Babajide', 'Aisha', 'Chidi', 'Ngozi',
    'Olayinka', 'Mandla', 'Thabo', 'Tendai', 'Amina',
    'Koffi', 'Abena', 'Chinua', 'Folami', 'Kehinde'
];



    const isLoggedIn = ref(window.auth.isLoggedIn)
    const userBalance = ref(window.auth.user ? window.auth.user.wallet_balance : 0)
      const demoBalance = ref(1000)

  const canPlaceBet = computed(() => {
      if (isLoggedIn.value) {
        return !isGameActive.value && betAmount.value > 0 && betAmount.value <= userBalance.value;
      }
      return !isGameActive.value && betAmount.value > 0 && betAmount.value <= demoBalance.value;
    });


    const checkAutoCashout = (currentMultiplier) => {
  if (hasActiveBet.value && autoEnabled.value && autoCashoutPoint.value) {
    if (currentMultiplier >= autoCashoutPoint.value) {
      cashOut();
    }
  }
};
      onMounted(() => {
        if (window.innerWidth <= 768) {
    const gameElement = document.querySelector('.crash-game');
    if (gameElement) {
      gameElement.scrollIntoView({ behavior: 'smooth' });
    }
  }
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

        handleBotBets();
       // Reset rocket state
  // Set initial rocket position at bottom center
  rocketPosition.value = {
    x: 50,
    y: gameCanvas.value.clientHeight - 100
  };
  rocketRotation.value = -15;
  rocketScale.value = 1;
  rocketOpacity.value = 1;


    }

      // Enhanced multiplier handling
    const updateMultiplier = (multiplier) => {
      const targetMultiplier = Number(multiplier)

        handleBotCashouts(multiplier);

  // Smooth animation for multiplier counting
      gsap.to(currentMultiplier, {
        value: targetMultiplier,
        duration: 0.1,
        ease: "power1.in", // Exponential easing
        snap: {
          value: 0.01 // Snap to 2 decimal places
        },
        onUpdate: () => {
          const displayValue = Number(currentMultiplier.value).toFixed(2)
          if (document.querySelector('.multiplier')) {
            document.querySelector('.multiplier').textContent = `${displayValue}x`
          }
        }
      })

  // Calculate rocket position using logarithmic scaling
      const progress = Math.log(targetMultiplier) / Math.log(10)
      const maxHeight = gameCanvas.value?.clientHeight * 0.8 || 0
      const currentHeight = maxHeight * progress

      // Smooth rocket movement
      gsap.to(rocketPosition.value, {
        x: (gameCanvas.value?.clientWidth || 0) * (progress * 0.3),
        y: (gameCanvas.value?.clientHeight || 0) - currentHeight,
        duration: 0.1,
        ease: "none"
      })

      // Update rocket rotation for smooth flight path
      gsap.to(rocketRotation, {
        value: -15 + (progress * 30),
        duration: 0.1
      })

      // Check for auto-cashout
      checkAutoCashout(targetMultiplier)
    }

    // Enhanced crash animation
    const crashGame = (finalMultiplier) => {
      if (flightAnimation) flightAnimation.kill()

      isGameActive.value = false
      hasCrashed.value = true
      crashPoint.value = finalMultiplier
      currentMultiplier.value = finalMultiplier

      const canvasHeight = gameCanvas.value?.clientHeight || 400

      // Enhanced crash animation sequence
      gsap.timeline()
        .to(rocketRotation, {
          value: 90,
          duration: 0.5,
          ease: "power2.in"
        })
        .to(rocketPosition.value, {
          y: canvasHeight + 100,
          x: rocketPosition.value.x + 50,
          duration: 0.8,
          ease: "power2.in"
        }, 0)
        .to(rocketScale, {
          value: 0.5,
          duration: 0.8
        }, 0)
        .to(rocketOpacity, {
          value: 0,
          duration: 0.8,
          onComplete: () => {
            setTimeout(startCountdown, 3000)
            if (hasActiveBet.value) {
              handleGameCrash(finalMultiplier)
            }
          }
        }, 0)

      if (gameCanvas.value && airplane.value) {
        const canvasHeight = gameCanvas.value.offsetHeight || 400

        gsap.to(airplane.value, {
      rotation: 90,
      y: canvasHeight + 100, // Move below canvas
      x: `+=${50}`, // Slight horizontal movement
      scale: 0.5,
      opacity: 0,
      duration: 0.8,
      ease: "power2.in",
      onComplete: () => {
        setTimeout(() => {
          startCountdown();
        }, 3000);
              if (hasActiveBet.value) {
                // Update lost status in database
        axios.post('/game/crash', {
            game_id: currentGameId.value,
            crash_point: finalMultiplier
        }).then(() => {
            // Refresh user balance
            if (isLoggedIn.value) {
                loadUserBalance();
            }
        });

                  showLoseNotification(betAmount.value);
              hasActiveBet.value = false
              canCashOut.value = false
              activePlayers.value--
            }
          }
        })
      }

          // Update game history
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

      const notificationTimeout = ref(null)
// Updated animation refs
const rocketPosition = ref({ x: 0, y: 0 })
const rocketRotation = ref(-15)
const rocketScale = ref(1)
const rocketOpacity = ref(1)
const animationInProgress = ref(false)

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
    // Handle demo cashout
    if (!isLoggedIn.value) {
      const winAmount = betAmount.value * currentMultiplier.value;
      demoBalance.value += winAmount;
      showWinNotification(winAmount);
      hasActiveBet.value = false;
      canCashOut.value = false;
      activePlayers.value--;
      return;
    }

    // Handle real cashout
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
        showWinNotification(response.data.win_amount);
      }
    } catch (error) {
      console.error('Cashout error:', error.response?.data?.message || error.message);
    }
  }
};

const handleGameCrash = (crashPoint) => {
  if (hasActiveBet.value && !canCashOut.value) {
  // Handle demo loss
    if (!isLoggedIn.value) {
      showLoseNotification(betAmount.value);
      hasActiveBet.value = false;
      canCashOut.value = false;
      activePlayers.value--;
      return;
    }
 // Handle real money loss
    axios.post('/game/crash', {
      bet_id: currentBetId.value,
      game_id: currentGameId.value,
      crash_point: crashPoint
    }).then(() => {
      if (isLoggedIn.value) {
        loadUserBalance();
      }
     // showLoseNotification(betAmount.value);
      hasActiveBet.value = false;
      canCashOut.value = false;
      activePlayers.value--;
    });
  }
};


const showWinNotification = (amount) => {
  notifications.value.push({
    type: 'win',
    amount: amount
  });

  setTimeout(() => {
    notifications.value.shift();
  }, 3000);
};

// Add this function to handle bot betting
const handleBotBets = () => {
    botBets.value = []
    const activeBots = Math.floor(Math.random() * botsCount.value) + 1

    for (let i = 0; i < activeBots; i++) {
        const randomName = africanNames[Math.floor(Math.random() * africanNames.length)]
        const botBet = {
            id: `bot-${i}`,
            name: randomName,
            amount: Math.floor(Math.random() * 90) + 10,
            autoCashout: (Math.random() * 3 + 1.2).toFixed(2)
        }
        botBets.value.push(botBet)
        activePlayers.value++
        totalBets.value += botBet.amount
    }
}//end bot betting

// Add bot cashout logic
const handleBotCashouts = (multiplier) => {
  botBets.value.forEach((bot, index) => {
    if (multiplier >= bot.autoCashout) {
      // Bot cashes out
      activePlayers.value--
      botBets.value.splice(index, 1)
    }
  })
}


const showLoseNotification = (amount) => {
  notifications.value.push({
    type: 'lose',
    amount: amount
  });

  setTimeout(() => {
    notifications.value.shift();
  }, 3000);
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

// Enhanced computed properties
    const rocketStyle = computed(() => ({
      transform: `translate(${rocketPosition.value.x}px, ${rocketPosition.value.y}px)
                rotate(${rocketRotation.value}deg)
                scale(${rocketScale.value})`,
      opacity: rocketOpacity.value,
      transition: 'transform 0.1s linear'
    }))

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
     notifications,
        notificationTimeout,
  showWinNotification,
        showLoseNotification,
   rocketStyle,
  rocketPosition,
  rocketRotation,
  rocketScale,
  rocketOpacity,
        animationInProgress,
  botsCount,
  botBets,
  handleBotBets,
  handleBotCashouts
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
  margin-bottom: 5px;

}
@media (max-width: 768px) {
  .game-stats {
    flex-direction: column;
  }

  .current-stats {
    order: 1;
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
  }

  .previous-crashes {
    order: 2;
  }
}
.previous-crashes span {
  margin-left: 5px;
  padding: 2px 2px;
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
  height: 300px;
  /* Option 1: Modern gradient with deep space feel */
  background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f172a 100%);

  /* Option 2: Animated gradient background */
  background: linear-gradient(-45deg, #1a1a2e, #16213e, #0f172a, #1e293b);
  background-size: 400% 400%;
  animation: gradientBG 15s ease infinite;

  /* Option 3: Starry night effect */
  background: radial-gradient(circle at center, #1a1a2e 0%, #16213e 100%);
  box-shadow: inset 0 0 50px rgba(255,255,255,0.1);

  overflow: hidden;
}

/* For animated gradient */
@keyframes gradientBG {
  0% { background-position: 0% 50% }
  50% { background-position: 100% 50% }
  100% { background-position: 0% 50% }
}

.game-canvas::before {
  content: '';
  position: absolute;
  width: 200%;
  height: 100%;
  background-image: radial-gradient(white 1px, transparent 1px);
  background-size: 50px 50px;
  opacity: 0.1;
  animation: moveStars 15s linear infinite;
}

@keyframes moveStars {
  0% {
    transform: translateX(0);
  }
  100% {
    transform: translateX(-50%);
  }
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
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  font-size: 72px;
  font-weight: bold;
  color: #4CAF50;
  text-shadow: 0 0 10px rgba(76, 175, 80, 0.3);
  font-family: 'Digital-7', monospace;
  letter-spacing: 2px;
  transition: all 0.1s linear;
}

.multiplier.active {
  color: #00ff00;
  text-shadow: 0 0 20px rgba(0, 255, 0, 0.5);
}

.multiplier.crashed {
  color: #f44336;
  text-shadow: 0 0 15px rgba(244, 67, 54, 0.5);
  animation: shake 0.5s ease-in-out;
}

@keyframes shake {
  0%, 100% { transform: translate(-50%, -50%); }
  25% { transform: translate(-52%, -50%); }
  75% { transform: translate(-48%, -50%); }
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
      margin-bottom: 5px;
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

    .controls-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 0px;
}

.auto-cashout-toggle {
  display: flex;
  align-items: center;
  color: #888;
}

.auto-cashout-toggle input[type="checkbox"] {
  margin-right: 5px;
}

@media (max-width: 768px) {
  .controls-row {
    flex-direction: column;
    gap: 10px;
  }

  .auto-cashout-toggle {
    width: 100%;
    justify-content: center;
  }
}


.game-notification {
  position: fixed;
  top: 20px;
  right: 20px;
  padding: 20px;
  border-radius: 8px;
  color: white;
  animation: slideIn 0.5s ease-out;
  z-index: 1000;
}

.notification-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 5px;
}

.win {
  background: linear-gradient(45deg, #4CAF50, #45a049);
  box-shadow: 0 0 20px rgba(76, 175, 80, 0.3);
}

.lose {
  background: linear-gradient(45deg, #f44336, #e53935);
  box-shadow: 0 0 20px rgba(244, 67, 54, 0.3);
}

.win-amount, .lose-amount {
  font-size: 24px;
  font-weight: bold;
}

.win-text, .lose-text {
  font-size: 16px;
}

.fade-out {
  animation: fadeOut 1s ease-out forwards;
}

@keyframes slideIn {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

@keyframes fadeOut {
  to {
    opacity: 0;
    transform: translateY(-20px);
  }
}


.rocket-container {
  position: absolute;
  width: 80px;
  height: 80px;
  will-change: transform;
}

.rocket-container img {
  width: 100%;
  height: 100%;
  object-fit: contain;
}


.active-bets {
  position: absolute;
  left: 20px;
  top: 20px;
  background: rgba(0, 0, 0, 0.5);
  padding: 10px;
  border-radius: 8px;
}

.bot-bet {
  color: white;
  margin: 5px 0;
  font-size: 14px;
}

.bot-name {
  color: #4CAF50;
  margin-right: 10px;
}
.high-crash {
  background: linear-gradient(45deg, #4CAF50, #45a049);
  box-shadow: 0 0 10px rgba(76, 175, 80, 0.3);
}

.stat-card {
  transition: all 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}
/* new code */
.multiplier {
  transition: color 0.2s ease;
}

/* Enhanced animation for crash effect */
@keyframes shake {
  0%, 100% { transform: translate(-50%, -50%) rotate(0deg); }
  25% { transform: translate(-52%, -50%) rotate(-2deg); }
  75% { transform: translate(-48%, -50%) rotate(2deg); }
}


.multiplier.crashed {
  animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
}

</style>

