import { useAuthenticatedFetch } from './useAuthenticatedFetch';

import { useState, useEffect } from 'react';
import { convertKeysToCamelCase } from './keyConverter';
import { Subscription } from '~/components/contexts/BlackcartContext';

const useSubscriptionApi = () => {
    const [appInstallationData, setAppInstallationData] = useState<any | null>(null);
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState(null);
    const fetch = useAuthenticatedFetch();

    useEffect(() => {
        const fetchData = async () => {
            setIsLoading(true);
            setError(null);
            try {
                const response = await fetch('/api/stores/billings/subscriptions/shopify_current_app_installation');
                const data = await response.json();
                if (data.shopify_current_app_installation) {
                    const appInstallation = convertKeysToCamelCase(data.shopify_current_app_installation);
                    setAppInstallationData(appInstallation);
                } else {
                    throw new Error('No subscription data found');
                }
            } catch (error: any) {
                setError(error);
            } finally {
                setIsLoading(false);
            }
        };
        fetchData();
    }, []);

    const createSubscription = async (): Promise<Subscription> => {
        setIsLoading(true);
        setError(null);
        try {
            const response = await fetch(`/api/stores/billings/subscriptions`, {
                method: 'POST',
                headers: {
                    'Content-type': 'application/json; charset=UTF-8',
                },
            });
            const data = await response.json();
            const subscription = convertKeysToCamelCase(data.subscription);
            return subscription;
        } catch (error: any) {
            setError(error);
            throw error;
        } finally {
            setIsLoading(false);
        }
    };
    return { appInstallationData, createSubscription, error, isLoading };
};

export default useSubscriptionApi;
