import React from "react";

function Textarea({ className = "", ...props }) {
    return (
        <textarea
            data-slot="textarea"
            className={`flex min-h-[96px] w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm outline-none transition placeholder:text-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500 ${className}`}
            {...props}
        />
    );
}

export { Textarea };
