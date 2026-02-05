/**
 * FloatingToolbar.jsx
 * Appears above the selected text and exposes formatting controls:
 *   Bold · Italic · H3 · H4 · Quote (2 styles) · Link / Unlink
 *
 * Positioning is recalculated on every editor update and on scroll
 * so the toolbar tracks the selection even when the page moves.
 */
import React, { useState, useEffect, useCallback, useRef } from 'react';
import { $getSelection, $isRangeSelection, FORMAT_TEXT_COMMAND, $createParagraphNode } from 'lexical';
import { useLexicalComposerContext } from '@lexical/react/LexicalComposerContext';
import { $isHeadingNode, $isQuoteNode, $createHeadingNode, $createQuoteNode } from '@lexical/rich-text';
import { $setBlocksType } from '@lexical/selection';
import { $isLinkNode, toggleLink } from '@lexical/link';

import ToolbarButton from './ToolbarButton';
import LinkInput     from './LinkInput';
import { getToolbarPosition } from './utils';

/* ── SVG icons ─────────────────────────────────────────── */
const BoldIcon = () => (
    <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
        <path d="M12.5 7.5c0 1.38-1.12 2.5-2.5 2.5H7V5h3c1.38 0 2.5 1.12 2.5 2.5zM14 13.5c0 1.38-1.12 2.5-2.5 2.5H7v-5h4.5c1.38 0 2.5 1.12 2.5 2.5zM5 3v14h6.5c2.48 0 4.5-2.02 4.5-4.5 0-1.48-.72-2.79-1.82-3.61C15.28 8.21 16 6.9 16 5.5 16 3.02 13.98 1 11.5 1H5v2z"/>
    </svg>
);

const ItalicIcon = () => (
    <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
        <path d="M9 3v2h2.5l-3 10H6v2h8v-2h-2.5l3-10H17V3H9z"/>
    </svg>
);

const QuoteIcon = () => (
    <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
        <path d="M6 10c0-2 1.5-3.5 3.5-3.5V5C6.36 5 4 7.36 4 10.5V15h5v-4H6v-1zm8 0c0-2 1.5-3.5 3.5-3.5V5c-3.14 0-5.5 2.36-5.5 5.5V15h5v-4h-3v-1z"/>
    </svg>
);

const LinkIcon = () => (
    <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
        <path fillRule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clipRule="evenodd"/>
    </svg>
);

const UnlinkIcon = () => (
    <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
        <path d="M17 7h-3v2h3c1.65 0 3 1.35 3 3s-1.35 3-3 3h-3v2h3c2.76 0 5-2.24 5-5s-2.24-5-5-5zm-6 0H8C5.24 7 3 9.24 3 12s2.24 5 5 5h3v-2H8c-1.66 0-3-1.35-3-3s1.34-3 3-3h3V7zm-1 4h4v2H10v-2z"/>
        <line x1="3" y1="3" x2="21" y2="21" stroke="currentColor" strokeWidth="2" strokeLinecap="round"/>
    </svg>
);

/* ── Separator between button groups ───────────────────── */
const Separator = () => <div className="w-px h-6 bg-gray-600 mx-1" />;

/* ── Main component ────────────────────────────────────── */
export default function FloatingToolbar() {
    const [editor] = useLexicalComposerContext();

    // ── state ─────────────────────────────────────────────
    const [isVisible, setIsVisible]         = useState(false);
    const [position, setPosition]           = useState({ top: 0, left: 0 });
    const [showLinkInput, setShowLinkInput] = useState(false);
    const [quoteStyle, setQuoteStyle]       = useState(0); // 0 | 1 | 2

    const [activeFormats, setActiveFormats] = useState({
        bold: false, italic: false,
        h3: false,   h4: false,
        quote: false, link: false,
    });

    // ── helpers that read Lexical state ───────────────────
    /**
     * Returns the top-level element that owns the current selection anchor,
     * or the root node itself when the anchor IS the root.
     */
    const getSelectedElement = (selection) => {
        const anchor = selection.anchor.getNode();
        return anchor.getKey() === 'root' ? anchor : anchor.getTopLevelElementOrThrow();
    };

    /**
     * Reads the current quote-style class ("quote-style-1" / "quote-style-2")
     * directly from the DOM element that Lexical rendered for *element*.
     */
    const getQuoteStyleFromDOM = (element) => {
        const dom = editor.getElementByKey(element.getKey());
        if (!dom) return 0;
        if (dom.classList.contains('quote-style-2')) return 2;
        if (dom.classList.contains('quote-style-1')) return 1;
        return 0;
    };

    // ── toolbar position & active-format sync ────────────
    const updateToolbar = useCallback(() => {
        // Don't reposition while the link-input popup is open
        if (showLinkInput) return;

        const selection = $getSelection();
        if (!$isRangeSelection(selection) || selection.isCollapsed()) {
            setIsVisible(false);
            return;
        }

        const pos = getToolbarPosition();
        if (!pos) { setIsVisible(false); return; }

        // ── detect active formats ───────────────────────
        const element = getSelectedElement(selection);
        const node    = selection.anchor.getNode();
        const parent  = node.getParent();

        const isQuote = $isQuoteNode(element);

        setQuoteStyle(isQuote ? getQuoteStyleFromDOM(element) : 0);

        setActiveFormats({
            bold:   selection.hasFormat('bold'),
            italic: selection.hasFormat('italic'),
            h3:     $isHeadingNode(element) && element.getTag() === 'h3',
            h4:     $isHeadingNode(element) && element.getTag() === 'h4',
            quote:  isQuote,
            link:   $isLinkNode(parent) || $isLinkNode(node),
        });

        setPosition(pos);
        setIsVisible(true);
    }, [editor, showLinkInput]);

    useEffect(() => {
        const unregister = editor.registerUpdateListener(({ editorState }) => {
            editorState.read(() => updateToolbar());
        });

        const onScroll = () => editor.getEditorState().read(() => updateToolbar());
        window.addEventListener('scroll', onScroll, true);

        return () => { unregister(); window.removeEventListener('scroll', onScroll, true); };
    }, [editor, updateToolbar]);

    // ── format actions ────────────────────────────────────
    const formatBold   = () => editor.dispatchCommand(FORMAT_TEXT_COMMAND, 'bold');
    const formatItalic = () => editor.dispatchCommand(FORMAT_TEXT_COMMAND, 'italic');

    const formatHeading = (tag) => {
        editor.update(() => {
            const sel = $getSelection();
            if (!$isRangeSelection(sel)) return;

            const element       = getSelectedElement(sel);
            const alreadyActive = $isHeadingNode(element) && element.getTag() === tag;

            $setBlocksType(sel, () => alreadyActive ? $createParagraphNode() : $createHeadingNode(tag));
        });
    };

    const toggleQuote = () => {
        editor.update(() => {
            const sel = $getSelection();
            if (!$isRangeSelection(sel)) return;

            const element        = getSelectedElement(sel);
            const isCurrentQuote = $isQuoteNode(element);

            if (!isCurrentQuote) {
                // Not a quote yet → apply quote + style-1
                $setBlocksType(sel, () => $createQuoteNode());
                setQuoteStyle(1);

                // Lexical updates the DOM asynchronously, so apply the class after a tick
                setTimeout(() => {
                    editor.update(() => {
                        const s = $getSelection();
                        if (!$isRangeSelection(s)) return;
                        const el  = getSelectedElement(s);
                        const dom = editor.getElementByKey(el.getKey());
                        if (dom && $isQuoteNode(el)) {
                            dom.classList.remove('quote-style-1', 'quote-style-2');
                            dom.classList.add('quote-style-1');
                        }
                    });
                }, 10);
                return;
            }

            // Already a quote – cycle: style-1 → style-2 → remove
            const dom = editor.getElementByKey(element.getKey());
            if (!dom) return;

            if (dom.classList.contains('quote-style-1')) {
                $setBlocksType(sel, () => $createQuoteNode())
                dom.classList.replace('quote-style-1', 'quote-style-2');
                setQuoteStyle(2);
            } else if (dom.classList.contains('quote-style-2')) {
                $setBlocksType(sel, () => $createParagraphNode());
                setQuoteStyle(0);
            } else {
                dom.classList.add('quote-style-1');
                setQuoteStyle(1);
            }
        });
    };

    const handleInsertLink = (url) => {
        editor.update(() => {
            const sel = $getSelection();
            if ($isRangeSelection(sel)) toggleLink(url);
        });
        setShowLinkInput(false);
    };

    const handleUnlink = () => {
        editor.update(() => toggleLink(null));
        setShowLinkInput(false);
    };

    // ── render ────────────────────────────────────────────
    if (!isVisible) return null;

    return (
        <div
            className="floating-toolbar"
            style={{ position: 'fixed', top: position.top, left: position.left, transform: 'translateX(-50%)', zIndex: 1000 }}
        >
            {/* Toolbar bar */}
            <div className="bg-black rounded-lg shadow-2xl px-2 py-2 flex items-center gap-1">
                <ToolbarButton onClick={formatBold}   active={activeFormats.bold}   title="Bold (Ctrl+B)"><BoldIcon /></ToolbarButton>
                <ToolbarButton onClick={formatItalic} active={activeFormats.italic} title="Italic (Ctrl+I)"><ItalicIcon /></ToolbarButton>

                <Separator />

                <ToolbarButton onClick={() => formatHeading('h3')} active={activeFormats.h3} title="Heading 3"><span className="font-bold text-sm">H3</span></ToolbarButton>
                <ToolbarButton onClick={() => formatHeading('h4')} active={activeFormats.h4} title="Heading 4"><span className="font-bold text-sm">H4</span></ToolbarButton>

                <Separator />

                {/* Quote – badge shows current style number */}
                <ToolbarButton onClick={toggleQuote} active={quoteStyle > 0} title={`Quote (Style ${quoteStyle || 'Off'})`}>
                    <QuoteIcon />
                    {quoteStyle > 0 && (
                        <span className="absolute -top-1 -right-1 bg-blue-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">
                            {quoteStyle}
                        </span>
                    )}
                </ToolbarButton>

                {/* Link / Unlink */}
                <ToolbarButton onClick={() => setShowLinkInput(true)} title="Add link"><LinkIcon /></ToolbarButton>
                {activeFormats.link && (
                    <ToolbarButton onClick={handleUnlink} active={true} title="Remove link"><UnlinkIcon /></ToolbarButton>
                )}
            </div>

            {/* Link-input popup (only when adding a new link) */}
            {showLinkInput && !activeFormats.link && (
                <LinkInput
                    onInsert={handleInsertLink}
                    onCancel={() => setShowLinkInput(false)}
                />
            )}
        </div>
    );
}
