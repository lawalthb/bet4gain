<?php

namespace App\Services;

use App\Models\Bet;
use App\Models\Game;
use App\Models\User;
use App\Models\Bot;

class BettingService
{
    public function placeBet($data, $user = null)
    {
        $game = Game::findOrFail($data['game_id']);

        if ($user) {
            if ($user->wallet_balance < $data['amount']) {
                throw new \Exception('Insufficient balance');
            }

            $user->wallet_balance -= $data['amount'];
            $user->save();
        }

        return Bet::create([
            'game_id' => $game->id,
            'user_id' => $user ? $user->id : null,
            'amount' => $data['amount'],
            'auto_cashout' => $data['auto_cashout'] ?? null,
            'is_demo' => !$user,
            'is_bot' => $data['is_bot'] ?? false,
            'bot_name' => $data['bot_name'] ?? null,
            'status' => 'pending'
        ]);
    }

    public function processCashout($bet, $crashPoint)
    {
        if ($bet->status !== 'pending') {
            throw new \Exception('Bet already processed');
        }

        $winAmount = $bet->amount * $crashPoint;
        $profit = $winAmount - $bet->amount;

        $bet->update([
            'status' => 'won',
            'cashout_multiplier' => $crashPoint,
            'profit' => $profit
        ]);

        // Update user wallet balance if it's a real user bet
        if ($bet->user_id && !$bet->is_bot) {
            $user = User::find($bet->user_id);
            $user->wallet_balance += $winAmount;
            $user->save();
        }

        return $winAmount;
    }

    public function placeBotBets($game)
    {
        $bots = Bot::where('is_active', true)->get();

        foreach ($bots as $bot) {
            $betAmount = rand($bot->min_bet * 100, $bot->max_bet * 100) / 100;
            $autoCashout = rand($bot->min_cashout * 100, $bot->max_cashout * 100) / 100;

            $this->placeBet([
                'game_id' => $game->id,
                'amount' => $betAmount,
                'auto_cashout' => $autoCashout,
                'is_bot' => true,
                'bot_name' => $bot->name
            ]);
        }
    }

    public function processBotBets($game, $crashPoint)
    {
        $pendingBotBets = Bet::where('game_id', $game->id)
                            ->where('is_bot', true)
                            ->where('status', 'pending')
                            ->get();

        foreach ($pendingBotBets as $bet) {
            if ($bet->auto_cashout <= $crashPoint) {
                $this->processCashout($bet, $bet->auto_cashout);
            } else {
                $bet->update([
                    'status' => 'lost',
                    'profit' => -$bet->amount
                ]);
            }
        }
    }
}
