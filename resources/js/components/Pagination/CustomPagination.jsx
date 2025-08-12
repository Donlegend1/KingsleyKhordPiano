import React from "react";
import { ChevronLeft, ChevronRight } from "lucide-react";

export default function CustomPagination({ currentPage, totalPages, onPageChange }) {
    if (totalPages <= 1) return null; // No need to render

    const getPages = () => {
        const pages = [];
        const maxButtons = 5;
        let start = Math.max(1, currentPage - Math.floor(maxButtons / 2));
        let end = Math.min(totalPages, start + maxButtons - 1);

        if (end - start < maxButtons - 1) {
            start = Math.max(1, end - maxButtons + 1);
        }

        for (let i = start; i <= end; i++) {
            pages.push(i);
        }
        return pages;
    };

    return (
        <div className="flex items-center justify-center gap-2 mt-6">
            {/* Previous Button */}
            <button
                onClick={() => onPageChange(currentPage - 1)}
                disabled={currentPage === 1}
                className={`p-2 rounded-full border border-gray-300 dark:border-gray-600 ${
                    currentPage === 1
                        ? "opacity-50 cursor-not-allowed"
                        : "hover:bg-gray-200 dark:hover:bg-gray-700"
                }`}
            >
                <ChevronLeft size={18} />
            </button>

            {/* Page Numbers */}
            {getPages().map((page) => (
                <button
                    key={page}
                    onClick={() => onPageChange(page)}
                    className={`px-3 py-1 rounded-md text-sm border ${
                        currentPage === page
                            ? "bg-[#FFD736] border-[#FFD736] text-black font-medium"
                            : "border-gray-300 dark:border-gray-600 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300"
                    }`}
                >
                    {page}
                </button>
            ))}

            {/* Next Button */}
            <button
                onClick={() => onPageChange(currentPage + 1)}
                disabled={currentPage === totalPages}
                className={`p-2 rounded-full border border-gray-300 dark:border-gray-600 ${
                    currentPage === totalPages
                        ? "opacity-50 cursor-not-allowed"
                        : "hover:bg-gray-200 dark:hover:bg-gray-700"
                }`}
            >
                <ChevronRight size={18} />
            </button>
        </div>
    );
}
