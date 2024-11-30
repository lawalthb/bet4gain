<template>
  <div class="game-switcher">
    <!-- Game Icon Trigger -->
    <div class="game-icon" @click="toggleDrawer" title="More games">
   <!-- Dice Icon -->
<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
  <circle cx="8.5" cy="14.5" r="1.5"/>
  <circle cx="15.5" cy="14.5" r="1.5"/>
  <circle cx="12" cy="17.5" r="1.5"/>
</svg>

    </div>

    <!-- Drawer Menu -->
    <div class="game-tabs" :class="{ 'drawer-open': isDrawerOpen }">
      <button
        @click="selectGame('crash')"
        :class="{ active: activeGame === 'crash' }"
        class="tab-button"
      >
        Crash Game
      </button>
      <button
        @click="selectGame('spin')"
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
    const isDrawerOpen = ref(false)

    const toggleDrawer = () => {
      isDrawerOpen.value = !isDrawerOpen.value
    }

    const selectGame = (game) => {
      activeGame.value = game
      isDrawerOpen.value = false
    }

    return {
      activeGame,
      isDrawerOpen,
      toggleDrawer,
      selectGame
    }
  }
}
</script>

<style scoped>
.game-switcher {
  display: flex;
  gap: 20px;
  position:relative
}

.game-icon {
  position: fixed;
  left: 0;
  top: 50%;
  transform: translateY(-50%);
  cursor: pointer;
  padding: 10px;
  background: #333;
  border-radius: 0 8px 8px 0;
  transition: all 0.3s ease;
  z-index: 1000;
}

.game-icon:hover {
  background: #444;
}

.game-tabs {
  position: fixed;
  left: -200px;
  top: 50%;
  transform: translateY(-50%);
  transition: all 0.3s ease;
  background: #1a1a1a;
  padding: 20px;
  border-radius: 0 8px 8px 0;
  display: flex;
  flex-direction: column;
  gap: 10px;
  z-index: 999;
}

.drawer-open {
  left: 0;
}

.tab-button {
  padding: 12px 24px;
  background: #333;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  white-space: nowrap;
}

.tab-button.active {
  background: #4CAF50;
  transform: translateX(2px);
  box-shadow: 0 4px 12px rgba(76, 175, 80, 0.2);
}

.tab-button:hover:not(.active) {
  background: #444;
}
</style>
