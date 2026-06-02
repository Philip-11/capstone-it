<script setup>
import { useForm, Head } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Login" />

    <div class="min-h-screen bg-gradient-to-br from-[#0c1a3a] via-[#1e3a8a] to-[#3730a3] font-sans flex flex-col justify-center items-center px-4">
        
        <div class="mb-6 flex flex-col items-center gap-3 text-center">
            <div class="w-14 h-14 rounded-2xl bg-white p-2.5 shadow-xl flex items-center justify-center">
                <img src="/images/LOGO.png" alt="School Logo" class="w-full h-full object-contain" />
            </div>
            <div>
                <h1 class="text-xl font-bold text-white tracking-tight">GLMS</h1>
                <!-- <p class="text-xs text-blue-200/80 font-medium">Teacher Portal</p> -->
            </div>
        </div>

        <div class="w-full sm:max-w-[420px] bg-white rounded-2xl shadow-xl p-8 space-y-5">
            
            <div class="text-center pb-2 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Sign In</h2>
                <p class="text-xs text-gray-400 mt-0.5">Enter your credentials to access your account.</p>
            </div>

            <form @submit.prevent="submit" class="space-y-4 text-xs">
                
                <div class="space-y-1">
                    <label class="font-bold text-gray-600 block">Email Address</label>
                    <div class="relative flex items-center">
                        <span class="absolute left-4 text-gray-400 pointer-events-none">
                            <i class="fa-solid fa-envelope"></i>
                        </span>
                        <input 
                            v-model="form.email" 
                            type="email" 
                            placeholder="your.email@school.edu.ph"
                            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 bg-gray-50/50 text-sm text-gray-900" 
                            required 
                            autofocus 
                        />
                    </div>
                    <div v-if="form.errors.email" class="text-red-500 font-medium mt-1 text-[11px]">
                        {{ form.errors.email }}
                    </div>
                </div>

                <div class="space-y-1">
                    <div class="flex justify-between items-center">
                        <label class="font-bold text-gray-600">Password</label>
                        <a href="#" class="font-bold text-blue-600 hover:underline">Forgot password?</a>
                    </div>
                    <div class="relative flex items-center">
                        <span class="absolute left-4 text-gray-400 pointer-events-none">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input 
                            v-model="form.password" 
                            type="password" 
                            placeholder="••••••••"
                            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 bg-gray-50/50 text-sm text-gray-900" 
                            required 
                        />
                    </div>
                    <div v-if="form.errors.password" class="text-red-500 font-medium mt-1 text-[11px]">
                        {{ form.errors.password }}
                    </div>
                </div>

                <div class="flex items-center pt-0.5">
                    <label class="flex items-center cursor-pointer select-none">
                        <input type="checkbox" v-model="form.remember" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                        <span class="ml-2 font-medium text-gray-500">Remember me</span>
                    </label>
                </div>

                <div class="pt-1">
                    <button 
                        type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl shadow-md transition duration-150 active:translate-y-0 flex items-center justify-center gap-2 text-xs uppercase tracking-wider"
                        :disabled="form.processing"
                    >
                        <i class="fa-solid" :class="form.processing ? 'fa-spinner animate-spin' : 'fa-right-to-bracket'"></i>
                        {{ form.processing ? 'Logging in...' : 'Login' }}
                    </button>
                </div>

            </form>
        </div>

    </div>
</template>