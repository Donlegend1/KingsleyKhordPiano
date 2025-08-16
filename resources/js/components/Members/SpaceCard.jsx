import { FaGlobe, FaUsers } from "react-icons/fa";

const SpaceCard = ({ title, isAdmin, members, slug }) => {
    return (
        <div className="bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg transition overflow-hidden">
            {/* Image/Placeholder */}
            <div className="bg-gray-100 dark:bg-gray-700 h-40 flex items-center justify-center">
                <FaUsers size={36} className="text-gray-400" />
            </div>

            {/* Content */}
            <div className="p-4">
                {/* Title & Admin Badge */}
                <div className="flex items-center gap-2 mb-3">
                    <h3 className="font-semibold text-gray-800 dark:text-gray-100">
                        {title}
                    </h3>
                    {isAdmin && (
                        <span className="bg-green-100 text-green-700 text-xs font-medium px-2 py-0.5 rounded">
                            Admin
                        </span>
                    )}
                </div>

                {/* Public + Members */}
                <div className="flex items-center justify-between text-gray-500 dark:text-gray-400 text-sm mb-4">
                    <span className="flex items-center gap-1">
                        <FaGlobe /> Public
                    </span>
                    <span className="flex items-center gap-1">
                        <FaUsers /> {members} Members
                    </span>
                </div>

                {/* View Button */}
                <a
                    href={`/member/community/space/${slug}`}
                    className="block w-full text-center border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 py-2 rounded-lg font-medium hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                >
                    View Space
                </a>
            </div>
        </div>
    );
};

export default SpaceCard;
