import useProgramApi from '../../hooks/useProgramApi';
import { renderHook, act, waitFor} from "@testing-library/react";

global.fetch = jest.fn().mockResolvedValue({
    json: jest.fn().mockResolvedValue({
        programs: [
            {
                id: "1",
                name: "test",
                depositValue: 10,
                tryPeriodDays: 1,
                minTbybItems: 1,
                maxTbybItems: 2,
                storeId: 1
            },
        ],
    }),
});
jest.mock('../../hooks/useAuthenticatedFetch', () => ({
    useAuthenticatedFetch: jest.fn(() => global.fetch),
}));

describe("useProgramApi", () => {
    it("should return the initial values for data", async () => {
        const { result } = renderHook(() => useProgramApi());

        await act(async () => {
            // Wait for the asynchronous code to complete
            await waitFor(() => result.current.programData !== null);
        });

        const { programData } = result.current;

        expect(programData).toStrictEqual({
            id: "1",
            name: "test",
            depositValue: 10,
            tryPeriodDays: 1,
            minTbybItems: 1,
            maxTbybItems: 2,
            storeId: 1
        });
    });
});
