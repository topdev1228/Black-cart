import {
    Card,
    BlockStack,
    Text,
    Layout,
    Page,
    Grid,
    Divider,
    DataTable,
    InlineGrid,
    Link,
    Collapsible,
    Button,
    Banner,
} from '@shopify/polaris';
import MonthPicker from '../components/monthPicker';
import ExternalLinkIcon from '../components/externalLinkIcon';
import useAnalyticsApi from '../hooks/useAnalyticsApi';
import React, { useCallback, useContext, useEffect, useState } from 'react';
import { AnalyticsData, AnalyticsDataRecord, BlackcartContext } from '../components/contexts/BlackcartContext';
import { OrderStatus } from '../components/enums/enums';
import { useI18n } from '@shopify/react-i18n';
import { CaretDownIcon, CaretUpIcon } from '@shopify/polaris-icons';

const Analytics = () => {
    const { analyticsData } = useAnalyticsApi();
    const [analytics, setAnalytics] = useState<AnalyticsData | null>(null);
    const defaultDateRangeSelected = [
        new Date().toLocaleString('en-US', { month: 'long', year: 'numeric' }).toLowerCase().replace(' ', '-'),
    ];
    const [dateRangeSelected, setDateRangeSelected] = useState(defaultDateRangeSelected);
    const [dates, setDates] = useState(defaultDateRangeSelected);
    const [salesData, setSalesData] = useState({});
    const [orderData, setOrderData] = useState({});
    const { store } = useContext(BlackcartContext);
    const [i18n] = useI18n();
    const [firstLoad, setFirstLoad] = useState(true);
    const formatMoney = (money: number, isNegative: boolean = false): string => {
        let negative = isNegative ? '-' : '';
        return (
            negative +
            i18n.formatCurrency(money, {
                currency: store?.currency ?? 'USD',
                form: 'short',
            })
        );
    };

    const [open, setOpen] = useState(false);
    const handleToggle = useCallback(() => setOpen((open) => !open), []);

    const defaultSalesTableData = {
        data: [
            ['Gross Sales', formatMoney(0)],
            ['Discounts', formatMoney(0, true)],
            ['Returns', formatMoney(0, true)],
            ['Net Sales', formatMoney(0)],
            ['Product Cost*', formatMoney(0, true)],
            ['Fulfillment Cost*', formatMoney(0, true)],
            ['Return Shipping Cost*', formatMoney(0, true)],
            ['Payment Processing Cost*', formatMoney(0, true)],
            ['Paid Advertising Cost*', formatMoney(0, true)],
            ['TBYB Fee*', formatMoney(0, true)],
        ],
        profitContribution: formatMoney(0),
        grossSales: formatMoney(0),
    };
    const defaultOrderTableData = {
        data: [
            ['Processing (not yet shipped)', '0', formatMoney(0), formatMoney(0)],
            ['Shipped', '0', formatMoney(0), formatMoney(0)],
            ['Trial in Progress', '0', formatMoney(0), formatMoney(0)],
            ['Completed', '0', formatMoney(0), formatMoney(0)],
        ],
        grossSales: formatMoney(0),
        netSales: formatMoney(0),
        orderCount: 0,
    };
    const [salesTableData, setSalesTableData] = useState(defaultSalesTableData);
    const [orderTableData, setOrderTableData] = useState(defaultOrderTableData);

    useEffect(() => {
        setAnalytics(analyticsData);
    }, [analyticsData]);

    useEffect(() => {
        if (analytics) {
            formatData(analytics);
        }
    }, [analytics]);

    useEffect(() => {
        let selectedDate = dateRangeSelected[0];
        if (selectedDate in salesData) {
            // Because tbyb fee data is returned per order with a hardcoded 4% on tbyb net sales, this does not take into
            // account our pricing plan which goes on steps of 99, 199, 299, 399, etc. on every $2500 in net sales.
            // Due to this difference, we need to recalculate the tbybFee to match the pricing plan based on the net sales.
            let netSales = salesData[selectedDate]['netSales'];
            let numberOfSteps = Math.ceil(netSales / 2500);
            let tbybFee = numberOfSteps * 100 - 1;

            salesTableData['data'] = [
                ['Gross Sales', formatMoney(salesData[selectedDate]['grossSales'])],
                ['Discounts', formatMoney(salesData[selectedDate]['discounts'], true)],
                ['Returns', formatMoney(salesData[selectedDate]['returns'], true)],
                ['Net Sales', formatMoney(netSales)],
                ['Product Cost*', formatMoney(salesData[selectedDate]['productCost'], true)],
                ['Fulfillment Cost*', formatMoney(salesData[selectedDate]['fulfillmentCost'], true)],
                ['Return Shipping Cost*', formatMoney(salesData[selectedDate]['returnShippingCost'], true)],
                ['Payment Processing Cost*', formatMoney(salesData[selectedDate]['paymentProcessingCost'], true)],
                ['Paid Advertising Cost*', formatMoney(salesData[selectedDate]['paidAdvertisingCost'], true)],
                ['TBYB Fee*', formatMoney(tbybFee, true)],
            ];
            salesTableData['profitContribution'] = formatMoney(salesData[selectedDate]['profitContribution'] - tbybFee);
            salesTableData['grossSales'] = formatMoney(salesData[selectedDate]['grossSales']);

            setSalesTableData(salesTableData);
        }
        if (selectedDate in orderData) {
            orderTableData['data'] = [
                [
                    'Processing (not yet shipped)',
                    orderData[selectedDate][OrderStatus.PROCESSING]['orderCount'],
                    formatMoney(orderData[selectedDate][OrderStatus.PROCESSING]['grossSales']),
                    formatMoney(orderData[selectedDate][OrderStatus.PROCESSING]['netSales']),
                ],
                [
                    'Shipped',
                    orderData[selectedDate][OrderStatus.SHIPPED]['orderCount'],
                    formatMoney(orderData[selectedDate][OrderStatus.SHIPPED]['grossSales']),
                    formatMoney(orderData[selectedDate][OrderStatus.SHIPPED]['netSales']),
                ],
                [
                    'Trial in Progress',
                    orderData[selectedDate][OrderStatus.TRIAL_IN_PROGRESS]['orderCount'],
                    formatMoney(orderData[selectedDate][OrderStatus.TRIAL_IN_PROGRESS]['grossSales']),
                    formatMoney(orderData[selectedDate][OrderStatus.TRIAL_IN_PROGRESS]['netSales']),
                ],
                [
                    'Completed',
                    orderData[selectedDate][OrderStatus.COMPLETED]['orderCount'],
                    formatMoney(orderData[selectedDate][OrderStatus.COMPLETED]['grossSales']),
                    formatMoney(orderData[selectedDate][OrderStatus.COMPLETED]['netSales']),
                ],
            ];
            let totalOrderCount =
                orderData[selectedDate][OrderStatus.PROCESSING]['orderCount'] +
                orderData[selectedDate][OrderStatus.SHIPPED]['orderCount'] +
                orderData[selectedDate][OrderStatus.TRIAL_IN_PROGRESS]['orderCount'] +
                orderData[selectedDate][OrderStatus.COMPLETED]['orderCount'];
            let totalGrossSales =
                orderData[selectedDate][OrderStatus.PROCESSING]['grossSales'] +
                orderData[selectedDate][OrderStatus.SHIPPED]['grossSales'] +
                orderData[selectedDate][OrderStatus.TRIAL_IN_PROGRESS]['grossSales'] +
                orderData[selectedDate][OrderStatus.COMPLETED]['grossSales'];
            let totalNetSales =
                orderData[selectedDate][OrderStatus.PROCESSING]['netSales'] +
                orderData[selectedDate][OrderStatus.SHIPPED]['netSales'] +
                orderData[selectedDate][OrderStatus.TRIAL_IN_PROGRESS]['netSales'] +
                orderData[selectedDate][OrderStatus.COMPLETED]['netSales'];
            orderTableData['orderCount'] = totalOrderCount;
            orderTableData['grossSales'] = formatMoney(totalGrossSales);
            orderTableData['netSales'] = formatMoney(totalNetSales);
            setOrderTableData(orderTableData);
        }
        if (firstLoad && Object.keys(salesData).length != 0 && Object.keys(orderData).length != 0) {
            formatData(analytics);
            setFirstLoad(false);
        }
    }, [salesData, orderData, dateRangeSelected]);

    useEffect(() => {
        if (!analyticsData) {
            return;
        }
        formatData(analyticsData);
    }, [dateRangeSelected]);

    function sortByMonth(arr) {
        const monthOrder = [
            'january',
            'february',
            'march',
            'april',
            'may',
            'june',
            'july',
            'august',
            'september',
            'october',
            'november',
            'december',
        ];
        arr.sort((a, b) => {
            //val of arr is in format of january-2024
            return monthOrder.indexOf(a.split('-')[0]) - monthOrder.indexOf(b.split('-')[0]);
        });
        return arr;
    }

    function formatData(data: AnalyticsData) {
        let orderData = {};
        let salesData = {};
        data?.data.forEach((datum: AnalyticsDataRecord) => {
            let recordDate = new Date(new Date(datum.date).toISOString()).toLocaleString('en-US', {
                month: 'long',
                year: 'numeric',
            });
            let dateId = recordDate.toLowerCase().replace(' ', '-');
            formatOrderData(datum, dateId, orderData);
            formatSalesData(datum, dateId, salesData);
        });
        setOrderData(orderData);
        setSalesData(salesData);
        setDates(sortByMonth(Object.keys(salesData)));
    }

    function formatOrderData(datum, dateId, orderData) {
        if (orderData[dateId] === undefined) {
            orderData[dateId] = {};
            Object.keys(OrderStatus).forEach((key) => {
                orderData[dateId][OrderStatus[key]] = {
                    orderCount: 0,
                    grossSales: 0,
                    netSales: 0,
                };
            });
        }
        orderData[dateId][datum['orderStatus']]['orderCount'] += datum['orderCount'];
        orderData[dateId][datum['orderStatus']]['grossSales'] += datum['grossSales'];
        orderData[dateId][datum['orderStatus']]['netSales'] += datum['netSales'];
    }

    function formatSalesData(datum, dateId, salesData) {
        if (salesData[dateId] === undefined) {
            salesData[dateId] = {
                grossSales: 0,
                discounts: 0,
                returns: 0,
                netSales: 0,
                productCost: 0,
                fulfillmentCost: 0,
                returnShippingCost: 0,
                paymentProcessingCost: 0,
                paidAdvertisingCost: 0,
                tbybFee: 0,
                profitContribution: 0,
            };
        }
        salesData[dateId]['grossSales'] += datum.grossSales;
        salesData[dateId]['discounts'] += datum.discounts;
        salesData[dateId]['returns'] += datum.returns;
        salesData[dateId]['netSales'] += datum.netSales;
        salesData[dateId]['productCost'] += datum.productCost;
        salesData[dateId]['fulfillmentCost'] += datum.fulfillmentCost;
        salesData[dateId]['returnShippingCost'] += datum.returnShippingCost;
        salesData[dateId]['paymentProcessingCost'] += datum.paymentProcessingCost;
        salesData[dateId]['paidAdvertisingCost'] += datum.paidAdvertisingCost;
        salesData[dateId]['tbybFee'] += datum.tbybFee;
        salesData[dateId]['profitContribution'] += datum.profitContribution;
    }

    const getTotalSales = () => {
        return salesTableData['grossSales'];
    };

    const getTotalProfit = () => {
        return salesTableData['profitContribution'];
    };

    const getOrderSummaryTotalOrderCount = () => {
        return orderTableData['orderCount'];
    };

    const getOrderSummaryTotalGrossSales = () => {
        return orderTableData['grossSales'];
    };

    const getOrderSummaryTotalNetSales = () => {
        return orderTableData['netSales'];
    };
    return (
        <Page>
            <Layout>
                <Layout.Section>
                    <Card roundedAbove="sm">
                        <BlockStack gap="500">
                            <BlockStack gap="200">
                                <Grid>
                                    <Grid.Cell columnSpan={{ xs: 6, sm: 2, md: 2, lg: 6, xl: 6 }}>
                                        <Text variant="headingXl" as="h4">
                                            Analytics Dashboard
                                        </Text>
                                    </Grid.Cell>
                                    <Grid.Cell columnSpan={{ xs: 6, sm: 4, md: 4, lg: 6, xl: 6 }}>
                                        <div style={{ float: 'right' }}>
                                            <a
                                                href="https://blackcart.com/how-it-works-shopper"
                                                target="_blank"
                                                style={{ textDecoration: 'none' }}
                                            >
                                                <ExternalLinkIcon />
                                                Learn More
                                            </a>
                                        </div>
                                    </Grid.Cell>
                                </Grid>
                                <MonthPicker setDateRangeSelected={setDateRangeSelected} dates={dates} />
                                <Text as="p" variant="bodyMd">
                                    Select a month to view its corresponding Blackcart Profit Contribution and GMV
                                    Distribution by Order Status. Please note that the current month’s date is updated
                                    Month-To-Date (MTD) and refreshed daily at 5 AM EST for the previous day’s
                                    transactions.
                                </Text>
                                <Divider borderColor="transparent" />
                            </BlockStack>
                        </BlockStack>
                        <Grid>
                            <Grid.Cell columnSpan={{ xs: 6, sm: 6, md: 3, lg: 6, xl: 6 }}>
                                <Card padding="0">
                                    <DataTable
                                        showTotalsInFooter
                                        columnContentTypes={['text', 'numeric']}
                                        headings={['Blackcart Profit Contribution*', '']}
                                        rows={salesTableData['data']}
                                        totals={['Profit Contribution', salesTableData['profitContribution']]}
                                        totalsName={{
                                            singular: 'Profit Contribution',
                                            plural: 'Profit Contribution',
                                        }}
                                    />
                                </Card>
                            </Grid.Cell>
                            <Grid.Cell columnSpan={{ xs: 6, sm: 6, md: 3, lg: 6, xl: 6 }}>
                                <InlineGrid gap="200" columns={2}>
                                    <Card padding="100" sectioned>
                                        <BlockStack gap="500">
                                            <Divider borderColor="transparent" />
                                            <Text alignment="center" variant="headingMd" as="h6">
                                                Total Gross Sales
                                            </Text>
                                        </BlockStack>
                                        <BlockStack>
                                            <Text alignment="center">
                                                Total of Try Before You Buy orders placed in a selected month
                                            </Text>
                                        </BlockStack>
                                        <BlockStack gap="500">
                                            <Divider borderColor="transparent" />
                                            <Text alignment="center" tone="success" variant="headingXl" as="h4">
                                                {getTotalSales()}
                                            </Text>
                                            <Divider borderColor="transparent" />
                                        </BlockStack>
                                    </Card>
                                    <Card padding="100" sectioned>
                                        <BlockStack gap="500">
                                            <Divider borderColor="transparent" />
                                            <Text alignment="center" variant="headingMd" as="h6">
                                                Total Profit Contribution
                                            </Text>
                                        </BlockStack>
                                        <BlockStack>
                                            <Text alignment="center">
                                                Total of Try Before You Buy profit contribution in a selected month
                                            </Text>
                                        </BlockStack>
                                        <BlockStack gap="500">
                                            <Divider borderColor="transparent" />
                                            <Text alignment="center" tone="success" variant="headingXl" as="h4">
                                                {getTotalProfit()}
                                            </Text>
                                            <Divider borderColor="transparent" />
                                        </BlockStack>
                                    </Card>
                                    <Divider borderColor="transparent" />
                                </InlineGrid>
                                <Card padding="0" sectioned>
                                    <DataTable
                                        showTotalsInFooter
                                        columnContentTypes={['text', 'numeric', 'numeric', 'numeric']}
                                        headings={['Order Summary**', 'Order Count', 'Gross Sales', 'Net Sales']}
                                        rows={orderTableData['data']}
                                        totals={[
                                            'Total',
                                            getOrderSummaryTotalOrderCount(),
                                            getOrderSummaryTotalGrossSales(),
                                            getOrderSummaryTotalNetSales(),
                                        ]}
                                        totalsName={{
                                            singular: 'Total',
                                            plural: 'Total',
                                        }}
                                    />
                                </Card>
                            </Grid.Cell>
                        </Grid>
                        <BlockStack gap="500">
                            <Divider borderColor="transparent" />
                            <Banner>
                                <strong>*Blackcart Profit Contribution</strong> - This view showcases your monthly
                                Blackcart Profit Contribution, featuring Sales, Returns, and Discounts from your
                                Blackcart selling plan on Shopify, and incorporates Blackcart's market-based estimates
                                for Product Costs, Shipping, and Payment Processing. {!open && '...'}
                                {open && (
                                    <span>
                                        These figures are used to calculate your Net Sales and Profit Contribution. Note
                                        that the values for Product Costs, Shipping, and Payment Processing are
                                        estimates that may vary from your actual costs. For modifications to better
                                        align these estimates with your business specifics, please contact us at{' '}
                                        <Link url="mailto: merchantsupport@blackcart.co">
                                            merchantsupport@blackcart.co
                                        </Link>
                                        .
                                    </span>
                                )}
                                <br />
                                <Collapsible
                                    open={open}
                                    id="basic-collapsible"
                                    transition={{ duration: '500ms', timingFunction: 'ease-in-out' }}
                                    expandOnPrint
                                >
                                    <br />
                                    <Text>
                                        <strong as="p" fontWeight="bold">
                                            Product Cost
                                        </strong>{' '}
                                        - Assumes 25% cost of goods;{' '}
                                        <strong as="p" fontWeight="bold">
                                            Fulfillment Cost
                                        </strong>{' '}
                                        - Assumes {formatMoney(8)} per order;{' '}
                                        <strong as="p" fontWeight="bold">
                                            Return Shipping Cost
                                        </strong>{' '}
                                        - Assumes {formatMoney(8)} per return;{' '}
                                        <strong as="p" fontWeight="bold">
                                            Payment Processing Cost
                                        </strong>{' '}
                                        - Assumes 2.3% + {formatMoney(0.3)} per order;{' '}
                                        <strong as="p" fontWeight="bold">
                                            Paid Advertising Cost
                                        </strong>{' '}
                                        - No additional advertising required to generate TBYB orders;{' '}
                                        <strong as="p" fontWeight="bold">
                                            TBYB Fee
                                        </strong>{' '}
                                        - Blackcart fee charged on Net Sales of TBYB items only.
                                    </Text>
                                    <br />
                                    <Text>
                                        <strong as="p" fontWeight="bold">
                                            **Order Summary
                                        </strong>{' '}
                                        - This view displays the number of orders and total gross sales by order status
                                        for the selected month. This report is a snapshot in time and will update as
                                        orders complete their trial period. Completed is the final state of an order and
                                        includes all orders, whether items were kept or returned.
                                    </Text>
                                </Collapsible>
                                <br />
                                <Button
                                    onClick={handleToggle}
                                    ariaExpanded={open}
                                    ariaControls="basic-collapsible"
                                    icon={open ? CaretUpIcon : CaretDownIcon}
                                ></Button>
                            </Banner>
                        </BlockStack>
                    </Card>
                </Layout.Section>
            </Layout>
        </Page>
    );
};

export default Analytics;
