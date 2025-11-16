import { BlockStack, Checkbox, extend, Text } from '@shopify/admin-ui-extensions';

// Your extension must render all four modes
extend('Admin::Product::SubscriptionPlan::Add', Add);
extend('Admin::Product::SubscriptionPlan::Create', Create);
extend('Admin::Product::SubscriptionPlan::Remove', Remove);
extend('Admin::Product::SubscriptionPlan::Edit', Edit);

function Edit(root, { extensionPoint }) {
    root.appendChild(
        root.createComponent(
            Text,
            {},
            `Selling plan options are coming soon. Go to the Blackcart app to modify your existing selling plan`,
        ),
    );
    root.mount();
}

function Create(root, { extensionPoint }) {
    root.appendChild(
        root.createComponent(
            Text,
            {},
            `Selling plan options are coming soon. Go to the Blackcart app to modify your existing selling plan`,
        ),
    );
    root.mount();
}

const backend = 'https://shop-app.blackcart.com';

async function fetchProgramId(sessionToken) {
    const token = await sessionToken.getSessionToken();
    const response = await fetch(backend + '/api/stores/programs', {
        headers: {
            Authorization: 'Bearer ' + token || 'unknown token',
        },
    });
    const responseData = await response.json();
    return responseData.programs[0].id;
}

async function fetchVariants(sessionToken, productId, variantId) {
    const token = await sessionToken.getSessionToken();
    const response = await fetch(backend + '/api/stores/products/' + productId, {
        headers: {
            Authorization: 'Bearer ' + token || 'unknown token',
        },
    });
    const responseData = await response.json();
    if (variantId !== undefined) {
        const filteredVariants = responseData.data.product.variants.edges.filter(
            (variant) => variant.node.id === variantId,
        );

        responseData.data.product.variants.edges = filteredVariants;
    }
    return responseData;
}

async function variantsInSellingPlan(sessionToken, variantIds, programId) {
    const token = await sessionToken.getSessionToken();
    const encodedVariantIds = variantIds.map((id) => encodeURIComponent(id)).join(',');
    const url = `${backend}/api/stores/programs/${programId}/variants?shopify_variant_ids=${encodedVariantIds}`;

    const response = await fetch(url, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            Authorization: 'Bearer ' + token || 'unknown token',
        },
    });
    const responseData = await response.json();
    return responseData;
}

const selectedVariants = {};
let removeProduct = false;
let program = '';

function Add(root, api) {
    const { close, done, setPrimaryAction, setSecondaryAction } = api.container;

    setPrimaryAction({
        content: 'Add to plan',
        onAction: async () => {
            // Get a fresh session token before every call to your app server.
            const token = await sessionToken.getSessionToken();

            const selectedVariantIds = Object.keys(selectedVariants);

            const formData = {
                productId: api.data.productId,
                selected_variant_ids: selectedVariantIds,
            };

            const response = await fetch(backend + '/api/stores/programs/' + program + '/variants', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Authorization: 'Bearer ' + token || 'unknown token',
                },
                body: JSON.stringify(formData),
            });
            done();
        },
    });
    setSecondaryAction({
        content: 'Cancel',
        onAction: () => close(),
    });

    const sessionToken = api.sessionToken;
    const parts = api.data.productId.split('/');
    const productId = parts[parts.length - 1];
    const variantId = api.data.variantId;
    fetchProgramId(sessionToken).then((programId) => {
        program = programId;
        fetchVariants(sessionToken, productId, variantId)
            .then((responseData) => {
                const variants = responseData.data.product.variants.edges;
                const variantIds = variants.map((variant) => variant.node.id);
                variantsInSellingPlan(sessionToken, variantIds, program)
                    .then((variantsInSellingPlanData) => {
                        const filteredVariants = variants.filter((variant) => {
                            const variantId = variant.node.id.split('/').pop();
                            const sellingPlanKey = `productVariant${variantId}`;
                            return !variantsInSellingPlanData.data.sellingPlanGroup[sellingPlanKey];
                        });
                        const blockStack = root.createComponent(BlockStack, {
                            inlineAlignment: 'leading',
                            spacing: 'tight',
                        });

                        const addText =
                            variantId !== undefined ? 'Add variant to TBYB' : 'Add product and variants to TBYB';
                        const allVariantsAdded =
                            variantId !== undefined
                                ? 'Variant already added to TBYB'
                                : 'All variants are already added to TBYB';
                        if (filteredVariants === undefined || filteredVariants.length == 0) {
                            blockStack.appendChild(root.createComponent(Text, {}, allVariantsAdded));
                        } else {
                            blockStack.appendChild(root.createComponent(Text, {}, addText));
                        }

                        const handleCheckboxChange = (variantId, variantName, isChecked, checkbox) => {
                            checkbox.updateProps({ checked: isChecked });
                            if (isChecked) {
                                selectedVariants[variantId] = variantName;
                            } else {
                                delete selectedVariants[variantId];
                            }
                        };

                        // Create checkboxes dynamically for each variant
                        filteredVariants.forEach((variant) => {
                            const variantName = variant.node.displayName;
                            const variantId = variant.node.id;
                            const checkbox = root.createComponent(Checkbox, {
                                label: `${variantName}`,
                                onChange: (isChecked) =>
                                    handleCheckboxChange(variantId, variantName, isChecked, checkbox),
                            });
                            blockStack.appendChild(checkbox);
                        });

                        root.appendChild(blockStack);
                        root.mount();
                    })
                    .catch((error) => {
                        console.error('Error fetching data:', error);
                    });
            })
            .catch((error) => {
                console.error('Error fetching data:', error);
            });
    });
}

function Remove(root, api) {
    const { close, done, setPrimaryAction, setSecondaryAction } = api.container;

    setPrimaryAction({
        content: 'Remove from plan',
        onAction: async () => {
            // Get a fresh session token before every call to your app server.
            const token = await sessionToken.getSessionToken();
            
            if (removeProduct) {
                const formData = {
                    productId: api.data.productId,
                    selected_product_ids: [api.data.productId],
                };

                const response = await fetch(backend + '/api/stores/programs/' + program + '/products', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        Authorization: 'Bearer ' + token || 'unknown token',
                    },
                    body: JSON.stringify(formData),
                });
            } else {
                const selectedVariantIds = Object.keys(selectedVariants);

                const formData = {
                    productId: api.data.productId,
                    selected_variant_ids: selectedVariantIds,
                };

                const response = await fetch(backend + '/api/stores/programs/' + program + '/variants', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        Authorization: 'Bearer ' + token || 'unknown token',
                    },
                    body: JSON.stringify(formData),
                });
            }
            done();
        },
    });
    setSecondaryAction({
        content: 'Cancel',
        onAction: () => close(),
    });

    const sessionToken = api.sessionToken;
    const parts = api.data.productId.split('/');
    const productId = parts[parts.length - 1];
    const variantId = api.data.variantId;

    fetchProgramId(sessionToken).then((programId) => {
        program = programId;
        fetchVariants(sessionToken, productId, variantId)
            .then((responseData) => {
                const variants = responseData.data.product.variants.edges;
                const variantIds = variants.map((variant) => variant.node.id);
                variantsInSellingPlan(sessionToken, variantIds, program)
                    .then((variantsInSellingPlanData) => {
                        const filteredVariants = variants.filter((variant) => {
                            const variantId = variant.node.id.split('/').pop();
                            const sellingPlanKey = `productVariant${variantId}`;
                            return variantsInSellingPlanData.data.sellingPlanGroup[sellingPlanKey];
                        });
                        const blockStack = root.createComponent(BlockStack, {
                            inlineAlignment: 'leading',
                            spacing: 'tight',
                        });
                        
                        if (filteredVariants.length === 0) {
                            removeProduct = true;
                        }

                        const removeText = filteredVariants.length === 0 ? 'Remove product from TBYB' :
                            variantId !== undefined ? 'Remove variant from TBYB' : 'Remove variants from TBYB';
                        blockStack.appendChild(root.createComponent(Text, {}, removeText));

                        const handleCheckboxChange = (variantId, variantName, isChecked, checkbox) => {
                            checkbox.updateProps({ checked: isChecked });
                            if (isChecked) {
                                selectedVariants[variantId] = variantName;
                            } else {
                                delete selectedVariants[variantId];
                            }
                        };

                        // Create checkboxes dynamically for each variant
                        filteredVariants.forEach((variant) => {
                            const variantName = variant.node.displayName;
                            const variantId = variant.node.id;
                            const checkbox = root.createComponent(Checkbox, {
                                label: `${variantName}`,
                                onChange: (isChecked) =>
                                    handleCheckboxChange(variantId, variantName, isChecked, checkbox),
                            });
                            blockStack.appendChild(checkbox);
                        });

                        root.appendChild(blockStack);
                        root.mount();
                    })
                    .catch((error) => {
                        console.error('Error fetching data:', error);
                    });
            })
            .catch((error) => {
                console.error('Error fetching data:', error);
            });
    });
}
