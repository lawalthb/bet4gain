<template>
  <div class="game-history">
    <div class="history-header">
      <h3>Game History</h3>
      <div class="filters">
        <select v-model="filter">
          <option value="all">All Games</option>
          <option value="wins">Wins</option>
          <option value="losses">Losses</option>
        </select>
      </div>
    </div>

    <div class="history-table">
      <table>
        <thead>
          <tr>
            <th>Time</th>
            <th>Bet Amount</th>
            <th>Multiplier</th>
            <th>Profit/Loss</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="game in filteredHistory" :key="game.id">
            <td>{{ formatTime(game.created_at) }}</td>
            <td>₦{{ game.bet_amount }}</td>
            <td :class="{ 'high-multiplier': game.crash_point >= 2 }">
              {{ game.crash_point }}x
            </td>
            <td :class="{ 'profit': game.profit > 0, 'loss': game.profit < 0 }">
              ₦{{ game.profit }}
            </td>
            <td>
              <span :class="['status', game.status]">
                {{ game.status }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
      <div class="pagination">
    <button
      :disabled="currentPage === 1"
      @click="changePage(currentPage - 1)"
      class="page-btn"
    >
      Previous
    </button>

    <span class="page-info">
      Page {{ currentPage }} of {{ totalPages }}
    </span>

    <button
      :disabled="currentPage === totalPages"
      @click="changePage(currentPage + 1)"
      class="page-btn"
    >
      Next
    </button>
  </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';

export default {
  name: 'GameHistory',

  setup() {
    const gameHistory = ref([]);
      const filter = ref('all');
    const currentPage = ref(1);
    const totalPages = ref(1);

   const fetchHistory = async (page = 1) => {
      try {
        const response = await axios.get(`/game/history?page=${page}`);
        gameHistory.value = response.data.data;
        currentPage.value = response.data.current_page;
        totalPages.value = response.data.last_page;
      } catch (error) {
        console.error('Failed to load game history:', error);
      }
    };

    const filteredHistory = computed(() => {
      if (filter.value === 'all') return gameHistory.value;
      if (filter.value === 'wins') return gameHistory.value.filter(game => game.profit > 0);
      if (filter.value === 'losses') return gameHistory.value.filter(game => game.profit < 0);
    });

    const formatTime = (timestamp) => {
      return new Date(timestamp).toLocaleTimeString();
    };

    const changePage = (page) => {
      fetchHistory(page);
    };


    onMounted(() => {
      fetchHistory();
    });

      return {
        currentPage,
      totalPages,
      changePage,
      gameHistory,
      filter,
      filteredHistory,
      formatTime
    };
  }
}
</script>

<style scoped>
.game-history {
  background: #1a1a1a;
  border-radius: 8px;
  padding: 20px;
  margin-top: 20px;
}

.history-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.history-header h3 {
  color: #fff;
  margin: 0;
}

.filters select {
  background: #333;
  color: #fff;
  border: 1px solid #444;
  padding: 5px 10px;
  border-radius: 4px;
}

.history-table {
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
  color: #fff;
}

th, td {
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #333;
}

th {
  background: #222;
  font-weight: 600;
}

.high-multiplier {
  color: #4CAF50;
}

.profit {
  color: #4CAF50;
}

.loss {
  color: #f44336;
}

.status {
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
}

.status.won {
  background: #4CAF50;
  color: #fff;
}

.status.lost {
  background: #f44336;
  color: #fff;
}

@media (max-width: 768px) {
  .history-table {
    font-size: 14px;
  }

  th, td {
    padding: 8px;
  }
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 20px;
  margin-top: 20px;
  padding: 20px 0;
}

.page-btn {
  background: #333;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
  transition: background 0.3s;
}

.page-btn:hover:not(:disabled) {
  background: #444;
}

.page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-info {
  color: #888;
}
</style>
