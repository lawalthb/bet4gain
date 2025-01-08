<template>
  <div class="spin-game">
      <div class="game-stats">
  <div class="stats-container">
    <div class="recent-results">
      <h3>Last Results</h3>
      <div class="result-dots">
        <div v-for="(result, index) in lastResults"
             :key="index"
             :class="['result-dot', result.color]"
             :title="`${result.color.toUpperCase()} - ${result.multiplier}x`">
          {{result.multiplier}}x
        </div>
      </div>
    </div>

    <div class="stats-grid">
  <div class="stat-box">
    <span class="stat-label">Your Balance</span>
    <span class="stat-value">₦{{isLoggedIn ? userBalance : demoBalance}}</span>
  </div>
  <div class="stat-box">
    <span class="stat-label">Total Games</span>
    <span class="stat-value">{{stats.totalGames}}</span>
  </div>
  <div class="stat-box">
    <span class="stat-label">Highest Win</span>
    <span class="stat-value">₦{{stats.highestWin}}</span>
  </div>
  <div class="stat-box">
    <span class="stat-label">Average Multiplier</span>
    <span class="stat-value">{{stats.avgMultiplier}}x</span>
  </div>
</div>
  </div>
</div>
<div class="wheel-container">
  <img src="/resources/img/wheel.png" alt="Wheel"
       class="wheel"
       :class="{ stopped: isSpinning }"
       :style="{ transform: `rotate(${initialRotation + wheelRotation}deg)` }"
       ref="wheel">
  <div class="pointer"></div>

</div>
<div v-if="countdown > 0" class="countdown-timer">
  <div class="timer-display">
      <span class="timer-label">Please Wait</span>
    <span class="timer-value">{{countdown}}</span>
    <span class="timer-label">seconds</span>
  </div>
</div>
<div class="notifications-wrapper">
    <div v-for="(notification, index) in notifications"
         :key="index"
         :class="['game-notification', notification.type]">
        <div class="notification-content">
            <span :class="`${notification.type}-amount`">
                {{ notification.type === 'win' ? '+' : '-' }}₦{{ notification.amount.toFixed(2) }}
            </span>
            <span :class="`${notification.type}-text`">
                {{ notification.message }}
            </span>
        </div>
    </div>
</div>
    <div class="betting-panel">
      <div class="bet-options">
        <button v-for="segment in segments" :key="segment.color"
          @click="selectColor(segment.color)"
          :class="{ active: selectedColor === segment.color, [segment.color]: true }">
          {{ segment.multiplier }}x
        </button>
      </div>

      <div class="bet-controls">
        <input type="number" v-model="betAmount" placeholder="Bet amount" />
        <button @click="placeBet" :disabled="!canBet || isSpinning">Place Bet</button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import gsap from 'gsap'
import Pusher from 'pusher-js';

export default {
  name: 'SpinWheel',
    setup() {

    const wheel = ref(null)
    const isSpinning = ref(false)
    const selectedColor = ref(null)
    const betAmount = ref(10)
    const userBalance = ref(window.auth?.user?.wallet_balance || 0)
    const isLoggedIn = ref(window.auth?.isLoggedIn || false)
    const demoBalance = ref(1000)
        const notifications = ref([])
        const wheelRotation = ref(0)
        const countdown = ref(5) // 10 seconds countdown
const countdownInterval = ref(null)
const initialRotation = ref(0)
    const segments = ref([
      { color: 'red', multiplier: 2 },
      { color: 'black', multiplier: 2 },
      { color: 'green', multiplier: 14 },
      { color: 'yellow', multiplier: 3 },
      { color: 'blue', multiplier: 5 },
      { color: 'purple', multiplier: 2 },
      { color: 'orange', multiplier: 2 },
      { color: 'pink', multiplier: 7 },
      { color: 'cyan', multiplier: 2 },
      { color: 'brown', multiplier: 2 },
      { color: 'magenta', multiplier: 9 },
      { color: 'lime', multiplier: 2 }
    ])
    const lastResults = ref([])
const stats = ref({
  totalGames: 0,
  highestWin: 0,
  greenRate: 0,
  avgMultiplier: 0
})





    const canBet = computed(() => {
      if (isLoggedIn.value) {
        return !isSpinning.value && selectedColor.value && betAmount.value > 0 && betAmount.value <= userBalance.value
      }
      return !isSpinning.value && selectedColor.value && betAmount.value > 0 && betAmount.value <= demoBalance.value
    })

     const spinWheel = (result) => {
  isSpinning.value = true
  const wheelElement = wheel.value

  const rotations = 5
  const segmentAngle = 360 / segments.value.length
  const targetSegment = segments.value.findIndex(s => s.color === result)
  // Calculate exact angle needed to point to the winning color
  const targetAngle = 360 - (targetSegment * segmentAngle)
  const totalRotation = rotations * 360 + targetAngle

  wheelRotation.value = totalRotation

  gsap.to(wheelRotation, {
    value: totalRotation,
    duration: 5,
    ease: "power2.out",
    onComplete: () => {
      isSpinning.value = false
      handleSpinResult(result)
    }
  })
     }

     // Calculate initial position based on last result
const setInitialPosition = (color) => {
  const segmentAngle = 360 / segments.value.length
  const segmentIndex = segments.value.findIndex(s => s.color === color)
  initialRotation.value = -(segmentIndex * segmentAngle)
}


            // Initialize Pusher
            const pusher = new Pusher('87892ed076b91483ee2a', {
                cluster: 'mt1'
            })

            // Subscribe to spin-game channel
            const chl = pusher.subscribe('spin-game')

            // Log connection status
            chl.bind('pusher:subscription_succeeded', () => {
                console.log('Successfully connected to spin-game chl')
            })

            chl.bind('pusher:subscription_error', (error) => {
                console.error('Failed to connect to spin-game chl:', error)
            })

           // Listen for SpinResult event
chl.bind('SpinResult', (data) => {
    console.log('Received spin result:', data)
  startCountdown()
    lastResults.value.unshift({
        color: data.result_color,
        multiplier: data.multiplier
    })
    if (lastResults.value.length > 6) {
        lastResults.value.pop()
    }
    updateStats(data)


      setInitialPosition(data.result_color) // Set initial position
    spinWheel(data.result_color)

    // Only show notifications if player placed a bet
    if (selectedColor.value) {
        if (selectedColor.value === data.result_color) {
            const winAmount = betAmount.value * data.multiplier
            showWinNotification(winAmount)
        } else {
            showLoseNotification(betAmount.value)
        }
    }
})

    const placeBet = async () => {
      if (!canBet.value) return

      try {
        if (!isLoggedIn.value) {
          // Handle demo mode
          demoBalance.value -= betAmount.value
          // Simulate spin result
          const randomSegment = segments.value[Math.floor(Math.random() * segments.value.length)]
          spinWheel(randomSegment.color)
          return
        }

        const response = await axios.post('/spin/bet', {
          amount: betAmount.value,
          color: selectedColor.value
        })

        if (response.data.success) {
          userBalance.value = response.data.balance
          spinWheel(response.data.result)
        }
      } catch (error) {
        console.error('Betting error:', error)
      }
    }

    const handleSpinResult = (result) => {
  // Only process result if player placed a bet
  if (selectedColor.value) {
    const winningSegment = segments.value.find(s => s.color === result)

    if (selectedColor.value === result) {
      const winAmount = betAmount.value * winningSegment.multiplier
      if (isLoggedIn.value) {
        userBalance.value += winAmount
      } else {
        demoBalance.value += winAmount
      }
      showWinNotification(winAmount)
    } else {
      showLoseNotification(betAmount.value)
    }

    selectedColor.value = null
    betAmount.value = 10
  }
}


const startCountdown = () => {
  countdown.value = 5
  countdownInterval.value = setInterval(() => {
    countdown.value--
    if (countdown.value <= 0) {
      clearInterval(countdownInterval.value)
    }
  }, 1000)
}


    const showWinNotification = (amount) => {
    notifications.value.push({
        type: 'win',
        amount: amount,
        message: `Congratulations! You won ₦${amount.toFixed(2)}!`
    })

    setTimeout(() => {
        notifications.value.shift()
    }, 3000)
}

const showLoseNotification = (amount) => {
    notifications.value.push({
        type: 'lose',
        amount: amount,
        message: `Better luck next time! Lost ₦${amount.toFixed(2)}`
    })

    setTimeout(() => {
        notifications.value.shift()
    }, 3000)
}

    const selectColor = (color) => {
      if (!isSpinning.value) {
        selectedColor.value = color
      }
    }

const updateStats = (data) => {
    stats.value.totalGames++
    stats.value.highestWin = Math.max(stats.value.highestWin, data.multiplier * betAmount.value)

    const greenGames = lastResults.value.filter(r => r.color === 'green').length
    stats.value.greenRate = ((greenGames / lastResults.value.length) * 100).toFixed(1)

    const totalMultiplier = lastResults.value.reduce((sum, r) => sum + r.multiplier, 0)
    stats.value.avgMultiplier = (totalMultiplier / lastResults.value.length).toFixed(2)
}

// Clean up on component unmount
onUnmounted(() => {
  if (countdownInterval.value) {
    clearInterval(countdownInterval.value)
  }
})

    return {
      wheel,
      isSpinning,
      selectedColor,
      betAmount,
      segments,
      canBet,
      spinWheel,
      selectColor,
      placeBet,
      userBalance,
      isLoggedIn,
        demoBalance,
        notifications,
       wheelRotation,
        initialRotation,
 lastResults,
        stats,
  countdown
    }
  }
}
</script>

<style scoped>
.spin-game {
  max-width: 800px;
  margin: 0 auto;
  padding: 10px;
}

.wheel-container {
  width: 100%;
  max-width: 400px;
  height: auto;
  aspect-ratio: 1;
  margin: 20px auto;
  position: relative;
}

.wheel {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.pointer {
  position: absolute;
  top: -10px;
  left: 50%;
  transform: translateX(-50%);
  width: 0;
  height: 0;
  border-left: 10px solid transparent;
  border-right: 10px solid transparent;
  border-top: 20px solid white;
  z-index: 10;
}

.betting-panel {
  margin-top: 20px;
  padding: 15px;
  background: #333;
  border-radius: 8px;
}

.bet-options {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
  gap: 8px;
  margin-bottom: 15px;
}

.bet-options button {
  width: 100%;
  padding: 12px 8px;
  border: none;
  border-radius: 4px;
  font-weight: bold;
  cursor: pointer;
  font-size: 14px;
}

.bet-controls {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

@media (min-width: 768px) {
  .bet-controls {
    flex-direction: row;
  }

  .wheel-container {
    margin: 40px auto;
  }

  .betting-panel {
    padding: 20px;
  }

  .bet-options button {
    font-size: 16px;
    padding: 15px;
  }
}

.bet-controls input {
  width: 100%;
  padding: 12px;
  border: none;
  border-radius: 4px;
  background: #444;
  color: white;
}

.bet-controls button {
  width: 100%;
  padding: 12px;
  background: #3498db;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
}

/* Keep your existing color classes */
.red { background: #e74c3c; }
.black { background: #2c3e50; }
.green { background: #27ae60; }
.yellow { background: yellow; }
.blue { background: blue; }
.purple { background: purple; }
.orange { background: orange; }
.pink { background: pink; }
.cyan { background: cyan; }
.brown { background: brown; }
.magenta { background: magenta; }
.lime { background: lime; }

.bet-options button.active {
  transform: scale(0.95);
  box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
}

.bet-controls button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Add this animation keyframe */
@keyframes rotate {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

/* Modify the .wheel class */
.wheel {
  width: 100%;
  height: 100%;
  object-fit: contain;
  animation: rotate 10s linear infinite; /* Adjust speed by changing 10s */
}

/* Add this class for when wheel is stopped */
.wheel.stopped {
  animation: none;
}


.notifications-wrapper {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1000;
}

.game-notification {
    padding: 15px 20px;
    margin-bottom: 10px;
    border-radius: 8px;
    color: white;
    animation: slideIn 0.5s ease-out;
}

.win {
    background: linear-gradient(45deg, #4CAF50, #45a049);
    box-shadow: 0 0 20px rgba(76, 175, 80, 0.3);
}

.lose {
    background: linear-gradient(45deg, #f44336, #e53935);
    box-shadow: 0 0 20px rgba(244, 67, 54, 0.3);
}

.notification-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 5px;
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

.game-stats {
  margin: 20px auto;
  max-width: 800px;
}

.stats-container {
  background: #333;
  border-radius: 8px;
  padding: 15px;
  color: white;
}

.recent-results {
  margin-bottom: 20px;
}

.recent-results h3 {
  font-size: 1.2rem;
  margin-bottom: 10px;
}

.result-dots {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.result-dot {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 0.9rem;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 15px;
}

@media (min-width: 768px) {
  .stats-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}

.stat-box {
  background: #444;
  padding: 15px;
  border-radius: 6px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 5px;
}

.stat-label {
  font-size: 0.9rem;
  opacity: 0.8;
}

.stat-value {
  font-size: 1.2rem;
  font-weight: bold;
}
.countdown-timer {
  text-align: center;
  margin: 20px 0;
}

.timer-display {
  background: #333;
  display: inline-flex;
  flex-direction: column;
  padding: 15px 25px;
  border-radius: 8px;
  color: white;
}

.timer-value {
  font-size: 2rem;
  font-weight: bold;
  color: #3498db;
}

.timer-label {
  font-size: 0.9rem;
  opacity: 0.8;
}
</style>



