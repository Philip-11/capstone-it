<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    subject: Object
});
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-[#0c1a3a] via-[#1e3a8a] via-[#1e40af] via-[#1d4ed8] to-[#3730a3] text-gray-900 font-sans pb-12">
        
        <header class="h-[66px] bg-white border-b-3 border-blue-600 px-[5%] md:px-[9%] flex items-center justify-between shadow-lg sticky top-0 z-50">
            <div class="flex items-center gap-2.5">
                <img src="/images/LOGO.png" alt="School Logo" class="w-[30px] h-[30px] object-contain" />
                <h2 class="text-xl font-bold text-blue-900">GLMS</h2>
            </div>

            <nav class="hidden md:flex items-center gap-[34px]">
                <Link :href="route('student.dashboard')" class="text-blue-600 font-bold text-sm py-6 relative after:content-[''] after:absolute after:left-0 after:bottom-[-3px] after:w-full after:h-[3px] after:bg-blue-600 after:rounded-t">
                    Dashboard
                </Link>
                <Link :href="route('student.quizzes.index')" class="text-gray-600 hover:text-blue-600 text-sm font-semibold transition">
                    Quizzes
                </Link>
                <Link :href="route('student.assignments.index')" class="text-gray-600 hover:text-blue-600 text-sm font-semibold transition">
                    Assignments
                </Link>
                <!-- <a href="#" class="text-gray-600 hover:text-blue-600 text-sm font-semibold transition">Assignments</a> -->
                <!-- <a href="#" class="text-gray-600 hover:text-blue-600 text-sm font-semibold transition">Profile</a> -->
            </nav>

            <Link method="post" href="/logout" as="button" class="bg-gradient-to-r from-blue-800 to-blue-600 text-white px-4 py-2.5 rounded-lg text-xs font-semibold flex items-center gap-2 transition hover:bg-red-600 shadow-md">
                Logout
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
            </Link>
        </header>

        <div class="fixed right-6 bottom-6 w-[58px] h-[58px] rounded-full bg-gradient-to-br from-blue-800 to-blue-600 text-white flex items-center justify-center text-xl shadow-2xl cursor-pointer z-50 hover:from-blue-900 hover:to-blue-800 transition">
            <i class="fa-solid fa-robot"></i>
        </div>

        <main class="max-w-7xl mx-auto px-4 md:px-[9%] pt-11 space-y-7">

            <section class="bg-gradient-to-br from-blue-800 to-blue-600 text-white p-8 md:p-11 rounded-[24px] shadow-xl relative overflow-hidden">
                <span class="bg-white/20 px-3 py-1 rounded-full text-xs tracking-wide">Course Lessons</span>
                <h1 class="text-2xl md:text-4xl font-bold my-4.5 tracking-tight">{{ subject.name }}</h1>
                <p class="text-blue-100 text-sm md:text-base italic mb-5">Instructor: {{ subject.teacher ? subject.teacher.name : 'Unknown' }}</p>

                <Link :href="route('student.dashboard')" class="inline-flex items-center gap-2 bg-white text-blue-600 px-[15px] py-[11px] rounded-xl font-bold text-sm transition hover:-translate-y-0.5 shadow-md active:scale-95">
                    <i class="fa-solid fa-arrow-left"></i>
                    Back to Dashboard
                </Link>
            </section>

            <section class="bg-white rounded-[20px] p-6 shadow-md space-y-4">
                <div class="border-b border-gray-100 pb-3">
                    <h3 class="text-xl font-bold text-blue-900">Available Lessons</h3>
                </div>

                <div v-for="lesson in subject.lessons" :key="lesson.id" class="border border-gray-200 rounded-[18px] p-[18px] grid grid-cols-1 lg:grid-cols-[1fr_auto] gap-5 items-center transition duration-200 hover:border-blue-600 hover:shadow-md">
                    
                    <div class="flex items-start gap-3.5">
                        <div class="w-12 h-12 min-w-[48px] rounded-[14px] bg-blue-100 text-blue-600 flex items-center justify-center text-lg">
                            <i class="fa-solid" :class="lesson.file_path ? 'fa-file-code' : 'fa-code'"></i>
                        </div>
                        <div class="space-y-1">
                            <h4 class="font-bold text-gray-900 text-[1.05rem]">{{ lesson.title }}</h4>
                            <p class="text-gray-500 text-sm leading-relaxed">{{ lesson.content }}</p>
                            
                            <div v-if="lesson.file_path" class="pt-2">
                                <a :href="route('teacher.lessons.download', lesson.id)" class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 px-3 py-1 rounded-lg text-xs font-semibold transition">
                                    <i class="fa-solid fa-cloud-arrow-down"></i>
                                    Download Module File
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3.5 w-full lg:w-auto">
                        <div v-if="lesson.quizzes && lesson.quizzes.length > 0">
                            <Link v-for="(quiz, index) in lesson.quizzes" :key="quiz.id" :href="route('student.quiz.show', quiz.id)" class="bg-gradient-to-br from-blue-800 to-blue-600 hover:from-blue-900 hover:to-blue-800 text-white m-1 px-3.5 py-2.5 rounded-xl font-bold text-sm inline-flex items-center gap-2 whitespace-nowrap shadow-sm transition w-full sm:w-auto justify-center">
                            <i class="fa-solid fa-gamepad text-xs"></i>
                            Take Quiz {{ lesson.quizzes.length > 1 ? (index + 1) : '' }}
                            </Link>
                        </div>
                        

                        <div v-if="lesson.assignments && lesson.assignments.length > 0" class="w-full lg:w-auto space-y-2">
                            <div v-for="assignment in lesson.assignments" :key="assignment.id" class="bg-blue-50/70 border border-blue-100 p-3.5 rounded-[14px] flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3.5 min-w-full sm:min-w-[360px]">
                                <div>
                                    <h4 class="font-bold text-blue-900 text-[0.92rem] flex items-center gap-1.5">
                                        <i class="fa-solid fa-briefcase text-blue-500 text-xs"></i>
                                        {{ assignment.title }}
                                    </h4>
                                    <p class="text-gray-400 text-xs mt-0.5">Max Points: {{ assignment.max_points }} XP</p>
                                </div>
                                <Link :href="route('student.assignments.show', assignment.id)" class="bg-gradient-to-br from-blue-800 to-blue-600 hover:from-blue-900 hover:to-blue-800 text-white text-xs px-3.5 py-2.5 rounded-lg font-bold shadow-sm transition w-full sm:w-auto text-center whitespace-nowrap">
                                    Open Assignment
                                </Link>
                            </div>
                        </div>

                    </div>
                </div>

                <div v-if="subject.lessons.length === 0" class="text-center py-10 text-gray-400 italic text-sm">
                    <i class="fa-solid fa-folder-open text-2xl mb-2 block"></i>
                    No lessons uploaded yet for this subject.
                </div>

            </section>

        </main>
    </div>
</template>

<style>
nav a.active::after {
    content: "";
    position: absolute;
    left: 0;
    bottom: -3px;
    width: 100%;
    height: 3px;
    background: #1d4ed8;
    border-radius: 10px 10px 0 0;
}
</style>