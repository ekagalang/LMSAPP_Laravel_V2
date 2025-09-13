import './bootstrap';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

// Import interactive video player
import './interactive-video-player';

window.Alpine = Alpine;

Alpine.plugin(collapse);

Alpine.start();