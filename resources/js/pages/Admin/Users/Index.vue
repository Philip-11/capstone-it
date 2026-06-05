<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    users: Array
});

// Controls para sa Tab (All, Teachers, Students)
const currentTab = ref('all');
const isEditMode = ref(false);
const currentUserId = ref(null);

// Filtered Users base sa Tab
const filteredUsers = computed(() => {
    if (currentTab.value === 'teacher') {
        return props.users.filter(u => u.role === 'teacher');
    } else if (currentTab.value === 'student') {
        return props.users.filter(u => u.role === 'student');
    }
    return props.users;
});

// Modal State para sa Pag-add ng User
const showAddModal = ref(false);

// Laravel Inertia Form Setup
const form = useForm({
    name: '',
    email: '',
    role: 'student',
    password: '',
    password_confirmation: ''
});

const openAddModal = () => {
    isEditMode.value = false;
    currentUserId.value = null;
    form.reset();
    form.clearErrors();
    showAddModal.value = true;
}

const openEditModal = (user) => {
    isEditMode.value = true;
    currentUserId.value = user.id;
    form.clearErrors();

    form.name = user.name;
    form.email = user.email;
    form.role = user.role;
    form.password = '';
    form.password_confirmation = '';

    showAddModal.value = true;
}

const submit = () => {
    if (isEditMode.value){
        form.put(route('admin.users.update', currentUserId.value), {
            onSuccess: () => closeModal()
        });
    } else {
        form.post(route('admin.users.store'), {
            onSuccess: () => closeModal()
        });
    }
};

const closeModal = () => {
    form.reset();
    showAddModal.value = false;
}

const deleteUser = (id) => {
    if (confirm('Are you absolutely sure you want to permanently delete this account? This action cannot be undone.')) {
        form.delete(route('admin.users.destroy', id));
    }
};
</script>

<template>
    <Head title="User Management" />

    <!-- Dashboard Gradient Theme -->
    <div class="min-h-screen bg-gradient-to-br from-[#0c1a3a] via-[#1e3a8a] via-[#1e40af] via-[#1d4ed8] to-[#3730a3] text-gray-900 font-sans pb-12">
        
        <!-- HEADER (Admin Context Navbar) -->
        <header class="h-[66px] bg-white border-b-3 border-blue-600 px-[5%] md:px-[9%] flex items-center justify-between shadow-lg sticky top-0 z-50">
            <div class="flex items-center gap-2.5">
                <img src="/images/LOGO.png" alt="School Logo" class="w-[30px] h-[30px] object-contain" />
                <h2 class="text-xl font-bold text-blue-900">GLMS <span class="text-xs text-red-600 font-black tracking-widest bg-red-50 px-2 py-0.5 rounded ml-1 uppercase">Admin</span></h2>
            </div>

            <nav class="hidden md:flex items-center gap-[34px]">
                <Link href="#" class="text-gray-600 hover:text-blue-600 text-sm font-semibold transition">Dashboard</Link>
                <Link :href="route('admin.users.index')" class="text-blue-600 font-bold text-sm py-6 relative after:content-[''] after:absolute after:left-0 after:bottom-[-3px] after:w-full after:h-[3px] after:bg-blue-600 after:rounded-t">
                    User Management
                </Link>
                <a href="#" class="text-gray-600 hover:text-blue-600 text-sm font-semibold transition">Subjects</a>
            </nav>

            <Link method="post" href="/logout" as="button" class="bg-gradient-to-r from-blue-800 to-blue-600 text-white px-4 py-2.5 rounded-lg text-xs font-semibold flex items-center gap-2 transition hover:bg-red-600 shadow-md">
                Logout <i class="fa-solid fa-arrow-right-from-bracket"></i>
            </Link>
        </header>

        <!-- MAIN LAYOUT FRAME -->
        <main class="max-w-7xl mx-auto px-4 md:px-[9%] pt-11 space-y-7">
            
            <!-- Hero Banner -->
            <section class="bg-gradient-to-br from-blue-800 to-blue-600 text-white p-8 md:p-12 rounded-[24px] flex flex-col md:flex-row justify-between items-center gap-6 shadow-xl">
                <div class="text-center md:text-left">
                    <span class="bg-white/20 px-3 py-1 rounded-full text-xs tracking-wide">System Control Unit</span>
                    <h1 class="text-3xl md:text-4xl font-bold my-[18px]">User Account Management</h1>
                    <p class="max-w-[500px] text-blue-100 text-sm">Register new teachers, onboard students, and manage institutional access controls effortlessly.</p>
                </div>
                <button @click="openAddModal" class="bg-emerald-600 text-white px-5 py-3 rounded-xl font-bold hover:bg-emerald-700 transition duration-200 shadow-md flex items-center gap-2 shrink-0 active:scale-95">
                    <i class="fa-solid fa-user-plus"></i> Add New Account
                </button>
            </section>

            <!-- Pill Switcher Sub-Navigation -->
            <section class="flex gap-2 p-1.5 bg-white/10 backdrop-blur-sm rounded-xl max-w-md border border-white/10">
                <button @click="currentTab = 'all'" :class="[currentTab === 'all' ? 'bg-white text-blue-900 font-bold shadow-md' : 'text-white/80 hover:bg-white/5']" class="flex-1 py-2 text-center text-xs md:text-sm rounded-lg transition">All Profiles</button>
                <button @click="currentTab = 'teacher'" :class="[currentTab === 'teacher' ? 'bg-white text-blue-900 font-bold shadow-md' : 'text-white/80 hover:bg-white/5']" class="flex-1 py-2 text-center text-xs md:text-sm rounded-lg transition">Teachers</button>
                <button @click="currentTab = 'student'" :class="[currentTab === 'student' ? 'bg-white text-blue-900 font-bold shadow-md' : 'text-white/80 hover:bg-white/5']" class="flex-1 py-2 text-center text-xs md:text-sm rounded-lg transition">Students</button>
            </section>

            <!-- Main White Grid List Panel -->
            <section class="bg-white rounded-[18px] p-6 shadow-md space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-blue-900 capitalize">{{ currentTab }} Accounts</h3>
                    <span class="text-xs font-bold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-md">Total Records: {{ filteredUsers.length }}</span>
                </div>

                <div class="space-y-2">
                    <div v-if="filteredUsers.length > 0" class="space-y-2">
                        <div v-for="user in filteredUsers" :key="user.id" class="flex flex-col sm:flex-row justify-between sm:items-center p-4 rounded-xl hover:bg-gray-50 transition border border-gray-100 gap-3">
                            <div class="flex items-center gap-4">
                                <!-- Dynamic Icon base sa role -->
                                <div :class="[user.role === 'teacher' ? 'bg-purple-100 text-purple-600' : 'bg-blue-100 text-blue-600']" class="w-11 h-11 rounded-xl flex items-center justify-center text-lg shrink-0">
                                    <i :class="['fa-solid', user.role === 'teacher' ? 'fa-chalkboard-user' : 'fa-graduation-cap']"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-blue-900 text-sm md:text-base">{{ user.name }}</h4>
                                    <p class="text-gray-400 text-xs">{{ user.email }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-3 justify-between sm:justify-end border-t border-gray-50 sm:border-t-0 pt-2 sm:pt-0">
                                <!-- Role Badge -->
                                <span :class="[user.role === 'teacher' ? 'bg-purple-50 text-purple-700 border-purple-200' : 'bg-blue-50 text-blue-700 border-blue-200']" class="px-3 py-0.5 rounded-full text-xs font-bold border capitalize">
                                    {{ user.role }}
                                </span>
                                <button @click="openEditModal(user)" class="p-2 text-gray-400 hover:text-blue-600 transition text-sm rounded-lg hover:bg-blue-50">
                                    <i class="fa-solid fa-user-pen"></i>
                                </button>
                                <!-- Delete Action Button -->
                                <button @click="deleteUser(user.id)" class="p-2 text-gray-400 hover:text-red-600 transition text-sm rounded-lg hover:bg-red-50">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State Fallback -->
                    <div v-else class="text-center py-12 text-gray-400 text-sm border-2 border-dashed border-gray-100 rounded-xl">
                        <i class="fa-solid fa-users-slash text-slate-300 text-3xl mb-2 block"></i>
                        No registered accounts found under this scope.
                    </div>
                </div>
            </section>
        </main>

        <!-- ADD USER REGISTRATION MODAL LAYER -->
        <div v-if="showAddModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-[20px] max-w-md w-full p-6 shadow-2xl border border-slate-100 animate-[fadeIn_0.2s_ease-out]">
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-lg font-bold text-blue-900 flex items-center gap-2">
                        <i class="fa-solid fa-user-gear text-blue-600"></i> {{ isEditMode ? 'Update Account Details' : 'Register New Account' }}
                    </h3>
                    <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600 text-lg"><i class="fa-solid fa-xmark"></i></button>
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase">Full Name</label>
                        <input v-model="form.name" type="text" placeholder="John Doe" class="w-full mt-1 border p-2.5 rounded-xl focus:outline-blue-600 text-sm font-medium">
                        <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase">Email Address</label>
                        <input v-model="form.email" type="email" placeholder="johndoe@school.edu.ph" class="w-full mt-1 border p-2.5 rounded-xl focus:outline-blue-600 text-sm font-medium">
                        <p v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase">System Role</label>
                        <select v-model="form.role" class="w-full mt-1 border p-2.5 rounded-xl focus:outline-blue-600 text-sm font-bold text-gray-700 bg-white">
                            <option value="student">Student Portal Access</option>
                            <option value="teacher">Instructor / Teacher Access</option>
                        </select>
                        <p v-if="form.errors.role" class="text-red-500 text-xs mt-1">{{ form.errors.role }}</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Password 
                            </label>
                            <input v-model="form.password" type="password" placeholder="••••••••" class="w-full mt-1 border p-2.5 rounded-xl focus:outline-blue-600 text-sm">
                            <span v-if="isEditMode" class="text-[10px] text-amber-500 normal-case font-medium">(Leave blank to keep password)</span>
                            <p v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Confirm Password</label>
                            <input v-model="form.password_confirmation" type="password" placeholder="••••••••" class="w-full mt-1 border p-2.5 rounded-xl focus:outline-blue-600 text-sm">
                        </div>
                    </div>

                    <div class="pt-2 flex gap-2">
                        <button type="button" @click="showAddModal = false" class="flex-1 bg-gray-100 text-gray-700 py-2.5 rounded-xl font-bold text-sm hover:bg-gray-200 transition">Cancel</button>
                        <button type="submit" :disabled="form.processing" class="flex-1 bg-gradient-to-r from-blue-800 to-blue-600 text-white py-2.5 rounded-xl font-bold text-sm hover:from-blue-900 hover:to-blue-700 transition shadow-md disabled:opacity-50">
                            {{ isEditMode ? 'Update Account' : 'Save Account' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</template>