import React, { Dispatch, ReactNode, SetStateAction } from 'react';
import { OrderStatus, TransactionType } from '../enums/enums';

export interface Program {
    id: string;
    name: string;
    depositValue: number;
    depositType: string;
    tryPeriodDays: number;
    minTbybItems: number;
    maxTbybItems: number;
    storeId: string;
}

export interface Store {
    name: string;
    domain: string;
    currency: string;
}

export interface StoreSetting {
    name: string;
    value: string;
}

export interface StoreSettings {
    settings: Record<string, StoreSetting>;
}

export interface LineItem {
    id: string;
    subscriptionId: string;
    shopifyAppSubscriptionId: string;
    shopifyAppSubscriptionLineItemId: string;
    type: string;
    terms: string;
    recurringAmount: number;
    recurringAmountCurrency: string;
    usageCappedAmount: number;
    usageCappedAmountCurrency: string;
}

export interface Subscription {
    storeId: string;
    id: string;
    shopifyAppSubscriptionId: string;
    shopifyConfirmationUrl: string;
    status: string;
    activatedAt: string | null;
    deactivatedAt: string | null;
    lineItems: LineItem[];
}

export interface ActiveSubscription {
    id: string;
    status: string;
}

export interface AppInstallation {
    id: string;
    activeSubscriptions: ActiveSubscription[];
}

export interface AnalyticsData {
    data: AnalyticsDataRecord[];
}

export interface AnalyticsDataRecord {
    orderStatus: OrderStatus;
    date: Date;
    orderCount: number;
    grossSales: number;
    netSales: number;
    discounts: number;
    productCost: number;
    fulfillmentCost: number;
    returnShippingCost: number;
    paymentProcessingCost: number;
    paidAdvertisingCost: number;
    tbybFee: number;
    returns: number;
    profitContribution: number;
}

export interface TransactionsData {
    transactions: Transaction[];
    summary: TransactionSummary;
}

export interface Transaction {
    date: Date;
    type: TransactionType;
    orderNumber: string;
    amount: number;
}

export interface TransactionSummary {
    totalPayments: number;
    totalRefunds: number;
}

export interface Product {
    id: string;
    handle: string;
}

export const stage: string = '';
export const latestStage: string = '';

export interface ShopifyServiceContextType {
    store: Store | null;
    program: Program | null;
    storeSettings: StoreSettings | null;
    stage: string;
    latestStage: string;
    formRef: React.RefObject<HTMLFormElement> | null;
    product: Product | null;
    setProgram: (program: Program) => void;
    setStoreSettings: (storeSettings: StoreSettings) => void;
    setStoreAttribute: (index: string, value: any) => void;
    setProgramAttribute: (index: string, value: any) => void;
    setStoreSettingAttribute: (index: string, value: any) => void;
    setStage: Dispatch<React.SetStateAction<string>>;
    setLatestStage: Dispatch<React.SetStateAction<string>>;
    saveForm: () => void;
    save: () => void;
    launch: () => void;
    enableBilling: () => Promise<Subscription>;
    setThemeInstallClicked: () => void;
    updateStage: (stage: string) => void;
}

const defaultSubscription: Subscription = {
    storeId: '',
    id: '',
    shopifyAppSubscriptionId: '',
    shopifyConfirmationUrl: '',
    status: '',
    activatedAt: null,
    deactivatedAt: null,
    lineItems: [],
};

const defaultContextState = {
    store: null,
    program: null,
    storeSettings: null,
    stage: '',
    latestStage: '',
    formRef: null,
    product: null,
    setProgram: (program) => {},
    setStoreSettings: (storeSettings) => {},
    setStoreAttribute: (index, value) => {},
    setProgramAttribute: (index, value) => {},
    setStoreSettingAttribute: (index, value) => {},
    setStage: () => {},
    setLatestStage: () => {},
    saveForm: () => {},
    save: () => {},
    launch: () => {},
    enableBilling: (): Promise<Subscription> => Promise.resolve(defaultSubscription),
    setThemeInstallClicked: () => {},
    updateStage: (stage: string) => {},
} as ShopifyServiceContextType;

export const BlackcartContext = React.createContext(defaultContextState);
