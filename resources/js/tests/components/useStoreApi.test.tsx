import useStoreApi from '../../hooks/useStoreApi';
import { renderHook, act, waitFor} from "@testing-library/react";

global.fetch = jest.fn().mockResolvedValue({
    json: jest.fn().mockResolvedValue({
        stores: [
            {
                name: "test store",
                domain: "test.com",
            },
        ],
    }),
});
jest.mock('../../hooks/useAuthenticatedFetch', () => ({
    useAuthenticatedFetch: jest.fn(() => global.fetch),
}));

describe("useStoreApi", () => {
    it("should return the initial values for data", async () => {
        const { result } = renderHook(() => useStoreApi());

        await act(async () => {
            // Wait for the asynchronous code to complete
            await waitFor(() => result.current.storeData !== null);
        });

        const { storeData } = result.current;

        expect(storeData).toStrictEqual( {
            name: "test store",
            domain: "test.com",
        });
    });
});
