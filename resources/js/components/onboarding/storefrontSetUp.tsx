import {
    Badge,
    BlockStack,
    Button,
    ButtonGroup,
    Card,
    InlineGrid,
    Layout,
    MediaCard,
    Text,
    Banner,
    Collapsible,
} from '@shopify/polaris';
import React, { useCallback, useContext, useState } from 'react';
import { toggle as toggleImage, button as buttonImage, cartPage, learnMore } from '../../assets';
import { ExternalIcon } from '@shopify/polaris-icons';
import { BlackcartContext } from '../contexts/BlackcartContext';
import { productEligibility } from '../../assets';
import { CaretDownIcon, CaretUpIcon } from '@shopify/polaris-icons';

import '../../../css/app.css';
import { APP_BLOCK_ID } from '../../constants/constants';

function getTitle(): string {
    return 'Storefront Setup';
}

interface storeFrontSetUpProps {}

const StoreFrontSetUp = (props: storeFrontSetUpProps) => {
    const { store, setThemeInstallClicked } = useContext(BlackcartContext);
    const appBlockId = APP_BLOCK_ID ?? '';
    const [open, setOpen] = useState(false);

    const handleToggle = useCallback(() => setOpen((open) => !open), []);
    const imageStyle = {
        width: '100%',
        height: 'auto',
        display: 'block',
        borderRadius: '10px',
    };

    return (
        <Layout>
            <Layout.Section>
                <MediaCard
                    title="Review Your Product Eligibility"
                    description="To maximize Try Before You Buy's impact and provide a consistent user experience, make most of your products eligible.

                    Click the link below to learn how to update your product eligibilities."
                    primaryAction={{
                        content: 'Learn How',
                        plain: true,
                        icon: ExternalIcon,
                        onAction: () => {
                            window.open(
                                'https://merchantsupport.blackcart.com/hc/en-us/articles/23064981894285',
                                '_blank',
                            );
                        },
                    }}
                >
                    <img
                        width="100%"
                        height="100%"
                        style={{
                            objectPosition: 'center',
                        }}
                        src={productEligibility}
                        alt="Review Your Product Eligibility"
                    ></img>
                </MediaCard>
            </Layout.Section>
            <Layout.Section>
                <Card>
                    <BlockStack gap="400">
                        <Text variant="headingSm" as="h6">
                            Add your TBYB widget
                        </Text>
                        <Text variant="bodySm" as="p">
                            In this step, you'll add the Try Before You Buy widget to your product detail page template
                            within Shopify's theme editor. The Blackcart app block will only be visible to live traffic when your Try Before You Buy (TBYB) program is launched.${' '}
                        </Text>
                        <Text variant="bodySm" as="p">
                            You can choose between a toggle with a single add to cart button or an additional TBYB
                            button. We recommend the toggle - our merchant data shows a 40% higher conversion increase
                            with the toggle.
                        </Text>
                        <InlineGrid gap="400" columns={2}>
                            <div>
                                Toggle Style <Badge tone="attention">Converts 40% higher</Badge>
                            </div>
                            <div>Button Style</div>
                        </InlineGrid>
                        <InlineGrid gap="400" columns={2}>
                            <img src={toggleImage} style={imageStyle} alt="toggle"></img>
                            <img src={buttonImage} style={imageStyle} alt="tbyb button"></img>
                        </InlineGrid>
                        <Banner>
                            When you click the button below to install, we will try to automatically place the toggle in
                            your template. Click and drag the app block to the desired placed on the product page (we
                            recommend directly above the Add to Cart button).
                            <Collapsible
                                open={true}
                                id="basic-collapsible"
                                transition={{ duration: '500ms', timingFunction: 'ease-in-out' }}
                                expandOnPrint
                            >
                                <br />
                                If we're unable to automatically place it, you will need to manually place it.
                                <ul style={{ listStyleType: 'disc', paddingLeft: '15px' }}>
                                    <li>Click '+ Add Block'</li>
                                    <li>Select Blackcart TBYB under App Blocks</li>
                                    <li>
                                        Click and drag the app block to the desired placed on the product page (we
                                        recommend directly above the Add to Cart button).
                                    </li>
                                </ul>
                            </Collapsible>
                            <br />
                        </Banner>
                        <div style={{ display: 'flex', justifyContent: 'flex-end' }}>
                            <ButtonGroup>
                                <Button
                                    variant="plain"
                                    icon={ExternalIcon}
                                    url={
                                        'https://merchantsupport.blackcart.com/hc/en-us/articles/23948428625293-Add-Your-TBYB-Widget'
                                    }
                                >
                                    Learn more
                                </Button>
                                <Button
                                    variant="primary"
                                    icon={ExternalIcon}
                                    url={`https://${store?.domain}/admin/themes/current/editor?template=product&addAppBlockId=${appBlockId}/tbyb&target=mainSection`}
                                    onClick={setThemeInstallClicked}
                                >
                                    Install in Theme
                                </Button>
                            </ButtonGroup>
                        </div>
                    </BlockStack>
                </Card>
            </Layout.Section>
        </Layout>
    );
};

StoreFrontSetUp.getTitle = getTitle;
export default StoreFrontSetUp;
