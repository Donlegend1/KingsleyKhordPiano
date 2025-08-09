import React, { useState } from "react";
import ReactDOM from "react-dom/client";

const SkeletonPost = () => (
    <div className="animate-pulse p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 mb-4">
        <div className="flex space-x-4 mb-3">
            <div className="w-10 h-10 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
            <div className="flex-1 space-y-2">
                <div className="h-4 bg-gray-300 dark:bg-gray-600 rounded w-1/2"></div>
                <div className="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/3"></div>
            </div>
        </div>
        <div className="h-4 bg-gray-200 dark:bg-gray-700 rounded mb-2"></div>
        <div className="h-4 bg-gray-200 dark:bg-gray-700 rounded w-5/6"></div>
    </div>
);

export default SkeletonPost