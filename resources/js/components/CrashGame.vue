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
import Pusher from 'pusher-js'

export default {
  name: 'CrashGame',
    setup() {
        const gameCanvas = ref(null)
        const airplane = ref(null)
        const currentMultiplier = ref(1.00)
        const isGameActive = ref(false)
        const hasCrashed = ref(false)
        const crashPoint = ref(0)
        const countdown = ref(5)
        let flightAnimation = null
         let pusher = null
    let channel = null
        onMounted(() => {
 pusher = new Pusher('87892ed076b91483ee2a', {
        cluster: 'mt1',
        forceTLS: false,
        enabledTransports: ['ws', 'wss']
      })

      // Subscribe to channel
      channel = pusher.subscribe('game')

        // Debug connection status
      pusher.connection.bind('connected', () => {
        console.log('Connected to Pusher')
      })

      pusher.connection.bind('error', (err) => {
        console.error('Pusher Connection Error:', err)
      })

      // Bind events with proper event names (notice the dot prefix)
            channel.bind('GameStarted', (data) => {
         alert(data);
        console.log('Game Started:', data)
        startGame()
      })

            channel.bind('GameUpdated', (data) => {

        console.log('📈 Game Updated:', data)
        if (data && data.multiplier) {
          updateMultiplier(data.multiplier)
        }
      })

      // Add subscription debugging
      channel.bind('pusher:subscription_succeeded', () => {
        console.log('Successfully subscribed to game channel')
      })

      channel.bind('pusher:subscription_error', (error) => {
        console.error('Subscription Error:', error)
      })




//     console.log('Initializing WebSocket connection...');
// // In your Vue component or JavaScript file
//     window.Echo.channel('game')

//     .listen('GameStarted', (e) => {
//         console.log('Game Started Event Received:', e);
//     })
//     .subscribed(() => {
//         console.log('Subscribed to game channel');
//     })
//     .error((error) => {
//         console.error('Echo error:', error);
//     });

//     window.Echo.connector.pusher.connection.bind('connected', () => {
//         console.log('✅ WebSocket Connected!');
//     });

//     window.Echo.connector.pusher.connection.bind('disconnected', () => {
//         console.log('❌ WebSocket Disconnected');
//     });

//     window.Echo.channel('game')
//         .listen('GameStarted', (e) => {
//             console.log('🎮 Game Started:', e);
//             startGame();
//         })
//         .listen('GameUpdated', (e) => {
//             console.log('📈 Game Updated:', e);
//             updateMultiplier(e.multiplier);
//         })
//         .listen('GameCrashed', (e) => {
//             console.log('💥 Game Crashed:', e);
//             crashGame(e.game.crash_point);
//         });

//     window.Echo.channel('test-channel')
//         .listen('TestEvent', (e) => {
//             console.log('Test Event Received:', e);
//             alert('Test Event Received!');
//         });
});
    const startGame = () => {
      isGameActive.value = true
      hasCrashed.value = false
      currentMultiplier.value = 1.00

      flightAnimation = gsap.timeline()
        .to(airplane.value, {
          motionPath: {
            path: `M0,${gameCanvas.value.clientHeight} Q${gameCanvas.value.clientWidth/2},${gameCanvas.value.clientHeight} ${gameCanvas.value.clientWidth},0`,
            autoRotate: true,
          },
          ease: "power1.in",
          duration: 30
        })
    }

    const updateMultiplier = (multiplier) => {
      currentMultiplier.value = multiplier
      gsap.to(airplane.value, {
        y: -(multiplier - 1) * 100,
        duration: 0.1
      })
    }

    const crashGame = (finalMultiplier) => {
      isGameActive.value = false
      hasCrashed.value = true
      crashPoint.value = finalMultiplier

      if (flightAnimation) {
        flightAnimation.kill()
      }

      gsap.to(airplane.value, {
        rotation: 720,
        scale: 0,
        opacity: 0,
        duration: 1,
        ease: "power4.in",
        onComplete: () => {
          startCountdown()
        }
      })
    }

    const startCountdown = () => {
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
      if (channel) {
        channel.unbind_all()
        channel.unsubscribe()
      }
      if (pusher) {
        pusher.disconnect()
      }
    })

    const flightPath = ref(null)
    const betAmount = ref(10)
    const canCashOut = ref(false)
    const userBalance = ref(1000)
    const hasActiveBet = ref(false)
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
