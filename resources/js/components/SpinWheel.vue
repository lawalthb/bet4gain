<template>
  <div class="spin-game">
    <div class="wheel-container">
     <img src="/resources/img/wheel.png" alt="Wheel" class="wheel"
     :class="{ stopped: isSpinning }"
     ref="wheel">
      <div class="pointer"></div>
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
import { ref, computed } from 'vue'
import gsap from 'gsap'

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
      const targetAngle = targetSegment * segmentAngle

      gsap.to(wheelElement, {
        rotation: `+=${rotations * 360 + targetAngle}`,
        duration: 5,
        ease: "power2.out",
        onComplete: () => {
          isSpinning.value = false
          handleSpinResult(result)
          wheelElement.style.transform = ''
        }
      })
    }

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
       notifications
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

</style>
