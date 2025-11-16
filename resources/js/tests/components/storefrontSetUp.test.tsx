import { render, screen } from '@testing-library/react';
import { BlackcartContext } from '../../components/contexts/BlackcartContext';
import { RefObject, createRef } from 'react';
import { AppProvider } from '@shopify/polaris';
import StoreFrontSetUp from '../../components/onboarding/storefrontSetUp';
import React from 'react';

jest.mock('../../constants/constants', () => ({
    APP_BLOCK_ID: 'test_id',
}));

describe('storefrontSetUp', () => {
    const save = jest.fn();
    const setStoreAttribute = jest.fn();
    const setProgramAttribute = jest.fn();
    const setStage = jest.fn();
    const setLatestStage = jest.fn();
    const saveForm = jest.fn();
    const formRef: RefObject<HTMLFormElement> = createRef();
    const setStoreSettingAttribute = jest.fn();
    
    const storeSettings = {
        settings: {
            stage: {
                name: "stage",
                value: "storefrontSetUp"
            }
        }
    };

    const store = {
        id: '1',
        name: 'test',
        domain: 'test',
    };
    const program = {
        id: '1',
        storeId: '123',
        name: 'Test Program',
        depositValue: 100,
        depositType: 'percentage',
        tryPeriodDays: 30,
        minTbybItems: 1,
        maxTbybItems: 2,
    };
    const stage = 'storefrontSetUp';
    const latestStage = 'storefrontSetUp';
    
    const wrapper = (
            <AppProvider i18n={[]}>
                <BlackcartContext.Provider
                    value={{
                        formRef,
                        program,
                        store,
                        stage,
                        latestStage,
                        storeSettings,
                        save,
                        setStoreAttribute,
                        setProgramAttribute,
                        setStage,
                        setLatestStage,
                        saveForm,
                        setStoreSettingAttribute
                    }}
                >
                    <StoreFrontSetUp />
                </BlackcartContext.Provider>
            </AppProvider>
    );

    it('page is rendered correctly', () => {
        const { getByText } = render(wrapper);
        expect(getByText('Review Your Product Eligibility')).toBeInTheDocument();
    });
});
