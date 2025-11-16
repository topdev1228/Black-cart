import { fireEvent, render, screen } from '@testing-library/react';
import { BlackcartContext } from '../../components/contexts/BlackcartContext';
import { RefObject, createRef } from 'react';
import { AppProvider } from '@shopify/polaris';
import ReturnsConfigurations from '../../components/onboarding/returnsConfigurations';
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
                name: "stage",
                value: "checkoutConfigurations"
            },
            returnsPortalUrl: {
              
            },
            customerSupportEmail: {
              
            },
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
                        setStoreSettingAttribute
                    }}
                >
                    <ReturnsConfigurations />
                </BlackcartContext.Provider>
            </AppProvider>
    );

    it('fields are rendered correctly', () => {
        const { getByRole } = render(wrapper);
        const formElement = screen.getByRole('form');
        fireEvent.submit(formElement);
        expect(getByRole('textbox', { name: 'Return Portal URL' })).toBeInTheDocument();
        expect(getByRole('textbox', { name: 'Customer Support Email or Website' })).toBeInTheDocument();
    });

    it('validation is triggered correctly', () => {
        const { getByText } = render(wrapper);
        const formElement = screen.getByRole('form');
        fireEvent.submit(formElement);

        expect(getByText('A Customer Support email or website is required')).toBeInTheDocument();
        expect(getByText('A Returns Portal URL is required')).toBeInTheDocument();
    });

    it('email and url validation is triggered correctly', () => {
        storeSettings.settings.returnsPortalUrl = {
            name: 'returnsPortalUrl',
            value: 'test',
        };
        storeSettings.settings.customerSupportEmail = {
            name: 'customerSupportEmail',
            value: 'test',
        };
        const { getByText } = render(wrapper);
        const formElement = screen.getByRole('form');
        fireEvent.submit(formElement);
        
        expect(getByText('Please enter a valid email or website')).toBeInTheDocument();
        expect(getByText('Please enter a valid URL')).toBeInTheDocument();
    });

    it('prepends https:// to returnsPortalUrl if it does not start with http:// or https://', () => {
        storeSettings.settings.returnsPortalUrl = {
            name: 'returnsPortalUrl',
            value: 'example.com',
        };
        const { getByLabelText, getByRole } = render(wrapper);

        const input = getByLabelText('Return Portal URL') as HTMLInputElement;
        fireEvent.blur(input);

        expect(setStoreSettingAttribute).toHaveBeenCalled();
        expect(setStoreSettingAttribute).toHaveBeenCalledWith('returnsPortalUrl', 'https://example.com');    });

});
