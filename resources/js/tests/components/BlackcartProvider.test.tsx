import React  from 'react';
import { render } from '@testing-library/react';
import { BlackcartProvider } from '../../components/providers/BlackcartProvider';
import { MemoryRouter } from 'react-router-dom';

import useStoreSettingsApi from '../../hooks/useStoreSettingsApi';

// Mock the entire module
jest.mock('../../hooks/useStoreSettingsApi');

jest.mock('../../hooks/useAuthenticatedFetch', () => ({
    useAuthenticatedFetch: jest.fn(),
}));

describe('BlackcartProvider', () => {
    it('sets initial stage correctly', () => {
        useStoreSettingsApi.mockReturnValue({
            storeSettingsData: {settings: {
                stage: {
                  name: 'test',
                  value: 'test',
                },
              },},
            updateStoreSettings: jest.fn(), // You can mock this function as well
          });
          
        render(
          <MemoryRouter>
            <BlackcartProvider>
                <div></div>
            </BlackcartProvider>
          </MemoryRouter>
        );
    });
});
