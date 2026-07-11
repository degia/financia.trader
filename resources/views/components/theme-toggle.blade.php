<button
    x-data
    @click="$store.theme.toggle()"
    class="p-2 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors duration-150"
    :title="$store.theme.isDark ? 'Switch to light mode' : 'Switch to dark mode'"
>
    <template x-if="$store.theme.isDark">
        <x-icon name="sun" class="w-5 h-5 text-zinc-400 hover:text-zinc-200 transition-colors" />
    </template>
    <template x-if="!$store.theme.isDark">
        <x-icon name="moon" class="w-5 h-5 text-zinc-500 hover:text-zinc-700 transition-colors" />
    </template>
</button>
