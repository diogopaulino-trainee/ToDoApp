import { computed, onUnmounted, ref } from 'vue';

/**
 * Composable function that encapsulates the Pomodoro logic.
 * This must be called inside a component's setup (or <script setup>) so
 * that lifecycle hooks (like onUnmounted) are properly registered.
 */
export function usePomodoro() {
    /**
     * Reactive variable for the Pomodoro duration (in minutes).
     */
    const pomodoroMinutes = ref<number>(25);

    /**
     * Reactive variable for the remaining time (in seconds).
     */
    const remainingTime = ref<number>(pomodoroMinutes.value * 60);

    /**
     * Indicates whether the Pomodoro timer is currently running.
     */
    const isPomodoroRunning = ref<boolean>(false);

    /**
     * Indicates whether the Pomodoro timer has finished.
     */
    const isPomodoroFinished = ref<boolean>(false);

    let timerInterval: number | null = null;

    /**
     * Computed property to format the remaining time as "MM:SS".
     */
    const formattedTime = computed(() => {
        const safeTime = Math.max(remainingTime.value, 0);
        const minutes = Math.floor(safeTime / 60);
        const seconds = safeTime % 60;

        return `${String(minutes).padStart(2, '0')}:${seconds.toFixed(2).padStart(5, '0')}`;
    });

    /**
     * Starts the Pomodoro timer.
     * If a duration (in minutes) is provided, it resets the timer accordingly.
     */
    function startPomodoro(minutes?: number) {
        if (minutes) {
            pomodoroMinutes.value = minutes;
            remainingTime.value = minutes * 60;
        }
        if (timerInterval !== null) {
            clearInterval(timerInterval);
        }
        isPomodoroRunning.value = true;
        isPomodoroFinished.value = false;
        timerInterval = window.setInterval(() => {
            if (remainingTime.value > 0) {
                remainingTime.value--;
            } else {
                // Time's up!
                isPomodoroRunning.value = false;
                isPomodoroFinished.value = true;
                clearInterval(timerInterval!);
                timerInterval = null;
                playPlimSound();
            }
        }, 1000);
    }

    /**
     * Resets the Pomodoro timer to the initially set minutes.
     */
    function resetPomodoro() {
        if (timerInterval !== null) {
            clearInterval(timerInterval);
            timerInterval = null;
        }
        remainingTime.value = pomodoroMinutes.value * 60;
        isPomodoroRunning.value = false;
        isPomodoroFinished.value = false;
    }

    /**
     * Plays a sound to signal the end of the Pomodoro.
     */
    function playPlimSound() {
        const audio = new Audio('/sounds/plimsound.wav');
        audio.play().catch((error) => {
            console.error('Error playing sound:', error);
        });
    }

    /**
     * Cleans up the interval when the component using this composable is unmounted.
     */
    onUnmounted(() => {
        if (timerInterval !== null) {
            clearInterval(timerInterval);
        }
    });

    /**
     * Returns the Pomodoro timer state and methods.
     *
     * @returns Object containing Pomodoro timer state and methods
     */
    return {
        pomodoroMinutes,
        remainingTime,
        isPomodoroRunning,
        isPomodoroFinished,
        formattedTime,
        startPomodoro,
        resetPomodoro,
    };
}
