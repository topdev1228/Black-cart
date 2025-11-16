import ReactDOM from 'react-dom/client';
import React from 'react';
import App from "./App";
import { initI18n } from "./utils/i18nUtils";

// Ensure that locales are loaded before rendering the app
initI18n().then(() => {
    let root = ReactDOM.createRoot(document.getElementById('app') as HTMLElement);
    root.render(
    <React.StrictMode>
        <App />
    </React.StrictMode>,
    );
});
