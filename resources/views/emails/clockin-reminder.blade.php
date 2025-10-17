<x-mail::message>
# 🕐 Daily Clockin Reminder

Hello **{{ $userName }}**,

This is your friendly reminder to clock in for today, **{{ $currentDate }}**.

Please remember to clock in for the tasks you have for the day.

<x-mail::button :url="url('/')">
Clock In Now
</x-mail::button>

---

### Important Reminders:
- Clock in on time to ensure accurate attendance records
- Don't forget to clock out at the end of your shift
- Contact your supervisor if you have any attendance issues
- Contact support if you need assistance with the system

**Automated message sent by the Clockin System**

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
