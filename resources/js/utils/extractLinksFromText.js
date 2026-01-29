/**
 * Extracts links and text from a string in order.
 * @param {string} input - The input string.
 * @returns {Array<{ type: "text" | "link", content: string }>} - Ordered blocks.
 */
const extractLinksFromText = (input) => {
    if (!input) return [];

    const urlRegex = /(https?:\/\/[^\s]+)/g;
    const blocks = [];
    let lastIndex = 0;
    let match;

    while ((match = urlRegex.exec(input)) !== null) {
        // Text before the link
        if (match.index > lastIndex) {
            const textPart = input.substring(lastIndex, match.index).trim();
            if (textPart) {
                blocks.push({ type: "text", content: textPart });
            }
        }

        // The link itself
        blocks.push({ type: "link", content: match[0] });

        lastIndex = match.index + match[0].length;
    }

    // Remaining text after last link
    const remaining = input.substring(lastIndex).trim();
    if (remaining) {
        blocks.push({ type: "text", content: remaining });
    }

    return blocks;
};

export default extractLinksFromText;
