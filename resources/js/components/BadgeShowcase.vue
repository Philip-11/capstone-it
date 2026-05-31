<script setup>
const props = defineProps({
    allBadges: {
        type: Array,
        default: () => []
    },
    unlockedBadges: {
        type: Array,
        default: () => []
    }
});

// Shortcut function para tingnan kung unlocked ba ang isang partikular na badge
const isUnlocked = (badgeCode) => {
    return props.unlockedBadges.includes(badgeCode);
};
</script>

<template>
    <div class="bg-white p-5 rounded-2xl shadow-md border border-gray-100 font-sans">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-7 h-7 rounded-lg bg-amber-50 flex items-center justify-center border border-amber-200">
                <i class="fa-solid fa-trophy text-amber-500 text-xs"></i>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 text-sm tracking-tight">Mga Parangal at Badges</h4>
                <p class="text-[10px] text-gray-500 font-medium">
                    Naka-unlock ka ng {{ unlockedBadges.length }} sa {{ allBadges.length }} na Medals
                </p>
            </div>
        </div>

        <div class="grid grid-cols-4 gap-3">
            <div 
                v-for="badge in allBadges" 
                :key="badge.badge_code"
                class="group relative flex flex-col items-center p-2.5 rounded-xl border transition duration-200 text-center"
                :class="isUnlocked(badge.badge_code) 
                    ? 'bg-gradient-to-b from-amber-50/40 to-white border-amber-100 shadow-sm' 
                    : 'bg-gray-50/50 border-gray-100 opacity-50 grayscale'"
            >
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm shadow-inner transition group-hover:scale-110"
                     :class="isUnlocked(badge.badge_code) ? 'bg-amber-100/70' : 'bg-gray-200'">
                    <i class="fa-solid" :class="badge.icon_class"></i>
                </div>

                <span class="text-[9px] font-bold text-slate-700 mt-1.5 truncate w-full">
                    {{ badge.name }}
                </span>

                <div class="absolute bottom-full mb-2 hidden group-hover:flex flex-col items-center z-10 w-44 pointer-events-none">
                    <div class="bg-slate-900 text-white text-[10px] p-2 rounded-lg shadow-xl text-center leading-tight">
                        <p class="font-bold text-amber-400 mb-0.5">{{ badge.name }}</p>
                        <p class="text-gray-300 font-medium">{{ badge.description }}</p>
                        <p v-if="!isUnlocked(badge.badge_code)" class="text-rose-400 font-bold text-[9px] mt-1 uppercase tracking-wider">
                            🔒 Naka-lock pa
                        </p>
                        <p v-else class="text-emerald-400 font-bold text-[9px] mt-1 uppercase tracking-wider">
                            ✨ Na-unlock Na!
                        </p>
                    </div>
                    <div class="w-2 h-2 bg-slate-900 rotate-45 -mt-1"></div>
                </div>

            </div>
        </div>
    </div>
</template>