<template>
  <div class="spin-game">
    <div class="wheel-container">
      <img src="/resources/img/wheel.png" alt="Wheel" class="wheel" ref="wheel">
      <div class="pointer"></div>
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

    const spinWheel = (result) => {
      isSpinning.value = true
      const rotations = 5
      const segmentAngle = 360 / segments.value.length
      const targetAngle = calculateTargetAngle(result)

      gsap.to(wheel.value, {
        rotation: `+=${rotations * 360 + targetAngle}`,
        duration: 5,
        ease: "power2.out",
        onComplete: () => {
          isSpinning.value = false
        }
      })
    }

    const calculateTargetAngle = (result) => {
      const segmentIndex = segments.value.findIndex(s => s.color === result)
      return segmentIndex * (360 / segments.value.length)
    }

    const placeBet = async () => {
      try {
        const response = await axios.post('/spin/bet', {
          amount: betAmount.value,
          color: selectedColor.value
        })
        // Handle successful bet
      } catch (error) {
        console.error('Betting error:', error)
      }
    }

    const canBet = computed(() => selectedColor.value && betAmount.value > 0 && !isSpinning.value)

    const selectColor = (color) => {
      selectedColor.value = color
    }

    return {
      wheel, isSpinning, selectedColor, betAmount, segments, canBet, spinWheel, selectColor, placeBet
    }
  }
}
</script>

<style scoped>
.spin-game {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

.wheel-container {
  width: 400px;
  height: 400px;
  margin: 50px auto;
  position: relative;
}

.wheel {
  width: 100%;
  height: 100%;
}

.pointer {
  position: absolute;
  top: -10px; /* Positioned at the bottom */
  left: 50%;
  transform: translateX(-50%);
  width: 0;           /* Width zero for triangle */
  height: 0;          /* Height zero for triangle */
  border-left: 10px solid transparent; /* Left side of triangle */
  border-right: 10px solid transparent;/* Right side of triangle */
  border-top: 20px solid white;      /* Top side of triangle (the pointer) */
}
.betting-panel {
  margin-top: 20px;
  padding: 20px;
  background: #333;
  border-radius: 8px;
}

.bet-options {
  display: flex;
  gap: 10px;
  margin-bottom: 15px;
}

.bet-options button {
  flex: 1;
  padding: 15px;
  border: none;
  border-radius: 4px;
  font-weight: bold;
  cursor: pointer;
}

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

.bet-controls {
  display: flex;
  gap: 10px;
}

.bet-controls input {
  flex: 1;
  padding: 10px;
  border: none;
  border-radius: 4px;
}

.bet-controls button {
  padding: 10px 20px;
  background: #3498db;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.bet-controls button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
</style>
