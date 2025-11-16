import { render, fireEvent } from '@testing-library/react';
import { BlackcartContext } from '../../components/contexts/BlackcartContext';
import { RefObject, createRef } from 'react';
import { AppProvider } from '@shopify/polaris';
import React from 'react';
import Launch from '../../components/onboarding/launch';

describe('launch', () => {
    const save = jest.fn();
    const setStoreAttribute = jest.fn();
    const setProgramAttribute = jest.fn();
    const setStage = jest.fn();
    const setLatestStage = jest.fn();
    const saveForm = jest.fn();
    const formRef: RefObject<HTMLFormElement> = createRef();
    const setStoreSettingAttribute = jest.fn();
    const launch = jest.fn();

    const storeSettings = {
        settings: {
            stage: {
                name: "stage",
                value: "launch"
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
    const stage = 'launch';
    const latestStage = 'launch';
    
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
                        launch
                    }}
                >
                    <Launch />
                </BlackcartContext.Provider>
            </AppProvider>
    );

    it('page renders correctly', () => {
        const { getByText, getByTestId } = render(wrapper);
        expect(getByText('Test your Try Before You Buy Program')).toBeInTheDocument();
        expect(getByText('Launch your Try Before You Buy program')).toBeInTheDocument();
        const launchButton = document.getElementById('launch-button');
        if (launchButton) {
            fireEvent.click(launchButton);
        }
        expect(launch).toHaveBeenCalled();
    });
});
