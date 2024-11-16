<template>
  <div class="game-switcher">
    <div class="game-tabs">
      <button
        @click="activeGame = 'crash'"
        :class="{ active: activeGame === 'crash' }"
        class="tab-button"
      >
        Crash Game
      </button>
      <button
        @click="activeGame = 'spin'"
        :class="{ active: activeGame === 'spin' }"
        class="tab-button"
      >
        Spin Wheel
      </button>
    </div>

    <CrashGame v-if="activeGame === 'crash'" />
    <SpinWheel v-else />
  </div>
</template>

<script>
import { ref } from 'vue'
import CrashGame from './components/CrashGame.vue'
import SpinWheel from './components/SpinWheel.vue'

export default {
  components: {
    CrashGame,
    SpinWheel
  },
  setup() {
    const activeGame = ref('crash')
    return { activeGame }
  }
}
</script>

<style scoped>
.game-switcher {
  width: 100%;
  max-width: 800px;
  margin: 0 auto;
}

.game-tabs {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
}

.tab-button {
  padding: 12px 24px;
  background: #333;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.tab-button.active {
  background: #4CAF50;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(76, 175, 80, 0.2);
}

.tab-button:hover:not(.active) {
  background: #444;
}
</style>
