import { useAuthenticatedFetch } from './useAuthenticatedFetch';
import { Product } from '../components/contexts/BlackcartContext';

import { useState, useEffect } from 'react';
import { convertKeysToCamelCase } from './keyConverter';

const useProductApi = () => {
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState(null);
    const fetch = useAuthenticatedFetch();

    const getProduct = async (programId: string): Promise<Product | null> => {
        if (programId) {
            setIsLoading(true);
            setError(null);
            try {
                const response = await fetch(`/api/stores/programs/${programId}/products`);
                const data = await response.json();
                if (data && data.data.sellingPlanGroup.products.edges.length > 0) {
                    const product = convertKeysToCamelCase(data.data.sellingPlanGroup.products.edges[0].node);
                    return product;
                } else {
                    throw new Error('No product data found');
                }
            } catch (error: any) {
                setError(error);
                throw error;
            } finally {
                setIsLoading(false);
            }
        }
        return null;
    };
    return { getProduct, error, isLoading };
};

export default useProductApi;
