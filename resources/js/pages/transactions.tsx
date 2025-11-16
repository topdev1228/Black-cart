import { Button, Card, BlockStack, Text, Layout, Page, InlineGrid, Divider, DataTable } from '@shopify/polaris';
import React, { useContext, useEffect, useState } from 'react';
import { useTranslation } from 'react-i18next';
import DatePicker from '../components/datePicker';
import useTransactionsApi from '../hooks/useTransactionsApi';
import { BlackcartContext, TransactionsData } from '../components/contexts/BlackcartContext';
import { useI18n } from '@shopify/react-i18n';

const Transactions = () => {
    const { t } = useTranslation();
    const [transactionData, setTransactionData] = useState<TransactionsData | null>(null);
    const [formattedTransactionData, setFormattedTransactionData] = useState([]);
    const [i18n] = useI18n();
    const { store } = useContext(BlackcartContext);

    const now = new Date();
    const startDate = new Date(now.getFullYear(), now.getMonth(), 1);
    const endDate = now;

    const defaultDateRangeSelected = [startDate.toISOString(), endDate.toISOString()];

    const [dateRangeSelected, setDateRangeSelected] = useState(defaultDateRangeSelected);
    const { exportTransactions, getTransactions } = useTransactionsApi();

    const formatMoney = (money: number, isNegative: boolean = false): number => {
        let negative = isNegative ? '-' : '';
        return (
            negative +
            i18n.formatCurrency(money, {
                currency: store?.currency ?? 'USD',
                form: 'short',
            })
        );
    };

    useEffect(() => {
        if (dateRangeSelected[0] === dateRangeSelected[1]) {
            return;
        }

        getTransactions(dateRangeSelected[0], dateRangeSelected[1]).then((transactionData: TransactionData) => {
            setTransactionData(transactionData);
        });
    }, [dateRangeSelected]);

    useEffect(() => {
        let formattedTransactions = [];
        transactionData?.transactions.forEach((t: Transaction) => {
            let transactionDate = new Date(new Date(t.date).toISOString()).toLocaleTimeString('en-US', {
                month: 'long',
                day: 'numeric',
                year: 'numeric',
                hour: '2-digit',
                hour12: false,
                minute: '2-digit',
                second: '2-digit',
            });

            formattedTransactions.push([
                transactionDate,
                t.type.charAt(0).toUpperCase() + t.type.slice(1),
                t.orderNumber,
                formatMoney(t.amount), // The backend already returns refunds as negative numbers
            ]);
        });
        setFormattedTransactionData(formattedTransactions);
    }, [transactionData]);

    return (
        <Page>
            <ui-nav-menu>
                <a href="/" rel="home">
                    Home
                </a>
                <a href="/transactions">Transactions</a>
                <a href="/storeSettings">Store Settings</a>
                <a href="/help">Help Center</a>
            </ui-nav-menu>
            <Layout>
                <Layout.Section>
                    <InlineGrid gap="200" columns={2}>
                        <DatePicker setDateRangeSelected={setDateRangeSelected} dates={defaultDateRangeSelected} />
                        <div style={{ display: 'flex', justifyContent: 'flex-end' }}>
                            <Button
                                disabled={transactionData === null}
                                onClick={() => exportTransactions(dateRangeSelected[0], dateRangeSelected[1])}
                            >
                                Export
                            </Button>
                        </div>
                    </InlineGrid>
                </Layout.Section>
                <Layout.Section>
                    <InlineGrid gap="200" columns={2}>
                        <Card padding="100" sectioned>
                            <BlockStack gap="500">
                                <Divider borderColor="transparent" />
                                <Text alignment="center" variant="headingMd" as="h6">
                                    Total Payments
                                </Text>
                            </BlockStack>
                            <BlockStack>
                                <Text alignment="center">Total payments captured on TBYB orders</Text>
                            </BlockStack>
                            <BlockStack gap="500">
                                <Divider borderColor="transparent" />
                                <Text alignment="center" tone="success" variant="headingXl" as="h4">
                                    {formatMoney(transactionData?.summary.totalPayments) || '--'}
                                </Text>
                                <Divider borderColor="transparent" />
                            </BlockStack>
                        </Card>
                        <Card padding="100" sectioned>
                            <BlockStack gap="500">
                                <Divider borderColor="transparent" />
                                <Text alignment="center" variant="headingMd" as="h6">
                                    Total Refunds
                                </Text>
                            </BlockStack>
                            <BlockStack>
                                <Text alignment="center">Total refunds of TBYB orders</Text>
                            </BlockStack>
                            <BlockStack gap="500">
                                <Divider borderColor="transparent" />
                                <Text alignment="center" tone="success" variant="headingXl" as="h4">
                                    {formatMoney(transactionData?.summary.totalRefunds) || '--'}
                                </Text>
                                <Divider borderColor="transparent" />
                            </BlockStack>
                        </Card>
                        <Divider borderColor="transparent" />
                    </InlineGrid>
                    <Card padding="200" sectioned>
                        <DataTable
                            columnContentTypes={['text', 'text', 'text', 'numeric']}
                            headings={['Transaction Date', 'Type', 'Order Number', 'Amount']}
                            rows={formattedTransactionData || []}
                        />
                    </Card>
                </Layout.Section>
            </Layout>
        </Page>
    );
};

export default Transactions;
