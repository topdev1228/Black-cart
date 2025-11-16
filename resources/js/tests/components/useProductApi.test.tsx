import useProductApi from '../../hooks/useProductApi';
import { renderHook, act } from "@testing-library/react";

global.fetch = jest.fn().mockResolvedValue({
    json: jest.fn().mockResolvedValue({
        data: {
            sellingPlanGroup: {
                products: {
                    edges: [
                        {
                            node: {
                                id: "gid://shopify/Product/7223659724939",
                                handle: "7-shakra-bracelet"
                            }
                        }
                    ]
                }
            }
        }
    }),
});
jest.mock('../../hooks/useAuthenticatedFetch', () => ({
    useAuthenticatedFetch: jest.fn(() => global.fetch),
}));

describe("useProductApi", () => {
    it("should return the product data", async () => {
        const { result } = renderHook(() => useProductApi());

        let productData;
        await act(async () => {
            productData = await result.current.getProduct("testProgramId");
        });

        expect(productData).toStrictEqual({
            id: "gid://shopify/Product/7223659724939",
            handle: "7-shakra-bracelet"
        });
    });
});