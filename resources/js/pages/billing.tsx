import { Button, Card, BlockStack, Text, InlineStack, ButtonGroup, Layout, Page } from '@shopify/polaris';
import React, { useContext, useState } from 'react';
import { BlackcartContext } from '../components/contexts/BlackcartContext';
import { useTranslation } from 'react-i18next';
import { useNavigate } from '@shopify/app-bridge-react';

const Billing = () => {
    const { t } = useTranslation();
    const { enableBilling} = useContext(BlackcartContext);
    const [loading, setLoading] = useState(false);
    const navigate = useNavigate();

    const initiateBilling = async () => {
        setLoading(true);
        try {
            const res = await enableBilling();
            navigate(res.shopifyConfirmationUrl);
        } catch (err) {
            console.log(err);
        } finally {
            setLoading(false);
        }
    };

    return (
        <Page>
            <Layout>
                <Layout.Section>
                    <Card roundedAbove="sm">
                        <BlockStack gap="500">
                            <BlockStack gap="200">
                                <Text as="h2" variant="headingSm">
                                    Enable Billing
                                </Text>
                                <Text as="p" variant="bodyMd">
                                To start using Blackcart, you must accept the billing terms. The terms will be outlined for your review on the next screen and you'll be asked to approve.
                                </Text>
                            </BlockStack>
                            <InlineStack align="end">
                                <ButtonGroup>
                                    <Button
                                        loading={loading}
                                        onClick={initiateBilling}
                                        accessibilityLabel="Enable Billing"
                                    >
                                        Review Billing
                                    </Button>
                                </ButtonGroup>
                            </InlineStack>
                        </BlockStack>
                    </Card>
                </Layout.Section>
            </Layout>
        </Page>
    );
};

export default Billing;
