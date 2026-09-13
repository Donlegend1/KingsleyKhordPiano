import React, { useMemo, useRef } from "react";
import ReactQuill, { Quill } from "react-quill";

// Inserts a plain smiley at the cursor position — a lightweight stand-in for
// a full emoji picker, just enough to give the toolbar the emoji button
// shown in the design reference.
const insertEmoji = (quill) => {
    const range = quill.getSelection(true);
    quill.insertText(range ? range.index : quill.getLength(), "🙂", "user");
    quill.setSelection((range ? range.index : 0) + 1, 0, "user");
};

const RichTextEditor = ({ id, value, onChange, placeholder }) => {
    const quillRef = useRef(null);
    const toolbarId = `ql-toolbar-${id}`;

    const modules = useMemo(
        () => ({
            toolbar: {
                container: `#${toolbarId}`,
                handlers: {
                    emoji: function () {
                        insertEmoji(this.quill);
                    },
                },
            },
        }),
        [toolbarId]
    );

    return (
        <div className="border border-gray-300 rounded-lg overflow-hidden bg-white">
            <div id={toolbarId} className="ql-toolbar ql-snow !border-0 !border-b !border-gray-200">
                <span className="ql-formats">
                    <button className="ql-bold" />
                    <button className="ql-italic" />
                    <button className="ql-underline" />
                    <button className="ql-strike" />
                </span>
                <span className="ql-formats">
                    <button className="ql-link" />
                    <button className="ql-blockquote" />
                    <button className="ql-code-block" />
                </span>
                <span className="ql-formats">
                    <button className="ql-emoji" title="Insert emoji">
                        🙂
                    </button>
                </span>
                <span className="ql-formats">
                    <button className="ql-list" value="bullet" />
                </span>
                <span className="ql-formats">
                    <button className="ql-align" value="" />
                    <button className="ql-align" value="center" />
                    <button className="ql-align" value="right" />
                </span>
                <span className="ql-formats">
                    <select className="ql-color" />
                </span>
            </div>
            <ReactQuill
                ref={quillRef}
                theme="snow"
                value={value}
                onChange={onChange}
                placeholder={placeholder}
                modules={modules}
                formats={["bold", "italic", "underline", "strike", "link", "blockquote", "code-block", "list", "align", "color"]}
            />
        </div>
    );
};

export default RichTextEditor;
