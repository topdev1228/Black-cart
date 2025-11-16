import useStoreSettingsApi from '../../hooks/useStoreSettingsApi';
import { renderHook } from "@testing-library/react";
// import { useAuthenticatedFetch } from '../../hooks/useAuthenticatedFetch';


global.fetch = jest.fn().mockResolvedValue({
  json: jest.fn().mockResolvedValue({
    settings: {
      test: {
        name: 'test',
        value: 'test',
      },
    },
  }),
});
jest.mock('../../hooks/useAuthenticatedFetch', () => ({
  useAuthenticatedFetch: jest.fn(() => global.fetch),
}));

describe("useFetchedData", () => {
  it("should return the initial values for data", async () => {
    const { result } = renderHook(() => useStoreSettingsApi());
    const { storeSettingsData } = result.current;

    expect(storeSettingsData).toBe(null);
  });

  it("handle empty array response", async () => {
    global.fetch = jest.fn().mockResolvedValue({
      json: jest.fn().mockResolvedValue({
        settings: []
      }),
    })
    const { result } = renderHook(() => useStoreSettingsApi());
    const { storeSettingsData } = result.current;

    expect(storeSettingsData).toBe(null);
  });
});
