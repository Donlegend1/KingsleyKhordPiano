import dayjs from "dayjs";
import duration from "dayjs/plugin/duration";

dayjs.extend(duration);

export const formatRelativeTime = (dateString) => {
    const now = Date.now(); // current UTC timestamp in ms
    const posted = Date.parse(dateString); // parse ISO date string as UTC

    const diffInSeconds = Math.floor((now - posted) / 1000);

    if (isNaN(diffInSeconds)) return "Invalid date";

    const minutes = Math.floor(diffInSeconds / 60);
    const hours = Math.floor(minutes / 60);
    const days = Math.floor(hours / 24);
    const weeks = Math.floor(days / 7);
    const months = Math.floor(days / 30); // approximate month length
    const years = Math.floor(days / 365); // approximate year length

    if (diffInSeconds < 60) {
        return `${diffInSeconds} second${diffInSeconds !== 1 ? "s" : ""} ago`;
    } else if (minutes < 60) {
        return `${minutes} minute${minutes !== 1 ? "s" : ""} ago`;
    } else if (hours < 24) {
        return `${hours} hour${hours !== 1 ? "s" : ""} ago`;
    } else if (days < 7) {
        return `${days} day${days !== 1 ? "s" : ""} ago`;
    } else if (weeks < 5) {
        return `${weeks} week${weeks !== 1 ? "s" : ""} ago`;
    } else if (months < 12) {
        return `${months} month${months !== 1 ? "s" : ""} ago`;
    } else {
        return `${years} year${years !== 1 ? "s" : ""} ago`;
    }
};

   export const capitaliseAndRemoveHyphen = (text) => {
        return text
            ?.split("_")
            ?.map((word) => word.charAt(0).toUpperCase() + word.slice(1))
            ?.join(" ");
    };


    export const calculateCountdown = (startTime) => {
        const now = dayjs();
        const eventTime = dayjs(startTime);
        const diff = eventTime.diff(now);

        if (diff <= 0) {
            return { days: 0, hours: 0, minutes: 0, seconds: 0 };
        }

        const dur = dayjs.duration(diff);
        return {
            days: Math.floor(dur.asDays()),
            hours: dur.hours(),
            minutes: dur.minutes(),
            seconds: dur.seconds(),
        };
    };

// Add this helper
export const formatLocalTime = (startTime) => {
    const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    
    const date = new Date(startTime);
    
    const localDate = new Intl.DateTimeFormat('en-US', {
        timeZone: userTimezone,
        month: 'short',
        day: '2-digit',
        year: 'numeric',
    }).format(date);

    const localTime = new Intl.DateTimeFormat('en-US', {
        timeZone: userTimezone,
        hour: '2-digit',
        minute: '2-digit',
        hour12: false,
    }).format(date);

    // Get short timezone label e.g. "WAT", "EST", "GMT+1"
    const tzLabel = new Intl.DateTimeFormat('en-US', {
        timeZone: userTimezone,
        timeZoneName: 'short',
    }).formatToParts(date).find(p => p.type === 'timeZoneName')?.value;

    return { localDate, localTime, tzLabel };
};

// Africa/Lagos (WAT) has no DST, so it's always a fixed UTC+1 offset.
// Admin forms collect times in WAT since that's the studio's business timezone.
export const watInputToUtcIso = (datetimeLocalValue) => {
    if (!datetimeLocalValue) return "";
    return new Date(`${datetimeLocalValue}:00+01:00`).toISOString();
};

export const utcIsoToWatInput = (utcValue) => {
    if (!utcValue) return "";
    const date = new Date(utcValue);
    const parts = new Intl.DateTimeFormat('en-CA', {
        timeZone: 'Africa/Lagos',
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        hour12: false,
    }).formatToParts(date).reduce((acc, p) => ({ ...acc, [p.type]: p.value }), {});

    return `${parts.year}-${parts.month}-${parts.day}T${parts.hour}:${parts.minute}`;
};
