import './globals/theme.js'; /* By Sheaf.dev */ 

import './bootstrap';
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import './globals/modals.js';

// SheafUI Datepicker
import './components/date-picker/index.js';
// SheafUI Calendar
import './components/calendar/index.js';

// now you can register
// components using Alpine.data(...) and
// plugins using Alpine.plugin(...) 


 
Livewire.start()