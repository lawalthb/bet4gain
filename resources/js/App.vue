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
.game-tabs {
  display: flex;
  gap: 8px;
  margin-bottom: 15px;
  justify-content: flex-end; /* Align to right */
}

.tab-button {
  padding: 8px 16px;
  background: rgba(51, 51, 51, 0.8);
  color: #888;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 14px;
}

.tab-button.active {
  background: #4CAF50;
  color: white;
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(76, 175, 80, 0.2);
}

.tab-button:hover:not(.active) {
  background: #444;
  color: #aaa;
}

</style>
