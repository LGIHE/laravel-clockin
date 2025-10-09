import './bootstrap';
import './toast';

// Alpine is already included with Livewire
// No need to import and start it separately

// Listen for Livewire toast events and convert them to browser events
document.addEventListener('livewire:init', () => {
    Livewire.on('toast', (event) => {
        // event is an array with one element containing the data
        const data = Array.isArray(event) ? event[0] : event;
        window.toast(data.message, data.variant || 'info');
    });
});
