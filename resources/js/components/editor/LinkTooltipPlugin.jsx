/**
 * LinkTooltipPlugin.jsx
 * Lexical plugin – shows the URL in a dark tooltip when the user
 * hovers over any <a> tag inside the editor.
 * Does not render any visible DOM of its own; it attaches mouse
 * event listeners to the editor root element and creates / moves
 * a single tooltip element on <body>.
 */
import { useEffect } from 'react';
import { useLexicalComposerContext } from '@lexical/react/LexicalComposerContext';

export default function LinkTooltipPlugin() {
    const [editor] = useLexicalComposerContext();

    useEffect(() => {
        const tooltip = (() => {
            let el = document.getElementById('link-tooltip');
            if (!el) {
                el = document.createElement('div');
                el.id        = 'link-tooltip';
                el.className = 'link-tooltip';
                document.body.appendChild(el);
            }
            return el;
        })();

        const show = (e) => {
            const link = e.target.closest('a');
            if (!link) return;

            const href = link.getAttribute('href');
            if (!href) return;

            tooltip.textContent  = href;
            tooltip.style.display = 'block';

            const rect = link.getBoundingClientRect();
            tooltip.style.top  = `${rect.bottom + window.scrollY + 5}px`;
            tooltip.style.left = `${rect.left   + window.scrollX}px`;
        };

        const hide = (e) => {
            if (e.target.closest('a')) {
                tooltip.style.display = 'none';
            }
        };

        const root = editor.getRootElement();
        if (root) {
            root.addEventListener('mouseover', show);
            root.addEventListener('mouseout',  hide);
        }

        return () => {
            if (root) {
                root.removeEventListener('mouseover', show);
                root.removeEventListener('mouseout',  hide);
            }
        };
    }, [editor]);

    return null;
}
