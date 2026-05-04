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
    <Head title="Log in" />

    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <h2 class="text-2xl font-bold mb-6 text-center">Teacher Login</h2>

            <form @submit.prevent="submit">
                <!-- Email -->
                <div>
                    <label class="block font-medium text-sm text-gray-700">Email</label>
                    <input 
                        v-model="form.email" 
                        type="email" 
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                        required 
                        autofocus 
                    />
                    <div v-if="form.errors.email" class="text-red-600 text-sm mt-2">{{ form.errors.email }}</div>
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <label class="block font-medium text-sm text-gray-700">Password</label>
                    <input 
                        v-model="form.password" 
                        type="password" 
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                        required 
                    />
                    <div v-if="form.errors.password" class="text-red-600 text-sm mt-2">{{ form.errors.password }}</div>
                </div>

                <!-- Remember Me -->
                <div class="block mt-4">
                    <label class="flex items-center">
                        <input type="checkbox" v-model="form.remember" class="rounded border-gray-300 text-indigo-600 shadow-sm" />
                        <span class="ml-2 text-sm text-gray-600">Remember me</span>
                    </label>
                </div>

                <div class="flex items-center justify-end mt-4">
                    <button 
                        type="submit" 
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150"
                        :disabled="form.processing"
                    >
                        Log in
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>