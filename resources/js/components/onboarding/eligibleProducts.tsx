import { ResourcePicker } from '@shopify/app-bridge-react';
import {
    IndexFilters,
    IndexFiltersProps,
    IndexTable,
    IndexTableSelectionType,
    InlineStack,
    TabProps,
    Text,
    Thumbnail,
    useIndexResourceState,
    useSetIndexFiltersMode,
} from '@shopify/polaris';
import React from 'react';
import { useCallback, useEffect, useState } from 'react';

function getTitle(): string {
    return 'Product Eligiblility for Try Before You Buy';
}

interface eligibleProductsProps {}
interface Product {
    id: string;
    thumbnail: JSX.Element;
    product: JSX.Element;
    status: string;
    tbyb: boolean;
    [key: string]: unknown;
}
const baseProducts = [
    {
        id: '1020',
        thumbnail: (
            <Thumbnail
                size="medium"
                source="https://burst.shopifycdn.com/photos/black-leather-choker-necklace_373x@2x.jpg"
                alt="Black choker necklace"
            ></Thumbnail>
        ),
        product: (
            <Text as="span" variant="bodyMd">
                Black choker necklace
            </Text>
        ),
        status: 'active',
        tbyb: true,
    },
    {
        id: '1021',
        thumbnail: (
            <Thumbnail
                size="medium"
                source="https://test-store-jlonghi.myshopify.com/cdn/shop/products/download_76fbf85e-88c5-4f20-8e76-41555a1e5802_compact.jpg?v=1626121748"
                alt="Pants"
            ></Thumbnail>
        ),
        product: (
            <Text as="span" variant="bodyMd">
                Pants
            </Text>
        ),
        status: 'draft',
        tbyb: true,
    },
    {
        id: '1028',
        thumbnail: (
            <Thumbnail
                size="medium"
                source="https://cdn.pixabay.com/photo/2016/12/06/09/31/blank-1886008_640.png"
                alt="shirt"
            ></Thumbnail>
        ),
        product: (
            <Text as="span" variant="bodyMd">
                Shirt
            </Text>
        ),
        status: 'draft',
        tbyb: false,
    },
];
const EligibleProducts = (props: eligibleProductsProps) => {
    const [products, setProducts] = useState<Product[]>([]);

    useEffect(() => {
        setProducts(baseProducts);
    }, [baseProducts]);

    const sleep = (ms: number) => new Promise((resolve) => setTimeout(resolve, ms));
    const [itemStrings, setItemStrings] = useState(['All', 'Active']);
    const deleteView = (index: number) => {
        const newItemStrings = [...itemStrings];
        newItemStrings.splice(index, 1);
        setItemStrings(newItemStrings);
        setSelected(0);
    };

    const duplicateView = async (name: string) => {
        setItemStrings([...itemStrings, name]);
        setSelected(itemStrings.length);
        await sleep(1);
        return true;
    };

    const handleTabChange = (item, index) => {
        setSelected(index);
        if (item === 'All') {
            setProducts(baseProducts);
        } else if (item === 'Active') {
            setProducts(baseProducts.filter((item) => item.status === 'active'));
        }
    };

    const tabs: TabProps[] = itemStrings.map((item, index) => ({
        content: item,
        index,
        onAction: () => handleTabChange(item, index),
        id: `${item}-${index}`,
        isLocked: index === 0,
        actions:
            index === 0
                ? []
                : [
                      {
                          type: 'rename',
                          onAction: () => {},
                          onPrimaryAction: async (value: string): Promise<boolean> => {
                              const newItemsStrings = tabs.map((item, idx) => {
                                  if (idx === index) {
                                      return value;
                                  }
                                  return item.content;
                              });
                              await sleep(1);
                              setItemStrings(newItemsStrings);
                              return true;
                          },
                      },
                      {
                          type: 'duplicate',
                          onPrimaryAction: async (value: string): Promise<boolean> => {
                              await sleep(1);
                              duplicateView(value);
                              return true;
                          },
                      },
                      {
                          type: 'edit',
                      },
                      {
                          type: 'delete',
                          onPrimaryAction: async () => {
                              await sleep(1);
                              deleteView(index);
                              return true;
                          },
                      },
                  ],
    }));
    const [selected, setSelected] = useState(0);
    const onCreateNewView = async (value: string) => {
        await sleep(500);
        setItemStrings([...itemStrings, value]);
        setSelected(itemStrings.length);
        return true;
    };
    const sortOptions: IndexFiltersProps['sortOptions'] = [
        { label: 'Product', value: 'product asc', directionLabel: 'A-Z' },
        { label: 'Product', value: 'product desc', directionLabel: 'Z-A' },
    ];

    const [sortSelected, setSortSelected] = useState(['order asc']);
    const { mode, setMode } = useSetIndexFiltersMode();
    const onHandleCancel = () => {};

    const onHandleSave = async () => {
        await sleep(1);
        return true;
    };

    const primaryAction: IndexFiltersProps['primaryAction'] =
        selected === 0
            ? {
                  type: 'save-as',
                  onAction: onCreateNewView,
                  disabled: false,
                  loading: false,
              }
            : {
                  type: 'save',
                  onAction: onHandleSave,
                  disabled: false,
                  loading: false,
              };

    const [queryValue, setQueryValue] = useState('');

    const handleFiltersQueryChange = useCallback((value: string) => setQueryValue(value), []);

    const resourceName = {
        singular: 'product',
        plural: 'products',
    };

    useEffect(() => {
        const initialSelectedResources = baseProducts
            .filter((product) => product.tbyb === true)
            .map((product) => product.id);
        initialSelectedResources.forEach((id) => {
            handleSelectionChange(IndexTableSelectionType.Single, true, id);
        });
    }, []);

    const { selectedResources, allResourcesSelected, handleSelectionChange } = useIndexResourceState<Product>(products);

    const rowMarkup = products.map(({ id, product, thumbnail }, index) => (
        <IndexTable.Row id={id} key={id} selected={selectedResources.includes(id)} position={index}>
            <IndexTable.Cell>
                <InlineStack gap="400" blockAlign="center">
                    {thumbnail}
                    {product}
                </InlineStack>
            </IndexTable.Cell>
        </IndexTable.Row>
    ));

    return (
        <>
            <ResourcePicker resourceType="Product" open />

            <IndexFilters
                sortOptions={sortOptions}
                sortSelected={sortSelected}
                queryValue={queryValue}
                queryPlaceholder="Searching in all"
                onQueryChange={handleFiltersQueryChange}
                onQueryClear={() => setQueryValue('')}
                onSort={setSortSelected}
                primaryAction={primaryAction}
                cancelAction={{
                    onAction: onHandleCancel,
                    disabled: false,
                    loading: false,
                }}
                tabs={tabs}
                selected={selected}
                onSelect={setSelected}
                canCreateNewView
                onCreateNewView={onCreateNewView}
                filters={[]}
                onClearAll={() => {}}
                mode={mode}
                setMode={setMode}
            />
            <IndexTable
                resourceName={resourceName}
                itemCount={products.length}
                selectedItemsCount={allResourcesSelected ? 'All' : selectedResources.length}
                onSelectionChange={handleSelectionChange}
                headings={[{ title: 'Product' }]}
            >
                {rowMarkup}
            </IndexTable>
        </>
    );
};

EligibleProducts.getTitle = getTitle;
export default EligibleProducts;
