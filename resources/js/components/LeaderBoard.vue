<template>
  <div class="leaderboard">
    <div class="leaderboard-header">
      <h3>Top Players</h3>
      <div class="time-filter">
        <select v-model="timeFrame">
          <option value="daily">Today</option>
          <option value="weekly">This Week</option>
          <option value="monthly">This Month</option>
          <option value="all">All Time</option>
        </select>
      </div>
    </div>

    <div class="leaderboard-content">
      <div class="leader-item" v-for="(player, index) in leaders" :key="player.id">
        <div class="rank" :class="{'top-3': index < 3}">{{ index + 1 }}</div>
        <div class="player-info">
          <span class="player-name">{{ player.username }}</span>
          <span class="player-stats">
            Wins: {{ player.total_wins }} | Profit: ₦{{ player.total_profit }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, watch } from 'vue'

export default {
  name: 'Leaderboard',
  setup() {
    const leaders = ref([])
    const timeFrame = ref('daily')

    const fetchLeaders = async () => {
      try {
        const response = await axios.get(`/leaderboard/${timeFrame.value}`)
        leaders.value = response.data
      } catch (error) {
        console.error('Failed to load leaderboard:', error)
      }
    }

    watch(timeFrame, () => {
      fetchLeaders()
    })

    onMounted(() => {
        fetchLeaders();
      window.Echo.channel('game')

          .listen('.LeaderboardUpdated', (e) => {
          fetchLeaders()
        leaders.value = e.leaders;
    });
    })

    return {
      leaders,
      timeFrame
    }
  }
}
</script>

<style scoped>
.leaderboard {
  background: #1a1a1a;
  border-radius: 8px;
  padding: 20px;
  color: white;
}

.leaderboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.time-filter select {
  background: #333;
  color: white;
  border: 1px solid #444;
  padding: 5px 10px;
  border-radius: 4px;
}

.leader-item {
  display: flex;
  align-items: center;
  padding: 12px;
  border-bottom: 1px solid #333;
  transition: background 0.3s;
}

.leader-item:hover {
  background: #222;
}

.rank {
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  background: #333;
  margin-right: 15px;
}

.rank.top-3 {
  background: linear-gradient(45deg, #FFD700, #FFA500);
  color: #000;
}

.player-info {
  display: flex;
  flex-direction: column;
}

.player-name {
  font-weight: bold;
  color: #4CAF50;
}

.player-stats {
  font-size: 0.9em;
  color: #888;
}
</style>
