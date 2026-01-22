import '../js/pages/navbar.js';

import React from 'react';
import { createRoot } from 'react-dom/client';
import Editor from './components/Editor';

const el = document.getElementById('editor-root');

if (el) {
    createRoot(el).render(<Editor />);
}
