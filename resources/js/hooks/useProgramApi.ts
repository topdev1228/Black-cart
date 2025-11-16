import { useAuthenticatedFetch } from './useAuthenticatedFetch';
import { Program } from '../components/contexts/BlackcartContext';

import { useState, useEffect } from 'react';
import { convertKeysToCamelCase, convertKeysToSnakeCase } from './keyConverter';

const useProgramApi = () => {
    const [programData, setProgramData] = useState<Program | null>(null);
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState(null);
    const fetch = useAuthenticatedFetch();

    useEffect(() => {
        const fetchData = async () => {
            setIsLoading(true);
            setError(null);

            try {
                const response = await fetch('/api/stores/programs');
                const data = await response.json();
                if (data && data.programs && data.programs.length > 0) {
                    const program = convertKeysToCamelCase(data.programs[0]);
                    setProgramData(program);
                } else {
                    throw new Error('No program data found');
                }
            } catch (error: any) {
                setError(error);
            } finally {
                setIsLoading(false);
            }
        };

        fetchData();
    }, []);

    const updateProgram = async (updatedProgram: Program | null): Promise<Program> => {
        if (!updatedProgram) {
            throw new Error('Program is required for update');
        }

        try {
            const formattedProgram = convertKeysToSnakeCase(updatedProgram);
            if (formattedProgram.max_tbyb_items === 'unlimited') {
                formattedProgram.max_tbyb_items = null;
            }
            const response = await fetch(`/api/stores/programs/${updatedProgram.id}`, {
                method: 'PUT',
                body: JSON.stringify(formattedProgram),
                headers: {
                    'Content-type': 'application/json; charset=UTF-8',
                },
            });
            const updatedProgramData = await response.json();
            return updatedProgramData;
        } catch (error: any) {
            setError(error);
            throw error;
        }
    };

    return { programData, updateProgram, error, isLoading };
};

export default useProgramApi;
