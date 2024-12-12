<?php

namespace App\Services;

class Game
{
    private $id;
    private $crashPoint;
    private $status;
    private $startTime;
    private $endTime;

    public function __construct()
    {
        $this->id = uniqid();
        $this->status = 'pending';
        $this->crashPoint = $this->generateCrashPoint();
    }

    private function generateCrashPoint()
    {
        // Add your crash point generation logic here
        // Example: Random number between 1.00 and 10.00
        return round(1 + rand(0, 900) / 100, 2);
    }

    // Add getters and setters as needed
    public function getId() { return $this->id; }
    public function getCrashPoint() { return $this->crashPoint; }
    public function getStatus() { return $this->status; }
    public function setStatus($status) { $this->status = $status; }
    public function getStartTime() { return $this->startTime; }
    public function setStartTime($time) { $this->startTime = $time; }
    public function getEndTime() { return $this->endTime; }
    public function setEndTime($time) { $this->endTime = $time; }
}
