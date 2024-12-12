<template>
  <div class="spin-game">
    <div class="wheel-container">
     <img src="/resources/img/wheel.png" alt="Wheel" class="wheel"
     :class="{ stopped: isSpinning }"
     ref="wheel">
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
import Echo from 'laravel-echo'

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

//     const spinWheel = (result) => {
//   isSpinning.value = true;
//   const wheel = wheel.value;

//   // Get the current rotation
//   const currentRotation = getComputedStyle(wheel).getPropertyValue('transform');
//   wheel.style.transform = currentRotation;

//   const rotations = 5;
//   const segmentAngle = 360 / segments.value.length;
//   const targetAngle = calculateTargetAngle(result);

//   gsap.to(wheel, {
//     rotation: `+=${rotations * 360 + targetAngle}`,
//     duration: 5,
//     ease: "power2.out",
//     onComplete: () => {
//       isSpinning.value = false;
//       // Reset transform to allow continuous rotation to resume
//       wheel.style.transform = '';
//     }
//   });
// };
// onMounted(() => {
//       Echo.channel('game')
//         .listen('GameSpinResult', (e) => {
//           spinWheel(e.result)
//         })
//     })

    const placeBet = async () => {
  try {
    const response = await axios.post('/spin/bet', {
      amount: betAmount.value,
      color: selectedColor.value
    })

    if(response.data.success) {
      // Show betting confirmation
      betAmount.value = 10 // Reset bet amount
      selectedColor.value = null // Reset color selection

      // Start wheel animation
      isSpinning.value = true
    }
  } catch (error) {
    // Show error message to user
    console.error('Betting error:', error)
  }
}

const handleSpinResult = (result) => {
  const winningSegment = segments.value.find(s => s.color === result)
  const winAmount = betAmount.value * winningSegment.multiplier

  // Show win/loss message
  if(selectedColor.value === result) {
    // User won
    alert(`You won ${winAmount}!`)
  } else {
    // User lost
    alert('Better luck next time!')
  }
}


const spinWheel = (result) => {
  isSpinning.value = true
  const wheel = wheel.value

  gsap.to(wheel, {
    rotation: `+=${5 * 360 + calculateTargetAngle(result)}`,
    duration: 5,
    ease: "power2.out",
    onComplete: () => {
      isSpinning.value = false
      handleSpinResult(result)
      wheel.style.transform = ''
    }
  })
}


    const calculateTargetAngle = (result) => {
      const segmentIndex = segments.value.findIndex(s => s.color === result)
      return segmentIndex * (360 / segments.value.length)
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
</style>
