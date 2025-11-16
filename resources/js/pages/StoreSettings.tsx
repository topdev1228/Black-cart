import { useTranslation } from 'react-i18next';
import { Layout, Page, BlockStack, Tabs, Button } from '@shopify/polaris';
import { useCallback, useContext, useEffect, useRef, useState } from 'react';
import CheckoutConfigurations from '../components/onboarding/checkoutConfigurations';
import ReturnsConfigurations from '../components/onboarding/returnsConfigurations';
import StoreFrontSetUp from '../components/onboarding/storefrontSetUp';
import { useContextualSaveBar } from '@shopify/app-bridge-react';
import { BlackcartContext } from '../components/contexts/BlackcartContext';
import React from 'react';
import Launch from '../components/onboarding/launch';

const StoreSettings = () => {
    const { t } = useTranslation();
    const [selected, setSelected] = useState(0);
    const { show, hide, saveAction, discardAction } = useContextualSaveBar();
    const { program, storeSettings, saveForm, setProgram, setStoreSettings } = useContext(BlackcartContext);
    const [originalProgram, setOriginalProgram] = useState(program);
    const [originalStoreSettings, setOriginalStoreSettings] = useState(storeSettings);

    useEffect(() => {
        saveAction.setOptions({
            disabled: false,
            loading: false,
            onAction: () => {
                saveForm();
                hide();
            },
        });
        discardAction.setOptions({
            disabled: false,
            loading: false,
            onAction: () => {
                if (originalProgram) {
                    setProgram(originalProgram);
                }
                if (originalStoreSettings) {
                    setStoreSettings(originalStoreSettings);
                }
                hide();
            },
        });
    }, [originalProgram, originalStoreSettings]);

    useEffect(() => {
        if (program != null && originalProgram == null) {
            setOriginalProgram(program);
        }
        if (storeSettings != null && originalStoreSettings == null) {
            setOriginalStoreSettings(storeSettings);
        }

        if (JSON.stringify(program) !== JSON.stringify(originalProgram) && originalProgram != null) {
            show({ fullWidth: true, leaveConfirmationDisable: true });
        }

        if (JSON.stringify(storeSettings) !== JSON.stringify(originalStoreSettings) && originalStoreSettings != null) {
            show({ fullWidth: true, leaveConfirmationDisable: true });
        }
    }, [program, storeSettings]);

    const handleTabChange = useCallback((selectedTabIndex: number) => setSelected(selectedTabIndex), []);
    const tabs = [
        {
            id: 'program-configurations-1',
            content: 'TBYB Configurations',
            accessibilityLabel: 'Program Configurations',
            panelID: 'program-configurations-content-1',
        },
        {
            id: 'returns-configurations-1',
            content: 'Returns Configurations',
            accessibilityLabel: 'Returns Configurations',
            panelID: 'returns-configurations-content-1',
        },
        {
            id: 'storefront-setup-1',
            content: 'Storefront Setup',
            accessibilityLabel: 'Storefront Setup',
            panelID: 'storefront-setup-content-1',
        },
    ];
    const steps = [
        { title: CheckoutConfigurations.getTitle(), component: <CheckoutConfigurations /> },
        { title: ReturnsConfigurations.getTitle(), component: <ReturnsConfigurations /> },
        { title: StoreFrontSetUp.getTitle(), component: <StoreFrontSetUp /> },
    ];
    return (
        <Page>
            <ui-nav-menu>
                <a href="/" rel="home">
                    Home
                </a>
                <a href="/transactions">Transactions</a>
                <a href="/storeSettings">Store Settings</a>
                <a href="/help">Help Center</a>
            </ui-nav-menu>
            <BlockStack gap="500">
                <Layout>
                    <Layout.Section>
                        <Tabs tabs={tabs} selected={selected} onSelect={handleTabChange} fitted></Tabs>
                    </Layout.Section>
                </Layout>
                <Layout>
                    <Layout.Section>{steps[selected].component}</Layout.Section>
                </Layout>
            </BlockStack>
        </Page>
    );
};

export default StoreSettings;
