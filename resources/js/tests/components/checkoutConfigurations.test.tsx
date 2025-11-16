import { fireEvent, render, screen, waitFor } from '@testing-library/react';
import { BlackcartContext } from '../../components/contexts/BlackcartContext';
import CheckoutConfigurations from '../../components/onboarding/checkoutConfigurations';
import { RefObject, createRef } from 'react';
import { AppProvider } from '@shopify/polaris';
import React from 'react';

describe('CheckoutConfigurations', () => {
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
                name: 'stage',
                value: 'checkoutConfigurations',
            },
        },
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
        depositType: 'fixed',
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
                }}
            >
                <CheckoutConfigurations />
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

    it('validation is triggered correctly', () => {
        program.depositValue = -100;
        program.tryPeriodDays = 0;
        program.maxTbybItems = -100;
        program.name = '';

        const { getByText } = render(wrapper);
        const formElement = screen.getByRole('form');
        fireEvent.submit(formElement);
        expect(getByText('The deposit must be between 0 and 100 %')).toBeInTheDocument();
        expect(getByText('The program name is required')).toBeInTheDocument();
        expect(getByText('The period length must be greater than 3')).toBeInTheDocument();
        expect(getByText('The maximum number of items must be greater than the minimum')).toBeInTheDocument();

    });

    it('deposit type is set correctly on render', async () => {
        render(wrapper);
        await waitFor(() => {
            expect(setProgramAttribute).toHaveBeenCalledWith('depositType', 'percentage');
        });
    });
});
