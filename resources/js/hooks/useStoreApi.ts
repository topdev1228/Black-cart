import { useAuthenticatedFetch } from "./useAuthenticatedFetch";
import {Store} from "../components/contexts/BlackcartContext";

import { useState, useEffect } from 'react';
import { convertKeysToCamelCase } from "./keyConverter";

const useStoreApi = () => {
  const [storeData, setStoreData] = useState<Store | null>(null);
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState(null);
  const fetch = useAuthenticatedFetch();
  
  useEffect(() => {
    const fetchData = async () => {
      setIsLoading(true);
      setError(null);
      
      try {
        const response = await fetch('/api/stores');
        const data = await response.json();
        if (data && data.stores && data.stores.length > 0) {
            const store = convertKeysToCamelCase(data.stores[0])
            setStoreData(store);
        } else {
            throw new Error('No store data found');
        } 
      } catch (error: any) {
        setError(error);
      } finally {
        setIsLoading(false);
      }
    };

    fetchData();
  }, []);

  return {storeData, error, isLoading}
};

export default useStoreApi;