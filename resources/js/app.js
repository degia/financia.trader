import './bootstrap';

document.addEventListener('alpine:init', () => {
    Alpine.store('theme', {
        mode: localStorage.getItem('theme') || 'dark',

        init() {
            this.apply();
        },

        toggle() {
            this.mode = this.mode === 'dark' ? 'light' : 'dark';
            localStorage.setItem('theme', this.mode);
            this.apply();
        },

        apply() {
            if (this.mode === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        },

        get isDark() {
            return this.mode === 'dark';
        }
    });
});
