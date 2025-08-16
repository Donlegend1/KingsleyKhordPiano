
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
