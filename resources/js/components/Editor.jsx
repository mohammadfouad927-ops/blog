import React, { useState, useEffect, useCallback, useRef } from 'react';
import { LexicalComposer } from '@lexical/react/LexicalComposer';
import { RichTextPlugin } from '@lexical/react/LexicalRichTextPlugin';
import { ContentEditable } from '@lexical/react/LexicalContentEditable';
import { HistoryPlugin } from '@lexical/react/LexicalHistoryPlugin';
import { OnChangePlugin } from '@lexical/react/LexicalOnChangePlugin';
import { LexicalErrorBoundary } from '@lexical/react/LexicalErrorBoundary';
import { $getRoot, $getSelection, $isRangeSelection, FORMAT_TEXT_COMMAND, $createParagraphNode } from 'lexical';
import { useLexicalComposerContext } from '@lexical/react/LexicalComposerContext';
import { HeadingNode, QuoteNode, $createHeadingNode, $createQuoteNode, $isHeadingNode, $isQuoteNode } from '@lexical/rich-text';
import { $setBlocksType } from '@lexical/selection';
import { LinkNode, toggleLink, $isLinkNode } from '@lexical/link';
import { LinkPlugin } from '@lexical/react/LexicalLinkPlugin';
import { $generateHtmlFromNodes } from '@lexical/html';



const theme = {
    paragraph: 'editor-paragraph my-2',
    heading: {
        h3: 'text-3xl font-bold my-4 text-gray-900',
        h4: 'text-2xl font-semibold my-3 text-gray-800',
    },
    quote: 'quote-style-1',
    link: 'text-blue-600 underline cursor-pointer hover:text-blue-800 relative group',
    text: {
        bold: 'font-bold',
        italic: 'italic',
        underline: 'underline',
    }
};

function Placeholder() {
    return <div className="editor-placeholder text-2xl text-[#b3b3b1]">Tell your story...</div>;
}

function isValidUrl(string) {
    try {
        if (!string.startsWith('http://') && !string.startsWith('https://')) {
            string = 'https://' + string;
        }
        const url = new URL(string);
        return (url.protocol === 'http:' || url.protocol === 'https:') && url.hostname.includes('.');
    } catch (e) {
        return false;
    }
}

function FloatingToolbar() {
    const [editor] = useLexicalComposerContext();
    const toolbarRef = useRef(null);
    const [isVisible, setIsVisible] = useState(false);
    const [position, setPosition] = useState({ top: 0, left: 0 });
    const [showLinkInput, setShowLinkInput] = useState(false);
    const [linkUrl, setLinkUrl] = useState('');
    const [linkError, setLinkError] = useState('');
    const [quoteStyle, setQuoteStyle] = useState(0);

    const [activeFormats, setActiveFormats] = useState({
        bold: false,
        italic: false,
        h3: false,
        h4: false,
        quote: false,
        link: false,
    });

    const updateToolbar = useCallback(() => {

        if (showLinkInput) return;

        const selection = $getSelection();

        if (!$isRangeSelection(selection) || selection.isCollapsed()) {
            setIsVisible(false);
            setShowLinkInput(false);
            return;
        }

        const domSelection = window.getSelection();
        if (!domSelection || domSelection.rangeCount === 0) {
            setIsVisible(false);
            return;
        }

        const domRange = domSelection.getRangeAt(0);

        // Get all client rects for multi-line/multi-paragraph selections
        const rects = domRange.getClientRects();

        if (!rects || rects.length === 0) {
            setIsVisible(false);
            return;
        }

        // Use the FIRST rect (top line) for toolbar positioning
        const firstRect = rects[0];

        if (!firstRect || firstRect.width === 0 || firstRect.height === 0) {
            setIsVisible(false);
            return;
        }

        const toolbarHeight = 48;
        const toolbarWidth = 280;
        const gap = 8;

        // Position toolbar above the FIRST line of selection
        let top = firstRect.top - toolbarHeight - gap;
        let left = firstRect.left + firstRect.width / 2;

        // Add scroll offset
        top += window.pageYOffset;
        left += window.pageXOffset;

        // Keep toolbar within viewport horizontally
        const minLeft = toolbarWidth / 2 + 10;
        const maxLeft = window.innerWidth - toolbarWidth / 2 - 10;
        left = Math.max(minLeft, Math.min(left, maxLeft));

        // Get format states
        const anchorNode = selection.anchor.getNode();
        const element = anchorNode.getKey() === 'root'
            ? anchorNode
            : anchorNode.getTopLevelElementOrThrow();

        const isH3 = $isHeadingNode(element) && element.getTag() === 'h3';
        const isH4 = $isHeadingNode(element) && element.getTag() === 'h4';
        const isQuote = $isQuoteNode(element);

        const node = selection.anchor.getNode();
        const parent = node.getParent();
        const isLink = $isLinkNode(parent) || $isLinkNode(node);

        let currentQuoteStyle = 0;
        if (isQuote) {
            const dom = editor.getElementByKey(element.getKey());
            if (dom) {
                if (dom.classList.contains('quote-style-1')) currentQuoteStyle = 1;
                else if (dom.classList.contains('quote-style-2')) currentQuoteStyle = 2;
            }
        }

        setQuoteStyle(currentQuoteStyle);

        setActiveFormats({
            bold: selection.hasFormat('bold'),
            italic: selection.hasFormat('italic'),
            h3: isH3,
            h4: isH4,
            quote: isQuote,
            link: isLink,
        });

        setPosition({ top, left });
        setIsVisible(true);
    }, [showLinkInput]);

    useEffect(() => {
        const unregister = editor.registerUpdateListener(({ editorState }) => {
            editorState.read(() => {
                updateToolbar();
            });
        });

        const handleScroll = () => {
            editor.getEditorState().read(() => {
                updateToolbar();
            });
        };

        window.addEventListener('scroll', handleScroll, true);

        return () => {
            unregister();
            window.removeEventListener('scroll', handleScroll, true);
        };
    }, [editor, updateToolbar]);

    const formatBold = () => {
        editor.dispatchCommand(FORMAT_TEXT_COMMAND, 'bold');
    };

    const formatItalic = () => {
        editor.dispatchCommand(FORMAT_TEXT_COMMAND, 'italic');
    };

    const formatHeading = (tag) => {
        editor.update(() => {
            const selection = $getSelection();
            if ($isRangeSelection(selection)) {
                const anchorNode = selection.anchor.getNode();
                const element = anchorNode.getKey() === 'root'
                    ? anchorNode
                    : anchorNode.getTopLevelElementOrThrow();

                const isAlreadyHeading = $isHeadingNode(element) && element.getTag() === tag;

                if (isAlreadyHeading) {
                    $setBlocksType(selection, () => $createParagraphNode());
                } else {
                    $setBlocksType(selection, () => $createHeadingNode(tag));
                }
            }
        });
    };

    const toggleQuote = () => {
        editor.update(() => {
            const selection = $getSelection();
            if ($isRangeSelection(selection)) {
                const anchorNode = selection.anchor.getNode();
                const element = anchorNode.getKey() === 'root'
                    ? anchorNode
                    : anchorNode.getTopLevelElementOrThrow();

                const isCurrentlyQuote = $isQuoteNode(element);

                if (!isCurrentlyQuote) {
                    const quoteNode = $createQuoteNode();
                    $setBlocksType(selection, () => quoteNode);
                    setQuoteStyle(1);

                    setTimeout(() => {
                        editor.update(() => {
                            const sel = $getSelection();
                            if ($isRangeSelection(sel)) {
                                const node = sel.anchor.getNode();
                                const elem = node.getKey() === 'root' ? node : node.getTopLevelElementOrThrow();
                                if ($isQuoteNode(elem)) {
                                    const dom = editor.getElementByKey(elem.getKey());
                                    if (dom) {
                                        dom.classList.remove('quote-style-1', 'quote-style-2');
                                        dom.classList.add('quote-style-1');
                                    }
                                }
                            }
                        });
                    }, 10);
                } else {
                    const dom = editor.getElementByKey(element.getKey());
                    if (dom) {
                        const hasStyle1 = dom.classList.contains('quote-style-1');
                        const hasStyle2 = dom.classList.contains('quote-style-2');

                        if (hasStyle1) {
                            dom.classList.remove('quote-style-1');
                            dom.classList.add('quote-style-2');
                            setQuoteStyle(2);
                        } else if (hasStyle2) {
                            $setBlocksType(selection, () => $createParagraphNode());
                            setQuoteStyle(0);
                        } else {
                            dom.classList.add('quote-style-1');
                            setQuoteStyle(1);
                        }
                    }
                }
            }
        });
    };

    const toggleLinkHandler = () => {
            setShowLinkInput(true);
            setLinkError('');
    };

    const unlink = () => {
        editor.update(() => {
            toggleLink(null);
        });

        setShowLinkInput(false);
        setLinkUrl('');
        setLinkError('');
    };

    const insertLink = () => {
        setLinkError('');

        if (!linkUrl.trim()) {
            setLinkError('Please enter a URL');
            return;
        }

        let finalUrl = linkUrl.trim();

        if (!finalUrl.startsWith('http://') && !finalUrl.startsWith('https://')) {
            finalUrl = 'https://' + finalUrl;
        }

        if (!isValidUrl(finalUrl)) {
            setLinkError('Please enter a valid URL (e.g., google.com or https://example.com)');
            return;
        }

        editor.update(() => {
            const selection = $getSelection();
            if ($isRangeSelection(selection)) {
                toggleLink(finalUrl);
            }
        });

        setLinkUrl('');
        setShowLinkInput(false);
        setLinkError('');
    };

    if (!isVisible) return null;

    return (
        <div
            ref={toolbarRef}
            className="floating-toolbar"
            style={{
                position: 'fixed',
                top: `${position.top}px`,
                left: `${position.left}px`,
                transform: 'translateX(-50%)',
                zIndex: 1000,
            }}
        >
            <div className="bg-black rounded-lg shadow-2xl px-2 py-2 flex items-center gap-1">
                <button
                    type="button"
                    onClick={formatBold}
                    className={`toolbar-btn ${activeFormats.bold ? 'active' : ''}`}
                    title="Bold (Ctrl+B)"
                >
                    <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M12.5 7.5c0 1.38-1.12 2.5-2.5 2.5H7V5h3c1.38 0 2.5 1.12 2.5 2.5zM14 13.5c0 1.38-1.12 2.5-2.5 2.5H7v-5h4.5c1.38 0 2.5 1.12 2.5 2.5zM5 3v14h6.5c2.48 0 4.5-2.02 4.5-4.5 0-1.48-.72-2.79-1.82-3.61C15.28 8.21 16 6.9 16 5.5 16 3.02 13.98 1 11.5 1H5v2z"/>
                    </svg>
                </button>

                <button
                    type='button'
                    onClick={formatItalic}
                    className={`toolbar-btn ${activeFormats.italic ? 'active' : ''}`}
                    title="Italic (Ctrl+I)"
                >
                    <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 3v2h2.5l-3 10H6v2h8v-2h-2.5l3-10H17V3H9z"/>
                    </svg>
                </button>

                <div className="w-px h-6 bg-gray-600 mx-1"></div>

                <button
                    type='button'
                    onClick={() => formatHeading('h3')}
                    className={`toolbar-btn ${activeFormats.h3 ? 'active' : ''}`}
                    title="Heading 3"
                >
                    <span className="font-bold text-sm">H3</span>
                </button>

                <button
                    type='button'
                    onClick={() => formatHeading('h4')}
                    className={`toolbar-btn ${activeFormats.h4 ? 'active' : ''}`}
                    title="Heading 4"
                >
                    <span className="font-bold text-sm">H4</span>
                </button>

                <div className="w-px h-6 bg-gray-600 mx-1"></div>

                <button
                    type='button'
                    onClick={toggleQuote}
                    className={`toolbar-btn ${quoteStyle > 0 ? 'active' : ''}`}
                    title={`Quote (Style ${quoteStyle > 0 ? quoteStyle : 'Off'})`}
                >
                    <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M6 10c0-2 1.5-3.5 3.5-3.5V5C6.36 5 4 7.36 4 10.5V15h5v-4H6v-1zm8 0c0-2 1.5-3.5 3.5-3.5V5c-3.14 0-5.5 2.36-5.5 5.5V15h5v-4h-3v-1z"/>
                    </svg>
                    {quoteStyle > 0 && (
                        <span className="absolute -top-1 -right-1 bg-blue-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">
                            {quoteStyle}
                        </span>
                    )}
                </button>

                {/*<button*/}
                {/*    type='button'*/}
                {/*    onClick={toggleLinkHandler}*/}
                {/*    className={`toolbar-btn ${activeFormats.link ? 'active' : ''}`}*/}
                {/*    title={activeFormats.link ? "Remove Link" : "Add Link"}*/}
                {/*>*/}
                {/*    {activeFormats.link ? (*/}
                {/*        <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">*/}
                {/*            <path fillRule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clipRule="evenodd"/>*/}
                {/*            <line x1="4" y1="4" x2="16" y2="16" stroke="currentColor" strokeWidth="2"/>*/}
                {/*        </svg>*/}
                {/*    ) : (*/}
                {/*        <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">*/}
                {/*            <path fillRule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clipRule="evenodd"/>*/}
                {/*        </svg>*/}
                {/*    )}*/}
                {/*</button>*/}
                <button
                    type="button"
                    onClick={toggleLinkHandler}
                    className="toolbar-btn"
                    title="Add link"
                >
                    <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fillRule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clipRule="evenodd"/>
                    </svg>
                </button>

                {/* UNLINK (only when link is active) */}
                {activeFormats.link && (
                    <button
                        type="button"
                        onClick={unlink}
                        className="toolbar-btn active"
                        title="Remove link"
                    >
                        <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fillRule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clipRule="evenodd"/>
                            <line x1="4" y1="4" x2="16" y2="16" stroke="currentColor" strokeWidth="2"/>
                        </svg>
                    </button>
                )}
            </div>

            {showLinkInput && !activeFormats.link && (
                <div className="absolute top-12 left-1/2 transform -translate-x-1/2 bg-white rounded-lg shadow-xl p-4 w-80 border border-gray-200">
                    <div className="mb-2">
                        <label className="block text-sm font-medium text-gray-700 mb-1">
                            Enter URL
                        </label>
                        <input
                            type="text"
                            value={linkUrl}
                            onPaste={(e) => {
                                e.stopPropagation(); // 🔥 REQUIRED
                            }}
                            onChange={(e) => setLinkUrl(e.target.value)}
                            onKeyDown={(e) => {
                                if (e.key === 'Enter') {
                                    e.preventDefault();
                                    insertLink();
                                }
                                if (e.key === 'Escape') {
                                    setShowLinkInput(false);
                                    setLinkUrl('');
                                    setLinkError('');
                                }
                            }}
                            placeholder="example.com or https://example.com"
                            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            autoFocus
                        />
                        {linkError && (
                            <p className="text-red-500 text-xs mt-1">{linkError}</p>
                        )}
                        <p className="text-gray-500 text-xs mt-1">
                            Example: google.com or https://www.example.com
                        </p>
                    </div>
                    <div className="flex gap-2">
                        <button
                            type='button'
                            onClick={insertLink}
                            className="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors text-sm font-medium"
                        >
                            Insert Link
                        </button>
                        <button
                            type='button'
                            onClick={() => {
                                setShowLinkInput(false);
                                setLinkUrl('');
                                setLinkError('');
                            }}
                            className="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 transition-colors text-sm font-medium"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            )}
        </div>
    );
}

function LinkTooltipPlugin() {
    const [editor] = useLexicalComposerContext();

    useEffect(() => {
        const handleMouseOver = (e) => {
            const target = e.target;
            const link = target.closest('a');
            if (link) {
                const href = link.getAttribute('href');
                if (href) {
                    let tooltip = document.getElementById('link-tooltip');
                    if (!tooltip) {
                        tooltip = document.createElement('div');
                        tooltip.id = 'link-tooltip';
                        tooltip.className = 'link-tooltip';
                        document.body.appendChild(tooltip);
                    }

                    tooltip.textContent = href;
                    tooltip.style.display = 'block';

                    const rect = link.getBoundingClientRect();
                    tooltip.style.top = `${rect.bottom + window.scrollY + 5}px`;
                    tooltip.style.left = `${rect.left + window.scrollX}px`;
                }
            }
        };

        const handleMouseOut = (e) => {
            const target = e.target;
            const link = target.closest('a');
            if (link) {
                const tooltip = document.getElementById('link-tooltip');
                if (tooltip) {
                    tooltip.style.display = 'none';
                }
            }
        };

        const editorElement = editor.getRootElement();
        if (editorElement) {
            editorElement.addEventListener('mouseover', handleMouseOver);
            editorElement.addEventListener('mouseout', handleMouseOut);
        }

        return () => {
            if (editorElement) {
                editorElement.removeEventListener('mouseover', handleMouseOver);
                editorElement.removeEventListener('mouseout', handleMouseOut);
            }
        };
    }, [editor]);

    return null;
}

export default function Editor() {
    const initialConfig = {
        namespace: 'PostEditor',
        theme,
        nodes: [HeadingNode, QuoteNode, LinkNode],
        onError(error) {
            console.error(error);
        },
    };

    function onChange(editorState,editor) {
        editorState.read(() => {
            const root = $getRoot();
            console.log(root.getTextContent());
            const html = $generateHtmlFromNodes(editor);
            window.__EDITOR_HTML__ = html; // expose globally
        });
    }

    return (
        <LexicalComposer initialConfig={initialConfig}>
            <div className="editor-container relative">
                <FloatingToolbar />
                <LinkTooltipPlugin />
                <RichTextPlugin
                    contentEditable={
                        <ContentEditable className="editor-input w-full text-xl font-serif leading-8 min-h-96 p-4 bg-[#fff] focus:ring-0 focus:shadow-none focus:outline-none" />
                    }
                    placeholder={<Placeholder />}
                    ErrorBoundary={LexicalErrorBoundary}
                />
                <HistoryPlugin />
                <OnChangePlugin onChange={onChange} />
                <LinkPlugin />
            </div>

            <style>{`
                .floating-toolbar {
                    animation: fadeIn 0.15s ease-out;
                }

                @keyframes fadeIn {
                    from {
                        opacity: 0;
                        transform: translateX(-50%) translateY(10px);
                    }
                    to {
                        opacity: 1;
                        transform: translateX(-50%) translateY(0);
                    }
                }

                .toolbar-btn {
                    position: relative;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    width: 36px;
                    height: 36px;
                    color: white;
                    background: transparent;
                    border: none;
                    border-radius: 6px;
                    cursor: pointer;
                    transition: all 0.2s;
                }

                .toolbar-btn:hover {
                    background: rgba(255, 255, 255, 0.15);
                }

                .toolbar-btn.active {
                    background: rgba(59, 130, 246, 0.6);
                }

                .editor-paragraph {
                    margin: 0.5rem 0;
                }

                .editor-placeholder {
                    position: absolute;
                    top: 16px;
                    left: 16px;
                    pointer-events: none;
                    opacity: 0.5;
                }

                .quote-style-1 {
                    border-left: 4px solid #9CA3AF;
                    padding-left: 1rem;
                    margin: 1rem 0;
                    font-style: italic;
                    color: #4B5563;
                    background-color: #F9FAFB;
                    padding: 1rem;
                    border-radius: 0 0.5rem 0.5rem 0;
                }

                .quote-style-2 {
                    border-left: 4px solid #3B82F6;
                    padding-left: 1rem;
                    margin: 1rem 0;
                    font-style: italic;
                    color: #1E40AF;
                    background: linear-gradient(to right, #DBEAFE, transparent);
                    padding: 1rem;
                    border-radius: 0 0.5rem 0.5rem 0;
                    font-weight: 500;
                }

                .link-tooltip {
                    position: absolute;
                    display: none;
                    background: #1F2937;
                    color: white;
                    padding: 6px 12px;
                    border-radius: 6px;
                    font-size: 13px;
                    z-index: 10000;
                    max-width: 300px;
                    word-break: break-all;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                    pointer-events: none;
                }

                .link-tooltip::before {
                    content: '';
                    position: absolute;
                    top: -4px;
                    left: 10px;
                    width: 0;
                    height: 0;
                    border-left: 4px solid transparent;
                    border-right: 4px solid transparent;
                    border-bottom: 4px solid #1F2937;
                }
                .floating-toolbar::after {
                content: '';
                position: absolute;
                bottom: -8px;            /* distance under toolbar */
                left: 50%;
                transform: translateX(-50%);
                width: 0;
                height: 0;
                border-left: 8px solid transparent;
                border-right: 8px solid transparent;
                border-top: 8px solid #000; /* same as toolbar background */
            }

            `}</style>
        </LexicalComposer>
    );
}



