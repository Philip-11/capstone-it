<script setup>
import { computed } from 'vue';

// Tanggapin ang data galing sa parent page/layout
const props = defineProps({
    gamification: {
        type: Object,
        default: () => ({ xp: 0, level: 1, streak: 0 })
    }
});

// MATH COMPUTATION PARA SA PROGRESS BAR
// Halimbawa: Every 200 XP = 1 Level. Alamin natin kung ilang XP ang natitira sa kasalukuyang level.
const xpPerLevel = 200;
const currentLevelXp = computed(() => props.gamification.xp % xpPerLevel);

// I-compute ang percentage para sa width ng progress bar
const progressPercentage = computed(() => {
    const percentage = (currentLevelXp.value / xpPerLevel) * 100;
    return Math.min(percentage, 100); // Siguraduhing hindi lalampas sa 100%
});
</script>

<template>
    <div class="bg-gradient-to-br from-[#0c1a3a] to-[#1e3a8a] text-white p-5 rounded-2xl shadow-xl border border-blue-900/40 relative overflow-hidden font-sans">
        
        <div class="absolute -right-10 -top-10 w-32 h-32 bg-blue-500/10 rounded-full blur-2xl"></div>
        
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 flex flex-col items-center justify-center shadow-lg font-black text-slate-900 tracking-tight">
                    <span class="text-[9px] uppercase font-bold leading-none text-amber-950">LVL</span>
                    <span class="text-lg leading-none">{{ gamification.level }}</span>
                </div>
                <div>
                    <h4 class="font-bold text-sm tracking-tight">Student Ranking Profile</h4>
                    <p class="text-[11px] text-blue-200 font-medium">
                        {{ gamification.xp }} Kabuuang XP na Naipon
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-1.5 bg-white/10 px-3 py-1.5 rounded-xl border border-white/5 shadow-inner">
                <i class="fa-solid fa-fire text-orange-500 animate-pulse text-sm"></i>
                <div class="text-right">
                    <p class="text-[14px] font-black leading-none text-orange-400">{{ gamification.streak }}</p>
                    <p class="text-[9px] font-bold text-blue-200 uppercase tracking-wider">Day Streak</p>
                </div>
            </div>
        </div>

        <div class="space-y-1.5">
            <div class="flex justify-between items-center text-[10px] font-bold text-blue-200 uppercase tracking-wide">
                <span>XP Progress</span>
                <span class="text-amber-400 font-mono">{{ currentLevelXp }} / {{ xpPerLevel }} XP</span>
            </div>
            
            <div class="w-full h-3 bg-slate-950/40 rounded-full p-[2px] border border-white/5 shadow-inner overflow-hidden">
                <div 
                    class="h-full bg-gradient-to-r from-amber-400 via-orange-400 to-yellow-300 rounded-full transition-all duration-500 ease-out shadow-[0_0_8px_rgba(251,191,36,0.5)]"
                    :style="{ width: `${progressPercentage}%` }"
                ></div>
            </div>
            
            <p class="text-[10px] text-blue-300 italic font-medium text-center pt-0.5">
                Kailangan mo pa ng {{ xpPerLevel - currentLevelXp }} XP para mag-Level Up! 🚀
            </p>
        </div>
    </div>
</template>