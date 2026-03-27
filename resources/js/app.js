import './bootstrap';
import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';
import collapse from '@alpinejs/collapse';

window.Alpine = Alpine;
Alpine.plugin(intersect);
Alpine.plugin(collapse);
Alpine.start();
