#!/bin/bash

# Laravel ClockIn - Development Startup Script
# This script starts the Laravel scheduler for auto clock-out functionality

echo "🚀 Starting Laravel ClockIn Development Environment..."
echo ""

# Get the directory where the script is located
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$DIR"

# Check if scheduler is already running
if pgrep -f "schedule:work" > /dev/null; then
    echo "⚠️  Scheduler is already running!"
    echo ""
    echo "Process details:"
    ps aux | grep "schedule:work" | grep -v grep
    echo ""
    read -p "Do you want to restart it? (y/n) " -n 1 -r
    echo ""
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        echo "🔄 Stopping existing scheduler..."
        pkill -f "schedule:work"
        sleep 2
    else
        echo "✅ Keeping existing scheduler running"
        exit 0
    fi
fi

# Start the scheduler
echo "📅 Starting Laravel scheduler..."
nohup php artisan schedule:work > storage/logs/scheduler.log 2>&1 &
SCHEDULER_PID=$!

sleep 2

# Verify it started
if pgrep -f "schedule:work" > /dev/null; then
    echo "✅ Scheduler started successfully (PID: $SCHEDULER_PID)"
    echo ""
    echo "📊 Scheduled tasks:"
    php artisan schedule:list
    echo ""
    echo "📝 Logs: tail -f storage/logs/scheduler.log"
    echo "🛑 Stop: pkill -f 'schedule:work'"
    echo ""
    echo "✨ Auto clock-out is now active!"
else
    echo "❌ Failed to start scheduler"
    exit 1
fi
