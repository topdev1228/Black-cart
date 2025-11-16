import { ProgressBar, Layout, Page, Text, BlockStack } from '@shopify/polaris';
import { useContext, useRef, useState } from 'react';
import CheckoutConfigurations from '../components/onboarding/checkoutConfigurations';
import Navigation from '../components/onboarding/navigation';
import ReturnsConfigurations from '../components/onboarding/returnsConfigurations';
import StoreFrontSetUp from '../components/onboarding/storefrontSetUp';
import Launch from '../components/onboarding/launch';
import { BlackcartContext } from '../components/contexts/BlackcartContext';
import React from 'react';
import { useTranslation, Trans } from 'react-i18next';

const Onboarding = () => {
    const { t } = useTranslation();
    const { saveForm, stage, setStage, latestStage, storeSettings} = useContext(BlackcartContext);
    const currentStep = stage ?? 'checkoutConfigurations';
    
    const steps = {
        checkoutConfigurations: { title: CheckoutConfigurations.getTitle(), component: <CheckoutConfigurations /> },
        returnsConfigurations: { title: ReturnsConfigurations.getTitle(), component: <ReturnsConfigurations /> },
        storefrontSetUp: { title: StoreFrontSetUp.getTitle(), component: <StoreFrontSetUp /> },
        launch: { title: Launch.getTitle(), component: <Launch /> },
    };

    let disableNext = false;
    if (currentStep === 'storefrontSetUp' ) {
        disableNext = true;
        if (storeSettings && storeSettings.settings && storeSettings.settings.theme_install_clicked) {
            disableNext = false;
        }
    }
   
    const stepKeys = Object.keys(steps);

    const back = () => {
        const currentIndex = stepKeys.indexOf(currentStep);
        const prevIndex = Math.max(0, currentIndex - 1);
        setStage(stepKeys[prevIndex]);
    };
    const progressPercent = 25 + Math.max(0, Math.floor(stepKeys.indexOf(currentStep))) * 25;

    return (
        <Page>
            <BlockStack gap="500">
                <Layout>
                    <Layout.Section>
                        <ProgressBar size="small" progress={progressPercent} tone="success"></ProgressBar>
                        <br></br>
                        <Text as="h1">
                            <b>{steps[currentStep]?.title}</b>
                        </Text>
                    </Layout.Section>
                    <Layout.Section variant="oneThird"></Layout.Section>
                </Layout>
                <Layout>
                    <Layout.Section>{steps[currentStep]?.component}</Layout.Section>
                    <Layout.Section variant="oneThird">
                        <Navigation
                            currentStep={currentStep}
                            latestStep={latestStage}
                            disableNext={disableNext}
                            save={saveForm}
                            back={back}
                        ></Navigation>
                    </Layout.Section>
                </Layout>
            </BlockStack>
        </Page>
    );
};

export default Onboarding;
