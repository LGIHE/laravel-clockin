# UI Component Library

This component library follows shadcn/ui design patterns with Tailwind CSS and Alpine.js for interactivity.

## UI Components

### Button
```blade
<x-ui.button variant="primary" size="md">Click me</x-ui.button>
<x-ui.button variant="secondary">Secondary</x-ui.button>
<x-ui.button variant="danger">Delete</x-ui.button>
<x-ui.button variant="outline">Outline</x-ui.button>
```

**Props:**
- `variant`: primary, secondary, danger, success, outline, ghost, link
- `size`: sm, md, lg, icon
- `type`: button, submit, reset
- `disabled`: boolean

### Input
```blade
<x-ui.input type="text" placeholder="Enter text" />
<x-ui.input type="email" error="Invalid email" />
```

**Props:**
- `type`: text, email, password, etc.
- `disabled`: boolean
- `error`: string (error message)

### Card
```blade
<x-ui.card>
    <h3>Card Title</h3>
    <p>Card content</p>
</x-ui.card>
```

**Props:**
- `padding`: boolean (default: true)
- `shadow`: boolean (default: true)

### Table
```blade
<x-ui.table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>John Doe</td>
            <td>john@example.com</td>
        </tr>
    </tbody>
</x-ui.table>
```

### Modal
```blade
<x-ui.modal name="confirm-delete" maxWidth="md">
    <div class="p-6">
        <h3>Confirm Delete</h3>
        <p>Are you sure?</p>
    </div>
</x-ui.modal>

<!-- Trigger -->
<x-ui.button @click="$dispatch('open-modal', 'confirm-delete')">
    Open Modal
</x-ui.button>
```

**Props:**
- `name`: string (unique identifier)
- `show`: boolean
- `maxWidth`: sm, md, lg, xl, 2xl, 3xl, 4xl, 5xl, 6xl

### Dropdown
```blade
<x-ui.dropdown align="right" width="48">
    <x-slot name="trigger">
        <x-ui.button>Options</x-ui.button>
    </x-slot>

    <x-slot name="content">
        <x-ui.dropdown-link href="#">Edit</x-ui.dropdown-link>
        <x-ui.dropdown-link href="#">Delete</x-ui.dropdown-link>
    </x-slot>
</x-ui.dropdown>
```

**Props:**
- `align`: left, right, top
- `width`: 48, 56, 64

### Badge
```blade
<x-ui.badge variant="success">Active</x-ui.badge>
<x-ui.badge variant="warning">Pending</x-ui.badge>
<x-ui.badge variant="danger">Inactive</x-ui.badge>
```

**Props:**
- `variant`: default, primary, success, warning, danger, info, secondary
- `size`: sm, md, lg

### Alert
```blade
<x-ui.alert variant="success" dismissible>
    Operation completed successfully!
</x-ui.alert>
```

**Props:**
- `variant`: info, success, warning, danger
- `dismissible`: boolean

### Toast
```blade
<!-- Include in layout -->
<x-ui.toast />

<!-- Trigger from JavaScript -->
<script>
    window.toast('Success message', 'success');
    window.toast('Error message', 'danger');
</script>
```

### Loading
```blade
<x-ui.loading size="md" text="Loading..." />
```

**Props:**
- `size`: sm, md, lg, xl
- `text`: string (optional)

### Empty State
```blade
<x-ui.empty-state 
    title="No records found" 
    description="Get started by creating a new record"
>
    <x-slot name="action">
        <x-ui.button>Create New</x-ui.button>
    </x-slot>
</x-ui.empty-state>
```

## Form Components

### Text Input
```blade
<x-form.text-input 
    label="Full Name" 
    name="name" 
    required 
    hint="Enter your full name"
    error="{{ $errors->first('name') }}"
/>
```

**Props:**
- `label`: string
- `error`: string
- `hint`: string
- `required`: boolean
- `disabled`: boolean

### Select
```blade
<x-form.select label="Country" name="country" required>
    <option value="">Select a country</option>
    <option value="us">United States</option>
    <option value="uk">United Kingdom</option>
</x-form.select>
```

### Textarea
```blade
<x-form.textarea 
    label="Description" 
    name="description" 
    rows="5"
    hint="Maximum 500 characters"
/>
```

**Props:**
- `rows`: number (default: 3)

### Date Picker
```blade
<x-form.date-picker 
    label="Start Date" 
    name="start_date" 
    min="2024-01-01"
    max="2024-12-31"
/>
```

**Props:**
- `min`: date string
- `max`: date string

## Layout Components

### App Layout
```blade
<x-layout.app title="Dashboard">
    <x-slot name="header">
        <x-layout.header title="Dashboard">
            <x-slot name="actions">
                <x-ui.button>New Item</x-ui.button>
            </x-slot>
        </x-layout.header>
    </x-slot>

    <x-slot name="sidebar">
        <x-layout.sidebar>
            <x-ui.nav-link href="/dashboard" :active="true">Dashboard</x-ui.nav-link>
            <x-ui.nav-link href="/users">Users</x-ui.nav-link>
        </x-layout.sidebar>
    </x-slot>

    <!-- Main content -->
    <div class="p-6">
        <h1>Welcome</h1>
    </div>

    <x-slot name="footer">
        <x-layout.footer />
    </x-slot>
</x-layout.app>
```

## JavaScript Helpers

### Toast Notifications
```javascript
// Success toast
window.toast('Operation successful!', 'success');

// Error toast
window.toast('Something went wrong', 'danger');

// Warning toast
window.toast('Please review your input', 'warning');

// Info toast
window.toast('New update available', 'info');
```

### Modal Control
```javascript
// Open modal
window.openModal('modal-name');

// Close modal
window.closeModal('modal-name');
```

## Styling Guidelines

All components follow these principles:
- Consistent spacing using Tailwind's spacing scale
- Focus states for accessibility
- Hover states for interactive elements
- Disabled states with reduced opacity
- Error states with red color scheme
- Smooth transitions for state changes
- Responsive design patterns
