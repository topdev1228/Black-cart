import { fireEvent, render, screen } from '@testing-library/react';
import { BlackcartContext } from '../../components/contexts/BlackcartContext';
import { RefObject, createRef } from 'react';
import { AppProvider } from '@shopify/polaris';
import React from 'react';
import StoreSettings from '../../pages/StoreSettings';

jest.mock('@shopify/app-bridge-react', () => ({
    useContextualSaveBar: jest.fn(() => ({
      show: jest.fn(),
      hide: jest.fn(),
      saveAction: { setOptions: jest.fn() },
      discardAction: { setOptions: jest.fn() },
    })),
  }));

jest.mock('../../constants/constants', () => ({
    APP_BLOCK_ID: 'test_id',
}));

describe('CheckoutConfigurations', () => {
    const save = jest.fn();
    const setStoreAttribute = jest.fn();
    const setProgramAttribute = jest.fn();
    const setStage = jest.fn();
    const setLatestStage = jest.fn();
    const saveForm = jest.fn();
    const formRef: RefObject<HTMLFormElement> = createRef();
    const setStoreSettingAttribute = jest.fn();
    const setProgram = jest.fn();
    const setStoreSettings = jest.fn();
  
    const storeSettings = {
        settings: {
            stage: {
                name: "stage",
                value: "checkoutConfigurations"
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
    const stage = 'checkoutConfigurations';
    const latestStage = 'checkoutConfigurations';
    
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
                        setStoreSettingAttribute,
                        setProgram,
                        setStoreSettings
                    }}
                >
                    <StoreSettings />
                </BlackcartContext.Provider>
            </AppProvider>
    );

    it('fields are rendered correctly', () => {
        const { getByRole, getByText } = render(wrapper);
        const formElement = screen.getByRole('form');
        fireEvent.submit(formElement);
        expect(getByText('Program Name')).toBeInTheDocument();
        expect(getByRole('textbox', { name: 'Program Name' })).toBeInTheDocument();
        expect(getByRole('spinbutton', { name: 'Deposit Recommended %' })).toBeInTheDocument();
        expect(getByRole('spinbutton', { name: 'Try Period Length days' })).toBeInTheDocument();
    });
});
