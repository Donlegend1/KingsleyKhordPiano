<script>
    (function () {
        const section = document.getElementById('discussion-section');
        if (!section) return;

        const courseId = section.dataset.courseId;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const list = document.getElementById('comment-list');
        const form = document.getElementById('comment-form');

        function buildCommentNode(comment) {
            const wrapper = document.createElement('div');
            wrapper.className = 'flex gap-4 p-4 rounded-xl hover:bg-gray-50 transition-colors';
            wrapper.dataset.commentId = comment.id;

            const avatar = document.createElement('div');
            avatar.className = 'w-10 h-10 bg-[#2563EB]/10 rounded-full flex items-center justify-center flex-shrink-0';
            const initial = document.createElement('span');
            initial.className = 'text-[#2563EB] font-bold text-sm';
            const name = comment.user ? (comment.user.first_name || comment.user.name || 'U') : 'U';
            initial.textContent = name.charAt(0).toUpperCase();
            avatar.appendChild(initial);

            const body = document.createElement('div');
            body.className = 'flex-1 min-w-0';

            const headerRow = document.createElement('div');
            headerRow.className = 'flex items-center justify-between gap-2 mb-1';

            const nameRow = document.createElement('div');
            nameRow.className = 'flex items-center gap-2';
            const nameEl = document.createElement('p');
            nameEl.className = 'text-[14px] font-bold text-gray-900';
            nameEl.textContent = comment.user
                ? `${comment.user.first_name ?? ''} ${comment.user.last_name ?? ''}`.trim() || comment.user.name || 'User'
                : 'User';
            const timeEl = document.createElement('span');
            timeEl.className = 'text-[11px] text-gray-400';
            timeEl.textContent = '• Just now';
            nameRow.appendChild(nameEl);
            nameRow.appendChild(timeEl);
            headerRow.appendChild(nameRow);

            const isOwner = window.authUser && comment.user_id === window.authUser.id;

            if (isOwner) {
                const menuWrap = document.createElement('div');
                menuWrap.className = 'relative comment-menu';
                menuWrap.innerHTML = `
                    <button type="button" class="comment-menu-toggle text-gray-400 hover:text-gray-600 px-1">
                        <i class="fa-solid fa-ellipsis-vertical"></i>
                    </button>
                    <div class="comment-menu-dropdown hidden absolute right-0 mt-1 w-32 bg-white border border-gray-100 rounded-lg shadow-md z-10">
                        <button type="button" class="comment-edit-btn block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Edit</button>
                        <button type="button" class="comment-delete-btn block w-full px-4 py-2 text-left text-sm text-red-500 hover:bg-gray-50">Delete</button>
                    </div>
                `;
                headerRow.appendChild(menuWrap);
            }

            body.appendChild(headerRow);

            const textEl = document.createElement('p');
            textEl.className = 'text-[13px] text-gray-600 leading-relaxed comment-text';
            textEl.textContent = comment.comment;
            body.appendChild(textEl);

            if (isOwner) {
                const editForm = document.createElement('div');
                editForm.className = 'comment-edit-form hidden mt-2 space-y-2';
                editForm.innerHTML = `
                    <textarea class="comment-edit-input w-full bg-gray-50 border border-gray-100 rounded-xl p-3 text-[13px] focus:ring-2 focus:ring-[#2563EB] focus:border-transparent transition-all outline-none" rows="2"></textarea>
                    <div class="flex justify-end gap-2">
                        <button type="button" class="comment-edit-cancel text-[12px] font-semibold text-gray-500 px-3 py-1.5 rounded-lg hover:bg-gray-100">Cancel</button>
                        <button type="button" class="comment-edit-save text-[12px] font-semibold text-white bg-[#2563EB] px-3 py-1.5 rounded-lg hover:bg-[#1D4ED8]">Save</button>
                    </div>
                `;
                editForm.querySelector('.comment-edit-input').value = comment.comment;
                body.appendChild(editForm);
            }

            wrapper.appendChild(avatar);
            wrapper.appendChild(body);
            return wrapper;
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
            }
        });

        document.addEventListener('click', function (e) {
            if (!e.target.closest('.comment-menu')) {
                document.querySelectorAll('.comment-menu-dropdown').forEach(d => d.classList.add('hidden'));
            }
        });
    })();
</script>
