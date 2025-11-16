import { useAuthenticatedFetch } from './useAuthenticatedFetch';
import { StoreSetting, StoreSettings } from '../components/contexts/BlackcartContext';
import { useState, useEffect } from 'react';
import { convertKeysToSnakeCase } from './keyConverter';

const useStoreSettingsApi = () => {
    const [storeSettingsData, setStoreSettingsData] = useState<StoreSettings | null>(null);
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState(null);
    const fetch = useAuthenticatedFetch();

    useEffect(() => {
        const fetchData = async () => {
            setIsLoading(true);
            setError(null);
            try {
                const response = await fetch('/api/stores/settings');
                const data = await response.json();
                if (data.settings) {
                    data.settings = Array.isArray(data.settings) ? {} : data.settings;
                }
                setStoreSettingsData(data);
            } catch (error: any) {
                setError(error);
            } finally {
                setIsLoading(false);
            }
        };

        fetchData();
    }, []);

    const updateStoreSettings = async (storeSettings: StoreSettings | null): Promise<StoreSettings> => {
        if (!storeSettings) {
            throw new Error('Store Setting is required for update');
        }

        try {
            const response = await fetch('/api/stores/settings', {
                method: 'PATCH',
                body: JSON.stringify(storeSettings),
                headers: {
                    'Content-type': 'application/json; charset=UTF-8',
                },
            });
            const updatedStoreSetting = await response.json();
            return updatedStoreSetting;
        } catch (error: any) {
            setError(error);
            throw error;
        }
    };

    return { storeSettingsData, updateStoreSettings, error, isLoading };
};

export default useStoreSettingsApi;
