import React from "react";

function Input({ className = "", ...props }) {
    return (
        <input
            data-slot="input"
            className={`flex h-9 w-full min-w-0 rounded-md border border-gray-300 bg-white px-3 py-1 text-sm text-gray-900 shadow-sm outline-none transition placeholder:text-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500 ${className}`}
            {...props}
        />
    );
}

export { Input };
