import { useAuthenticatedFetch } from './useAuthenticatedFetch';
import { AnalyticsData } from '../components/contexts/BlackcartContext';

import { useState, useEffect } from 'react';
import { convertKeysToCamelCase } from './keyConverter';

const useAnalyticsApi = () => {
    const [analyticsData, setAnalyticsData] = useState<AnalyticsData | null>(null);
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState(null);
    const fetch = useAuthenticatedFetch();
    useEffect(() => {
        const fetchData = async () => {
            setIsLoading(true);
            setError(null);

            try {
                const response = await fetch('/api/stores/orders/analytics');
                const data = await response.json();
                if (data && data.analytics && data.analytics.data.length > 0) {
                    const analytics = convertKeysToCamelCase(data.analytics.data);
                    setAnalyticsData({'data': analytics});
                } else {
                    throw new Error('No analytics data found');
                }
            } catch (error: any) {
                setError(error);
            } finally {
                setIsLoading(false);
            }
        };
        fetchData();
    }, []);
    return { analyticsData, error, isLoading };
};

export default useAnalyticsApi;
