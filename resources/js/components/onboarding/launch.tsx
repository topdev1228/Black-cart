import { Layout, MediaCard } from '@shopify/polaris';
import React, { useContext } from 'react';
import { launchImg, testProgram } from '../../assets';
import { ExternalIcon } from '@shopify/polaris-icons';
import { BlackcartContext } from '../contexts/BlackcartContext';
import '../../../css/app.css';

function getTitle(): string {
    return 'Launch your Program!';
}

interface launchProps {}

const Launch = (props: launchProps) => {
    const { store, launch, stage, product } = useContext(BlackcartContext);
    return (
        <Layout>
            {stage !== 'postLaunch' && (
                <Layout.Section>
                    <MediaCard
                        title="Test your Try Before You Buy Program"
                        description="View your Try Before You Buy (TBYB) program in a theme preview. Orders placed in preview mode are real, so be sure to stop fulfillment on the test order. The TBYB theme blocks are not visible to your customers until you launch the TBYB program."
                        primaryAction={{
                            content: 'Preview Here',
                            icon: ExternalIcon,
                            plain: true,
                            onAction: () => {
                                window.open(`https://${store?.domain}/products/${product?.handle}?tbyb=1`, '_blank');
                            },
                        }}
                    >
                        <img
                            width="100%"
                            height="100%"
                            style={{
                                objectPosition: 'center',
                            }}
                            src={testProgram}
                            alt="Test your Try Before You Buy Program"
                        ></img>
                    </MediaCard>
                </Layout.Section>
            )}
            {stage !== 'postLaunch' && (
                <Layout.Section>
                    <MediaCard
                        title="Launch your Try Before You Buy program"
                        description="Clicking Launch will publish Try Before You Buy to shoppers on your store."
                        primaryAction={{
                            content: 'Launch',
                            id: 'launch-button',
                            onAction: () => {
                                launch();
                            },
                        }}
                    >
                        <img
                            width="100%"
                            height="100%"
                            style={{
                                objectPosition: 'center',
                            }}
                            src={launchImg}
                            alt="Launch your Try Before You Buy program"
                        ></img>
                    </MediaCard>
                </Layout.Section>
            )}
        </Layout>
    );
};

Launch.getTitle = getTitle;
export default Launch;
