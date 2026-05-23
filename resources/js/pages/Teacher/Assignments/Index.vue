<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({ assignments: Array });
const form = useForm({});

const deleteAssignment = (id) => {
    if (confirm('Are you sure you want to delete this assignment?')) {
        form.delete(route('teacher.assignments.destroy', id));
    }
};
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-[#0c1a3a] via-[#1e3a8a] via-[#1e40af] to-[#3730a3] text-gray-900 font-sans pb-16">

        <main class="max-w-5xl mx-auto px-4 md:px-0 pt-10 space-y-6">

            <section class="bg-white rounded-[24px] p-6 md:p-8 shadow-xl flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="space-y-1">
                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold tracking-wide">
                        <i class="fa-solid fa-briefcase text-xs mr-1"></i> Assignment Manager
                    </span>
                    <h1 class="text-2xl md:text-3xl font-bold text-blue-950 mt-1">Coursework Activities</h1>
                    <p class="text-sm text-gray-500">Review, modify, or append technical task instructions for students.</p>
                </div>
                
                <Link 
                    :href="route('teacher.assignments.create')" 
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-purple-700 to-purple-600 hover:from-purple-800 hover:to-purple-700 text-white px-5 py-3 rounded-xl text-xs font-bold shadow-lg transition duration-150 hover:-translate-y-0.5 whitespace-nowrap w-full sm:w-auto justify-center"
                >
                    <i class="fa-solid fa-plus"></i> Create New Coursework
                </Link>
            </section>

            <section class="bg-white rounded-[24px] shadow-xl border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-sm">
                        
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100 text-gray-600 font-bold">
                                <th class="p-4 md:p-5 text-left uppercase tracking-wider text-xs">Activity Task Title</th>
                                <th class="p-4 md:p-5 text-left uppercase tracking-wider text-xs">Total Items / Prompts</th>
                                <th class="p-4 md:p-5 text-center uppercase tracking-wider text-xs">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 bg-white">
                            <tr 
                                v-for="assignment in assignments" 
                                :key="assignment.id" 
                                class="hover:bg-blue-50/30 transition duration-150"
                            >
                                <td class="p-4 md:p-5 font-bold text-blue-950">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-sm">
                                            <i class="fa-solid fa-scroll text-xs"></i>
                                        </div>
                                        <span class="hover:text-purple-700 cursor-pointer transition">{{ assignment.title }}</span>
                                    </div>
                                </td>

                                <td class="p-4 md:p-5 font-medium text-gray-600">
                                    <span class="bg-gray-100 text-gray-700 font-bold px-2.5 py-1 rounded-lg text-xs border border-gray-200">
                                        {{ assignment.questions_count || 0 }} {{ (assignment.questions_count || 0) > 1 ? 'items' : 'item' }}
                                    </span>
                                </td>

                                <td class="p-4 md:p-5 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <Link 
                                            :href="route('teacher.assignments.edit', assignment.id)" 
                                            class="inline-flex items-center gap-1 px-3 py-2 bg-gray-50 text-amber-600 border border-gray-100 hover:border-amber-200 hover:bg-amber-50 rounded-xl text-xs font-bold transition"
                                            title="Modify Assignment Details"
                                        >
                                            <i class="fa-solid fa-pen text-[11px]"></i> Edit
                                        </Link>
                                        
                                        <button 
                                            @click="deleteAssignment(assignment.id)" 
                                            class="inline-flex items-center gap-1 px-3 py-2 bg-gray-50 text-red-600 border border-gray-100 hover:border-red-200 hover:bg-red-50 rounded-xl text-xs font-bold transition"
                                            title="Purge Assignment Record"
                                        >
                                            <i class="fa-solid fa-trash-can text-[11px]"></i> Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="assignments.length === 0">
                                <td colspan="3" class="p-12 text-center text-gray-400 italic font-medium">
                                    <div class="space-y-2">
                                        <i class="fa-solid fa-inbox text-3xl text-gray-300"></i>
                                        <p class="text-sm">No assignment records deployed in this faculty folder repository.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>

                    </table>
                </div>
            </section>
            
        </main>
    </div>
</template>