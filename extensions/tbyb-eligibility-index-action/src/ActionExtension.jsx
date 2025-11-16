import { useEffect, useState } from 'react';
import {
    AdminAction,
    BlockStack,
    Button,
    reactExtension,
    Text,
    useApi,
    Select,
} from '@shopify/ui-extensions-react/admin';

// The target used here must match the target used in the extension's toml file (./shopify.extension.toml)
const TARGET = 'admin.product-index.selection-action.render';

export default reactExtension(TARGET, () => <App />);

function App() {
    const {
        extension: { target },
        i18n,
        close,
        data,
    } = useApi(TARGET);
    const [sellingPlanGroupId, setSellingPlanGroupId] = useState('');
    const [addOrRemove, setAddOrRemove] = useState('true');
    const [loading, setLoading] = useState(true);
    const [inSellingPlan, setInSellingPlan] = useState([]);
    const [notInSellingPlan, setNotInSellingPlan] = useState([]);
    useEffect(() => {
        (async function getSellingPlan() {
            const programRes = await fetch(`/api/stores/programs`);
            const programData = await programRes.json();
            setSellingPlanGroupId(programData.programs[0].shopify_selling_plan_group_id);

            const fieldSelectors = data.selected
                .map((item, index) => `product${item.id.split('/').pop()}: appliesToProduct(productId: "${item.id}")`)
                .join('\n');

            const getSellingPlanQuery = {
                query: `query SellingPlanGroup($id: ID!) {
        sellingPlanGroup(id: $id) {
            ${fieldSelectors}
        }
    }`,
                variables: {
                    id: programData.programs[0].shopify_selling_plan_group_id,
                },
            };
            const res = await fetch('shopify:admin/api/graphql.json', {
                method: 'POST',
                body: JSON.stringify(getSellingPlanQuery),
            });
            const sellingPlanData = await res.json();
            const productIds = Object.keys(sellingPlanData.data.sellingPlanGroup).map((fieldName) =>
                fieldName.replace('product', ''),
            );

            // Separating into arrays based on the boolean values
            const inSellingPlan = [];
            const notInSellingPlan = [];

            productIds.forEach((id) => {
                const isTrue = sellingPlanData.data.sellingPlanGroup[`product${id}`];
                if (isTrue) {
                    inSellingPlan.push(`gid://shopify/Product/${id}`);
                } else {
                    notInSellingPlan.push(`gid://shopify/Product/${id}`);
                }
            });
            setInSellingPlan(inSellingPlan);
            setNotInSellingPlan(notInSellingPlan);
            setAddOrRemove('true');
            setLoading(false);
        })();
    }, []);

    async function addRemoveProductToPlanGroup(sellingPlanGroupId) {
        let mutation;
        if (addOrRemove === 'true') {
            mutation = `
  mutation sellingPlanGroupAddProducts($id: ID!, $productIds: [ID!]!) {
    sellingPlanGroupAddProducts(id: $id, productIds: $productIds) {
      sellingPlanGroup {
        id
      }
      userErrors {
        field
        message
      }
    }
  }
`;
        } else {
            mutation = `
  mutation sellingPlanGroupRemoveProducts($id: ID!, $productIds: [ID!]!) {
    sellingPlanGroupRemoveProducts(id: $id, productIds: $productIds) {
      userErrors {
        field
        message
      }
    }
  }
`;
        }

        const variables = {
            id: sellingPlanGroupId,
            productIds: addOrRemove === 'true' ? notInSellingPlan : inSellingPlan,
        };
        const res = await fetch('shopify:admin/api/graphql.json', {
            method: 'POST',
            body: JSON.stringify({
                query: mutation,
                variables: variables,
            }),
        });

        if (!res.ok) {
            console.error('Error adding product variants to selling plan group');
            return null;
        }

        // Handle the response as needed
        return await res.json();
    }

    return (
        // The AdminAction component provides an API for setting the title and actions of the Action extension wrapper.
        <AdminAction
            loading={loading}
            primaryAction={
                <Button
                    onPress={() => {
                        addRemoveProductToPlanGroup(sellingPlanGroupId);
                        close();
                    }}
                >
                    Done
                </Button>
            }
            secondaryAction={
                <Button
                    onPress={() => {
                        close();
                    }}
                >
                    Close
                </Button>
            }
        >
            <BlockStack>
                <Select
                    label="Add or Remove selected products from selling plan"
                    value={addOrRemove}
                    onChange={setAddOrRemove}
                    options={[
                        {
                            value: 'true',
                            label: 'Enable TBYB',
                        },
                        {
                            value: 'false',
                            label: 'Disable TBYB',
                        },
                    ]}
                />
            </BlockStack>
        </AdminAction>
    );
}
