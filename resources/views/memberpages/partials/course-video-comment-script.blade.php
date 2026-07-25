<script>
    (function () {
        const section = document.getElementById('discussion-section');
        if (!section) return;

        const courseId = section.dataset.courseId;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const list = document.getElementById('comment-list');
        const form = document.getElementById('comment-form');

        function userName(user) {
            if (!user) return 'User';
            return `${user.first_name ?? ''} ${user.last_name ?? ''}`.trim() || user.name || 'User';
        }

        function buildReplyNode(reply) {
            const wrapper = document.createElement('div');
            wrapper.className = 'flex gap-3 py-3 first:pt-0';
            wrapper.dataset.replyId = reply.id;

            const name = userName(reply.user);

            wrapper.innerHTML = `
                <div class="w-7 h-7 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
                    <span class="text-gray-500 font-semibold text-[11px]">${name.charAt(0).toUpperCase()}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-baseline gap-2">
                        <p class="text-[13px] font-semibold text-gray-900"></p>
                        <span class="text-[11px] text-gray-400">Just now</span>
                    </div>
                    <p class="text-[13px] text-gray-600 leading-relaxed mt-0.5"></p>
                </div>
            `;
            wrapper.querySelector('p.font-semibold').textContent = name;
            wrapper.querySelector('p.leading-relaxed').textContent = reply.reply;

            return wrapper;
        }

        function buildCommentNode(comment) {
            const wrapper = document.createElement('div');
            wrapper.className = 'flex gap-3.5 py-5 first:pt-0';
            wrapper.dataset.commentId = comment.id;

            const name = userName(comment.user);
            const isOwner = window.authUser && comment.user_id === window.authUser.id;

            wrapper.innerHTML = `
                <div class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
                    <span class="text-gray-500 font-semibold text-xs">${name.charAt(0).toUpperCase()}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-baseline gap-2">
                            <p class="text-[13.5px] font-semibold text-gray-900 comment-author"></p>
                            <span class="text-[11.5px] text-gray-400">Just now</span>
                        </div>
                        ${isOwner ? `
                        <div class="relative comment-menu">
                            <button type="button" class="comment-menu-toggle text-gray-300 hover:text-gray-500 px-1 transition-colors">
                                <i class="fa-solid fa-ellipsis-vertical text-xs"></i>
                            </button>
                            <div class="comment-menu-dropdown hidden absolute right-0 mt-1 w-32 bg-white border border-gray-100 rounded-xl shadow-lg z-10 overflow-hidden">
                                <button type="button" class="comment-edit-btn block w-full px-4 py-2 text-left text-[13px] text-gray-600 hover:bg-gray-50">Edit</button>
                                <button type="button" class="comment-delete-btn block w-full px-4 py-2 text-left text-[13px] text-red-500 hover:bg-gray-50">Delete</button>
                            </div>
                        </div>` : ''}
                    </div>

                    <p class="text-[13.5px] text-gray-600 leading-relaxed mt-1 comment-text"></p>

                    ${isOwner ? `
                    <div class="comment-edit-form hidden mt-3 space-y-2">
                        <textarea class="comment-edit-input w-full bg-white border border-gray-200 rounded-2xl p-3 text-[13.5px] focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-colors outline-none resize-none" rows="2"></textarea>
                        <div class="flex justify-end gap-2">
                            <button type="button" class="comment-edit-cancel text-[12px] font-semibold text-gray-500 px-3.5 py-1.5 rounded-full hover:bg-gray-100 transition-colors">Cancel</button>
                            <button type="button" class="comment-edit-save text-[12px] font-semibold text-white bg-gray-900 px-3.5 py-1.5 rounded-full hover:bg-black transition-colors">Save</button>
                        </div>
                    </div>` : ''}

                    <button type="button" class="comment-reply-toggle mt-2 text-[12px] font-semibold text-gray-500 hover:text-gray-900 transition-colors">
                        Reply
                    </button>

                    <div class="comment-reply-form hidden mt-3 space-y-2">
                        <textarea class="comment-reply-input w-full bg-white border border-gray-200 rounded-2xl p-3 text-[13px] focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-colors outline-none resize-none" rows="2" placeholder="Write a reply..."></textarea>
                        <div class="flex justify-end gap-2">
                            <button type="button" class="comment-reply-cancel text-[12px] font-semibold text-gray-500 px-3.5 py-1.5 rounded-full hover:bg-gray-100 transition-colors">Cancel</button>
                            <button type="button" class="comment-reply-submit text-[12px] font-semibold text-white bg-gray-900 px-3.5 py-1.5 rounded-full hover:bg-black transition-colors">Reply</button>
                        </div>
                    </div>

                    <div class="comment-replies-list mt-4 space-y-4"></div>
                </div>
            `;

            wrapper.querySelector('.comment-author').textContent = name;
            wrapper.querySelector('.comment-text').textContent = comment.comment;

            if (isOwner) {
                wrapper.querySelector('.comment-edit-input').value = comment.comment;
            }

            return wrapper;
        }

        function addReplyToComment(wrapper, reply) {
            const repliesList = wrapper.querySelector('.comment-replies-list');
            repliesList.classList.add('border-l-2', 'pl-4');
            repliesList.appendChild(buildReplyNode(reply));
        }

        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            const textarea = form.querySelector('textarea[name="comment"]');
            const text = textarea.value.trim();
            if (!text) return;

            try {
                const res = await fetch(`/api/member/course/${courseId}/video-comment`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        comment: text,
                        category: section.dataset.commentCategory || 'others',
                        course_id: courseId,
                        url: window.location.href,
                    }),
                });

                if (!res.ok) throw new Error('Failed to post comment');

                const { data } = await res.json();
                list.prepend(buildCommentNode(data));
                textarea.value = '';
            } catch (err) {
                console.error('Failed to post comment:', err);
                alert('Failed to post comment. Please try again.');
            }
        });

        list.addEventListener('click', function (e) {
            const wrapper = e.target.closest('[data-comment-id]');
            if (!wrapper) return;
            const commentId = wrapper.dataset.commentId;

            if (e.target.closest('.comment-menu-toggle')) {
                wrapper.querySelector('.comment-menu-dropdown').classList.toggle('hidden');
                return;
            }

            if (e.target.closest('.comment-edit-btn')) {
                wrapper.querySelector('.comment-menu-dropdown').classList.add('hidden');
                wrapper.querySelector('.comment-text').classList.add('hidden');
                wrapper.querySelector('.comment-edit-form').classList.remove('hidden');
                return;
            }

            if (e.target.closest('.comment-edit-cancel')) {
                wrapper.querySelector('.comment-edit-form').classList.add('hidden');
                wrapper.querySelector('.comment-text').classList.remove('hidden');
                return;
            }

            if (e.target.closest('.comment-edit-save')) {
                const input = wrapper.querySelector('.comment-edit-input');
                const newText = input.value.trim();
                if (!newText) return;

                fetch(`/api/member/comment/${commentId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ comment: newText }),
                })
                    .then(res => {
                        if (!res.ok) throw new Error('Failed to update comment');
                        wrapper.querySelector('.comment-text').textContent = newText;
                        wrapper.querySelector('.comment-text').classList.remove('hidden');
                        wrapper.querySelector('.comment-edit-form').classList.add('hidden');
                    })
                    .catch(err => {
                        console.error('Failed to update comment:', err);
                        alert('Failed to update comment. Please try again.');
                    });
                return;
            }

            if (e.target.closest('.comment-delete-btn')) {
                wrapper.querySelector('.comment-menu-dropdown').classList.add('hidden');
                if (!confirm('Delete this comment?')) return;

                fetch(`/api/member/comment/${commentId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                })
                    .then(res => {
                        if (!res.ok) throw new Error('Failed to delete comment');
                        wrapper.remove();
                    })
                    .catch(err => {
                        console.error('Failed to delete comment:', err);
                        alert('Failed to delete comment. Please try again.');
                    });
                return;
            }

            if (e.target.closest('.comment-reply-toggle')) {
                wrapper.querySelector('.comment-reply-form').classList.toggle('hidden');
                wrapper.querySelector('.comment-reply-input').focus();
                return;
            }

            if (e.target.closest('.comment-reply-cancel')) {
                const replyForm = wrapper.querySelector('.comment-reply-form');
                replyForm.querySelector('.comment-reply-input').value = '';
                replyForm.classList.add('hidden');
                return;
            }

            if (e.target.closest('.comment-reply-submit')) {
                const replyForm = wrapper.querySelector('.comment-reply-form');
                const input = replyForm.querySelector('.comment-reply-input');
                const text = input.value.trim();
                if (!text) return;

                fetch(`/api/member/comment/${commentId}/reply`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ comment: text }),
                })
                    .then(res => {
                        if (!res.ok) throw new Error('Failed to post reply');
                        return res.json();
                    })
                    .then(({ data }) => {
                        addReplyToComment(wrapper, data);
                        input.value = '';
                        replyForm.classList.add('hidden');
                    })
                    .catch(err => {
                        console.error('Failed to post reply:', err);
                        alert('Failed to post reply. Please try again.');
                    });
                return;
            }
        });

        document.addEventListener('click', function (e) {
            if (!e.target.closest('.comment-menu')) {
                document.querySelectorAll('.comment-menu-dropdown').forEach(d => d.classList.add('hidden'));
            }
        });
    })();
</script>
