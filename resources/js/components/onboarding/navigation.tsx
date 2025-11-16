import { Button, Card, Divider, InlineStack, Text, BlockStack, Box } from '@shopify/polaris';
import SettingsIcon from './images/settingsIcon';
import ReturnsIcon from './images/returnsIcon';
import AddProductsIcon from './images/addProductsIcon';
import OnlineStoreIcon from './images/onlineStoreIcon';
import LaunchIcon from './images/launchIcon';
import React from 'react';

interface navigationProps {
    currentStep: string;
    latestStep: string;
    disableNext: boolean;
    back: () => void;
    save: () => void;
}

const Navigation = (props: navigationProps) => {
    const NavItem = ({ active = false, icon, passed = false, children }) => {
        return (
            <div style={{ display: 'inline-flex' }}>
                <Box
                    padding="100"
                    background={active || passed ? 'bg-surface-selected' : undefined}
                    borderRadius="150"
                    paddingInlineStart="200"
                    paddingInlineEnd="200"
                    width="100%"
                >
                    <InlineStack wrap={false} gap="200" blockAlign="center">
                        {icon}
                        <p
                            style={{
                                display: 'inline-block',
                                textAlign: 'left',
                                fontWeight: active ? '650' : passed ? '550' : '150',
                            }}
                        >
                            {children}
                        </p>
                    </InlineStack>
                </Box>
            </div>
        );
    };

    const steps = {
        checkoutConfigurations: { step: 'TBYB Configurations', icon: <SettingsIcon /> },
        returnsConfigurations: { step: 'Returns Configurations', icon: <ReturnsIcon /> },
        storefrontSetUp: { step: 'Storefront Setup', icon: <OnlineStoreIcon /> },
        launch: { step: 'Launch', icon: <LaunchIcon /> },
    };

    return (
        <Card>
            <BlockStack gap="200">
                <Text variant="headingSm" as="h6">
                    Onboarding Steps
                </Text>
                <Box paddingInlineStart="400" paddingInlineEnd="400">
                    <BlockStack gap="200">
                        {Object.keys(steps).map((key, index) => {
                            const passed = Object.keys(steps).indexOf(props.latestStep) >= index;
                            const step = steps[key];
                            return (
                                <NavItem
                                    key={index}
                                    icon={step.icon}
                                    active={key === props.currentStep}
                                    passed={passed}
                                >
                                    {step.step}
                                </NavItem>
                            );
                        })}
                    </BlockStack>
                </Box>
                <Divider />
                <InlineStack align="space-between">
                    <Button variant="primary" onClick={props.back}>
                        Back
                    </Button>
                    {props.currentStep !== 'launch' && (
                        <Button variant="primary" onClick={props.save} disabled={props.disableNext}>
                            Save & Continue
                        </Button>
                    )}
                </InlineStack>
            </BlockStack>
        </Card>
    );
};

export default Navigation;
