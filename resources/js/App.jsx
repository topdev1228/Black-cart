import { BrowserRouter } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import Routes from './Routes';
import {I18nContext, I18nManager} from '@shopify/react-i18n';

import { AppBridgeProvider, QueryProvider, PolarisProvider, BlackcartProvider } from './components';
import {getUserLocale} from "./utils/i18nUtils";

const locale = getUserLocale();
const i18nManager = new I18nManager({
    locale
});

export default function App() {
    // Any .tsx or .jsx files in /pages will become a route
    // See documentation for <Routes /> for more info
    const pages = import.meta.globEager('./pages/**/!(*.test.[jt]sx)*.([jt]sx)');

    const { t } = useTranslation();

    return (
        <I18nContext.Provider value={i18nManager}>
            <PolarisProvider>
                <BrowserRouter>
                    <AppBridgeProvider>
                        <QueryProvider>
                            <BlackcartProvider>
                                <Routes pages={pages} />
                            </BlackcartProvider>
                        </QueryProvider>
                    </AppBridgeProvider>
                </BrowserRouter>
            </PolarisProvider>
        </I18nContext.Provider>
    );
}
