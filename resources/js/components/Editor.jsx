/**
 * Editor.jsx
 * Main entry-point for the Lexical rich-text editor.
 * Wires together the Lexical composer, plugins and the floating toolbar.
 */
import React from 'react';
import { LexicalComposer }             from '@lexical/react/LexicalComposer';
import { RichTextPlugin }              from '@lexical/react/LexicalRichTextPlugin';
import { ContentEditable }             from '@lexical/react/LexicalContentEditable';
import { HistoryPlugin }               from '@lexical/react/LexicalHistoryPlugin';
import { OnChangePlugin }              from '@lexical/react/LexicalOnChangePlugin';
import { LexicalErrorBoundary }        from '@lexical/react/LexicalErrorBoundary';
import { LinkPlugin }                  from '@lexical/react/LexicalLinkPlugin';
import { $getRoot }                    from 'lexical';
import { HeadingNode, QuoteNode }      from '@lexical/rich-text';
import { LinkNode }                    from '@lexical/link';
import { $generateHtmlFromNodes }      from '@lexical/html';

import theme                from './editor/editorTheme';
import FloatingToolbar      from './editor/FloatingToolbar';
import LinkTooltipPlugin    from './editor/LinkTooltipPlugin';
import './editor/editorStyles.css';

/* ── Placeholder shown when editor is empty ──────────── */
function Placeholder() {
    return <div className="editor-placeholder text-2xl text-[#b3b3b1]">Tell your story...</div>;
}

/* ── Main component ──────────────────────────────────── */
export default function Editor() {
    const initialConfig = {
        namespace: 'PostEditor',
        theme,
        nodes: [HeadingNode, QuoteNode, LinkNode],
        onError: (error) => console.error(error),
    };

    /**
     * Fires on every editor change.
     * Exposes the latest HTML on window so the Laravel form can grab it.
     */
    const onChange = (editorState, editor) => {
        editorState.read(() => {
            const text = $getRoot().getTextContent();
            const html = $generateHtmlFromNodes(editor);

            console.log('text :', text);
            console.log('__Editor HTML__:', html);
            window.__EDITOR_HTML__ = html;   // available for form submission
        });
    };

    return (
        <LexicalComposer initialConfig={initialConfig}>
            <div className="editor-container relative">
                <FloatingToolbar />
                <LinkTooltipPlugin />

                <RichTextPlugin
                    contentEditable={
                        <ContentEditable className="editor-input w-full text-xl font-serif leading-8 min-h-96 p-4 bg-white focus:ring-0 focus:shadow-none focus:outline-none" />
                    }
                    placeholder={<Placeholder />}
                    ErrorBoundary={LexicalErrorBoundary}
                />

                <HistoryPlugin />
                <OnChangePlugin onChange={onChange} />
                <LinkPlugin />
            </div>
        </LexicalComposer>
    );
}
