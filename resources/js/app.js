import './bootstrap';

import Alpine from 'alpinejs';
import { registerApplicationsCalendarCard } from './alpine/applicationsCalendarCard';

window.Alpine = Alpine;
registerApplicationsCalendarCard(Alpine);

// Alpine will be started by Livewire when available.
// Avoid calling Alpine.start() here to prevent double-initialization warnings.
