import { useAuthenticatedFetch } from './useAuthenticatedFetch';
import { TransactionsData } from '../components/contexts/BlackcartContext';

import { useState, useEffect } from 'react';
import { convertKeysToCamelCase } from './keyConverter';

const useTransactionsApi = () => {
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState(null);
    const fetch = useAuthenticatedFetch();

    const getTransactions = async (startDatetime: string, endDatetime: string): Promise<TransactionsData | null> => {
        if (startDatetime && endDatetime) {
            setIsLoading(true);
            setError(null);
            try {
                const response = await fetch(
                    `/api/stores/orders/transactions?start=${startDatetime}&end=${endDatetime}`,
                );
                const responseJson = await response.json();

                if (responseJson) {
                    return convertKeysToCamelCase(responseJson);
                } else {
                    throw new Error('No transaction data found');
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

    const exportTransactions = async (startDatetime: string, endDatetime: string): Promise<void> => {
        if (startDatetime && endDatetime) {
            setIsLoading(true);
            setError(null);
            try {
                const response = await fetch(
                    `/api/stores/orders/transactions?start=${startDatetime}&end=${endDatetime}&export=1`,
                );
                const data = await response.blob();
                const url = window.URL.createObjectURL(data);
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'transactions.csv');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } catch (error: any) {
                setError(error);
                throw error;
            } finally {
                setIsLoading(false);
            }
        }
        return null;
    };

    return { exportTransactions, getTransactions, error, isLoading };
};

export default useTransactionsApi;
