import { Page, Layout, Modal, Spinner } from '@shopify/polaris';
import { useTranslation, Trans } from 'react-i18next';
import { BlackcartContext } from '../components/contexts/BlackcartContext';
import { useState, useContext, useCallback, useEffect } from 'react';
import Confetti from 'react-dom-confetti';
import { useNavigate } from '@shopify/app-bridge-react';
import Analytics from './Analytics';

export default function HomePage() {
    const { t } = useTranslation();
    const { stage, setStage } = useContext(BlackcartContext);
    const [active, setActive] = useState(false);
    const navigate = useNavigate();

    const handleChange = useCallback(() => {
        setActive(!active), [active];
        setStage('postLaunch');
    });
    useEffect(() => {
        if (stage === 'showLaunchModal') {
            setActive(true);
        }
    }, [stage]);

    const config = {
        angle: 90,
        spread: 360,
        startVelocity: 30,
        elementCount: 70,
        dragFriction: 0.12,
        duration: 3000,
        stagger: 3,
        width: '10px',
        height: '10px',
        colors: ['#a864fd', '#29cdff', '#78ff44', '#ff718d', '#fdff6a'],
    };

    return (
        <Page>
            {stage === 'postLaunch' && (
                <ui-nav-menu>
                    <a href="/" rel="home">
                        Home
                    </a>
                    <a href="/transactions">Transactions</a>
                    <a href="/storeSettings">Store Settings</a>
                    <a href="/help">Help Center</a>
                </ui-nav-menu>
            )}
            <Layout>
                {stage !== 'postLaunch' && (
                    <div style={{ display: 'flex', justifyContent: 'center', alignItems: 'center', height: '100vh' }}>
                        <Spinner />
                    </div>
                )}
                <Confetti active={active} config={config} />
                <Modal
                    open={active}
                    onClose={handleChange}
                    title="Congratulations!"
                    primaryAction={{
                        content: 'Continue',
                        onAction: handleChange,
                    }}
                >
                    <Modal.Section>
                        <p>You've launched your Try Before You Buy program.</p>
                        <ul>
                            <li>Check out your home page to find useful analytics about your program.</li>
                            <li>Check out your Store Settings page to make any changes to your program.</li>
                            <li>For any questions visit our help center!</li>
                        </ul>
                    </Modal.Section>
                </Modal>
            </Layout>
            {!active && stage === 'postLaunch' && <Analytics />}
        </Page>
    );
}
