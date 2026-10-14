import { onMounted, onUnmounted, ref } from 'vue';

/** Live "HH:MM" clock for a given IANA timezone, refreshed every 15s. */
export function useLocalTime(timeZone = 'UTC') {
    const time = ref('');
    let timer: ReturnType<typeof setInterval> | undefined;

    const format = () => {
        time.value = new Intl.DateTimeFormat('en-GB', {
            timeZone,
            hour: '2-digit',
            minute: '2-digit',
        }).format(new Date());
    };

    onMounted(() => {
        format();
        timer = setInterval(format, 15_000);
    });
    onUnmounted(() => clearInterval(timer));

    return time;
}
