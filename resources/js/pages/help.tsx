import { Page, Card, Link, Text } from '@shopify/polaris';
import React from 'react';

export default function Help() {
    return (
        <Page narrowWidth>
            <Card>
                <Text as="p" alignment="center">
                    If you need help, please visit our{' '}
                    <Link url="https://merchantsupport.blackcart.com" target="_blank">
                        Help Center
                    </Link>
                    .
                </Text>
            </Card>
        </Page>
    );
}
