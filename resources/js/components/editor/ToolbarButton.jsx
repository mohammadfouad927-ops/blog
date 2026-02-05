/**
 * ToolbarButton.jsx
 * A single reusable button inside the floating toolbar.
 *
 * Props:
 *   onClick  – click handler
 *   active   – boolean, adds the "active" highlight
 *   title    – tooltip shown on hover
 *   children – the icon/content rendered inside
 */
import React from 'react';

export default function ToolbarButton({ onClick, active = false, title, children }) {
    return (
        <button
            type="button"
            onClick={onClick}
            className={`toolbar-btn ${active ? 'active' : ''}`}
            title={title}
        >
            {children}
        </button>
    );
}
