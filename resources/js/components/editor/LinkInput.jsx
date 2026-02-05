/**
 * LinkInput.jsx
 * The small popup that appears below the toolbar when the user
 * clicks "Add link".  Handles validation and calls back with the
 * final URL or cancel signal.
 *
 * Props:
 *   onInsert – (url: string) => void   called with the validated URL
 *   onCancel – () => void              called when the user cancels
 */
import React, { useState } from 'react';
import { isValidUrl, normalizeUrl } from './utils';

export default function LinkInput({ onInsert, onCancel }) {
    const [url, setUrl]       = useState('');
    const [error, setError]   = useState('');

    const handleInsert = () => {
        if (!url.trim()) {
            setError('Please enter a URL');
            return;
        }

        const normalized = normalizeUrl(url);

        if (!isValidUrl(normalized)) {
            setError('Please enter a valid URL (e.g. google.com or https://example.com)');
            return;
        }

        onInsert(normalized);
    };

    const handleKeyDown = (e) => {
        if (e.key === 'Enter')  { e.preventDefault(); handleInsert(); }
        if (e.key === 'Escape') { e.preventDefault(); onCancel(); }
    };

    return (
        <div className="absolute top-12 left-1/2 transform -translate-x-1/2 bg-white rounded-lg shadow-xl p-4 w-80 border border-gray-200">
            <label className="block text-sm font-medium text-gray-700 mb-1">
                Enter URL
            </label>

            <input
                type="text"
                value={url}
                autoFocus
                placeholder="example.com or https://example.com"
                className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                onChange={(e) => { setUrl(e.target.value); setError(''); }}
                onPaste={(e) => e.stopPropagation()}
                onKeyDown={handleKeyDown}
            />

            {error && <p className="text-red-500 text-xs mt-1">{error}</p>}

            <p className="text-gray-400 text-xs mt-1">
                Example: google.com or https://www.example.com
            </p>

            <div className="flex gap-2 mt-3">
                <button
                    type="button"
                    onClick={handleInsert}
                    className="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors text-sm font-medium"
                >
                    Insert Link
                </button>
                <button
                    type="button"
                    onClick={onCancel}
                    className="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 transition-colors text-sm font-medium"
                >
                    Cancel
                </button>
            </div>
        </div>
    );
}
