<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { BookOpenCheck, Brain, Coffee, ExternalLink, HelpCircle, Lightbulb, ListChecks, Target, Timer, Zap } from 'lucide-vue-next';
import { computed, ref } from 'vue';

/**
 * Define the Tip interface.
 */
interface Tip {
    text: string;
    icon: any;
    explanation: string;
}

/**
 * Define the QuizOption interface.
 */
interface QuizOption {
    text: string;
    value: 'sprint' | 'zen' | 'deadline' | 'collaborative' | 'creative' | 'analytical' | 'perfectionist' | 'adaptive' | 'proactive' | 'laidBack';
}

/**
 * Define the QuizQuestion interface.
 */
interface QuizQuestion {
    question: string;
    options: QuizOption[];
}

/**
 * Define the allTips array.
 */
const allTips: Tip[] = [
    {
        /**
         * Define the first tip.
         */
        text: 'Break tasks into manageable steps to stay focused.',
        icon: Lightbulb,
        explanation: 'Divide large tasks into 2–4 smaller subtasks. This reduces overwhelm and gives a sense of progress with each small win.',
    },
    {
        /**
         * Define the second tip.
         */
        text: 'Use the Pomodoro technique to boost energy and focus.',
        icon: Timer,
        explanation:
            'Set a timer for 25 minutes of work followed by a 5-minute break. After 4 cycles, take a longer break. This keeps your brain fresh.',
    },
    {
        /**
         * Define the third tip.
         */
        text: 'Clear your workspace — clear your thoughts.',
        icon: Brain,
        explanation: 'A clutter-free desk helps reduce cognitive distractions. Put away unused items before starting work.',
    },
    {
        /**
         * Define the fourth tip.
         */
        text: 'Take mindful breaks — a short pause improves clarity.',
        icon: Coffee,
        explanation: 'Step away from your screen every hour. Stretch, breathe, or take a short walk to reset your focus.',
    },
    {
        /**
         * Define the fifth tip.
         */
        text: 'Start with your top priority each day.',
        icon: Target,
        explanation: 'Each morning, choose your “main mission.” Completing it early builds momentum for the rest of the day.',
    },
    {
        /**
         * Define the sixth tip.
         */
        text: 'Celebrate small wins. They build long-term momentum.',
        icon: ListChecks,
        explanation: 'Reward yourself after finishing tasks — even small ones. Motivation grows through consistent recognition.',
    },
    {
        /**
         * Define the seventh tip.
         */
        text: 'Avoid multitasking. Focus on one task at a time.',
        icon: Brain,
        explanation: 'Multitasking reduces productivity. Focused attention leads to better results.',
    },
    {
        /**
         * Define the eighth tip.
         */
        text: 'Use deadlines to create urgency.',
        icon: Timer,
        explanation: 'Even artificial deadlines push you to finish tasks faster and avoid procrastination.',
    },
    {
        /**
         * Define the ninth tip.
         */
        text: 'Declutter digital space.',
        icon: Coffee,
        explanation: 'Organize your folders and close unused tabs. A clean screen means a clean mind.',
    },
    {
        /**
         * Define the tenth tip.
         */
        text: 'Track your energy levels.',
        icon: Zap,
        explanation: 'Find your peak hours and schedule your most important tasks then.',
    },
    {
        /**
         * Define the eleventh tip.
         */
        text: 'Use checklists to reduce mental load.',
        icon: ListChecks,
        explanation: 'Checking off tasks creates a sense of accomplishment and keeps you focused.',
    },
    {
        /**
         * Define the twelfth tip.
         */
        text: 'Review your goals weekly.',
        icon: Lightbulb,
        explanation: 'Spend 10 minutes reviewing what went well and what needs adjustment.',
    },
];

/**
 * Define the showAllTips ref.
 */
const showAllTips = ref(false);

/**
 * Define the displayedTips computed property.
 */
const displayedTips = computed(() => (showAllTips.value ? allTips : allTips.slice(0, 6)));

/**
 * Define the toggleTips function.
 */
const toggleTips = () => (showAllTips.value = !showAllTips.value);

/**
 * Define the expandedIndex ref.
 */
const expandedIndex = ref<number | null>(null);

/**
 * Define the toggleExpand function.
 */
function toggleExpand(index: number) {
    expandedIndex.value = expandedIndex.value === index ? null : index;
}

/**
 * Define the faqs array.
 */
const faqs = [
    {
        /**
         * Define the first faq.
         */
        q: 'How does the AI Assistant work?',
        a: "It detects keywords like 'tasks', 'profile', or 'dashboard' and responds with links, tips, or motivation.",
    },
    {
        /**
         * Define the second faq.
         */
        q: 'Can I customize or reorder my tasks?',
        a: 'Yes! You can freely add, edit, mark as done, or reorder your tasks.',
    },
    {
        /**
         * Define the third faq.
         */
        q: 'What happens if I close the browser?',
        a: 'Your tasks and progress are saved automatically in your account.',
    },
    {
        /**
         * Define the fourth faq.
         */
        q: 'How do I deal with burnout?',
        a: 'Try the 20-20-20 rule, and allow moments of silence. Don’t hesitate to pause — progress needs balance.',
    },
    {
        /**
         * Define the fifth faq.
         */
        q: 'Can this tool help with academic or personal projects?',
        a: 'Absolutely. Plan project phases as tasks, and let the assistant keep you motivated and on track.',
    },
    {
        /**
         * Define the sixth faq.
         */
        q: 'Is there a mobile version?',
        a: 'Yes! The app is fully responsive and can be used on phones and tablets.',
    },
    {
        /**
         * Define the seventh faq.
         */
        q: 'Can I collaborate with others?',
        a: 'Collaborative features are planned for future updates. Stay tuned!',
    },
];

/**
 * Define the resources array.
 */
const resources = [
    {
        /**
         * Define the first resource.
         */
        label: 'Todoist Blog (Planning Tips)',
        url: 'https://blog.todoist.com/',
    },
    {
        /**
         * Define the second resource.
         */
        label: 'Notion Templates for Students',
        url: 'https://www.notion.so/templates/students',
    },
    {
        /**
         * Define the third resource.
         */
        label: 'Pomofocus (Free Pomodoro Timer)',
        url: 'https://pomofocus.io/',
    },
    {
        /**
         * Define the fourth resource.
         */
        label: 'Mindfulness Exercises (Calm)',
        url: 'https://www.calm.com/',
    },
    {
        /**
         * Define the fifth resource.
         */
        label: 'Mental Health & Focus Guide',
        url: 'https://www.headspace.com/mindfulness',
    },
];

/**
 * Define the moods array.
 */
const moods = [
    {
        /**
         * Define the first mood.
         */
        label: 'Stressed',
        value: 'stressed',
    },
    {
        /**
         * Define the second mood.
         */
        label: 'Focused',
        value: 'focused',
    },
    {
        /**
         * Define the third mood.
         */
        label: 'Tired',
        value: 'tired',
    },
    {
        /**
         * Define the fourth mood.
         */
        label: 'Relaxed',
        value: 'relaxed',
    },
    {
        /**
         * Define the fifth mood.
         */
        label: 'Overwhelmed',
        value: 'overwhelmed',
    },
    {
        /**
         * Define the sixth mood.
         */
        label: 'Motivated',
        value: 'motivated',
    },
    {
        /**
         * Define the seventh mood.
         */
        label: 'Bored',
        value: 'bored',
    },
    {
        /**
         * Define the eighth mood.
         */
        label: 'Procrastinating',
        value: 'procrastinating',
    },
    {
        /**
         * Define the ninth mood.
         */
        label: 'Energized',
        value: 'energized',
    },
    {
        /**
         * Define the tenth mood.
         */
        label: 'Calm',
        value: 'calm',
    },
];

/**
 * Define the currentMood ref.
 */
const currentMood = ref<string>('focused');

/**
 * Define the moodSuggestions object.
 */
const moodSuggestions: Record<string, { phrase: string; tip: string }> = {
    stressed: {
        /**
         * Define the first mood suggestion.
         */
        phrase: 'Take a deep breath. One step at a time can overcome anything.',
        tip: 'Try a quick 5-minute meditation or go for a short walk to clear your head.',
    },
    focused: {
        /**
         * Define the second mood suggestion.
         */
        phrase: 'Use this focus to tackle your most important tasks!',
        tip: 'Set clear goals and keep up the momentum with brief, regular breaks.',
    },
    tired: {
        /**
         * Define the third mood suggestion.
         */
        phrase: 'Rest is crucial for productivity. Recharge to come back stronger.',
        tip: 'Take a short break, drink water, or do light stretches to wake up.',
    },
    relaxed: {
        /**
         * Define the fourth mood suggestion.
         */
        phrase: 'A calm state is perfect for planning and reflection.',
        tip: 'Review your tasks and outline a smooth plan for the week.',
    },
    overwhelmed: {
        /**
         * Define the fifth mood suggestion.
         */
        phrase: 'Break down tasks and prioritize. You can handle this step by step.',
        tip: 'List out tasks in small chunks and celebrate each small completion.',
    },
    motivated: {
        /**
         * Define the sixth mood suggestion.
         */
        phrase: 'Your motivation is high! Channel it into tasks you’ve been putting off.',
        tip: 'Focus on key goals and ride this wave of inspiration to make real progress.',
    },
    bored: {
        /**
         * Define the seventh mood suggestion.
         */
        phrase: 'Try switching up your routine or environment to spark interest.',
        tip: 'Add a creative twist to your tasks or set mini-challenges to stay engaged.',
    },
    procrastinating: {
        /**
         * Define the eighth mood suggestion.
         */
        phrase: 'Recognize the delay and take one small action to start moving forward.',
        tip: 'Use a timer to push through the first few minutes; momentum will build naturally.',
    },
    energized: {
        /**
         * Define the ninth mood suggestion.
         */
        phrase: 'You have plenty of energy—put it to good use!',
        tip: 'Tackle high-effort tasks now, and consider helping others or learning new skills.',
    },
    calm: {
        /**
         * Define the tenth mood suggestion.
         */
        phrase: 'Perfect time for steady, thoughtful progress.',
        tip: 'Maintain this peaceful mindset by avoiding distractions and planning breaks.',
    },
};

/**
 * Define the quizQuestions array.
 */
const quizQuestions: QuizQuestion[] = [
    {
        /**
         * Define the first question.
         */
        question: '1) How do you typically begin your workday?',
        options: [
            {
                /**
                 * Define the first option.
                 */
                text: 'Dive in and try to complete tasks quickly (Sprint).',
                value: 'sprint',
            },
            {
                /**
                 * Define the second option.
                 */
                text: 'Set a calm environment before starting (Zen).',
                value: 'zen',
            },
            {
                /**
                 * Define the third option.
                 */
                text: 'Check deadlines and focus on urgent tasks (Deadline).',
                value: 'deadline',
            },
            {
                /**
                 * Define the fourth option.
                 */
                text: 'Coordinate with others to align efforts (Collaborative).',
                value: 'collaborative',
            },
        ],
    },
    {
        /**
         * Define the second question.
         */
        question: '2) When a big project appears, how do you handle it?',
        options: [
            {
                /**
                 * Define the first option.
                 */
                text: 'Break it down and go fast in short bursts (Sprint).',
                value: 'sprint',
            },
            {
                /**
                 * Define the second option.
                 */
                text: 'Stay calm and maintain a steady pace (Zen).',
                value: 'zen',
            },
            {
                /**
                 * Define the third option.
                 */
                text: 'Set deadlines and work backward (Deadline).',
                value: 'deadline',
            },
            {
                /**
                 * Define the fourth option.
                 */
                text: 'Brainstorm with others for shared solutions (Collaborative).',
                value: 'collaborative',
            },
        ],
    },
    {
        /**
         * Define the third question.
         */
        question: '3) How do you feel most accomplished?',
        options: [
            {
                /**
                 * Define the first option.
                 */
                text: 'When I finish tasks ahead of time (Proactive).',
                value: 'proactive',
            },
            {
                /**
                 * Define the second option.
                 */
                text: 'When I reach a calm, productive flow (Zen).',
                value: 'zen',
            },
            {
                /**
                 * Define the third option.
                 */
                text: 'When I meet or beat crucial deadlines (Deadline).',
                value: 'deadline',
            },
            {
                /**
                 * Define the fourth option.
                 */
                text: 'When I generate unique or creative solutions (Creative).',
                value: 'creative',
            },
        ],
    },
];

/**
 * Define the quizAnswers ref.
 */
const quizAnswers = ref<(QuizOption['value'] | null)[]>(quizQuestions.map(() => null));

/**
 * Define the allAnswered computed property.
 */
const allAnswered = computed(() => quizAnswers.value.every((answer) => answer !== null));

/**
 * Define the quizResult ref.
 */
const quizResult = ref<QuizOption['value'] | null>(null);

/**
 * Define the calculateQuizResult function.
 */
function calculateQuizResult() {
    /**
     * If the quiz is not all answered, return.
     */
    if (!allAnswered.value) return;

    /**
     * Define the question weights.
     */
    const questionWeights = [3, 2, 1];

    /**
     * Define the weighted tally.
     */
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

    /**
     * Define the weighted tally.
     */
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

/**
 * Define the productivityStyles object.
 */
const productivityStyles: Record<QuizOption['value'], { title: string; description: string }> = {
    sprint: {
        /**
         * Define the first productivity style.
         */
        title: 'Sprint-Focused',
        description: 'You love speed and finishing tasks fast. Remember to balance with breaks to avoid burnout.',
    },
    zen: {
        /**
         * Define the second productivity style.
         */
        title: 'Zen-Flow',
        description: 'You prefer a calm, steady flow. Keep a peaceful environment and guard against distractions.',
    },
    deadline: {
        /**
         * Define the third productivity style.
         */
        title: 'Deadline-Driven',
        description: 'You work best under time pressure. Plan carefully to reduce stress and meet those deadlines.',
    },
    collaborative: {
        /**
         * Define the fourth productivity style.
         */
        title: 'Collaborative',
        description: 'You thrive by working with others. Sharing ideas and responsibilities is your strength.',
    },
    creative: {
        /**
         * Define the fifth productivity style.
         */
        title: 'Creative',
        description: 'You excel in generating innovative ideas. Keep exploring new angles and solutions.',
    },
    analytical: {
        /**
         * Define the sixth productivity style.
         */
        title: 'Analytical',
        description: 'You rely on logic and data. Systematic thinking helps you produce accurate results.',
    },
    perfectionist: {
        /**
         * Define the seventh productivity style.
         */
        title: 'Perfectionist',
        description: 'You aim for flawless outcomes. Don’t forget to balance time and avoid overthinking.',
    },
    adaptive: {
        /**
         * Define the eighth productivity style.
         */
        title: 'Adaptive',
        description: 'You adjust quickly to new situations. Flexibility is your key strength.',
    },
    proactive: {
        /**
         * Define the ninth productivity style.
         */
        title: 'Proactive',
        description: 'You stay ahead of the curve. Tackling tasks early helps you avoid last-minute rushes.',
    },
    laidBack: {
        /**
         * Define the tenth productivity style.
         */
        title: 'Laid-Back',
        description: 'You handle tasks calmly and prefer minimal stress. Just be mindful of deadlines and motivation.',
    },
};

/**
 * Define the showExtras ref.
 */
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
