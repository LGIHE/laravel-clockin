<x-mail::message>
# 🕐 Daily Clockin Reminder

Hello **{{ $userName }}**,

This is your friendly reminder to clock in for today, **{{ $currentDate }}**.

Please remember to clock in at your designated time: **{{ $clockinTime }}**

<x-mail::button :url="url('/attendance')">
Clock In Now
</x-mail::button>

---

### Important Reminders:
- Clock in on time to ensure accurate attendance records
- Don't forget to clock out at the end of your shift
- Contact your supervisor if you have any attendance issues

**Automated message sent by the Attendance Management System**

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
