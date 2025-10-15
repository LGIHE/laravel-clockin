#!/bin/bash

# Email Monitoring Script for Laravel ClockIn
# This script helps monitor email sending in real-time

echo "=========================================="
echo "   Laravel Email Monitoring Tool"
echo "=========================================="
echo ""
echo "Monitoring email logs in real-time..."
echo "Press Ctrl+C to stop"
echo ""
echo "Recent email activity:"
echo "=========================================="
echo ""

# Follow the laravel log file
tail -f storage/logs/laravel.log | grep --line-buffered -i -E "(email|mail|welcome)"
