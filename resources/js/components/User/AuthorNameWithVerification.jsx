import { VerifiedIcon } from "lucide-react";

const AuthorNameWithVerification = ({ author }) => {
    if (!author) return null;

    return (
        <div className="flex items-center gap-2">
            <h3 className="text-sm font-semibold text-[#1F2937] dark:text-white">
                {author.first_name} {author.last_name}
            </h3>

            {author.verified ===1 && (
                <VerifiedIcon className="w-4 h-4 text-blue-500" />
            )}
        </div>
    );
};

export default AuthorNameWithVerification;
