<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { BookOpenCheck, Brain, Coffee, ExternalLink, HelpCircle, Lightbulb, ListChecks, Target, Timer, Zap } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Tip {
    text: string;
    icon: any;
    explanation: string;
}

interface QuizOption {
    text: string;
    value: 'sprint' | 'zen' | 'deadline' | 'collaborative' | 'creative' | 'analytical' | 'perfectionist' | 'adaptive' | 'proactive' | 'laidBack';
}

interface QuizQuestion {
    question: string;
    options: QuizOption[];
}

const allTips: Tip[] = [
    {
        text: 'Break tasks into manageable steps to stay focused.',
        icon: Lightbulb,
        explanation: 'Divide large tasks into 2–4 smaller subtasks. This reduces overwhelm and gives a sense of progress with each small win.',
    },
    {
        text: 'Use the Pomodoro technique to boost energy and focus.',
        icon: Timer,
        explanation:
            'Set a timer for 25 minutes of work followed by a 5-minute break. After 4 cycles, take a longer break. This keeps your brain fresh.',
    },
    {
        text: 'Clear your workspace — clear your thoughts.',
        icon: Brain,
        explanation: 'A clutter-free desk helps reduce cognitive distractions. Put away unused items before starting work.',
    },
    {
        text: 'Take mindful breaks — a short pause improves clarity.',
        icon: Coffee,
        explanation: 'Step away from your screen every hour. Stretch, breathe, or take a short walk to reset your focus.',
    },
    {
        text: 'Start with your top priority each day.',
        icon: Target,
        explanation: 'Each morning, choose your “main mission.” Completing it early builds momentum for the rest of the day.',
    },
    {
        text: 'Celebrate small wins. They build long-term momentum.',
        icon: ListChecks,
        explanation: 'Reward yourself after finishing tasks — even small ones. Motivation grows through consistent recognition.',
    },
    {
        text: 'Avoid multitasking. Focus on one task at a time.',
        icon: Brain,
        explanation: 'Multitasking reduces productivity. Focused attention leads to better results.',
    },
    {
        text: 'Use deadlines to create urgency.',
        icon: Timer,
        explanation: 'Even artificial deadlines push you to finish tasks faster and avoid procrastination.',
    },
    {
        text: 'Declutter digital space.',
        icon: Coffee,
        explanation: 'Organize your folders and close unused tabs. A clean screen means a clean mind.',
    },
    {
        text: 'Track your energy levels.',
        icon: Zap,
        explanation: 'Find your peak hours and schedule your most important tasks then.',
    },
    {
        text: 'Use checklists to reduce mental load.',
        icon: ListChecks,
        explanation: 'Checking off tasks creates a sense of accomplishment and keeps you focused.',
    },
    {
        text: 'Review your goals weekly.',
        icon: Lightbulb,
        explanation: 'Spend 10 minutes reviewing what went well and what needs adjustment.',
    },
];

const showAllTips = ref(false);
const displayedTips = computed(() => (showAllTips.value ? allTips : allTips.slice(0, 6)));
const toggleTips = () => (showAllTips.value = !showAllTips.value);

const expandedIndex = ref<number | null>(null);
function toggleExpand(index: number) {
    expandedIndex.value = expandedIndex.value === index ? null : index;
}

const faqs = [
    {
        q: 'How does the AI Assistant work?',
        a: "It detects keywords like 'tasks', 'profile', or 'dashboard' and responds with links, tips, or motivation.",
    },
    {
        q: 'Can I customize or reorder my tasks?',
        a: 'Yes! You can freely add, edit, mark as done, or reorder your tasks.',
    },
    {
        q: 'What happens if I close the browser?',
        a: 'Your tasks and progress are saved automatically in your account.',
    },
    {
        q: 'How do I deal with burnout?',
        a: 'Try the 20-20-20 rule, and allow moments of silence. Don’t hesitate to pause — progress needs balance.',
    },
    {
        q: 'Can this tool help with academic or personal projects?',
        a: 'Absolutely. Plan project phases as tasks, and let the assistant keep you motivated and on track.',
    },
    {
        q: 'Is there a mobile version?',
        a: 'Yes! The app is fully responsive and can be used on phones and tablets.',
    },
    {
        q: 'Can I collaborate with others?',
        a: 'Collaborative features are planned for future updates. Stay tuned!',
    },
];

const resources = [
    { label: 'Todoist Blog (Planning Tips)', url: 'https://blog.todoist.com/' },
    {
        label: 'Notion Templates for Students',
        url: 'https://www.notion.so/templates/students',
    },
    { label: 'Pomofocus (Free Pomodoro Timer)', url: 'https://pomofocus.io/' },
    { label: 'Mindfulness Exercises (Calm)', url: 'https://www.calm.com/' },
    {
        label: 'Mental Health & Focus Guide',
        url: 'https://www.headspace.com/mindfulness',
    },
];

const moods = [
    { label: 'Stressed', value: 'stressed' },
    { label: 'Focused', value: 'focused' },
    { label: 'Tired', value: 'tired' },
    { label: 'Relaxed', value: 'relaxed' },
    { label: 'Overwhelmed', value: 'overwhelmed' },
    { label: 'Motivated', value: 'motivated' },
    { label: 'Bored', value: 'bored' },
    { label: 'Procrastinating', value: 'procrastinating' },
    { label: 'Energized', value: 'energized' },
    { label: 'Calm', value: 'calm' },
];

const currentMood = ref<string>('focused');

const moodSuggestions: Record<string, { phrase: string; tip: string }> = {
    stressed: {
        phrase: 'Take a deep breath. One step at a time can overcome anything.',
        tip: 'Try a quick 5-minute meditation or go for a short walk to clear your head.',
    },
    focused: {
        phrase: 'Use this focus to tackle your most important tasks!',
        tip: 'Set clear goals and keep up the momentum with brief, regular breaks.',
    },
    tired: {
        phrase: 'Rest is crucial for productivity. Recharge to come back stronger.',
        tip: 'Take a short break, drink water, or do light stretches to wake up.',
    },
    relaxed: {
        phrase: 'A calm state is perfect for planning and reflection.',
        tip: 'Review your tasks and outline a smooth plan for the week.',
    },
    overwhelmed: {
        phrase: 'Break down tasks and prioritize. You can handle this step by step.',
        tip: 'List out tasks in small chunks and celebrate each small completion.',
    },
    motivated: {
        phrase: 'Your motivation is high! Channel it into tasks you’ve been putting off.',
        tip: 'Focus on key goals and ride this wave of inspiration to make real progress.',
    },
    bored: {
        phrase: 'Try switching up your routine or environment to spark interest.',
        tip: 'Add a creative twist to your tasks or set mini-challenges to stay engaged.',
    },
    procrastinating: {
        phrase: 'Recognize the delay and take one small action to start moving forward.',
        tip: 'Use a timer to push through the first few minutes; momentum will build naturally.',
    },
    energized: {
        phrase: 'You have plenty of energy—put it to good use!',
        tip: 'Tackle high-effort tasks now, and consider helping others or learning new skills.',
    },
    calm: {
        phrase: 'Perfect time for steady, thoughtful progress.',
        tip: 'Maintain this peaceful mindset by avoiding distractions and planning breaks.',
    },
};

const quizQuestions: QuizQuestion[] = [
    {
        question: '1) How do you typically begin your workday?',
        options: [
            {
                text: 'Dive in and try to complete tasks quickly (Sprint).',
                value: 'sprint',
            },
            {
                text: 'Set a calm environment before starting (Zen).',
                value: 'zen',
            },
            {
                text: 'Check deadlines and focus on urgent tasks (Deadline).',
                value: 'deadline',
            },
            {
                text: 'Coordinate with others to align efforts (Collaborative).',
                value: 'collaborative',
            },
        ],
    },
    {
        question: '2) When a big project appears, how do you handle it?',
        options: [
            {
                text: 'Break it down and go fast in short bursts (Sprint).',
                value: 'sprint',
            },
            {
                text: 'Stay calm and maintain a steady pace (Zen).',
                value: 'zen',
            },
            {
                text: 'Set deadlines and work backward (Deadline).',
                value: 'deadline',
            },
            {
                text: 'Brainstorm with others for shared solutions (Collaborative).',
                value: 'collaborative',
            },
        ],
    },
    {
        question: '3) How do you feel most accomplished?',
        options: [
            {
                text: 'When I finish tasks ahead of time (Proactive).',
                value: 'proactive',
            },
            {
                text: 'When I reach a calm, productive flow (Zen).',
                value: 'zen',
            },
            {
                text: 'When I meet or beat crucial deadlines (Deadline).',
                value: 'deadline',
            },
            {
                text: 'When I generate unique or creative solutions (Creative).',
                value: 'creative',
            },
        ],
    },
];

const quizAnswers = ref<(QuizOption['value'] | null)[]>(quizQuestions.map(() => null));

const allAnswered = computed(() => quizAnswers.value.every((answer) => answer !== null));

const quizResult = ref<QuizOption['value'] | null>(null);

function calculateQuizResult() {
    if (!allAnswered.value) return;

    const questionWeights = [3, 2, 1];

    const weightedTally: Record<QuizOption['value'], number> = {
        sprint: 0,
        zen: 0,
        deadline: 0,
        collaborative: 0,
        creative: 0,
        analytical: 0,
        perfectionist: 0,
        adaptive: 0,
        proactive: 0,
        laidBack: 0,
    };

    quizAnswers.value.forEach((answer, index) => {
        if (answer) {
            weightedTally[answer] += questionWeights[index];
        }
    });

    let maxType: QuizOption['value'] = 'sprint';
    let maxScore = 0;
    for (const type in weightedTally) {
        if (weightedTally[type as QuizOption['value']] > maxScore) {
            maxType = type as QuizOption['value'];
            maxScore = weightedTally[type as QuizOption['value']];
        }
    }

    quizResult.value = maxType;
}

const productivityStyles: Record<QuizOption['value'], { title: string; description: string }> = {
    sprint: {
        title: 'Sprint-Focused',
        description: 'You love speed and finishing tasks fast. Remember to balance with breaks to avoid burnout.',
    },
    zen: {
        title: 'Zen-Flow',
        description: 'You prefer a calm, steady flow. Keep a peaceful environment and guard against distractions.',
    },
    deadline: {
        title: 'Deadline-Driven',
        description: 'You work best under time pressure. Plan carefully to reduce stress and meet those deadlines.',
    },
    collaborative: {
        title: 'Collaborative',
        description: 'You thrive by working with others. Sharing ideas and responsibilities is your strength.',
    },
    creative: {
        title: 'Creative',
        description: 'You excel in generating innovative ideas. Keep exploring new angles and solutions.',
    },
    analytical: {
        title: 'Analytical',
        description: 'You rely on logic and data. Systematic thinking helps you produce accurate results.',
    },
    perfectionist: {
        title: 'Perfectionist',
        description: 'You aim for flawless outcomes. Don’t forget to balance time and avoid overthinking.',
    },
    adaptive: {
        title: 'Adaptive',
        description: 'You adjust quickly to new situations. Flexibility is your key strength.',
    },
    proactive: {
        title: 'Proactive',
        description: 'You stay ahead of the curve. Tackling tasks early helps you avoid last-minute rushes.',
    },
    laidBack: {
        title: 'Laid-Back',
        description: 'You handle tasks calmly and prefer minimal stress. Just be mindful of deadlines and motivation.',
    },
};

const showExtras = ref(false);
</script>

<template>
    <AppLayout title="Help & Motivation">
        <Head title="Help & Motivation" />

        <div class="neon-wrapper min-h-screen bg-gradient-to-b from-[#0f172a] to-black px-6 py-16 text-white">
            <section
                class="mb-12 rounded-xl border border-cyan-400/30 bg-gradient-to-r from-cyan-500/10 to-blue-500/10 p-8 text-center shadow-2xl backdrop-blur-md"
            >
                <h2 class="mb-2 flex animate-pulse items-center justify-center gap-2 text-4xl font-bold tracking-wide text-cyan-200">
                    <Zap class="h-6 w-6" /> Welcome to Your Mind Lab
                </h2>
                <p class="mx-auto max-w-3xl text-gray-300">Explore techniques, strategies, and tools to boost focus, energy, and peace of mind.</p>
            </section>

            <section class="mb-20">
                <h2 class="mb-8 flex items-center gap-2 text-3xl font-bold text-cyan-300">
                    <Lightbulb class="h-6 w-6" /> Interactive Productivity Tips
                </h2>
                <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="(tip, index) in displayedTips"
                        :key="index"
                        class="group relative rounded-xl border border-cyan-400/20 bg-gray-800/70 p-6 shadow-xl backdrop-blur-md transition-all hover:scale-[1.02] hover:ring-1 hover:ring-cyan-400"
                    >
                        <div class="flex cursor-pointer items-start justify-between" @click="toggleExpand(index)">
                            <component :is="tip.icon" class="h-6 w-6 text-cyan-300 transition group-hover:scale-110" />
                            <svg
                                class="transition-transform"
                                :class="{ 'rotate-45': expandedIndex === index }"
                                xmlns="http://www.w3.org/2000/svg"
                                width="20"
                                height="20"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <polyline points="15 3 21 3 21 9" />
                                <polyline points="9 21 3 21 3 15" />
                                <line x1="21" y1="3" x2="14" y2="10" />
                                <line x1="3" y1="21" x2="10" y2="14" />
                            </svg>
                        </div>
                        <p class="mt-3 font-semibold text-gray-100">{{ tip.text }}</p>
                        <transition name="slide-fade">
                            <p v-if="expandedIndex === index" class="mt-3 border-l-2 border-cyan-400 pl-3 text-sm text-gray-300">
                                {{ tip.explanation }}
                            </p>
                        </transition>
                    </div>
                </div>
                <div class="mt-10 text-center">
                    <button
                        @click="toggleTips"
                        class="rounded-full border border-cyan-500 px-6 py-2 text-cyan-300 transition hover:bg-cyan-500 hover:text-black"
                    >
                        {{ showAllTips ? 'Show Less' : 'Show More' }}
                    </button>
                </div>
            </section>

            <section class="mb-20">
                <h2 class="mb-6 flex items-center gap-2 text-2xl font-semibold text-purple-300">
                    <HelpCircle class="h-6 w-6" /> Frequently Asked Questions
                </h2>
                <div class="space-y-4">
                    <details
                        v-for="(faq, index) in faqs"
                        :key="index"
                        class="group cursor-pointer rounded-lg bg-gray-800/70 p-5 shadow transition-all hover:ring-1 hover:ring-purple-400"
                    >
                        <summary class="flex items-center justify-between text-lg font-medium text-white transition group-open:text-purple-300">
                            <span>{{ faq.q }}</span>
                            <svg
                                class="h-5 w-5 text-purple-400 transition-transform group-open:rotate-180"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </summary>
                        <transition name="slide-fade">
                            <p class="mt-3 border-l-2 border-purple-400 pl-3 text-gray-300">
                                {{ faq.a }}
                            </p>
                        </transition>
                    </details>
                </div>
            </section>

            <section>
                <h2 class="mb-6 flex items-center gap-2 text-2xl font-semibold text-emerald-300">
                    <BookOpenCheck class="h-6 w-6" /> Useful Resources
                </h2>
                <ul class="grid gap-4 sm:grid-cols-2 md:grid-cols-3">
                    <li
                        v-for="(r, index) in resources"
                        :key="index"
                        class="flex items-center gap-3 rounded-lg bg-gray-800/70 p-4 transition hover:ring-1 hover:ring-emerald-400"
                    >
                        <ExternalLink class="h-5 w-5 text-emerald-400" />
                        <a :href="r.url" target="_blank" class="text-sm text-white hover:underline">
                            {{ r.label }}
                        </a>
                    </li>
                </ul>
            </section>

            <section class="mt-12 text-center">
                <button
                    class="rounded-full border border-pink-500 px-6 py-2 text-pink-300 transition hover:bg-pink-500 hover:text-black"
                    @click="showExtras = !showExtras"
                >
                    {{ showExtras ? 'Hide Mood Switcher & Quiz' : 'Show Mood Switcher & Quiz' }}
                </button>

                <div v-if="showExtras" class="mt-8 space-y-16 border-t border-pink-500/50 pt-8">
                    <section>
                        <h2 class="mb-4 text-2xl font-bold text-green-300">Mood Switcher (Daily Mental State)</h2>
                        <p class="mb-4 text-sm text-gray-300">
                            Select your current mood to receive personalized tips and suggestions to improve your day.
                        </p>

                        <div class="flex flex-wrap items-center justify-center gap-4">
                            <label v-for="m in moods" :key="m.value" class="flex cursor-pointer items-center space-x-2">
                                <input type="radio" :value="m.value" v-model="currentMood" />
                                <span class="text-white">{{ m.label }}</span>
                            </label>
                        </div>
                        <div class="mx-auto mt-4 max-w-xl rounded border border-green-400 p-4 backdrop-blur-md">
                            <h3 class="font-semibold text-green-200">Current Mood: {{ currentMood }}</h3>
                            <p class="mt-2 text-sm text-gray-100">
                                {{ moodSuggestions[currentMood].phrase }}
                            </p>
                            <p class="mt-1 text-xs text-gray-300">
                                {{ moodSuggestions[currentMood].tip }}
                            </p>
                        </div>
                    </section>

                    <section>
                        <h2 class="mb-6 text-2xl font-semibold text-yellow-300">Interactive Quiz: Discover Your Productivity Style</h2>
                        <p class="mb-4 text-sm text-gray-300">Answer these 3 quick questions to see which productivity style suits you best.</p>

                        <div class="mx-auto max-w-xl space-y-6">
                            <div v-for="(q, qIndex) in quizQuestions" :key="qIndex" class="rounded-lg bg-gray-800/70 p-4 shadow">
                                <p class="font-semibold text-gray-100">
                                    {{ q.question }}
                                </p>
                                <div class="mt-3 space-y-2">
                                    <label v-for="(option, oIndex) in q.options" :key="oIndex" class="flex cursor-pointer items-center space-x-2">
                                        <input type="radio" :name="'question-' + qIndex" :value="option.value" v-model="quizAnswers[qIndex]" />
                                        <span class="text-gray-300">{{ option.text }}</span>
                                    </label>
                                </div>
                            </div>

                            <div class="text-center">
                                <button
                                    class="rounded-full border border-yellow-500 px-6 py-2 text-yellow-300 transition hover:bg-yellow-500 hover:text-black disabled:opacity-50"
                                    :disabled="!allAnswered"
                                    @click="calculateQuizResult"
                                >
                                    Show Result
                                </button>
                            </div>

                            <div v-if="quizResult" class="mt-6 rounded-lg border border-yellow-400 p-4">
                                <h3 class="text-xl font-bold text-yellow-200">
                                    {{ productivityStyles[quizResult].title }}
                                </h3>
                                <p class="mt-2 text-gray-100">
                                    {{ productivityStyles[quizResult].description }}
                                </p>
                            </div>
                        </div>
                    </section>
                </div>
            </section>
        </div>
    </AppLayout>
</template>

<style scoped>
.neon-wrapper {
    border: 1px solid transparent;
    border-radius: 1rem;
    animation: neonGlow 3s ease-in-out infinite alternate;
    box-shadow:
        0 0 10px #0ff3,
        0 0 20px #0ff2,
        0 0 30px #0ff1;
}

@keyframes neonGlow {
    0% {
        box-shadow:
            0 0 10px #0ff3,
            0 0 20px #0ff2,
            0 0 30px #0ff1;
    }
    100% {
        box-shadow:
            0 0 15px #0ff6,
            0 0 30px #0ff5,
            0 0 45px #0ff4;
    }
}

details summary::-webkit-details-marker {
    display: none;
}

.slide-fade-enter-active,
.slide-fade-leave-active {
    transition: all 0.3s ease;
}
.slide-fade-enter-from,
.slide-fade-leave-to {
    opacity: 0;
    transform: translateY(8px);
}

button:disabled {
    cursor: not-allowed;
}
</style>
